<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BarangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $gambarPath = null;
        
        // Jika ada URL gambar di Excel
        if (isset($row['gambar']) && !empty($row['gambar'])) {
            // Opsi 1: Jika gambar adalah URL public
            if (filter_var($row['gambar'], FILTER_VALIDATE_URL)) {
                $imageContent = file_get_contents($row['gambar']);
                $fileName = 'barang-' . Str::random(10) . '.jpg';
                Storage::disk('public')->put('barang/' . $fileName, $imageContent);
                $gambarPath = 'barang/' . $fileName;
            }
            
            // Opsi 2: Jika gambar adalah path lokal
            elseif (file_exists($row['gambar'])) {
                $fileName = 'barang-' . Str::random(10) . '.jpg';
                Storage::disk('public')->put(
                    'barang/' . $fileName, 
                    file_get_contents($row['gambar'])
                );
                $gambarPath = 'barang/' . $fileName;
            }
        }

        return new Barang([
            'kode'           => $row['kode'],
            'nama_barang'    => $row['nama_barang'],
            'jenis_id'       => $row['jenis_id'],
            'size'           => $row['size'],
            'stok_minimum'   => $row['stok_minimum'],
            'stok_maximum'   => $row['stok_maximum'],
            'stok'           => $row['stok'],
            'nama_supplier'  => $row['supplier'],
            'price'          => $row['harga'],
            'gambar'         => $gambarPath,
        ]);
    }

    public function rules(): array
    {
        return [
            'jenis_id' => 'required|exists:jenis,id',
            'kode' => 'required|unique:barangs,kode',
            'nama_barang' => 'required',
            'size' => 'required',
            'stok_minimum' => 'required|numeric',
            'stok_maximum' => 'required|numeric',
            'stok' => 'required|numeric',
            'supplier' => 'required',
            'harga' => 'required|numeric',
            'gambar' => 'nullable|string' // URL atau path lokal
        ];
    }

    public function customValidationMessages()
    {
        return [
            'jenis_id.exists' => 'ID Jenis tidak ditemukan dalam database.',
            'gambar.string' => 'Format path gambar tidak valid.',
        ];
    }
}