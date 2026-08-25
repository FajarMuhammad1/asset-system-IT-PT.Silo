@extends('layouts.app')

@section('content')
{{-- ZXing-js: Decoder terbaik untuk Code 128 di webcam laptop --}}
<script src="https://unpkg.com/@zxing/library@0.21.3/umd/index.min.js"></script>

<style>
    #camera-wrapper {
        position: relative;
        width: 100%;
        background: #0d0d0d;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 32px rgba(0,0,0,0.5);
    }
    #scanVideo {
        width: 100%;
        height: 340px;
        object-fit: cover;
        display: block;
        border-radius: 12px;
    }
    #processCanvas { display: none; }
    #trackingCanvas {
        position: absolute; top: 0; left: 0;
        width: 100%; height: 100%;
        pointer-events: none; z-index: 20;
    }
    .guide-zone {
        position: absolute; top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 86%; height: 120px;
        z-index: 10; pointer-events: none; display: none;
    }
    .guide-zone-inner {
        width: 100%; height: 100%;
        border: 2.5px solid rgba(0,255,100,0.85);
        border-radius: 10px;
        box-shadow: 0 0 0 3000px rgba(0,0,0,0.55);
        position: relative;
    }
    .guide-zone-inner::before { content:''; position:absolute; top:-3px; left:-3px; width:22px; height:22px; border-top:5px solid #00ff64; border-left:5px solid #00ff64; }
    .guide-zone-inner::after  { content:''; position:absolute; top:-3px; right:-3px; width:22px; height:22px; border-top:5px solid #00ff64; border-right:5px solid #00ff64; }
    .corner-bl { position:absolute; bottom:-3px; left:-3px; width:22px; height:22px; border-bottom:5px solid #00ff64; border-left:5px solid #00ff64; }
    .corner-br { position:absolute; bottom:-3px; right:-3px; width:22px; height:22px; border-bottom:5px solid #00ff64; border-right:5px solid #00ff64; }
    .scan-laser {
        position:absolute; left:0; width:100%; height:2px;
        background: linear-gradient(90deg, transparent 0%, #ff4444 30%, #ff8888 50%, #ff4444 70%, transparent 100%);
        box-shadow: 0 0 10px #ff4444;
        animation: laserMove 1.6s ease-in-out infinite;
        pointer-events: none;
    }
    @keyframes laserMove { 0%{top:5%} 50%{top:90%} 100%{top:5%} }

    #detectedBadge {
        display: none; position: absolute;
        top: 10px; left: 50%; transform: translateX(-50%);
        background: #00e65a; color: #000;
        font-weight: 800; font-size: 13px;
        padding: 7px 20px; border-radius: 30px; z-index: 30;
        box-shadow: 0 0 20px rgba(0,230,90,0.7);
        animation: badgePop .3s ease; white-space:nowrap;
        max-width:90%; overflow:hidden; text-overflow:ellipsis;
    }
    @keyframes badgePop { 0%{transform:translateX(-50%) scale(.7);opacity:0} 80%{transform:translateX(-50%) scale(1.05)} 100%{transform:translateX(-50%) scale(1);opacity:1} }

    #scanStatusBar {
        position:absolute; bottom:0; left:0; right:0; z-index:25;
        background: rgba(0,0,0,0.75);
        padding: 7px 14px; font-size:13px;
        font-family:'Courier New',monospace; color:#ccc;
        border-top: 1px solid rgba(255,255,255,0.07);
        display: none;
    }
    .slider-control label { font-size:11px; font-weight:700; color:#555; text-transform:uppercase; letter-spacing:.5px; }
    .slider-control input[type="range"] { width:100%; accent-color:#1cc88a; cursor:pointer; }
    .slider-val { font-size:12px; font-weight:700; color:#1cc88a; min-width:38px; text-align:right; }
    .blink { animation: blink 1.2s step-start infinite; }
    @keyframes blink { 50%{opacity:0} }
</style>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-barcode mr-2 text-success"></i> Scan Barcode Aset
        </h1>
        <a href="{{ route('barangmasuk.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-3">
        <i class="fas fa-exclamation-triangle mr-2"></i>{{ session('error') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
    @endif

    <div class="row">
        {{-- KOLOM KAMERA --}}
        <div class="col-lg-7 mb-4">
            <div class="card shadow border-0">
                <div class="card-header py-3 d-flex align-items-center justify-content-between"
                     style="background:linear-gradient(135deg,#1cc88a,#17a673);color:white;border-radius:.35rem .35rem 0 0;">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-camera mr-2"></i> Kamera Barcode Otomatis</h6>
                    <div>
                        <button class="btn btn-sm btn-light font-weight-bold mr-1" id="btnStart" onclick="startScanner()">
                            <i class="fas fa-play text-success mr-1"></i> Buka Kamera
                        </button>
                        <button class="btn btn-sm btn-danger" id="btnStop" onclick="stopScanner()" style="display:none;">
                            <i class="fas fa-stop mr-1"></i> Tutup
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="camera-wrapper">
                        <video id="scanVideo" autoplay playsinline muted></video>
                        <canvas id="processCanvas"></canvas>
                        <canvas id="trackingCanvas"></canvas>

                        <div class="guide-zone" id="guideZone">
                            <div class="guide-zone-inner">
                                <div class="corner-bl"></div>
                                <div class="corner-br"></div>
                                <div class="scan-laser"></div>
                            </div>
                        </div>

                        <div id="detectedBadge"><i class="fas fa-check-circle mr-1"></i><span id="detectedText"></span></div>

                        <div id="cameraPlaceholder" style="position:absolute;top:0;left:0;width:100%;height:340px;display:flex;align-items:center;justify-content:center;flex-direction:column;background:#111;z-index:5;text-align:center;padding:20px;">
                            <i class="fas fa-barcode fa-4x mb-3" style="color:#1cc88a;"></i>
                            <h5 class="font-weight-bold text-white mb-1">Scanner ZXing Siap</h5>
                            <p class="small text-muted px-3 mb-3">Klik <strong class="text-success">Buka Kamera</strong> untuk memulai.<br>Dioptimalkan khusus untuk kamera bawaan laptop.</p>
                            <label class="btn btn-outline-success btn-sm mb-0">
                                <i class="fas fa-image mr-1"></i> Scan dari Foto
                                <input type="file" id="fileInputPlaceholder" accept="image/*" style="display:none;" onchange="scanFromFile(this)">
                            </label>
                        </div>

                        <div id="scanStatusBar">
                            <i class="fas fa-circle fa-xs text-success mr-1 blink"></i>
                            <span id="statusText">Menginisialisasi...</span>
                        </div>
                    </div>

                    {{-- PANEL KONTROL ENHANCEMENT --}}
                    <div id="cameraControls" class="px-3 py-3 border-top" style="display:none;background:#f8f9fc;">
                        <div class="row align-items-center mb-2">
                            <div class="col-12 mb-1">
                                <small class="font-weight-bold text-success text-uppercase">
                                    <i class="fas fa-sliders-h mr-1"></i> Enhancement Gambar Kamera
                                </small>
                            </div>
                            <div class="col-6 slider-control">
                                <label class="d-flex justify-content-between">Kontras <span class="slider-val" id="valContrast">160%</span></label>
                                <input type="range" id="sliderContrast" min="80" max="300" value="160" oninput="updateFilters()">
                            </div>
                            <div class="col-6 slider-control">
                                <label class="d-flex justify-content-between">Kecerahan <span class="slider-val" id="valBrightness">110%</span></label>
                                <input type="range" id="sliderBrightness" min="60" max="250" value="110" oninput="updateFilters()">
                            </div>
                        </div>
                        <div class="row align-items-center">
                            <div class="col-6 slider-control">
                                <label class="d-flex justify-content-between">Zoom Digital <span class="slider-val" id="valZoom">1.0x</span></label>
                                <input type="range" id="sliderZoom" min="10" max="22" value="10" oninput="updateZoom()">
                            </div>
                            <div class="col-6 d-flex align-items-end pb-1 flex-wrap" style="gap:4px;">
                                <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()"><i class="fas fa-undo mr-1"></i>Reset</button>
                                <button class="btn btn-sm btn-outline-primary" onclick="presetDarkRoom()"><i class="fas fa-moon mr-1"></i>Gelap</button>
                                <button class="btn btn-sm btn-outline-warning" onclick="presetBright()"><i class="fas fa-sun mr-1"></i>Terang</button>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:8px;">
                            <small class="text-muted"><i class="fas fa-info-circle mr-1"></i><span id="fpsCounter">ZXing Code 128 Decoder Aktif</span></small>
                            <div class="d-flex align-items-center" style="gap:6px;">
                                <select id="cameraSelect" class="form-control form-control-sm" style="display:none;width:auto;" onchange="switchCamera(this.value)"></select>
                                <label class="btn btn-sm btn-outline-primary mb-0">
                                    <i class="fas fa-image mr-1"></i> Scan dari Foto
                                    <input type="file" id="fileInput2" accept="image/*" style="display:none;" onchange="scanFromFile(this)">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form id="formKamera" action="{{ route('scan.process') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="kode_asset" id="hasilScanKamera">
            </form>
        </div>

        {{-- KOLOM MANUAL --}}
        <div class="col-lg-5 mb-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-header py-3" style="background:linear-gradient(135deg,#4e73df,#375bd2);color:white;border-radius:.35rem .35rem 0 0;">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-keyboard mr-2"></i> Input Manual / USB Scanner</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('scan.process') }}" method="POST">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted">Kode Aset / Serial Number</label>
                            <input type="text" name="kode_asset" id="inputKode"
                                   class="form-control form-control-lg text-center font-weight-bold text-uppercase"
                                   placeholder="SCAN ATAU KETIK..."
                                   style="font-size:20px;letter-spacing:2px;"
                                   autofocus required>
                            <small class="text-muted">USB barcode scanner otomatis mengisi kolom ini.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow">
                            <i class="fas fa-search mr-2"></i> Cek Data Aset
                        </button>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header py-2 bg-light">
                    <small class="font-weight-bold text-muted"><i class="fas fa-lightbulb text-warning mr-1"></i> Tips Scan Barcode Laptop</small>
                </div>
                <div class="card-body py-2 px-3">
                    <ul class="small text-muted mb-0 pl-3" style="line-height:1.9;">
                        <li>Posisikan barcode <strong>di dalam kotak hijau</strong></li>
                        <li>Jarak optimal: <strong>10–20 cm</strong> dari kamera</li>
                        <li>Naikkan slider <strong>Kontras</strong> jika barcode kurang terbaca</li>
                        <li>Gunakan <strong>Zoom Digital</strong> untuk barcode ukuran kecil</li>
                        <li>Pastikan label tidak <strong>terlipat, kusut, atau mengkilat</strong></li>
                        <li>Jika kamera buram → gunakan <strong>Scan dari Foto</strong></li>
                        <li>Preset <strong>Gelap</strong> → ruangan minim cahaya</li>
                        <li>Preset <strong>Terang</strong> → ruangan terlalu silau</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── STATE ────────────────────────────────────────────────
let stream       = null;
let zxingReader  = null;
let decodeTimer  = null;
let scanCooldown = false;
let frameCount   = 0;
let fpsTimer     = null;

const video   = document.getElementById('scanVideo');
const canvas  = document.getElementById('processCanvas');
const ctx     = canvas.getContext('2d', { willReadFrequently: true });

// ─── AUTO FOCUS INPUT MANUAL ──────────────────────────────
document.addEventListener('click', function(e) {
    const skip = ['BUTTON','A','INPUT','SELECT','LABEL'];
    if (!skip.includes(e.target.tagName)) {
        const inp = document.getElementById('inputKode');
        if (inp) inp.focus();
    }
});

// ─── INIT ZXING ───────────────────────────────────────────
function initZXing() {
    const hints = new Map();
    hints.set(ZXing.DecodeHintType.POSSIBLE_FORMATS, [
        ZXing.BarcodeFormat.CODE_128,
        ZXing.BarcodeFormat.CODE_39,
        ZXing.BarcodeFormat.EAN_13,
        ZXing.BarcodeFormat.EAN_8,
        ZXing.BarcodeFormat.UPC_A,
        ZXing.BarcodeFormat.UPC_E,
        ZXing.BarcodeFormat.ITF,
        ZXing.BarcodeFormat.CODABAR,
        ZXing.BarcodeFormat.QR_CODE,
    ]);
    hints.set(ZXing.DecodeHintType.TRY_HARDER, true);
    zxingReader = new ZXing.MultiFormatReader();
    zxingReader.setHints(hints);
}

// ─── BUKA KAMERA ─────────────────────────────────────────
async function startScanner() {
    document.getElementById('btnStart').style.display         = 'none';
    document.getElementById('btnStop').style.display          = 'inline-block';
    document.getElementById('cameraPlaceholder').style.display= 'none';
    document.getElementById('guideZone').style.display        = 'block';
    document.getElementById('scanStatusBar').style.display    = 'block';
    document.getElementById('cameraControls').style.display   = 'block';
    showStatus('Membuka kamera...');

    initZXing();

    const tryConstraints = [
        { video: { width:{ideal:1280}, height:{ideal:720}, facingMode:'environment', advanced:[{focusMode:'continuous'}] } },
        { video: { width:{ideal:1280}, height:{ideal:720} } },
        { video: true }
    ];

    for (const c of tryConstraints) {
        try { stream = await navigator.mediaDevices.getUserMedia(c); break; }
        catch(e) { stream = null; }
    }

    if (!stream) {
        showStatus('<span class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Gagal akses kamera! Aktifkan izin kamera di browser.</span>');
        resetUI(); return;
    }

    video.srcObject = stream;
    video.onloadedmetadata = () => {
        video.play();
        showStatus('<i class="fas fa-crosshairs text-success mr-1"></i> Aktif — Arahkan kode batang ke kotak hijau');
        updateFilters();
        startDecoding();
        populateCameraSelect();
        startFpsCounter();
    };
}

// ─── ENGINE DECODE (INTERVAL 80ms ≈ 12fps) ───────────────
function startDecoding() {
    decodeTimer = setInterval(decodeFrame, 80);
}

function decodeFrame() {
    if (!stream || scanCooldown || video.readyState !== video.HAVE_ENOUGH_DATA) return;
    const vw = video.videoWidth, vh = video.videoHeight;
    if (!vw || !vh) return;

    canvas.width = vw; canvas.height = vh;
    ctx.drawImage(video, 0, 0, vw, vh);
    enhanceImageData();

    try {
        const src = new ZXing.HTMLCanvasElementLuminanceSource(canvas);
        const bmp = new ZXing.BinaryBitmap(new ZXing.HybridBinarizer(src));
        const res = zxingReader.decode(bmp);
        if (res && res.getText()) onDetected(res.getText());
    } catch(e) { /* NotFoundException normal */ }

    frameCount++;
}

// ─── PREPROCESSING KONTRAS + GRAYSCALE ───────────────────
function enhanceImageData() {
    const contrast   = parseInt(document.getElementById('sliderContrast').value) / 100;
    const brightness = parseInt(document.getElementById('sliderBrightness').value) / 100;
    const data       = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const d          = data.data;
    const factor     = (259 * (contrast * 255 + 255)) / (255 * (259 - contrast * 255));
    const bOff       = (brightness - 1) * 128;
    for (let i = 0; i < d.length; i += 4) {
        const gray = d[i]*0.299 + d[i+1]*0.587 + d[i+2]*0.114;
        let v = factor * ((gray + bOff) - 128) + 128;
        v = Math.max(0, Math.min(255, v));
        d[i] = d[i+1] = d[i+2] = v;
    }
    ctx.putImageData(data, 0, 0);
}

// ─── TERDETEKSI ───────────────────────────────────────────
function onDetected(text) {
    if (scanCooldown) return;
    scanCooldown = true;
    playBeep();
    document.getElementById('detectedText').innerText = text;
    document.getElementById('detectedBadge').style.display = 'block';
    showStatus('<span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> TERDETEKSI: ' + text + ' — Mengarahkan...</span>');
    clearInterval(decodeTimer);
    setTimeout(() => {
        document.getElementById('hasilScanKamera').value = text;
        document.getElementById('formKamera').submit();
    }, 600);
}

// ─── TUTUP KAMERA ─────────────────────────────────────────
function stopScanner() {
    clearInterval(decodeTimer);
    clearInterval(fpsTimer);
    if (stream) { stream.getTracks().forEach(t => t.stop()); stream = null; }
    video.srcObject = null;
    scanCooldown = false;
    resetUI();
}

function resetUI() {
    document.getElementById('btnStart').style.display          = 'inline-block';
    document.getElementById('btnStop').style.display           = 'none';
    document.getElementById('cameraPlaceholder').style.display = 'flex';
    document.getElementById('guideZone').style.display         = 'none';
    document.getElementById('scanStatusBar').style.display     = 'none';
    document.getElementById('cameraControls').style.display    = 'none';
    document.getElementById('detectedBadge').style.display     = 'none';
    document.getElementById('cameraSelect').style.display      = 'none';
}

// ─── FILTER CSS & ZOOM ────────────────────────────────────
function updateFilters() {
    const c = document.getElementById('sliderContrast').value;
    const b = document.getElementById('sliderBrightness').value;
    document.getElementById('valContrast').innerText   = c + '%';
    document.getElementById('valBrightness').innerText = b + '%';
    video.style.filter = `contrast(${c}%) brightness(${b}%)`;
}

function updateZoom() {
    const z = document.getElementById('sliderZoom').value / 10;
    document.getElementById('valZoom').innerText = z.toFixed(1) + 'x';
    video.style.transform = `scale(${z})`;
    video.style.transformOrigin = 'center center';
}

function resetFilters() {
    document.getElementById('sliderContrast').value   = 160;
    document.getElementById('sliderBrightness').value = 110;
    document.getElementById('sliderZoom').value       = 10;
    updateFilters(); updateZoom();
}

function presetDarkRoom() {
    document.getElementById('sliderContrast').value   = 240;
    document.getElementById('sliderBrightness').value = 165;
    updateFilters();
}

function presetBright() {
    document.getElementById('sliderContrast').value   = 200;
    document.getElementById('sliderBrightness').value = 75;
    updateFilters();
}

// ─── PILIH KAMERA ─────────────────────────────────────────
async function populateCameraSelect() {
    try {
        const devices = (await navigator.mediaDevices.enumerateDevices()).filter(d => d.kind === 'videoinput');
        if (devices.length > 1) {
            const sel = document.getElementById('cameraSelect');
            sel.innerHTML = '';
            devices.forEach((cam, i) => {
                const o = document.createElement('option');
                o.value = cam.deviceId;
                o.text  = cam.label || 'Kamera ' + (i+1);
                sel.appendChild(o);
            });
            sel.style.display = 'inline-block';
        }
    } catch(e) {}
}

async function switchCamera(id) {
    if (stream) stream.getTracks().forEach(t => t.stop());
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { deviceId:{exact:id}, width:{ideal:1280}, height:{ideal:720} } });
        video.srcObject = stream;
        video.play();
    } catch(e) {}
}

// ─── SCAN DARI FILE FOTO ──────────────────────────────────
function scanFromFile(input) {
    if (!input.files || !input.files[0]) return;
    showStatus('<i class="fas fa-spinner fa-spin mr-1 text-primary"></i> Membaca barcode dari foto...');

    const img = new Image();
    const url = URL.createObjectURL(input.files[0]);

    img.onload = function() {
        const fc   = document.createElement('canvas');
        fc.width   = img.naturalWidth;
        fc.height  = img.naturalHeight;
        const fctx = fc.getContext('2d', { willReadFrequently:true });
        fctx.drawImage(img, 0, 0);

        // Enhance file image
        const d    = fctx.getImageData(0, 0, fc.width, fc.height);
        const px   = d.data;
        const f    = (259 * (160 + 255)) / (255 * (259 - 160));
        for (let i = 0; i < px.length; i += 4) {
            const g = px[i]*0.299 + px[i+1]*0.587 + px[i+2]*0.114;
            let v   = f * (g - 128) + 128;
            v = Math.max(0, Math.min(255, v));
            px[i] = px[i+1] = px[i+2] = v;
        }
        fctx.putImageData(d, 0, 0);

        if (!zxingReader) initZXing();

        try {
            const src = new ZXing.HTMLCanvasElementLuminanceSource(fc);
            const bmp = new ZXing.BinaryBitmap(new ZXing.HybridBinarizer(src));
            const res = zxingReader.decode(bmp);
            if (res && res.getText()) {
                playBeep();
                showStatus('<span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i> Terdeteksi dari foto: ' + res.getText() + '</span>');
                document.getElementById('hasilScanKamera').value = res.getText();
                setTimeout(() => document.getElementById('formKamera').submit(), 700);
            }
        } catch(e) {
            showStatus('<span class="text-danger"><i class="fas fa-times-circle mr-1"></i> Barcode tidak terbaca di foto. Pastikan foto jernih, tidak buram dan tidak terlipat.</span>');
        }
        URL.revokeObjectURL(url);
    };
    img.src = url;
}

// ─── FPS COUNTER ──────────────────────────────────────────
function startFpsCounter() {
    fpsTimer = setInterval(() => {
        document.getElementById('fpsCounter').innerText = 'ZXing Code 128 — ' + (frameCount * 2) + ' frame/det diproses';
        frameCount = 0;
    }, 500);
}

// ─── STATUS & BEEP ────────────────────────────────────────
function showStatus(html) {
    const el = document.getElementById('statusText');
    if (el) el.innerHTML = html;
}

function playBeep() {
    try {
        const AC = window.AudioContext || window.webkitAudioContext;
        if (!AC) return;
        const ac=new AC(), osc=ac.createOscillator(), g=ac.createGain();
        osc.type='sine'; osc.frequency.setValueAtTime(1800, ac.currentTime);
        g.gain.setValueAtTime(0.35, ac.currentTime);
        g.gain.exponentialRampToValueAtTime(0.001, ac.currentTime+0.18);
        osc.connect(g); g.connect(ac.destination);
        osc.start(); osc.stop(ac.currentTime+0.18);
    } catch(e) {}
}
</script>

@if(session('error_unregistered'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kode = @json(session('error_unregistered'));
    Swal.fire({
        title: 'Aset Belum Terdaftar!',
        html: 'Kode <strong>"' + kode + '"</strong> berhasil dibaca,<br>namun <u>belum tercatat</u> di database aset.<br><br>Daftarkan aset baru?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        confirmButtonText: '<i class="fas fa-plus-circle mr-1"></i> Input Barang Masuk Baru',
        cancelButtonText: 'Tutup / Scan Lagi'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("barangmasuk.create") }}?serial_number=' + encodeURIComponent(kode);
        }
    });
});
</script>
@endif
@endpush