@extends('layouts.app')

@section('title', $title) {{-- Nangkep $title dari controller --}}

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    <a href="{{ route('surat-jalan.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> Buat Surat Jalan Baru
    </a>
</div>

<!-- Content Row (INI KARTU STATISTIK LO) -->
<div class="row">

    <!-- 1. Kartu Total Tim IT (TAMPILAN ASLI + LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        {{-- KARTU LUAR TETAP ASLI (ADA 'py-2') --}}
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            {{-- LINK-NYA TARUH DI JUDUL, KASIH KELAS 'stretched-link' --}}
                            <a href="{{ route('team') }}" class="stretched-link text-primary">
                                Total Team IT (Staff/Admin)
                            </a>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teamCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Kartu Total Pengguna (TAMPILAN ASLI + LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            <a href="{{ route('pengguna.index') }}" class="stretched-link text-success">
                                Total Pengguna (User)
                            </a>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penggunaCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Kartu Total Aset (TAMPILAN ASLI + LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            <a href="{{ route('barangmasuk.index') }}" class="stretched-link text-info">
                                Total Aset Terdaftar
                            </a>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assetCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-archive fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Kartu PPI Pending (TAMPILAN ASLI + LINK) -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1">
                            <a href="{{ route('admin.ppi.index') }}" class="stretched-link text-warning">
                                PPI Menunggu Persetujuan
                            </a>
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ppiPendingCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- (Lo bisa tambahin chart atau tabel lain di sini) --}}

@endsection