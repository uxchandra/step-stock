@extends('layouts.app')
@include('laporan-permintaan-department.detail')

@section('content')
    <div class="section-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 font-weight-bold">Data Permintaan: {{ $department->nama_departemen }}</h1>
        <a href="{{ route('laporan-permintaan-department.index') }}" class="btn btn-primary btn-sm px-3">
            <i class="fas fa-arrow-left"></i> Kembali
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
            
            @if($orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="bg-primary">
                                <th class="px-3 py-2 text-white" style="width: 5%">No</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Tanggal</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Requester</th>
                                <th class="px-3 py-2 text-white" style="width: 5%">Total Item</th>
                                <th class="px-3 py-2 text-white" style="width: 15%">Status</th>
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
                                    <td class="px-3 py-2 align-middle">
                                        <span class="text-dark" style="font-size: 0.875rem">
                                            {{ $order->requester->name ?? 'N/A' }}
                                        </span>
                                    </td>
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
                                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailModal{{ $order->id }}">
                                            <i class="fas fa-eye text-white"></i>
                                        </button>
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
                    <p class="text-dark mb-0" style="font-size: 0.875rem">Belum ada data permintaan untuk departemen ini</p>
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