@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('staff.helpdesk.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Alert untuk pesan sukses atau error (Validasi Keamanan) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">

            <h5><strong>No. Tiket:</strong> {{ $ticket->no_tiket }}</h5>
            <p class="mb-1"><strong>Judul:</strong> {{ $ticket->judul_masalah }}</p>
            <p class="mb-3"><strong>Deskripsi:</strong> {{ $ticket->deskripsi }}</p>

            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1">
                        <strong>Status:</strong>
                        @if($ticket->status == 'Open')
                            <span class="badge badge-warning text-dark">{{ $ticket->status }}</span>
                        @elseif($ticket->status == 'Progres')
                            <span class="badge badge-info">{{ $ticket->status }}</span>
                        @elseif($ticket->status == 'Closed')
                            <span class="badge badge-success">{{ $ticket->status }}</span>
                        @elseif($ticket->status == 'Ditolak' || $ticket->status == 'Reject')
                            <span class="badge badge-danger">{{ $ticket->status }}</span>
                        @endif
                    </p>
                    <p class="mb-1"><strong>Pelapor:</strong> {{ $ticket->pelapor->nama }}</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Prioritas:</strong> <span class="badge badge-secondary">{{ $ticket->prioritas ?? 'Normal' }}</span></p>
                    <p class="mb-1">
                        <strong>Tipe Pengerjaan:</strong> 
                        @if($ticket->tipe_penyelesaian == 'Tim')
                            <span class="badge badge-primary"><i class="fas fa-users"></i> Tim (Kolaborasi)</span>
                        @else
                            <span class="badge badge-info"><i class="fas fa-user"></i> Individu</span>
                        @endif
                    </p>
                </div>
            </div>

            @if($ticket->foto_masalah)
                <p><strong>Foto Masalah:</strong></p>
                <img src="{{ asset('storage/' . $ticket->foto_masalah) }}"
                     class="img-fluid rounded mb-3 shadow-sm"
                     style="max-width:300px;">
            @endif

            <hr>

            @if($ticket->status === 'Closed')
                <div class="alert alert-success border-left-success shadow-sm">
                    <i class="fas fa-check-circle mr-1"></i> Tiket selesai pada: <b>{{ $ticket->tgl_selesai }}</b>
                </div>
                <p><strong>Solusi Teknis:</strong></p>
                <div class="alert alert-secondary">{{ $ticket->solusi_teknisi }}</div>

            @elseif($ticket->status === 'Ditolak' || $ticket->status === 'Reject')
                <div class="alert alert-danger border-left-danger shadow-sm">
                    <i class="fas fa-times-circle mr-1"></i> <strong>Tugas Ditolak:</strong> {{ $ticket->alasan_penolakan }}
                </div>

            @else
                
                {{-- Cek apakah user yang login adalah PIC Utama --}}
                @if($ticket->teknisi_id == Auth::id())
                    
                    @if($ticket->status == 'Open')
                        <form action="{{ route('staff.helpdesk.start', $ticket->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary font-weight-bold">
                                <i class="fas fa-play mr-1"></i> Mulai Kerjakan
                            </button>
                        </form>
                    @endif

                    <button class="btn btn-success font-weight-bold" data-toggle="modal" data-target="#modalSelesai">
                        <i class="fas fa-check mr-1"></i> Selesaikan Tiket
                    </button>

                    <button class="btn btn-danger font-weight-bold" data-toggle="modal" data-target="#modalTolak">
                        <i class="fas fa-times mr-1"></i> Tolak Tugas
                    </button>

                @else
                    {{-- Jika bukan PIC Utama (Hanya Anggota Tim yang memantau) --}}
                    <div class="alert alert-info border-left-info shadow-sm">
                        <h6 class="font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Informasi Kolaborasi Tim</h6>
                        <p class="mb-0">Anda dapat melihat tiket ini karena dikategorikan sebagai <strong>Tugas Tim</strong>. <br>
                        PIC Utama (Ketua Tim) yang bertanggung jawab atas status tiket ini adalah: <strong>{{ $ticket->teknisi->nama }}</strong>.</p>
                    </div>
                @endif

            @endif

        </div>
    </div>
</div>

@if($ticket->teknisi_id == Auth::id() && !in_array($ticket->status, ['Closed', 'Ditolak', 'Reject']))
<div class="modal fade" id="modalSelesai" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('staff.helpdesk.finish', $ticket->id) }}"
              method="POST" class="modal-content">
            @csrf

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle mr-1"></i> Selesaikan Tiket</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <label><strong>Solusi Perbaikan / Tindakan yang Dilakukan *</strong></label>
                <textarea name="solusi_teknisi" class="form-control" rows="4" placeholder="Jelaskan solusi yang telah diterapkan..." required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Selesaikan Tiket</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('staff.helpdesk.reject', $ticket->id) }}"
              method="POST" class="modal-content">
            @csrf

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-times-circle mr-1"></i> Tolak Tugas</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <label><strong>Alasan Penolakan *</strong></label>
                <textarea name="alasan_penolakan" class="form-control" rows="3" placeholder="Sebutkan alasan mengapa tugas ini tidak dapat dikerjakan/ditolak..." required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak Tugas</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection