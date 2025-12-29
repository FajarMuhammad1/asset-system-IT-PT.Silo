@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER & NAVIGASI --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle mr-2"></i> {{ $title ?? 'Input Surat Jalan Baru' }}
        </h1>
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    {{-- ERROR ALERT --}}
    @if ($errors->any())
        <div class="alert alert-danger border-left-danger shadow-sm">
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Perhatian!</strong> Mohon cek inputan berikut:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat-jalan.store') }}" method="POST" enctype="multipart/form-data" id="suratJalanForm">
        @csrf

        {{-- CARD 1: INFORMASI HEADER --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">1. Informasi Dokumen (Header)</h6>
            </div>
            <div class="card-body">
                
                {{-- Baris 1: Identitas Utama --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ID Surat Jalan (Internal) <span class="text-danger">*</span></label>
                            <input type="text" name="id_suratjalan" 
                                   class="form-control @error('id_suratjalan') is-invalid @enderror" 
                                   value="{{ old('id_suratjalan') }}" 
                                   placeholder="Cth: SJ-HO-24001" required>
                            @error('id_suratjalan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nomor Surat Jalan (Fisik) <span class="text-danger">*</span></label>
                            <input type="text" name="no_sj" 
                                   class="form-control @error('no_sj') is-invalid @enderror" 
                                   value="{{ old('no_sj') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Input <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_input" 
                                   class="form-control @error('tanggal_input') is-invalid @enderror" 
                                   value="{{ old('tanggal_input', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- Baris 2: Referensi --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nomor PPI <span class="text-danger">*</span></label>
                            <select name="no_ppi" class="form-control select2-base @error('no_ppi') is-invalid @enderror" required>
                                <option value="">-- Pilih Nomor PPI --</option>
                                @foreach($daftarPpi as $ppi)
                                    <option value="{{ $ppi->no_ppi }}" {{ old('no_ppi') == $ppi->no_ppi ? 'selected' : '' }}>
                                        {{ $ppi->no_ppi }} - {{ $ppi->user->nama ?? 'User' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya PPI 'Disetujui' & 'Selesai' yang tampil.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nomor PO <span class="text-danger">*</span></label>
                            <input type="text" name="no_po" class="form-control" value="{{ old('no_po') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jenis Surat Jalan <span class="text-danger">*</span></label>
                            <input type="text" name="jenis_surat_jalan" class="form-control" 
                                   placeholder="Cth: Mutasi, Service, Proyek"
                                   value="{{ old('jenis_surat_jalan') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Baris 3: Keterangan --}}
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Keterangan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                        </div>
                    </div>
                    {{-- Info Status BAST --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status Serah Terima (BAST)</label>
                            <div class="alert alert-light border border-left-info py-2 small text-muted">
                                <i class="fas fa-info-circle text-info mr-1"></i>
                                Status awal otomatis <strong>PENDING</strong>. Status akan berubah menjadi <strong>SELESAI</strong> setelah barang diproses di menu BAST.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Baris 4: File Upload --}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Upload Scan Dokumen (Opsional)</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('file') is-invalid @enderror" id="customFile" name="file">
                                <label class="custom-file-label" for="customFile">Pilih file PDF/Gambar...</label>
                            </div>
                            @error('file')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- CARD 2: LIST BARANG --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">2. Daftar Barang (Items)</h6>
                <button type="button" id="add-item-row" class="btn btn-sm btn-success shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Baris
                </button>
            </div>
            <div class="card-body bg-light">
                
                <div id="items-container">
                    {{-- ROW ITEM PERTAMA (DEFAULT) --}}
                    <div class="row item-row card-body bg-white border rounded mb-2 shadow-sm p-3">
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold">Nama Barang</label>
                                <select name="items[0][master_barang_id]" class="form-control select2-item" required>
                                    <option value="">-- Cari Barang --</option>
                                    @foreach ($masterBarangList as $item) 
                                        <option value="{{ $item->id }}" {{ old('items.0.master_barang_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->nama_barang }} ({{ $item->merk }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="small font-weight-bold">Qty</label>
                                <input type="number" name="items[0][qty]" class="form-control" min="1" value="1" required>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-block remove-item-row" disabled>
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- ALERT JIKA KOSONG --}}
                <div id="empty-alert" class="d-none text-center p-3">
                    <small class="text-danger font-italic">*Minimal harus ada 1 barang.</small>
                </div>

            </div>
        </div>

        {{-- TOMBOL SUBMIT --}}
        <div class="d-flex justify-content-end mb-5">
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary mr-2">Batal</a>
            <button type="submit" class="btn btn-primary shadow px-4">
                <i class="fas fa-save mr-2"></i> Simpan Data
            </button>
        </div>

    </form>
</div>

{{-- DATA HIDDEN UNTUK JS --}}
<div id="master-barang-data" data-json='@json($masterBarangList)'></div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // 1. Custom File Input Label
    $('.custom-file-input').on('change', function() { 
        let fileName = $(this).val().split('\\').pop(); 
        $(this).next('.custom-file-label').addClass("selected").html(fileName); 
    });

    // 2. Init Select2 untuk PPI (Header)
    $('.select2-base').select2({
        placeholder: '-- Pilih Opsi --',
        allowClear: true,
        width: '100%'
    });

    // ---------------------------------------------------------
    // LOGIC TAMBAH BARANG DINAMIS
    // ---------------------------------------------------------

    // Ambil data Master Barang dari element hidden agar efisien
    let masterBarangList = [];
    try {
        masterBarangList = JSON.parse($('#master-barang-data').attr('data-json'));
    } catch (e) {
        console.error("Gagal parsing data barang", e);
    }

    // Buat string Option sekali saja untuk performance
    let optionsHtml = '<option value="">-- Cari Barang --</option>';
    masterBarangList.forEach(function(item) {
        optionsHtml += `<option value="${item.id}">${item.nama_barang} (${item.merk})</option>`;
    });

    // Init Select2 pada baris pertama (bawaan load)
    $('.select2-item').select2({
        placeholder: '-- Cari Barang --',
        allowClear: true,
        width: '100%'
    });

    // Event Klik Tambah Baris
    $('#add-item-row').click(function() {
        // Hitung index berdasarkan jumlah row saat ini agar unique
        let newIndex = $('.item-row').length;

        let newRow = `
            <div class="row item-row card-body bg-white border rounded mb-2 shadow-sm p-3 fade-in">
                <div class="col-md-6">
                    <div class="form-group mb-0">
                         <label class="small font-weight-bold">Nama Barang</label>
                        <select name="items[${newIndex}][master_barang_id]" class="form-control select2-new" required>
                            ${optionsHtml}
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                         <label class="small font-weight-bold">Qty</label>
                        <input type="number" name="items[${newIndex}][qty]" class="form-control" min="1" value="1" required>
                    </div>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-block remove-item-row">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;

        // Append ke container
        $('#items-container').append(newRow);

        // Init Select2 HANYA pada element yang baru (class .select2-new)
        // Kemudian hapus class marker tersebut agar tidak tumpang tindih nanti
        $('.select2-new').select2({
            placeholder: '-- Cari Barang --',
            allowClear: true,
            width: '100%'
        }).removeClass('select2-new').addClass('select2-item');

        checkRowCount();
    });

    // Event Hapus Baris (Delegation)
    $(document).on('click', '.remove-item-row', function() {
        // Hapus instance select2 dulu untuk mencegah memory leak
        $(this).closest('.item-row').find('select').select2('destroy');
        
        // Hapus element row
        $(this).closest('.item-row').remove();
        
        checkRowCount();
    });

    // Fungsi Cek Jumlah Row (Agar tidak menghapus baris terakhir)
    function checkRowCount() {
        let count = $('.item-row').length;
        if (count <= 1) {
            $('.remove-item-row').prop('disabled', true);
        } else {
            $('.remove-item-row').prop('disabled', false);
        }
    }

    // Jalankan cek awal
    checkRowCount();
});
</script>

<style>
    /* Animasi halus saat tambah row */
    .fade-in {
        animation: fadeIn 0.5s;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
</style>
@endpush