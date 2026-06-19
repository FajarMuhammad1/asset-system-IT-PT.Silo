@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-signature mr-2"></i> {{ $title }}</h1>
        <a href="{{ route('staff.reports.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Buat Laporan Manual
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4 border-bottom-primary">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Laporan Tugas Saya</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul Kegiatan</th>
                            <th>Sumber Tugas</th>
                            <th>Durasi Pengerjaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</td>
                            <td class="font-weight-bold">{{ Str::limit($item->judul, 40) }}</td>
                            
                            {{-- Sumber Tugas: Tiket vs Manual --}}
                            <td>
                                @if($item->ticket_id)
                                    <span class="badge badge-info px-2 py-1" data-toggle="tooltip" title="Laporan otomatis dari Helpdesk">
                                        <i class="fas fa-ticket-alt mr-1"></i> Tiket: {{ $item->ticket->no_tiket ?? '-' }}
                                    </span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1" data-toggle="tooltip" title="Laporan dibuat secara manual">
                                        <i class="fas fa-user-edit mr-1"></i> Tugas Manual
                                    </span>
                                @endif
                            </td>

                            {{-- Kalkulasi Durasi --}}
                            <td>
                                @if($item->tanggal_selesai)
                                    @php
                                        $start = \Carbon\Carbon::parse($item->tanggal_mulai);
                                        $end   = \Carbon\Carbon::parse($item->tanggal_selesai);

                                        $totalMinutes = $start->diffInMinutes($end);
                                        $hours = intdiv($totalMinutes, 60);
                                        $minutes = $totalMinutes % 60;
                                    @endphp

                                    @if($hours > 0)
                                        <span class="badge badge-light border border-dark text-dark">{{ $hours }} Jam</span>
                                    @endif

                                    @if($minutes > 0)
                                        <span class="badge badge-light border border-dark text-dark">{{ $minutes }} Menit</span>
                                    @endif

                                    @if($hours == 0 && $minutes == 0)
                                        <span class="badge badge-light border border-dark text-dark">< 1 Menit</span>
                                    @endif
                                @else
                                    <span class="badge badge-warning text-dark"><i class="fas fa-spinner fa-spin mr-1"></i> Belum Selesai</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('staff.reports.show', $item->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Lihat Detail Laporan">
                                    <i class="fas fa-search"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i>
                                <h5>Belum ada laporan kerja.</h5>
                                <p>Selesaikan tiket Helpdesk untuk menghasilkan laporan otomatis, atau buat laporan manual.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection