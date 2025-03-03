@extends('layouts.app')
@include('orders.create')
@include('orders.show')
@include('orders.approve')

@section('content')
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 font-weight-bold">Data Permintaan Barang</h1>
        <button class="btn btn-primary btn-sm px-3" data-toggle="modal" data-target="#addOrderModal">
            <i class="fas fa-plus"></i> Buat Permintaan
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <label class="mr-2 mb-0">Show</label>
                    <select class="form-control form-control-sm mr-2" style="width: 70px" id="entriesPerPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    <label class="mb-0">entries</label>
                </div>
                <div class="d-flex align-items-center">
                    <label class="mr-2 mb-0">Search:</label>
                    <input type="text" class="form-control form-control-sm" id="searchInput" style="width: 200px">
                </div>
            </div>
            @if($orders && $orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="bg-primary">
                                <th class="px-3 py-2 text-white" style="width: 5%">No</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Tanggal</th>
                                {{-- <th class="px-3 py-2 text-white" style="width: 5%">Waktu</th> --}}
                                @if(in_array(auth()->user()->role->role, ['kepala gudang', 'admin gudang', 'superadmin']))
                                    <th class="px-3 py-2 text-white" style="width: 15%">Department</th>
                                @endif
                                <th class="px-3 py-2 text-white" style="width: 5%">Total Item</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Status</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Keterangan</th>
                                <th class="px-3 py-2 text-white" style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach($orders as $index => $order)
                                <tr class="border-bottom">
                                    <td class="px-3 py-2 align-middle font-weight-medium">{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</td>
                                    <td class="px-3 py-2 align-middle">
                                        <span class="text-dark" style="font-size: 0.875rem">
                                            {{ $order->created_at->translatedFormat('d F Y') }}
                                            <br>
                                            <small class="text-muted">{{ $order->created_at->format('H:i') }}</small>
                                        </span>
                                    </td>
                                    {{-- <td class="px-3 py-2 align-middle">
                                        <span class="text-dark" style="font-size: 0.875rem">
                                            {{ $order->created_at->format('H:i') }}
                                        </span>
                                    </td> --}}
                                    @if(in_array(auth()->user()->role->role, ['kepala gudang', 'admin gudang', 'superadmin']))
                                        <td class="px-3 py-2 align-middle">
                                            <span class="text-dark" style="font-size: 0.875rem">
                                                {{ $order->department->nama_departemen }}
                                            </span>
                                        </td>
                                    @endif
                                    <td class="px-3 py-2 align-middle">
                                        <span class="badge bg-secondary text-dark" style="font-size: 0.75rem">
                                            {{ $order->orderItems->count() }} items
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        @php
                                            $statusColors = [
                                                'Pending' => ['bg' => '#ffc107', 'text' => '#000'],
                                                'Approved by Kadiv' => ['bg' => '#0d6efd', 'text' => '#fff'],
                                                'Approved by Kagud' => ['bg' => '#198754', 'text' => '#fff'],
                                                'Ready' => ['bg' => '#0dcaf0', 'text' => '#000'],
                                                'Completed' => ['bg' => '#212529', 'text' => '#fff'],
                                            ];
                                            $status = $statusColors[$order->status] ?? ['bg' => '#6c757d', 'text' => '#fff'];
                                        @endphp
                                        <span class="px-2 py-1 rounded-pill" style="font-size: 0.75rem; background-color: {{ $status['bg'] }}; color: {{ $status['text'] }}">
                                            {{ $order->status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <small class="text-dark" style="font-size: 0.8125rem">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            {{ $order->keterangan }}
                                        </small>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-info btn-sm mr-3" data-toggle="modal" data-target="#detailModal{{ $index }}">
                                                <i class="fas fa-eye text-white"></i>
                                            </button>
                                            @switch($order->status)
                                                @case('Pending')
                                                    @if(auth()->user()->role->role === 'kepala divisi')
                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#approveModal{{ $order->id }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    @break
                                                @case('Approved by Kadiv')
                                                    @if(auth()->user()->role->role === 'kepala gudang')
                                                        <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#approveModal{{ $order->id }}">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                    @break
                                                @case('Approved by Kagud')
                                                    @if(auth()->user()->role->role === 'admin gudang')
                                                        <a href="{{ route('orders.scan', $order->id) }}" class="btn btn-primary btn-sm">
                                                            <i class="fas fa-barcode"></i>
                                                        </a>
                                                    @endif
                                                    @break
                                                @case('Ready')
                                                    @if(auth()->user()->role->role === 'admin gudang')
                                                        <form action="{{ route('orders.complete', $order->id) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    @break
                                            @endswitch
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} entries
                    </div>
                    <div class="pagination-container">
                        {{ $orders->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-primary mb-3"></i>
                    <p class="text-dark mb-0" style="font-size: 0.875rem">Belum ada data permintaan barang</p>
                </div>
            @endif
        </div>
    </div>

    <style>
         .pagination {
            margin-bottom: 0;
        }
        .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        .page-item.active .page-link {
            background-color: #339eab;
            border-color: #339eab;
        }
        .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            border-top: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .table td {
            border-top: none;
            border-bottom: 1px solid #dee2e6;
            color: #333;
        }
        .table tr:hover {
            background-color: #f8f9fa;
        }
        .table tr:last-child td {
            border-bottom: none;
        }
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .card {
            border: none;
            border-radius: 0.5rem;
        }
        .section-header h1 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }
        .badge {
            font-weight: 500;
        }
    </style>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const searchInput = document.getElementById('searchInput');
        const entriesPerPage = document.getElementById('entriesPerPage');
        let searchTimer;
        

        function fetchData(page = 1) {
            const searchTerm = searchInput.value;
            const entries = entriesPerPage.value;
            
            fetch(`/orders?page=${page}&search=${searchTerm}&entries=${entries}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('tableBody').innerHTML = data.html;
                document.querySelector('.pagination-container').innerHTML = data.pagination;
                
                // Reattach event listeners to new pagination links
                attachPaginationListeners();
            })
            .catch(error => console.error('Error:', error));
        }

        // Search with debounce
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                fetchData();
            }, 500);
        });

        // Change entries per page
        entriesPerPage.addEventListener('change', function() {
            fetchData();
        });

        // Attach pagination listeners
        function attachPaginationListeners() {
            document.querySelectorAll('.pagination .page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    console.log("Pagination link clicked:", this.href); // Debug
                    
                    let page = 1;
                    try {
                        const href = this.getAttribute('href');
                        if (href && href.includes('page=')) {
                            page = href.split('page=')[1].split('&')[0];
                        }
                    } catch (error) {
                        console.error("Error parsing pagination URL:", error);
                    }
                    
                    console.log("Navigating to page:", page);
                    fetchData(page);
                });
            });
        }

        // Initial attachment of pagination listeners
        attachPaginationListeners();

        // Tambahkan event listener untuk menambahkan baris baru
        document.getElementById('tambah-barang').addEventListener('click', function () {
            const barangList = document.getElementById('barang-list');
            const newRow = document.querySelector('.barang-row').cloneNode(true);

            // Reset nilai input di baris baru
            newRow.querySelector('.barang-select').selectedIndex = 0;
            newRow.querySelector('.quantity-input').value = '';
            newRow.querySelector('.remove-barang').disabled = false;

            // Inisialisasi Select2 untuk baris baru
            $(newRow).find('.barang-select').select2({
                width: '100%'
            });

            // Tambahkan baris baru ke daftar
            barangList.appendChild(newRow);
        });

        // Tombol untuk menghapus baris barang
        $(document).on('click', '.remove-barang', function () {
            if ($('.barang-row').length > 1) {
                $(this).closest('.barang-row').remove();
            }
        });

        // Validasi stok saat memilih barang
        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('barang-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const stok = selectedOption.getAttribute('data-stok');
                const quantityInput = e.target.closest('.barang-row').querySelector('.quantity-input');

                if (stok) {
                    quantityInput.setAttribute('max', stok);
                    if (quantityInput.value > stok) {
                        alert('Stok tidak mencukupi!');
                        quantityInput.value = '';
                    }
                }
            }
        });

        // Menangkap submit form
        document.getElementById('createOrderForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            // Dapatkan referensi ke modal
            const modal = $('#addOrderModal');
            
            // Mengirim form via AJAX
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            })
            .then(response => response.json())
            .then(data => {
                modal.modal('hide');
                
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: data.message,
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message,
                        icon: 'error',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true
                    });
                }
            })
            .catch(error => {
                // Sembunyikan modal
                modal.modal('hide');
                
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menyimpan data',
                    icon: 'error',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
            });
        });

        @if(session('success'))
            Swal.fire({
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                icon: 'success',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'Error!',
                text: "{{ session('error') }}",
                icon: 'error',
                showConfirmButton: false,
                timer: 2000,
            });
        @endif
        
        // Tangkap semua form dengan ID yang dimulai dengan "approveForm"
        document.querySelectorAll('form[id^="approveForm"]').forEach(function(form) {
            form.addEventListener('submit', function() {
                // Tutup modal saat form di-submit
                const modalId = this.closest('.modal').id;
                $('#' + modalId).modal('hide');
            });
        });
    });
</script>

