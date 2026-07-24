@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-smile mr-2"></i> {{ $title ?? 'Laporan Kepuasan Pengguna' }}
        </h1>
        
        <!-- Tombol Cetak dengan SweetAlert Konfirmasi -->
        <button type="button" 
                class="btn btn-sm btn-danger shadow-sm btn-cetak-swal"
                data-url="{{ route('admin.report.feedback.print', request()->query()) }}"
                data-title="Cetak Laporan Kepuasan Pengguna"
                data-desc="Anda akan mencetak laporan kepuasan pengguna helpdesk periode:<br><strong>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</strong><br><br>Halaman cetak akan terbuka di tab baru dan otomatis menampilkan dialog print browser. Lanjutkan?"
                data-icon="info"
                data-confirm="Ya, Cetak Sekarang"
                data-target="_blank">
            <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Cetak Laporan
        </button>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Feedback -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Responden (Feedback Terisi)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalFeedback, 0, ',', '.') }} Tiket
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Rata-rata Rating Kepuasan</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($averageRating, 2, ',', '.') }} 
                                <small class="text-muted">/ 5.00</small>
                            </div>
                            <div class="mt-1">
                                @for($i=1;$i<=5;$i++)
                                    @if($i <= round($averageRating))
                                        <i class="fas fa-star text-warning"></i>
                                    @else
                                        <i class="far fa-star text-gray-300"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-1"></i> Filter Periode Laporan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.report.feedback') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4 mb-3">
                        <label for="start_date">Dari Tanggal (Tgl Selesai Tiket)</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="end_date">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 mr-2"><i class="fas fa-search"></i> Terapkan Filter</button>
                        <a href="{{ route('admin.report.feedback') }}" class="btn btn-secondary"><i class="fas fa-sync-alt"></i></a>
                    </div>
                </div>
                <p class="small text-muted mb-0">
                    <i class="fas fa-info-circle mr-1"></i>
                    Laporan hanya menampilkan tiket yang sudah <strong>selesai</strong> dan sudah <strong>diisi feedback/penilaian</strong> oleh pengguna.
                </p>
            </form>
        </div>
    </div>

    <!-- DataTable Feedback -->
    <div class="card shadow mb-4 border-bottom-warning">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">Rincian Data Feedback Kepuasan Pengguna</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover datatable" width="100%" cellspacing="0">
                    <thead class="bg-warning text-white text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>No Tiket</th>
                            <th>Judul / Masalah</th>
                            <th>Pelapor</th>
                            <th>Teknisi</th>
                            <th>Tgl Selesai</th>
                            <th>Rating</th>
                            <th>Komentar Pengguna</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $tik)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center"><span class="badge badge-primary">{{ $tik->no_tiket }}</span></td>
                            <td class="font-weight-bold">{{ $tik->judul ?? $tik->masalah ?? '-' }}</td>
                            <td>{{ $tik->pelapor->name ?? $tik->pelapor->nama ?? '-' }}</td>
                            <td>{{ $tik->teknisi->name ?? $tik->teknisi->nama ?? '-' }}</td>
                            <td class="text-center">
                                {{ $tik->tgl_selesai ? \Carbon\Carbon::parse($tik->tgl_selesai)->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td class="text-center">
                                @php $r = $tik->feedback->rating ?? 0; @endphp
                                <div class="mb-1">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="fas fa-star {{ $i<=$r ? 'text-warning' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <small class="font-weight-bold text-{{ $r>=4 ? 'success' : ($r==3 ? 'warning' : 'danger') }}">
                                    {{ $r }} / 5
                                </small>
                            </td>
                            <td>
                                @if(!empty($tik->feedback->komentar))
                                    <small>{{ $tik->feedback->komentar }}</small>
                                @else
                                    <span class="text-muted font-italic">- Tidak ada komentar -</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-info-circle mb-2 fa-2x"></i><br>
                                Tidak ada data feedback untuk rentang tanggal yang dipilih.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light">
                        <tr>
                            <th colspan="5" class="text-right font-weight-bold">RATA-RATA RATING KEPUASAN:</th>
                            <th class="text-center font-weight-bold text-warning h6 mb-0">
                                {{ number_format($averageRating, 2, ',', '.') }} / 5.00
                            </th>
                            <th colspan="2" class="text-right font-weight-bold">
                                Total: {{ $totalFeedback }} Feedback
                            </th>
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
                        "search": "Cari feedback:",
                        "emptyTable": "Data kosong"
                    },
                    "pageLength": 25,
                    "order": [[ 5, "desc" ]]
                });
            }
        }, 100);
    });
</script>
@endsection
