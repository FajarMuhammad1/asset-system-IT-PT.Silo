@extends('layouts.app') {{-- (Sesuaikan nama layout lo) --}}

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-plus mr-2"></i> {{ $title }}
</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan! Silakan cek kembali input Anda:</strong>
            </div>
        @endif

        <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h5 class="font-weight-bold">Informasi Dokumen (Header)</h5>
            <hr>
            
            <div class="row">
                
                {{-- 1. Input ID SURAT JALAN (DARI HO) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ID Surat Jalan  <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="id_suratjalan" 
                               class="form-control @error('id_suratjalan') is-invalid @enderror" 
                               value="{{ old('id_suratjalan') }}" 
                               required>
                        @error('id_suratjalan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- 2. Input Nomor Surat Jalan (SJ) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor Surat Jalan (SJ)</label>
                        <input type="text" 
                               name="no_sj" 
                               class="form-control @error('no_sj') is-invalid @enderror" 
                               value="{{ old('no_sj') }}" 
                               required>
                        @error('no_sj')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- 3. Input Nomor PPI --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PPI</label>
                        <input type="text" 
                               name="no_ppi" 
                               class="form-control @error('no_ppi') is-invalid @enderror" 
                               value="{{ old('no_ppi') }}" 
                               required>
                        @error('no_ppi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- 4. Input Nomor PO --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PO</label>
                        <input type="text" 
                               name="no_po" 
                               class="form-control @error('no_po') is-invalid @enderror" 
                               value="{{ old('no_po') }}" 
                               required>
                        @error('no_po')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- 5. Input Tanggal Input --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Input</label>
                        <input type="date" 
                               name="tanggal_input" 
                               class="form-control @error('tanggal_input') is-invalid @enderror" 
                               value="{{ old('tanggal_input', date('Y-m-d')) }}" 
                               required>
                        @error('tanggal_input')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- 6. Input Jenis Surat Jalan --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Surat Jalan</label>
                        <input type="text" 
                               name="jenis_surat_jalan" 
                               class="form-control @error('jenis_surat_jalan') is-invalid @enderror" 
                               value="{{ old('jenis_surat_jalan') }}" 
                               required>
                        @error('jenis_surat_jalan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- 7. Input Keterangan --}}
                 <div class="col-md-8">
                    <div class="form-group">
                        <label>Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2">{{ old('keterangan') }}</textarea>
                         @error('keterangan')
                             <div class="invalid-feedback">{{ $message }}</div>
                         @enderror
                    </div>
                </div>

                {{-- 8. Checkbox BAST --}}
                <div class="col-md-4 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="is_bast_submitted" 
                               value="1" 
                               id="bastCheck" 
                               {{ old('is_bast_submitted') ? 'checked' : '' }}>
                        <label class="form-check-label" for="bastCheck">
                            BAST Sudah Selesai?
                        </label>
                    </div>
                </div>

                {{-- 9. Upload Scan File --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Upload Scan File (Opsional)</label>
                        <input type="file" 
                               name="file" 
                               class="form-control @error('file') is-invalid @enderror">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div> {{-- Akhir Row Header --}}

            <h5 class="font-weight-bold mt-4">List Barang (Detail)</h5>
            <hr>
            
            <div id="items-container">
                <div class="row item-row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Pilih Barang (dari Katalog)</label>
                            <select name="items[0][master_barang_id]" class="form-control select2-master-barang" required>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($masterBarangList as $item) 
                                    <option value="{{ $item->id }}" {{ old('items.0.master_barang_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_barang }} ({{ $item->merk }})</option>
                                @endforeach
                            </select>
                            @error('items.0.master_barang_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Qty</label>
                            <input type="number" 
                                   name="items[0][qty]" 
                                   class="form-control @error('items.0.qty') is-invalid @enderror" 
                                   min="1" 
                                   required 
                                   value="{{ old('items.0.qty') }}">
                             @error('items.0.qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-block remove-item-row" disabled>Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- PERBAIKAN 1: Tambahkan data attribute untuk membawa data barang ke JS --}}
            <button type="button" id="add-item-row" class="btn btn-success mt-2" 
                    data-master-barang='@json($masterBarangList)'>
                <i class="fas fa-plus mr-1"></i> Tambah Baris Barang
            </button>
            
            <hr>
            <button type="submit" class="btn btn-primary btn-lg">Simpan Surat Jalan</button>
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary btn-lg">Batal</a>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
   
    const masterBarangElement = $('#add-item-row');
    let masterBarangList = [];
    try {
        // Parsing data JSON dari data attribute
        masterBarangList = JSON.parse(masterBarangElement.attr('data-master-barang'));
    } catch (e) {
        console.error("Error parsing master barang list from data attribute:", e);
    }
    
    // 2. INISIALISASI SELECT2 PADA BARIS PERTAMA
    $('.select2-master-barang').select2({
        placeholder: '-- Pilih Barang --',
        allowClear: true,
        dropdownAutoWidth: true,
        width: '100%'
    });

    let masterBarangOptions = '<option value="">-- Pilih Barang --</option>';
    masterBarangList.forEach(function(item) {
        masterBarangOptions += `<option value="${item.id}">${item.nama_barang} (${item.merk})</option>`;
    });
    
    // Logic untuk tambah baris
    $('#add-item-row').click(function() {
        let currentRowIndex = $('#items-container .item-row').length;

        let newRowHtml = `
        <div class="row item-row mt-2">
            <div class="col-md-6">
                <div class="form-group">
                    <select name="items[${currentRowIndex}][master_barang_id]" class="form-control select2-master-barang-new" required>
                        ${masterBarangOptions}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <input type="number" name="items[${currentRowIndex}][qty]" class="form-control" min="1" required placeholder="Qty">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-block remove-item-row">Hapus</button>
                </div>
            </div>
        </div>
        `;
        $('#items-container').append(newRowHtml);
        
        // INISIALISASI SELECT2 PADA BARIS YANG BARU DITAMBAHKAN
        $(`.select2-master-barang-new`).last().select2({
            placeholder: '-- Pilih Barang --',
            allowClear: true,
            dropdownAutoWidth: true,
            width: '100%'
        }).removeClass('select2-master-barang-new'); // Hapus class sementara untuk mencegah inisialisasi ganda

        updateRemoveButtons(); 
    });

    // Logic untuk hapus baris
    $(document).on('click', '.remove-item-row', function() {
        // Hapus juga instance Select2 sebelum elemen dihapus
        const selectElement = $(this).closest('.item-row').find('.select2-master-barang');
        if (selectElement.data('select2')) {
            selectElement.select2('destroy');
        }
        
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            updateRemoveButtons(); 
        }
    });

    // Update button "Hapus" di baris pertama
    function updateRemoveButtons() {
        if ($('.item-row').length === 1) {
            $('.remove-item-row').prop('disabled', true);
        } else {
            $('.remove-item-row').prop('disabled', false);
        }
    }
    
    updateRemoveButtons();
});
</script>
@endpush