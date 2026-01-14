@extends('layouts.app') 

@section('content')
<div class="container py-3">

    {{-- JUDUL HALAMAN --}}
    <div class="d-none d-md-block mb-4">
        <h1 class="h3 text-gray-800">Konfirmasi Penerimaan Aset</h1>
    </div>
    <div class="d-md-none mb-3">
        <h1 class="h4 text-gray-800">Tanda Tangan BAST</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <div class="card shadow-lg">
                
                {{-- HEADER --}}
                <div class="card-header bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-file-contract mr-3 fa-lg"></i> 
                    <h6 class="m-0 font-weight-bold">Formulir Digital Serah Terima</h6>
                </div>

                <div class="card-body">
                    
                    {{-- DETAIL ASET --}}
                    <div class="d-none d-md-block">
                        <div class="alert alert-secondary p-4">
                            <h5 class="alert-heading font-weight-bold mb-3"><i class="fas fa-box"></i> Detail Aset:</h5>
                            <table class="table table-borderless table-sm mb-0">
                                <tr>
                                    <td width="20%" class="text-muted">Nama Barang</td>
                                    <td class="font-weight-bold">: {{ $bast->aset->masterBarang->nama_barang ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Kategori</td>
                                    <td class="font-weight-bold">: {{ $bast->aset->masterBarang->kategori ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Serial Number</td>
                                    <td class="font-weight-bold">: {{ $bast->aset->serial_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    {{-- TAMPILAN MOBILE --}}
                    <div class="d-md-none mb-4">
                        <div class="bg-light p-3 rounded border-left-primary shadow-sm">
                            <label class="small text-muted mb-0">Barang:</label>
                            <div class="h5 font-weight-bold text-primary mb-1">
                                {{ $bast->aset->masterBarang->nama_barang ?? '-' }}
                            </div>
                            <small class="text-muted">Kategori: {{ $bast->aset->masterBarang->kategori ?? '-' }}</small>
                        </div>
                    </div>

                    {{-- FORM START --}}
                    <form action="{{ route('pengguna.userbast.submit', $bast->id) }}" method="POST" id="form-sign">
                        @csrf

                        {{-- SYARAT & KETENTUAN --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-danger">
                                <i class="fas fa-exclamation-circle"></i> Syarat & Ketentuan
                            </label>
                            
                            <div id="sk-box" class="border rounded p-3 bg-white" 
                                 style="height: 200px; overflow-y: auto; text-align: justify; border: 2px solid #e3e6f0;">
                                
                                @php
                                    // AMBIL LANGSUNG FIELD KATEGORI
                                    // Pakai trim() biar spasi "Laptop " jadi "Laptop" bersih
                                    $kategori = trim($bast->aset->masterBarang->kategori ?? '');
                                @endphp

                                {{-- DEBUG (Hapus nanti kalau mau bersih): --}}
                                {{-- <small class="d-block text-danger mb-2">Debug Kategori: "{{ $kategori }}"</small> --}}

                                {{-- LOGIKA PENGECEKAN --}}
                                @if (stripos($kategori, 'Radio') !== false || stripos($kategori, 'HT') !== false)
                                    
                                    <h6 class="font-weight-bold text-dark">Aturan Penggunaan Radio Komunikasi:</h6>
                                    <ul class="pl-3 small text-dark">
                                        <li>Wajib menjaga alat dan menggunakan sesuai SOP.</li>
                                        <li>Dilarang memodifikasi frekuensi tanpa izin IT.</li>
                                        <li>Bertanggung jawab penuh atas kerusakan fisik.</li>
                                        <br><br><br> </ul>

                                @elseif (stripos($kategori, 'Laptop') !== false || stripos($kategori, 'PC') !== false || stripos($kategori, 'Komputer') !== false)
                                    
                                    <h6 class="font-weight-bold text-dark">Aturan Penggunaan Komputer/Laptop:</h6>
                                    <ul class="pl-3 small text-dark">
                                        <li>Dilarang install software bajakan/ilegal.</li>
                                        <li>Wajib menjaga kerahasiaan data perusahaan.</li>
                                        <li>Tidak boleh meminjamkan ke pihak luar.</li>
                                        <br><br><br> </ul>

                                @else
                                    
                                    <h6 class="font-weight-bold text-dark">Ketentuan Umum:</h6>
                                    <p class="small text-dark">
                                        1. <strong>Tanggung Jawab:</strong> Karyawan bertanggung jawab penuh atas keamanan aset.<br>
                                        2. <strong>Kerusakan:</strong> Kerusakan akibat kelalaian (human error) ditanggung pemegang aset.<br>
                                        3. <strong>Pengembalian:</strong> Aset wajib dikembalikan saat mutasi/resign.
                                        <br><br><br> </p>

                                @endif
                                
                                <p class="text-center text-muted small mt-5"><em>--- Akhir Dokumen ---</em></p>
                            </div>
                        </div>

                        {{-- CHECKBOX --}}
                        <div class="custom-control custom-checkbox mb-4">
                            <input type="checkbox" class="custom-control-input" id="agree-check" name="agree" disabled>
                            <label class="custom-control-label font-weight-bold text-dark" for="agree-check">
                                Saya menyetujui Syarat & Ketentuan di atas.
                            </label>
                        </div>

                        {{-- AREA TANDA TANGAN --}}
                        <div id="signature-area" style="display: none;">
                            <label class="font-weight-bold">Tanda Tangan Digital:</label>
                            
                            {{-- Container Canvas --}}
                            <div id="canvas-container" class="border border-dark rounded mb-2" style="width: 100%; height: 200px; background: #fff; touch-action: none;">
                                <canvas id="ttd-pad" style="width: 100%; height: 100%; display: block;"></canvas>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger mb-3" id="clear-btn">
                                <i class="fas fa-eraser"></i> Hapus Tanda Tangan
                            </button>
                            
                            <input type="hidden" name="ttd_penerima" id="ttd_penerima_input">

                            <hr>
                            
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
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // VARIABEL
    const skBox = document.getElementById('sk-box');
    const agreeCheck = document.getElementById('agree-check');
    const signatureArea = document.getElementById('signature-area');
    const submitBtn = document.getElementById('submit-btn');
    const canvas = document.getElementById('ttd-pad');
    const container = document.getElementById('canvas-container');
    const clearBtn = document.getElementById('clear-btn');
    const ttdInput = document.getElementById('ttd_penerima_input');
    const form = document.getElementById('form-sign');

    // SETUP SIGNATURE PAD
    var signaturePad = new SignaturePad(canvas, { 
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)'
    });

    // 1. LOGIKA SCROLL S&K
    function checkScroll() {
        if (skBox.scrollHeight <= skBox.clientHeight || (skBox.scrollHeight - skBox.scrollTop <= skBox.clientHeight + 20)) {
            agreeCheck.disabled = false;
            skBox.style.borderColor = '#1cc88a'; 
        }
    }
    checkScroll();
    skBox.addEventListener('scroll', checkScroll);

    // 2. RESIZE CANVAS (Agar tidak gepeng/hilang)
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        if(container.offsetWidth > 0) {
            canvas.width = container.offsetWidth * ratio;
            canvas.height = container.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear(); 
        }
    }

    // 3. EVENT LISTENER CHECKBOX
    agreeCheck.addEventListener('change', function() {
        if (this.checked) {
            signatureArea.style.display = 'block';
            setTimeout(function() {
                resizeCanvas();
                container.scrollIntoView({behavior: "smooth", block: "center"});
            }, 200);
        } else {
            signatureArea.style.display = 'none';
        }
    });

    // Resize saat layar berubah
    window.addEventListener("resize", function() {
        if(signatureArea.style.display !== 'none') {
            resizeCanvas();
        }
    });

    // 4. TOMBOL CLEAR
    clearBtn.addEventListener('click', function() {
        signaturePad.clear();
        submitBtn.disabled = true;
    });

    // 5. TOMBOL SUBMIT AKTIF JIKA ADA TTD
    signaturePad.addEventListener("endStroke", () => {
        if (!signaturePad.isEmpty()) {
            submitBtn.disabled = false;
        }
    });

    // 6. SUBMIT
    form.addEventListener('submit', function(e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            alert("Mohon tanda tangan terlebih dahulu.");
            container.scrollIntoView({behavior: "smooth", block: "center"});
        } else {
            ttdInput.value = signaturePad.toDataURL();
        }
    });
});
</script>
@endsection