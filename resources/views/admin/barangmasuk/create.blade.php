@extends('layouts.app') {{-- (Sesuaikan layout lo) --}}

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-download mr-2"></i> {{ $title }}
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

        <form action="{{ route('barangmasuk.store') }}" method="POST">
            @csrf
            
            <p class="text-muted">Form ini digunakan untuk mendaftarkan <strong>satu per satu</strong> aset fisik (serial number) yang masuk.</p>
            <hr>
            
            <h5 class="font-weight-bold">1. Informasi Dokumen (Pilih SJ)</h5>
            <div class="row">
                {{-- DROPDOWN SURAT JALAN --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Pilih ID Surat Jalan </label>
                        <select name="surat_jalan_id" id="surat_jalan_id" class="form-control" required>
                            <option value="">-- Pilih ID Surat Jalan --</option>
                            @foreach ($daftarSuratJalan as $sj)
                                <option value="{{ $sj->id_sj }}" {{-- Value = PK (Integer) --}}
                                        data-id_suratjalan="{{ $sj->id_suratjalan }}"
                                        data-no_sj="{{ $sj->no_sj }}"
                                        data-no_ppi="{{ $sj->no_ppi }}"
                                        data-no_po="{{ $sj->no_po }}"
                                        {{ old('surat_jalan_id') == $sj->id_sj ? 'selected' : '' }}>
                                    {{-- Teks Tampil digabung --}}
                                    <strong>{{ $sj->id_suratjalan }}</strong> (No. SJ: {{ $sj->no_sj }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- TAMPILAN BARU (col-md-4) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ID Surat Jalan</label>
                        <input type="text" id="id_suratjalan_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor SJ (Otomatis)</label>
                        <input type="text" id="no_sj_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PPI (Otomatis)</label>
                        <input type="text" id="no_ppi_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PO (Otomatis)</label>
                        <input type="text" id="no_po_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                {{-- --------------------------------------- --}}
            </div>

            <hr>
            <h5 class="font-weight-bold">2. Informasi Aset (Pilih Katalog)</h5>
            <div class="row">
                {{-- DROPDOWN "OTOMATIS KE ISI" --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Pilih Jenis Barang (dari Katalog)</label>
                        {{-- INI YANG DIGANTI: $daftarMasterBarang --}}
                        <select name="master_barang_id" id="master_barang_id" class="form-control" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($daftarMasterBarang as $item) 
                                <option value="{{ $item->id }}" 
                                        data-kategori="{{ $item->kategori }}"
                                        data-merk="{{ $item->merk }}"
                                        data-spek="{{ $item->spesifikasi }}"
                                        {{ old('master_barang_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- FIELD OTOMATIS KEISI (READ-ONLY) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kategori (Otomatis)</label>
                        <input type="text" id="kategori_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Merk (Otomatis)</label>
                        <input type="text" id="merk_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Spesifikasi (Otomatis)</label>
                        <textarea id="spek_auto" class="form-control" readonly rows="2" style="background-color: #e9ecef;"></textarea>
                    </div>
                </div>
            </div>
            
            <hr>
            <h5 class="font-weight-bold text-primary">3. Input Fisik (Wajib Manual)</h5>
            <div class="row">
                {{-- INPUT MANUAL FISIK --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Serial Number (SN)</label>
                        <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}" required>
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Aset (Tempel Stiker)</label>
                        <input type="text" name="kode_asset" class="form-control @error('kode_asset') is-invalid @enderror" value="{{ old('kode_asset') }}" required>
                        @error('kode_asset')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Masuk (Aset Diterima)</label>
                        <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                     <div class="form-group">
                        <label>Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="1">{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save mr-2"></i> Simpan Aset Ini
            </button>
            <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary btn-lg">Kembali ke List Aset</a>
        </form>

    </div>
</div>
@endsection

@push('scripts')
{{-- Asumsi lo pake jQuery --}}
<script>
$(document).ready(function() {
    
    // --- FUNGSI UNTUK OTOMATIS KEISI ID HO, NO SJ, PPI, & PO ---
    function fillSjData() {
        var selectedOption = $('#surat_jalan_id').find('option:selected');
        
        if (selectedOption.val() === "") {
            $('#id_suratjalan_auto').val('');
            $('#no_sj_auto').val('');
            $('#no_ppi_auto').val('');
            $('#no_po_auto').val('');
        } else {
            $('#id_suratjalan_auto').val(selectedOption.data('id_suratjalan'));
            $('#no_sj_auto').val(selectedOption.data('no_sj'));
            $('#no_ppi_auto').val(selectedOption.data('no_ppi'));
            $('#no_po_auto').val(selectedOption.data('no_po'));
        }
    }

    // --- FUNGSI UNTUK OTOMATIS KEISI KATALOG ---
    function fillMasterData() {
        var selectedOption = $('#master_barang_id').find('option:selected');
        
        if (selectedOption.val() === "") {
            $('#kategori_auto').val('');
            $('#merk_auto').val('');
            $('#spek_auto').val('');
        } else {
            $('#kategori_auto').val(selectedOption.data('kategori'));
            $('#merk_auto').val(selectedOption.data('merk'));
            $('#spek_auto').val(selectedOption.data('spek'));
        }
    }

    // Panggil fungsi pas dropdown-nya ganti
    $('#surat_jalan_id').on('change', fillSjData);
    $('#master_barang_id').on('change', fillMasterData);

    // Panggil fungsi pas halaman baru di-load (buat nanganin 'old()' value)
    fillSjData();
    fillMasterData();

});
</script>
@endpush