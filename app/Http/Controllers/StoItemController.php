<?php

namespace App\Http\Controllers;

use App\Models\StoEvent;
use App\Models\StoItem;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoItemController extends Controller
{
    public function index()
    {
        // Ambil event aktif dan semua event untuk ditampilkan di filter
        $activeEvent = StoEvent::where('status', 'active')->first();
        $events = StoEvent::all(); // Ambil semua event untuk dropdown
        return view('sto.items.index', compact('activeEvent', 'events'));
    }

    public function getData(Request $request)
    {
        // Ambil data sto_items dengan relasi ke barang dan user
        $stoItems = StoItem::with(['barang', 'scannedByUser']);

        // Filter berdasarkan event_id jika ada
        if ($request->has('event_id') && $request->event_id != '') {
            $stoItems = $stoItems->where('sto_event_id', $request->event_id);
        }

        $stoItems = $stoItems->get();

        // Format data untuk DataTables
        $data = $stoItems->map(function ($item) {
            return [
                'id' => $item->id,
                'kode_barang' => $item->barang->kode ?? '-', // Fallback kalau barang null
                'nama_barang' => $item->barang->nama_barang ?? '-', // Fallback kalau barang null
                'stok_sistem' => $item->stok_sistem,
                'stok_aktual' => $item->stok_aktual,
                'selisih' => $item->selisih,
                'waktu_scan' => $item->waktu_scan ? $item->waktu_scan->format('Y-m-d H:i:s') : '-',
                'scanned_by' => $item->scannedByUser ? $item->scannedByUser->name : 'Unknown',
                'status' => $item->status ?? 'open', // Default status kalau null
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:open,close']);

        $stoItem = StoItem::findOrFail($id);
        $stoItem->update(['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
    }

    public function show($id) {}
}
