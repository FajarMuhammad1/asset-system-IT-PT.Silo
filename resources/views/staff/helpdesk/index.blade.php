@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-clipboard-list mr-2"></i> {{ $title }}</h1>
    </div>

    {{-- Alert Sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4 border-bottom-primary">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Penugasan Helpdesk</h6>
        </div>
        <div class="card-body">
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No Tiket</th>
                            <th>Pelapor</th>
                            <th>Judul Masalah</th>
                            <th>Prioritas</th>
                            <th>Status Penugasan</th>
                            <th>Status Tiket</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $t)
                        <tr>
                            <td class="font-weight-bold">{{ $t->no_tiket }}</td>
                            <td>{{ $t->pelapor->nama ?? 'Tidak Diketahui' }}</td>
                            <td>{{ Str::limit($t->judul_masalah, 40) }}</td>
                            
                            {{-- Kolom Prioritas --}}
                            <td>
                                @php
                                    $colorPrio = ['Low' => 'secondary', 'Normal' => 'info', 'High' => 'warning', 'Urgent' => 'danger'][$t->prioritas] ?? 'info';
                                @endphp
                                <span class="badge badge-{{ $colorPrio }} px-2 py-1">{{ $t->prioritas ?? 'Normal' }}</span>
                            </td>

                            {{-- Kolom Tipe Pengerjaan (Tim vs Individu) --}}
                            <td>
                                @if($t->tipe_penyelesaian == 'Tim')
                                    <span class="badge badge-primary mb-1"><i class="fas fa-users mr-1"></i> Tugas Tim</span><br>
                                    
                                    {{-- Cek apakah staff yang login adalah PIC Utama --}}
                                    @if($t->teknisi_id == Auth::id())
                                        <small class="text-success font-weight-bold"><i class="fas fa-star text-warning"></i> Anda PIC Utama</small>
                                    @else
                                        <small class="text-muted">PIC: {{ $t->teknisi->nama ?? '-' }}</small>
                                    @endif
                                    
                                @else
                                    <span class="badge badge-secondary"><i class="fas fa-user mr-1"></i> Individu</span>
                                @endif
                            </td>

                            {{-- Kolom Status Tiket --}}
                            <td>
                                @if($t->status == 'Open')
                                    <span class="badge badge-warning text-dark"><i class="fas fa-envelope-open mr-1"></i> Open</span>
                                @elseif($t->status == 'Progres')
                                    <span class="badge badge-info"><i class="fas fa-spinner fa-spin mr-1"></i> Progres</span>
                                @elseif($t->status == 'Closed')
                                    <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Closed</span>
                                @elseif($t->status == 'Ditolak' || $t->status == 'Reject')
                                    <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                @else
                                    <span class="badge badge-secondary">{{ $t->status }}</span>
                                @endif
                            </td>

                            {{-- Kolom Aksi --}}
                            <td class="text-center align-middle">
                                @if($t->teknisi_id == Auth::id() && $t->status == 'Open')
                                    {{-- Jika dia PIC dan tiket masih Open, bisa langsung klik lihat untuk mulai --}}
                                    <a href="{{ route('staff.helpdesk.show', $t->id) }}" class="btn btn-sm btn-primary shadow-sm" title="Mulai Kerjakan">
                                        <i class="fas fa-play mr-1"></i> Mulai
                                    </a>
                                @else
                                    {{-- Jika sekadar anggota tim atau tiket sudah progres/selesai --}}
                                    <a href="{{ route('staff.helpdesk.show', $t->id) }}" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-search mr-1"></i> Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-mug-hot fa-3x mb-3 text-gray-300"></i>
                                <h5>Belum ada tugas!</h5>
                                <p>Tidak ada tiket individu maupun penugasan tim saat ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>
@endsection