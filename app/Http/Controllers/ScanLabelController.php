<?php

namespace App\Http\Controllers;

use App\Models\StoEvent;
use App\Models\StoItem;
use App\Models\Barang;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class ScanLabelController extends Controller
{
    public function index()
    {
        // Ambil event STO yang statusnya active
        $activeEvent = StoEvent::where('status', 'active')->first();

        // Kalau tidak ada event aktif, bisa redirect atau beri pesan
        if (!$activeEvent) {
            return redirect()->back()->with('error', 'Tidak ada event STO aktif saat ini.');
        }

        return view('sto.scan.index', compact('activeEvent'));
    }

    public function scan(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|exists:barangs,kode',
        ]);

        $activeEvent = StoEvent::where('status', 'active')->first();
        if (!$activeEvent) {
            return response()->json(['error' => 'Tidak ada event STO aktif.'], 400);
        }

        $barang = Barang::where('kode', $request->kode)->first();

        $existingScan = StoItem::where('sto_event_id', $activeEvent->id)
                               ->where('barang_id', $barang->id)
                               ->exists();
        if ($existingScan) {
            return response()->json(['error' => 'Barang ini sudah discan di event ini.'], 400);
        }

        // Tambah 'size' di response
        return response()->json([
            'success' => true,
            'barang' => [
                'id' => $barang->id,
                'kode' => $barang->kode,
                'nama' => $barang->nama_barang,
                'stok_sistem' => $barang->stok,
                'size' => $barang->size, // Kolom spesifikasi
            ]
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input dari modal
        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'stok_aktual' => 'required|integer|min:0',
        ]);

        // Ambil event aktif
        $activeEvent = StoEvent::where('status', 'active')->first();
        if (!$activeEvent) {
            return response()->json(['error' => 'Tidak ada event STO aktif.'], 400);
        }

        // Ambil barang
        $barang = Barang::find($request->barang_id);

        // Simpan ke sto_items
        $stoItem = StoItem::create([
            'sto_event_id' => $activeEvent->id,
            'barang_id' => $barang->id,
            'stok_sistem' => $barang->stok,
            'stok_aktual' => $request->stok_aktual,
            'selisih' => $request->stok_aktual - $barang->stok,
            'scanned_by' => Auth::user()->id, // Asumsi pakai auth
            'catatan' => $request->catatan ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Stok aktual berhasil disimpan.']);
    }
}
