<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Jenis;
use Illuminate\Support\Facades\Validator;
use App\Imports\BarangImport;
use Maatwebsite\Excel\Facades\Excel;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    public function index()
    {
        return view('barang.index', [
            'barangs'         => Barang::all(),
            'jenis_barangs'   => Jenis::all(),
        ]);
    }

    public function getDataBarang()
    {
        $barangs = Barang::with('jenis')->get();

        // Perbaikan cara generate QR Code
        $renderer = new ImageRenderer(
            new RendererStyle(70), // Ukuran QR Code
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        foreach ($barangs as $barang) {
            // Generate QR code untuk setiap barang
            $qrCode = $writer->writeString($barang->kode);

            // Konversi ke HTML untuk ditampilkan di view
            $barang->barcode_html = '<div style="width: 100px; text-align: center">' . $qrCode . '</div>';
            $barang->barcode_html .= '<div style="font-size: 14px; text-align: center">' . $barang->kode . '</div>';

            $barang->gambar = $barang->gambar ? asset('storage/' . $barang->gambar) : null;

            $barang->id = $barang->id;
        }

        return response()->json([
            'success' => true,
            'data'    => $barangs
        ]);
    }


    public function create()
    {
        return view('barang.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode'          => 'required|string|max:15|unique:barangs',
            'gambar'        => 'nullable|image|max:10240',
            'nama_barang'   => 'required',
            'jenis_id'      => 'required',
            'size'          => 'required|string|max:10',
            'stok_minimum'  => 'required|numeric',
            'stok_maximum'  => 'required|numeric',
            'stok'          => 'nullable|numeric',
            'nama_supplier' => 'required|string|max:100',
            'price'         => 'required|numeric',
        ], [
            'kode.required'         => 'Form Kode Barang Wajib Di Isi !',
            'kode.unique'           => 'Kode Barang Sudah Terdaftar !',
            'gambar.image'          => 'Format File Tidak Sesuai !',
            'gambar.max'            => 'Ukuran File Maksimal 10MB !',
            'nama_barang.required'  => 'Form Nama Barang Wajib Di Isi !',
            'jenis_id.required'     => 'Pilih Jenis Barang !',
            'size.required'         => 'Form Size Wajib Di Isi !',
            'stok_minimum.required' => 'Form Stok Minimum Wajib Di Isi !',
            'stok_minimum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'stok_maximum.required' => 'Form Stok Maksimum Wajib Di Isi !',
            'stok_maximum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'stok.numeric'          => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'nama_supplier.required'=> 'Form Nama Supplier Wajib Di Isi !',
            'price.required'        => 'Form Harga Wajib Di Isi !',
            'price.numeric'         => 'Gunakan Angka Untuk Mengisi Form Harga !',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Proses gambar jika ada
        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $fileName = 'barang-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images/barang', $fileName, 'public');
            $gambarPath = 'images/barang/' . $fileName;
        }

        $barang = Barang::create([
            'kode'          => $request->kode,
            'nama_barang'   => $request->nama_barang,
            'jenis_id'      => $request->jenis_id,
            'size'          => $request->size,
            'stok_minimum'  => $request->stok_minimum,
            'stok_maximum'  => $request->stok_maximum,
            'stok'          => $request->stok ?? 0,  // Default 0 jika null
            'nama_supplier' => $request->nama_supplier,
            'price'         => $request->price,
            'gambar'        => $gambarPath,
        ]);

        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Disimpan !',
            'data'      => $barang
        ]);
    }


    public function show(Barang $barang)
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail Data Barang',
            'data'    => $barang
        ]);
    }

    public function edit(Barang $barang)
    {
        return response()->json([
            'success' => true,
            'message' => 'Edit Data Barang',
            'data'    => $barang
        ]);
    }

    public function update(Request $request, Barang $barang)
    {
        // Buat array rules validasi dasar (tanpa gambar)
        $rules = [
            'kode'          => 'required|string|max:15|unique:barangs,kode,' . $barang->id,
            'nama_barang'   => 'required',
            'jenis_id'      => 'required',
            'size'          => 'required|string|max:10',
            'stok_minimum'  => 'required|numeric',
            'stok_maximum'  => 'required|numeric',
            'nama_supplier' => 'required|string|max:100',
            'price'         => 'required|numeric',
        ];

        // Tambah validasi gambar hanya jika ada file yang diupload
        if ($request->hasFile('gambar')) {
            $rules['gambar'] = 'image|max:10240';
        }

        // Buat array pesan error
        $messages = [
            'kode.required'         => 'Form Kode Barang Wajib Di Isi !',
            'kode.unique'           => 'Kode Barang Sudah Terdaftar !',
            'gambar.image'          => 'Format File Tidak Sesuai !',
            'gambar.max'            => 'Ukuran File Maksimal 10MB !',
            'nama_barang.required'  => 'Form Nama Barang Wajib Di Isi !',
            'jenis_id.required'     => 'Pilih Jenis Barang !',
            'size.required'         => 'Form Size Wajib Di Isi !',
            'stok_minimum.required' => 'Form Stok Minimum Wajib Di Isi !',
            'stok_minimum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'stok_maximum.required' => 'Form Stok Maksimum Wajib Di Isi !',
            'stok_maximum.numeric'  => 'Gunakan Angka Untuk Mengisi Form Ini !',
            'nama_supplier.required'=> 'Form Nama Supplier Wajib Di Isi !',
            'price.required'        => 'Form Harga Wajib Di Isi !',
            'price.numeric'         => 'Gunakan Angka Untuk Mengisi Form Harga !',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Siapkan data untuk update
        $updateData = [
            'kode'          => $request->kode,
            'nama_barang'   => $request->nama_barang,
            'jenis_id'      => $request->jenis_id,
            'size'          => $request->size,
            'stok_minimum'  => $request->stok_minimum,
            'stok_maximum'  => $request->stok_maximum,
            'nama_supplier' => $request->nama_supplier,
            'price'         => $request->price,
        ];

        // Proses gambar hanya jika ada file baru
        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($barang->gambar && file_exists(public_path($barang->gambar))) {
                unlink(public_path($barang->gambar));
            }

            // Simpan gambar baru
            $file = $request->file('gambar');
            $fileName = 'barang-' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images/barang', $fileName, 'public');
            $updateData['gambar'] = 'images/barang/' . $fileName;
        }

        // Update data barang
        $barang->update($updateData);

        return response()->json([
            'success'   => true,
            'message'   => 'Data Berhasil Terupdate',
            'data'      => $barang
        ]);
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Barang $barang)
    { 
        // Cek apakah gambar ada sebelum menghapus
        $filePath = '.'.Storage::url($barang->gambar);
        if ($barang->gambar && file_exists($filePath)) {
            unlink($filePath);
        }

        Barang::destroy($barang->id);

        return response()->json([
            'success' => true,
            'message' => 'Data Barang Berhasil Dihapus!'
        ]);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $import = new BarangImport();
            Excel::import($import, $request->file('file'));

            // Ambil data terbaru setelah import
            $barangs = Barang::with('jenis')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data Berhasil Diimport!',
                'data' => $barangs
            ]);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation Errors',
                'errors' => $errorMessages
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function printBarcode(Request $request)
    {
        $ids = $request->query('ids'); // Ambil query parameter 'ids'
        
        if (empty($ids)) {
            // Jika tidak ada ID yang diberikan, ambil semua barang
            $barangs = Barang::all();
        } else {
            // Ubah string ids menjadi array dengan memisahkan nilai berdasarkan koma
            $idsArray = explode(',', $ids);
            
            // Ambil barang berdasarkan ID yang sudah diubah menjadi array
            $barangs = Barang::whereIn('id', $idsArray)->get();
        }
        
        // Generate barcode untuk setiap barang
        $renderer = new ImageRenderer(
            new RendererStyle(100), // Ukuran QR Code lebih besar untuk print
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        foreach ($barangs as $barang) {
            // Generate QR code untuk setiap barang
            $qrCode = $writer->writeString($barang->kode);

            // Konversi ke HTML untuk ditampilkan di view - HANYA QR CODE tanpa teks
            $barang->barcode_html = '<div>' . $qrCode . '</div>';
            
            // Pastikan ada nilai default untuk stok_maksimum jika tidak ada
            $barang->stok_maksimum = $barang->stok_maksimum ?? 5; // Nilai default 5 jika tidak ada
            $barang->stok_minimum = $barang->stok_minimum ?? 1; // Nilai default 1 jika tidak ada
        }
        
        return view('barang.print_barcode', compact('barangs'));
    }
}
