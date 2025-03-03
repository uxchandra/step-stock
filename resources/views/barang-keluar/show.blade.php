<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Barang Keluar</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row mb-1">
                    <div class="col-md-6">
                        <p><strong>Tanggal Keluar:</strong> <span id="modal-tanggal"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Input By:</strong> <span id="modal-user"></span></p>
                    </div>
                </div>

                <!-- Card Catatan -->
                <div class="card text-dark mb-3" style="background-color: #fff8e1; border-left: 4px solid #ffc107; border-radius: 6px; max-height: 80px; overflow: hidden;">
                    <div class="card-body" style="padding: 8px 12px;">
                        <div class="d-flex align-items-center mb-1" style="gap: 6px;">
                            <i class="fas fa-info-circle" style="color: #6c757d; font-size: 0.9rem;"></i>
                            <h6 class="card-title mb-0" style="color: #495057; font-weight: bold; font-size: 0.9rem;">Catatan:</h6>
                        </div>
                        <p id="modal-catatan" class="mb-0" style="color: #6c757d; font-size: 0.85rem; margin-left: 18px;">Tidak ada catatan.</p>
                    </div>
                </div>                                                                   
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody id="modal-items">
                            <!-- Data akan diisi oleh AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>