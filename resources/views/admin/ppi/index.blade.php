@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Monitoring Request PPI</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Masuk</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No PPI</th>
                            <th>Tanggal</th>
                            <th>Pemohon</th>
                            
                            {{-- KOLOM BARU DITAMBAHKAN --}}
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
                            <td>{{ date('d-m-Y', strtotime($item->tanggal)) }}</td>
                            
                            {{-- Nama User (Pake ?? biar aman kalau user dihapus) --}}
                            <td>{{ $item->user->name ?? $item->user->nama ?? 'User Hapus' }}</td>

                            {{-- DATA TAMBAHAN (Relasi User) --}}
                            {{-- Pastikan nama kolom di database users sesuai (jabatan, departemen, perusahaan) --}}
                            <td>{{ $item->user->jabatan ?? '-' }}</td>
                            <td>{{ $item->user->departemen ?? '-' }}</td>
                            <td>{{ $item->user->perusahaan ?? '-' }}</td>

                            <td>{{ $item->perangkat }}</td>
                            <td>
                                {{-- Badge Status Warna-Warni --}}
                                @if($item->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->status == 'disetujui')
                                    <span class="badge badge-primary">Disetujui (Proses)</span>
                                @elseif($item->status == 'selesai')
                                    <span class="badge badge-success">Selesai</span>
                                @else
                                    <span class="badge badge-danger">Ditolak</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex">
                                    {{-- Tombol Lihat Detail --}}
                                    <a href="{{ route('admin.ppi.show', $item->id) }}" class="btn btn-info btn-sm mr-1">
                                        <i class="fas fa-eye"></i> 
                                    </a>

                                    {{-- Tombol Ganti Status (Dropdown) --}}
                                    <div class="dropdown">
                                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <form action="{{ route('admin.ppi.update', $item->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                
                                                <button name="status" value="disetujui" class="dropdown-item text-primary">
                                                    <i class="fas fa-check"></i> Setujui
                                                </button>
                                                
                                                <button name="status" value="selesai" class="dropdown-item text-success">
                                                    <i class="fas fa-check-double"></i> Selesai
                                                </button>
                                                
                                                <div class="dropdown-divider"></div>
                                                
                                                <button name="status" value="ditolak" class="dropdown-item text-danger">
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
@endsection