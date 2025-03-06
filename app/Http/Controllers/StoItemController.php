<?php

namespace App\Http\Controllers;

use App\Models\StoEvent;
use App\Models\StoItem;

use Illuminate\Http\Request;

class StoItemController extends Controller
{
    public function index()
    {
        // Ambil event aktif untuk ditampilkan di header (opsional)
        $activeEvent = StoEvent::where('status', 'active')->first();
        return view('sto.items.index', compact('activeEvent'));
    }

    public function getData()
    {
        // Ambil data sto_items dengan relasi ke barang
        $stoItems = StoItem::with('barang')->get();

        // Format data untuk DataTables
        $data = $stoItems->map(function ($item) {
            return [
                'id' => $item->id,
                'kode_barang' => $item->barang->kode,
                'nama_barang' => $item->barang->nama_barang,
                'stok_sistem' => $item->stok_sistem,
                'stok_aktual' => $item->stok_aktual,
                'selisih' => $item->selisih,
                'scanned_by' => $item->scanned_by, // ID user, bisa diganti nama kalau relasi ke users ada
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function show($id){}
}
