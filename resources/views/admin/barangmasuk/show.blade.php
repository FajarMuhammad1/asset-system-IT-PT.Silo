@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 text-gray-800">
        <i class="fas fa-eye mr-2"></i> Detail Barang Masuk
    </h4>

    <div class="card shadow">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>No Surat Jalan:</strong><br>
                    {{ $barangMasuk->no_sj }}
                </div>
                <div class="col-md-6">
                    <strong>No PPI:</strong><br>
                    {{ $barangMasuk->no_ppi }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>No PO:</strong><br>
                    {{ $barangMasuk->no_po }}
                </div>
                <div class="col-md-6">
                    <strong>Nama Barang:</strong><br>
                    {{ $barangMasuk->nama_barang }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Kategori:</strong><br>
                    {{ $barangMasuk->kategori }}
                </div>
                <div class="col-md-6">
                    <strong>Jumlah:</strong><br>
                    {{ $barangMasuk->jumlah }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Tanggal Masuk:</strong><br>
                    {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d-m-Y') }}
                </div>
                <div class="col-md-6">
                    <strong>Keterangan:</strong><br>
                    {{ $barangMasuk->keterangan ?? '-' }}
                </div>
            </div>

            <div class="mt-4">
                <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
                <a href="{{ route('barangmasuk.edit', $barangMasuk->id) }}" class="btn btn-warning">
                    <i class="fas fa-edit mr-2"></i> Edit Data
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
