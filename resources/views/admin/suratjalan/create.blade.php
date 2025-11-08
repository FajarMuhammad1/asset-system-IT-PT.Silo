@extends('layouts.app')
@section('content')

<h3 class="h3 mb-4 text-gray-800">
    <i class="fas fa-envelope-open mr-2"></i> {{ $title }}
</h3>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label>ID SJ</label>
                    <input type="text" name="id_sj" class="form-control" value="{{ old('id_sj') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>No SJ</label>
                    <input type="text" name="no_sj" class="form-control" value="{{ old('no_sj') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>No PPI</label>
                    <input type="text" name="no_ppi" class="form-control" value="{{ old('no_ppi') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>No PO</label>
                    <input type="text" name="no_po" class="form-control" value="{{ old('no_po') }}">
                </div>

                <div class="col-md-6 mb-3">
    <label for="kategori">Kategori</label>
    <select id="kategori" name="kategori[]" class="form-control select2" multiple="multiple" data-placeholder="Pilih kategori...">
        <option value="Access Point">Access Point</option>
        <option value="Analogue Telephone">Analogue Telephone</option>
        <option value="Antena Radio Rig">Antena Radio Rig</option>
        <option value="Camera">Camera</option>
        <option value="Consumable">Consumable</option>
        <option value="CCTV">CCTV</option>
        <option value="Dongle">Dongle</option>
        <option value="Extender">Extender</option>
        <option value="Fingerprint">Fingerprint</option>
        <option value="GPS">GPS</option>
        <option value="Headset">Headset</option>
        <option value="Hard Disk Eksternal">Hard Disk Eksternal</option>
        <option value="IP Telephone">IP Telephone</option>
        <option value="Keyboard">Keyboard</option>
        <option value="Laptop">Laptop</option>
        <option value="Modem">Modem</option>
        <option value="Monitor">Monitor</option>
        <option value="Mouse">Mouse</option>
        <option value="Mobile Phone">Mobile Phone</option>
        <option value="PC Desktop">PC Desktop</option>
        <option value="Power Supply">Power Supply</option>
        <option value="Proyektor">Proyektor</option>
        <option value="Print Server">Print Server</option>
        <option value="Printer">Printer</option>
        <option value="Radio HT">Radio HT</option>
        <option value="Radio Rig">Radio Rig</option>
        <option value="Router">Router</option>
        <option value="Scanner">Scanner</option>
        <option value="SSD Eksternal">SSD Eksternal</option>
        <option value="Stavolt">Stavolt</option>
        <option value="Switch Hub">Switch Hub</option>
        <option value="UPS">UPS</option>
        <option value="TV">TV</option>
        <option value="Webcam">Webcam</option>
    </select>
</div>

                <div class="col-md-6 mb-3">
                    <label>Merk</label>
                    <input type="text" name="merk" class="form-control" value="{{ old('merk') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Model</label>
                    <input type="text" name="model" class="form-control" value="{{ old('model') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Spesifikasi</label>
                    <input type="text" name="spesifikasi" class="form-control" value="{{ old('spesifikasi') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Serial Number</label>
                    <div class="input-group">
                        <input type="text" name="serial_number" id="serial_number" class="form-control">
                        <button type="button" class="btn btn-outline-secondary" id="scanSerial">
                            <i class="fas fa-barcode"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Kode Asset</label>
                    <div class="input-group">
                        <input type="text" name="kode_asset" id="kode_asset" class="form-control">
                        <button type="button" class="btn btn-outline-secondary" id="scanAsset">
                            <i class="fas fa-barcode"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Qty</label>
                    <input type="number" name="qty" class="form-control" value="{{ old('qty') }}">
                </div>

                <!-- Tambahkan di form -->
                <div class="col-md-6 mb-3">
                 <label for="jenis_surat_jalan">Jenis Surat Jalan</label>
                 <select id="jenis_surat_jalan" name="jenis_surat_jalan" class="form-control select2" data-placeholder="Pilih jenis surat jalan...">
                     <option value="">-- Pilih Jenis --</option>
                     <option value="Penambahan">Penambahan</option>
                     <option value="Perbaikan">Perbaikan</option>
                  </select>
                </div>


                <div class="col-md-6 mb-3">
                    <label>Tanggal Input</label>
                    <input type="date" name="tanggal_input" class="form-control" value="{{ old('tanggal_input') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>BAST</label>
                    <input type="text" name="bast" class="form-control" value="{{ old('bast') }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Upload File (jika ada)</label>
                    <input type="file" name="file" class="form-control">
                </div>
            </div>

            
            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mt-3">
                <button class="btn btn-primary mr-3">Simpan</button>
                <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary mr-3">Kembali</a>
            </div>
        </form>
    </div>
</div>

    {{-- Modal Barcode Scanner --}}
    <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-barcode"></i> Scan Barcode</h5>
                    
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    
                    <div id="camera" style="width:100%; height:400px; background:#000; border-radius:10px; overflow: hidden;">
                        </div>

                    <div class="text-success mt-2 fw-bold" id="statusText">🔍 Arahkan barcode ke kamera...</div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="switchCamera" class="btn btn-info">🔄 Ganti Kamera</button>
                    
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

   @push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
<script>
    // --- Variabel Global ---
    let targetInput = null;         // Input field mana yang akan diisi (serial_number atau kode_asset)
    let quaggaInitialized = false;  // Status Quagga
    let usingFrontCamera = false;     // Status kamera (belakang/depan)
    const barcodeModalEl = document.getElementById('barcodeModal');
    const barcodeModal = new bootstrap.Modal(barcodeModalEl);

    // --- Fungsi Utama Quagga ---

    /**
     * Memulai Quagga dan stream kamera.
     */
    function startQuagga() {
        if (quaggaInitialized) {
            Quagga.stop();
        }
        const statusText = document.getElementById('statusText');
        statusText.innerText = "🔍 Membuka kamera...";

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.getElementById('camera'), // Target div
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: usingFrontCamera ? "user" : "environment"
                }
            },
            decoder: {
                readers: [
                    "code_128_reader", "ean_reader", "ean_8_reader",
                    "code_39_reader", "upc_reader", "code_93_reader"
                ]
            },
            locate: true
        }, function(err) {
            if (err) {
                console.error("❌ Gagal memulai Quagga:", err);
                statusText.innerText = "❌ Gagal akses kamera: " + err.message;
                return;
            }
            Quagga.start();
            quaggaInitialized = true;
            statusText.innerText = "📸 Kamera aktif, arahkan barcode...";
        });

        // Hapus listener lama jika ada (untuk menghindari duplikasi)
        Quagga.offDetected(onBarcodeDetected); 
        
        // Tambahkan listener baru
        Quagga.onDetected(onBarcodeDetected);
    }

    /**
     * Dipanggil ketika barcode berhasil terdeteksi.
     */
    function onBarcodeDetected(data) {
        const code = data.codeResult.code;
        
        if (targetInput) {
            targetInput.value = code; // Isi input field
            document.getElementById('statusText').innerText = "✅ Barcode terdeteksi: " + code;
            
            setTimeout(() => {
                stopQuagga(); // Matikan kamera
                barcodeModal.hide(); // Tutup modal
            }, 800);
        }
    }

    /**
     * Menghentikan stream kamera dan membersihkan div.
     */
    function stopQuagga() {
        if (quaggaInitialized) {
            Quagga.stop();
            quaggaInitialized = false;
        }
        document.getElementById('statusText').innerText = "🔒 Kamera dimatikan.";
        document.getElementById('camera').innerHTML = ''; 
    }

    /**
     * Mengganti antara kamera depan dan belakang.
     */
    function switchCamera() {
        usingFrontCamera = !usingFrontCamera; // Ganti status
        startQuagga(); // Mulai ulang Quagga dengan kamera baru
    }

    // --- Event Listener untuk Tombol dan Modal ---

    // Saat modal DITAMPILKAN, panggil startQuagga()
    barcodeModalEl.addEventListener('shown.bs.modal', startQuagga);

    // ⭐ [PERBAIKAN]
    // Saat modal MULAI DITUTUP, panggil stopQuagga()
    // Ini akan mematikan kamera secara instan.
    barcodeModalEl.addEventListener('hide.bs.modal', stopQuagga);

    // Tombol scan untuk "Serial Number"
    document.getElementById("scanSerial")?.addEventListener("click", function() {
        targetInput = document.getElementById("serial_number");
        barcodeModal.show();
    });

    // Tombol scan untuk "Kode Asset"
    document.getElementById("scanAsset")?.addEventListener("click", function() {
        targetInput = document.getElementById("kode_asset");
        barcodeModal.show();
    });

    // Tombol "Ganti Kamera"
    document.getElementById("switchCamera").addEventListener("click", switchCamera);

    // Listener manual untuk "tutupModalButton" sudah tidak diperlukan
    // karena 'hide.bs.modal' sudah menangani semua skenario penutupan.

</script>
@endpush

@endsection





