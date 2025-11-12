@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-eye mr-2"></i> {{ $title }}
</h1>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="mb-3">
            <label class="fw-bold text-primary">Nama Barang</label>
            <div class="p-2 border rounded bg-light">{{ $masterBarang->nama_barang }}</div>
        </div>
        <div class="mb-3">
            <label class="fw-bold text-primary">Kategori</label>
            <div class="p-2 border rounded bg-light">{{ $masterBarang->kategori }}</div>
        </div>
        <div class="mb-3">
            <label class="fw-bold text-primary">Merk</label>
            <div class="p-2 border rounded bg-light">{{ $masterBarang->merk }}</div>
        </div>
        <div class="mb-3">
            <label class="fw-bold text-primary">Spesifikasi</label>
            <div class="p-2 border rounded bg-light">{{ $masterBarang->spesifikasi ?? '-' }}</div>
        </div>
        <div class="mb-3">
            <label class="fw-bold text-primary">Dibuat Pada</label>
            <div class="p-2 border rounded bg-light">{{ $masterBarang->created_at->format('d-m-Y H:i') }}</div>
        </div>
        <div class="mb-3">
            <label class="fw-bold text-primary">Diupdate Pada</label>
            <div class="p-2 border rounded bg-light">{{ $masterBarang->updated_at->format('d-m-Y H:i') }}</div>
        </div>

        <hr>
        <a href="{{ route('master-barang.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali ke List
        </a>
        <a href="{{ route('master-barang.edit', $masterBarang->id) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit
        </a>
    </div>
</div>
@endsection