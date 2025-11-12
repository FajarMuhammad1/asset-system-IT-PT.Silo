@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-plus-square mr-2"></i> {{ $title }}
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

        <form action="{{ route('master-barang.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Nama Barang (Contoh: Laptop Latitude 5440)</label>
                <input type="text" 
                       name="nama_barang" 
                       class="form-control @error('nama_barang') is-invalid @enderror" 
                       value="{{ old('nama_barang') }}" 
                       required>
                @error('nama_barang')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- START: DROPDOWN KATEGORI DENGAN CLASS UNTUK SELECT2 --}}
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" 
                        class="form-control select2-kategori @error('kategori') is-invalid @enderror" 
                        required>
                    <option value="">-- Pilih Kategori --</option>
                    
                    @php
                        $categories = [
                            'access point', 'analogue telephone', 'antena radio rig', 
                            'camera', 'consumable', 'cctv', 'dongle', 'extender', 
                            'fingerprint', 'gps', 'headset', 'hardisk eksternal', 
                            'ip telephone', 'keyboard', 'laptop', 'modem', 
                            'monitor', 'mouse', 'mobile phone', 'pc desktop', 
                            'power supply', 'proyektor', 'print server', 'printer', 
                            'radio ht', 'radio rig', 'router', 'scanner', 
                            'ssd eksternal', 'stavolt', 'switch hub', 'ups', 
                            'tv', 'webcam',
                        ];
                    @endphp

                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ old('kategori') == $category ? 'selected' : '' }}>
                            {{ ucwords($category) }}
                        </option>
                    @endforeach
                </select>
                @error('kategori')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- END: DROPDOWN KATEGORI DENGAN CLASS UNTUK SELECT2 --}}

            <div class="form-group">
                <label>Merk (Contoh: Dell, HP, Epson)</label>
                <input type="text" 
                       name="merk" 
                       class="form-control @error('merk') is-invalid @enderror" 
                       value="{{ old('merk') }}" 
                       required>
                @error('merk')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Spesifikasi (Contoh: i5, 16GB RAM, 512GB SSD)</label>
                <textarea name="spesifikasi" 
                          class="form-control @error('spesifikasi') is-invalid @enderror" 
                          rows="3">{{ old('spesifikasi') }}</textarea>
                @error('spesifikasi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <hr>
            <button type="submit" class="btn btn-primary">Simpan ke Katalog</button>
            <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">Batal</a>
        </form>

    </div>
</div>
@endsection

{{-- PUSH SCRIPT UNTUK INISIALISASI SELECT2 --}}
@push('scripts')
{{-- PASTIKAN ANDA SUDAH MEMASANG LIBRARY SELECT2 DI LAYOUTS/APP.BLADE.PHP --}}
{{-- (CSS di <head> dan JS di bawah jQuery) --}}
<script>
    $(document).ready(function() {
        // Mengaktifkan Select2 pada elemen Kategori
        $('.select2-kategori').select2({
            placeholder: 'Ketik untuk mencari kategori...',
            allowClear: true // Opsi agar bisa menghapus pilihan
        });
    });
</script>
@endpush