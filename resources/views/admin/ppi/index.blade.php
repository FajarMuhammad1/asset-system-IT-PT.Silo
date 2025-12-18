@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Monitoring Request PPI</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Masuk</h6>
            
            {{-- TOMBOL BUKA MODAL --}}
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
                            <th>Departemen</th>
                            <th>Perusahaan</th> <th>Perangkat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dataPpi as $item)
                        <tr>
                            <td><strong>{{ $item->no_ppi }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}</td>
                            <td>{{ $item->user->name ?? $item->user->nama ?? 'User Hapus' }}</td>

                            {{-- Tampilkan Dept & Perusahaan --}}
                            <td>{{ $item->user->departemen ?? '-' }}</td> 
                            <td><span class="badge badge-info">{{ $item->user->perusahaan ?? '-' }}</span></td>

                            <td>{{ $item->perangkat ?? '-' }}</td>
                            
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->status == 'disetujui')
                                    <span class="badge badge-primary">Disetujui</span>
                                @elseif($item->status == 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @elseif($item->status == 'ditolak')
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('admin.ppi.show', $item->id) }}" class="btn btn-info btn-sm mr-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    {{-- Dropdown Aksi Status --}}
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <form action="{{ route('admin.ppi.update', $item->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <button name="status" value="disetujui" class="dropdown-item text-primary" onclick="return confirm('Setujui?')">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                                <button name="status" value="selesai" class="dropdown-item text-success" onclick="return confirm('Selesai?')">
                                                    <i class="fas fa-check-double"></i> Selesai
                                                </button>
                                                <div class="dropdown-divider"></div>
                                                <button name="status" value="ditolak" class="dropdown-item text-danger" onclick="return confirm('Tolak?')">
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

{{-- MODAL FILTER EXPORT --}}
<div class="modal fade" id="modalExport" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalExportLabel"><i class="fas fa-filter"></i> Filter Laporan Excel</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            {{-- Form mengarah ke Controller --}}
            <form action="{{ route('admin.ppi.export') }}" method="GET">
                <div class="modal-body">
                    
                    {{-- OPSI 1: PER HARIAN --}}
                    <div class="alert alert-secondary p-2 mb-3">
                        <strong>Opsi 1: Laporan Harian</strong>
                        <div class="form-group mb-0 mt-1">
                            <input type="date" name="tanggal" class="form-control">
                            <small class="text-danger">*Isi ini jika ingin download per tanggal saja. (Bulan/Tahun akan diabaikan).</small>
                        </div>
                    </div>

                    <div class="text-center font-weight-bold mb-3">--- ATAU ---</div>

                    {{-- OPSI 2: BULANAN --}}
                    <div class="alert alert-secondary p-2 mb-3">
                        <strong>Opsi 2: Laporan Bulanan</strong>
                        <div class="row mt-2">
                            <div class="col-6">
                                <label>Bulan</label>
                                <select name="bulan" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                            {{ date("F", mktime(0, 0, 0, $i, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Tahun</label>
                                <select name="tahun" class="form-control">
                                    @php $thn = date('Y'); @endphp
                                    @for ($y = $thn; $y >= $thn - 3; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- FILTER PERUSAHAAN (Ganti dari Departemen) --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Filter Perusahaan (Opsional)</label>
                        <select name="perusahaan" class="form-control">
                            <option value="semua">-- Semua Perusahaan --</option>
                            
                            {{-- AMBIL LIST PERUSAHAAN DARI TABEL USERS --}}
                            @php
                                $list_pt = \App\Models\User::select('perusahaan')
                                                ->whereNotNull('perusahaan')
                                                ->distinct()
                                                ->orderBy('perusahaan', 'asc')
                                                ->get();
                            @endphp

                            @foreach($list_pt as $pt)
                                <option value="{{ $pt->perusahaan }}">{{ $pt->perusahaan }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih perusahaan untuk rekapitulasi data spesifik.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection