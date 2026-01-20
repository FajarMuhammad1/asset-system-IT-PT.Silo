@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Monitoring Request PPI (Admin IT)</h1>

    {{-- Notifikasi Sukses/Error --}}
    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-left-danger shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Masuk</h6>
            
            {{-- TOMBOL BUKA MODAL EXPORT --}}
            <button type="button" class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalExport">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Filter & Export Excel
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No PPI</th>
                            <th>Tanggal</th>
                            <th>Pemohon</th>
                            <th>Dept / PT</th>
                            <th>Perangkat / Aset</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataPpi as $item)
                        <tr>
                            <td class="font-weight-bold text-primary align-middle">{{ $item->no_ppi }}</td>
                            <td class="align-middle">{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                            <td class="align-middle">
                                <div class="font-weight-bold">{{ $item->user->name ?? 'User Hapus' }}</div>
                                <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                            </td>

                            <td class="align-middle">
                                <div class="small font-weight-bold">{{ $item->user->departemen ?? '-' }}</div>
                                <span class="badge badge-light border">{{ $item->user->perusahaan ?? '-' }}</span>
                            </td>

                            <td class="align-middle">
                                <span class="text-dark font-weight-bold">{{ $item->perangkat ?? '-' }}</span>
                                {{-- Cek jika ada file lampiran --}}
                                @if($item->file_ppi)
                                    <br>
                                    <a href="{{ asset('storage/'.$item->file_ppi) }}" target="_blank" class="badge badge-info mt-1">
                                        <i class="fas fa-paperclip"></i> Lihat Lampiran
                                    </a>
                                @endif
                            </td>
                            
                            {{-- KOLOM STATUS --}}
                            <td class="text-center align-middle">
                                @if($item->status == 'pending')
                                    <span class="badge badge-info px-2 py-1 shadow-sm">
                                        <i class="fas fa-search"></i> Cek Admin
                                    </span>
                                
                                @elseif($item->status == 'pending_superadmin')
                                    <span class="badge badge-warning px-2 py-1 shadow-sm text-dark">
                                        <i class="fas fa-user-tie"></i> Menunggu SPV/SA
                                    </span>
                                
                                @elseif($item->status == 'disetujui')
                                    <span class="badge badge-success px-2 py-1 shadow-sm">
                                        <i class="fas fa-check-circle"></i> Disetujui
                                    </span>
                                
                                @elseif($item->status == 'selesai')
                                    <span class="badge badge-dark px-2 py-1 shadow-sm">
                                        <i class="fas fa-flag-checkered"></i> Selesai
                                    </span>
                                
                                @elseif($item->status == 'ditolak')
                                    <span class="badge badge-danger px-2 py-1 shadow-sm">
                                        <i class="fas fa-times-circle"></i> Ditolak
                                    </span>
                                @endif
                            </td>
                            
                            {{-- KOLOM AKSI --}}
                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    
                                    {{-- 1. TOMBOL DETAIL --}}
                                    <a href="{{ route('admin.ppi.show', $item->id) }}" class="btn btn-sm btn-outline-primary mb-2">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>

                                    {{-- 2. LOGIC TOMBOL PROSES --}}
                                    @if($item->status == 'pending')
                                        <form action="{{ route('admin.ppi.forward', $item->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm" onclick="return confirm('Teruskan ke SuperAdmin?')">
                                                <i class="fas fa-paper-plane"></i> Ajukan ke SA
                                            </button>
                                        </form>

                                    @elseif($item->status == 'pending_superadmin')
                                        <button class="btn btn-secondary btn-sm btn-block" disabled style="cursor: not-allowed; opacity: 0.7;">
                                            <i class="fas fa-clock"></i> Menunggu SA
                                        </button>

                                    @elseif($item->status == 'disetujui')
                                        
                                        {{-- 
                                            WAK, PERHATIKAN DI SINI YA:
                                            Cek nama route 'surat-jalan.create' ini di web.php sampeyan.
                                            Kalau di web.php namanya 'admin.delivery.create', ganti di bawah ini.
                                            
                                            Juga pastikan Controller SuratJalan sampeyan bisa nerima parameter 'ppi_id'.
                                            Kalau controller surat jalan sampeyan masih kosongan (gak nerima ppi_id), 
                                            hapus bagian array ['ppi_id' => ...] nya.
                                        --}}

                                        @if(Route::has('admin.surat-jalan.create'))
                                            {{-- Jika route ada, tampilkan tombol --}}
                                            <a href="{{ route('admin.surat-jalan.create', ['ppi_id' => $item->id]) }}" class="btn btn-success btn-sm btn-block shadow-sm">
                                                <i class="fas fa-truck"></i> Buat Surat Jalan
                                            </a>
                                        @else
                                            {{-- Jika route TIDAK ada / salah nama, tampilkan tombol dummy biar gak error --}}
                                            <a href="#" class="btn btn-success btn-sm btn-block shadow-sm" onclick="alert('Route surat jalan belum ketemu di web.php wak!')">
                                                <i class="fas fa-truck"></i> Buat Surat Jalan
                                            </a>
                                        @endif

                                    @elseif($item->status == 'ditolak')
                                        <button class="btn btn-danger btn-sm btn-block" disabled>
                                            <i class="fas fa-ban"></i> Ditolak
                                        </button>

                                    @elseif($item->status == 'selesai')
                                        <button class="btn btn-dark btn-sm btn-block" disabled>
                                            <i class="fas fa-check"></i> Closed
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                                Belum ada data permintaan PPI.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL FILTER EXPORT (Bagian ini sama saja, tidak perlu diubah) --}}
{{-- ... Kode modal export di sini ... --}}
{{-- Saya skip biar gak kepanjangan, pakai yang lama saja --}}

@endsection