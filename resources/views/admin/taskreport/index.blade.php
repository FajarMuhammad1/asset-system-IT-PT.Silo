@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER: JUDUL DI KIRI, TOMBOL EXPORT DI KANAN --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        
        {{-- TOMBOL PEMICU MODAL --}}
        <button type="button" class="btn btn-sm btn-success shadow-sm" data-toggle="modal" data-target="#modalTaskExport">
            <i class="fas fa-file-excel fa-sm mr-2 text-white-50"></i>Export Excel
        </button>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">

            {{-- WRAP TABEL AGAR RESPONSIVE --}}
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Staff / Teknisi</th>
                            <th>Judul Pekerjaan</th>
                            <th>Tiket (Jika Ada)</th>
                            <th>Durasi</th>
                            <th>Tanggal</th>
                            <th>Lampiran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($reports as $item)
                        <tr>
                            {{-- Nama Staff --}}
                            <td>{{ $item->staff->name ?? $item->staff->nama ?? 'Unknown' }}</td>

                            <td>{{ $item->judul ?? $item->pekerjaan }}</td>

                            <td>
                                @if($item->ticket)
                                    <a href="#" class="badge badge-info">{{ $item->ticket->no_tiket }}</a>
                                @else
                                    <span class="badge badge-secondary">Non-Tiket</span>
                                @endif
                            </td>

                            <td>
                                {{-- Durasi Jam & Menit --}}
                                @php
                                    $start = \Carbon\Carbon::parse($item->tanggal_mulai ?? $item->created_at);
                                    $end = \Carbon\Carbon::parse($item->tanggal_selesai ?? $item->created_at);
                                    
                                    // Hitung durasi jika ada tanggal selesai
                                    if($item->tanggal_selesai) {
                                        $total = $start->diffInMinutes($end);
                                        $h = intdiv($total, 60);
                                        $m = $total % 60;
                                    } else {
                                        $h = 0; $m = 0;
                                    }
                                @endphp

                                @if($item->tanggal_selesai)
                                    @if($h > 0) {{ $h }} Jam @endif
                                    @if($m > 0) {{ $m }} Menit @endif
                                    @if($h == 0 && $m == 0) < 1 Menit @endif
                                @else
                                    <span class="text-warning"><i class="fas fa-clock"></i> Proses</span>
                                @endif
                            </td>

                            <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>

                            <td>
                                @if($item->lampiran)
                                    <a href="{{ asset('storage/'.$item->lampiran) }}" target="_blank" class="btn btn-sm btn-info">
                                        <i class="fas fa-paperclip"></i> File
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ route('taskreport.show', $item->id) }}" class="btn btn-sm btn-primary">
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

{{-- MODAL FILTER EXPORT TASK REPORT (DITARUH DI BAWAH) --}}
<div class="modal fade" id="modalTaskExport" tabindex="-1" role="dialog" aria-labelledby="modalTaskLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalTaskLabel"><i class="fas fa-filter"></i> Laporan Kinerja Tim IT</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            {{-- Pastikan Route ini sesuai dengan web.php --}}
            <form action="{{ route('admin.task_report.export') }}" method="GET">
                <div class="modal-body">
                    
                    {{-- 1. PILIH BULAN --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Bulan</label>
                        <select name="bulan" class="form-control">
                            <option value="">-- Semua Bulan --</option>
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                    {{ date("F", mktime(0, 0, 0, $i, 10)) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- 2. PILIH TAHUN --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Tahun</label>
                        <select name="tahun" class="form-control">
                            @php $tahun_sekarang = date('Y'); @endphp
                            @for ($y = $tahun_sekarang; $y >= $tahun_sekarang - 3; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- 3. PILIH TEKNISI (STAFF) --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Teknisi (Opsional)</label>
                        <select name="staff_id" class="form-control">
                            <option value="semua">-- Semua Teknisi --</option>
                            
                            {{-- LOGIC PHP: Ambil Staff yang pernah bikin Task Report --}}
                            @php
                                $staff_ids = \App\Models\TaskReport::distinct()->pluck('staff_id');
                                $staff_list = \App\Models\User::whereIn('id', $staff_ids)->get();
                            @endphp

                            @foreach($staff_list as $s)
                                <option value="{{ $s->id }}">{{ $s->name ?? $s->nama }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Pilih nama untuk melihat laporan kerja orang tertentu.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection