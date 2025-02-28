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