<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\BarangKeluarItem;
use App\Models\Barang;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BarangKeluarController extends Controller
{
    public function processScan(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'tanggal_keluar' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:barangs,id',
            'qty' => 'required|array',
            'qty.*' => 'integer|min:1',
        ]);

        // Ambil data order
        $order = Order::findOrFail($id);
        
        // Dapatkan semua item yang ada di order
        $orderItems = $order->orderItems()->pluck('barang_id')->toArray();
        
        // Validasi barang yang discan harus ada di order
        foreach ($request->barang_id as $barangId) {
            if (!in_array($barangId, $orderItems)) {
                $barang = Barang::findOrFail($barangId);
                return redirect()->back()->with('error', 'Barang ' . $barang->nama_barang . ' tidak ada dalam order ini.');
            }
        }

        // Buat entri di tabel barang_keluars
        $barangKeluar = BarangKeluar::create([
            'order_id' => $order->id,
            'tanggal_keluar' => $request->tanggal_keluar,
            'user_id' => Auth::id(), // ID user yang melakukan scan
        ]);

        // Simpan barang yang di-scan ke tabel barang_keluar_items
        foreach ($request->barang_id as $index => $barangId) {
            $quantity = $request->qty[$index];

            // Cek stok barang
            $barang = Barang::findOrFail($barangId);
            if ($barang->stok < $quantity) {
                return redirect()->back()->with('error', 'Stok barang ' . $barang->nama_barang . ' tidak mencukupi.');
            }

            // Kurangi stok barang
            $barang->stok -= $quantity;
            $barang->save();

            // Simpan ke tabel barang_keluar_items
            BarangKeluarItem::create([
                'barang_keluar_id' => $barangKeluar->id,
                'barang_id' => $barangId,
                'quantity' => $quantity,
            ]);
        }

        $order->status = 'Ready';
        $order->save();

        // Redirect dengan pesan sukses
        return redirect()->route('orders.index')->with('success', 'Barang berhasil di-scan dan data telah disimpan.');
    }

    public function index(Request $request)
    {
        // Build the base query with relationships
        $query = BarangKeluar::with([
            'user',
            'items',
            'order.department'  
        ])
        ->withCount('items')
        ->withSum('items as total_quantity', 'quantity');
        
        // Apply search functionality - khusus untuk tanggal dan departemen
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                // Pencarian berdasarkan departemen
                $q->whereHas('order.department', function($subQuery) use ($searchTerm) {
                    $subQuery->where('nama_departemen', 'like', '%' . $searchTerm . '%');
                })
                // Pencarian berdasarkan tanggal
                ->orWhere('tanggal_keluar', 'like', '%' . $searchTerm . '%');
            });
            
        }
        
        // Set default sorting
        $query->orderBy('tanggal_keluar', 'desc');
        
        // Apply pagination
        $perPage = $request->input('perPage', 10);
        $barangKeluar = $query->paginate($perPage)->appends($request->except('page'));
        
        // Handle AJAX requests if needed
        if ($request->ajax()) {
            return response()->json([
                'html' => view('barang-keluar._table_body', compact('barangKeluar'))->render(),
                'pagination' => view('pagination.simple-ajax', ['paginator' => $barangKeluar])->render()
            ]);
        }
        
        return view('barang-keluar.index', compact('barangKeluar'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'tanggal_keluar' => 'required|date',
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:barangs,id',
            'qty' => 'required|array',
            'qty.*' => 'integer|min:1',
            'catatan' => 'required|string|max:255', // Wajib isi catatan untuk barang keluar manual
        ]);

        // Buat entri di tabel barang_keluars
        $barangKeluar = BarangKeluar::create([
            'order_id' => null, // Tidak ada order terkait
            'tanggal_keluar' => $request->tanggal_keluar,
            'user_id' => Auth::id(), // ID user yang melakukan scan
            'catatan' => $request->catatan, // Simpan catatan
        ]);

        // Simpan barang yang di-scan ke tabel barang_keluar_items
        foreach ($request->barang_id as $index => $barangId) {
            $quantity = $request->qty[$index];

            // Cek stok barang
            $barang = Barang::findOrFail($barangId);
            if ($barang->stok < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok barang ' . $barang->nama_barang . ' tidak mencukupi.'
                ], 422);
            }

            // Kurangi stok barang
            $barang->stok -= $quantity;
            $barang->save();

            // Simpan ke tabel barang_keluar_items
            BarangKeluarItem::create([
                'barang_keluar_id' => $barangKeluar->id,
                'barang_id' => $barangId,
                'quantity' => $quantity,
            ]);
        }

        // Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Barang keluar manual berhasil disimpan.',
            'redirect' => route('barang-keluar.index')
        ]);
    }

    public function create()
    {
        // Untuk kasus manual tanpa order
        return view('barang-keluar.create', [
            'isManual' => true
        ]);
    }

    /**
     * Menampilkan detail barang keluar untuk modal
     * Termasuk informasi items dengan kode dan nama barang
     */
    public function detail(BarangKeluar $barangKeluar)
    {
        // Memuat relasi yang dibutuhkan dalam satu query untuk optimasi
        $barangKeluar->load([
            'items.barang',  // Memuat data barang untuk setiap item
            'user',          // Memuat data user yang melakukan scan
            'order'          // Memuat data order terkait
        ]);
        
        // Memformat data untuk response JSON
        return response()->json([
            // Informasi umum transaksi
            'tanggal_keluar' => Carbon::parse($barangKeluar->tanggal_keluar)->translatedFormat('d F Y'),
            'waktu' => $barangKeluar->created_at->format('H:i'),
            'user_name' => $barangKeluar->user->name,
            'catatan' => $barangKeluar->catatan,
            
            // Informasi order terkait jika ada
            'order_info' => $barangKeluar->order ? [
                'id' => $barangKeluar->order->id,
                'status' => $barangKeluar->order->status,
                'keterangan' => $barangKeluar->order->keterangan,
                'catatan' => $barangKeluar->order->catatan
            ] : null,
            
            // Detail items yang dikeluarkan
            'items' => $barangKeluar->items->map(function ($item) {
                return [
                    'kode_barang' => $item->barang->kode ?? '-',
                    'nama_barang' => $item->barang->nama_barang ?? 'Barang tidak ditemukan',
                    'quantity' => $item->quantity,
                    'satuan' => $item->barang->satuan ?? '-',
                ];
            }),
            
            // Informasi summary
            'summary' => [
                'total_items' => $barangKeluar->items->count(),
                'total_quantity' => $barangKeluar->items->sum('quantity'),
            ]
        ]);
    }
}
