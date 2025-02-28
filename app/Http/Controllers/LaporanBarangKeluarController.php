<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use Dompdf\Dompdf;

class LaporanBarangKeluarController extends Controller
{
    public function index()
    {
        return view('laporan-barang-keluar.index');
    }

    public function getData(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $barangKeluar = BarangKeluar::with(['order.department', 'items.barang'])
            ->select('barang_keluars.*');

        if ($tanggalMulai && $tanggalSelesai) {
            $barangKeluar->whereBetween('tanggal_keluar', [$tanggalMulai, $tanggalSelesai]);
        }

        $data = $barangKeluar->get()->map(function ($item) {
            $barangData = [];
            foreach ($item->items as $barangItem) {
                $barangData[] = [
                    'id' => $item->id,
                    'tanggal_keluar' => $item->tanggal_keluar,
                    'department' => $item->order->department->nama_departemen,
                    'nama_barang' => $barangItem->barang->nama_barang,
                    'jumlah_keluar' => $barangItem->quantity
                ];
            }
            return $barangData;
        })->flatten(1);

        return response()->json($data);
    }

    public function printBarangKeluar(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $barangKeluar = BarangKeluar::with(['order.department', 'items.barang']);

        if ($tanggalMulai && $tanggalSelesai) {
            $barangKeluar->whereBetween('tanggal_keluar', [$tanggalMulai, $tanggalSelesai]);
        }

        $data = $barangKeluar->get()->map(function ($item) {
            $barangData = [];
            foreach ($item->items as $barangItem) {
                $barangData[] = [
                    'id' => $item->id,
                    'tanggal_keluar' => $item->tanggal_keluar,
                    'department' => $item->order->department->nama_departemen,
                    'nama_barang' => $barangItem->barang->nama_barang,
                    'jumlah_keluar' => $barangItem->quantity
                ];
            }
            return $barangData;
        })->flatten(1);
        
        $dompdf = new Dompdf();
        $html = view('/laporan-barang-keluar/print-barang-keluar', compact('data', 'tanggalMulai', 'tanggalSelesai'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('print-barang-keluar.pdf', ['Attachment' => false]);
    }
}
