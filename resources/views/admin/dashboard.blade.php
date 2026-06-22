@extends('layouts.app')

@section('title', $title)

@section('content')

{{-- STYLE TAMBAHAN MODERN --}}
<style>
    .card-hover-effect {
        transition: all 0.25s ease-in-out;
        cursor: pointer;
    }
    .card-hover-effect:hover {
        transform: translateY(-4px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.12) !important;
        text-decoration: none;
    }
    /* Scrollbar halus untuk widget list */
    .widget-scroll {
        max-height: 350px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f8fafc;
    }
    .widget-scroll::-webkit-scrollbar { 
        width: 6px; 
    }
    .widget-scroll::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .widget-scroll::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 10px; 
    }
    .widget-scroll::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

{{-- HEADER DASHBOARD --}}
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div class="mb-3 mb-sm-0">
        <h1 class="h3 mb-1 text-gray-800 font-weight-bold">{{ $title }}</h1>
        <span class="badge badge-light text-muted p-2 border">
            <i class="far fa-calendar-alt text-primary mr-1"></i> Data per {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}
        </span>
    </div>
    
    {{-- TOMBOL PINTASAN OPERASIONAL (Hanya Admin) --}}
    @if(auth()->user()->role === 'Admin')
        <div class="d-flex flex-wrap align-items-center justify-content-start justify-content-sm-end" style="gap: 5px;">
            <a href="{{ route('surat-jalan.create') }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Surat Jalan
            </a>
            <a href="{{ route('barangkeluar.create') }}" class="btn btn-sm btn-success shadow-sm">
                <i class="fas fa-handshake fa-sm text-white-50 mr-1"></i> BAST
            </a>
            <a href="{{ route('asset-lifecycle.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-sync-alt fa-sm text-white-50 mr-1"></i> Asset Lifecycle
            </a>
            <a href="{{ route('disposal.index') }}" class="btn btn-sm btn-danger shadow-sm">
                <i class="fas fa-trash-alt fa-sm text-white-50 mr-1"></i> Pengajuan Disposal
            </a>
            <a href="{{ route('mutasi.index') }}" class="btn btn-sm btn-info shadow-sm text-white">
                <i class="fas fa-exchange-alt fa-sm text-white-50 mr-1"></i> Mutasi Aset
            </a>
            {{-- TOMBOL MAINTENANCE YANG BARU DITAMBAHKAN --}}
            <a href="{{ route('admin.maintenance.index') }}" class="btn btn-sm btn-dark shadow-sm text-white">
                <i class="fas fa-tools fa-sm text-white-50 mr-1"></i> Maintenance Aset
            </a>
            <a href="{{ route('rkab.index') }}" class="btn btn-sm btn-warning shadow-sm text-dark font-weight-bold">
                <i class="fas fa-chart-pie fa-sm text-dark-50 mr-1"></i> Budget RKAB
            </a>
        </div>
    @endif
</div>

{{-- BARIS 1: KARTU STATISTIK INDIKATOR UTAMA --}}
<div class="row">

    {{-- Kartu Team IT --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('team') }}" class="card border-left-primary shadow h-100 py-2 card-hover-effect text-decoration-none">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Team IT (Staff)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $teamCount ?? 0 }} <small class="text-muted" style="font-size: 0.7em">Personil</small>
                        </div>
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

    {{-- Kartu Aset --}}
    @php
        $userRole = auth()->user()->role;
        $linkAset = ($userRole === 'Admin') ? route('barangmasuk.index') : route('master-barang.index');
    @endphp
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ $linkAset }}" class="card border-left-info shadow h-100 py-2 card-hover-effect text-decoration-none">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Aset</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $assetCount ?? 0 }} <small class="text-muted" style="font-size: 0.7em">Unit</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-info text-white p-3 rounded-circle">
                            <i class="fas fa-laptop fa-lg"></i>
                        </div>
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
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Tiket (Open)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $ticketOpenCount ?? 0 }} <small class="text-muted" style="font-size: 0.7em">Tiket</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-warning text-white p-3 rounded-circle">
                            <i class="fas fa-headset fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

    {{-- Kartu Pengajuan Disposal Pending --}}
    <div class="col-xl-3 col-md-6 mb-4">
        <a href="{{ route('disposal.index') }}" class="card border-left-danger shadow h-100 py-2 card-hover-effect text-decoration-none">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Disposal (Pending)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $disposalPendingCount ?? 0 }} <small class="text-muted" style="font-size: 0.7em">Aset</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="icon-circle bg-danger text-white p-3 rounded-circle">
                            <i class="fas fa-trash-alt fa-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>

</div>

{{-- BARIS 2: ANALISIS GRAFIK --}}
<div class="row">

    {{-- AREA CHART (TREN PPI) --}}
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom-0">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line mr-1"></i> Tren Permintaan (PPI) {{ date('Y') }}</h6>
            </div>
            <div class="card-body">
                <div class="chart-area" style="position: relative; height: 320px;">
                    <canvas id="myAreaChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- DONUT CHART (SEBARAN DEPARTEMEN) --}}
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom-0">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-pie mr-1"></i> Sebaran PPI per Dept</h6>
            </div>
            <div class="card-body">
                @if(empty($deptLabels) || count($deptLabels) === 0)
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chart-pie fa-3x mb-3 text-gray-300"></i>
                        <p class="small">Belum ada data statistik departemen.</p>
                    </div>
                @else
                    <div class="chart-pie pt-2 pb-2" style="position: relative; height: 240px;">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-3 text-center small text-muted">
                        Data diambil dari 5 departemen teraktif.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- BARIS 3: WIDGET DETAIL DAN LOG AKTIVITAS --}}
<div class="row">

    {{-- KOLOM 1: TOP STAFF & RIWAYAT BAST --}}
    <div class="col-lg-4 col-md-12">
        
        {{-- Top Staff IT --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex align-items-center justify-content-between bg-white border-bottom-0">
                <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-trophy mr-1"></i> Top 5 Staff IT</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="text-center border-0 py-2" width="15%">#</th>
                                <th class="border-0 py-2">Nama Staff</th>
                                <th class="text-center border-0 py-2" width="25%">Task Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topTechnicians ?? [] as $index => $tech)
                            <tr>
                                <td class="text-center align-middle py-2">
                                    @if($index == 0) 
                                        <i class="fas fa-medal fa-lg text-warning" title="Emas"></i>
                                    @elseif($index == 1) 
                                        <i class="fas fa-medal fa-lg text-secondary" title="Perak"></i>
                                    @elseif($index == 2) 
                                        <i class="fas fa-medal fa-lg" style="color: #cd7f32;" title="Perunggu"></i>
                                    @else 
                                        <span class="font-weight-bold text-gray-500 small">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold text-dark py-2">{{ $tech->nama ?? 'Tanpa Nama' }}</td>
                                <td class="text-center align-middle py-2">
                                    <span class="badge badge-success badge-pill font-weight-bold">{{ $tech->total_task ?? 0 }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 small text-muted">
                                    <i class="fas fa-info-circle mr-1"></i> Belum ada data kinerja staff.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat BAST --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom-0">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-1"></i> Serah Terima Terakhir (BAST)</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush widget-scroll">
                    @forelse ($recentBast ?? [] as $bast)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div class="mr-2 overflow-hidden">
                            <div class="text-truncate font-weight-bold text-dark small" style="max-width: 190px;">
                                {{ $bast->aset->masterBarang->nama_barang ?? 'Asset Tidak Diketahui' }}
                            </div>
                            <div class="small text-muted text-truncate" style="font-size: 0.85em;">
                                <i class="fas fa-user-circle mr-1 text-gray-400"></i> {{ $bast->pemegang->nama ?? 'Tidak Diketahui' }}
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="badge badge-light border small text-gray-600">
                                {{ \Carbon\Carbon::parse($bast->created_at)->format('d M') }}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4 small">
                        <i class="fas fa-folder-open mr-1"></i> Belum ada riwayat serah terima.
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    {{-- KOLOM 2: KESEHATAN STOK ASET (PROGRESS BAR) --}}
    <div class="col-lg-4 col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom-0">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-heartbeat mr-1"></i> Kesehatan Stok Aset</h6>
            </div>
            <div class="card-body">
                @php
                    $currentAssetCount = $assetCount ?? 0;
                    $totalAset = ($currentAssetCount > 0) ? $currentAssetCount : 1;
                    
                    $currentDipakai = $dipakaiCount ?? 0;
                    $currentStok = $stokCount ?? 0;

                    $persenDipakai = ($currentDipakai / $totalAset) * 100;
                    $persenStok = ($currentStok / $totalAset) * 100;

                    // Logika Dinamis Warna Indikator Stok
                    $stokColor = 'bg-success';
                    $stokTextColor = 'text-success';
                    if($persenStok < 10) {
                        $stokColor = 'bg-danger';
                        $stokTextColor = 'text-danger';
                    } elseif($persenStok < 30) {
                        $stokColor = 'bg-warning';
                        $stokTextColor = 'text-warning';
                    }
                @endphp

                {{-- Unit Dipakai --}}
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small font-weight-bold text-dark">Unit Dipakai <span class="text-muted">({{ $currentDipakai }})</span></span>
                        <span class="small font-weight-bold text-primary">{{ round($persenDipakai) }}%</span>
                    </div>
                    <div class="progress progress-sm rounded shadow-sm" style="height: 8px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $persenDipakai }}%" aria-valuenow="{{ $persenDipakai }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                {{-- Stok / Backup --}}
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small font-weight-bold text-dark">Stok / Backup Standby <span class="text-muted">({{ $currentStok }})</span></span>
                        <span class="small font-weight-bold {{ $stokTextColor }}">{{ round($persenStok) }}%</span>
                    </div>
                    <div class="progress progress-sm rounded shadow-sm" style="height: 8px;">
                        <div class="progress-bar {{ $stokColor }}" role="progressbar" style="width: {{ $persenStok }}%" aria-valuenow="{{ $persenStok }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    
                    {{-- Alert Peringatan Stok Menipis --}}
                    @if($persenStok < 10)
                        <div class="mt-3 p-2 bg-light border border-left-danger rounded small text-danger font-weight-bold animate__animated animate__flash">
                            <i class="fas fa-exclamation-triangle mr-1"></i> CRITICAL: Stok menipis di bawah 10%! Segera lakukan pengadaan.
                        </div>
                    @endif
                </div>

                <div class="text-center mt-4">
                    <a href="{{ $linkAset }}" class="btn btn-outline-info btn-sm btn-block shadow-sm">
                        <i class="fas fa-search mr-1"></i> Lihat Manajemen Aset Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- KOLOM 3: ACCORDION DETAIL PPI PER PERUSAHAAN (PT) --}}
    <div class="col-lg-4 col-md-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom-0">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-building mr-1"></i> Akumulasi PPI (Per PT)</h6>
            </div>
            <div class="card-body p-2 widget-scroll">
                @if(empty($ppiPerCompany) || count($ppiPerCompany) === 0)
                    <div class="text-center text-muted py-5 small">
                        <i class="fas fa-info-circle fa-2x mb-2 text-gray-300"></i><br>Belum ada rekapan permintaan.
                    </div>
                @else
                    <div class="accordion" id="accordionPPI">
                        @foreach($ppiPerCompany as $ptName => $data)
                            <div class="card border border-light mb-2 shadow-sm rounded">
                                <div class="card-header p-0 bg-white border-0" id="heading{{ $loop->index }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-light btn-block text-left d-flex justify-content-between align-items-center py-3 px-3 collapsed" 
                                                type="button" data-toggle="collapse" data-target="#collapse{{ $loop->index }}" aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                                            <span class="font-weight-bold text-dark small text-uppercase"><i class="fas fa-factory text-muted mr-1"></i> {{ $ptName }}</span>
                                            <span class="badge badge-primary badge-pill font-weight-bold shadow-sm">{{ $data['total_company'] ?? 0 }}</span>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse{{ $loop->index }}" class="collapse" aria-labelledby="heading{{ $loop->index }}" data-parent="#accordionPPI">
                                    <div class="card-body py-2 px-2 bg-light border-top">
                                        <ul class="list-group list-group-flush bg-transparent">
                                            @foreach($data['departments'] ?? [] as $dept)
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent border-0 py-1 px-2 small">
                                                    <span><i class="fas fa-caret-right text-primary mr-2"></i> {{ $dept['name'] ?? 'N/A' }}</span>
                                                    <span class="font-weight-bold text-gray-700">{{ $dept['count'] ?? 0 }}</span>
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
    // --- UTILITY FORMAT NOMOR ---
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

    // 1. MODERNISASI AREA CHART DENGAN GRADIENT BACKGROUND
    var ctxArea = document.getElementById("myAreaChart");
    var areaData = @json($ppiMonthlyData ?? []); 

    if(ctxArea) {
        var canvasArea = ctxArea.getContext('2d');
        
        // Membuat efek gradient fill canvas yang halus
        var gradientFill = canvasArea.createLinearGradient(0, 0, 0, 300);
        gradientFill.addColorStop(0, "rgba(78, 115, 223, 0.3)");
        gradientFill.addColorStop(1, "rgba(78, 115, 223, 0.0)");

        new Chart(canvasArea, {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
                datasets: [{
                    label: "Total Permintaan PPI",
                    lineTension: 0.35,
                    backgroundColor: gradientFill,
                    borderColor: "rgba(78, 115, 223, 1)",
                    borderWidth: 2.5,
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 15,
                    pointBorderWidth: 2,
                    data: areaData, 
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: { padding: { left: 10, right: 25, top: 15, bottom: 0 } },
                scales: {
                    xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 12 } }],
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

    // 2. PIE / DOUGHNUT CHART (SEBARAN DEPT)
    var ctxPie = document.getElementById("myPieChart");
    var pieLabels = @json($deptLabels ?? []);
    var pieData = @json($deptData ?? []);

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
                    borderWidth: 2
                }],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 12, yPadding: 12, displayColors: true, caretPadding: 10,
                },
                legend: { display: true, position: 'bottom', labels: { boxWidth: 12, padding: 15 } },
                cutoutPercentage: 75,
            },
        });
    }
</script>
@endpush