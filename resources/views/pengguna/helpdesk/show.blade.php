@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Tiket</h1>
        <a href="{{ route('pengguna.helpdesk.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    {{-- Alert Success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">

        {{-- ========================================== --}}
        {{-- KOLOM KIRI: INFO META & STATUS --}}
        {{-- ========================================== --}}
        <div class="col-lg-4 mb-4">
            
            {{-- Tentukan Warna Garis Atas Berdasarkan Status --}}
            @php
                $borderClass = 'border-top-warning'; // Default
                if($ticket->status == 'Progres') $borderClass = 'border-top-info';
                if($ticket->status == 'Closed') $borderClass = 'border-top-success';
                if($ticket->status == 'Ditolak') $borderClass = 'border-top-danger';
            @endphp

            <div class="card shadow mb-4 {{ $borderClass }}" style="border-top-width: 5px;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tiket</h6>
                </div>
                <div class="card-body">
                    
                    {{-- NOMOR TIKET --}}
                    <div class="mb-4 text-center">
                        <label class="small font-weight-bold text-muted mb-0">NOMOR TIKET</label>
                        <h2 class="font-weight-bold text-dark">#{{ $ticket->no_tiket }}</h2>
                    </div>

                    <ul class="list-group list-group-flush">
                        {{-- STATUS --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="small font-weight-bold">Status</span>
                            @if($ticket->status == 'Open') 
                                <span class="badge badge-warning px-2 py-1">Open</span>
                            @elseif($ticket->status == 'Progres') 
                                <span class="badge badge-info px-2 py-1">Diproses</span>
                            @elseif($ticket->status == 'Closed') 
                                <span class="badge badge-success px-2 py-1">Selesai</span>
                            @else 
                                <span class="badge badge-danger px-2 py-1">Ditolak</span> 
                            @endif
                        </li>

                        {{-- TANGGAL --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="small font-weight-bold">Tanggal Lapor</span>
                            <span class="text-right small">{{ $ticket->created_at->format('d M Y') }}<br>{{ $ticket->created_at->format('H:i') }} WIB</span>
                        </li>

                        {{-- TEKNISI --}}
                        <li class="list-group-item">
                            <span class="small font-weight-bold d-block mb-1">Teknisi Bertugas</span>
                            @if($ticket->teknisi)
                                <div class="d-flex align-items-center">
                                    <div class="btn-circle btn-sm btn-primary mr-2">
                                        <i class="fas fa-user-cog"></i>
                                    </div>
                                    <span class="font-weight-bold">{{ $ticket->teknisi->nama }}</span>
                                </div>
                            @else
                                <span class="text-muted font-italic small">Belum ditugaskan</span>
                            @endif
                        </li>
                    </ul>

                </div>
            </div>

            {{-- JIKA ADA ASET TERKAIT --}}
            @if($ticket->barang_masuk_id)
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light">
                    <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-cube mr-1"></i> Aset Terkait</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold text-primary">{{ $ticket->aset->masterBarang->nama_barang ?? '-' }}</h6>
                    <small class="text-muted d-block">Kode: <strong>{{ $ticket->aset->kode_asset }}</strong></small>
                    <small class="text-muted d-block">SN: {{ $ticket->aset->serial_number }}</small>
                </div>
            </div>
            @endif

        </div>

        {{-- ========================================== --}}
        {{-- KOLOM KANAN: DETAIL MASALAH & SOLUSI --}}
        {{-- ========================================== --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rincian Masalah</h6>
                </div>
                <div class="card-body">
                    
                    {{-- JUDUL & DESKRIPSI --}}
                    <h4 class="font-weight-bold text-dark mb-3">{{ $ticket->judul_masalah }}</h4>
                    <div class="p-3 bg-light rounded border mb-4">
                        <p class="mb-0 text-gray-800" style="white-space: pre-line;">{{ $ticket->deskripsi }}</p>
                    </div>

                    {{-- FOTO MASALAH (DENGAN MODAL ZOOM) --}}
                    @if($ticket->foto_masalah)
                        <div class="mb-4">
                            <label class="font-weight-bold text-muted small">BUKTI FOTO:</label>
                            <div class="d-block">
                                <a href="#" data-toggle="modal" data-target="#photoModal">
                                    <img src="{{ asset('storage/' . $ticket->foto_masalah) }}" 
                                         class="img-thumbnail shadow-sm" 
                                         style="max-height: 200px; cursor: pointer;"
                                         alt="Foto Masalah">
                                </a>
                                <small class="d-block mt-1 text-muted"><i class="fas fa-search-plus"></i> Klik gambar untuk memperbesar</small>
                            </div>
                        </div>
                    @endif

                    <hr>

                    {{-- AREA SOLUSI (HANYA MUNCUL JIKA SELESAI) --}}
                    @if($ticket->status == 'Closed')
                        <div class="alert alert-success border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-start">
                                <div class="mr-3">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                                <div>
                                    <h5 class="alert-heading font-weight-bold">Laporan Selesai!</h5>
                                    <p class="mb-1"><strong>Solusi / Tindakan Teknisi:</strong></p>
                                    <p class="mb-2 bg-white p-2 rounded text-dark border border-success">
                                        {{ $ticket->solusi_teknisi }}
                                    </p>
                                    <hr class="my-2">
                                    <small class="mb-0">
                                        <i class="far fa-calendar-check"></i> Diselesaikan pada: 
                                        <strong>{{ \Carbon\Carbon::parse($ticket->tgl_selesai)->format('d F Y, H:i') }}</strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    @elseif($ticket->status == 'Ditolak')
                         <div class="alert alert-danger border-0 shadow-sm">
                            <h5 class="alert-heading font-weight-bold"><i class="fas fa-times-circle"></i> Laporan Ditolak</h5>
                            <p>{{ $ticket->solusi_teknisi ?? 'Laporan tidak valid atau sudah diselesaikan sebelumnya.' }}</p>
                         </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-tools fa-2x mb-2"></i>
                            <p>Menunggu tindakan perbaikan oleh teknisi.</p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- ========================================== --}}
            {{-- AREA FEEDBACK PENGGUNA --}}
            {{-- ========================================== --}}
            @if($ticket->status == 'Closed' && !$ticket->feedback)
                <div class="card shadow mb-4 border-left-info">
                    <div class="card-header py-3 bg-white">
                        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-star mr-1"></i> Berikan Penilaian Pelayanan</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info border-0 small">
                            <i class="fas fa-info-circle mr-1"></i> Mohon kesediaannya memberikan penilaian atas kinerja teknisi kami.
                        </div>
                        <form action="{{ route('pengguna.helpdesk.feedback', $ticket->id) }}" method="POST">
                            @csrf
                            
                            <div class="form-group mb-3">
                                <label for="rating"><strong>Rating (1-5 Bintang) <span class="text-danger">*</span></strong></label>
                                <select name="rating" id="rating" class="form-control" required>
                                    <option value="">-- Pilih Rating --</option>
                                    <option value="5">⭐⭐⭐⭐⭐ (Sangat Puas)</option>
                                    <option value="4">⭐⭐⭐⭐ (Puas)</option>
                                    <option value="3">⭐⭐⭐ (Cukup)</option>
                                    <option value="2">⭐⭐ (Kurang)</option>
                                    <option value="1">⭐ (Sangat Kurang)</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="feedback_text"><strong>Kritik & Saran (Opsional)</strong></label>
                                <textarea name="feedback_text" id="feedback_text" rows="3" class="form-control" placeholder="Tuliskan pengalaman atau saran Anda..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-info shadow-sm font-weight-bold">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Penilaian
                            </button>
                        </form>
                    </div>
                </div>
            @elseif($ticket->feedback)
                <div class="card shadow mb-4 border-left-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle fa-2x text-success mr-3"></i>
                            <div>
                                <h6 class="font-weight-bold text-success mb-0">Terima kasih atas penilaian Anda!</h6>
                                <p class="mb-0 text-muted small">Anda memberikan rating <strong>{{ $ticket->feedback->rating }} Bintang</strong>.</p>
                            </div>
                        </div>
                        
                        @if($ticket->feedback->feedback_text)
                            <div class="p-3 bg-light rounded mt-3">
                                <p class="mb-0 font-italic">"{{ $ticket->feedback->feedback_text }}"</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
            {{-- END AREA FEEDBACK --}}

        </div>

    </div>
</div>

{{-- MODAL UNTUK PREVIEW FOTO --}}
@if($ticket->foto_masalah)
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="photoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="photoModalLabel">Bukti Foto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center bg-dark">
                <img src="{{ asset('storage/' . $ticket->foto_masalah) }}" class="img-fluid" alt="Foto Full">
            </div>
        </div>
    </div>
</div>
@endif

@endsection