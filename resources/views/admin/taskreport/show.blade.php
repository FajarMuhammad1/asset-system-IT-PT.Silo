@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow">
        <div class="card-body">

            <h4 class="font-weight-bold">{{ $report->judul }}</h4>
            <p class="text-muted">Oleh: {{ $report->staff->name }}</p>

            @if($report->ticket)
                <p><strong>Tiket Terkait:</strong> {{ $report->ticket->no_tiket }}</p>
            @endif

            <hr>

            <h5 class="font-weight-bold">Deskripsi</h5>
            <p>{{ $report->deskripsi }}</p>
            
            <h5 class="font-weight-bold">Hasil</h5>
            <p>{{ $report->hasil }}</p>

            <h5 class="mt-4 font-weight-bold">Durasi Pengerjaan</h5>

            @php
                $start = \Carbon\Carbon::parse($report->tanggal_mulai);
                $end = \Carbon\Carbon::parse($report->tanggal_selesai);
                $total = $start->diffInMinutes($end);
                $h = intdiv($total, 60);
                $m = $total % 60;
            @endphp

            <p>
                @if($h > 0) {{ $h }} Jam @endif
                @if($m > 0) {{ $m }} Menit @endif
                @if($h == 0 && $m == 0) < 1 Menit @endif
            </p>

            @if($report->lampiran)
                <h5 class="mt-4 font-weight-bold">Lampiran</h5>
                <a href="{{ asset('storage/'.$report->lampiran) }}" target="_blank" class="btn btn-info">
                    <i class="fas fa-file"></i> Lihat Lampiran
                </a>
            @endif

        </div>
    </div>
    <a href="{{ route('taskreport.index') }}" class="btn btn-secondary mt-3">
                <i class="fas fa-arrow-left"></i> Kembali 
            </a>

</div>
@endsection
