@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-exchange-alt mr-2 text-primary"></i>{{ $title }}</h1>
    <a href="{{ route('pengguna.mutasi.create') }}" class="btn btn-primary btn-icon-split shadow-sm">
        <span class="icon text-white-50"><i class="fas fa-plus"></i></span>
        <span class="text">Buat Pengajuan Mutasi</span>
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Status Pengajuan Mutasi Aset</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Kode & Nama Aset</th>
                        <th>User Lama</th>
                        <th>User Tujuan</th>
                        <th>Alasan / Keterangan</th>
                        <th>Status Approval</th>
                        <th>Aksi / Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatPengajuan as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $item->barangMasuk->kode_asset ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $item->barangMasuk->masterBarang->nama_barang ?? 'Aset' }}</small>
                            </td>
                            <td>{{ $item->userAsal->nama ?? 'Stok Gudang' }}</td>
                            <td><span class="badge badge-info">{{ $item->userTujuan->nama ?? '-' }}</span></td>
                            <td>{{ $item->keterangan }}</td>
                            <td>
                                @if($item->status == 'Menunggu Approval Manager')
                                    <span class="badge badge-warning p-2"><i class="fas fa-clock mr-1"></i> Menunggu Approval Manager</span>
                                @elseif($item->status == 'Disetujui Manager')
                                    <span class="badge badge-primary p-2"><i class="fas fa-check-circle mr-1"></i> Disetujui Manager (Proses IT)</span>
                                @elseif($item->status == 'Menunggu TTD BAST')
                                    <span class="badge badge-info p-2"><i class="fas fa-signature mr-1"></i> Menunggu TTD BAST</span>
                                @elseif($item->status == 'Selesai')
                                    <span class="badge badge-success p-2"><i class="fas fa-check-double mr-1"></i> Mutasi Selesai</span>
                                @elseif($item->status == 'Ditolak Manager')
                                    <span class="badge badge-danger p-2"><i class="fas fa-times-circle mr-1"></i> Ditolak Manager</span>
                                @else
                                    <span class="badge badge-secondary p-2">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td>
                                @if($item->status == 'Menunggu TTD BAST' && $item->user_tujuan_id == Auth::id())
                                    <a href="{{ route('pengguna.userbast.index') }}" class="btn btn-sm btn-success mb-1">
                                        <i class="fas fa-signature mr-1"></i> TTD BAST Digital
                                    </a>
                                @endif

                                @if(in_array($item->status, ['Menunggu TTD BAST', 'Selesai']))
                                    <a href="{{ route('pengguna.mutasi.cetak', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-danger shadow-sm mb-1">
                                        <i class="fas fa-file-pdf mr-1"></i> Cetak Berita Acara
                                    </a>
                                @elseif($item->status == 'Ditolak Manager')
                                    <small class="text-danger d-block"><strong>Alasan:</strong> {{ $item->alasan_penolakan ?? 'Tidak ada catatan' }}</small>
                                @else
                                    <small class="text-muted d-block"><i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</small>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block"></i> Belum ada pengajuan mutasi aset.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
