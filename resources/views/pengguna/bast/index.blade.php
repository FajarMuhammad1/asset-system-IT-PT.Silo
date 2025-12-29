@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- TAMPILAN DESKTOP (TABEL) - Hidden di HP --}}
    {{-- ========================================== --}}
    <div class="d-none d-md-block">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Daftar Dokumen BAST</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Kode Aset</th>
                                <th>Nama Barang</th>
                                <th>Tanggal</th>
                                <th>Admin</th>
                                <th style="width: 15%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bastList as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge badge-secondary">{{ $item->aset->kode_asset }}</span></td>
                                <td class="font-weight-bold">{{ $item->aset->masterBarang->nama_barang }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->tanggal_serah_terima)->format('d M Y') }}</td>
                                <td>{{ $item->admin->nama }}</td>
                                <td class="text-center">
                                    <a href="{{ route('pengguna.userbast.sign', $item->id) }}" class="btn btn-success btn-sm shadow-sm">
                                        <i class="fas fa-file-signature"></i> Tanda Tangan
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open fa-2x mb-2"></i><br>
                                    Tidak ada dokumen BAST untuk ditandatangani.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- TAMPILAN MOBILE (KARTU) - Hidden di Laptop --}}
    {{-- ========================================== --}}
    <div class="d-md-none">
        @forelse($bastList as $item)
        <div class="card shadow mb-3 border-left-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="h6 font-weight-bold text-dark mb-0">
                        {{ $item->aset->masterBarang->nama_barang }}
                    </h5>
                    <small class="text-muted">
                        {{ \Carbon\Carbon::parse($item->tanggal_serah_terima)->format('d M Y') }}
                    </small>
                </div>
                
                <div class="mb-3">
                    <div class="small text-gray-600">Kode Aset:</div>
                    <div class="font-weight-bold text-primary">{{ $item->aset->kode_asset }}</div>
                </div>

                <div class="mb-3">
                    <div class="small text-gray-600">Petugas Admin:</div>
                    <div>{{ $item->admin->nama }}</div>
                </div>

                <hr>

                <a href="{{ route('pengguna.userbast.sign', $item->id) }}" class="btn btn-success btn-block btn-lg">
                    <i class="fas fa-file-signature mr-2"></i> Tanda Tangan
                </a>
            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
            <p class="text-muted">Semua dokumen sudah aman.<br>Tidak ada yang perlu ditandatangani.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection