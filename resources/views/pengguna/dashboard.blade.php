@extends('layouts.app') {{-- Pastikan ini sesuai nama file layout utama lo --}}

{{-- 1. JUDUL TAB BROWSER (Ngambil dari Controller) --}}
@section('title', $title)

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        {{-- 2. JUDUL HALAMAN (Ngambil dari Controller) --}}
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    </div>

    <div class="row">

        <div class="col-lg-12 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Halo, {{ Auth::user()->nama }}! 👋</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        {{-- Gambar ilustrasi (opsional) --}}
                        
                    </div>
                    <p>Selamat datang di <strong>{{ $title }}</strong>. Silakan gunakan menu di samping untuk mengajukan permintaan perbaikan.</p>
                    
                    <hr>
                    
                    <a href="{{ route('pengguna.ppi.create') }}" class="btn btn-primary btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fas fa-clipboard-list"></i>
                        </span>
                        <span class="text">Buat Pengajuan PPI</span>
                    </a>
                </div>
            </div>
        </div>

    </div>

@endsection