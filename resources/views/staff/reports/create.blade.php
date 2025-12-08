@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Laporan Pekerjaan</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('staff.reports.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label>Judul Kegiatan / Pekerjaan</label>
                    <input type="text" name="judul" class="form-control" required placeholder="Contoh: Maintenance Server Rutin">
                </div>

                <div class="form-group">
                    <label>Tiket Terkait (Opsional)</label>
                    <select name="ticket_id" class="form-control">
                        <option value="">-- Tidak Ada / Pekerjaan Non-Tiket --</option>
                        @foreach($closedTickets as $ticket)
                            <option value="{{ $ticket->id }}">
                                {{ $ticket->no_tiket }} - {{ Str::limit($ticket->judul_masalah, 50) }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilih jika laporan ini berdasarkan tiket yang sudah Anda selesaikan.</small>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Waktu Mulai</label>
                            <input type="datetime-local" name="tanggal_mulai" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Waktu Selesai</label>
                            <input type="datetime-local" name="tanggal_selesai" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Deskripsi Pekerjaan</label>
                    <textarea name="deskripsi" class="form-control" rows="3" required placeholder="Jelaskan apa yang dikerjakan..."></textarea>
                </div>

                <div class="form-group">
                    <label>Hasil / Capaian</label>
                    <textarea name="hasil" class="form-control" rows="2" placeholder="Contoh: Server kembali normal, suhu stabil..."></textarea>
                </div>

                <div class="form-group">
                    <label>Lampiran Bukti (Foto/Dokumen)</label>
                    <input type="file" name="lampiran" class="form-control-file">
                </div>

                <hr>
                <button type="submit" class="btn btn-primary">Simpan Laporan</button>
                <a href="{{ route('staff.reports.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>
@endsection