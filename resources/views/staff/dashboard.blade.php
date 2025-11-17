@extends('layouts.app') {{-- Pastikan nama layout lo bener --}}

@section('title', $title) {{-- Nangkep $title dari controller --}}

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-tasks fa-sm text-white-50"></i> Lihat Semua Tugas
    </a>
</div>

<!-- Content Row (Kartu Statistik) -->
<div class="row">

    <!-- Tugas Pending -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tugas Baru (Pending)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tugasPending }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-inbox fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tugas Diproses -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Tugas Dikerjakan</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tugasProses }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tugas Selesai -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tugas Selesai</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tugasSelesai }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row (Tabel Tugas Terbaru) -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">5 Tugas Terbaru (Pending)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No. Tiket</th>
                                <th>Tanggal Lapor</th>
                                <th>Pelapor</th>
                                <th>Masalah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTugas as $tugas)
                                <tr>
                                    <td>{{ $tugas->no_tiket }}</td>
                                    <td>{{ $tugas->created_at->format('d-m-Y') }}</td>
                                    <td>{{ $tugas->pelapor->name ?? 'N/A' }}</td>
                                    <td>{{ $tugas->judul_masalah }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> Lihat
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <p class="text-success mb-0">Tidak ada tugas baru. Santai dulu, Bos!</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection