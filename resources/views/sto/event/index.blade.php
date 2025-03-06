@extends('layouts.app')
@include('sto.event.create')
@include('sto.event.edit')

@section('content')
    <div class="section-header">
        <h1>Data Master Event</h1>
        <div class="ml-auto">
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_event"><i class="fa fa-plus"></i>
                Event</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="table_id" class="display" style="font-size: 13px;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Event</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                    <th>Opsi</th>
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
    <!-- Datatables Jquery -->
    <script>
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            return date.toISOString().split('T')[0]; 
        }

        $(document).ready(function() {
            $('#table_id').DataTable({
                paging: true,
                searching: true,
                ordering: true,
            });
            $.ajax({
                url: "/master-event/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    let counter = 1;
                    $('#table_id').DataTable().clear();
                    $.each(response.data, function(key, value) {
                        let event = `
                <tr class="event-row" id="index_${value.id}">
                    <td>${counter++}</td>   
                    <td>${value.nama_event}</td>
                    <td>${formatDate(value.tanggal_mulai)}</td>
                    <td>${formatDate(value.tanggal_selesai)}</td>
                    <td>${value.status}</td>
                    <td>
                        <a href="javascript:void(0)" id="button_edit_event" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                        <a href="javascript:void(0)" id="button_hapus_event" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                    </td>
                </tr>
            `;
                        $('#table_id').DataTable().row.add($(event)).draw(false);
                    });
                }
            });
        });
    </script>

    <!-- Show Modal Tambah Event -->
    <script>
        $('body').on('click', '#button_tambah_event', function() {
            $('#modal_tambah_event').modal('show');
        });

        $('#store').click(function(e) {
            e.preventDefault();

            let nama_event = $('#nama_event').val();
            let tanggal_mulai = $('#tanggal_mulai').val();
            let tanggal_selesai = $('#tanggal_selesai').val();
            let status = $('#status').val();
            let token = $("meta[name='csrf-token']").attr("content");

            let formData = new FormData();
            formData.append('nama_event', nama_event);
            formData.append('tanggal_mulai', tanggal_mulai);
            formData.append('tanggal_selesai', tanggal_selesai);
            formData.append('status', status);
            formData.append('_token', token);

            $.ajax({
                url: '/master-event',
                type: "POST",
                cache: false,
                data: formData,
                contentType: false,
                processData: false,

                success: function(response) {
                    Swal.fire({
                        type: 'success',
                        icon: 'success',
                        title: `${response.message}`,
                        showConfirmButton: true,
                        timer: 3000
                    });

                    $.ajax({
                        url: '/master-event/get-data',
                        type: "GET",
                        cache: false,
                        success: function(response) {
                            $('#table-barangs').html('');

                            let counter = 1;
                            $('#table_id').DataTable().clear();
                            $.each(response.data, function(key, value) {
                                let event = `
                                <tr class="event-row" id="index_${value.id}">
                                    <td>${counter++}</td>   
                                    <td>${value.nama_event}</td>
                                    <td>${formatDate(value.tanggal_mulai)}</td>
                                    <td>${formatDate(value.tanggal_selesai)}</td>
                                    <td>${value.status}</td>
                                    <td>
                                        <a href="javascript:void(0)" id="button_edit_event" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                        <a href="javascript:void(0)" id="button_hapus_event" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                    </td>
                                </tr>
                             `;
                                $('#table_id').DataTable().row.add($(event))
                                    .draw(false);
                            });

                            $('#nama_event').val('');
                            $('#tanggal_mulai').val('');
                            $('#tanggal_selesai').val('');
                            $('#status').val('');
                            $('#modal_tambah_event').modal('hide');

                            let table = $('#table_id').DataTable();
                            table.draw(); // memperbarui Datatables
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    })
                },

                error: function(error) {
                    if (error.responseJSON && error.responseJSON.nama_event && error.responseJSON
                        .nama_event[0]) {
                        $('#alert-nama_event').removeClass('d-none');
                        $('#alert-nama_event').addClass('d-block');

                        $('#alert-nama_event').html(error.responseJSON.nama_event[0]);
                    }
                }
            });
        });
    </script>

    <!-- Edit Data Event -->
    <script>
        //Show modal edit
        $('body').on('click', '#button_edit_event', function() {
            let event_id = $(this).data('id');

            $.ajax({
                url: `/master-event/${event_id}/edit`,
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#event_id').val(response.data.id);
                    $('#edit_nama_event').val(response.data.nama_event);
                    $('#edit_tanggal_mulai').val(formatDate(response.data.tanggal_mulai));
                    $('#edit_tanggal_selesai').val(formatDate(response.data.tanggal_selesai));
                    $('#edit_status').val(response.data.status);

                    $('#modal_edit_event').modal('show');
                }
            });
        });

        // Proses Update Data
        $('#update').click(function(e) {
            e.preventDefault();

            let event_id = $('#event_id').val();
            let nama_event = $('#edit_nama_event').val();
            let tanggal_mulai = $('#edit_tanggal_mulai').val();
            let tanggal_selesai = $('#edit_tanggal_selesai').val();
            let status = $('#edit_status').val();
            let token = $("meta[name='csrf-token']").attr('content');

            let formData = new FormData();
            formData.append('nama_event', nama_event);
            formData.append('tanggal_mulai', tanggal_mulai);
            formData.append('tanggal_selesai', tanggal_selesai);
            formData.append('status', status);
            formData.append('_token', token);
            formData.append('_method', 'PUT');

            $.ajax({
                url: `/master-event/${event_id}`,
                type: "POST",
                cache: false,
                data: formData,
                contentType: false,
                processData: false,

                success: function(response) {
                    Swal.fire({
                        type: 'success',
                        icon: 'success',
                        title: `${response.message}`,
                        showConfirmButton: true,
                        timer: 3000
                    });

                    let row = $(`#index_${response.data.id}`);
                    let rowData = row.find('td');
                    rowData.eq(1).text(response.data.nama_event);
                    rowData.eq(2).text(formatDate(response.data.tanggal_mulai));
                    rowData.eq(3).text(formatDate(response.data.tanggal_selesai));
                    rowData.eq(4).text(response.data.status);

                    $('#modal_edit_event').modal('hide');
                },

                error: function(error) {
                    if (error.responseJSON && error.responseJSON.nama_event && error.responseJSON
                        .nama_event[0]) {
                        $('#alert-nama_event').removeClass('d-none');
                        $('#alert-nama_event').addClass('d-block');

                        $('#alert-nama_event').html(error.responseJSON.nama_event[0]);
                    }
                }
            });
        });
    </script>

    <!-- Hapus Data Barang -->
    <script>
        $('body').on('click', '#button_hapus_event', function() {
            let event_id = $(this).data('id');
            let token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Apakah Kamu Yakin?',
                text: "ingin menghapus data ini !",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'TIDAK',
                confirmButtonText: 'YA, HAPUS!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/master-event/${event_id}`,
                        type: "DELETE",
                        cache: false,
                        data: {
                            "_token": token
                        },
                        success: function(response) {
                            Swal.fire({
                                type: 'success',
                                icon: 'success',
                                title: `${response.message}`,
                                showConfirmButton: true,
                                timer: 3000
                            });
                            $('#table_id').DataTable().clear().draw();

                            $.ajax({
                                url: "/master-event/get-data",
                                type: "GET",
                                dataType: 'JSON',
                                success: function(response) {
                                    let counter = 1;
                                    $('#table_id').DataTable().clear();
                                    $.each(response.data, function(key, value) {
                                        let event = `
                                        <tr class="event-row" id="index_${value.id}">
                                            <td>${counter++}</td>   
                                            <td>${value.nama_event}</td>
                                            <td>${formatDate(value.tanggal_mulai)}</td>
                                            <td>${formatDate(value.tanggal_selesai)}</td>
                                            <td>${value.status}</td>
                                            <td>
                                                <a href="javascript:void(0)" id="button_edit_event" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                                                <a href="javascript:void(0)" id="button_hapus_event" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>
                                            </td>
                                        </tr>
                                    `;
                                        $('#table_id').DataTable().row.add(
                                            $(event)).draw(false);
                                    });
                                }
                            });
                        }
                    })
                }
            });
        });
    </script>
@endsection
