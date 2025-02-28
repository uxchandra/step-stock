@foreach($orders as $index => $order)
<tr class="border-bottom">
    <td class="px-3 py-2 align-middle font-weight-medium">{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</td>
    <td class="px-3 py-2 align-middle">
        <span class="text-dark" style="font-size: 0.875rem">
            {{ $order->created_at->translatedFormat('d F Y') }}
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