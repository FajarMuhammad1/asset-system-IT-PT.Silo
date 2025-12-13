@extends('layouts.app')

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
            
            <p class="text-muted">Form ini digunakan untuk mendaftarkan <strong>satu per satu</strong> aset fisik yang masuk.</p>
            <hr>
            
            {{-- BAGIAN 1: SURAT JALAN --}}
            <h5 class="font-weight-bold">1. Informasi Dokumen (Pilih SJ)</h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Pilih ID Surat Jalan </label>
                        <select name="surat_jalan_id" id="surat_jalan_id" class="form-control" required>
                            <option value="">-- Pilih ID Surat Jalan --</option>
                            @foreach ($daftarSuratJalan as $sj)
                                <option value="{{ $sj->id_sj }}" 
                                        data-id_suratjalan="{{ $sj->id_suratjalan }}"
                                        data-no_sj="{{ $sj->no_sj }}"
                                        data-no_ppi="{{ $sj->no_ppi }}"
                                        data-no_po="{{ $sj->no_po }}"
                                        {{ old('surat_jalan_id') == $sj->id_sj ? 'selected' : '' }}>
                                    <strong>{{ $sj->id_suratjalan }}</strong> (No. SJ: {{ $sj->no_sj }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Detail SJ (Auto Fill) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ID Surat Jalan</label>
                        <input type="text" id="id_suratjalan_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor SJ</label>
                        <input type="text" id="no_sj_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PPI</label>
                        <input type="text" id="no_ppi_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
            </div>

            <hr>
            
            {{-- BAGIAN 2: MASTER BARANG --}}
            <h5 class="font-weight-bold">2. Informasi Aset (Pilih Katalog)</h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Pilih Jenis Barang (dari Katalog)</label>
                        <select name="master_barang_id" id="master_barang_id" class="form-control" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($daftarMasterBarang as $item) 
                                {{-- Pastikan relasi kategori diload atau ambil nama kolomnya --}}
                                <option value="{{ $item->id }}" 
                                        data-kategori="{{ $item->kategori->nama_kategori ?? $item->kategori ?? '-' }}"
                                        data-merk="{{ $item->merk }}"
                                        data-spek="{{ $item->spesifikasi }}"
                                        {{ old('master_barang_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Detail Barang (Auto Fill) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kategori</label>
                        <input type="text" id="kategori_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Merk</label>
                        <input type="text" id="merk_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Spesifikasi</label>
                        <textarea id="spek_auto" class="form-control" readonly rows="2" style="background-color: #e9ecef;"></textarea>
                    </div>
                </div>
            </div>
            
            <hr>
            
            {{-- BAGIAN 3: INPUT FISIK (UPDATE DISINI) --}}
            <h5 class="font-weight-bold text-primary">3. Input Fisik & Kode</h5>
            <div class="row">
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Aset <small class="text-danger">*Auto Generated</small></label>
                        {{-- Input Disabled (Tidak dikirim ke controller, karena controller buat sendiri) --}}
                        <input type="text" class="form-control" 
                               value="[Otomatis] Sesuai Kategori Barang" 
                               disabled 
                               style="background-color: #e9ecef; font-style: italic; cursor: not-allowed; color: #6c757d;">
                        <small class="text-info font-weight-bold mt-1 d-block">
                            <i class="fas fa-info-circle"></i> Info Sistem:
                        </small>
                        <small class="text-muted">
                            - Aset Tetap: Dibuatkan kode unik (Cth: <b>LPT-0001</b>).<br>
                            - Consumable (Tinta/ATK): <b>Tidak ada kode</b>.
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Serial Number (SN) <small class="text-muted">(Opsional)</small></label>
                        {{-- Hapus attribute required --}}
                        <input type="text" name="serial_number" 
                               class="form-control @error('serial_number') is-invalid @enderror" 
                               value="{{ old('serial_number') }}" 
                               placeholder="Kosongkan jika tidak ada SN">
                        
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Isi jika barang memiliki SN pabrik.</small>
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
                        <textarea name="keterangan" class="form-control" rows="1" placeholder="Cth: Barang baru, kondisi segel">{{ old('keterangan') }}</textarea>
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
<script>
$(document).ready(function() {
    
    // --- FUNGSI AUTO FILL SURAT JALAN ---
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

    // Run on Load (untuk old input)
    fillSjData();
    fillMasterData();
});
</script>
@endpush