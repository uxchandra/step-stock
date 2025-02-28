@extends('layouts.app')

@section('content')

<div class="section-header d-flex justify-content-between align-items-center mb-4">
    <h1 class="fw-bold text-dark" style="font-family: 'Orbitron', sans-serif; font-weight: 700;">Data Permintaan Department</h1>
</div>

<div class="container">
    <div class="row">
        @foreach($departments as $department)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4"> <!-- Tetap ukuran kolom untuk card lebih kecil -->
            <a href="{{ route('laporan-permintaan-department.show', $department->id) }}" class="text-decoration-none">
                <div class="card h-100 shadow-sm hover-card border-0 bg-gradient-custom">
                    <div class="card-body d-flex flex-column align-items-start p-3 text-white"> <!-- Tambah sedikit padding -->
                        <h5 class="card-title fw-semibold mb-2" style="font-family: 'Orbitron', sans-serif; font-weight: 700; font-size: 1rem;">{{ $department->nama_departemen }}</h5> 
                        <div class="w-100 text-center">
                            <span class="badge badge-custom mt-2" style="font-family: 'Orbitron', sans-serif; font-weight: 700; font-size: 0.875rem;">
                                {{ $department->orders_count }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        </div>               
        @endforeach
    </div>
</div>

<style>
    /* Warna Background Kartu */
    .bg-gradient-custom {
        background: linear-gradient(135deg, #0d1b2a, #1b263b);
        backdrop-filter: blur(5px); /* Efek glassmorphism ringan */
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1); /* Border tipis untuk glassmorphism */
    }

    .hover-card {
        transition: all 0.3s ease-in-out;
    }

    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0, 221, 235, 0.3); /* Glow biru techy */
    }

    /* Badge */
    .badge-custom {
        background: rgba(255, 255, 255, 0.1); /* Warna putih transparan */
        color: #ffffff;
        font-size: 0.875rem;
        padding: 6px 12px;
        border-radius: 20px;
        display: inline-block;
        text-shadow: 0 0 5px rgba(0, 221, 235, 0.3); /* Glow biru halus */
        transition: all 0.3s ease;
    }

    .badge-custom:hover {
        box-shadow: 0 0 10px rgba(0, 221, 235, 0.5); /* Glow lebih kuat saat hover */
    }
</style>

@endsection