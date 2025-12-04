@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Tiket Bantuan Saya</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Riwayat Laporan</h6>
            <a href="{{ route('pengguna.helpdesk.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus fa-sm text-white-50"></i> Buat Laporan Baru
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No Tiket</th>
                            <th>Tanggal</th>
                            <th>Judul Masalah</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($tickets as $item)
                        <tr>
                            <td class="font-weight-bold text-primary">{{ $item->no_tiket }}</td>
                            <td>{{ $item->created_at->format('d M Y') }}</td>
                            <td>{{ Str::limit($item->judul_masalah, 40) }}</td>

                            <td>
                                @if($item->status == 'Open')
                                    <span class="badge badge-warning">Open</span>
                                @elseif($item->status == 'Progres')
                                    <span class="badge badge-info">Diproses</span>
                                @elseif($item->status == 'Closed')
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('pengguna.helpdesk.show', $item->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-3">Belum ada tiket laporan.</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>

            </div>
        </div>
    </div>

</div>
@endsection
