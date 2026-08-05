@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-money-bill-wave mr-2 text-success"></i> {{ $title ?? 'Rekap Biaya Operasional Staff' }}
        </h1>
    </div>

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
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jumlah Staff Tercatat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $rekapPerStaff->count() }} Staff
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        <label for="staff_id">Staff</label>
                        <select name="staff_id" id="staff_id" class="form-control">
                            <option value="">Semua Staff</option>
                            @foreach($staffList as $staff)
                                <option value="{{ $staff->id }}" {{ (string) $staffId === (string) $staff->id ? 'selected' : '' }}>
                                    {{ $staff->nama ?? $staff->name }}
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

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-users mr-1"></i> Rekap per Staff
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>No</th>
                            <th>Nama Staff</th>
                            <th>Jumlah Tiket</th>
                            <th>Total Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rekapPerStaff as $row)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $row['staff']->nama ?? $row['staff']->name ?? '-' }}</td>
                                <td class="text-center">{{ $row['jumlah_tiket'] }}</td>
                                <td class="text-right font-weight-bold text-success">Rp {{ number_format($row['total_nominal'], 0, ',', '.') }}</td>
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
                            <th>Staff</th>
                            <th>Diberikan Oleh</th>
                            <th>Keterangan</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataRekap as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">{{ optional($item->tanggal_pemberian)->format('d-m-Y') }}</td>
                                <td>
                                    @if($item->ticket)
                                        <a href="{{ route('admin.helpdesk.show', $item->ticket->id) }}">
                                            {{ $item->ticket->no_tiket }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $item->staff->nama ?? $item->staff->name ?? '-' }}</td>
                                <td>{{ $item->pemberi->nama ?? $item->pemberi->name ?? '-' }}</td>
                                <td>{{ $item->keterangan ?: '-' }}</td>
                                <td class="text-right font-weight-bold">Rp {{ number_format($item->nominal, 0, ',', '.') }}</td>
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
