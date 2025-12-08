@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="{{ route('staff.reports.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke List
        </a>
    </div>

    <div class="row">

        <!-- KOLOM KIRI: Informasi Waktu & Referensi -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Kegiatan</h6>
                </div>
                <div class="card-body">
                    
                    {{-- Status Tiket --}}
                    <div class="mb-4">
                        <label class="small font-weight-bold text-gray-500">TIKET TERKAIT</label>
                        @if($report->ticket)
                            <div class="p-2 bg-light rounded border border-left-primary">
                                <a href="{{ route('staff.helpdesk.show', $report->ticket->id) }}" class="font-weight-bold text-primary text-decoration-none">
                                    {{ $report->ticket->no_tiket }}
                                </a>
                                <div class="small text-gray-600 mt-1">
                                    {{ Str::limit($report->ticket->judul_masalah, 50) }}
                                </div>
                            </div>
                        @else
                            <div class="p-2 bg-light rounded text-muted font-italic">
                                Tidak ada tiket (Pekerjaan Rutin/Non-Tiket)
                            </div>
                        @endif
                    </div>

                    {{-- Waktu Mulai --}}
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">WAKTU MULAI</label>
                        <h6 class="font-weight-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($report->tanggal_mulai)->format('d F Y') }}
                            <br>
                            <span class="text-primary">{{ \Carbon\Carbon::parse($report->tanggal_mulai)->format('H:i') }} WIB</span>
                        </h6>
                    </div>

                    {{-- Waktu Selesai --}}
                    <div class="mb-3">
                        <label class="small font-weight-bold text-gray-500">WAKTU SELESAI</label>
                        <h6 class="font-weight-bold text-gray-800">
                            {{ \Carbon\Carbon::parse($report->tanggal_selesai)->format('d F Y') }}
                            <br>
                            <span class="text-success">{{ \Carbon\Carbon::parse($report->tanggal_selesai)->format('H:i') }} WIB</span>
                        </h6>
                    </div>

                    {{-- Durasi --}}
                    <hr>
                    <div class="mb-0">
                        <label class="small font-weight-bold text-gray-500">TOTAL DURASI</label>
                         @php
                            $start = \Carbon\Carbon::parse($report->tanggal_mulai);
                            $end = \Carbon\Carbon::parse($report->tanggal_selesai);
                            
                            // Hitung total menit selisih
                            $totalMinutes = $start->diffInMinutes($end);
                            
                            // Konversi ke Jam dan Menit
                            $hours = intdiv($totalMinutes, 60);
                            $minutes = $totalMinutes % 60;
                        @endphp
                        <h5 class="font-weight-bold text-dark">
                        @if($hours > 0)
                                {{ $hours }} Jam
                            @endif
                            
                            @if($minutes > 0)
                                {{ $minutes }} Menit
                            @endif
                            
                            {{-- Kalau durasinya kurang dari 1 menit --}}
                            @if($hours == 0 && $minutes == 0)
                                < 1 Menit
                       @endif
                        </h5>
                    </div>

                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Detail Pekerjaan -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Detail Laporan</h6>
                </div>
                <div class="card-body">
                    
                    {{-- Judul --}}
                    <div class="mb-4">
                        <label class="small font-weight-bold text-gray-500">JUDUL KEGIATAN</label>
                        <h4 class="font-weight-bold text-gray-800">{{ $report->judul }}</h4>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label class="small font-weight-bold text-gray-500">DESKRIPSI PEKERJAAN</label>
                        <div class="p-3 bg-gray-100 rounded text-gray-800" style="white-space: pre-line;">
                            {{ $report->deskripsi }}
                        </div>
                    </div>

                    {{-- Hasil --}}
                    <div class="mb-4">
                        <label class="small font-weight-bold text-gray-500">HASIL / CAPAIAN</label>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle mr-2"></i> {{ $report->hasil ?? 'Tidak ada catatan hasil.' }}
                        </div>
                    </div>

                    {{-- Lampiran --}}
                    @if($report->lampiran)
                        <hr>
                        <div class="mb-2">
                            <label class="small font-weight-bold text-gray-500">LAMPIRAN BUKTI</label>
                        </div>
                        
                        @php
                            $extension = pathinfo($report->lampiran, PATHINFO_EXTENSION);
                        @endphp

                        @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                            {{-- Jika Gambar, Tampilkan Preview --}}
                            <div class="text-center bg-dark p-3 rounded">
                                <img src="{{ asset('storage/' . $report->lampiran) }}" class="img-fluid" style="max-height: 400px;" alt="Bukti Laporan">
                            </div>
                        @else
                            {{-- Jika Dokumen (PDF, dll), Tampilkan Tombol Download --}}
                            <div class="card border-left-info py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">File Lampiran</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ basename($report->lampiran) }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ asset('storage/' . $report->lampiran) }}" target="_blank" class="btn btn-info btn-icon-split">
                                                <span class="icon text-white-50">
                                                    <i class="fas fa-download"></i>
                                                </span>
                                                <span class="text">Unduh / Lihat</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
        

    </div>

</div>
@endsection