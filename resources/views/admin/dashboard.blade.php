@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
    {{-- Tombol Pintas Atas --}}
    <div class="d-none d-sm-inline-block">
        <a href="{{ route('surat-jalan.create') }}" class="btn btn-sm btn-primary shadow-sm mr-2">
            <i class="fas fa-plus fa-sm text-white-50"></i> Surat Jalan Baru
        </a>
        <a href="{{ route('barangkeluar.create') }}" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-handshake fa-sm text-white-50"></i> Serah Terima Baru
        </a>
    </div>
</div>

<div class="row">

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Team IT (Staff/Admin)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $teamCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('team') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Pengguna (User)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $penggunaCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('pengguna.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Aset Terdaftar</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assetCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-laptop fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('barangmasuk.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Tiket Helpdesk (Open)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $ticketOpenCount }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-headset fa-2x text-gray-300"></i>
                    </div>
                </div>
                <a href="{{ route('admin.helpdesk.index') }}" class="stretched-link"></a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Overview Maintenance Helpdesk (Tahun {{ date('Y') }})</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart"></canvas>
                </div>
                <hr>
                <div class="small text-center text-muted">
                    Grafik ini menampilkan jumlah tiket kerusakan yang masuk dan tercatat di sistem per bulan.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">

    <div class="col-lg-4">
        
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-gradient-light">
                <h6 class="m-0 font-weight-bold text-success">🏆 Top 5 Staff IT Teraktif</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="10%" class="text-center">Rank</th>
                                <th>Nama Staff</th>
                                <th class="text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topTechnicians as $index => $tech)
                            <tr>
                                <td class="text-center font-weight-bold">
                                    @if($index == 0) 🥇
                                    @elseif($index == 1) 🥈
                                    @elseif($index == 2) 🥉
                                    @else {{ $index + 1 }}
                                    @endif
                                </td>
                                <td>
                                    <span class="font-weight-bold">{{ $tech->nama }}</span><br>
                                    <small class="text-muted">{{ $tech->jabatan ?? '-' }}</small>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge badge-success px-2 py-1">
                                        {{ $tech->total_task }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted small py-3">Belum ada data task.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Riwayat BAST Terakhir</h6>
                <a href="{{ route('barangkeluar.index') }}" class="btn btn-sm btn-info">Lihat</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" width="100%" cellspacing="0">
                        <thead>
                            <tr><th>Tgl</th><th>Barang</th><th class="text-center">Sts</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($recentBast ?? [] as $bast)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($bast->tanggal_serah_terima)->format('d/m') }}</td>
                                <td>
                                    {{ Str::limit($bast->aset->masterBarang->nama_barang ?? '-', 15) }}
                                    <br><small class="text-muted">{{ $bast->pemegang->nama ?? '-' }}</small>
                                </td>
                                <td class="text-center align-middle">
                                    @if($bast->status == 'selesai') <i class="fas fa-check-circle text-success"></i>
                                    @else <i class="fas fa-clock text-warning"></i> @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted small">Belum ada data.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sebaran Tiket per Dept.</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart"></canvas>
                </div>
                <div class="mt-4 text-center small text-muted">
                    <span class="mr-2"><i class="fas fa-circle text-primary"></i> Data Realtime</span>
                </div>
                @if(empty($deptLabels) || count($deptLabels) == 0)
                    <div class="alert alert-secondary mt-3 text-center small">Belum ada data tiket.</div>
                @endif
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">Kesehatan Aset</h6>
            </div>
            <div class="card-body">
                @php
                    $totalAset = $assetCount > 0 ? $assetCount : 1;
                    $dipakai = $dipakaiCount ?? 0; 
                    $stok = $stokCount ?? 0;
                    $rusak = $rusakCount ?? 0;
                    
                    $persenDipakai = ($dipakai / $totalAset) * 100;
                    $persenStok = ($stok / $totalAset) * 100;
                    $persenRusak = ($rusak / $totalAset) * 100;
                @endphp

                <h4 class="small font-weight-bold">Dipakai ({{ $dipakai }}) <span class="float-right">{{ round($persenDipakai) }}%</span></h4>
                <div class="progress mb-4"><div class="progress-bar bg-success" style="width: {{ $persenDipakai }}%"></div></div>
                
                <h4 class="small font-weight-bold">Stok ({{ $stok }}) <span class="float-right">{{ round($persenStok) }}%</span></h4>
                <div class="progress mb-4"><div class="progress-bar bg-warning" style="width: {{ $persenStok }}%"></div></div>

                <h4 class="small font-weight-bold">Rusak ({{ $rusak }}) <span class="float-right">{{ round($persenRusak) }}%</span></h4>
                <div class="progress mb-4"><div class="progress-bar bg-danger" style="width: {{ $persenRusak }}%"></div></div>
            </div>
        </div>

    </div>

    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistik PPI Masuk (Per PT)</h6>
            </div>
            <div class="card-body">
                
                @if(empty($ppiPerCompany))
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i><br>
                        Belum ada data PPI.
                    </div>
                @else
                    <div class="accordion" id="accordionPPI">
                        @foreach($ppiPerCompany as $ptName => $data)
                            @php 
                                $collapseId = 'collapse' . Str::slug($ptName); 
                            @endphp

                            <div class="card mb-2 border-left-primary">
                                <div class="card-header p-0" id="heading{{ $collapseId }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center text-decoration-none py-3" 
                                                type="button" 
                                                data-toggle="collapse" 
                                                data-target="#{{ $collapseId }}" 
                                                aria-expanded="false" 
                                                aria-controls="{{ $collapseId }}">
                                            
                                            <span class="font-weight-bold text-dark text-uppercase" style="font-size: 0.9rem;">
                                                <i class="fas fa-building mr-2 text-gray-400"></i> {{ $ptName }}
                                            </span>
                                            
                                            <span class="badge badge-primary badge-pill">{{ $data['total_company'] }}</span>
                                        </button>
                                    </h2>
                                </div>

                                <div id="{{ $collapseId }}" class="collapse" aria-labelledby="heading{{ $collapseId }}" data-parent="#accordionPPI">
                                    <div class="card-body bg-light py-2 px-3">
                                        <small class="text-muted text-uppercase font-weight-bold mb-2 d-block" style="font-size: 0.75rem;">Detail Departemen:</small>
                                        <ul class="list-group list-group-flush shadow-sm rounded">
                                            @foreach($data['departments'] as $dept)
                                                <li class="list-group-item d-flex justify-content-between align-items-center bg-white px-3 py-2 border-bottom">
                                                    <span style="font-size: 0.85rem;">{{ $dept['name'] }}</span>
                                                    <span class="badge badge-secondary" style="font-size: 0.8em;">{{ $dept['count'] }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3 text-center small text-muted font-italic">
                        *Klik nama perusahaan untuk melihat detail departemen.
                    </div>
                @endif

            </div>
        </div>
    </div>

</div>

@endsection

@push('scripts')
{{-- Gunakan Chart.js versi 2.9.4 agar kompatibel dengan SB Admin 2 --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

<script>
    // Set Default Font
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    // 1. AREA CHART (GRAFIK MAINTENANCE BULANAN)
    var ctxArea = document.getElementById("myAreaChart");
    if(ctxArea) {
        var myAreaChart = new Chart(ctxArea, {
            type: 'line',
            data: {
                labels: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"],
                datasets: [{
                    label: "Tiket Masuk",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($maintenanceData ?? []), // Data dari Controller
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

    // 2. PIE CHART (STATISTIK DEPARTEMEN)
    var ctxPie = document.getElementById("myPieChart");
    var pieLabels = @json($deptLabels ?? []);
    var pieData = @json($deptData ?? []);

    if(ctxPie && pieLabels.length > 0) {
        var myPieChart = new Chart(ctxPie, {
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
                cutoutPercentage: 75,
            },
        });
    }
</script>
@endpush