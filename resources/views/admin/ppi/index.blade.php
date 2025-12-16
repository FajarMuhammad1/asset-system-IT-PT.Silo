@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Monitoring Request PPI</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        {{-- UPDATE: Header Flex --}}
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Masuk</h6>
            
            {{-- UPDATE: TOMBOL MEMBUKA MODAL FILTER --}}
            <button type="button" class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalExport">
                <i class="fas fa-file-excel"></i> Filter & Export Excel
            </button>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No PPI</th>
                            <th>Tanggal</th>
                            <th>Pemohon</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Perusahaan</th>
                            <th>Perangkat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataPpi as $item)
                        <tr>
                            <td><strong>{{ $item->no_ppi }}</strong></td>
                            {{-- Format Tanggal --}}
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                            
                            {{-- Nama User --}}
                            <td>{{ $item->user->name ?? $item->user->nama ?? 'User Hapus' }}</td>

                            {{-- DATA TAMBAHAN (Relasi User) --}}
                            <td>{{ $item->user->jabatan ?? '-' }}</td>
                            {{-- Ambil dari user->departemen atau user->divisi --}}
                            <td>{{ $item->user->departemen ?? $item->user->divisi ?? '-' }}</td> 
                            <td>{{ $item->user->perusahaan ?? '-' }}</td>

                            <td>{{ $item->perangkat ?? '-' }}</td>
                            
                            <td>
                                {{-- Badge Status --}}
                                @if($item->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->status == 'disetujui')
                                    <span class="badge badge-primary">Disetujui (Proses)</span>
                                @elseif($item->status == 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($item->status == 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @else
                                    <span class="badge badge-secondary">{{ $item->status }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    {{-- Tombol Lihat Detail --}}
                                    <a href="{{ route('admin.ppi.show', $item->id) }}" class="btn btn-info btn-sm mr-1" title="Lihat Detail">
                                        <i class="fas fa-eye"></i> 
                                    </a>

                                    {{-- Dropdown Aksi --}}
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <form action="{{ route('admin.ppi.update', $item->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                
                                                <button name="status" value="disetujui" class="dropdown-item text-primary" onclick="return confirm('Setujui permintaan ini?')">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                                
                                                <button name="status" value="selesai" class="dropdown-item text-success" onclick="return confirm('Tandai sebagai selesai?')">
                                                    <i class="fas fa-check-double"></i> Selesai
                                                </button>
                                                
                                                <div class="dropdown-divider"></div>
                                                
                                                <button name="status" value="ditolak" class="dropdown-item text-danger" onclick="return confirm('Tolak permintaan ini?')">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL FILTER EXPORT (Baru Ditambahkan) --}}
<div class="modal fade" id="modalExport" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalExportLabel"><i class="fas fa-filter"></i> Filter & Download Laporan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            {{-- Form mengarah ke route admin.ppi.export dengan method GET --}}
            <form action="{{ route('admin.ppi.export') }}" method="GET">
                <div class="modal-body">
                    
                    {{-- 1. PILIH BULAN --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Bulan</label>
                        <select name="bulan" class="form-control">
                            <option value="">-- Semua Bulan --</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                    {{ date("F", mktime(0, 0, 0, $i, 10)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- 2. PILIH TAHUN --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Tahun</label>
                        <select name="tahun" class="form-control">
                            @php $tahun_sekarang = date('Y'); @endphp
                            @for ($y = $tahun_sekarang; $y >= $tahun_sekarang - 3; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- 3. PILIH DEPARTEMEN --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Departemen (Opsional)</label>
                        <select name="divisi" class="form-control">
                            <option value="semua">-- Semua Departemen --</option>
                            
                            {{-- LOGIC PHP UNTUK AMBIL LIST DEPARTEMEN DARI DB --}}
                            @php
                                // Mengambil daftar departemen unik dari tabel users yang tidak kosong
                                $list_dept = \App\Models\User::select('departemen')
                                                ->whereNotNull('departemen')
                                                ->distinct()
                                                ->orderBy('departemen', 'asc')
                                                ->get();
                            @endphp

                            @foreach($list_dept as $d)
                                <option value="{{ $d->departemen }}">{{ $d->departemen }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih departemen untuk melihat siapa yang paling sering request.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection