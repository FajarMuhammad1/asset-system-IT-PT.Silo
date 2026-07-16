@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800"><i class="fas fa-wallet mr-2 text-success"></i>{{ $title }}</h1>
        <a href="{{ route('admin.report.asset_value.print', request()->all()) }}" target="_blank" class="btn btn-primary shadow-sm">
            <i class="fas fa-print mr-1"></i> Cetak Laporan
        </a>
    </div>

    {{-- CARD STATISTIK NILAI ASET --}}
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Nilai Kapitalisasi Aset</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Kuantitas Aset Tercatat</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $totalQty }} Unit Aset</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-dark"><i class="fas fa-filter mr-1"></i> Panel Penyaringan Data</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report.asset_value') }}" method="GET">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Dari Tanggal Pembelian</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Sampai Tanggal Pembelian</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="font-weight-bold">Status Kondisi Aset</label>
                        <select name="status" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="Aktif" {{ $selectedStatus == 'Aktif' ? 'selected' : '' }}>Aktif / Baik</option>
                            <option value="Rusak" {{ $selectedStatus == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                            <option value="Disposal" {{ $selectedStatus == 'Disposal' ? 'selected' : '' }}>Disposal / Dihapuskan</option>
                        </select>
                    </div>
                </div>
                <div class="text-right">
                    <a href="{{ route('admin.report.asset_value') }}" class="btn btn-secondary mr-1"><i class="fas fa-undo mr-1"></i> Reset</a>
                    <button type="submit" class="btn btn-success"><i class="fas fa-search mr-1"></i> Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th width="50">No</th>
                            <th>Kode/No Aset</th>
                            <th>Nama Barang</th>
                            <th>Merek/Tipe</th>
                            <th class="text-center">Tgl Beli</th>
                            <th class="text-center">Status</th>
                            <th class="text-right">Harga Perolehan (Nilai Beli)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $index => $asset)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold text-gray-800">{{ $asset->kode_barang ?? $asset->no_aset }}</td>
                                <td>{{ $asset->nama_barang }}</td>
                                <td>{{ $asset->merek ?? '-' }}</td>
                                <td class="text-center">{{ $asset->tgl_beli ? \Carbon\Carbon::parse($asset->tgl_beli)->format('d-m-Y') : '-' }}</td>
                                <td class="text-center">
                                    <span class="badge badge-{{ $asset->status == 'Aktif' ? 'success' : ($asset->status == 'Rusak' ? 'danger' : 'secondary') }} px-2 py-1">
                                        {{ $asset->status ?? 'Aktif' }}
                                    </span>
                                </td>
                                <td class="text-right font-weight-bold text-dark">Rp {{ number_format($asset->harga_beli, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted font-italic">Tidak ada data nilai aset yang sesuai dengan kriteria filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($assets->isNotEmpty())
                    <tfoot class="bg-light font-weight-bold text-dark">
                        <tr>
                            <td colspan="6" class="text-right h5 font-weight-bold">TOTAL NILAI KESELURUHAN ASET:</td>
                            <td class="text-right h5 font-weight-bold text-success">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

</div>
@endsection