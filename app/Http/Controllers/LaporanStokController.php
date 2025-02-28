<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use Dompdf\Dompdf;
use App\Http\Controllers\Controller;

class LaporanStokController extends Controller
{
    public function index()
    {
        return view('laporan-stok.index');
    }

    /**
     * Get Data 
     */
    public function getData(Request $request)
    {
        $selectedOption = $request->input('opsi');

        if($selectedOption == 'semua'){
            $barangs = Barang::all();
        } elseif ($selectedOption == 'minimum'){
            $barangs = Barang::whereRaw('stok <= stok_minimum')->get();
        } elseif ($selectedOption == 'stok-habis'){
            $barangs = Barang::where('stok', 0)->get();
        } else {
            $barangs = Barang::all();
        }

        return response()->json($barangs);
    }

    public function printStok(Request $request)
    {
        $selectedOption = $request->input('opsi');

        if ($selectedOption == 'semua') {
            $barangs = Barang::all();
        } elseif ($selectedOption == 'minimum') {
            $barangs = Barang::whereRaw('stok <= stok_minimum')->get();
        } elseif ($selectedOption == 'stok-habis') {
            $barangs = Barang::where('stok', 0)->get();
        } else {
            $barangs = Barang::all();
        }

       // Generate PDF
       $dompdf = new Dompdf();
       $html = view('/laporan-stok/print-stok', compact('barangs', 'selectedOption'))->render();
       $dompdf->loadHtml($html);
       $dompdf->setPaper('A4', 'portrait');
       $dompdf->render();
       $dompdf->stream('print-stok.pdf', ['Attachment' => false]);
    }
}
