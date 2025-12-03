@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <a href="{{ route('pengguna.helpdesk.index') }}" class="btn btn-secondary mb-3"><i class="fas fa-arrow-left"></i> Kembali</a>

    <div class="row">
        {{-- Kolom Kiri: Status & Info --}}
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Info Tiket</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">NOMOR TIKET</label>
                        <h5 class="font-weight-bold">{{ $ticket->no_tiket }}</h5>
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">STATUS</label><br>
                        @if($ticket->status == 'Open') <span class="badge badge-warning p-2">Open</span>
                        @elseif($ticket->status == 'Progres') <span class="badge badge-info p-2">Sedang Dikerjakan</span>
                        @elseif($ticket->status == 'Closed') <span class="badge badge-success p-2">Selesai</span>
                        @else <span class="badge badge-danger p-2">Ditolak</span> @endif
                    </div>
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">TANGGAL LAPOR</label>
                        <p>{{ $ticket->created_at->format('d F Y, H:i') }}</p>
                    </div>
                    <div>
                        <label class="small font-weight-bold text-gray-500">TEKNISI</label>
                        <p>{{ $ticket->teknisi->name ?? 'Belum ditugaskan' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Detail Masalah --}}
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Masalah</h6>
                </div>
                <div class="card-body">
                    <h4 class="font-weight-bold mb-3">{{ $ticket->judul_masalah }}</h4>
                    
                    @if($ticket->barang_masuk_id)
                    <div class="alert alert-info">
                        <i class="fas fa-cube mr-1"></i> Aset Terkait: <strong>{{ $ticket->aset->masterBarang->nama_barang }}</strong> (Kode: {{ $ticket->aset->kode_asset }})
                    </div>
                    @endif

                    <hr>
                    <p class="text-gray-800" style="white-space: pre-line;">{{ $ticket->deskripsi }}</p>

                    @if($ticket->foto_masalah)
                        <hr>
                        <label class="font-weight-bold">Bukti Foto:</label><br>
                        <img src="{{ asset('storage/' . $ticket->foto_masalah) }}" class="img-fluid rounded border" style="max-height: 400px;">
                    @endif

                    @if($ticket->status == 'Closed')
                        <div class="alert alert-success mt-4">
                            <h5 class="alert-heading"><i class="fas fa-check-circle"></i> Masalah Diselesaikan!</h5>
                            <p class="mb-0"><strong>Solusi Teknisi:</strong> {{ $ticket->solusi_teknisi }}</p>
                            <small class="text-muted">Diselesaikan pada: {{ \Carbon\Carbon::parse($ticket->tgl_selesai)->format('d M Y, H:i') }}</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection