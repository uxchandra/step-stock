@extends('layouts.app')

@section('content')
    <div class="section-header">
        <h1>Data STO Items</h1>
        <div class="ml-auto d-flex align-items-center">
            <!-- Filter Event -->
            <select id="eventFilter" class="form-control mr-2" style="width: auto;">
                <option value="">Semua Event</option>
                @foreach ($events as $event)
                    <option value="{{ $event->id }}">{{ $event->nama_event }}</option>
                @endforeach
            </select>
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
                                    <th>Waktu Scan</th>
                                    <th>Scanned By</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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

    <!-- Modal Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStatusModalLabel">Update Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id="updateStatusForm">
                    <div class="modal-body">
                        <input type="hidden" id="item_id" name="item_id">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" name="status">
                                <option value="open">Open</option>
                                <option value="close">Close</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
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

            // Fungsi untuk load data berdasarkan filter
            function loadData(eventId = '') {
                $.ajax({
                    url: "/sto-items/get-data",
                    type: "GET",
                    data: { event_id: eventId },
                    dataType: 'JSON',
                    success: function(response) {
                        table.clear();
                        let counter = 1;
                        $.each(response.data, function(key, value) {
                            let statusBadge = '';
                            if (value.status === 'open') {
                                statusBadge = '<span class="badge badge-primary">Open</span>';
                            } else {
                                statusBadge = '<span class="badge badge-success">Close</span>';
                            }
                            
                            let actionBtn = `
                                <button class="btn btn-sm btn-info update-status" data-id="${value.id}" data-status="${value.status}">
                                    <i class="fas fa-edit"></i> Status
                                </button>
                            `;
                            
                            table.row.add([
                                counter++,
                                value.kode_barang,
                                value.nama_barang,
                                value.stok_sistem,
                                value.stok_aktual,
                                value.selisih,
                                value.waktu_scan,
                                value.scanned_by,
                                statusBadge,
                                actionBtn
                            ]).draw(false);
                        });
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                    }
                });
            }

            // Load data awal
            loadData();

            // Filter berdasarkan event
            $('#eventFilter').on('change', function() {
                const eventId = $(this).val();
                loadData(eventId);
            });
            
            // Handle klik tombol update status
            $(document).on('click', '.update-status', function() {
                const id = $(this).data('id');
                const status = $(this).data('status');
                
                $('#item_id').val(id);
                $('#status').val(status);
                $('#updateStatusModal').modal('show');
            });
            
            // Handle submit form update status
            $('#updateStatusForm').on('submit', function(e) {
                e.preventDefault();
                
                const id = $('#item_id').val();
                const status = $('#status').val();
                
                $.ajax({
                    url: `/sto-items/${id}/status`,
                    type: 'PUT',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        status: status
                    },
                    success: function(response) {
                        $('#updateStatusModal').modal('hide');
                        
                        // Refresh table dengan filter yang aktif
                        const eventId = $('#eventFilter').val();
                        loadData(eventId);
                        
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status berhasil diperbarui',
                            icon: 'success',
                            timer: 1500
                        });
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat memperbarui status',
                            icon: 'error'
                        });
                    }
                });
            });
        });
    </script>
@endsection