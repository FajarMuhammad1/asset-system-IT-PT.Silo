@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle mr-2"></i> {{ $title ?? 'Input Barang Masuk' }}
        </h1>
        <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger shadow-sm">
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Perhatian!</strong> Silakan cek inputan Anda:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">Formulir Penerimaan Aset</h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('barangmasuk.store') }}" method="POST">
                @csrf
                
                {{-- INFO HEADER --}}
                <div class="alert alert-info shadow-sm mb-4">
                    <i class="fas fa-info-circle mr-1"></i> Form ini digunakan untuk mendaftarkan <strong>satu per satu</strong> aset fisik (unik) yang masuk ke gudang.
                </div>

                {{-- ================================================= --}}
                {{-- BAGIAN 1: SURAT JALAN                             --}}
                {{-- ================================================= --}}
                <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3">1. Dokumen Sumber (Surat Jalan)</h5>
                
                <div class="form-group">
                    <label class="font-weight-bold">Pilih ID Surat Jalan <span class="text-danger">*</span></label>
                    <select name="surat_jalan_id" id="surat_jalan_id" class="form-control form-control-lg border-primary" required>
                        <option value="">-- Pilih ID Surat Jalan --</option>
                        @foreach ($daftarSuratJalan as $sj)
                            <option value="{{ $sj->id_sj }}" 
                                    data-id_suratjalan="{{ $sj->id_suratjalan }}"
                                    data-no_sj="{{ $sj->no_sj }}"
                                    data-no_ppi="{{ $sj->no_ppi }}"
                                    data-no_po="{{ $sj->no_po }}"
                                    {{ old('surat_jalan_id') == $sj->id_sj ? 'selected' : '' }}>
                                    {{ $sj->id_suratjalan }} | No. SJ: {{ $sj->no_sj }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Panel Detail SJ (Read Only) --}}
                <div class="p-3 mb-4 rounded" style="background-color: #f8f9fc; border: 1px dashed #d1d3e2;">
                    <label class="small text-muted font-weight-bold text-uppercase mb-2">Detail Dokumen (Terisi Otomatis)</label>
                    <div class="form-row">
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <label class="small mb-1">ID Sistem</label>
                            <input type="text" id="id_suratjalan_auto" class="form-control form-control-sm bg-white" readonly>
                        </div>
                        <div class="col-12 col-md-4 mb-2 mb-md-0">
                            <label class="small mb-1">Nomor SJ</label>
                            <input type="text" id="no_sj_auto" class="form-control form-control-sm bg-white" readonly>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="small mb-1">Nomor PPI</label>
                            <input type="text" id="no_ppi_auto" class="form-control form-control-sm bg-white" readonly>
                        </div>
                    </div>
                </div>

                {{-- ================================================= --}}
                {{-- BAGIAN 2: MASTER BARANG                           --}}
                {{-- ================================================= --}}
                <h5 class="font-weight-bold text-gray-800 border-bottom pb-2 mb-3 mt-4">2. Katalog Barang</h5>

                <div class="form-group">
                    <label class="font-weight-bold">Pilih Barang dari Katalog <span class="text-danger">*</span></label>
                    <select name="master_barang_id" id="master_barang_id" class="form-control form-control-lg border-primary" required>
                        <option value="">-- Pilih Nama Barang --</option>
                        @foreach ($daftarMasterBarang as $item) 
                            <option value="{{ $item->id }}" 
                                    data-kategori="{{ $item->kategori->nama_kategori ?? $item->kategori ?? '-' }}"
                                    data-merk="{{ $item->merk }}"
                                    data-spek="{{ $item->spesifikasi }}"
                                    {{ old('master_barang_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->nama_barang }} ({{ $item->merk }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Panel Detail Barang (Read Only) --}}
                <div class="p-3 mb-4 rounded" style="background-color: #f8f9fc; border: 1px dashed #d1d3e2;">
                    <label class="small text-muted font-weight-bold text-uppercase mb-2">Spesifikasi Barang (Terisi Otomatis)</label>
                    <div class="form-row">
                        <div class="col-12 col-md-6 mb-2">
                            <label class="small mb-1">Kategori</label>
                            <input type="text" id="kategori_auto" class="form-control form-control-sm bg-white" readonly>
                        </div>
                        <div class="col-12 col-md-6 mb-2">
                            <label class="small mb-1">Merk / Brand</label>
                            <input type="text" id="merk_auto" class="form-control form-control-sm bg-white" readonly>
                        </div>
                        <div class="col-12">
                            <label class="small mb-1">Spesifikasi Lengkap</label>
                            <textarea id="spek_auto" class="form-control form-control-sm bg-white" readonly rows="2"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ================================================= --}}
                {{-- BAGIAN 3: INPUT FISIK (YANG DIINPUT USER)         --}}
                {{-- ================================================= --}}
                <h5 class="font-weight-bold text-primary border-bottom pb-2 mb-3 mt-4">3. Identitas Fisik Aset</h5>

                <div class="row">
                    {{-- Kode Aset Info --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label>Kode Aset (System Generated)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-light"><i class="fas fa-barcode"></i></span>
                                </div>
                                <input type="text" class="form-control bg-light" value="Otomatis by System" disabled style="cursor: not-allowed;">
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle text-info"></i> Kode akan digenerate otomatis saat disimpan.
                            </small>
                        </div>
                    </div>

                    {{-- Serial Number --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label>Serial Number (SN)</label>
                            <input type="text" name="serial_number" 
                                   class="form-control @error('serial_number') is-invalid @enderror" 
                                   value="{{ old('serial_number') }}" 
                                   placeholder="Masukkan SN pabrik (jika ada)">
                            @error('serial_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Kosongkan jika barang tidak memiliki SN.</small>
                        </div>
                    </div>

                    {{-- Tanggal Masuk --}}
                    <div class="col-12 col-md-6">
                        <div class="form-group">
                            <label>Tanggal Terima <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="col-12 col-md-6">
                         <div class="form-group">
                            <label>Keterangan / Kondisi Awal</label>
                            <textarea name="keterangan" class="form-control" rows="1" placeholder="Cth: Barang baru, segel utuh">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="mt-4">
                
                {{-- TOMBOL AKSI --}}
                <div class="d-flex justify-content-end">
                    <button type="reset" class="btn btn-secondary mr-2">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-success btn-lg shadow-sm">
                        <i class="fas fa-save mr-2"></i> Simpan Data Aset
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // --- FUNGSI AUTO FILL SURAT JALAN ---
    function fillSjData() {
        var selectedOption = $('#surat_jalan_id').find('option:selected');
        
        if (selectedOption.val() === "") {
            $('#id_suratjalan_auto').val('');
            $('#no_sj_auto').val('');
            $('#no_ppi_auto').val('');
        } else {
            $('#id_suratjalan_auto').val(selectedOption.data('id_suratjalan'));
            $('#no_sj_auto').val(selectedOption.data('no_sj'));
            $('#no_ppi_auto').val(selectedOption.data('no_ppi'));
        }
    }

    // --- FUNGSI AUTO FILL MASTER BARANG ---
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

    // Event Listeners
    $('#surat_jalan_id').on('change', fillSjData);
    $('#master_barang_id').on('change', fillMasterData);

    // Run on Load (untuk jika ada error validasi dan kembali ke halaman ini)
    if($('#surat_jalan_id').val() !== "") fillSjData();
    if($('#master_barang_id').val() !== "") fillMasterData();
});
</script>
@endpush