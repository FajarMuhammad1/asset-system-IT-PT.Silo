@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    {{-- Header & Navigasi --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <a href="{{ route('admin.ppi.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke List
        </a>
        <h1 class="h3 mb-0 text-gray-800">Detail Request PPI</h1>
    </div>

    <div class="row">

        {{-- KOLOM KIRI: INFO UTAMA & USER --}}
        <div class="col-xl-4 col-md-5 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle mb-2" src="https://ui-avatars.com/api/?name={{ urlencode($ppi->user->name ?? 'User') }}&background=4e73df&color=ffffff" style="width: 80px; height: 80px;">
                        <h5 class="font-weight-bold text-dark">{{ $ppi->user->nama ?? 'User Terhapus' }}</h5>
                        <div class="text-xs font-weight-bold text-uppercase mb-1 text-muted">
                            {{ $ppi->user->jabatan ?? 'Jabatan -' }}
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="small font-weight-bold text-muted text-uppercase mb-1">Departemen</div>
                    <div class="h6 mb-3 text-gray-800">{{ $ppi->user->departemen ?? '-' }}</div>

                    <div class="small font-weight-bold text-muted text-uppercase mb-1">Perusahaan</div>
                    <div class="h6 mb-3 text-gray-800">
                        <span class="badge badge-light border">{{ $ppi->user->perusahaan ?? '-' }}</span>
                    </div>

                    <div class="small font-weight-bold text-muted text-uppercase mb-1">Kontak / Email</div>
                    <div class="h6 mb-0 text-gray-800">{{ $ppi->user->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: DETAIL PPI & SURAT JALAN --}}
        <div class="col-xl-8 col-md-7 mb-4">
            
            {{-- CARD 1: DETAIL PERMINTAAN --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Tiket: #{{ $ppi->no_ppi }}</h6>
                    
                    {{-- STATUS BADGE --}}
                    @if($ppi->status == 'pending')
                        <span class="badge badge-info px-3 py-2">Status: Menunggu Admin</span>
                    @elseif($ppi->status == 'pending_superadmin')
                        <span class="badge badge-warning px-3 py-2 text-dark">Status: Menunggu SPV/SA</span>
                    @elseif($ppi->status == 'disetujui')
                        <span class="badge badge-primary px-3 py-2">Status: Disetujui (Sedang Proses)</span>
                    @elseif($ppi->status == 'selesai')
                        <span class="badge badge-success px-3 py-2">Status: Selesai</span>
                    @elseif($ppi->status == 'ditolak')
                        <span class="badge badge-danger px-3 py-2">Status: Ditolak</span>
                    @endif
                </div>

                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Tanggal Request</label>
                        <div class="col-sm-9 col-form-label">
                            {{ \Carbon\Carbon::parse($ppi->created_at)->translatedFormat('d F Y, H:i') }} WIB
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Perangkat</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control bg-light" value="{{ $ppi->perangkat }}" readonly>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label font-weight-bold">Deskripsi Kerusakan</label>
                        <div class="col-sm-9">
                            <div class="alert alert-secondary text-dark mb-0" role="alert">
                                {!! nl2br(e($ppi->ba_kerusakan)) !!}
                            </div>
                        </div>
                    </div>
                    
                    @if($ppi->file_ppi)
                    <div class="form-group row mt-3">
                        <label class="col-sm-3 col-form-label font-weight-bold">Lampiran</label>
                        <div class="col-sm-9">
                            <a href="{{ asset('storage/' . $ppi->file_ppi) }}" target="_blank" class="btn btn-sm btn-info shadow-sm">
                                <i class="fas fa-file-download"></i> Lihat Lampiran User
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                
                {{-- FOOTER CARD: AKSI TOMBOL APPROVE/REJECT --}}
                <div class="card-footer bg-light text-right">
                    @if($ppi->status == 'pending')
                        <form action="{{ route('admin.ppi.forward', $ppi->id) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Teruskan ke SuperAdmin?')">
                                <i class="fas fa-paper-plane"></i> Ajukan ke SA
                            </button>
                        </form>
                        
                        {{-- Opsi Tolak Langsung (Optional jika admin boleh nolak) --}}
                        <form action="{{ route('admin.ppi.update', $ppi->id) }}" method="POST" class="d-inline ml-2">
                            @csrf @method('PUT')
                            <input type="hidden" name="status" value="ditolak">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Tolak permintaan ini?')">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                        </form>

                    @elseif($ppi->status == 'selesai')
                        <button class="btn btn-secondary" disabled>Tiket Closed</button>
                    @endif
                </div>
            </div>

            {{-- CARD 2: INTEGRASI SURAT JALAN (Hanya Muncul Jika Disetujui/Selesai) --}}
            @if($ppi->status == 'disetujui' || $ppi->status == 'selesai')
            <div class="card shadow mb-4 border-left-success">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-truck"></i> Status Pengiriman / Surat Jalan
                    </h6>
                </div>
                <div class="card-body">
                    
                    {{-- Cek apakah relasi suratJalan ada --}}
                    @if($ppi->suratJalan)
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>No Surat Jalan:</strong></p>
                                <h5 class="text-dark font-weight-bold">{{ $ppi->suratJalan->no_surat_jalan }}</h5>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tujuan Perbaikan:</strong></p>
                                <p class="text-dark">{{ $ppi->suratJalan->tujuan_perbaikan }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Tanggal Kirim:</strong></p>
                                <p>{{ \Carbon\Carbon::parse($ppi->suratJalan->tgl_kirim)->format('d F Y') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Status Pengiriman:</strong></p>
                                <span class="badge badge-success">{{ ucfirst($ppi->suratJalan->status) }}</span>
                            </div>
                        </div>
                        
                        {{-- Tombol Cetak (Jika nanti ada fitur cetak) --}}
                        <div class="mt-3">
                            <button class="btn btn-sm btn-outline-dark" onclick="alert('Fitur cetak PDF bisa ditambahkan nanti!')">
                                <i class="fas fa-print"></i> Cetak Surat Jalan
                            </button>
                        </div>

                    @else
                        {{-- JIKA BELUM ADA SURAT JALAN --}}
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">Permintaan telah disetujui, namun <strong>Surat Jalan belum dibuat</strong>.</p>
                            
                            {{-- Cek Route agar tidak error --}}
                            @if(Route::has('admin.surat-jalan.create'))
                                <a href="{{ route('admin.surat-jalan.create', ['ppi_id' => $ppi->id]) }}" class="btn btn-success shadow-sm">
                                    <i class="fas fa-plus-circle"></i> Buat Surat Jalan Sekarang
                                </a>
                            @else
                                <a href="#" class="btn btn-secondary disabled">Route Surat Jalan Belum Ada</a>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
            @endif

        </div>
    </div>

</div>
@endsection