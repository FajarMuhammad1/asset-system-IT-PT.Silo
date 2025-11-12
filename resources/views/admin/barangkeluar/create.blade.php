@extends('layouts.app') {{-- (Sesuaikan layout lo) --}}

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-upload mr-2"></i> {{ $title }}
</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-body">
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan!</strong> Cek kembali input Anda:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- PENTING: enctype="multipart/form-data" buat upload file --}}
        <form action="{{ route('barangkeluar.store') }}" method="POST" id="bast-form" enctype="multipart/form-data">
            @csrf
            
            <p class="text-muted">Form ini akan mengubah status aset dari <strong>'Stok'</strong> menjadi <strong>'Dipakai'</strong>.</p>
            <hr>

            {{-- BAGIAN 1: PILIH ASET --}}
            <h5 class="font-weight-bold">1. Pilih Aset (Scan/Pilih Kode Aset)</h5>
            <div class="form-group">
                <label>Pilih Aset (Hanya yang berstatus 'Stok')</label>
                <select name="barang_masuk_id" id="aset_select" class="form-control" required>
                    <option value="">-- Pilih Aset (Kode Aset / SN) --</option>
                    @foreach ($asetStok as $aset)
                        <option value="{{ $aset->id }}" {{ old('barang_masuk_id') == $aset->id ? 'selected' : '' }}>
                            {{ $aset->kode_asset }} | {{ $aset->masterBarang->nama_barang }} (SN: {{ $aset->serial_number }})
                        </option>
                    @endforeach
                </select>
                <div id="aset-error" class="text-danger mt-2" style="display: none;"></div>
            </div>
            
            <div id="loading-spinner" style="display: none;" class="text-center my-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>

            {{-- BAGIAN 2: DETAIL ASET (OTOMATIS) --}}
            <div id="detail-aset-container" style="display: none;"> {{-- Sembunyiin awalnya --}}
                <h5 class="font-weight-bold mt-4">2. Detail Aset (Otomatis)</h5>
                <div class="row">
                    <div class="col-md-4"><div class="form-group"><label>No. SJ</label><input type="text" id="no_sj" class="form-control" readonly></div></div>
                    <div class="col-md-4"><div class="form-group"><label>No. PPI</label><input type="text" id="no_ppi" class="form-control" readonly></div></div>
                    <div class="col-md-4"><div class="form-group"><label>No. PO</label><input type="text" id="no_po" class="form-control" readonly></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Kategori</label><input type="text" id="kategori" class="form-control" readonly></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Merk</label><input type="text" id="merk" class="form-control" readonly></div></div>
                    <div class="col-md-4"><div class="form-group"><label>Model</label><input type="text" id="model" class="form-control" readonly></div></div>
                    <div class="col-md-6"><div class="form-group"><label>Serial Number</label><input type="text" id="serial_number" class="form-control" readonly></div></div>
                    <div class="col-md-6"><div class="form-group"><label>Kode Aset</label><input type="text" id="kode_asset" class="form-control" readonly></div></div>
                    <div class="col-md-12"><div class="form-group"><label>Spesifikasi</label><textarea id="spesifikasi" class="form-control" readonly rows="2"></textarea></div></div>
                </div>
            </div>

            {{-- BAGIAN 3: INFORMASI SERAH TERIMA --}}
            <div id="serah-terima-container" style="display: none;"> {{-- Sembunyiin awalnya --}}
                <h5 class="font-weight-bold mt-4">3. Informasi Serah Terima</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Serahkan Kepada (User Pemegang)</label>
                            <select name="user_pemegang_id" class="form-control" required>
                                <option value="">-- Pilih User --</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_pemegang_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tanggal Serah Terima</label>
                            <input type="date" name="tanggal_serah_terima" class="form-control" value="{{ old('tanggal_serah_terima', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Keterangan </label>
                            <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Bukti Foto (Opsional)</label>
                            <input type="file" name="foto_bukti" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>File Pendukung (Opsional)</label>
                            <input type="file" name="file" class="form-control">
                        </div>
                    </div>
                </div>

                {{-- BAGIAN 4: TANDA TANGAN --}}
                <h5 class="font-weight-bold mt-4">4. Tanda Tangan</h5>
                <div class="row">
                    {{-- TTD Penerima --}}
                    <div class="col-md-6">
                        <label>TTD Penerima</label>
                        <div class="canvas-wrapper">
                            <canvas id="ttd-penerima" class="signature-pad" width=400 height=200></canvas>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger clear-pad" data-target="ttd-penerima">Hapus TTD</button>
                        <input type="hidden" name="ttd_penerima" id="input-ttd-penerima">
                    </div>
                    {{-- TTD Petugas --}}
                    <div class="col-md-6">
                        <label>TTD Petugas IT (Anda)</label>
                        <div class="canvas-wrapper">
                            <canvas id="ttd-petugas" class="signature-pad" width=400 height=200></canvas>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger clear-pad" data-target="ttd-petugas">Hapus TTD</button>
                        <input type="hidden" name="ttd_petugas" id="input-ttd-petugas">
                    </div>
                </div>

                <hr>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-check-circle mr-2"></i> Serahkan Aset
                </button>
            </div>

        </form>
    </div>
</div>

<style>
    /* Bikin canvas TTD ada border */
    .canvas-wrapper {
        border: 1px dashed #ccc;
        border-radius: 5px;
        width: 400px; 
        height: 200px;
        margin-bottom: 5px;
    }
    .signature-pad {
        cursor: crosshair;
    }
</style>
@endsection

@push('scripts')
{{-- Library TTD (SignaturePad) --}}
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

{{-- Asumsi lo pake jQuery --}}
<script>
$(document).ready(function() {
    
    // --- SETUP SIGNATURE PAD ---
    const canvasPenerima = document.getElementById('ttd-penerima');
    const signaturePadPenerima = new SignaturePad(canvasPenerima);

    const canvasPetugas = document.getElementById('ttd-petugas');
    const signaturePadPetugas = new SignaturePad(canvasPetugas);

    // Hapus TTD
    $('.clear-pad').click(function() {
        let target = $(this).data('target');
        if (target === 'ttd-penerima') {
            signaturePadPenerima.clear();
        } else if (target === 'ttd-petugas') {
            signaturePadPetugas.clear();
        }
    });

    // --- LOGIKA AJAX (OTOMATIS KE ISI) ---
    $('#aset_select').change(function() {
        let asetId = $(this).val();
        let detailContainer = $('#detail-aset-container');
        let serahTerimaContainer = $('#serah-terima-container');
        let spinner = $('#loading-spinner');
        let errorMsg = $('#aset-error');
        
        // Reset dulu
        detailContainer.hide();
        serahTerimaContainer.hide();
        errorMsg.hide();

        if (asetId === "") {
            return; // Kalo milih "--Pilih--", gak ngapa-ngapain
        }

        spinner.show();

        // Panggil route AJAX
        $.ajax({
            url: "{{ route('barangkeluar.getAssetDetails') }}",
            type: 'GET',
            data: { id: asetId },
            success: function(data) {
                // Isi semua field read-only
                $('#no_sj').val(data.no_sj);
                $('#no_ppi').val(data.no_ppi);
                $('#no_po').val(data.no_po);
                $('#kategori').val(data.kategori);
                $('#merk').val(data.merk);
                $('#model').val(data.model);
                $('#spesifikasi').val(data.spesifikasi);
                $('#serial_number').val(data.serial_number);
                $('#kode_asset').val(data.kode_asset);

                // Tampilkan form
                spinner.hide();
                detailContainer.slideDown();
                serahTerimaContainer.slideDown();
            },
            error: function(xhr) {
                // Kalo error (misal: Aset gak 'Stok' atau gak ketemu)
                let error = xhr.responseJSON.error || "Gagal mengambil data aset.";
                errorMsg.text('Error: ' + error).show();
                spinner.hide();
            }
        });
    });

    // --- LOGIKA SUBMIT FORM (NYIMPEN TTD) ---
    $('#bast-form').submit(function(e) {
        // Cek TTD Penerima
        if (signaturePadPenerima.isEmpty()) {
            alert("Tanda Tangan Penerima tidak boleh kosong!");
            e.preventDefault(); // Stop form submit
            return;
        }
        // Cek TTD Petugas
        if (signaturePadPetugas.isEmpty()) {
            alert("Tanda Tangan Petugas IT tidak boleh kosong!");
            e.preventDefault(); // Stop form submit
            return;
        }

        // Kalo udah diisi, ambil data Base64-nya
        let ttdPenerimaData = signaturePadPenerima.toDataURL('image/png');
        let ttdPetugasData = signaturePadPetugas.toDataURL('image/png');

        // Masukin ke hidden input biar ke-kirim
        $('#input-ttd-penerima').val(ttdPenerimaData);
        $('#input-ttd-petugas').val(ttdPetugasData);
    });

});
</script>
@endpush