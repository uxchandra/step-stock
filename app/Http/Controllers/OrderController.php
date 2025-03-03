<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Department;
use App\Models\Barang;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $userRole = $user->role->role;
        $userDepartmentId = $user->department_id;
        
        // Query orders berdasarkan role dan department
        $ordersQuery = Order::with(['requester', 'department', 'orderItems.barang']);
        
        // Filter berdasarkan role
        if ($userRole === 'admin divisi' || $userRole === 'kepala divisi') {
            $ordersQuery->where('department_id', $userDepartmentId);
        } elseif ($userRole === 'kepala gudang') {
            $ordersQuery->where('status', 'Approved by Kadiv');
        } elseif ($userRole === 'admin gudang') {
            $ordersQuery->whereIn('status', ['Approved by Kagud', 'Ready']);
        }
        
        // Search functionality - apply BEFORE pagination
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $ordersQuery->where(function($q) use ($searchTerm) {
                $q->whereHas('department', function($query) use ($searchTerm) {
                    $query->where('nama_departemen', 'like', '%' . $searchTerm . '%');
                })
                ->orWhere('status', 'like', '%' . $searchTerm . '%')
                ->orWhere('keterangan', 'like', '%' . $searchTerm . '%')
                ->orWhere('created_at', 'like', '%' . $searchTerm . '%');
            });
        }
        
        // Get departments and barangs for the view
        $departments = Department::all();
        $barangs = Barang::all();
        
        // Pagination - do this ONCE
        $perPage = $request->input('entries', 10);
        $orders = $ordersQuery->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->appends($request->except('page'));
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('orders._table_body', compact('orders'))->render(),
                'pagination' => view('pagination.simple-ajax', ['paginator' => $orders])->render()
            ]);
        }
        
        return view('orders.index', compact('orders', 'departments', 'barangs'));
    }

    public function create()
    {
        $departments = Department::all();
        return view('orders.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'barang_id' => 'required|array',
            'barang_id.*' => 'exists:barangs,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
            'catatan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->barang_id as $index => $barangId) {
                $barang = Barang::find($barangId);
                $requestedQuantity = $request->quantity[$index];
                
                $stokTersedia = $barang->stok - ($barang->stok_terpesan ?? 0);
                
                if ($stokTersedia < $requestedQuantity) {
                    DB::rollBack();
                    
                    if ($request->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => "Stok {$barang->nama_barang} tidak mencukupi! Tersedia: {$stokTersedia}, Diminta: {$requestedQuantity}"
                        ]);
                    }
                    
                    return redirect()->back()->with('error', "Stok {$barang->nama_barang} tidak mencukupi! Tersedia: {$stokTersedia}, Diminta: {$requestedQuantity}");
                }
                
                // Update stok_terpesan
                $barang->stok_terpesan = ($barang->stok_terpesan ?? 0) + $requestedQuantity;
                $barang->save();
            }

            // Buat order baru
            $order = Order::create([
                'requester_id' => Auth::id(),
                'department_id' => $request->department_id,
                'status' => 'Pending',
                'catatan' => $request->catatan,
                'tanggal_order' => now(),
            ]);

            // Simpan item-item yang diminta
            foreach ($request->barang_id as $index => $barangId) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'barang_id' => $barangId,
                    'quantity' => $request->quantity[$index],
                ]);
            }
        
            DB::commit();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Permintaan barang berhasil dibuat.',
                    'redirect' => route('orders.index')
                ]);
            }
            
            return redirect()->route('orders.index')->with('success', 'Permintaan barang berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ]);
            }
            
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show(Order $order)
    {
        $order->load([
            'requester',
            'department',
            'orderItems.barang',
            'approvedByKadiv', // Load relasi approvedByKadiv
            'approvedByKagud', // Load relasi approvedByKagud
        ]);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $departments = Department::all();
        return view('orders.edit', compact('order', 'departments'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved by Kadiv,Approved by Kagud,Processed',
            'catatan' => 'nullable|string',
        ]);

        $order->update([
            'status' => $request->status,
            'catatan' => $request->catatan,
        ]);

        return redirect()->route('orders.index')->with('success', 'Order berhasil diperbarui');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus');

        // // Mulai transaksi database untuk memastikan operasi atomik
        // DB::beginTransaction();
        // try {
        //     // Kembalikan stok_terpesan untuk setiap item di order
        //     foreach ($order->orderItems as $item) {
        //         $barang = $item->barang;
        //         $barang->stok_terpesan = max(0, ($barang->stok_terpesan ?? 0) - $item->quantity);
        //         $barang->save();
        //     }
            
        //     // Hapus order
        //     $order->delete();
            
        //     DB::commit();
        //     return redirect()->route('orders.index')->with('success', 'Order berhasil dihapus dan stok terpesan telah dikembalikan.');
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus order: ' . $e->getMessage());
        // }
    }

    public function approve(Request $request, $id)
    {
        // Cari order berdasarkan ID
        $order = Order::findOrFail($id);
        
        // Dapatkan user dan departemennya
        $user = Auth::user();
        $userRole = $user->role->role;
        $userDepartmentId = $user->department_id;
        
        // Verifikasi departemen dan role untuk kepala divisi
        if ($userRole === 'kepala divisi') {
            // Pastikan order berasal dari departemen yang sama
            if ($order->department_id != $userDepartmentId) {
                return redirect()->back()->with('error', 'Anda hanya dapat menyetujui permintaan dari departemen Anda.');
            }
            
            if ($order->status === 'Pending') {
                $order->status = 'Approved by Kadiv';
                $order->approved_by_kadiv = $user->id;
                $order->tanggal_approve_kadiv = now();
            } else {
                return redirect()->back()->with('error', 'Status order tidak valid untuk disetujui.');
            }
        } 
        // Verifikasi untuk kepala gudang
        elseif ($userRole === 'kepala gudang' && $order->status === 'Approved by Kadiv') {
            $order->status = 'Approved by Kagud';
            $order->approved_by_kagud = $user->id;
            $order->tanggal_approve_kagud = now();
        } else {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk melakukan aksi ini.');
        }
        
        // Simpan perubahan
        $order->save();
        
        // Redirect dengan pesan sukses
        return redirect()->route('orders.index')->with('success', 'Permintaan berhasil disetujui.');
    }

    public function scan($id)
    {
        $order = Order::with('orderItems.barang')->findOrFail($id);
        $orderItems = $order->orderItems()->pluck('barang_id')->toArray();

        // Setup QR Code generator
        $renderer = new ImageRenderer(
            new RendererStyle(80), // Ukuran QR Code
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);

        foreach ($order->orderItems as $item) {
            // Generate QR code untuk setiap barang
            $qrCode = $writer->writeString($item->barang->kode);

            // Simpan dalam format HTML agar bisa ditampilkan di view
            $item->barcode_html = '<div style="text-align: center;">' . $qrCode . '</div>';
        }

        return view('barang-keluar.create', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    public function complete($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== 'Ready') {
            return redirect()->back()->with('error', 'Status order tidak valid untuk diselesaikan.');
        }

        $order->status = 'Completed';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Order berhasil diselesaikan.');
    }
}
