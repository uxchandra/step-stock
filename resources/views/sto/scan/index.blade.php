@extends('layouts.app')
@include('sto.scan.scan')

@section('content')
    <div class="section-header">
        <h1>Scan Label</h1>
        <div class="ml-auto">
        </div>
    </div>

    <div class="container-fluid mt-4 px-0">
        <!-- Nama Event -->
        <div class="row mb-3">
            <div class="col-12">
                <h5 class="text-muted">Event: {{ $activeEvent->nama_event ?? 'STO Aktif' }}</h5>
            </div>
        </div>

        <!-- Kolom Scan/Input Kode Barang -->
        <div class="row mb-4">
            <div class="col-12">
                <input 
                    type="text" 
                    id="kode-barang" 
                    class="form-control form-control-sm mb-3 border border-dark" 
                    autofocus
                >
                <button 
                    id="scan-btn" 
                    class="btn btn-primary w-100"
                >
                    Submit Scan
                </button>
            </div>
        </div>

        <!-- Total Scan -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card w-100 shadow border border-primary">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Scan</h5>
                        <p id="total-scan" class="display-4 text-primary">0</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let totalScanned = 0;
        const scanBtn = document.getElementById('scan-btn');
        const kodeBarangInput = document.getElementById('kode-barang');
        const totalScanElement = document.getElementById('total-scan');
        const scanModal = new bootstrap.Modal(document.getElementById('scanModal'));
        const scanForm = document.getElementById('scanForm');
        const saveBtn = document.getElementById('save-btn');

        scanBtn.addEventListener('click', () => {
            const kode = kodeBarangInput.value.trim();
            if (kode) {
                fetch('/scan-label/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ kode: kode })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Isi tabel di modal dengan data barang
                        document.getElementById('modal-kode').textContent = data.barang.kode;
                        document.getElementById('modal-nama').textContent = data.barang.nama;
                        document.getElementById('modal-size').textContent = data.barang.size || '-'; // Fallback kalau size null
                        document.getElementById('modal-stok-sistem').textContent = data.barang.stok_sistem;
                        document.getElementById('modal-barang-id').value = data.barang.id;
                        scanModal.show();
                    } else {
                        alert(data.error);
                    }
                })
                .catch(error => console.error('Error:', error));

                kodeBarangInput.value = '';
                kodeBarangInput.focus();
            }
        });

        // Submit dengan Enter di input scan
        kodeBarangInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                scanBtn.click();
            }
        });

        // Simpan data dari modal
        saveBtn.addEventListener('click', () => {
            const formData = new FormData(scanForm);

            fetch('/scan-label/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    totalScanned++;
                    totalScanElement.textContent = totalScanned;
                    scanModal.hide();
                    scanForm.reset();
                } else {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        // Submit dengan Enter di input stok aktual
        document.getElementById('modal-stok-aktual').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                saveBtn.click();
            }
        });
    </script>
@endsection