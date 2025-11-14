@extends('layouts.app') {{-- Pastikan nama layout lo bener --}}

@section('title', $title) {{-- Nangkep $title dari controller --}}

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Isi Detail Kebutuhan / Kerusakan</h6>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Error!</strong> Cek lagi inputan lo:
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            {{-- PENTING: enctype="multipart/form-data" buat upload file --}}
            <form action="{{ route('pengguna.ppi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <!-- Kolom Kiri: Info Dasar (Otomatis) -->
                    <div class="col-md-6">
                        
                        <!-- Tanggal -->
                        <div class="form-group">
                            <label class="font-weight-bold">Tanggal Request</label>
                            <input type="text" class="form-control bg-light" value="{{ date('d-m-Y') }}" readonly>
                        </div>

                        <!-- No PPI -->
                        <div class="form-group">
                            <label class="font-weight-bold">No. PPI</label>
                            <input type="text" class="form-control bg-light" value="OTOMATIS (Setelah Disimpan)" readonly>
                        </div>

                        <!-- Pemohon -->
                        <div class="form-group">
                            <label class="font-weight-bold">Pemohon (Otomatis)</label>
                            <input type="text" class="form-control bg-light" value="{{ Auth::user()->name }}" readonly>
                        </div>

                    </div>

                    <!-- Kolom Kanan: Inputan User -->
                    <div class="col-md-6">
                        
                        <!-- Perangkat -->
                        <div class="form-group">
                            <label class="font-weight-bold">Perangkat yang diminta / Rusak <span class="text-danger">*</span></label>
                            <input type="text" name="perangkat" class="form-control" placeholder="Misal: Printer Epson L360 / Laptop Dell Latitude" value="{{ old('perangkat') }}" required>
                        </div>

                        <!-- BA Kerusakan -->
                        <div class="form-group">
                            <label class="font-weight-bold">BA Kerusakan / Masalah <span class="text-danger">*</span></label>
                            <textarea name="ba_kerusakan" class="form-control" rows="3" placeholder="Jelaskan detail kerusakan (Tinta macet, Layar Bluescreen, dll)" required>{{ old('ba_kerusakan') }}</textarea>
                        </div>

                        <!-- Keterangan -->
                        <div class="form-group">
                            <label class="font-weight-bold">Keterangan Tambahan (Opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
                        </div>

                        <!-- File Upload -->
                        <div class="form-group">
                            <label class="font-weight-bold">Upload File Pendukung (Foto/Dokumen)</label>
                            <input type="file" name="file_ppi" class="form-control-file">
                            <small class="text-muted">Format: JPG, PNG, PDF. Max 2MB.</small>
                        </div>

                    </div>
                </div>

                <hr>
                <div class="text-right">
                    <a href="{{ route('pengguna.dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan & Ajukan PPI
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>
@endsection
```
