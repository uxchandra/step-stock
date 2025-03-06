@extends('layouts.app')

@section('content')
    <div class="section-header">
        <h1>Data STO Items</h1>
        <div class="ml-auto">
            <!-- Opsional: Bisa tambah tombol kalau ada fungsi tambahan -->
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_id" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Stok Sistem</th>
                                    <th>Stok Aktual</th>
                                    <th>Selisih</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Datatables dan JavaScript -->
    <script>
        $(document).ready(function() {
            const table = $('#table_id').DataTable({
                paging: true,
                searching: true,
                ordering: true,
            });

            // Load data sto_items
            $.ajax({
                url: "/sto-items/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    table.clear();
                    let counter = 1;
                    $.each(response.data, function(key, value) {
                        table.row.add([
                            counter++,
                            value.kode_barang,
                            value.nama_barang,
                            value.stok_sistem,
                            value.stok_aktual,
                            value.selisih,
                            
                        ]).draw(false);
                    });
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                }
            });
        });
    </script>
@endsection