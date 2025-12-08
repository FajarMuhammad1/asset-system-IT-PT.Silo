@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('staff.reports.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat Laporan Baru
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Judul Kegiatan</th>
                            <th>Tiket Terkait</th>
                            <th>Durasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</td>
                            <td>{{ $item->judul }}</td>
                            <td>
                                @if($item->ticket_id)
                                    <span class="badge badge-info">{{ $item->ticket->no_tiket }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @php
                                    $start = \Carbon\Carbon::parse($item->tanggal_mulai);
                                    $end   = \Carbon\Carbon::parse($item->tanggal_selesai);

                                    $totalMinutes = $start->diffInMinutes($end);
                                    $hours = intdiv($totalMinutes, 60);
                                    $minutes = $totalMinutes % 60;
                                @endphp

                                @if($hours > 0)
                                    {{ $hours }} Jam
                                @endif

                                @if($minutes > 0)
                                    {{ $minutes }} Menit
                                @endif

                                @if($hours == 0 && $minutes == 0)
                                    < 1 Menit
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('staff.reports.show', $item->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada laporan kerja.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection