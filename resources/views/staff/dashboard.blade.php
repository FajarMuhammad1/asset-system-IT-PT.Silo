@extends('layouts.app')

@section('title', $title)

@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>

    <a href="{{ route('staff.helpdesk.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-list fa-sm text-white-50"></i> Lihat Semua Tugas
    </a>
</div>

<!-- CARD STATISTIK -->
<div class="row">

    <!-- Pending -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body position-relative">
                <div class="row no-gutters align-items-center">

                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <a href="{{ route('staff.helpdesk.index') }}" class="stretched-link text-warning">
                                Tugas Baru (Pending)
                            </a>
                        </div>

                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tugasPending }}</div>
                    </div>

                    <div class="col-auto">
                        <i class="fas fa-inbox fa-2x text-gray-300"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Progres -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body position-relative">
                <div class="row no-gutters align-items-center">

                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <a href="{{ route('staff.helpdesk.index') }}" class="stretched-link text-info">
                                Tugas Sedang Dikerjakan
                            </a>
                        </div>

                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $tugasProses }}</div>
                    </div>

                    <div class="col-auto">
                        <i class="fas fa-tools fa-2x text-gray-300"></i>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Selesai -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body position-relative">
                <div class="row no-gutters align-items-center">

                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <a href="{{ route('staff.helpdesk.index') }}" class="stretched-link text-success">
                                Tugas Selesai
                            </a>
                        </div>

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

<!-- TABEL 5 TUGAS TERBARU -->
<div class="row">
    <div class="col-lg-12 mb-4">
        <div class="card shadow mb-4">

            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">5 Tugas Terbaru</h6>
            </div>

            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No. Tiket</th>
                                <th>Tanggal</th>
                                <th>Pelapor</th>
                                <th>Judul</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($recentTugas as $t)
                            <tr>
                                <td>{{ $t->no_tiket }}</td>
                                <td>{{ $t->created_at->format('d-m-Y') }}</td>
                                <td>{{ $t->pelapor->nama }}</td>
                                <td>{{ $t->judul_masalah }}</td>
                                <td>
                                    @if($t->status == 'Open')
                                        <span class="badge badge-warning">Open</span>
                                    @elseif($t->status == 'Progres')
                                        <span class="badge badge-info">Progres</span>
                                    @elseif($t->status == 'Closed')
                                        <span class="badge badge-success">Closed</span>
                                    @else
                                        <span class="badge badge-danger">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('staff.helpdesk.show', $t->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    Tidak ada tugas baru.
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
