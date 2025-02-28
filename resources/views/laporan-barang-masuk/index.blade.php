@extends('layouts.app')

@section('content')
<div class="section-header">
    <h1>Laporan Barang Masuk</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-primary" id="print-barang-masuk">
            <i class="fa fa-sharp fa-light fa-print"></i> Print PDF
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <form id="filter_form" action="/laporan-barang-masuk/get-data" method="GET">
                        <div class="row">
                            <div class="col-md-5">
                                <label>Pilih Tanggal Mulai :</label>
                                <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
                            </div>
                            <div class="col-md-5">
                                <label>Pilih Tanggal Selesai :</label>
                                <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-1">Filter</button>
                                <button type="button" class="btn btn-danger" id="refresh_btn">Refresh</button>
                            </div>
                        </div>
                    </form>
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
                                <th>Tanggal Masuk</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Masuk</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-laporan-barang-masuk">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#table_id').DataTable({ paging: true });

    loadData();

    $('#filter_form').submit(function(event) {
        event.preventDefault();
        loadData();
    });

    $('#refresh_btn').on('click', function() {
        refreshTable();
    });

    function loadData() {
        var tanggalMulai = $('#tanggal_mulai').val();
        var tanggalSelesai = $('#tanggal_selesai').val();

        $.ajax({
            url: '/laporan-barang-masuk/get-data',
            type: 'GET',
            dataType: 'json',
            data: {
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai
            },
            success: function(response) {
                table.clear().draw();

                if (response.length > 0) {
                    $.each(response, function(index, item) {
                        var tanggalMasuk = new Date(item.tanggal_masuk);
                        var formattedDate = ('0' + tanggalMasuk.getDate()).slice(-2) + '-' +
                                          ('0' + (tanggalMasuk.getMonth() + 1)).slice(-2) + '-' +
                                          tanggalMasuk.getFullYear();

                        var row = [
                            (index + 1),
                            formattedDate,
                            item.nama_barang,
                            item.jumlah_masuk
                        ];
                        table.row.add(row).draw(false);
                    });
                } else {
                    var emptyRow = ['', 'Tidak ada data yang tersedia.', '', ''];
                    table.row.add(emptyRow).draw(false);
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    }

    function refreshTable() {
        $('#filter_form')[0].reset();
        loadData();
    }

    $('#print-barang-masuk').on('click', function(e) {
        e.preventDefault();
        var tanggalMulai = $('#tanggal_mulai').val();
        var tanggalSelesai = $('#tanggal_selesai').val();

        var url = '/laporan-barang-masuk/print-barang-masuk';

        if (tanggalMulai && tanggalSelesai) {
            url += '?tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
        }

        window.open(url, '_blank');
    });
});
</script>
@endsection