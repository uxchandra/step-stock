@extends('layouts.app')
@section('content')
    <div class="section-header">
        <h1>Tambah Barang Masuk</h1>
    </div>
    <div class="container-fluid mt-2">
        <form method="post" action="{{ route('barang-masuk.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-8 col-sm-8">
                    <div class="card">
                        <div class="card-body">
                            <!-- Tambahkan form scan di sini -->
                            <div class="form-group row">
                                <div class="col-md-8">
                                    <input type="text" class="form-control" id="kode_barang" placeholder="Scan/Input Kode Barang">
                                </div>
                                <div class="col-md-4">
                                    <button class="btn btn-primary" id="submit_kode">
                                        <i class="fas fa-barcode"></i> Scan
                                    </button>
                                </div>
                            </div>
                            
                            <div class="table-responsive mt-4">
                                <table class="table" id="tableItem">
                                    <thead>
                                        <tr>
                                            <th class="text-center text-muted">Kode Barang</th>
                                            <th class="text-center text-muted">Nama Barang</th>
                                            <th class="text-center text-muted">Stok Saat Ini</th>
                                            <th class="text-center text-muted">Qty Masuk</th>
                                            <th class="text-center text-muted">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="tanggal_masuk">Tanggal Masuk</label>
                                <input type="date" class="form-control" id="tanggal_masuk" name="tanggal_masuk" value="{{ date('Y-m-d') }}" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary float-right">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var rowCounter = 0;

                // Focus pada input kode saat halaman dimuat
                $('#kode_barang').focus();

                // Handle ketika tombol Enter ditekan pada input kode (dipicu oleh scanner barcode)
                $('#kode_barang').keypress(function(e) {
                    if(e.which == 13) { // Enter key
                        e.preventDefault();
                        var code = $('#kode_barang').val();
                        
                        if (!code) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Peringatan',
                                text: 'Silakan masukkan kode barang!',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            return;
                        }

                        var existingRow = $('#tableItem').find('input.code[value="' + code + '"]').closest('tr');
                        
                        if (existingRow.length > 0) {
                            var qtyInput = existingRow.find('input.qty');
                            var currentQty = parseFloat(qtyInput.val());
                            qtyInput.val(currentQty + 1);
                            $('#kode_barang').val('').focus();
                        } else {
                            $.ajax({
                                type: 'GET',
                                url: "{{ url('/barang/kode') }}/" + code,
                                success: function(data) {
                                    if (data.success) {
                                        const newRow = `
                                            <tr id="rowItem${rowCounter}">
                                                <td><input type="text" class="form-control code" name="code[]" value="${data.barang.kode}" readonly disabled></td>
                                                <td><input type="text" class="form-control" name="nama_barang[]" value="${data.barang.nama_barang}" readonly disabled></td>
                                                <td><input type="text" class="form-control" name="stok[]" value="${data.barang.stok}" readonly disabled></td>
                                                <td><input type="number" class="form-control qty" name="qty[]" value="1" min="1"></td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm delete_row">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                                <td style="display: none">
                                                    <input type="hidden" name="barang_id[]" value="${data.barang.id}">
                                                </td>
                                            </tr>
                                        `;
                                        rowCounter++;
                                        $('#tableItem tbody').append(newRow);
                                        $('#kode_barang').val('').focus();
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops...',
                                            text: 'Barang tidak ditemukan',
                                            timer: 2000,
                                            showConfirmButton: false,
                                        });
                                        $('#kode_barang').val('').focus();
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Terjadi kesalahan saat mencari barang',
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    $('#kode_barang').val('').focus();
                                }
                            });
                        }
                    }
                });

                // Tambahkan handler untuk tombol scan
                $('#submit_kode').click(function(e) {
                    e.preventDefault();
                    var code = $('#kode_barang').val();
                    
                    if (!code) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan masukkan kode barang!',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        return;
                    }

                    // Copy paste kode yang sama dari event keypress di atas
                    var existingRow = $('#tableItem').find('input.code[value="' + code + '"]').closest('tr');
                    
                    if (existingRow.length > 0) {
                        var qtyInput = existingRow.find('input.qty');
                        var currentQty = parseFloat(qtyInput.val());
                        qtyInput.val(currentQty + 1);
                        $('#kode_barang').val('').focus();
                    } else {
                        $.ajax({
                            type: 'GET',
                            url: "{{ url('/barang/kode') }}/" + code,
                            success: function(data) {
                                // Kode yang sama seperti di event keypress
                                if (data.success) {
                                    const newRow = `
                                        <tr id="rowItem${rowCounter}">
                                            <td><input type="text" class="form-control code" name="code[]" value="${data.barang.kode}" readonly disabled></td>
                                            <td><input type="text" class="form-control" name="nama_barang[]" value="${data.barang.nama_barang}" readonly disabled></td>
                                            <td><input type="text" class="form-control" name="stok[]" value="${data.barang.stok}" readonly disabled></td>
                                            <td><input type="number" class="form-control qty" name="qty[]" value="1" min="1"></td>
                                            <td>
                                                <button class="btn btn-danger btn-sm delete_row">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                            <td style="display: none">
                                                <input type="hidden" name="barang_id[]" value="${data.barang.id}">
                                            </td>
                                        </tr>
                                    `;
                                    rowCounter++;
                                    $('#tableItem tbody').append(newRow);
                                    $('#kode_barang').val('').focus();
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Oops...',
                                        text: 'Barang tidak ditemukan',
                                        timer: 2000,
                                        showConfirmButton: false,
                                    });
                                    $('#kode_barang').val('').focus();
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan saat mencari barang',
                                    timer: 2000,
                                    showConfirmButton: false,
                                });
                                $('#kode_barang').val('').focus();
                            }
                        });
                    }
                });

                // Handle delete row
                $(document).on('click', '.delete_row', function(e) {
                    e.preventDefault(); // Mencegah perilaku default (seperti mengirimkan form atau mengarahkan ke #)
                    $(this).closest('tr').remove(); // Hapus baris yang sesuai
                    $('#kode_barang').focus(); // Fokus kembali ke input kode barang
                });

                // Handle form submit
                $('form').submit(function(e) {
                    e.preventDefault(); // Prevent default form submission

                    if ($('#tableItem tbody tr').length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Peringatan',
                            text: 'Silakan tambahkan minimal satu barang!',
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        return;
                    }

                    // Serialize form data
                    var formData = $(this).serialize();

                    // Send AJAX request
                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: formData,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Barang masuk berhasil ditambahkan',
                                timer: 2000,
                                showConfirmButton: false,
                            }).then((result) => {
                                    // Redirect to index page or reload current page
                                    window.location.href = "{{ route('barang-masuk.index') }}";
                                
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: 'Terjadi kesalahan saat menyimpan data',
                                timer: 2000,
                                showConfirmButton: false,
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection