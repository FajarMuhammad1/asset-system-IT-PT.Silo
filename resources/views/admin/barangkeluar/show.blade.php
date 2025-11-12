@extends('layouts.app') {{-- (Sesuaikan layout lo) --}}

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-file-invoice mr-2"></i> {{ $title }} (ID: {{ $log->id }})
</h1>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            BAST untuk Aset: {{ $log->aset->kode_asset ?? 'N/A' }}
        </h6>
        <a href="{{ route('barangkeluar.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>
    <div class="card-body">
        
        <h5 class="font-weight-bold">Informasi Serah Terima</h5>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><td style="width: 40%;"><strong>Tanggal Serah Terima</strong></td><td>: {{ \Carbon\Carbon::parse($log->tanggal_serah_terima)->format('d F Y') }}</td></tr>
                    <tr><td><strong>Penerima (Pemegang)</strong></td><td>: {{ $log->pemegang->nama ?? 'N/A' }}</td></tr>
                    <tr><td><strong>Petugas IT (Admin)</strong></td><td>: {{ $log->admin->nama ?? 'N/A' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                 <table class="table table-sm table-borderless">
                    <tr><td style="width: 40%;"><strong>Keterangan</strong></td><td>: {{ $log->keterangan ?? '-' }}</td></tr>
                    <tr><td><strong>Foto Bukti</strong></td><td>
                        @if ($log->foto_bukti)
                            <a href="{{ asset('storage/' . $log->foto_bukti) }}" target="_blank" class="btn btn-sm btn-info">Lihat Foto</a>
                        @else
                            -
                        @endif
                    </td></tr>
                    <tr><td><strong>File Pendukung</strong></td><td>
                        @if ($log->file)
                            <a href="{{ asset('storage/' . $log->file) }}" target="_blank" class="btn btn-sm btn-info">Lihat File</a>
                        @else
                            -
                        @endif
                    </td></tr>
                </table>
            </div>
        </div>
        
        <hr>
        <h5 class="font-weight-bold">Informasi Aset</h5>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><td style="width: 40%;"><strong>Kode Aset</strong></td><td>: {{ $log->aset->kode_asset ?? 'N/A' }}</td></tr>
                    <tr><td><strong>Serial Number</strong></td><td>: {{ $log->aset->serial_number ?? 'N/A' }}</td></tr>
                    <tr><td><strong>Nama Barang</strong></td><td>: {{ $log->aset->masterBarang->nama_barang ?? 'N/A' }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr><td style="width: 40%;"><strong>Kategori</strong></td><td>: {{ $log->aset->masterBarang->kategori ?? 'N/A' }}</td></tr>
                    <tr><td><strong>Merk</strong></td><td>: {{ $log->aset->masterBarang->merk ?? 'N/A' }}</td></tr>
                    <tr><td><strong>Spesifikasi</strong></td><td>: {{ $log->aset->masterBarang->spesifikasi ?? 'N/A' }}</td></tr>
                </table>
            </div>
        </div>

        <hr>
        <h5 class="font-weight-bold">Tanda Tangan</h5>
        <div class="row text-center">
            <div class="col-md-6">
                <p><strong>Penerima</strong></p>
                <div style="border: 1px dashed #ccc; padding: 10px; border-radius: 5px;">
                    {{-- Data TTD adalah Base64, kita lempar langsung ke <img> --}}
                    <img src="{{ $log->ttd_penerima }}" alt="TTD Penerima" style="max-width: 300px; height: auto;">
                </div>
                <p class="mt-2">{{ $log->pemegang->name ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Petugas IT</strong></p>
                <div style="border: 1px dashed #ccc; padding: 10px; border-radius: 5px;">
                    <img src="{{ $log->ttd_petugas }}" alt="TTD Petugas" style="max-width: 300px; height: auto;">
                </div>
                 <p class="mt-2">{{ $log->admin->name ?? 'N/A' }}</p>
            </div>
        </div>

    </div>
</div>
@endsection