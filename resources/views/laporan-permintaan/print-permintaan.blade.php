<!DOCTYPE html>
<html>
<head>
    <title>Laporan Permintaan Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 5px;
        }
        th {
            background-color: #f5f5f5;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .periode {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Permintaan Barang</h2>
        @if($tanggalMulai && $tanggalSelesai)
            <div class="periode">
                Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->format('d F Y') }} - 
                        {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d F Y') }}
            </div>
        @endif
    </div>

    @foreach($groupedData as $tanggal => $items)
        <table>
            <tr>
                <th width="20%">Tanggal</th>
                <th width="50%">Nama Barang</th>
                <th width="30%">Jumlah</th>
            </tr>
            @foreach($items as $index => $item)
                <tr>
                    @if($index === 0)
                        <td rowspan="{{ count($items) }}">{{ $tanggal }}</td>
                    @endif
                    <td>{{ $item['nama_barang'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                </tr>
            @endforeach
        </table>
    @endforeach
</body>
</html>