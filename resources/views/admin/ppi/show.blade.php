@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Tombol Kembali --}}
    <a href="{{ route('admin.ppi.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Monitoring
    </a>

    <div class="card shadow mb-4">
        {{-- Header Card dengan Status di pojok kanan --}}
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Detail Tiket: {{ $ppi->no_ppi }}</h6>
            
            {{-- Badge Status --}}
            @if($ppi->status == 'pending')
                <span class="badge badge-warning px-3 py-2">Status: Pending</span>
            @elseif($ppi->status == 'disetujui')
                <span class="badge badge-primary px-3 py-2">Status: Disetujui (Proses)</span>
            @elseif($ppi->status == 'selesai')
                <span class="badge badge-success px-3 py-2">Status: Selesai</span>
            @else
                <span class="badge badge-danger px-3 py-2">Status: Ditolak</span>
            @endif
        </div>

        <div class="card-body">
            <div class="row">
                {{-- ========================================== --}}
                {{-- KOLOM KIRI: INFORMASI PEMOHON (LENGKAP)    --}}
                {{-- ========================================== --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-gray-800 mb-3 border-bottom pb-2">Informasi Pemohon</h5>
                    
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="30%"><strong>Nama Pemohon</strong></td>
                            <td>: {{ $ppi->user->nama ?? 'User Terhapus' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jabatan</strong></td>
                            <td>: {{ $ppi->user->jabatan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Departemen</strong></td>
                            {{-- Cek departemen, kalau kosong cek divisi --}}
                            <td>: {{ $ppi->user->departemen ?? $ppi->user->divisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Perusahaan</strong></td>
                            <td>: {{ $ppi->user->perusahaan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tanggal Request</strong></td>
                            <td>: {{ date('d F Y', strtotime($ppi->tanggal)) }}</td>
                        </tr>
                    </table>
                </div>

                {{-- ========================================== --}}
                {{-- KOLOM KANAN: DETAIL KERUSAKAN & FILE       --}}
                {{-- ========================================== --}}
                <div class="col-md-6 border-left">
                    <h5 class="font-weight-bold text-gray-800 mb-3 border-bottom pb-2">Detail Permintaan</h5>

                    {{-- Perangkat --}}
                    <div class="form-group">
                        <label class="font-weight-bold text-primary">Perangkat:</label>
                        <input type="text" class="form-control bg-light" value="{{ $ppi->perangkat }}" readonly>
                    </div>

                    {{-- BA Kerusakan --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Berita Acara (BA) Kerusakan:</label>
                        <div class="alert alert-warning text-dark">
                            {{ $ppi->ba_kerusakan }}
                        </div>
                    </div>
                    
                    {{-- Keterangan Tambahan --}}
                    @if($ppi->keterangan)
                        <div class="form-group">
                            <label class="font-weight-bold">Catatan Tambahan:</label>
                            <p class="text-muted ml-2"><em>"{{ $ppi->keterangan }}"</em></p>
                        </div>
                    @endif

                    {{-- File Lampiran --}}
                    <div class="form-group mt-4">
                        <label class="font-weight-bold">Lampiran File/Foto:</label><br>
                        @if($ppi->file_ppi)
                            <a href="{{ asset('storage/' . $ppi->file_ppi) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-download"></i> Download / Lihat File
                            </a>
                        @else
                            <span class="text-muted font-italic">Tidak ada lampiran file.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER: TOMBOL AKSI ADMIN --}}
        <div class="card-footer text-right">
            @if($ppi->status == 'pending')
                {{-- Kalau Pending, Muncul Tombol TOLAK dan SETUJUI --}}
                <form action="{{ route('admin.ppi.update', $ppi->id) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    
                    <button name="status" value="ditolak" class="btn btn-danger mr-2" onclick="return confirm('Yakin ingin menolak PPI ini?')">
                        <i class="fas fa-times"></i> Tolak
                    </button>

                    <button name="status" value="disetujui" class="btn btn-primary" onclick="return confirm('Setujui permintaan ini?')">
                        <i class="fas fa-check"></i> Setujui & Proses
                    </button>
                </form>

            @elseif($ppi->status == 'disetujui')
                {{-- Kalau Sedang Proses, Muncul Tombol SELESAI --}}
                <form action="{{ route('admin.ppi.update', $ppi->id) }}" method="POST" class="d-inline">
                    @csrf @method('PUT')
                    <button name="status" value="selesai" class="btn btn-success" onclick="return confirm('Tandai permintaan ini sebagai SELESAI?')">
                        <i class="fas fa-check-double"></i> Tandai Selesai
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection