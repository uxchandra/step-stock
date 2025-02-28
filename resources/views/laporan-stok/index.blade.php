@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Laporan Stok</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-primary" id="print-stok"><i class="fa fa-sharp fa-light fa-print"></i> Print PDF</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="opsi-laporan-stok">Filter Stok Berdasarkan :</label>
                    <select class="form-control" name="opsi-laporan-stok" id="opsi-laporan-stok">
                        <option value="semua" selected>Semua</option>
                        <option value="minimum">Batas Minimum</option>
                        <option value="stok-habis">Stok Habis</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="table_id" class="display">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Stok</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-laporan-stok">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dropdown -->
<script>
    $(document).ready(function() {
        var table = $('#table_id').DataTable({
            paging: true
        });

        loadData('semua');

        $('#opsi-laporan-stok').on('change', function(){
            var selectedOption = $(this).val();
            loadData(selectedOption);
        });

        function loadData(selectedOption) {
            $.ajax({
                url: '/laporan-stok/get-data',
                type: 'GET',
                data: { opsi: selectedOption },
                success: function(response){
                    table.clear().draw();

                    let counter = 1;
                    $.each(response, function(index, item) {
                        // Menentukan kelas warna berdasarkan stok
                        let rowClass = "";
                        if (item.stok == 0) {
                            rowClass = "table-danger"; // Merah: Stok habis
                        } else if (item.stok <= item.stok_minimum) {
                            rowClass = "table-warning"; // Kuning: Batas minimum
                        }

                        // Menambahkan baris dengan warna
                        let rowNode = table.row.add([
                            counter++,
                            item.kode, 
                            item.nama_barang,
                            item.stok
                        ]).draw(false).node();

                        // Tambahkan class warna ke baris
                        $(rowNode).addClass(rowClass);
                    });
                }
            });
        }


        $('#print-stok').on('click', function(e){
            e.preventDefault();
            var selectedOption = $('#opsi-laporan-stok').val();
            // Membuka di tab baru dengan window.open()
            window.open('/laporan-stok/print-stok?opsi=' + selectedOption, '_blank');
        });
    });
</script>

@endsection