<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\Jenis;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BarangImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            // Auto-create jenis if not exists
            $jenis = Jenis::firstOrCreate(
                ['jenis_barang' => $row['jenis']]
            );

            // Process image if exists
            $gambarPath = null;
            if (isset($row['gambar']) && !empty($row['gambar'])) {
                $imagePath = trim($row['gambar']); // Hapus spasi di awal dan akhir
                
                // Bersihkan path dari karakter yang tidak perlu
                $imagePath = str_replace('\\', '/', $imagePath); 
                $imagePath = ltrim($imagePath, '/'); 
                
                // Cek beberapa kemungkinan lokasi file
                $possiblePaths = [
                    public_path($imagePath),
                    public_path('images/' . $imagePath),
                    storage_path('app/public/' . $imagePath),
                    storage_path('app/public/images/' . $imagePath)
                ];

                // Cari file di semua kemungkinan lokasi
                $existingPath = null;
                foreach ($possiblePaths as $path) {
                    if (file_exists($path)) {
                        $existingPath = $path;
                        break;
                    }
                }

                // Jika file ditemukan, proses gambar
                if ($existingPath) {
                    // Generate nama file baru dengan mengambil ekstensi asli
                    $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
                    $fileName = 'barang-' . Str::random(10) . '.' . $extension;
                    
                    // Simpan file ke storage
                    Storage::disk('public')->put(
                        'images/barang/' . $fileName, 
                        file_get_contents($existingPath)
                    );
                    
                    $gambarPath = 'images/barang/' . $fileName;
                }
            }

            // Create new Barang
            $barang = new Barang([
                'kode'          => $row['kode'],
                'nama_barang'   => $row['nama_barang'],
                'jenis_id'      => $jenis->id,
                'size'          => $row['size'] ?? null,
                'stok_minimum'  => $row['stok_minimum'],
                'stok_maximum'  => $row['stok_maximum'],
                'stok'          => $row['stok'] ?? 0,
                'nama_supplier' => $row['supplier'],
                'price'         => $row['harga'],
                'gambar'        => $gambarPath,
            ]);

            DB::commit();
            return $barang;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rules(): array
    {
        return [
            'jenis'         => 'nullable|string',
            'kode'          => 'required|unique:barangs,kode',
            'nama_barang'   => 'required',
            'size'          => 'nullable',       
            'stok_minimum'  => 'nullable|numeric',
            'stok_maximum'  => 'nullable|numeric',
            'stok'          => 'nullable|numeric',       
            'supplier'      => 'nullable|string',
            'harga'         => 'nullable|numeric',
            'gambar'        => 'nullable|string'         
        ];
    }

    public function customValidationMessages()
    {
        return [
            'jenis.required'        => 'Kolom jenis wajib diisi',
            'kode.required'         => 'Kolom kode wajib diisi',
            'kode.unique'           => 'Kode barang sudah terdaftar',
            'nama_barang.required'  => 'Kolom nama barang wajib diisi',
            'stok_minimum.required' => 'Kolom stok minimum wajib diisi',
            'stok_minimum.numeric'  => 'Stok minimum harus berupa angka',
            'stok_maximum.required' => 'Kolom stok maximum wajib diisi',
            'stok_maximum.numeric'  => 'Stok maximum harus berupa angka',
            'stok.numeric'          => 'Stok harus berupa angka',
            'supplier.required'     => 'Kolom supplier wajib diisi',
            'harga.required'        => 'Kolom harga wajib diisi',
            'harga.numeric'         => 'Harga harus berupa angka',
            'gambar.string'         => 'Format path gambar tidak valid',
        ];
    }
}