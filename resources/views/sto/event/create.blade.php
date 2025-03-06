<div class="modal fade" tabindex="-1" role="dialog" id="modal_tambah_event">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tambah Event STO</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form enctype="multipart/form-data">
        <div class="modal-body">

          <div class="form-group">
              <label>Nama Event</label>
              <input type="text" class="form-control" name="nama_event" id="nama_event">
              <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-nama_event"></div>
          </div>

          <div class="form-group">
              <label>Tanggal Mulai</label>
              <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai">
              <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-tanggal_mulai"></div>
          </div>

          <div class="form-group">
              <label>Tanggal Selesai</label>
              <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai">
              <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-tanggal_selesai"></div>
          </div>

          <div class="form-group">
              <label>Status</label>
              <select class="form-control" name="status" id="status">
                  <option value="active">Active</option>
                  <option value="closed">Closed</option>
              </select>
              <div class="alert alert-danger mt-2 d-none" role="alert" id="alert-status"></div>
          </div>

          <div class="form-group">
              <label>Keterangan (Opsional)</label>
              <textarea class="form-control" name="keterangan" id="keterangan" rows="3"></textarea>
          </div>

      </div>
      <div class="modal-footer bg-whitesmoke br">
        <button type="button" class="btn btn-dark" data-dismiss="modal">Keluar</button>
        <button type="button" class="btn btn-primary" id="store">Tambah</button>
      </div>
      </form>
    </div>
  </div>
</div>
</div>