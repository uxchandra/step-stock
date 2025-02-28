@extends('layouts.app')

@section('content')

<div class="section-header">
    <h1>Laporan Barang Keluar</h1>
    <div class="ml-auto">
        <a href="javascript:void(0)" class="btn btn-primary" id="print-barang-keluar"><i class="fa fa-sharp fa-light fa-print"></i> Print PDF</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <form id="filter_form" action="/laporan-barang-keluar/get-data" method="GET">
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
                                <th>Tanggal Keluar</th>
                                <th>Department</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Keluar</th>
                            </tr>
                        </thead>
                        <tbody id="tabel-laporan-barang-keluar">
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
        var table = $('#table_id').DataTable({ paging: true});

        loadData(); // Panggil fungsi loadData saat halaman dimuat

        $('#filter_form').submit(function(event) {
            event.preventDefault();
            loadData(); // Panggil fungsi loadData saat tombol filter ditekan
        });

        $('#refresh_btn').on('click', function() {
            refreshTable();
        });

        //Fungsi load data berdasarkan range tanggal_mulai dan tanggal_selesai
        function loadData() {
            var tanggalMulai = $('#tanggal_mulai').val();
            var tanggalSelesai = $('#tanggal_selesai').val();
            
            $.ajax({
                url: '/laporan-barang-keluar/get-data',
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
                            var tanggalKeluar = new Date(item.tanggal_keluar);
                            var formattedDate = ('0' + tanggalKeluar.getDate()).slice(-2) + '-' +
                                ('0' + (tanggalKeluar.getMonth() + 1)).slice(-2) + '-' +
                                tanggalKeluar.getFullYear();

                            var row = [
                                (index + 1),
                                formattedDate,
                                item.department,
                                item.nama_barang,
                                item.jumlah_keluar
                            ];
                            table.row.add(row).draw(false);
                        });
                    } else {
                        var emptyRow = ['', 'Tidak ada data yang tersedia.', '', '', ''];
                        table.row.add(emptyRow).draw(false);
                    }
                },
                error: function(xhr, status, error) {
                    console.log(error);
                }
            });
        }

        //Fungsi Refresh Tabel
        function refreshTable(){
            $('#filter_form')[0].reset();
            loadData();
        }

        //Print barang keluar
        $('#print-barang-keluar').on('click', function(e){
            e.preventDefault();
            var tanggalMulai = $('#tanggal_mulai').val();
            var tanggalSelesai = $('#tanggal_selesai').val();
            
            var url = '/laporan-barang-keluar/print-barang-keluar';

            if(tanggalMulai && tanggalSelesai){
                url += '?tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
            }

            window.open(url, '_blank');
        });
    });

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



@endsection