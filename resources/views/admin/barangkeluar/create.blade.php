@extends('layouts.app')

@section('title', 'Buat Serah Terima Aset (BAST)')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-handshake mr-2"></i> Form Serah Terima Aset
    </h1>

    {{-- Tampilkan Error Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('barangkeluar.store') }}" method="POST" id="bast-form" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            {{-- KOLOM KIRI: PILIH BARANG --}}
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">1. Pilih Aset (Stok Tersedia)</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Cari Aset</label>
                            <select name="barang_masuk_id" id="aset_select" class="form-control select2" required>
                                <option value="">-- Cari Kode / SN / Nama Barang --</option>
                                @foreach ($asetStok as $aset)
                                    <option value="{{ $aset->id }}">
                                        {{ $aset->kode_asset }} - {{ $aset->masterBarang->nama_barang }} (SN: {{ $aset->serial_number }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- AREA DETAIL ASET --}}
                        <div id="loading-asset" class="text-center py-3" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <small class="d-block mt-2">Mengambil data...</small>
                        </div>

                        <div id="detail-aset" class="alert alert-secondary mt-3" style="display:none; border-left: 5px solid #4e73df;">
                            <h6 class="font-weight-bold text-dark mb-2">Detail Aset:</h6>
                            <table class="table table-sm table-borderless mb-0" style="font-size: 0.9rem;">
                                <tr>
                                    <td width="35%" class="text-muted">Merk / Model</td>
                                    <td class="font-weight-bold" id="d_merk_model"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Serial Number</td>
                                    <td class="font-weight-bold" id="d_sn"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Spesifikasi</td>
                                    <td id="d_spek"></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Dokumen (SJ/PO)</td>
                                    <td id="d_dokumen"></td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: PENERIMA & TANDA TANGAN --}}
            <div class="col-lg-6">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold">2. Data Penerima & Penyerahan</h6>
                    </div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="font-weight-bold">User Penerima (Karyawan)</label>
                            <select name="user_pemegang_id" class="form-control select2" required>
                                <option value="">-- Pilih Nama User --</option>
                                @foreach ($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->nama }} - {{ $u->jabatan ?? 'Karyawan' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Serah Terima</label>
                                    <input type="date" name="tanggal_serah_terima" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Bukti Foto (Opsional)</label>
                                    <input type="file" name="foto_bukti" class="form-control-file" style="font-size: 0.9rem;">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Keterangan / Kelengkapan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Unit lengkap tas, charger, mouse..."></textarea>
                        </div>

                        <hr>

                        {{-- FITUR TANDA TANGAN --}}
                        <h6 class="font-weight-bold text-dark">Tanda Tangan Digital</h6>
                        <div class="alert alert-warning py-2" style="font-size: 0.85rem;">
                            <i class="fas fa-info-circle"></i> 
                            Silakan tanda tangan pada kotak di bawah ini.
                        </div>

                        <div class="row">
                            <div class="col-md-6 text-center mb-3">
                                <label class="small font-weight-bold mb-1">Penerima (User)</label>
                                {{-- PERBAIKAN: Style height wajib ada agar canvas muncul --}}
                                <div class="border rounded" style="background: #f8f9fc; height: 200px; width: 100%;">
                                    <canvas id="pad-penerima" style="width: 100%; height: 200px; display: block;"></canvas>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger mt-1 btn-block" onclick="clearPenerima()">
                                    <i class="fas fa-eraser"></i> Hapus / Ulangi
                                </button>
                                <input type="hidden" name="ttd_penerima" id="inp-penerima">
                            </div>

                            <div class="col-md-6 text-center mb-3">
                                <label class="small font-weight-bold mb-1">Penyerah (Admin)</label>
                                <div class="border rounded" style="background: #f8f9fc; height: 200px; width: 100%;">
                                    <canvas id="pad-petugas" style="width: 100%; height: 200px; display: block;"></canvas>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger mt-1 btn-block" onclick="clearPetugas()">
                                    <i class="fas fa-eraser"></i> Hapus / Ulangi
                                </button>
                                <input type="hidden" name="ttd_petugas" id="inp-petugas">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow">
                            <i class="fas fa-save mr-2"></i> PROSES SERAH TERIMA
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
{{-- Load Library --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<script>
    var padPenerima, padPetugas;

    $(document).ready(function() {
        
        // 1. SELECT2
        $('.select2').select2({ theme: "classic", width: '100%' });

        // 2. SETUP SIGNATURE PAD (FIXED)
        var canvas1 = document.getElementById('pad-penerima');
        var canvas2 = document.getElementById('pad-petugas');

        function resizeCanvas(canvas) {
            // Mengatasi masalah resolusi pada layar retina/HP
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            
            // Set ukuran internal canvas sesuai ukuran elemen CSS yang tampil
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }

        // Panggil resize
        resizeCanvas(canvas1);
        resizeCanvas(canvas2);

        // Inisialisasi Pad
        padPenerima = new SignaturePad(canvas1, { backgroundColor: 'rgba(255, 255, 255, 0)' });
        padPetugas = new SignaturePad(canvas2, { backgroundColor: 'rgba(255, 255, 255, 0)' });

        // Responsif saat layar diubah
        window.onresize = function() {
            resizeCanvas(canvas1);
            resizeCanvas(canvas2);
        };

        // 3. AJAX AMBIL DATA
        $('#aset_select').change(function() {
            let asetId = $(this).val();
            $('#detail-aset').hide();
            if (!asetId) return;

            $('#loading-asset').show();
            $.ajax({
                url: "{{ route('barangkeluar.getAssetDetails') }}",
                type: "GET",
                data: { id: asetId },
                success: function(response) {
                    $('#loading-asset').hide();
                    $('#d_merk_model').text((response.merk || '-') + ' ' + (response.model || ''));
                    $('#d_sn').text(response.serial_number);
                    $('#d_spek').text(response.spesifikasi || '-');
                    $('#d_dokumen').text((response.no_sj || '-') + ' / ' + (response.no_po || '-'));
                    $('#detail-aset').fadeIn();
                },
                error: function() {
                    $('#loading-asset').hide();
                    alert("Gagal mengambil data aset.");
                }
            });
        });

        // 4. SUBMIT FORM
        $('#bast-form').submit(function(e) {
            if (!padPenerima.isEmpty()) {
                $('#inp-penerima').val(padPenerima.toDataURL());
            }
            if (!padPetugas.isEmpty()) {
                $('#inp-petugas').val(padPetugas.toDataURL());
            }
        });
    });

    function clearPenerima() {
        padPenerima.clear();
        $('#inp-penerima').val('');
    }
    function clearPetugas() {
        padPetugas.clear();
        $('#inp-petugas').val('');
    }
</script>
@endpush