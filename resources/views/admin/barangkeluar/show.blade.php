@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-invoice mr-2"></i> Detail BAST #{{ $log->id }}
        </h1>
        
        {{-- BADGE STATUS --}}
        <div>
            @if($log->status == 'selesai')
                <span class="badge badge-success p-2" style="font-size: 1rem;">
                    <i class="fas fa-check-circle"></i> SELESAI
                </span>
            @elseif($log->status == 'menunggu_ttd_user')
                <span class="badge badge-warning p-2 text-dark" style="font-size: 1rem;">
                    <i class="fas fa-clock"></i> MENUNGGU USER
                </span>
            @elseif($log->status == 'menunggu_ttd_admin')
                <span class="badge badge-info p-2" style="font-size: 1rem;">
                    <i class="fas fa-pen-alt"></i> BUTUH TTD ADMIN
                </span>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Dokumen</h6>
            <div>
                <a href="{{ route('barangkeluar.index') }}" class="btn btn-sm btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
                </a>
            </div>
        </div>

        <div class="card-body">
            
            {{-- ALERT INFO --}}
            @if($log->status == 'menunggu_ttd_admin')
                <div class="alert alert-info border-left-info shadow-sm mb-4">
                    <h5 class="alert-heading"><i class="fas fa-info-circle"></i> User Sudah Tanda Tangan!</h5>
                    <p class="mb-0">Silakan periksa data di bawah, lalu lakukan tanda tangan pada kolom <strong>Petugas IT</strong> untuk menyelesaikan BAST ini.</p>
                </div>
            @endif

            <div class="row">
                {{-- KOLOM 1: INFO SERAH TERIMA --}}
                <div class="col-md-6 border-right">
                    <h5 class="font-weight-bold text-dark mb-3">A. Data Serah Terima</h5>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="35%" class="text-muted">Tanggal Transaksi</td>
                            <td class="font-weight-bold">{{ \Carbon\Carbon::parse($log->tanggal_serah_terima)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pihak Penerima</td>
                            <td class="font-weight-bold text-primary">{{ $log->pemegang->nama ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Pihak Menyerahkan</td>
                            <td>{{ $log->admin->nama ?? '-' }} (IT Support)</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Keterangan</td>
                            <td>{{ $log->keterangan ?? '-' }}</td>
                        </tr>
                    </table>
                </div>

                {{-- KOLOM 2: INFO ASET --}}
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-dark mb-3">B. Detail Aset</h5>
                    <div class="alert alert-light border">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td width="35%" class="text-muted">Kode Aset</td>
                                <td class="font-weight-bold">{{ $log->aset->kode_asset ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nama Barang</td>
                                <td class="font-weight-bold">{{ $log->aset->masterBarang->nama_barang ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Serial Number</td>
                                <td>{{ $log->aset->serial_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kondisi Saat Serah</td>
                                <td>{{ $log->kondisi_saat_serah ?? 'Baik' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            {{-- BAGIAN TANDA TANGAN --}}
            <div class="row">
                <div class="col-12">
                    <h5 class="font-weight-bold text-dark mb-3">C. Tanda Tangan Digital</h5>
                </div>
                
                {{-- TTD PENERIMA (READ ONLY) --}}
                <div class="col-md-6 text-center mb-4">
                    <label class="small font-weight-bold text-muted text-uppercase">Penerima (User)</label>
                    <div class="border rounded p-2 d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                        @if($log->ttd_penerima)
                            {{-- Cek apakah base64 atau path file --}}
                            @if(Str::startsWith($log->ttd_penerima, 'data:image'))
                                <img src="{{ $log->ttd_penerima }}" style="max-height: 140px; max-width: 100%;">
                            @else
                                <img src="{{ asset('storage/' . $log->ttd_penerima) }}" style="max-height: 140px; max-width: 100%;">
                            @endif
                        @else
                            <div class="text-muted font-italic">
                                <i class="fas fa-clock text-warning mb-1"></i><br>Menunggu Tanda Tangan User
                            </div>
                        @endif
                    </div>
                    <p class="mt-2 font-weight-bold">{{ $log->pemegang->nama ?? 'User' }}</p>
                </div>

                {{-- TTD PETUGAS (ACTION NEEDED) --}}
                <div class="col-md-6 text-center mb-4">
                    <label class="small font-weight-bold text-muted text-uppercase">Yang Menyerahkan (Admin)</label>
                    
                    {{-- KONDISI 1: Admin Belum TTD & Statusnya Menunggu Admin --}}
                    @if(!$log->ttd_petugas && $log->status == 'menunggu_ttd_admin')
                        
                        <form action="{{ route('barangkeluar.adminSign', $log->id) }}" method="POST" id="form-sign">
                            @csrf
                            <div class="border rounded shadow-sm" style="background: #fff; display: inline-block;">
                                <canvas id="pad-admin" width="300" height="150" style="touch-action: none;"></canvas>
                            </div>
                            <br>
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="clearPad()">
                                <i class="fas fa-eraser"></i> Hapus
                            </button>
                            <input type="hidden" name="ttd_petugas" id="inp-ttd">
                            
                            <button type="submit" class="btn btn-success mt-2 px-4 shadow">
                                <i class="fas fa-check-circle"></i> SIMPAN & SELESAIKAN
                            </button>
                        </form>

                    {{-- KONDISI 2: Sudah Tanda Tangan --}}
                    @elseif($log->ttd_petugas)
                        <div class="border rounded p-2 d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                            @if(Str::startsWith($log->ttd_petugas, 'data:image'))
                                <img src="{{ $log->ttd_petugas }}" style="max-height: 140px; max-width: 100%;">
                            @else
                                <img src="{{ asset('storage/' . $log->ttd_petugas) }}" style="max-height: 140px; max-width: 100%;">
                            @endif
                        </div>
                        <p class="mt-2 font-weight-bold">{{ $log->admin->nama ?? 'Admin' }}</p>
                    
                    {{-- KONDISI 3: Belum saatnya Admin TTD --}}
                    @else
                        <div class="border rounded p-2 d-flex align-items-center justify-content-center bg-light" style="height: 160px;">
                            <div class="text-muted font-italic">
                                <i class="fas fa-lock text-secondary mb-1"></i><br>Menunggu User Tanda Tangan Dulu
                            </div>
                        </div>
                        <p class="mt-2 font-weight-bold">{{ $log->admin->nama ?? 'Admin' }}</p>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    // Hanya jalankan script jika elemen canvas ada
    var canvas = document.getElementById('pad-admin');
    if (canvas) {
        var signaturePad = new SignaturePad(canvas, { backgroundColor: 'rgb(255, 255, 255)' });

        function clearPad() {
            signaturePad.clear();
        }

        document.getElementById('form-sign').addEventListener('submit', function(e) {
            if (signaturePad.isEmpty()) {
                e.preventDefault();
                alert('Harap tanda tangan terlebih dahulu!');
            } else {
                document.getElementById('inp-ttd').value = signaturePad.toDataURL();
            }
        });

        // Resize Canvas agar responsif
        function resizeCanvas() {
            var ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        resizeCanvas();
    }
</script>
@endpush