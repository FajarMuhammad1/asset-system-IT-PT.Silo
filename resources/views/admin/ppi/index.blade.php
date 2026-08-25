@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <style>
        .pulse-green {
            width: 10px;
            height: 10px;
            background-color: #28a745;
            border-radius: 50%;
            box-shadow: 0 0 0 rgba(40, 167, 69, 0.4);
            animation: pulse-green 1.5s infinite;
            display: inline-block;
        }
        .pulse-gray {
            width: 10px;
            height: 10px;
            background-color: #6c757d;
            border-radius: 50%;
            display: inline-block;
        }
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
    </style>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 m-0 text-gray-800">Monitoring Request PPI (Admin IT)</h1>
        
        <div class="d-flex align-items-center bg-white px-3 py-2 rounded shadow-sm border">
            <div id="realtimeStatusBadge" class="d-flex align-items-center mr-3">
                <span id="pulseDot" class="pulse-green mr-2"></span>
                <small class="font-weight-bold text-success" id="realtimeStatusText">Realtime Active</small>
            </div>
            <button id="btnToggleRealtime" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Toggle Auto Refresh">
                <i id="btnToggleIcon" class="fas fa-pause mr-1"></i> <span id="btnToggleText">Pause</span>
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-left-danger shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- KITA KELOMPOKKAN DATA MENGGUNAKAN LARAVEL COLLECTION AGAR LEBIH RAPI --}}
    @php
        $tabs = [
            ['id' => 'all', 'label' => 'Semua', 'icon' => 'fa-list', 'color' => 'primary', 'data' => $dataPpi],
            ['id' => 'pending', 'label' => 'Cek Admin', 'icon' => 'fa-search', 'color' => 'info', 'data' => $dataPpi->where('status', 'pending')],
            ['id' => 'spv', 'label' => 'Menunggu SPV/SA', 'icon' => 'fa-user-tie', 'color' => 'warning', 'data' => $dataPpi->where('status', 'pending_superadmin')],
            ['id' => 'disetujui', 'label' => 'Disetujui', 'icon' => 'fa-check-circle', 'color' => 'success', 'data' => $dataPpi->where('status', 'disetujui')],
            ['id' => 'selesai', 'label' => 'Selesai', 'icon' => 'fa-flag-checkered', 'color' => 'dark', 'data' => $dataPpi->where('status', 'selesai')],
        ];
    @endphp

    {{-- NAVIGASI TABS --}}
    <ul class="nav nav-tabs mb-3 shadow-sm bg-white rounded" role="tablist" style="border: none; padding: 5px;">
        @foreach($tabs as $index => $tab)
        <li class="nav-item">
            <a class="nav-link {{ $index == 0 ? 'active' : '' }} font-weight-bold px-4 text-{{ $tab['color'] }}" data-toggle="tab" href="#tab-{{ $tab['id'] }}" role="tab">
                <i class="fas {{ $tab['icon'] }} mr-1"></i> {{ $tab['label'] }} (<span id="tab-count-{{ $tab['id'] }}">{{ $tab['data']->count() }}</span>)
            </a>
        </li>
        @endforeach
    </ul>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Masuk</h6>
            <button type="button" class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalExport">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Filter & Export Excel
            </button>
        </div>
        
        <div class="card-body">
            <div class="tab-content">
                {{-- KONTEN TABS (TABEL OTOMATIS DIGENERATE SESUAI KATEGORI) --}}
                @foreach($tabs as $index => $tab)
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="tab-{{ $tab['id'] }}" role="tabpanel">
                    <div class="table-responsive">
                        {{-- Menggunakan class unik datatable-multi agar tidak bentrok dengan template JS Anda --}}
                        <table class="table table-bordered table-hover datatable-multi" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No PPI</th>
                                    <th>Tanggal</th>
                                    <th>Pemohon</th>
                                    <th>Dept / PT</th>
                                    <th>Perangkat / Aset</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="18%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tab['data'] as $item)
                                <tr>
                                    <td class="font-weight-bold text-primary align-middle">{{ $item->no_ppi }}</td>
                                    <td class="align-middle" data-sort="{{ Carbon\Carbon::parse($item->created_at)->timestamp }}">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold">{{ $item->user->nama ?? 'User Hapus' }}</div>
                                        <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                                    </td>

                                    <td class="align-middle">
                                        <div class="small font-weight-bold">{{ $item->user->departemen ?? '-' }}</div>
                                        <span class="badge badge-light border">{{ $item->user->perusahaan ?? '-' }}</span>
                                    </td>

                                    <td class="align-middle">
                                        <span class="text-dark font-weight-bold">{{ $item->perangkat ?? '-' }}</span>
                                        @if($item->file_ppi)
                                            <br>
                                            <a href="{{ asset('storage/'.$item->file_ppi) }}" target="_blank" class="badge badge-info mt-1">
                                                <i class="fas fa-paperclip"></i> Lampiran
                                            </a>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center align-middle">
                                        @if($item->status == 'pending')
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-search"></i> Cek Admin</span>
                                        @elseif($item->status == 'pending_superadmin')
                                            <span class="badge badge-warning px-2 py-1 text-dark"><i class="fas fa-user-tie"></i> Menunggu SPV/SA</span>
                                        @elseif($item->status == 'disetujui')
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Disetujui</span>
                                        @elseif($item->status == 'selesai')
                                            <span class="badge badge-dark px-2 py-1"><i class="fas fa-flag-checkered"></i> Selesai</span>
                                        @elseif($item->status == 'ditolak')
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle"></i> Ditolak</span>
                                        @endif
                                    </td>
                                    
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('admin.ppi.show', $item->id) }}" class="btn btn-sm btn-outline-primary mb-1">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>
                                            <a href="{{ route('admin.ppi.cetak', $item->id) }}" target="_blank" class="btn btn-sm btn-outline-danger mb-1">
                                                <i class="fas fa-file-pdf"></i> Cetak PDF
                                            </a>

                                            @if($item->status == 'pending')
                                                <form action="{{ route('admin.ppi.forward', $item->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm" onclick="return confirm('Teruskan ke SuperAdmin?')">
                                                        <i class="fas fa-paper-plane"></i> Ajukan ke SA
                                                    </button>
                                                </form>
                                            @elseif($item->status == 'pending_superadmin')
                                                <button class="btn btn-secondary btn-sm btn-block" disabled style="opacity: 0.7;">
                                                    <i class="fas fa-clock"></i> Menunggu SA
                                                </button>
                                            @elseif($item->status == 'ditolak')
                                                <button class="btn btn-danger btn-sm btn-block" disabled><i class="fas fa-ban"></i> Ditolak</button>
                                            @elseif($item->status == 'selesai')
                                                <button class="btn btn-dark btn-sm btn-block" disabled><i class="fas fa-check"></i> Closed</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i><br>Belum ada data permintaan PPI.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- MODAL EXPORT EXCEL --}}
<div class="modal fade" id="modalExport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel"></i> Filter & Export PPI</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form id="formPpiExport" action="{{ route('admin.ppi.export') }}" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Filter Harian (Tanggal):</label>
                        <input type="date" name="tanggal" class="form-control">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="font-weight-bold">Atau Filter Bulanan:</label>
                        <div class="row">
                            <div class="col-6">
                                <select name="bulan" class="form-control">
                                    <option value="">-- Pilih Bulan --</option>
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="tahun" class="form-control">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for($y=date('Y'); $y>=2020; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="font-weight-bold">Filter Perusahaan:</label>
                        <select name="perusahaan" class="form-control">
                            <option value="">Semua Perusahaan</option>
                            @php
                                $daftarPerusahaan = $dataPpi->map(function($item) {
                                    return $item->user->perusahaan ?? null;
                                })->filter()->unique()->sort();
                            @endphp
                            @foreach($daftarPerusahaan as $pt)
                                <option value="{{ $pt }}">{{ $pt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" 
                            class="btn btn-success btn-form-submit-swal"
                            data-form="formPpiExport"
                            data-title="Export Data PPI"
                            data-desc="Anda akan meng-export data PPI sesuai filter yang dipilih.<br>File Excel akan otomatis di-download. Lanjutkan?"
                            data-icon="success"
                            data-confirm="Ya, Download Excel">
                        <i class="fas fa-download"></i> Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Inisialisasi DataTables
        var initDataTables = setInterval(function() {
            if (window.jQuery && $.fn.DataTable) {
                clearInterval(initDataTables);
                
                $('.datatable-multi').DataTable({
                    "pageLength": 10,
                    "language": {
                        "emptyTable": "Belum ada data permintaan untuk status ini."
                    }
                });

                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                });
            }
        }, 100);

        // 2. REALTIME ENGINE LOGIC
        let isRealtimeActive = true;
        let lastPpiId = null;
        let lastUpdatedAt = null;
        let pollTimer = null;

        // Fungsi Membunyikan Suara Chime Notifikasi (Web Audio API - Tanpa File Eksternal)
        function playNotificationChime() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                
                // Nada 1: E5 (659Hz)
                const osc1 = ctx.createOscillator();
                const gain1 = ctx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, ctx.currentTime);
                gain1.gain.setValueAtTime(0.15, ctx.currentTime);
                gain1.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
                osc1.connect(gain1);
                gain1.connect(ctx.destination);
                osc1.start(ctx.currentTime);
                osc1.stop(ctx.currentTime + 0.3);

                // Nada 2: B5 (987.77Hz)
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(987.77, ctx.currentTime + 0.15);
                gain2.gain.setValueAtTime(0.2, ctx.currentTime + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start(ctx.currentTime + 0.15);
                osc2.stop(ctx.currentTime + 0.6);
            } catch (e) {
                console.log('Audio chime info:', e);
            }
        }

        // Tampilkan Toast Alert
        function showPpiToast(title, body) {
            if (window.Swal) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'info',
                    title: title,
                    text: body,
                    showConfirmButton: false,
                    timer: 5000,
                    timerProgressBar: true
                });
            } else {
                let toastId = 'toast-' + Date.now();
                let toastHtml = `
                    <div id="${toastId}" class="position-fixed p-3" style="top: 20px; right: 20px; z-index: 9999; max-width: 350px;">
                        <div class="toast show shadow-lg border-primary" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="toast-header bg-primary text-white">
                                <i class="fas fa-bell mr-2"></i>
                                <strong class="mr-auto">${title}</strong>
                                <small>Baru saja</small>
                                <button type="button" class="ml-2 mb-1 close text-white" onclick="document.getElementById('${toastId}').remove()">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="toast-body bg-white text-dark font-weight-bold">
                                ${body}
                            </div>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', toastHtml);
                setTimeout(() => {
                    let el = document.getElementById(toastId);
                    if (el) el.remove();
                }, 6000);
            }
        }

        // Update Counter Tab
        function updateTabCounts(counts) {
            if (!counts) return;
            if (document.getElementById('tab-count-all')) document.getElementById('tab-count-all').innerText = counts.all || 0;
            if (document.getElementById('tab-count-pending')) document.getElementById('tab-count-pending').innerText = counts.pending || 0;
            if (document.getElementById('tab-count-spv')) document.getElementById('tab-count-spv').innerText = counts.spv || 0;
            if (document.getElementById('tab-count-disetujui')) document.getElementById('tab-count-disetujui').innerText = counts.disetujui || 0;
            if (document.getElementById('tab-count-selesai')) document.getElementById('tab-count-selesai').innerText = counts.selesai || 0;
        }

        // Check Update Server
        function checkPpiRealtime() {
            if (!isRealtimeActive) return;

            fetch("{{ route('admin.ppi.realtime_check') }}", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update counter tabs
                    updateTabCounts(data.counts);

                    // First init
                    if (lastPpiId === null) {
                        lastPpiId = data.latest_id;
                        lastUpdatedAt = data.latest_updated_at;
                        return;
                    }

                    // Cek jika ada PPI baru dibuat (ID lebih tinggi)
                    if (data.latest_id > lastPpiId) {
                        lastPpiId = data.latest_id;
                        lastUpdatedAt = data.latest_updated_at;
                        
                        playNotificationChime();
                        let ppiInfo = data.latest_ppi ? `${data.latest_ppi.no_ppi} - ${data.latest_ppi.pemohon} (${data.latest_ppi.perangkat})` : 'Ada permohonan baru';
                        showPpiToast('🔔 PPI Baru Masuk!', ppiInfo);

                        // Reload tabel otomatis secara mulus
                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    } 
                    // Cek jika ada perubahan status data existing
                    else if (data.latest_updated_at !== lastUpdatedAt) {
                        lastUpdatedAt = data.latest_updated_at;
                        setTimeout(() => {
                            location.reload();
                        }, 1200);
                    }
                }
            })
            .catch(err => console.log('Realtime check notice:', err));
        }

        // Switch Toggle Pause/Play
        const btnToggle = document.getElementById('btnToggleRealtime');
        const pulseDot = document.getElementById('pulseDot');
        const statusText = document.getElementById('realtimeStatusText');
        const btnIcon = document.getElementById('btnToggleIcon');
        const btnText = document.getElementById('btnToggleText');

        if (btnToggle) {
            btnToggle.addEventListener('click', function() {
                isRealtimeActive = !isRealtimeActive;
                if (isRealtimeActive) {
                    pulseDot.className = 'pulse-green mr-2';
                    statusText.className = 'font-weight-bold text-success';
                    statusText.innerText = 'Realtime Active';
                    btnIcon.className = 'fas fa-pause mr-1';
                    btnText.innerText = 'Pause';
                    checkPpiRealtime();
                } else {
                    pulseDot.className = 'pulse-gray mr-2';
                    statusText.className = 'font-weight-bold text-secondary';
                    statusText.innerText = 'Realtime Paused';
                    btnIcon.className = 'fas fa-play mr-1';
                    btnText.innerText = 'Resume';
                }
            });
        }

        // Jalankan Polling setiap 5 detik
        pollTimer = setInterval(checkPpiRealtime, 5000);
        // Panggil langsung pada awal pemuatan
        checkPpiRealtime();
    });
</script>
@endsection