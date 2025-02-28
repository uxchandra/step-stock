@extends('layouts.app')

@section('content')
<div class="section-header">
    <h1>Dashboard</h1>
</div>

<div class="row">
    <div class="col-12">
        @if (auth()->user()->role->role === 'kepala gudang')
            @if ($orders->count() == 0)
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tidak ada permintaan barang yang perlu Anda approve saat ini.
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    Terdapat {{ $orders->count() }} permintaan barang menunggu persetujuan Anda
                    <a href="{{ route('orders.index') }}" class="ml-1" style="color: #0000e6; text-decoration: underline;">Lihat Detail</a>
                </div>
            @endif

        @elseif (auth()->user()->role->role === 'kepala divisi')
            @if ($ordersKadiv->count() == 0)
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tidak ada permintaan barang yang perlu Anda approve saat ini.
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    Terdapat {{ $ordersKadiv->count() }} permintaan barang menunggu persetujuan Anda
                    <a href="{{ route('orders.index') }}" class="ml-1" style="color: #0000e6; text-decoration: underline;">Lihat Detail</a>
                </div>
            @endif
       
        @elseif (auth()->user()->role->role === 'admin gudang')
            @if ($ordersAdmin->count() == 0)
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    Tidak ada permintaan barang yang perlu diproses
                </div>
            @else
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle mr-2"></i>
                    Terdapat {{ $ordersAdmin->count() }} permintaan barang siap untuk diproses
                    <a href="{{ route('orders.index') }}" class="ml-1" style="color: #0000e6; text-decoration: underline;">Lihat Detail</a>
                </div>
            @endif
        @endif
    </div>
</div>

@if (in_array(auth()->user()->role->role, ['kepala gudang', 'admin gudang', 'superadmin']))
    <div class="row">
        <!-- Card Semua Barang -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('barang.index') }}" class="text-decoration-none">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Barang</h4>
                        </div>
                        <div class="card-body">
                            {{ $barang }}
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Barang Masuk -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('barang-masuk.index') }}" class="text-decoration-none">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-success">
                        <i class="fas fa-file-import"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Barang Masuk</h4>
                        </div>
                        <div class="card-body">
                            {{ $barangMasuk }}
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Barang Keluar -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('barang-keluar.index') }}" class="text-decoration-none">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-warning">
                        <i class="fas fa-file-export"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Barang Keluar</h4>
                        </div>
                        <div class="card-body">
                            {{ $barangKeluar }}
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card Users -->
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <a href="{{ route('data-pengguna.index') }}" class="text-decoration-none">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-info">
                        <i class="far fa-user"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>Total Pengguna</h4>
                        </div>
                        <div class="card-body">
                            {{ $userCount }}
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Grafik Barang -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Grafik Barang Masuk & Keluar</h4>
                </div>
                <div class="card-body">
                    <canvas id="summaryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabel Stok Minimum -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4>Stok Mencapai Batas Minimum</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th>Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangMinimum as $item)
                                    <tr>
                                        <td>{{ $item->kode }}</td>
                                        <td>{{ $item->nama_barang }}</td>
                                        <td>
                                            @if($item->status_stok === 'danger')
                                                <span class="badge badge-danger">{{ $item->stok }}</span>
                                            @else
                                                <span class="badge badge-warning">{{ $item->stok }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Existing rows and content -->
        
        <!-- New Department Requests Chart -->
        <div class="col-lg-12 mt-4">
            <div class="card">
                <div class="card-header">
                    <h4>Jumlah Permintaan Per Departemen ({{ $currentMonth }})</h4>
                </div>
                <div class="card-body">
                    <canvas id="departmentRequestsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
@endif

    @if (auth()->user()->role->role === 'admin divisi' || auth()->user()->role->role === 'kepala divisi')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $jumlahPermintaan }}</h3>
                                    <p class="mb-0">Total Permintaan</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $permintaanPending }}</h3>
                                    <p class="mb-0">Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $permintaanProses }}</h3>
                                    <p class="mb-0">Dalam Proses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $permintaanSelesai }}</h3>
                                    <p class="mb-0">Completed</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <x-card title="Chart barang paling populer">
                <div id="chart-total-sales"></div>
            </x-card>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

@if (in_array(auth()->user()->role->role, ['kepala gudang', 'admin gudang', 'superadmin']))
<script>
    // Ambil elemen canvas
    var ctx = document.getElementById('summaryChart').getContext('2d');

    // Buat chart dan simpan dalam variabel
    var summaryChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($barangMasukData->pluck('date')->map(function($date) {
                return \Carbon\Carbon::createFromFormat('Y-m', $date)->format('F Y');
            })),
            datasets: [{
                label: 'Barang Masuk',
                data: @json($barangMasukData->pluck('total')),
                backgroundColor: 'rgba(40, 167, 69, 0.8)'
            },
            {
                label: 'Barang Keluar',
                data: @json($barangKeluarData->pluck('total')),
                backgroundColor: 'rgba(255, 193, 7, 0.8)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0,
                    stepSize: 1,
                    ticks: {
                        callback: function(value) {
                            if (value % 1 === 0) {
                                return value;
                            }
                        }
                    }
                }
            },
        }
    });

    // Tambahkan event listener untuk klik pada chart
    document.getElementById('summaryChart').onclick = function(evt) {
        var activePoints = summaryChart.getElementsAtEventForMode(evt, 'nearest', { intersect: true }, true);
        
        if (activePoints.length > 0) {
            var clickedDatasetIndex = activePoints[0].datasetIndex; // Index dataset yang diklik

            if (clickedDatasetIndex === 0) {
                // Barang Masuk diklik
                window.location.href = '/barang-masuk'; 
            } else if (clickedDatasetIndex === 1) {
                // Barang Keluar diklik
                window.location.href = '/barang-keluar'; 
            }
        }
    };

</script>

<script>
    // Department requests chart
    var deptCtx = document.getElementById('departmentRequestsChart').getContext('2d');
    new Chart(deptCtx, {
        type: 'bar',
        data: {
            labels: @json($departmentRequestLabels),
            datasets: [{
                label: 'Jumlah Permintaan',
                data: @json($departmentRequestData),
                backgroundColor: 'rgba(0, 50, 150, 0.8)', 
                borderColor: 'rgba(0, 50, 150, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Departemen dengan Permintaan Barang Terbanyak'
                },
                legend: {
                    position: 'top'
                }
            }
        }
    });
</script>
@endif

@if (auth()->user()->role->role === 'admin divisi' || auth()->user()->role->role === 'kepala divisi' )
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const chartContainer = document.getElementById('chart-total-sales');
        const chartData = {
            total: @json($total ?? []),
            label: @json($label ?? [])
        };

        if (!chartContainer) {
            console.error('Chart container not found!');
            return;
        }

        if (!chartData.total.length || !chartData.label.length) {
            chartContainer.innerHTML = '<div class="text-center p-3">Tidak ada data untuk ditampilkan</div>';
            return;
        }

        const options = {
            series: chartData.total,
            chart: {
                width: '100%',
                type: 'donut',
            },
            labels: chartData.label,
            plotOptions: {
                pie: {
                    donut: {
                        size: '50%'
                    }
                }
            },
            dataLabels: {
                enabled: true
            },
            legend: {
                position: 'top'
            },
        };

        try {
            const chart = new ApexCharts(chartContainer, options);
            chart.render().then(() => {
                console.log('Chart rendered successfully');
            }).catch(err => {
                console.error('Error rendering chart:', err);
            });
        } catch (error) {
            console.error('Error creating chart:', error);
        }
    });
</script>
@endif
@endpush