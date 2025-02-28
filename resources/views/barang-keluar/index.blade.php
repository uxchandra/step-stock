@extends('layouts.app')
@include('barang-keluar.show')

@section('content')
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 font-weight-bold">Data Barang Keluar</h1>
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-plus"></i> Tambah Barang Keluar
        </a>
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
            @if($barangKeluar && $barangKeluar->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="bg-primary">
                                <th class="px-3 py-2 text-white" style="width: 5%">No</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Tanggal</th>
                                <th class="px-3 py-2 text-white" style="width: 10%">Waktu</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Department</th>
                                <th class="px-3 py-2 text-white" style="width: 10%">Jumlah Item</th>
                                <th class="px-3 py-2 text-white" style="width: 10%">Total Quantity</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Input By</th>
                                <th class="px-3 py-2 text-white" style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            @foreach($barangKeluar as $index => $transaksi)
                                <tr class="border-bottom">
                                    <td class="px-3 py-2 align-middle font-weight-medium">{{ ($barangKeluar->currentPage() - 1) * $barangKeluar->perPage() + $index + 1 }}</td>
                                    <td class="px-3 py-2 align-middle">
                                        <span class="text-dark" style="font-size: 0.875rem">
                                            {{ \Carbon\Carbon::parse($transaksi->tanggal_keluar)->translatedFormat('d F Y') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <span class="text-dark" style="font-size: 0.875rem">
                                            {{ $transaksi->created_at ? $transaksi->created_at->format('H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <span class="text-dark" style="font-size: 0.875rem">
                                            @if($transaksi->order && $transaksi->order->department)
                                                {{ $transaksi->order->department->nama_departemen }}
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <span class="badge bg-secondary text-dark" style="font-size: 0.75rem">
                                            {{ $transaksi->items_count }} items
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <span class="badge bg-info text-white" style="font-size: 0.75rem">
                                            {{ $transaksi->total_quantity }} pcs
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <small class="text-dark" style="font-size: 0.8125rem">
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $transaksi->user->name }}
                                        </small>
                                    </td>
                                    <td class="px-3 py-2 align-middle">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-info btn-sm detail-btn" data-id="{{ $transaksi->id }}">
                                                <i class="fas fa-eye text-white"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        Showing {{ $barangKeluar->firstItem() }} to {{ $barangKeluar->lastItem() }} of {{ $barangKeluar->total() }} entries
                    </div>
                    <div class="pagination-container">
                        {{ $barangKeluar->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-primary mb-3"></i>
                    <p class="text-dark mb-0" style="font-size: 0.875rem">Belum ada data barang keluar</p>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const entriesPerPage = document.getElementById('entriesPerPage');
            let searchTimer;
            
            function fetchData(page = 1) {
                const searchTerm = searchInput.value;
                const perPage = entriesPerPage.value;
                
                // Show loading indicator
                document.getElementById('tableBody').innerHTML = '<tr><td colspan="8" class="text-center py-4"><i class="fas fa-spinner fa-spin mr-2"></i> Loading...</td></tr>';
                
                fetch(`/barang-keluar?page=${page}&search=${searchTerm}&perPage=${perPage}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('tableBody').innerHTML = data.html;
                    document.querySelector('.pagination-container').innerHTML = data.pagination;
                    
                    // Reattach event listeners
                    attachDetailButtonListeners();
                    attachPaginationListeners();
                    
                    // Update URL without refresh
                    const url = new URL(window.location);
                    url.searchParams.set('page', page);
                    url.searchParams.set('search', searchTerm);
                    url.searchParams.set('perPage', perPage);
                    window.history.pushState({}, '', url);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('tableBody').innerHTML = '<tr><td colspan="8" class="text-center py-4 text-danger"><i class="fas fa-exclamation-circle mr-2"></i> Error loading data.</td></tr>';
                });
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
                        
                        let page = 1;
                        try {
                            const href = this.getAttribute('href');
                            if (href && href.includes('page=')) {
                                page = href.split('page=')[1].split('&')[0];
                            }
                        } catch (error) {
                            console.error("Error parsing pagination URL:", error);
                        }
                        
                        fetchData(page);
                    });
                });
            }
            
            // Attach detail button listeners
            function attachDetailButtonListeners() {
                document.querySelectorAll('.detail-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = this.dataset.id;
                        
                        // Show loading state in modal
                        document.getElementById('modal-items').innerHTML = '<tr><td colspan="4" class="text-center">Loading...</td></tr>';
                        
                        // Show the modal using Bootstrap's JavaScript API
                        $('#detailModal').modal('show');
                        
                        // Fetch detail data
                        fetch(`/barang-keluar/${id}/detail`)
                            .then(response => response.json())
                            .then(data => {
                                // Update modal content
                                document.getElementById('modal-tanggal').textContent = data.tanggal_keluar;
                                document.getElementById('modal-user').textContent = data.user_name;
                                document.getElementById('modal-catatan').textContent = data.catatan || 'Tidak ada catatan.';

                                // Generate items table
                                let itemsHtml = '';
                                data.items.forEach((item, index) => {
                                    itemsHtml += `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${item.kode_barang}</td>
                                            <td>${item.nama_barang}</td>
                                            <td>${item.quantity}</td>
                                        </tr>
                                    `;
                                });
                                document.getElementById('modal-items').innerHTML = itemsHtml;
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                document.getElementById('modal-items').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Gagal memuat data</td></tr>';
                            });
                    });
                });
            }

            // Initial attachment of event listeners
            attachDetailButtonListeners();
            attachPaginationListeners();
            
            // Initialize with default values from URL if present
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('perPage')) {
                entriesPerPage.value = urlParams.get('perPage');
            }
            if (urlParams.has('search')) {
                searchInput.value = urlParams.get('search');
            }
        });
    </script>
@endpush