@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-plus mr-2"></i> {{ $title }}
</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan! Silakan cek kembali input Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <h5 class="font-weight-bold text-primary">Informasi Dokumen (Header)</h5>
            <hr>
            
            <div class="row">
                
                {{-- 1. Input ID SURAT JALAN (DARI HO) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ID Surat Jalan <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="id_suratjalan" 
                               class="form-control @error('id_suratjalan') is-invalid @enderror" 
                               value="{{ old('id_suratjalan') }}" 
                               placeholder="Contoh: SJ-HO-001"
                               required>
                        @error('id_suratjalan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- 2. Input Nomor Surat Jalan (SJ) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor Surat Jalan (SJ) <span class="text-danger">*</span></label>
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
                
                {{-- 3. Input Nomor PPI (UPDATE: JADI DROPDOWN) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PPI <span class="text-danger">*</span></label>
                        <select name="no_ppi" class="form-control select2-ppi @error('no_ppi') is-invalid @enderror" required>
                            <option value="">-- Pilih Nomor PPI --</option>
                            @foreach($daftarPpi as $ppi)
                                <option value="{{ $ppi->no_ppi }}" {{ old('no_ppi') == $ppi->no_ppi ? 'selected' : '' }}>
                                    {{ $ppi->no_ppi }} - {{ $ppi->user->name ?? 'User' }} ({{Str::limit($ppi->perangkat, 20)}})
                                </option>
                            @endforeach
                        </select>
                        @error('no_ppi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Hanya PPI status 'Disetujui' & 'Selesai' yang muncul.</small>
                    </div>
                </div>
                
                {{-- 4. Input Nomor PO --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PO <span class="text-danger">*</span></label>
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
                        <label>Tanggal Input <span class="text-danger">*</span></label>
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
                        <label>Jenis Surat Jalan <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="jenis_surat_jalan" 
                               class="form-control @error('jenis_surat_jalan') is-invalid @enderror" 
                               value="{{ old('jenis_surat_jalan') }}" 
                               placeholder="Contoh: Service, Mutasi, dll"
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
                        <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
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
                        <label class="form-check-label font-weight-bold text-success" for="bastCheck">
                            <i class="fas fa-check-circle mr-1"></i> BAST Sudah Selesai?
                        </label>
                    </div>
                </div>

                {{-- 9. Upload Scan File --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Upload Scan File (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input @error('file') is-invalid @enderror" id="customFile" name="file">
                            <label class="custom-file-label" for="customFile">Pilih file PDF/Gambar...</label>
                        </div>
                        @error('file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div> {{-- Akhir Row Header --}}

            <h5 class="font-weight-bold mt-4 text-primary">List Barang (Detail)</h5>
            <hr>
            
            <div id="items-container">
                <div class="row item-row bg-light p-3 rounded mb-2 border">
                    <div class="col-md-6">
                        <div class="form-group mb-0">
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
                        <div class="form-group mb-0">
                            <label>Qty</label>
                            <input type="number" 
                                   name="items[0][qty]" 
                                   class="form-control @error('items.0.qty') is-invalid @enderror" 
                                   min="1" 
                                   required 
                                   value="{{ old('items.0.qty', 1) }}">
                             @error('items.0.qty')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-block remove-item-row" disabled>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <button type="button" id="add-item-row" class="btn btn-success mt-2 shadow-sm" 
                    data-master-barang='@json($masterBarangList)'>
                <i class="fas fa-plus mr-1"></i> Tambah Baris Barang
            </button>
            
            <hr class="mt-5">
            <div class="d-flex justify-content-end">
                <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary btn-lg mr-2">Batal</a>
                <button type="submit" class="btn btn-primary btn-lg shadow">
                    <i class="fas fa-save mr-2"></i> Simpan Surat Jalan
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
{{-- Pastikan script Select2 sudah diload di layout utama --}}
<script>
$(document).ready(function() {
    
    // 0. Custom File Input Label change
    $('.custom-file-input').on('change', function() { 
        let fileName = $(this).val().split('\\').pop(); 
        $(this).next('.custom-file-label').addClass("selected").html(fileName); 
    });

    // 1. INISIALISASI SELECT2 UNTUK PPI (HEADER)
    $('.select2-ppi').select2({
        placeholder: '-- Pilih Nomor PPI --',
        allowClear: true,
        width: '100%'
    });

    // Persiapan Data Barang untuk Row Baru
    const masterBarangElement = $('#add-item-row');
    let masterBarangList = [];
    try {
        masterBarangList = JSON.parse(masterBarangElement.attr('data-master-barang'));
    } catch (e) {
        console.error("Error parsing master barang list:", e);
    }
    
    // 2. INISIALISASI SELECT2 PADA BARIS PERTAMA
    $('.select2-master-barang').select2({
        placeholder: '-- Pilih Barang --',
        allowClear: true,
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
        <div class="row item-row bg-light p-3 rounded mb-2 border mt-2">
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <select name="items[${currentRowIndex}][master_barang_id]" class="form-control select2-master-barang-new" required>
                        ${masterBarangOptions}
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group mb-0">
                    <input type="number" name="items[${currentRowIndex}][qty]" class="form-control" min="1" required placeholder="Qty" value="1">
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-center">
                <button type="button" class="btn btn-danger btn-block remove-item-row">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        `;
        $('#items-container').append(newRowHtml);
        
        // INISIALISASI SELECT2 PADA BARIS BARU
        $(`.select2-master-barang-new`).last().select2({
            placeholder: '-- Pilih Barang --',
            allowClear: true,
            width: '100%'
        }).removeClass('select2-master-barang-new'); 

        updateRemoveButtons(); 
    });

    // Logic untuk hapus baris
    $(document).on('click', '.remove-item-row', function() {
        const selectElement = $(this).closest('.item-row').find('select');
        // Hapus instance Select2 biar bersih
        if (selectElement.data('select2')) {
            selectElement.select2('destroy');
        }
        
        if ($('.item-row').length > 1) {
            $(this).closest('.item-row').remove();
            updateRemoveButtons(); 
        }
    });

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