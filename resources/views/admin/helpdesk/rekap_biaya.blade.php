@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-money-bill-wave mr-2 text-success"></i> {{ $title ?? 'Rekap Biaya Operasional Teknisi' }}
        </h1>
        
        <!-- TOMBOL CETAK LAPORAN DITAMBAHKAN DI SINI -->
        <a href="{{ route('admin.helpdesk.biaya.cetak', ['bulan' => $bulan, 'tahun' => $tahun, 'staff_id' => $staffId]) }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" target="_blank">
            <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Cetak Laporan
        </a>
    </div>

    <!-- NOTIFIKASI -->
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
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- WIDGET STATISTIK -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Grand Total Biaya</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        Rp {{ number_format($grandTotal ?? 0, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Entri Biaya</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $dataRekap->count() }} Item
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Teknisi Tercatat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $rekapPerStaff->count() }} Teknisi
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM INPUT BIAYA OPERASIONAL -->
    <div class="card shadow mb-4 border-left-primary">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-plus-circle mr-1"></i> Input Biaya Operasional Baru
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.helpdesk.biaya.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tanggal_pemberian">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_pemberian" id="tanggal_pemberian" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="input_staff_id">Teknisi <span class="text-danger">*</span></label>
                        <select name="staff_id" id="input_staff_id" class="form-control" required>
                            <option value="">-- Pilih Teknisi --</option>
                            @foreach($staffList as $staff)
                                <option value="{{ $staff->id }}">
                                    {{ $staff->name ?? $staff->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <!-- UBAH JADI ANGKA: Agar sesuai dengan tipe data foreign key ID -->
                        <label for="ticket_id">ID Tiket (Opsional, Masukkan Angka ID)</label>
                        <input type="number" name="ticket_id" id="ticket_id" class="form-control" placeholder="Contoh ID: 15">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nominal">Nominal (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="nominal" id="nominal" class="form-control" placeholder="Contoh: 50000" min="0" required>
                    </div>
                    <div class="col-md-10 mb-3">
                        <label for="keterangan">Keterangan <span class="text-danger">*</span></label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Contoh: Beli bensin, dll" required>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- FILTER DATA -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-1"></i> Filter Rekap
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.helpdesk.biaya') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3 mb-3">
                        <label for="bulan">Bulan</label>
                        <select name="bulan" id="bulan" class="form-control">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}" {{ (string) $bulan === str_pad($m, 2, '0', STR_PAD_LEFT) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="tahun">Tahun</label>
                        <select name="tahun" id="tahun" class="form-control">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ (string) $tahun === (string) $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="staff_id">Teknisi / Staff</label>
                        <select name="staff_id" id="staff_id" class="form-control">
                            <option value="">Semua Teknisi</option>
                            @foreach($staffList as $staff)
                                <option value="{{ $staff->id }}" {{ (string) $staffId === (string) $staff->id ? 'selected' : '' }}>
                                    {{ $staff->name ?? $staff->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i> Tampilkan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- REKAP PER TEKNISI -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-users mr-1"></i> Rekap per Teknisi
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Teknisi</th>
                            <th>Jumlah Tiket</th>
                            <th>Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapPerStaff as $row)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <!-- Sesuai relasi staff() di Model -->
                                <td>{{ $row['staff']->name ?? $row['staff']->nama ?? 'Unknown' }}</td>
                                <td class="text-center">{{ $row['jumlah_tiket'] }}</td>
                                <td class="text-right font-weight-bold text-success">
                                    Rp {{ number_format($row['total_nominal'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada data biaya operasional pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DETAIL BIAYA -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list mr-1"></i> Detail Biaya Operasional
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>No Tiket</th>
                            <th>Teknisi</th>
                            <th>Diberikan Oleh</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataRekap as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    {{ $item->tanggal_pemberian ? $item->tanggal_pemberian->format('d-m-Y') : '-' }}
                                </td>
                                <td>
                                    <!-- Sesuai relasi ticket() di Model -->
                                    @if($item->ticket)
                                        <a href="{{ route('admin.helpdesk.show', $item->ticket->id) }}">
                                            {{ $item->ticket->no_tiket ?? 'Lihat Tiket' }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <!-- Sesuai relasi staff() di Model -->
                                <td>{{ $item->staff->name ?? $item->staff->nama ?? '-' }}</td>
                                <!-- Sesuai relasi pemberi() di Model -->
                                <td>{{ $item->pemberi->name ?? $item->pemberi->nama ?? '-' }}</td>
                                <!-- Sesuai kolom keterangan & nominal di Model -->
                                <td>{{ $item->keterangan ?: '-' }}</td>
                                <td class="text-right font-weight-bold">
                                    Rp {{ number_format($item->nominal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Tidak ada data biaya operasional.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection