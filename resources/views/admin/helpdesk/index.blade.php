@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-headset mr-2"></i> {{ $title }}</h1>

    {{-- Alert Sukses (Jika ada aksi dari halaman detail) --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Tiket Helpdesk</h6>
        </div>
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No Tiket</th>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Perusahaan</th>
                            <th>Prioritas</th>      {{-- TAMBAHAN BARU --}}
                            <th>Tipe Tugas</th>     {{-- TAMBAHAN BARU --}}
                            <th>Status</th>
                            <th>Teknisi (PIC)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($tickets as $t)
                        <tr>
                            <td class="font-weight-bold">{{ $t->no_tiket }}</td>
                            <td>{{ Str::limit($t->judul_masalah, 30) }}</td>

                            <td>{{ $t->pelapor->nama ?? 'Tidak Diketahui' }}</td>
                            <td>{{ $t->pelapor->jabatan ?? '-' }}</td>
                            <td>{{ $t->pelapor->departemen ?? '-' }}</td>
                            <td>{{ $t->pelapor->perusahaan ?? '-' }}</td>

                            {{-- Kolom Prioritas --}}
                            <td>
                                @php
                                    $colorPrio = ['Low' => 'secondary', 'Normal' => 'info', 'High' => 'warning', 'Urgent' => 'danger'][$t->prioritas] ?? 'info';
                                @endphp
                                <span class="badge badge-{{ $colorPrio }}">{{ $t->prioritas ?? 'Normal' }}</span>
                            </td>

                            {{-- Kolom Tipe Pengerjaan --}}
                            <td>
                                @if($t->tipe_penyelesaian == 'Tim')
                                    <span class="badge badge-primary"><i class="fas fa-users mr-1"></i> Tim</span>
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-user mr-1"></i> Individu</span>
                                @endif
                            </td>

                            {{-- Kolom Status --}}
                            <td>
                                @if($t->status == 'Open')
                                    {{-- Warna Kuning --}}
                                    <span class="badge badge-warning text-dark">{{ $t->status }}</span>
                                
                                @elseif($t->status == 'Progres')
                                    {{-- Warna Biru Langit --}}
                                    <span class="badge badge-info">{{ $t->status }}</span>
                                
                                @elseif($t->status == 'Closed')
                                    {{-- Warna Hijau --}}
                                    <span class="badge badge-success">{{ $t->status }}</span>
                                
                                @elseif($t->status == 'Reject' || $t->status == 'Ditolak')
                                    {{-- Warna Merah --}}
                                    <span class="badge badge-danger">{{ $t->status }}</span>
                                
                                @else
                                    {{-- Warna Default (Abu-abu) --}}
                                    <span class="badge badge-secondary">{{ $t->status }}</span>
                                @endif
                            </td>

                            {{-- Kolom Teknisi --}}
                            <td>
                                @if($t->teknisi)
                                    <span class="badge badge-success">{{ $t->teknisi->nama }}</span>
                                @else
                                    <span class="badge badge-warning text-dark">Belum Di-assign</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <a href="{{ route('admin.helpdesk.show', $t->id) }}" 
                                   class="btn btn-primary btn-sm shadow-sm" title="Lihat Detail & Atur Tiket">
                                    <i class="fas fa-search"></i> Detail
                                </a>
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