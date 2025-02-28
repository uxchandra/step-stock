<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
        .container, .container-fluid {
            padding: 0 !important;
        }

        /* Khusus untuk header tidak terlalu rapat */
        .row.mb-4 {
            margin-bottom: 10px !important;
        }
        /* Layout default (non-print mode) */
        .row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .col-4 {
            width: 33%; /* Menyesuaikan agar 4 kolom per baris */
            margin: 10px;
        }
        .card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 130px;
        }
        .qr-container {
            flex: 1;
        }
        .item-details {
            flex: 2;
            text-align: left;
            padding-left: 5px;
        }
        .item-details h5 {
            font-size: 12px; /* Mengecilkan ukuran font nama barang */
        }
    
        /* Mode cetak */
        @media print {
            .row {
                display: flex;
                flex-wrap: wrap;
                justify-content: start;
            }
            .col-4 {
                width: 33%; /* Menyesuaikan agar 4 kolom per baris */
                float: left;
                margin: 1%;
            }
            .card {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 8px;
                border: 1px solid #000;
                min-height: auto;
            }
            .item-details h5 {
                font-size: 10px; /* Ukuran font lebih kecil saat cetak */
            }
            .no-print {
                display: none;
            }
        }
    </style>          
</head>
<body>
    <div class="container-fluid">
        @yield('content')
    </div>
    
    <div class="no-print text-center mt-4">
        <button onclick="window.print()" class="btn btn-primary">Print</button>
        <button onclick="window.close()" class="btn btn-secondary">Close</button>
    </div>
</body>
</html>