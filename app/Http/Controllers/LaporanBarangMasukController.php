<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasukItem;
use Dompdf\Dompdf;
use App\Models\BarangMasuk;

class LaporanBarangMasukController extends Controller
{
    public function index()
    {
        return view('laporan-barang-masuk.index');
    }

    public function getData(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = BarangMasukItem::query()
            ->join('barang_masuks', 'barang_masuk_items.barang_masuk_id', '=', 'barang_masuks.id')
            ->join('barangs', 'barang_masuk_items.barang_id', '=', 'barangs.id')
            ->select([
                'barang_masuks.tanggal_masuk',
                'barangs.nama_barang',
                'barang_masuk_items.quantity as jumlah_masuk'
            ]);

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('barang_masuks.tanggal_masuk', [$tanggalMulai, $tanggalSelesai]);
        }

        $data = $query->get();

        return response()->json($data);
    }

    public function printBarangMasuk(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai');
        $tanggalSelesai = $request->input('tanggal_selesai');

        $query = BarangMasukItem::query()
            ->join('barang_masuks', 'barang_masuk_items.barang_masuk_id', '=', 'barang_masuks.id')
            ->join('barangs', 'barang_masuk_items.barang_id', '=', 'barangs.id')
            ->select([
                'barang_masuks.tanggal_masuk',
                'barangs.nama_barang',
                'barang_masuk_items.quantity as jumlah_masuk'
            ]);

        if ($tanggalMulai && $tanggalSelesai) {
            $query->whereBetween('barang_masuks.tanggal_masuk', [$tanggalMulai, $tanggalSelesai]);
        }

        $data = $query->get();

        // Generate PDF
        $dompdf = new Dompdf();
        $html = view('/laporan-barang-masuk/print-barang-masuk', compact('data', 'tanggalMulai', 'tanggalSelesai'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream('print-barang-masuk.pdf', ['Attachment' => false]);
    }

}
