@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Pengajuan Anda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>No PPI</th>
                            <th>Tanggal</th>
                            <th>Perangkat</th>
                            <th>Status</th>
                            <th>Keterangan Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPpi as $item)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="font-weight-bold">{{ $item->no_ppi }}</td>
                            <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                            <td>{{ $item->perangkat }}</td>
                            <td class="text-center">
                                @if($item->status == 'pending')
                                    <span class="badge badge-warning px-2 py-1">Menunggu</span>
                                @elseif($item->status == 'disetujui')
                                    <span class="badge badge-primary px-2 py-1">Disetujui (Proses)</span>
                                @elseif($item->status == 'selesai')
                                    <span class="badge badge-success px-2 py-1">Selesai</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                {{-- Disini nanti bisa nampilin pesan dari admin/teknisi kalo ada --}}
                                {{ $item->keterangan ?? '-' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-3">Anda belum pernah mengajukan PPI.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection