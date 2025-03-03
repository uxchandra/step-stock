@foreach($orders as $index => $order)
    <div class="modal fade" id="approveModal{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel{{ $order->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="approveModalLabel{{ $order->id }}">Konfirmasi Persetujuan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Apakah Anda yakin ingin menyetujui permintaan ini?
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>Requester :</strong> {{ $order->requester->name }}</p>
                            <p><strong>Department :</strong> {{ $order->department->nama_departemen }}</p>
                            <p><strong>Status :</strong> {{ $order->status }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tanggal Request :</strong> {{ $order->created_at->translatedFormat('d F Y H:i') }}</p>
                            <p><strong>Approved by Kadiv :</strong> 
                                @if($order->tanggal_approve_kadiv)
                                    {{ \Carbon\Carbon::parse($order->tanggal_approve_kadiv)->translatedFormat('d F Y H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                            <p><strong>Approved by Kagud :</strong> 
                                @if($order->tanggal_approve_kagud)
                                    {{ \Carbon\Carbon::parse($order->tanggal_approve_kagud)->translatedFormat('d F Y H:i') }}
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr class="bg-secondary text-white">
                                    <th>No</th>
                                    <th>Nama Barang</th>
                                    <th>Quantity</th>
                                    <th>Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $itemIndex => $item)
                                    <tr>
                                        <td>{{ $itemIndex + 1 }}</td>
                                        <td>{{ $item->barang->nama_barang }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ $item->barang->satuan }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-dark" data-dismiss="modal">Batal</button>
                    <form action="{{ route('orders.approve', $order->id) }}" method="POST" id="approveForm{{ $order->id }}">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-success">Approve</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach