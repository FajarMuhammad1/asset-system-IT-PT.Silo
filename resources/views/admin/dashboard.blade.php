@extends('layouts.app')

@section('title', $title)

@section('content')

{{-- STYLE TAMBAHAN --}}
<style>
    .card-hover-effect {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .card-hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
        text-decoration: none;
    }
    /* Scrollbar halus untuk widget list */
    .widget-scroll {
        max-height: 350px;
        overflow-y: auto;
    }
    .widget-scroll::-webkit-scrollbar { width: 5px; }
    .widget-scroll::-webkit-scrollbar-thumb { background: #e3e6f0; border-radius: 4px; }
</style>

{{-- HEADER --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> Data per {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</small>
    </div>
    
    {{-- Tombol Pintas --}}
    <div class="d-none d-sm-inline-block">
        <a href="{{ route('surat-jalan.create') }}" class="btn btn-sm btn-primary shadow-sm mr-2">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Surat Jalan
        </a>
        <a href="{{ route('barangkeluar.create') }}" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-handshake fa-sm text-white-50 mr-1"></i> Serah Terima (BAST)
        </a>
    </div>
</div>

{{-- BARIS 1: KARTU STATISTIK (LINK SUDAH SESUAI ROUTE WEB.PHP) --}}
<div class="row">

    {{-- Kartu Team IT --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('team') }}" class="card border-left-primary shadow h-100 py-2 card-hover-effect text-decoration-none">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Team IT (Staff)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teamCount ?? 0 }} <small class="text-muted" style="font-size: 0.6em">Personil</small></div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-primary text-white p-3 rounded-circle">
                            <i class="fas fa-users fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Kartu Pengguna --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('pengguna.index') }}" class="card border-left-success shadow h-100 py-2 card-hover-effect text-decoration-none">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Pengguna</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penggunaCount ?? 0 }} <small class="text-muted" style="font-size: 0.6em">User</small></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Kartu Aset --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('barangmasuk.index') }}" class="card border-left-info shadow h-100 py-2 card-hover-effect text-decoration-none">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Aset</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assetCount ?? 0 }} <small class="text-muted" style="font-size: 0.6em">Unit</small></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-laptop fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Kartu Tiket Open --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('admin.helpdesk.index') }}" class="card border-left-warning shadow h-100 py-2 card-hover-effect text-decoration-none">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Tiket (Open)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ticketOpenCount ?? 0 }} <small class="text-muted" style="font-size: 0.6em">Tiket</small></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-headset fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- BARIS 2: CHARTS --}}
<div class="row">

    {{-- AREA CHART --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line mr-1"></i> Tren Permintaan (PPI) {{ date('Y') }}</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- DONUT CHART --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary">Sebaran PPI per Dept</h6>
            </div>
            <div class="card-body">
                @if(empty($deptLabels))
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-pie fa-3x mb-3 text-gray-300"></i>
                        <p class="small">Belum ada data statistik.</p>
                    </div>
                @else
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small text-muted">
                        Data diambil dari 5 departemen teraktif.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- BARIS 3: WIDGET BAWAH --}}
<div class="row">

    {{-- KOLOM 1: TOP STAFF & BAST --}}
    <div class="col-lg-4">
        
        {{-- Top Staff --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3  d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-trophy mr-1"></i> Top 5 Staff IT</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center border-0" width="15%">#</th>
                                <th class="border-0">Nama Staff</th>
                                <th class="text-center border-0" width="20%">Task</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topTechnicians as $index => $tech)
                            <tr>
                                <td class="text-center align-middle">
                                    @if($index == 0) <i class="fas fa-medal text-warning fa-lg"></i>
                                    @elseif($index == 1) <i class="fas fa-medal text-secondary fa-lg"></i>
                                    @elseif($index == 2) <i class="fas fa-medal text-primary fa-lg" style="color: #cd7f32 !important;"></i>
                                    @else <span class="font-weight-bold text-gray-500">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold text-dark">{{ $tech->nama }}</td>
                                <td class="text-center align-middle"><span class="badge badge-success badge-pill">{{ $tech->total_task }}</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-3 small text-muted">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat BAST --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-1"></i> Serah Terima Terakhir</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush widget-scroll">
                    @forelse ($recentBast ?? [] as $bast)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="mr-2 overflow-hidden">
                            <div class="text-truncate font-weight-bold text-dark" style="max-width: 180px;">
                                {{ $bast->aset->masterBarang->nama_barang ?? 'Unknown Asset' }}
                            </div>
                            <div class="small text-muted text-truncate">
                                <i class="fas fa-user-circle mr-1"></i> {{ $bast->pemegang->nama ?? 'Tanpa Nama' }}
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-light border">{{ \Carbon\Carbon::parse($bast->created_at)->format('d M') }}</span>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-3 small">Belum ada riwayat.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- KOLOM 2: STATUS ASET (DENGAN LOGIKA WARNA) --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-heartbeat mr-1"></i> Kesehatan Stok Aset</h6>
            </div>
            <div class="card-body">
                @php
                    $totalAset = ($assetCount > 0) ? $assetCount : 1;
                    $persenDipakai = ($dipakaiCount / $totalAset) * 100;
                    $persenStok = ($stokCount / $totalAset) * 100;

                    // Logika Warna Stok
                    $stokColor = 'bg-success';
                    if($persenStok < 10) $stokColor = 'bg-danger'; // Kritis < 10%
                    elseif($persenStok < 30) $stokColor = 'bg-warning'; // Waspada < 30%
                @endphp

                {{-- Dipakai --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small font-weight-bold text-dark">Unit Dipakai <span class="text-muted">({{ $dipakaiCount }})</span></span>
                        <span class="small font-weight-bold text-primary">{{ round($persenDipakai) }}%</span>
                    </div>
                    <div class="progress progress-sm rounded">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $persenDipakai }}%"></div>
                    </div>
                </div>
                
                {{-- Stok (Warna Dinamis) --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small font-weight-bold text-dark">Stok / Backup <span class="text-muted">({{ $stokCount }})</span></span>
                        <span class="small font-weight-bold {{ str_replace('bg-', 'text-', $stokColor) }}">{{ round($persenStok) }}%</span>
                    </div>
                    <div class="progress progress-sm rounded">
                        <div class="progress-bar {{ $stokColor }}" role="progressbar" style="width: {{ $persenStok }}%"></div>
                    </div>
                    @if($persenStok < 10)
                        <div class="mt-2 small text-danger font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Stok menipis! Segera restock.</div>
                    @endif
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('barangmasuk.index') }}" class="btn btn-outline-info btn-sm btn-block">Lihat Detail Aset</a>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM 3: ACCORDION PPI --}}
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-building mr-1"></i> Detail PPI (Per PT)</h6>
            </div>
            <div class="card-body p-2 widget-scroll">
                @if(empty($ppiPerCompany))
                    <div class="text-center text-muted py-5 small">Belum ada data permintaan.</div>
                @else
                    <div class="accordion" id="accordionPPI">
                        @foreach($ppiPerCompany as $ptName => $data)
                            <div class="card border-0 mb-1">
                                <div class="card-header p-0 bg-white border-0" id="heading{{ $loop->index }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-light btn-block text-left d-flex justify-content-between align-items-center py-3" 
                                                type="button" data-toggle="collapse" data-target="#collapse{{ $loop->index }}">
                                            <span class="font-weight-bold text-primary small text-uppercase">{{ $ptName }}</span>
                                            <span class="badge badge-primary badge-pill">{{ $data['total_company'] }}</span>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse{{ $loop->index }}" class="collapse" data-parent="#accordionPPI">
                                    <div class="card-body py-2 px-1 bg-light">
                                        <ul class="list-group list-group-flush">
                                            @foreach($data['departments'] as $dept)
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 py-1 small">
                                                    <span><i class="fas fa-caret-right text-gray-400 mr-2"></i> {{ $dept['name'] }}</span>
                                                    <span class="font-weight-bold text-gray-700">{{ $dept['count'] }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
    // --- FORMAT ANGKA ---
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) { s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep); }
        if ((s[1] || '').length < prec) { s[1] = s[1] || ''; s[1] += new Array(prec - s[1].length + 1).join('0'); }
        return s.join(dec);
    }

    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // 1. AREA CHART (PPI)
    var ctxArea = document.getElementById("myAreaChart");
    var areaData = @json($ppiMonthlyData ?? []); 

    if(ctxArea) {
        new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
                datasets: [{
                    label: "Total PPI",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: areaData, 
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                scales: {
                    xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 7 } }],
                    yAxes: [{
                        ticks: { maxTicksLimit: 5, padding: 10, beginAtZero: true }, 
                        gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                    }],
                },
                legend: { display: false },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15, yPadding: 15, displayColors: false, intersect: false, mode: 'index', caretPadding: 10,
                }
            }
        });
    }

    // 2. PIE CHART (DEPT)
    var ctxPie = document.getElementById("myPieChart");
    var pieLabels = {!! json_encode($deptLabels ?? []) !!};
    var pieData = {!! json_encode($deptData ?? []) !!};

    if(ctxPie && pieLabels.length > 0) {
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: pieLabels,
                datasets: [{
                    data: pieData,
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60616f'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15, yPadding: 15, displayColors: false, caretPadding: 10,
                },
                legend: { display: true, position: 'bottom' },
                cutoutPercentage: 70,
            },
        });
    }
</script>
@endpush