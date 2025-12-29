@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER & TOMBOL TAMBAH --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title ?? 'Tiket Bantuan Saya' }}</h1>
        <a href="{{ route('pengguna.helpdesk.create') }}" class="btn btn-primary btn-sm shadow-sm mt-2 mt-sm-0">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Laporan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ================================================= --}}
    {{-- TAMPILAN DESKTOP (TABEL) - Hidden di HP           --}}
    {{-- ================================================= --}}
    <div class="d-none d-md-block">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-list mr-1"></i> Daftar Tiket Laporan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th width="15%">No Tiket</th>
                                <th width="15%">Tanggal</th>
                                <th>Judul Masalah</th>
                                <th width="15%" class="text-center">Status</th>
                                <th width="10%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $item)
                            <tr>
                                <td class="font-weight-bold text-primary">{{ $item->no_tiket }}</td>
                                <td>{{ $item->created_at->format('d M Y H:i') }}</td>
                                <td>{{ Str::limit($item->judul_masalah, 50) }}</td>
                                <td class="text-center">
                                    @if($item->status == 'Open')
                                        <span class="badge badge-warning px-3 py-2">Open</span>
                                    @elseif($item->status == 'Progres')
                                        <span class="badge badge-info px-3 py-2">Sedang Diproses</span>
                                    @elseif($item->status == 'Closed')
                                        <span class="badge badge-success px-3 py-2">Selesai</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('pengguna.helpdesk.show', $item->id) }}" class="btn btn-sm btn-secondary shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-check fa-3x mb-3"></i><br>
                                    Belum ada laporan kendala yang dibuat.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- TAMPILAN MOBILE (KARTU) - Hidden di Desktop       --}}
    {{-- ================================================= --}}
    <div class="d-md-none">
        @forelse($tickets as $item)
        
        {{-- Tentukan Warna Border Berdasarkan Status --}}
        @php
            $borderColor = 'warning'; // Default Open
            if($item->status == 'Progres') $borderColor = 'info';
            if($item->status == 'Closed') $borderColor = 'success';
            if($item->status == 'Ditolak') $borderColor = 'danger';
        @endphp

        <div class="card shadow mb-3 border-left-{{ $borderColor }}">
            <div class="card-body">
                
                {{-- Baris Atas: No Tiket & Tanggal --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-weight-bold text-dark">#{{ $item->no_tiket }}</span>
                    <small class="text-muted">{{ $item->created_at->format('d M Y') }}</small>
                </div>

                {{-- Judul Masalah --}}
                <h5 class="h6 font-weight-bold text-primary mb-3">
                    {{ $item->judul_masalah }}
                </h5>

                {{-- Baris Bawah: Status & Tombol --}}
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        @if($item->status == 'Open')
                            <span class="badge badge-warning"><i class="fas fa-clock mr-1"></i> Menunggu</span>
                        @elseif($item->status == 'Progres')
                            <span class="badge badge-info"><i class="fas fa-tools mr-1"></i> Diproses</span>
                        @elseif($item->status == 'Closed')
                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Selesai</span>
                        @else
                            <span class="badge badge-danger">{{ $item->status }}</span>
                        @endif
                    </div>

                    <a href="{{ route('pengguna.helpdesk.show', $item->id) }}" class="btn btn-sm btn-outline-secondary">
                        Detail <i class="fas fa-chevron-right ml-1"></i>
                    </a>
                </div>

            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fas fa-smile fa-3x text-gray-300 mb-3"></i>
            <p class="text-muted">Tidak ada keluhan.<br>Sistem berjalan lancar!</p>
        </div>
        @endforelse
    </div>

</div>
@endsection