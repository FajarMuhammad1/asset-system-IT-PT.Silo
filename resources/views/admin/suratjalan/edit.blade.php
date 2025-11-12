@extends('layouts.app')
@section('content')

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit mr-2"></i> {{ $title }}
</h1>

<div class="card shadow-sm">
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

        <form action="{{ route('surat-jalan.update', $suratJalan->id_sj) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h5 class="font-weight-bold">Informasi Dokumen (Header)</h5>
            <p class="text-muted">Catatan: Mengedit hanya akan mengubah data header, bukan list barang.</p>
            <hr>
            <div class="row">
                
                {{-- Tambahan: ID Surat Jalan (dari HO) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ID Surat Jalan  <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="id_suratjalan" 
                               class="form-control @error('id_suratjalan') is-invalid @enderror" 
                               value="{{ old('id_suratjalan', $suratJalan->id_suratjalan) }}" 
                               required>
                        @error('id_suratjalan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor Surat Jalan (SJ)</label>
                        <input type="text" name="no_sj" class="form-control" value="{{ old('no_sj', $suratJalan->no_sj) }}" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PPI</label>
                        <input type="text" name="no_ppi" class="form-control" value="{{ old('no_ppi', $suratJalan->no_ppi) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PO</label>
                        <input type="text" name="no_po" class="form-control" value="{{ old('no_po', $suratJalan->no_po) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Input</label>
                        <input type="date" name="tanggal_input" class="form-control" value="{{ old('tanggal_input', $suratJalan->tanggal_input) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Surat Jalan</label>
                        <input type="text" name="jenis_surat_jalan" class="form-control" value="{{ old('jenis_surat_jalan', $suratJalan->jenis_surat_jalan) }}" required>
                    </div>
                </div>
                
                <div class="col-md-4 d-flex align-items-center">
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="is_bast_submitted" value="1" id="bastCheck"
                            {{-- Cek 'old' dulu, kalo gak ada, baru cek data dari DB --}}
                            {{ old('is_bast_submitted', $suratJalan->is_bast_submitted) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="bastCheck">
                            BAST Sudah Selesai?
                        </label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label>Upload File Baru (Opsional)</label>
                        <input type="file" name="file" class="form-control">
                        @if ($suratJalan->file)
                            <small class="text-success">File saat ini: <a href="{{ asset('storage/' . $suratJalan->file) }}" target="_blank">Lihat</a></small>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Header</button>
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary">Batal</a>
        </form>

        <h5 class="font-weight-bold mt-5">List Barang di Surat Jalan Ini (Read-Only)</h5>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-light">
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Merk</th>
                        <th class="text-center">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suratJalan->details as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->masterBarang->nama_barang }}</td>
                        <td>{{ $detail->masterBarang->kategori }}</td>
                        <td>{{ $detail->masterBarang->merk }}</td>
                        <td class="text-center">{{ $detail->qty }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada barang detail di Surat Jalan ini.</td>
                    </tr>
                    @endForelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection