@extends('layouts.app')

@push('styles')
<style>
    /* CSS Timeline Vertical yang Dioptimalkan */
    .timeline {
        position: relative;
        padding-left: 2rem;
        margin-top: 1rem;
    }
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0.75rem;
        height: 100%;
        width: 2px;
        background: #e3e6f0;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-icon {
        position: absolute;
        top: 0;
        left: -2.5rem;
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        z-index: 1;
        box-shadow: 0 0 0 3px #fff;
    }
    .timeline-content {
        background: #fff;
        padding: 1rem;
        border-radius: 0.35rem;
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-search-location mr-2"></i>{{ $title }}</h1>

    {{-- Alert jika validasi error --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Pencarian Gagal:</strong> Pastikan Anda mengisi Kode Aset / Serial Number.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Alert jika aset tidak ditemukan --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Form Pencarian / Pelacakan --}}
    <div class="card shadow mb-4 border-bottom-primary">
        <div class="card-body">
            <form action="{{ route('asset-lifecycle.track') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <label for="kode_asset" class="font-weight-bold text-dark">Masukkan Kode Barang / Serial Number Aset</label>
                        <input type="text" name="kode_asset" id="kode_asset" class="form-control form-control-lg" placeholder="Contoh: AST/IT/2026/1234 atau SN-12345" value="{{ request('kode_asset', $kodeAsset ?? '') }}" required autofocus>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm">
                            <i class="fas fa-search mr-1"></i> Lacak Aset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tampilkan Hasil Hanya Jika Data Aset Ditemukan --}}
    @if(isset($asset))
    
    {{-- UPDATE: Panel Bar Aksi Tambahan untuk Tombol Cetak --}}
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3">
        <h5 class="font-weight-bold text-dark mb-2 mb-sm-0">
            <i class="fas fa-poll-h mr-1 text-secondary"></i> Hasil Pelacakan Informasi Aset
        </h5>
        <a href="{{ route('asset.cetak_lifecycle', $asset->id) }}" target="_blank" class="btn btn-sm btn-danger font-weight-bold shadow-sm px-3 py-2">
            <i class="fas fa-file-pdf mr-1"></i> Cetak PDF Lifecycle
        </a>
    </div>

    <div class="row">
        
        {{-- Kiri: Informasi Detail Identitas Aset --}}
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-info-circle mr-1"></i> Informasi Identitas Aset</h6>
                </div>
                <div class="card-body border-top">
                    <div class="text-center mb-4">
                        {{-- Deteksi Ikon Otomatis Berdasarkan Kategori Ringkas --}}
                        @php
                            $kategori = strtolower($asset->masterBarang->kategori ?? '');
                            $iconClass = 'fa-boxes'; // Default icon
                            if (str_contains($kategori, 'laptop') || str_contains($kategori, 'notebook')) {
                                $iconClass = 'fa-laptop';
                            } elseif (str_contains($kategori, 'pc') || str_contains($kategori, 'computer') || str_contains($kategori, 'desktop')) {
                                $iconClass = 'fa-desktop';
                            } elseif (str_contains($kategori, 'printer')) {
                                $iconClass = 'fa-print';
                            } elseif (str_contains($kategori, 'network') || str_contains($kategori, 'switch') || str_contains($kategori, 'router')) {
                                $iconClass = 'fa-network-wired';
                            }
                        @endphp
                        <i class="fas {{ $iconClass }} fa-4x text-gray-300 mb-3"></i>
                        
                        <h5 class="font-weight-bold text-dark mb-0">{{ $asset->masterBarang->nama_barang ?? 'Nama Barang Tidak Ditemukan' }}</h5>
                        <p class="text-muted text-sm">{{ $asset->masterBarang->kategori ?? 'Tanpa Kategori' }}</p>
                    </div>
                    
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Kode Aset</span>
                            <span class="font-weight-bold text-dark">{{ $asset->kode_asset }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Serial Number</span>
                            <span class="font-weight-bold text-dark">{{ $asset->serial_number ?? '-' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Status Saat Ini</span>
                            @php
                                $statusColor = [
                                    'Stok' => 'success',
                                    'Digunakan' => 'primary',
                                    'Rusak' => 'warning',
                                    'Disposal' => 'danger',
                                    'Hilang' => 'dark'
                                ][$asset->status] ?? 'secondary';
                            @endphp
                            <span class="badge badge-{{ $statusColor }} px-2 py-1 shadow-sm">{{ $asset->status }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                            <span class="text-muted">Kondisi Fisik</span>
                            <span class="font-weight-bold text-dark">{{ $asset->kondisi ?? 'Bagus' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Kanan: Tampilan Riwayat Siklus Hidup (Timeline) --}}
        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white border-bottom-0">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-1"></i> Riwayat Siklus Hidup (Life Cycle)</h6>
                </div>
                <div class="card-body overflow-auto border-top" style="max-height: 600px;">
                    @if($timeline->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted mb-0">Belum ada riwayat aktivitas yang tercatat untuk aset ini.</p>
                        </div>
                    @else
                        <div class="timeline">
                            @foreach($timeline as $item)
                            <div class="timeline-item">
                                {{-- Ikon dinamis dari Controller --}}
                                <div class="timeline-icon {{ $item['icon'] }}">
                                    @if(str_contains($item['icon'], 'fa-box-open'))
                                        <i class="fas fa-box-open"></i>
                                    @elseif(str_contains($item['icon'], 'fa-truck-loading'))
                                        <i class="fas fa-shipping-fast"></i>
                                    @elseif(str_contains($item['icon'], 'fa-exchange-alt'))
                                        <i class="fas fa-exchange-alt"></i>
                                    @elseif(str_contains($item['icon'], 'fa-tools'))
                                        <i class="fas fa-tools"></i>
                                    @elseif(str_contains($item['icon'], 'fa-trash-alt'))
                                        <i class="fas fa-trash-alt"></i>
                                    @else
                                        <i class="fas fa-circle text-xs"></i>
                                    @endif
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-2">
                                        <h6 class="font-weight-bold text-dark mb-1 mb-sm-0">{{ $item['status'] }}</h6>
                                        <span class="badge badge-light text-gray-600 border px-2 py-1 text-xs font-weight-bold">
                                            <i class="far fa-calendar-alt mr-1"></i> 
                                            {{ \Carbon\Carbon::parse($item['tanggal'])->translatedFormat('d F Y (H:i)') }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-0">
                                        {{ $item['keterangan'] }}
                                    </p>
                                </div>
                            </div>
                            @endforeach
                            
                            {{-- Bendera Finish (Titik Terkini) --}}
                            <div class="timeline-item mt-4">
                                <div class="timeline-icon bg-success"><i class="fas fa-flag-checkered"></i></div>
                                <div class="timeline-content bg-light border-0 py-2 shadow-none">
                                    <span class="text-sm font-weight-bold text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Status Terkini: Aset berstatus resmi <strong>"{{ $asset->status }}"</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
    </div>
    @endif

</div>
@endsection