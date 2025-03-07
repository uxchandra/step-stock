<!-- Modal Reset Stok -->
<div class="modal fade" id="modal_reset_stok" tabindex="-1" role="dialog" aria-labelledby="resetStokModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetStokModalLabel">Konfirmasi Reset Stok</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Anda akan mereset stok sistem sesuai stok aktual dari event <strong id="event_name_reset"></strong>.</p>
                <p>Semua item STO akan berubah status menjadi "close" dan event akan berubah status menjadi "closed".</p>
                <p class="text-danger font-weight-bold">Tindakan ini tidak dapat dibatalkan!</p>
            </div>
            <div class="modal-footer">
                <form id="form_reset_stok" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" id="btn_submit_reset">Reset Stok</button>
                </form>
            </div>
        </div>
    </div>
</div>