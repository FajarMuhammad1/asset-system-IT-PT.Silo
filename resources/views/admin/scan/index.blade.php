@extends('layouts.app')

@section('content')
{{-- Load Quagga JS dari CDN --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>

<style>
    /* 1. CONTAINER KAMERA */
    #camera-wrapper {
        position: relative;
        width: 100%;
        height: 350px; 
        background: #000;
        border-radius: 10px;
        overflow: hidden;
    }

    /* Styling Video dari Quagga biar Full Pas */
    #interactive > video {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Biar video gak gepeng */
        position: absolute;
        top: 0; left: 0;
    }
    
    /* Hide Canvas bawaan Quagga (Garis ijo acak2an) kita pake overlay sendiri aja */
    #interactive > canvas {
        display: none;
    }

    /* 2. OVERLAY PEMBIDIK (VIEWFINDER) */
    .scan-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 10;
        display: none; 
        justify-content: center;
        align-items: center;
        pointer-events: none;
    }

    /* 3. KOTAK FOKUS BARCODE */
    .scan-box {
        width: 80%;
        max-width: 350px;
        height: 120px; /* Barcode biasanya pendek melebar */
        position: relative;
        box-shadow: 0 0 0 1000px rgba(0, 0, 0, 0.6); 
        border: 2px solid rgba(0, 255, 0, 0.4); 
        border-radius: 8px;
    }

    /* 4. POJOKAN HIJAU */
    .scan-box::before, .scan-box::after, 
    .scan-box > .corners::before, .scan-box > .corners::after {
        content: '';
        position: absolute;
        width: 25px;
        height: 25px;
        border-color: #00ff00;
        border-style: solid;
        border-width: 0;
    }
    .scan-box::before { top: -2px; left: -2px; border-top-width: 4px; border-left-width: 4px; }
    .scan-box::after { top: -2px; right: -2px; border-top-width: 4px; border-right-width: 4px; }
    .scan-box > .corners::before { bottom: -2px; left: -2px; border-bottom-width: 4px; border-left-width: 4px; }
    .scan-box > .corners::after { bottom: -2px; right: -2px; border-bottom-width: 4px; border-right-width: 4px; }

    /* 5. LASER MERAH SCANNER */
    .scan-laser {
        position: absolute;
        width: 100%;
        height: 2px;
        background: red;
        box-shadow: 0 0 8px red;
        top: 50%;
        animation: laser-bounce 2s infinite ease-in-out;
    }

    @keyframes laser-bounce {
        0% { top: 10%; opacity: 0.5; }
        50% { opacity: 1; }
        100% { top: 90%; opacity: 0.5; }
    }
</style>

<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-barcode mr-2"></i> Scan Barcode Aset</h1>
    </div>

    <div class="row justify-content-center">
        
        {{-- INPUT MANUAL / USB --}}
        <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
            <div class="card shadow mb-4 border-left-primary h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Input Manual / USB Scanner</h6>
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <form action="{{ route('scan.process') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <input type="text" 
                                   name="kode_asset" 
                                   id="inputKode" 
                                   class="form-control form-control-lg text-center font-weight-bold text-uppercase" 
                                   placeholder="KLIK & SCAN..." 
                                   style="font-size: 24px; letter-spacing: 2px;"
                                   autofocus 
                                   required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-search mr-2"></i> CEK DATA
                        </button>
                    </form>
                    @if(session('error'))
                        <div class="alert alert-danger mt-3">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- KAMERA BARCODE (QUAGGA JS) --}}
        <div class="col-xl-6 col-lg-6 col-md-12 mb-4">
            <div class="card shadow mb-4 border-left-success h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-success">Kamera Barcode (QuaggaJS)</h6>
                    <button class="btn btn-sm btn-success shadow-sm" type="button" onclick="startQuagga()">
                        <i class="fas fa-camera"></i> Buka Kamera
                    </button>
                </div>
                <div class="card-body text-center p-0">
                    
                    <div id="camera-wrapper">
                        {{-- Area Kamera Quagga --}}
                        <div id="interactive" class="viewport" style="width:100%; height:100%;"></div>
                        
                        {{-- Overlay UI --}}
                        <div class="scan-overlay" id="scanOverlay">
                            <div class="scan-box">
                                <div class="corners"></div>
                                <div class="scan-laser"></div>
                            </div>
                        </div>

                        {{-- Placeholder --}}
                        <div id="camera-placeholder" class="d-flex align-items-center justify-content-center h-100 text-muted" style="position: absolute; top:0; width:100%; pointer-events: none;">
                            <div style="z-index: 5;"><i class="fas fa-camera fa-3x mb-3"></i><br>Klik tombol hijau di atas.</div>
                        </div>
                    </div>
                    
                    <div class="p-3">
                        <small class="text-muted" id="camera-status">Code 128, EAN, UPC, Code 39</small>
                    </div>

                    <form id="formKamera" action="{{ route('scan.process') }}" method="POST" style="display: none;">
                        @csrf
                        <input type="hidden" name="kode_asset" id="hasilScanKamera">
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // 1. INPUT FOCUS LOGIC
    const inputField = document.getElementById('inputKode');
    document.addEventListener('click', function(e) {
        if(e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A' && e.target.tagName !== 'INPUT') {
            if(inputField) inputField.focus();
        }
    });

    // 2. QUAGGA JS CONFIG
    let isCameraRunning = false;

    function startQuagga() {
        if (isCameraRunning) return; // Cegah double klik

        document.getElementById('camera-status').innerText = "Menyiapkan Kamera...";
        document.getElementById('camera-placeholder').style.opacity = '0'; // Hide placeholder

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.querySelector('#interactive'),
                constraints: {
                    facingMode: "environment", // Kamera Belakang
                    aspectRatio: { min: 1, max: 2 }
                },
            },
            locator: {
                patchSize: "medium",
                halfSample: true
            },
            numOfWorkers: 2, // Gunakan 2 core CPU biar enteng
            decoder: {
                // DAFTAR TIPE BARCODE YG DIBACA (Penting!)
                readers: [
                    "code_128_reader", // Paling umum buat resi/gudang
                    "ean_reader",      // Produk supermarket
                    "ean_8_reader", 
                    "code_39_reader", 
                    "upc_reader"
                ]
            },
            locate: true
        }, function(err) {
            if (err) {
                console.log(err);
                document.getElementById('camera-status').innerHTML = "<span class='text-danger'>Gagal Akses Kamera! (HTTPS/Izin)</span>";
                document.getElementById('camera-placeholder').style.opacity = '1';
                return;
            }
            console.log("Initialization finished. Ready to start");
            Quagga.start();
            isCameraRunning = true;
            
            document.getElementById('camera-status').innerText = "Arahkan barcode ke kotak...";
            document.getElementById('scanOverlay').style.display = "flex";
        });

        // 3. SAAT BARCODE TERDETEKSI
        Quagga.onDetected(function(result) {
            let code = result.codeResult.code;
            console.log("Barcode detected: " + code);

            if (code) {
                // Matikan kamera biar gak scan terus2an
                Quagga.stop(); 
                isCameraRunning = false;
                document.getElementById('scanOverlay').style.display = "none";

                // Isi Value & Submit
                document.getElementById('hasilScanKamera').value = code;
                document.getElementById('formKamera').submit();
            }
        });
    }
</script>
@endpush