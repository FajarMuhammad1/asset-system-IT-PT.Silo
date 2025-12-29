@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 text-gray-800">{{ $title }}</h1>
        {{-- Tombol Tambah (Opsional, kalau mau dipasang disini juga) --}}
        {{-- <a href="{{ route('pengguna.ppi.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Buat Baru</a> --}}
    </div>

    {{-- ================================================= --}}
    {{-- TAMPILAN DESKTOP (TABEL) - HANYA MUNCUL DI LAPTOP --}}
    {{-- ================================================= --}}
    <div class="d-none d-md-block">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-history mr-1"></i> Daftar Riwayat Pengajuan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th width="5%">No</th>
                                <th>No PPI</th>
                                <th>Tanggal</th>
                                <th>Perangkat</th>
                                <th class="text-center">Status</th>
                                <th>Keterangan Admin</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($riwayatPpi as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold text-primary">{{ $item->no_ppi }}</td>
                                <td>{{ date('d M Y', strtotime($item->tanggal)) }}</td>
                                <td>{{ $item->perangkat }}</td>
                                <td class="text-center">
                                    @if($item->status == 'pending')
                                        <span class="badge badge-warning px-3 py-2">Menunggu</span>
                                    @elseif($item->status == 'disetujui')
                                        <span class="badge badge-primary px-3 py-2">Proses (Disetujui)</span>
                                    @elseif($item->status == 'selesai')
                                        <span class="badge badge-success px-3 py-2">Selesai</span>
                                    @else
                                        <span class="badge badge-danger px-3 py-2">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->keterangan)
                                        <small class="text-muted">{{ $item->keterangan }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i><br>
                                    Belum ada riwayat pengajuan PPI.
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
    {{-- TAMPILAN MOBILE (KARTU) - HANYA MUNCUL DI HP      --}}
    {{-- ================================================= --}}
    <div class="d-md-none">
        @forelse($riwayatPpi as $item)
        <div class="card shadow mb-3 border-left-{{ $item->status == 'selesai' ? 'success' : ($item->status == 'pending' ? 'warning' : ($item->status == 'disetujui' ? 'primary' : 'danger')) }}">
            <div class="card-body">
                
                {{-- Header Kartu: No PPI & Tanggal --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-weight-bold text-primary">#{{ $item->no_ppi }}</span>
                    <small class="text-muted"><i class="far fa-calendar-alt"></i> {{ date('d M Y', strtotime($item->tanggal)) }}</small>
                </div>

                {{-- Isi: Perangkat --}}
                <h5 class="h6 font-weight-bold text-dark mb-3">
                    {{ $item->perangkat }}
                </h5>

                {{-- Footer: Status & Keterangan --}}
                <div class="d-flex justify-content-between align-items-center">
                    
                    {{-- Status Badge Logic --}}
                    <div>
                        @if($item->status == 'pending')
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Menunggu</span>
                        @elseif($item->status == 'disetujui')
                            <span class="badge badge-primary"><i class="fas fa-tools"></i> Diproses</span>
                        @elseif($item->status == 'selesai')
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> Selesai</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times-circle"></i> Ditolak</span>
                        @endif
                    </div>

                    {{-- Jika ada balasan admin, muncul icon info --}}
                    @if($item->keterangan)
                    <button type="button" class="btn btn-sm btn-circle btn-info" data-toggle="popover" title="Catatan Admin" data-content="{{ $item->keterangan }}">
                        <i class="fas fa-info"></i>
                    </button>
                    @endif
                </div>

                {{-- Tampilkan Keterangan Langsung jika Ditolak/Selesai agar jelas --}}
                @if($item->keterangan)
                    <div class="mt-3 p-2 bg-gray-100 rounded small border">
                        <strong>Catatan:</strong> {{ $item->keterangan }}
                    </div>
                @endif

            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
            <p class="text-muted">Belum ada pengajuan PPI.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Aktifkan Popover untuk Mobile Info
    $(function () {
        $('[data-toggle="popover"]').popover()
    })
</script>
@endpush