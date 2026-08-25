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
        @keyframes pulse-green {
            0% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(40, 167, 69, 0); }
        }
    </style>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 m-0 text-gray-800">Dashboard Approval (SuperAdmin)</h1>
        
        <div class="d-flex align-items-center bg-white px-3 py-2 rounded shadow-sm border">
            <span class="pulse-green mr-2"></span>
            <small class="font-weight-bold text-success mr-2">Realtime Active</small>
            <span class="badge badge-warning text-dark ml-1" id="saPendingBadge">{{ $requestMasuk->count() }} Pending</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-dark">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-tasks mr-2"></i> Permintaan Menunggu Tanda Tangan
            </h6>
        </div>
        <div class="card-body">
            @if($requestMasuk->isEmpty())
                <div class="text-center py-5">
                    <img src="https://img.icons8.com/clouds/100/000000/checked.png"/>
                    <h4 class="text-gray-500 mt-3">Tidak ada permintaan pending.</h4>
                    <p>Semua pekerjaan sudah selesai!</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th>No PPI</th>
                                <th>Tanggal</th>
                                <th>Pemohon</th>
                                <th>Perangkat</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requestMasuk as $item)
                            <tr>
                                <td class="font-weight-bold">{{ $item->no_ppi }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                <td>
                                    <strong>{{ $item->user->nama ?? '-' }}</strong><br>
                                    <small class="text-muted">{{ $item->user->departemen ?? '' }} - {{ $item->user->perusahaan ?? '' }}</small>
                                </td>
                                <td>{{ $item->perangkat }}</td>
                                <td>
                                    <span class="badge badge-warning">Butuh TTD Anda</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('superadmin.approval.review', $item->id) }}" class="btn btn-primary btn-sm shadow-sm mr-1">
                                        <i class="fas fa-pen-fancy"></i> Review & TTD
                                    </a>
                                    <a href="{{ route('superadmin.approval.cetak', $item->id) }}" target="_blank" class="btn btn-danger btn-sm shadow-sm">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let lastCount = {{ $requestMasuk->count() }};
        let lastId = {{ $requestMasuk->first()->id ?? 0 }};

        function playChime() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(880, ctx.currentTime);
                gain.gain.setValueAtTime(0.2, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.4);
            } catch(e) {}
        }

        function checkSuperAdminRealtime() {
            fetch("{{ route('superadmin.approval.realtime_check') }}", {
                headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    const badge = document.getElementById('saPendingBadge');
                    if (badge) badge.innerText = data.count_pending + ' Pending';

                    if (data.count_pending > lastCount || data.latest_id > lastId) {
                        playChime();
                        setTimeout(() => location.reload(), 1000);
                    } else if (data.count_pending < lastCount) {
                        setTimeout(() => location.reload(), 1000);
                    }
                }
            })
            .catch(err => console.log(err));
        }

        setInterval(checkSuperAdminRealtime, 5000);
    });
</script>
@endsection