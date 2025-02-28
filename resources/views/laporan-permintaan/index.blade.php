@extends('layouts.app')

@section('content')
<div class="section-header d-flex justify-content-between align-items-center">
    <h1>Laporan Permintaan Barang</h1>
    <div>
        <a href="javascript:void(0)" class="btn btn-danger" id="print-permintaan">
            <i class="fas fa-print"></i> Print PDF
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <form id="filter_form" action="/laporan-permintaan/get-data" method="GET">
                        <div class="row">
                            <div class="col-md-3">
                                <label>Tanggal Mulai:</label>
                                <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
                            </div>
                            <div class="col-md-3">
                                <label>Tanggal Selesai:</label>
                                <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
                            </div>
                            {{-- <div class="col-md-3">
                                <label>Department:</label>
                                <select class="form-control" name="department_id" id="department_id">
                                    <option value="">Semua Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->nama_departemen }}</option>
                                    @endforeach
                                </select>
                            </div> --}}
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary mr-2">Filter</button>
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
                    <table id="table_id" class="table table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Permintaan</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Permintaan</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-laporan-permintaan">
                            <!-- Data akan diisi oleh AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Get Data -->
<script>
    $(document).ready(function() {
        var table = $('#table_id').DataTable({
            paging: true,
            searching: true,
            ordering: true,
        });

        loadData(); // Load data pertama kali

        $('#filter_form').submit(function(event) {
            event.preventDefault();
            loadData(); // Load data berdasarkan filter
        });

        $('#refresh_btn').on('click', function() {
            refreshTable();
        });

        function loadData() {
            var tanggalMulai = $('#tanggal_mulai').val();
            var tanggalSelesai = $('#tanggal_selesai').val();
            var departmentId = $('#department_id').val();

            $.ajax({
                url: '/laporan-permintaan/get-data',
                type: 'GET',
                dataType: 'json',
                data: {
                    tanggal_mulai: tanggalMulai,
                    tanggal_selesai: tanggalSelesai,
                    department_id: departmentId
                },
                success: function(response) {
                    table.clear().draw(); // Bersihkan tabel sebelum mengisi data baru

                    if (response.length > 0) {
                        $.each(response, function(index, item) {
                            var tanggalPermintaan = new Date(item.tanggal);
                            var formattedDate = ('0' + tanggalPermintaan.getDate()).slice(-2) + '-' +
                                ('0' + (tanggalPermintaan.getMonth() + 1)).slice(-2) + '-' +
                                tanggalPermintaan.getFullYear();

                            var row = [
                                (index + 1),
                                formattedDate,
                                item.nama_barang,
                                item.jumlah_permintaan,
                            ];
                            table.row.add(row).draw(false); // Tambahkan baris ke tabel
                        });
                    } else {
                        var emptyRow = ['', 'Tidak ada data yang tersedia.', '', '', '', '', ''];
                        table.row.add(emptyRow).draw(false); // Tampilkan pesan jika tidak ada data
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        // Fungsi Refresh Tabel
        function refreshTable() {
            $('#filter_form')[0].reset();
            loadData();
        }

        // Print permintaan
        $('#print-permintaan').on('click', function(e) {
            e.preventDefault();
            var tanggalMulai = $('#tanggal_mulai').val();
            var tanggalSelesai = $('#tanggal_selesai').val();
            var departmentId = $('#department_id').val();

            var url = '/laporan-permintaan/print';
            var params = [];
            if (tanggalMulai) params.push('tanggal_mulai=' + tanggalMulai);
            if (tanggalSelesai) params.push('tanggal_selesai=' + tanggalSelesai);
            if (departmentId) params.push('department_id=' + departmentId);

            if (params.length > 0) {
                url += '?' + params.join('&');
            }

            window.open(url, '_blank');
        });
    });
</script>
@endsection