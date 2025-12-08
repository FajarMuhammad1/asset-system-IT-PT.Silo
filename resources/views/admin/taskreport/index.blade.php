@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow">
        <div class="card-body">

            {{-- WRAP TABEL AGAR RESPONSIVE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>Staff</th>
                            <th>Judul</th>
                            <th>Tiket</th>
                            <th>Durasi</th>
                            <th>Tanggal</th>
                            <th>Lampiran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($reports as $item)
                        <tr>
                            <td>{{ $item->staff->nama }}</td>

                            <td>{{ $item->judul }}</td>

                            <td>
                                @if($item->ticket)
                                    <span class="badge badge-info">{{ $item->ticket->no_tiket }}</span>
                                @else
                                    <span class="badge badge-secondary">Tidak Ada</span>
                                @endif
                            </td>

                            <td>
                                {{-- Durasi Jam & Menit --}}
                                @php
                                    $start = \Carbon\Carbon::parse($item->tanggal_mulai);
                                    $end = \Carbon\Carbon::parse($item->tanggal_selesai);
                                    $total = $start->diffInMinutes($end);
                                    $h = intdiv($total, 60);
                                    $m = $total % 60;
                                @endphp

                                @if($h > 0) {{ $h }} Jam @endif
                                @if($m > 0) {{ $m }} Menit @endif
                                @if($h == 0 && $m == 0) < 1 Menit @endif
                            </td>

                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>

                            <td>
                                @if($item->lampiran)
                                    <a href="{{ asset('storage/'.$item->lampiran) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-file"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('taskreport.show', $item->id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div> {{-- END TABLE RESPONSIVE --}}

        </div>
    </div>

</div>
@endsection
