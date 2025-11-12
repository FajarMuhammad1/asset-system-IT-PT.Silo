@extends('layouts.app') {{-- (Sesuaikan layout lo) --}}

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-search mr-2"></i> {{ $title }}
    </h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            
            <h5 class="font-weight-bold">1. Informasi Dokumen</h5>
            <hr>
            <div class="row">
                
                {{-- INI KOLOM BARU LO --}}
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-primary">ID Surat Jalan (HO)</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->suratJalan->id_suratjalan ?? 'N/A' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-primary">No Surat Jalan</label>
                    <div class="p-2 border rounded bg-light">
                        {{ $barangMasuk->suratJalan->no_sj ?? 'N/A' }} 
                        @if($barangMasuk->suratJalan)
                            <a href="{{ route('surat-jalan.show', $barangMasuk->suratJalan->id_sj) }}" class="badge badge-info ml-2">Lihat SJ</a>
                        @endif
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-primary">Tanggal Masuk Aset</label>
                    <div class="p-2 border rounded bg-light">{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y') }}</div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-primary">No PPI</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->suratJalan->no_ppi ?? 'N/A' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-primary">No PO</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->suratJalan->no_po ?? 'N/A' }}</div>
                </div>
                
                <div class="col-md-12 mb-3">
                    <label class="font-weight-bold text-primary">Keterangan</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->keterangan ?? '-' }}</div>
                </div>
            </div>

            <h5 class="font-weight-bold mt-4">2. Informasi Barang (Katalog)</h5>
            <hr>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-primary">Nama Barang</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->masterBarang->nama_barang ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-primary">Kategori</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->masterBarang->kategori ?? 'N/A' }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-primary">Merk</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->masterBarang->merk ?? 'N/A' }}</div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="font-weight-bold text-primary">Spesifikasi</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->masterBarang->spesifikasi ?? 'N/A' }}</div>
                </div>
            </div>
            
            <h5 class="font-weight-bold mt-4">3. Informasi Fisik Aset</h5>
            <hr>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-primary">Serial Number (SN)</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->serial_number }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-primary">Kode Aset</label>
                    <div class="p-2 border rounded bg-light">{{ $barangMasuk->kode_asset }}</div>
                </div>
            </div>

            <hr>
            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mt-4">
                <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary mr-3">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('barangmasuk.edit', $barangMasuk->id) }}" class="btn btn-primary mr-3">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection