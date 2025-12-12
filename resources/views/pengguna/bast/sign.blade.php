@extends('layouts.app') 

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-file-contract mr-2"></i> Konfirmasi Penerimaan Aset</h5>
                </div>
                <div class="card-body">
                    
                    {{-- INFO ASET --}}
                    <div class="alert alert-info">
                        <strong>Barang yang diterima:</strong><br>
                        {{ $bast->aset->masterBarang->nama_barang ?? '-' }} <br>
                        <small>SN: {{ $bast->aset->serial_number }} | Kode: {{ $bast->aset->kode_asset }}</small>
                    </div>

                    <form action="{{ route('pengguna.userbast.submit', $bast->id) }}" method="POST" id="form-sign">
                        @csrf

                        {{-- AREA SYARAT & KETENTUAN (WAJIB SCROLL) --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Syarat & Ketentuan (Harap baca sampai selesai)</label>
                            
                            <div id="sk-box" class="border rounded p-3 bg-light" style="height: 250px; overflow-y: scroll; text-align: justify;">
                                
                                @php
                                    // 1. Coba ambil dari relasi kategori->nama_kategori
                                    // 2. Kalau null, coba ambil langsung kolom kategori
                                    // 3. Default '-'
                                    $kategoriRaw = $bast->aset->masterBarang->kategori->nama_kategori 
                                                   ?? $bast->aset->masterBarang->kategori 
                                                   ?? '-';
                                    
                                    // Bersihkan spasi depan/belakang agar pembacaan akurat
                                    $kategori = trim($kategoriRaw);
                                @endphp

                                {{-- DEBUGGING: Tampilkan kategori yang terbaca sistem --}}
                                <div class="alert alert-warning py-2 mb-3">
                                    <small>
                                        <i class="fas fa-tag mr-1"></i> Sistem Membaca Kategori: <strong>"{{ $kategori }}"</strong>
                                    </small>
                                </div>

                                {{-- === LOGIKA S&K (MENGGUNAKAN 'STRIPOS' AGAR TIDAK PEDULI HURUF BESAR/KECIL) === --}}
                                
                                {{-- Cek jika mengandung kata "Radio" --}}
                                @if (stripos($kategori, 'Radio') !== false || stripos($kategori, 'Rig') !== false)
                                    <h6 class="font-weight-bold">Khusus Perangkat Komunikasi (Radio Rig/HT):</h6>
                                    <ul>
                                        <li>Pemegang wajib menjaga alat dan menggunakannya sesuai SOP komunikasi perusahaan.</li>
                                        <li>Tidak boleh memodifikasi frekuensi tanpa izin IT.</li>
                                        <li>Bertanggung jawab penuh atas kehilangan atau kerusakan unit.</li>
                                        <li>Wajib melaporkan jika terjadi gangguan sinyal atau kerusakan fisik.</li>
                                    </ul>

                                {{-- Cek jika mengandung kata "Laptop", "Komputer", atau "PC" --}}
                                @elseif (stripos($kategori, 'Laptop') !== false || stripos($kategori, 'Komputer') !== false || stripos($kategori, 'PC') !== false)
                                    <h6 class="font-weight-bold">Khusus Perangkat Komputer/Laptop:</h6>
                                    <ul>
                                        <li>Pemegang wajib menjaga kerahasiaan data perusahaan yang tersimpan di dalam perangkat.</li>
                                        <li>Dilarang menginstall software ilegal (bajakan) atau aplikasi yang membahayakan keamanan jaringan.</li>
                                        <li>Dilarang meminjamkan perangkat kepada pihak luar tanpa izin tertulis.</li>
                                        <li>Wajib melakukan update antivirus dan menjaga kebersihan fisik perangkat.</li>
                                    </ul>

                                @else
                                    {{-- DEFAULT S&K --}}
                                    <h6 class="font-weight-bold">Syarat & Ketentuan Umum:</h6>
                                    <p><strong>1. Tanggung Jawab Penggunaan</strong><br>
                                    Karyawan bertanggung jawab penuh atas keamanan dan kondisi aset yang diserahkan. Kerusakan akibat kelalaian (human error) akan menjadi tanggung jawab pemegang aset.</p>
                                    
                                    <p><strong>2. Pengembalian Aset</strong><br>
                                    Apabila karyawan mengundurkan diri atau dimutasi, aset wajib dikembalikan dalam kondisi lengkap dan baik kepada departemen IT.</p>

                                    <p><strong>3. Pelaporan Kerusakan</strong><br>
                                    Segala bentuk kerusakan hardware atau software wajib dilaporkan ke IT Support maksimal 1x24 jam setelah kejadian.</p>
                                @endif
                                {{-- ====================================== --}}
                                
                                <br>
                                <p class="text-center text-muted"><em>--- Akhir dari Syarat & Ketentuan ---</em></p>
                            </div>
                            
                            {{-- Notifikasi kecil --}}
                            <small id="sk-warning" class="text-danger">* Silakan scroll S&K sampai mentok bawah untuk menyetujui.</small>
                        </div>

                        {{-- CHECKBOX PERSETUJUAN --}}
                        <div class="form-group mt-3">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="agree-check" name="agree" disabled>
                                <label class="custom-control-label" for="agree-check">
                                    Saya telah membaca, memahami, dan menyetujui Syarat & Ketentuan di atas.
                                </label>
                            </div>
                        </div>

                        {{-- AREA TANDA TANGAN (MUNCUL SETELAH CENTANG) --}}
                        <div id="signature-area" style="display: none; margin-top: 20px;">
                            <label class="font-weight-bold">Tanda Tangan Digital Penerima</label>
                            <div class="border rounded d-inline-block shadow-sm" style="background: #fff;">
                                <canvas id="ttd-pad" width="400" height="200"></canvas>
                            </div>
                            <br>
                            <button type="button" class="btn btn-sm btn-secondary mt-2" id="clear-btn">
                                <i class="fas fa-eraser"></i> Hapus / Ulangi
                            </button>
                            <input type="hidden" name="ttd_penerima" id="ttd_penerima_input">
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-success btn-lg btn-block" id="submit-btn" disabled>
                            <i class="fas fa-check-circle"></i> Terima Aset & Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT PENTING --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- 1. LOGIKA SCROLL S&K ---
    const skBox = document.getElementById('sk-box');
    const agreeCheck = document.getElementById('agree-check');
    const skWarning = document.getElementById('sk-warning');

    // Reset state saat reload
    agreeCheck.checked = false;
    agreeCheck.disabled = true;

    skBox.addEventListener('scroll', function() {
        // Cek apakah sudah scroll sampai bawah (dengan toleransi 5px)
        if (skBox.scrollHeight - skBox.scrollTop <= skBox.clientHeight + 5) {
            agreeCheck.disabled = false; // Aktifkan checkbox
            skWarning.style.display = 'none'; // Sembunyikan peringatan
            skBox.classList.remove('border-danger');
            skBox.classList.add('border-success');
        }
    });

    // --- 2. LOGIKA CHECKBOX ---
    const signatureArea = document.getElementById('signature-area');
    const submitBtn = document.getElementById('submit-btn');

    agreeCheck.addEventListener('change', function() {
        if (this.checked) {
            signatureArea.style.display = 'block'; // Tampilkan Canvas
            resizeCanvas(); // Refresh ukuran canvas
        } else {
            signatureArea.style.display = 'none';
            submitBtn.disabled = true;
        }
    });

    // --- 3. LOGIKA CANVAS TANDA TANGAN ---
    const canvas = document.getElementById('ttd-pad');
    const signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });
    const clearBtn = document.getElementById('clear-btn');
    const ttdInput = document.getElementById('ttd_penerima_input');
    const form = document.getElementById('form-sign');

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); // Reset saat resize agar tidak pecah
    }
    window.addEventListener("resize", resizeCanvas);

    clearBtn.addEventListener('click', function() {
        signaturePad.clear();
    });

    // Saat user mulai tanda tangan, aktifkan tombol submit
    signaturePad.addEventListener("endStroke", () => {
        if (!signaturePad.isEmpty()) {
            submitBtn.disabled = false;
        }
    });

    // --- 4. SUBMIT FORM ---
    form.addEventListener('submit', function(e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            alert("Harap tanda tangan terlebih dahulu!");
        } else {
            // Masukkan data gambar base64 ke input hidden
            ttdInput.value = signaturePad.toDataURL('image/png');
        }
    });

});
</script>
@endsection