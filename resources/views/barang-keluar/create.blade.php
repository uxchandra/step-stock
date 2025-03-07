@extends('layouts.app')

@section('content')
    <div class="container-fluid px-3 py-3">
        <div class="section-header d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Tambah Barang Keluar</h1>
            <a href="{{ route('barang-keluar.index') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        @if(isset($order))
            <form method="post" action="{{ route('orders.processScan', $order->id) }}">
        @else
            <form method="post" action="{{ route('barang-keluar.store') }}">
        @endif
            @csrf
            <!-- Scan Section -->
            <div class="card mb-3 shadow-sm">
                <div class="card-body p-3">
                    <div class="form-group mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" id="kode_barang" 
                                   placeholder="Scan/Input Kode" autocomplete="off">
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="submit_kode" type="button">
                                    <i class="fas fa-barcode"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Date and Notes -->
                    <div class="form-group mb-2">
                        <input type="date" class="form-control" id="tanggal_keluar" 
                               name="tanggal_keluar" value="{{ date('Y-m-d') }}" readonly>
                    </div>
                    @if(!isset($order))
                        <div class="form-group mb-0">
                            <textarea name="catatan" id="catatan" class="form-control" rows="2" 
                                      placeholder="Masukkan keterangan/alasan barang keluar tanpa order" required></textarea>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Order Details (if exists) -->
            @if(isset($order))
                <div class="card mb-3 shadow-sm">
                    <div class="card-body p-3">
                        <h5 class="h6 mb-2">Detail Order</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th class="text-muted">Kode</th>
                                        <th class="text-muted">Nama</th>
                                        <th class="text-muted">Stok</th>
                                        <th class="text-muted">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->orderItems as $item)
                                        <tr>
                                            <td>{!! $item->barcode_html !!}<div>{{ $item->barang->kode }}</div></td>
                                            <td>{{ $item->barang->nama_barang }}</td>
                                            <td>{{ $item->barang->stok }}</td>
                                            <td>{{ $item->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Scanned Items Table -->
            <div class="card shadow-sm">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-sm" id="tableItem">
                            <thead>
                                <tr>
                                    <th class="text-muted">Kode</th>
                                    <th class="text-muted">Nama</th>
                                    <th class="text-muted">Stok</th>
                                    <th class="text-muted">Qty</th>
                                    <th class="text-muted"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block mt-3">Simpan</button>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var rowCounter = 0;
                $('#kode_barang').focus();

                function processScan() {
                    var code = $('#kode_barang').val();
                    if (!code) {
                        Swal.fire({icon: 'warning', title: 'Peringatan', text: 'Masukkan kode barang!', timer: 1500, showConfirmButton: false});
                        return;
                    }

                    var existingRow = $('#tableItem').find('input.code[value="' + code + '"]').closest('tr');
                    if (existingRow.length > 0) {
                        var qtyInput = existingRow.find('input.qty');
                        qtyInput.val(parseFloat(qtyInput.val()) + 1);
                        resetInput();
                        return;
                    }

                    $.ajax({
                        type: 'GET',
                        url: "{{ url('/barang/kode') }}/" + code,
                        success: function(data) {
                            if (data.success) {
                                @if(isset($order))
                                    const allowedItems = @json($orderItems);
                                    if (allowedItems.includes(data.barang.id)) {
                                        addNewRow(data.barang);
                                    } else {
                                        showError('Barang ' + data.barang.nama_barang + ' tidak ada dalam order!');
                                    }
                                @else
                                    addNewRow(data.barang);
                                @endif
                            } else {
                                showError('Barang tidak ditemukan');
                            }
                        },
                        error: function() {
                            showError('Terjadi kesalahan saat mencari barang');
                        }
                    });
                }

                function addNewRow(barang) {
                    const newRow = `
                        <tr id="rowItem${rowCounter}">
                            <td><input type="text" class="form-control form-control-sm code" name="code[]" value="${barang.kode}" readonly disabled></td>
                            <td><input type="text" class="form-control form-control-sm" name="nama_barang[]" value="${barang.nama_barang}" readonly disabled></td>
                            <td><input type="text" class="form-control form-control-sm" name="stok[]" value="${barang.stok}" readonly disabled></td>
                            <td><input type="number" class="form-control form-control-sm qty" name="qty[]" value="1" min="1"></td>
                            <td>
                                <button class="btn btn-danger btn-sm delete_row">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                            <td class="d-none">
                                <input type="hidden" name="barang_id[]" value="${barang.id}">
                            </td>
                        </tr>
                    `;
                    rowCounter++;
                    $('#tableItem tbody').append(newRow);
                    resetInput();
                }

                function resetInput() {
                    $('#kode_barang').val('').focus();
                }

                function showError(message) {
                    Swal.fire({icon: 'error', title: 'Oops...', text: message, timer: 1500, showConfirmButton: false});
                    resetInput();
                }

                // Event Handlers
                $('#kode_barang').keypress(function(e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        processScan();
                    }
                });

                $('#submit_kode').click(function(e) {
                    e.preventDefault();
                    processScan();
                });

                $(document).on('click', '.delete_row', function(e) {
                    e.preventDefault();
                    $(this).closest('tr').remove();
                    resetInput();
                });

                $('form').submit(function(e) {
                    e.preventDefault();
                    if ($('#tableItem tbody tr').length === 0) {
                        Swal.fire({icon: 'warning', title: 'Peringatan', text: 'Tambahkan minimal satu barang!', timer: 1500, showConfirmButton: false});
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: $(this).attr('action'),
                        data: $(this).serialize(),
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Barang keluar berhasil disimpan',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('barang-keluar.index') }}";
                            });
                        },
                        error: function(xhr) {
                            var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                            showError(errorMessage);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection