@extends('layouts.app')

@include('barang.create')
@include('barang.edit')
@include('barang.show')
@include('barang.import')

@section('content')
    <div class="section-header">
        <h1>Data Barang</h1>
        <div class="ml-auto">
            <a href="javascript:void(0)" class="btn btn-danger" id="button_print_all"><i class="fa fa-print"></i> 
                Print All
            </a>
            {{-- <a href="javascript:void(0)" class="btn btn-warning" id="button_print_selected"><i class="fa fa-print"></i> 
                Print Selected
            </a> --}}
            <a href="javascript:void(0)" class="btn btn-info" id="button_import"><i class="fa fa-upload"></i> 
                Import Data
            </a>
            <a href="javascript:void(0)" class="btn btn-primary" id="button_tambah_barang"><i class="fa fa-plus"></i> Tambah
                Barang</a>
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
                                    {{-- <th><input type="checkbox" id="select_all_items"></th> --}}
                                    <th>Kode</th>
                                    <th>Gambar</th>
                                    <th>Nama Barang</th>
                                    <th>Spesifikasi</th>
                                    {{-- <th>Jenis</th> --}}
                                    <th>Stok Min.</th>
                                    <th>Supplier</th>
                                    <th>Stok</th>
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

    <script>
        // Modifikasi function loadData() untuk menampilkan barcode
        function loadData() {
            $.ajax({
                url: "/barang/get-data",
                type: "GET",
                dataType: 'JSON',
                success: function(response) {
                    let table = $('#table_id').DataTable();
                    table.clear();

                    if (response.data.length === 0) {
                        $('#table_id tbody').html('<tr><td colspan="8" style="text-align:center;">Tidak ada data tersedia</td></tr>');
                        return;
                    }
                    
                    $.each(response.data, function(key, value) {
                        let stok = value.stok != null ? value.stok : 0;
                        let stokMin = value.stok_minimum;
                        let rowClass = "";

                        // Menentukan warna berdasarkan stok
                        // if (stok == 0) {
                        //     rowClass = "table-danger";
                        // } else if (stok <= stokMin) {
                        //     rowClass = "table-warning";
                        // }

                        // Buat element gambar jika ada
                        let gambarHTML = value.gambar ? 
                            `<img src="${value.gambar}" alt="Gambar Barang" style="height: 80px; width: 80px; object-fit: cover;">` : 
                            `<div style="height: 80px; width: 80px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; color: #666;">No Img</div>`;

                        let rowNode = table.row.add([
                            // `<input type="checkbox" class="item_checkbox" data-id="${value.id}">`,
                            value.barcode_html,
                            gambarHTML,  // Tampilkan gambar sebagai img tag
                            value.nama_barang,
                            value.size,
                            stokMin,
                            value.nama_supplier,
                            stok,
                            `<a href="javascript:void(0)" id="button_detail_barang" data-id="${value.id}" class="btn btn-icon btn-success btn-lg mb-2"><i class="far fa-eye"></i> </a>
                            <a href="javascript:void(0)" id="button_edit_barang" data-id="${value.id}" class="btn btn-icon btn-warning btn-lg mb-2"><i class="far fa-edit"></i> </a>
                            <a href="javascript:void(0)" class="btn btn-icon btn-info btn-lg mb-2 button_print_single" data-id="${value.id}"><i class="fa fa-print"></i> </a>
                            <a href="javascript:void(0)" id="button_hapus_barang" data-id="${value.id}" class="btn btn-icon btn-danger btn-lg mb-2"><i class="fas fa-trash"></i> </a>`
                        ]).draw(false).node();

                        $(rowNode).addClass(rowClass);
                    });

                    // Close the loading indicator when data is loaded
                    Swal.close();
                },
                error: function(xhr, status, error) {
                    console.error('Error loading data:', error);
                    $('#table_id tbody').html('<tr><td colspan="8" style="text-align:center; color:red;">Gagal memuat data. Silakan coba lagi.</td></tr>');
                }
            });
        }
    </script>

    <!-- Datatables Jquery -->
    <script>
       $(document).ready(function() {

        $('<style>')
        .text(`
            .custom-loader {
                width: 40px;
                height: 40px;
                border: 5px solid #f3f3f3;
                border-top: 5px solid #3498db;
                border-radius: 50%;
                display: inline-block;
                animation: spin 1s linear infinite;
                margin-right: 10px;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
            .loading-container {
                text-align: center;
                padding: 20px;
            }
        `)
        .appendTo('head');

        // Inisialisasi DataTable
        let table = $('#table_id').DataTable({
            processing: true,
            language: {
                emptyTable: '<div class="loading-container"><div class="custom-loader"></div><div>Sedang memuat data...</div></div>'
            }
        });

        // Set initial state to show loading in the table
        $('#table_id tbody').html('<tr><td colspan="8" class="loading-container"><div class="custom-loader"></div><div>Sedang memuat data barang...</div></td></tr>');
        
        loadData();

        $('body').on('click', '#button_import', function() {
            $('#modal_import').modal('show');
        });
        
        // Handle form import submit
        $('#importForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            
            $.ajax({
                url: '/barang/import',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Tutup modal
                        $('#modal_import').modal('hide');
                        
                        // Reset form
                        $('#importForm')[0].reset();
                        
                        // Tampilkan pesan sukses
                        Swal.fire('Sukses!', response.message, 'success');
                        
                        // Reload data
                        loadData();
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON;
                    if (errors && errors.errors) {
                        Swal.fire('Error!', errors.errors.join('\n'), 'error');
                    } else {
                        Swal.fire('Error!', 'Terjadi kesalahan saat import data', 'error');
                    }
                }
            });
        });
    });
    </script>


    <!-- Show Modal Tambah Barang -->
    <script>
        $('body').on('click', '#button_tambah_barang', function() {
            $('#modal_tambah_barang').modal('show');
        });

        $('#store').click(function(e) {
        e.preventDefault();

        let kode = $('#kode').val();
        let gambar = $('#gambar')[0].files[0];
        let nama_barang = $('#nama_barang').val();
        let jenis_id = $('#jenis_id').val();
        let size = $('#size').val();
        let stok_minimum = $('#stok_minimum').val();
        let stok_maximum = $('#stok_maximum').val();
        let stok = $('#stok').val();
        let nama_supplier = $('#nama_supplier').val();
        let price = $('#price').val();
        let token = $("meta[name='csrf-token']").attr("content");

        let formData = new FormData();
        formData.append('kode', kode);
        formData.append('gambar', gambar);
        formData.append('nama_barang', nama_barang);
        formData.append('jenis_id', jenis_id);
        formData.append('size', size);
        formData.append('stok_minimum', stok_minimum);
        formData.append('stok_maximum', stok_maximum);
        formData.append('stok', stok);
        formData.append('nama_supplier', nama_supplier);
        formData.append('price', price);
        formData.append('_token', token);

        $.ajax({
            url: '/barang',
            type: "POST",
            cache: false,
            data: formData,
            contentType: false,
            processData: false,

            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: `${response.message}`,
                    showConfirmButton: true,
                    timer: 3000
                });

                // Reset form fields
                $('#kode').val('');
                $('#nama_barang').val('');
                $('#jenis_id').val('');
                $('#size').val('');
                $('#stok_minimum').val('');
                $('#stok_maximum').val('');
                $('#stok').val('');
                $('#nama_supplier').val('');
                $('#price').val('');
                $('#gambar').val(''); // Reset input file
                $('#preview').attr('src', '');

                // Close modal
                $('#modal_tambah_barang').modal('hide');

                // Panggil loadData() untuk memuat ulang data dengan barcode
                loadData();
            },

            error: function(error) {
                if (error.responseJSON) {
                    if (error.responseJSON.gambar) {
                        $('#alert-gambar').removeClass('d-none').addClass('d-block').html(error.responseJSON.gambar[0]);
                    }
                    if (error.responseJSON.kode) {
                        $('#alert-kode').removeClass('d-none').addClass('d-block').html(error.responseJSON.kode[0]);
                    }
                    if (error.responseJSON.nama_barang) {
                        $('#alert-nama_barang').removeClass('d-none').addClass('d-block').html(error.responseJSON.nama_barang[0]);
                    }
                    if (error.responseJSON.jenis_id) {
                        $('#alert-jenis_id').removeClass('d-none').addClass('d-block').html(error.responseJSON.jenis_id[0]);
                    }
                    if (error.responseJSON.size) {
                        $('#alert-size').removeClass('d-none').addClass('d-block').html(error.responseJSON.size[0]);
                    }
                    if (error.responseJSON.stok_minimum) {
                        $('#alert-stok_minimum').removeClass('d-none').addClass('d-block').html(error.responseJSON.stok_minimum[0]);
                    }
                    if (error.responseJSON.stok_maximum) {
                        $('#alert-stok_maximum').removeClass('d-none').addClass('d-block').html(error.responseJSON.stok_maximum[0]);
                    }
                    if (error.responseJSON.stok) {
                        $('#alert-stok').removeClass('d-none').addClass('d-block').html(error.responseJSON.stok[0]);
                    }
                    if (error.responseJSON.nama_supplier) {
                        $('#alert-nama_supplier').removeClass('d-none').addClass('d-block').html(error.responseJSON.nama_supplier[0]);
                    }
                    if (error.responseJSON.price) {
                        $('#alert-price').removeClass('d-none').addClass('d-block').html(error.responseJSON.price[0]);
                    }
                }
            }
        });
    });
    </script>

    <!-- Show Detail Data Barang -->
    <script>
        $('body').on('click', '#button_detail_barang', function() {
            let barang_id = $(this).data('id');

            $.ajax({
                url: `/barang/${barang_id}/`,
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#barang_id').val(response.data.id);
                    $('#detail_gambar').val(null);
                    $('#detail_kode').val(response.data.kode);
                    $('#detail_nama_barang').val(response.data.nama_barang);
                    $('#detail_jenis_id').val(response.data.jenis_id);
                    $('#detail_size').val(response.data.size);
                    $('#detail_stok_minimum').val(response.data.stok_minimum);
                    $('#detail_stok_maximum').val(response.data.stok_maximum);
                    $('#detail_stok').val(response.data.stok != null ? response.data.stok : 'Stok Kosong');
                    $('#detail_nama_supplier').val(response.data.nama_supplier);
                    $('#detail_price').val(response.data.price);
                    $('#detail_gambar_preview').attr('src', '/storage/' + response.data.gambar);

                    $('#modal_detail_barang').modal('show');
                }
            });
        });
    </script>


    <!-- Edit Data Barang -->
    <script>
        // Menampilkan Form Modal Edit
        $('body').on('click', '#button_edit_barang', function() {
            let barang_id = $(this).data('id');

            $.ajax({
                url: `/barang/${barang_id}/edit`,
                type: "GET",
                cache: false,
                success: function(response) {
                    $('#barang_id').val(response.data.id);
                    $('#edit_gambar').val(null); // Reset input file
                    $('#edit_kode').val(response.data.kode);
                    $('#edit_nama_barang').val(response.data.nama_barang);
                    $('#edit_jenis_id').val(response.data.jenis_id);
                    $('#edit_size').val(response.data.size);
                    $('#edit_stok_minimum').val(response.data.stok_minimum);
                    $('#edit_stok_maximum').val(response.data.stok_maximum);
                    $('#edit_nama_supplier').val(response.data.nama_supplier);
                    $('#edit_price').val(response.data.price);
                    $('#edit_gambar_preview').attr('src', '/storage/' + response.data.gambar);

                    $('#modal_edit_barang').modal('show');
                }
            });
        });

        // Proses Update Data
        $('#update').click(function(e) {
            e.preventDefault();

            let barang_id = $('#barang_id').val();
            let gambar = $('#edit_gambar')[0].files[0];
            let kode = $('#edit_kode').val();
            let nama_barang = $('#edit_nama_barang').val();
            let jenis_id = $('#edit_jenis_id').val();
            let size = $('#edit_size').val();
            let stok_minimum = $('#edit_stok_minimum').val();
            let stok_maximum = $('#edit_stok_maximum').val();
            let nama_supplier = $('#edit_nama_supplier').val();
            let price = $('#edit_price').val();
            let token = $("meta[name='csrf-token']").attr("content");

            // Gunakan FormData untuk mengirim file
            let formData = new FormData();
            formData.append('_token', token);
            formData.append('_method', 'PUT');
            formData.append('kode', kode);
            formData.append('gambar', gambar); // File akan undefined jika tidak ada gambar baru
            formData.append('nama_barang', nama_barang);
            formData.append('jenis_id', jenis_id);
            formData.append('size', size);
            formData.append('stok_minimum', stok_minimum);
            formData.append('stok_maximum', stok_maximum);
            formData.append('nama_supplier', nama_supplier);
            formData.append('price', price);

            $.ajax({
                url: `/barang/${barang_id}`,
                type: "POST", // POST dengan _method: PUT untuk Laravel
                data: formData,
                contentType: false, // Penting untuk FormData
                processData: false, // Penting untuk FormData
                cache: false,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: `${response.message}`,
                        showConfirmButton: true,
                        timer: 3000
                    });

                    loadData();
                    $('#modal_edit_barang').modal('hide');
                },
                error: function(error) {
                    if (error.responseJSON) {
                        let errors = error.responseJSON;

                        if (errors.kode) {
                            $('#alert-kode').removeClass('d-none').addClass('d-block').html(errors.kode[0]);
                        }
                        if (errors.gambar) {
                            $('#alert-gambar').removeClass('d-none').addClass('d-block').html(errors.gambar[0]);
                        }
                        if (errors.nama_barang) {
                            $('#alert-nama_barang').removeClass('d-none').addClass('d-block').html(errors.nama_barang[0]);
                        }
                        if (errors.jenis_id) {
                            $('#alert-jenis_id').removeClass('d-none').addClass('d-block').html(errors.jenis_id[0]);
                        }
                        if (errors.size) {
                            $('#alert-size').removeClass('d-none').addClass('d-block').html(errors.size[0]);
                        }
                        if (errors.stok_minimum) {
                            $('#alert-stok_minimum').removeClass('d-none').addClass('d-block').html(errors.stok_minimum[0]);
                        }
                        if (errors.stok_maximum) {
                            $('#alert-stok_maximum').removeClass('d-none').addClass('d-block').html(errors.stok_maximum[0]);
                        }
                        if (errors.stok) {
                            $('#alert-stok').removeClass('d-none').addClass('d-block').html(errors.stok[0]);
                        }
                        if (errors.nama_supplier) {
                            $('#alert-nama_supplier').removeClass('d-none').addClass('d-block').html(errors.nama_supplier[0]);
                        }
                        if (errors.price) {
                            $('#alert-price').removeClass('d-none').addClass('d-block').html(errors.price[0]);
                        }
                    }
                }
            });
        });
    </script>
        

    <!-- Hapus Data Barang -->
    <script>
        $('body').on('click', '#button_hapus_barang', function() {
            let barang_id = $(this).data('id');
            let token = $("meta[name='csrf-token']").attr("content");

            Swal.fire({
                title: 'Apakah Kamu Yakin?',
                text: "ingin menghapus data ini!",
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'TIDAK',
                confirmButtonText: 'YA, HAPUS!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/barang/${barang_id}`,
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

                            // Hapus data dari cache DataTables
                            $('#table_id').DataTable().clear().draw();

                            // Ambil ulang data dan gambar tabel
                            $.ajax({
                                url: "/barang/get-data",
                                type: "GET",
                                dataType: 'JSON',
                                success: function(response) {
                                    loadData();
                                    
                                }
                            });
                        }
                    })
                }
            })
        })
    </script>

    <!-- Preview Image -->
    <script>
        function previewImage() {
            preview.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

    <script>
        function previewImageEdit() {
            edit_gambar_preview.src = URL.createObjectURL(event.target.files[0]);
        }
    </script>

    <!-- HANDLE PRINT -->
    <script>
        $(document).ready(function() {
            loadData();

            // Toggle select all checkbox
            $('#select_all_items').on('click', function() {
                $('.item_checkbox').prop('checked', $(this).prop('checked'));
            });
            
            // Print Selected Items
            $('#button_print_selected').on('click', function() {
                let selectedIds = [];
                $('.item_checkbox:checked').each(function() {
                    selectedIds.push($(this).data('id'));
                });
                
                if (selectedIds.length === 0) {
                    Swal.fire('Info!', 'Silakan pilih barang yang ingin dicetak', 'info');
                    return;
                }
                
                // Redirect ke halaman print dengan parameter ID yang dipilih
                let url = '/barang/print?ids=' + selectedIds.join(',');
                window.open(url, '_blank');
            });
            
            // Print All Items
            $('#button_print_all').on('click', function() {
                window.open('/barang/print', '_blank');
            });
            
            // Print Single Item
            $('body').on('click', '.button_print_single', function() {
                let id = $(this).data('id');
                window.open('/barang/print?ids=' + id, '_blank');
            });
        });
    </script>
@endsection
