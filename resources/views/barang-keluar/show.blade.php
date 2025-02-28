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
                <div class="card bg-warning text-dark mb-3" style="max-height: 100px;">
                    <div class="card-body" style="padding: 10px;">
                        <h6 class="card-title" style="margin-bottom: 5px; color: white;"><strong>Catatan:</strong></h6>
                        <p id="modal-catatan" class="mb-0" style="color: white;">Tidak ada catatan.</p>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>