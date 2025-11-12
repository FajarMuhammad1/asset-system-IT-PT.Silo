@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-file-alt text-primary mr-2"></i> Detail Surat Jalan
    </h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            
            <h5 class="font-weight-bold">Informasi Dokumen</h5>
            <hr>
            <div class="row">
                
                {{-- Tambahan: ID Surat Jalan (dari HO) --}}
                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">ID Surat Jalan </label>
                    <div class="p-2 border rounded bg-light">{{ $suratJalan->id_suratjalan }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">No Surat Jalan</label>
                    <div class="p-2 border rounded bg-light">{{ $suratJalan->no_sj }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">No PPI</label>
                    <div class="p-2 border rounded bg-light">{{ $suratJalan->no_ppi }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">No PO</label>
                    <div class="p-2 border rounded bg-light">{{ $suratJalan->no_po }}</div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">Tanggal Input</label>
                    <div class="p-2 border rounded bg-light">
                           {{ \Carbon\Carbon::parse($suratJalan->tanggal_input)->format('d-m-Y') }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">Jenis Surat Jalan</label>
                    <div class="p-2 border rounded bg-light">{{ $suratJalan->jenis_surat_jalan }}</div>
                </div>
                
                {{-- Status BAST --}}
                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">Status BAST</label>
                    <div class="p-2 border rounded bg-light">
                        @if ($suratJalan->is_bast_submitted)
                            <span class="badge badge-success">Sudah Selesai</span>
                        @else
                            <span class="badge badge-danger">Belum Selesai</span>
                        @endif
                    </div>
                </div>
                
                {{-- Keterangan (Opsional) --}}
                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">Keterangan</label>
                    <div class="p-2 border rounded bg-light">{{ $suratJalan->keterangan ?? '-' }}</div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="fw-bold text-primary">File</label>
                    <div class="p-2 border rounded bg-light">
                        @if ($suratJalan->file)
                            <a href="{{ asset('storage/' . $suratJalan->file) }}" target="_blank" class="btn btn-sm btn-success">
                                <i class="fas fa-download"></i> Lihat / Download File
                            </a>
                        @else
                            <span class="text-muted">Tidak ada file</span>
                        @endif
                    </div>
                </div>
            </div>

            <h5 class="font-weight-bold mt-4">List Barang di Surat Jalan Ini</h5>
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="bg-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Merk</th>
                            <th>Spesifikasi</th>
                            <th class="text-center">Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suratJalan->details as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $detail->masterBarang->nama_barang }}</td>
                            <td>{{ $detail->masterBarang->kategori }}</td>
                            <td>{{ $detail->masterBarang->merk }}</td>
                            <td>{{ $detail->masterBarang->spesifikasi }}</td>
                            <td class="text-center">{{ $detail->qty }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Tidak ada barang detail di Surat Jalan ini.</td>
                        </tr>
                        @endForelse
                    </tbody>
                </table>
            </div>
            
            <hr>
            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mt-4">
                <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary mr-3">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <a href="{{ route('surat-jalan.edit', $suratJalan->id_sj) }}" class="btn btn-primary mr-3">
                    <i class="fas fa-edit"></i> Edit
                </a>
            </div>
        </div>
    </div>
</div>
@endsection