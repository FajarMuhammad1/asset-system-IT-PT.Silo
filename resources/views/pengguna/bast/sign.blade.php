@extends('layouts.app') 

@section('content')
<div class="container py-3">

    {{-- JUDUL HALAMAN (BEDA DESKTOP & MOBILE) --}}
    <div class="d-none d-md-block mb-4">
        <h1 class="h3 text-gray-800">Konfirmasi Penerimaan Aset</h1>
    </div>
    <div class="d-md-none mb-3">
        <h1 class="h4 text-gray-800">Tanda Tangan BAST</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card shadow-lg">
                
                {{-- HEADER CARD --}}
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-file-contract mr-3 fa-lg"></i> 
                    <h6 class="m-0 font-weight-bold">Formulir Digital Serah Terima</h6>
                </div>

                <div class="card-body">
                    
                    {{-- =============================================== --}}
                    {{-- 1. INFO BARANG (DESKTOP VIEW) - Tabel Rapi --}}
                    {{-- =============================================== --}}
                    <div class="d-none d-md-block">
                        <div class="alert alert-secondary p-4">
                            <h5 class="alert-heading font-weight-bold mb-3"><i class="fas fa-box"></i> Detail Aset yang Diterima:</h5>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td width="20%" class="text-muted">Nama Barang</td>
                                    <td class="font-weight-bold text-dark">: {{ $bast->aset->masterBarang->nama_barang ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kode Aset</td>
                                    <td class="font-weight-bold">: <span class="badge badge-primary">{{ $bast->aset->kode_asset }}</span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Serial Number</td>
                                    <td class="font-weight-bold">: {{ $bast->aset->serial_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- =============================================== --}}
                    {{-- 1. INFO BARANG (MOBILE VIEW) - Card Compact --}}
                    {{-- =============================================== --}}
                    <div class="d-md-none mb-4">
                        <div class="bg-light p-3 rounded border-left-primary shadow-sm">
                            <label class="small text-muted mb-0">Barang yang diterima:</label>
                            <div class="h5 font-weight-bold text-primary mb-2">
                                {{ $bast->aset->masterBarang->nama_barang ?? '-' }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <small class="text-muted d-block">Kode Aset:</small>
                                    <span class="font-weight-bold text-dark">{{ $bast->aset->kode_asset }}</span>
                                </div>
                                <div class="text-right">
                                    <small class="text-muted d-block">SN:</small>
                                    <span class="font-weight-bold text-dark">{{ $bast->aset->serial_number }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- FORM START --}}
                    <form action="{{ route('pengguna.userbast.submit', $bast->id) }}" method="POST" id="form-sign">
                        @csrf

                        {{-- =============================================== --}}
                        {{-- 2. SYARAT & KETENTUAN (RESPONSIVE BOX) --}}
                        {{-- =============================================== --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-danger">
                                <i class="fas fa-exclamation-circle"></i> Syarat & Ketentuan
                            </label>
                            <p class="small text-muted mb-2">Silakan baca dan scroll kotak di bawah ini sampai habis untuk mengaktifkan persetujuan.</p>
                            
                            {{-- Tinggi Box: Desktop 250px, HP 200px (biar gak menuhin layar) --}}
                            <div id="sk-box" class="border rounded p-3 bg-white" 
                                 style="height: 200px; overflow-y: scroll; text-align: justify; border: 2px solid #e3e6f0;">
                                
                                @php
                                    $kategoriRaw = $bast->aset->masterBarang->kategori->nama_kategori 
                                                   ?? $bast->aset->masterBarang->kategori 
                                                   ?? '-';
                                    $kategori = trim($kategoriRaw);
                                @endphp

                                {{-- Isi S&K (Sama seperti sebelumnya) --}}
                                @if (stripos($kategori, 'Radio') !== false || stripos($kategori, 'Rig') !== false)
                                    <h6 class="font-weight-bold text-dark">Aturan Penggunaan Radio Komunikasi:</h6>
                                    <ul class="pl-3 small text-dark">
                                        <li>Wajib menjaga alat dan menggunakan sesuai SOP.</li>
                                        <li>Dilarang memodifikasi frekuensi tanpa izin IT.</li>
                                        <li>Bertanggung jawab penuh atas kerusakan fisik.</li>
                                    </ul>
                                @elseif (stripos($kategori, 'Laptop') !== false || stripos($kategori, 'PC') !== false)
                                    <h6 class="font-weight-bold text-dark">Aturan Penggunaan Komputer/Laptop:</h6>
                                    <ul class="pl-3 small text-dark">
                                        <li>Dilarang install software bajakan/ilegal.</li>
                                        <li>Wajib menjaga kerahasiaan data perusahaan.</li>
                                        <li>Tidak boleh meminjamkan ke pihak luar.</li>
                                    </ul>
                                @else
                                    <h6 class="font-weight-bold text-dark">Ketentuan Umum:</h6>
                                    <p class="small text-dark">
                                        1. <strong>Tanggung Jawab:</strong> Karyawan bertanggung jawab penuh atas keamanan aset.<br>
                                        2. <strong>Kerusakan:</strong> Kerusakan akibat kelalaian (human error) ditanggung pemegang aset.<br>
                                        3. <strong>Pengembalian:</strong> Aset wajib dikembalikan saat mutasi/resign.
                                    </p>
                                @endif
                                
                                <br>
                                <p class="text-center text-muted small"><em>--- Akhir Dokumen ---</em></p>
                            </div>
                        </div>

                        {{-- CHECKBOX --}}
                        <div class="custom-control custom-checkbox mb-4">
                            <input type="checkbox" class="custom-control-input" id="agree-check" name="agree" disabled>
                            <label class="custom-control-label font-weight-bold text-dark" for="agree-check">
                                Saya menyetujui Syarat & Ketentuan di atas.
                            </label>
                        </div>

                        {{-- =============================================== --}}
                        {{-- 3. AREA TANDA TANGAN (FULL WIDTH DI HP) --}}
                        {{-- =============================================== --}}
                        <div id="signature-area" style="display: none;">
                            <label class="font-weight-bold">Tanda Tangan Digital:</label>
                            
                            {{-- Container ini penting untuk menghitung lebar canvas di HP --}}
                            <div id="canvas-container" class="border border-dark rounded mb-2" style="width: 100%; height: 200px; background: #fff; touch-action: none;">
                                <canvas id="ttd-pad" style="width: 100%; height: 100%;"></canvas>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger mb-3" id="clear-btn">
                                <i class="fas fa-eraser"></i> Hapus Tanda Tangan
                            </button>
                            
                            <input type="hidden" name="ttd_penerima" id="ttd_penerima_input">

                            <hr>
                            
                            {{-- Tombol Submit Besar di HP --}}
                            <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm" id="submit-btn" disabled>
                                <i class="fas fa-check-circle mr-2"></i> Konfirmasi Terima Aset
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- VARIABEL ---
    const skBox = document.getElementById('sk-box');
    const agreeCheck = document.getElementById('agree-check');
    const signatureArea = document.getElementById('signature-area');
    const submitBtn = document.getElementById('submit-btn');
    const canvas = document.getElementById('ttd-pad');
    const container = document.getElementById('canvas-container'); // Container pembungkus
    const clearBtn = document.getElementById('clear-btn');
    const ttdInput = document.getElementById('ttd_penerima_input');
    const form = document.getElementById('form-sign');

    // Inisialisasi SignaturePad
    const signaturePad = new SignaturePad(canvas, { 
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 0)'
    });

    // --- 1. LOGIKA SCROLL S&K ---
    agreeCheck.checked = false;
    agreeCheck.disabled = true;

    skBox.addEventListener('scroll', function() {
        if (skBox.scrollHeight - skBox.scrollTop <= skBox.clientHeight + 10) {
            agreeCheck.disabled = false;
            skBox.style.borderColor = '#1cc88a'; // Jadi hijau kalau sudah mentok
        }
    });

    // --- 2. TAMPILKAN AREA TANDA TANGAN ---
    agreeCheck.addEventListener('change', function() {
        if (this.checked) {
            signatureArea.style.display = 'block';
            resizeCanvas(); // PENTING: Resize saat area muncul agar tidak gepeng
        } else {
            signatureArea.style.display = 'none';
        }
    });

    // --- 3. LOGIKA RESIZE CANVAS (PENTING BUAT HP) ---
    // Fungsi ini memastikan canvas selalu selebar layar HP
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        
        // Ambil lebar container, bukan lebar layar window
        canvas.width = container.offsetWidth * ratio;
        canvas.height = container.offsetHeight * ratio;
        
        // Scale context agar tinta tidak pecah
        canvas.getContext("2d").scale(ratio, ratio);
        
        // Bersihkan ulang (karena resize menghapus isi canvas)
        // Kita simpan data lama jika perlu, tapi untuk simpel kita clear saja
        signaturePad.clear(); 
    }

    // Panggil resize saat layar diputar atau diubah ukurannya
    window.addEventListener("resize", resizeCanvas);

    // --- 4. TOMBOL CLEAR ---
    clearBtn.addEventListener('click', function() {
        signaturePad.clear();
        submitBtn.disabled = true; // Matikan tombol submit lagi
    });

    // --- 5. AKTIFKAN TOMBOL SUBMIT SAAT TTD ---
    signaturePad.addEventListener("endStroke", () => {
        if (!signaturePad.isEmpty()) {
            submitBtn.disabled = false;
        }
    });

    // --- 6. SUBMIT FORM ---
    form.addEventListener('submit', function(e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            alert("Mohon tanda tangan terlebih dahulu pada kotak yang tersedia.");
            // Scroll ke canvas otomatis
            container.scrollIntoView({behavior: "smooth"});
        } else {
            // Masukkan data gambar ke input hidden
            ttdInput.value = signaturePad.toDataURL('image/png');
        }
    });
});
</script>
@endsection