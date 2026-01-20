@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Dashboard Approval (SuperAdmin)</h1>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-dark">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-tasks mr-2"></i> Permintaan Menunggu Tanda Tangan
            </h6>
        </div>
        <div class="card-body">
            @if($requestMasuk->isEmpty())
                <div class="text-center py-5">
                    <img src="https://img.icons8.com/clouds/100/000000/checked.png"/>
                    <h4 class="text-gray-500 mt-3">Tidak ada permintaan pending.</h4>
                    <p>Semua pekerjaan sudah selesai!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No PPI</th>
                                <th>Tanggal</th>
                                <th>Pemohon</th>
                                <th>Perangkat</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requestMasuk as $item)
                            <tr>
                                <td class="font-weight-bold">{{ $item->no_ppi }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                <td>
                                    <strong>{{ $item->user->nama }}</strong><br>
                                    <small class="text-muted">{{ $item->user->departemen }} - {{ $item->user->perusahaan }}</small>
                                </td>
                                <td>{{ $item->perangkat }}</td>
                                <td>
                                    <span class="badge badge-warning">Butuh TTD Anda</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('superadmin.approval.review', $item->id) }}" class="btn btn-primary btn-sm shadow-sm">
                                        <i class="fas fa-pen-fancy"></i> Review & TTD
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection