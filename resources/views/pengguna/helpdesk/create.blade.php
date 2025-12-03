@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Tiket Laporan</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('pengguna.helpdesk.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-8">

                        {{-- JUDUL MASALAH --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Judul Masalah <span class="text-danger">*</span></label>
                            <input type="text" name="judul_masalah" class="form-control"
                                   placeholder="Contoh: Printer tidak bisa ngeprint / WiFi lantai 2 mati" required>
                        </div>

                        {{-- DESKRIPSI --}}
                        <div class="form-group">
                            <label class="font-weight-bold">Deskripsi Lengkap <span class="text-danger">*</span></label>
                            <textarea name="deskripsi" class="form-control" rows="5" required
                                placeholder="Jelaskan kronologi atau detail error yang terjadi..."></textarea>
                        </div>

                    </div>

                    <div class="col-md-4">

                        {{-- FOTO MASALAH --}}
                        <div class="card bg-light mb-3">
                            <div class="card-body">
                                <div class="form-group">
                                    <label class="font-weight-bold">Bukti Foto (Opsional)</label>
                                    <input type="file" name="foto_masalah" class="form-control-file">
                                    <small class="text-muted d-block mt-1">Format: JPG, PNG. Max: 2MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Pastikan deskripsi jelas agar teknisi bisa cepat menangani.
                        </div>

                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('pengguna.helpdesk.index') }}" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-paper-plane mr-1"></i> Kirim Laporan
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
