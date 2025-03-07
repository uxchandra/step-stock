<div class="modal fade" id="scanModal" tabindex="-1" aria-labelledby="scanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scanModalLabel">Detail Barang</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Tabel Informasi Barang -->
                <table class="table" style="font-size: 14px; border: 1px solid black;">
                    <tbody>
                        <tr style="border: 1px solid black;">
                            <th scope="row" class="col-4" style="border: 1px solid black;">Kode Barang</th>
                            <td id="modal-kode" style="border: 1px solid black;"></td>
                        </tr>
                        <tr style="border: 1px solid black;">
                            <th scope="row" style="border: 1px solid black;">Nama Barang</th>
                            <td id="modal-nama" style="border: 1px solid black;"></td>
                        </tr>
                        <tr style="border: 1px solid black;">
                            <th scope="row" style="border: 1px solid black;">Spesifikasi</th>
                            <td id="modal-size" style="border: 1px solid black;"></td>
                        </tr>
                        <tr style="border: 1px solid black;">
                            <th scope="row" style="border: 1px solid black;">Stok Sistem</th>
                            <td id="modal-stok-sistem" style="border: 1px solid black;"></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Form Input Stok Aktual -->
                <form id="scanForm">
                    <div class="mb-3">
                        <label for="modal-stok-aktual" class="form-label">Stok Aktual</label>
                        <input type="number" class="form-control" id="modal-stok-aktual" name="stok_aktual" min="0" required>
                        <input type="hidden" id="modal-barang-id" name="barang_id">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="save-btn" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>