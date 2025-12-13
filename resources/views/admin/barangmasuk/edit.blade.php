@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit mr-2"></i> {{ $title }}
</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-body">
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan!</strong> Cek kembali input Anda.
            </div>
        @endif

        <form action="{{ route('barangmasuk.update', $barangMasuk->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <p class="text-muted">Form ini digunakan untuk mengedit detail aset fisik yang sudah ada.</p>
            <hr>
            
            {{-- BAGIAN 1: SURAT JALAN --}}
            <h5 class="font-weight-bold">1. Informasi Dokumen</h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Pilih ID Surat Jalan</label>
                        <select name="surat_jalan_id" id="surat_jalan_id" class="form-control" required>
                            <option value="">-- Pilih ID Surat Jalan --</option>
                            @foreach ($daftarSuratJalan as $sj)
                                <option value="{{ $sj->id_sj }}" 
                                        data-no_ppi="{{ $sj->no_ppi }}"
                                        data-no_po="{{ $sj->no_po }}"
                                        {{ old('surat_jalan_id', $barangMasuk->surat_jalan_id) == $sj->id_sj ? 'selected' : '' }}>
                                    <strong>{{ $sj->id_suratjalan }}</strong> (No. SJ: {{ $sj->no_sj }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Field Otomatis (Readonly) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nomor PPI (Otomatis)</label>
                        <input type="text" id="no_ppi_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nomor PO (Otomatis)</label>
                        <input type="text" id="no_po_auto" class="form-control" readonly style="background-color: #e9ecef;">
                    </div>
                </div>
            </div>

            <hr>
            
            {{-- BAGIAN 2: MASTER BARANG --}}
            <h5 class="font-weight-bold">2. Informasi Aset (Katalog)</h5>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Pilih Jenis Barang</label>
                        <select name="master_barang_id" id="master_barang_id" class="form-control" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($daftarMasterBarang as $item)
                                <option value="{{ $item->id }}" 
                                        data-kategori="{{ $item->kategori->nama_kategori ?? $item->kategori ?? '-' }}"
                                        data-merk="{{ $item->merk }}"
                                        data-spek="{{ $item->spesifikasi }}"
                                        {{ old('master_barang_id', $barangMasuk->master_barang_id) == $item->id ? 'selected' : '' }}>
                                    {{ $item->nama_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- Detail Otomatis --}}
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
            
            {{-- BAGIAN 3: DETAIL FISIK --}}
            <h5 class="font-weight-bold text-primary">3. Detail Fisik & Status</h5>
            <div class="row">
                
                {{-- KODE ASET (READONLY) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Kode Aset</label>
                        @if($barangMasuk->kode_asset)
                            {{-- Jika ada kode, tampilkan READONLY (dikirim ke controller) --}}
                            <input type="text" name="kode_asset" class="form-control" 
                                   value="{{ $barangMasuk->kode_asset }}" readonly 
                                   style="background-color: #eaecf4; font-weight: bold;">
                            <small class="text-muted"><i class="fas fa-lock"></i> Kode unik tidak dapat diubah.</small>
                        @else
                            {{-- Jika Consumable (Null) --}}
                            <input type="text" class="form-control" value="Barang Habis Pakai (Tanpa Kode)" disabled>
                            {{-- Kirim null value agar validasi controller lolos --}}
                            <input type="hidden" name="kode_asset" value=""> 
                        @endif
                    </div>
                </div>

                {{-- SERIAL NUMBER (OPSIONAL) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Serial Number (SN)</label>
                        <input type="text" name="serial_number" 
                               class="form-control @error('serial_number') is-invalid @enderror" 
                               value="{{ old('serial_number', $barangMasuk->serial_number) }}"
                               placeholder="Kosongkan jika tidak ada">
                        @error('serial_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- TANGGAL MASUK --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal_masuk" class="form-control" 
                               value="{{ old('tanggal_masuk', $barangMasuk->tanggal_masuk) }}" required>
                    </div>
                </div>

                {{-- STATUS ASET (PENTING BUAT EDIT) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Status Saat Ini</label>
                        <select name="status" class="form-control">
                            <option value="Stok" {{ $barangMasuk->status == 'Stok' ? 'selected' : '' }}>Stok (Tersedia)</option>
                            <option value="Dipakai" {{ $barangMasuk->status == 'Dipakai' ? 'selected' : '' }}>Dipakai (User)</option>
                            <option value="Rusak" {{ $barangMasuk->status == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Hilang" {{ $barangMasuk->status == 'Hilang' ? 'selected' : '' }}>Hilang</option>
                        </select>
                    </div>
                </div>

                {{-- PEMEGANG ASET (PENTING BUAT EDIT) --}}
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Dipegang Oleh (User)</label>
                        <select name="user_pemegang_id" class="form-control">
                            <option value="">-- Tidak Ada (Gudang) --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ $barangMasuk->user_pemegang_id == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan jika barang ada di gudang IT.</small>
                    </div>
                </div>

                <div class="col-md-6">
                     <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="1">{{ old('keterangan', $barangMasuk->keterangan) }}</textarea>
                    </div>
                </div>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-save mr-2"></i> Update Data
            </button>
            <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary btn-lg">Batal</a>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // --- FUNGSI AUTO FILL DOKUMEN ---
    function fillSjData() {
        var selectedOption = $('#surat_jalan_id').find('option:selected');
        
        if (selectedOption.val() === "") {
            $('#no_ppi_auto').val('');
            $('#no_po_auto').val('');
        } else {
            $('#no_ppi_auto').val(selectedOption.data('no_ppi'));
            $('#no_po_auto').val(selectedOption.data('no_po'));
        }
    }

    // --- FUNGSI AUTO FILL KATALOG ---
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

    // Event Trigger
    $('#surat_jalan_id').on('change', fillSjData);
    $('#master_barang_id').on('change', fillMasterData);

    // Run on Load (Penting buat Edit agar data lama muncul)
    fillSjData();
    fillMasterData();

});
</script>
@endpush