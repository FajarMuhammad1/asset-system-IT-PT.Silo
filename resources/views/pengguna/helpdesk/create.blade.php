@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Buat Tiket Laporan</h1>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-danger">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-bug mr-1"></i> Form Laporan Kendala (Helpdesk)
            </h6>
        </div>
        <div class="card-body">
            
            <form action="{{ route('pengguna.helpdesk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    
                    {{-- KOLOM KIRI: INPUT UTAMA --}}
                    <div class="col-lg-8">

                        {{-- JUDUL MASALAH --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Judul Masalah <span class="text-danger">*</span></label>
                            <input type="text" name="judul_masalah" class="form-control form-control-lg"
                                   placeholder="Contoh: Internet Mati / Printer Macet" 
                                   value="{{ old('judul_masalah') }}" required>
                            <small class="text-muted">Tulis judul singkat mengenai kendala yang dialami.</small>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="form-group">
                            <label class="font-weight-bold text-dark">Deskripsi Lengkap <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" rows="6" required
                                placeholder="Ceritakan detail masalahnya, kapan terjadi, dan apa yang sudah dicoba...">{{ old('deskripsi') }}</textarea>
                        </div>

                    </div>

                    {{-- KOLOM KANAN: UPLOAD & INFO --}}
                    <div class="col-lg-4">

                        {{-- CARD UPLOAD FOTO --}}
                        <div class="card bg-light mb-3 border-0">
                            <div class="card-body">
                                <label class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-camera mr-1"></i> Bukti Foto (Opsional)
                                </label>
                                
                                <div class="custom-file">
                                    <input type="file" name="foto_masalah" class="custom-file-input" id="customFile">
                                    <label class="custom-file-label" for="customFile">Pilih foto...</label>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    Format: JPG, PNG. Maksimal 2MB.<br>
                                    Foto error/screen capture sangat membantu teknisi.
                                </small>
                            </div>
                        </div>

                        {{-- ALERT INFO --}}
                        <div class="alert alert-info shadow-sm">
                            <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Tips:</h6>
                            <small>
                                Jelaskan masalah sedetail mungkin agar teknisi dapat membawa peralatan yang sesuai sebelum datang ke lokasi.
                            </small>
                        </div>

                    </div>
                </div>

                <hr class="my-4">

                {{-- TOMBOL AKSI --}}
                <div class="row">
                    <div class="col-12 col-md-6 mb-2 mb-md-0">
                        <a href="{{ route('pengguna.helpdesk.index') }}" class="btn btn-secondary btn-block btn-lg">
                            <i class="fas fa-arrow-left"></i> Batal
                        </a>
                    </div>
                    <div class="col-12 col-md-6">
                        <button type="submit" class="btn btn-danger btn-block btn-lg shadow-sm">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim Laporan
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

{{-- SCRIPT AGAR NAMA FILE MUNCUL SETELAH DIPILIH --}}
@push('scripts')
<script>
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });
</script>
@endpush