@extends('layouts.print')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2>Daftar Barcode Barang</h2>
            <p>{{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
    
    <div class="row">
        @foreach($barangs as $index => $barang)
            <div class="col-4">
                <div class="card">
                    <div class="qr-container">
                        {!! $barang->barcode_html !!}
                        <p class="kode-barang"><strong>{{ $barang->kode }}</strong></p>
                    </div>
                    <div class="item-details">
                        <p class="nama-barang"><strong>{{ $barang->nama_barang }}</strong></p>
                        <hr style="margin: 5px 0;">
                        <div class="min-max-container">
                            <div class="min-max-values">
                                <span>MIN</span>
                                <span>{{ $barang->stok_minimum }}</span>
                            </div>
                            <div class="min-max-values">
                                <span>MAX</span>
                                <span>{{ $barang->stok_maksimum ?? 5 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @if(($index + 1) % 3 == 0 && !$loop->last)
                </div><div class="row" style="margin-top: 0; margin-bottom: 0;">
            @endif
        @endforeach
    </div>
</div>

<script>
    // Auto print saat halaman dimuat
    window.onload = function() {
        window.print();
    }
</script>

<style>
    .row {
        display: flex;
        flex-wrap: wrap;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        border: none !important;
        border-radius: 0 !important;
        margin-bottom: 0 !important;
        margin-top: 0 !important;
    }

    /* Tambahkan border pada kolom dengan cara khusus */
    .col-4 {
        padding: 0 !important; /* Hapus padding agar bordernya tersambung */
        margin: 0 !important;
        border: 1px solid #000;
        position: relative;
    }

    /* Hapus border duplikat antara kolom yang berdampingan */
    .col-4:not(:first-child):not(:nth-child(3n+1)) {
        border-left: none; /* Hapus border kiri kecuali di awal baris baru */
    }

    .col-4:not(:nth-child(-n+3)) {
        border-top: none; /* Hapus border atas kecuali di baris pertama */
    }

    .qr-container {
        margin-right: -20px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .qr-container svg {
        width: 80px;
        height: 80px;
    }
    .kode-barang {
        margin-top: -10px;
        font-size: 14px;
    }
    .item-details {
        flex: 1;
        text-align: center;
    }
    .nama-barang {
        margin-bottom: 5px;
        font-size: 10px;
    }
    .min-max-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0;
    }
    .min-max-values {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 2px 0;
    }
    .min-max-values span {
        width: 40px;
        text-align: center;
        font-size: 10px;
    }
    @media print {
        .col-4 {
            border: 1px solid #000;
        }

        .col-4:not(:first-child):not(:nth-child(3n+1)) {
            border-left: none;
        }
        
        .col-4:not(:nth-child(-n+3)) {
            border-top: none;
        }
        .row {
            margin: 0; 
        }
        .card {
            border: 1px solid #000;
            padding: 5px; 
            margin: 0;
            border-radius: 0; 
        }
        .nama-barang {
            font-size: 6px;
        }
        .kode-barang {
            font-size: 10px;
        }
        .min-max-values span {
            font-size: 10px;
            width: 35px;
        }

        p {
            margin: 0;
            font-size: 10px;
        }
        p strong {
            font-size: 10px;
        }
    }
</style>
@endsection