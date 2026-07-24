@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice-dollar mr-2"></i> {{ $title ?? 'Laporan Nilai Aset' }}
        </h1>
        
        <!-- Tombol Print (Mengirimkan parameter filter yang sedang aktif) -->
        <a href="{{ route('admin.report.asset_value_print', request()->query()) }}" target="_blank" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Cetak Laporan
        </a>
    </div>

    <!-- Papan Ringkasan (Summary Cards) -->
    <div class="row">
        <!-- Total Kuantitas Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kuantitas Aset Terfilter</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalQty, 0, ',', '.') }} Unit</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Nilai Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Nilai Aset Terfilter (Rp)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalValue, 0, ',', '.') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Filter Laporan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-1"></i> Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report.asset_value') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="start_date">Dari Tanggal (Tgl Beli)</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="end_date">Sampai Tanggal (Tgl Beli)</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="status">Status Kondisi Aset</label>
                        <select class="form-control" name="status" id="status">
                            <option value="">-- Semua Status --</option>
                            <option value="baik" {{ $selectedStatus == 'baik' ? 'selected' : '' }}>Baik</option>
                            <option value="rusak ringan" {{ $selectedStatus == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="rusak berat" {{ $selectedStatus == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 mr-1"><i class="fas fa-search"></i> Filter</button>
                        <a href="{{ route('admin.report.asset_value') }}" class="btn btn-secondary"><i class="fas fa-sync-alt"></i></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTabel Laporan -->
    <div class="card shadow mb-4 border-bottom-success">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-success">Rincian Data Aset</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" width="100%" cellspacing="0">
                    <thead class="bg-success text-white text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>Kode/SN Aset</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Tgl Beli</th>
                            <th>Status</th>
                            <th>Harga Beli (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assets as $asset)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $asset->kode_aset ?? $asset->serial_number ?? '-' }}</td>
                            <td class="font-weight-bold">{{ $asset->nama_barang }}</td>
                            <td class="text-center">{{ strtoupper($asset->kategori) }}</td>
                            <td class="text-center">{{ $asset->tgl_beli ? \Carbon\Carbon::parse($asset->tgl_beli)->format('d-m-Y') : '-' }}</td>
                            <td class="text-center">
                                @if(strtolower($asset->status) == 'baik')
                                    <span class="badge badge-success px-2 py-1">Baik</span>
                                @elseif(strtolower($asset->status) == 'rusak ringan')
                                    <span class="badge badge-warning px-2 py-1">Rusak Ringan</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1">{{ ucfirst($asset->status) }}</span>
                                @endif
                            </td>
                            <td class="text-right font-weight-bold">
                                {{ number_format($asset->harga_beli, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle mb-2"></i><br>
                                Tidak ada data aset ditemukan pada rentang filter ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="6" class="text-right font-weight-bold">TOTAL NILAI ASET:</th>
                            <th class="text-right font-weight-bold text-success h6 mb-0">Rp {{ number_format($totalValue, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Inisialisasi DataTable -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var initDataTables = setInterval(function() {
            if (window.jQuery && $.fn.DataTable) {
                clearInterval(initDataTables);
                $('.datatable').DataTable({
                    "language": {
                        "search": "Cari aset:",
                        "emptyTable": "Data kosong"
                    }
                });
            }
        }, 100);
    });
</script>
@endsection