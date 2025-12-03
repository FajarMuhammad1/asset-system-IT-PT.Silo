@extends('layouts.app')

@section('content')
<div class="container">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">

            <!-- Informasi Tiket -->
            <h5><strong>No. Tiket:</strong> {{ $ticket->no_tiket }}</h5>
            <p><strong>Judul:</strong> {{ $ticket->judul_masalah }}</p>
            <p><strong>Deskripsi:</strong> {{ $ticket->deskripsi }}</p>

            <p>
                <strong>Status:</strong>
                <span class="badge badge-info">{{ $ticket->status }}</span>
            </p>

            <p><strong>Pelapor:</strong> {{ $ticket->pelapor->nama }}</p>

            @if($ticket->foto_masalah)
                <p><strong>Foto Masalah:</strong></p>
                <img src="{{ asset('storage/' . $ticket->foto_masalah) }}"
                     class="img-fluid rounded mb-3"
                     style="max-width:300px;">
            @endif

            <hr>

            <!-- ====================================================== -->
            <!--                AKSI UNTUK STAFF                      -->
            <!-- ====================================================== -->

            {{-- 1. Admin sudah assign, tapi staff belum mulai --}}
            @if($ticket->status === 'Open' && $ticket->started_at === null)

                <form action="{{ route('staff.helpdesk.start', $ticket->id) }}"
                      method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-play"></i> Mulai Kerjakan
                    </button>
                </form>

                <button class="btn btn-danger"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTolak">
                    <i class="fas fa-times"></i> Tolak Tugas
                </button>

            {{-- 2. Staff sudah mulai mengerjakan (status Progres) --}}
            @elseif($ticket->status === 'Progres' && $ticket->started_at !== null)

                <button class="btn btn-success"
                        data-bs-toggle="modal"
                        data-bs-target="#modalSelesai">
                    <i class="fas fa-check"></i> Selesaikan Tiket
                </button>

            {{-- 3. Tiket sudah selesai --}}
            @elseif($ticket->status === 'Closed')

                <div class="alert alert-success">
                    Tiket selesai pada:
                    <b>{{ $ticket->tgl_selesai }}</b>
                </div>

                <p><strong>Solusi Teknis:</strong></p>
                <div class="alert alert-secondary">{{ $ticket->solusi_teknisi }}</div>

            {{-- 4. Tiket ditolak staff --}}
            @elseif($ticket->status === 'Ditolak')

                <div class="alert alert-danger">
                    <strong>Tugas Ditolak:</strong> {{ $ticket->alasan_penolakan }}
                </div>

            @endif

        </div>
    </div>
</div>

<!-- ====================================================== -->
<!-- MODAL SELESAIKAN TIKET -->
<!-- ====================================================== -->
<div class="modal fade" id="modalSelesai" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('staff.helpdesk.finish', $ticket->id) }}"
              method="POST" class="modal-content">
            @csrf

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Selesaikan Tiket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label><strong>Solusi Perbaikan *</strong></label>
                <textarea name="solusi_teknisi" class="form-control" rows="4" required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Selesaikan</button>
            </div>
        </form>
    </div>
</div>

<!-- ====================================================== -->
<!-- MODAL TOLAK TUGAS -->
<!-- ====================================================== -->
<div class="modal fade" id="modalTolak" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('staff.helpdesk.reject', $ticket->id) }}"
              method="POST" class="modal-content">
            @csrf

            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Tolak Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <label><strong>Alasan Penolakan *</strong></label>
                <textarea name="alasan_penolakan" class="form-control" rows="3" required></textarea>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger">Tolak</button>
            </div>
        </form>
    </div>
</div>

@endsection


