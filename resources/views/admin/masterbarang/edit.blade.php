@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit mr-2"></i> {{ $title }}
</h1>

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

        <form action="{{ route('master-barang.update', $masterBarang->id) }}" method="POST">
            @csrf
            @method('PUT') {{-- Pake method PUT buat update --}}
            
            <div class="form-group">
                <label>Nama Barang (Contoh: Laptop Latitude 5440)</label>
                <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $masterBarang->nama_barang) }}" required>
            </div>

            <div class="form-group">
                <label>Kategori (Contoh: Laptop, Printer, Mouse)</label>
                <input type="text" name="kategori" class="form-control" value="{{ old('kategori', $masterBarang->kategori) }}" required>
            </div>

            <div class="form-group">
                <label>Merk (Contoh: Dell, HP, Epson)</label>
                <input type="text" name="merk" class="form-control" value="{{ old('merk', $masterBarang->merk) }}" required>
            </div>

            <div class="form-group">
                <label>Spesifikasi (Contoh: i5, 16GB RAM, 512GB SSD)</label>
                <textarea name="spesifikasi" class="form-control" rows="3">{{ old('spesifikasi', $masterBarang->spesifikasi) }}</textarea>
            </div>

            <hr>
            <button type="submit" class="btn btn-primary">Update Data</button>
            <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">Batal</a>
        </form>

    </div>
</div>
@endsection