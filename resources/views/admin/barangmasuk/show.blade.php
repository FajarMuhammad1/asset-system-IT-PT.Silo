@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    {{-- HEADER & STATUS BADGE --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-info-circle mr-2"></i> Detail Aset
        </h1>
        
        {{-- Badge Status Besar di Pojok Kanan --}}
        <div>
            <span class="text-gray-600 font-weight-bold mr-2">Status Saat Ini:</span>
            @if ($barangMasuk->status == 'Stok')
                <span class="badge badge-success px-3 py-2" style="font-size: 1rem;">Tersedia (Stok)</span>
            @elseif ($barangMasuk->status == 'Dipakai')
                <span class="badge badge-warning px-3 py-2 text-dark" style="font-size: 1rem;">Sedang Dipakai</span>
            @elseif ($barangMasuk->status == 'Rusak')
                <span class="badge badge-danger px-3 py-2" style="font-size: 1rem;">Rusak</span>
            @elseif ($barangMasuk->status == 'Hilang')
                <span class="badge badge-dark px-3 py-2" style="font-size: 1rem;">Hilang</span>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            
            {{-- BAGIAN 1: DOKUMEN --}}
            <h5 class="font-weight-bold text-dark border-bottom pb-2">
                <i class="fas fa-file-alt mr-2 text-primary"></i> 1. Informasi Dokumen
            </h5>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">ID Surat Jalan (System)</label>
                    <div class="p-2 bg-light rounded text-dark font-weight-bold">
                        {{ $barangMasuk->suratJalan->id_suratjalan ?? '-' }}
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Nomor Surat Jalan (Fisik)</label>
                    <div class="p-2 bg-light rounded text-dark">
                        {{ $barangMasuk->suratJalan->no_sj ?? '-' }}
                        @if($barangMasuk->suratJalan)
                            <a href="#" class="float-right text-decoration-none text-info" title="Lihat Detail SJ">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Tanggal Terima</label>
                    <div class="p-2 bg-light rounded text-dark">
                        <i class="far fa-calendar-alt mr-1"></i>
                        {{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d F Y') }}
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Nomor PPI</label>
                    <div class="p-2 bg-light rounded">{{ $barangMasuk->suratJalan->no_ppi ?? '-' }}</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Nomor PO</label>
                    <div class="p-2 bg-light rounded">{{ $barangMasuk->suratJalan->no_po ?? '-' }}</div>
                </div>
            </div>

            {{-- BAGIAN 2: KATALOG BARANG --}}
            <h5 class="font-weight-bold text-dark border-bottom pb-2 mt-4">
                <i class="fas fa-box mr-2 text-primary"></i> 2. Spesifikasi Barang (Katalog)
            </h5>
            <div class="row mt-3">
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Nama Barang</label>
                    <div class="p-2 bg-light rounded font-weight-bold text-primary">
                        {{ $barangMasuk->masterBarang->nama_barang ?? '-' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Kategori</label>
                    <div class="p-2 bg-light rounded">
                        {{ $barangMasuk->masterBarang->kategori->nama_kategori ?? $barangMasuk->masterBarang->kategori ?? '-' }}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Merk / Brand</label>
                    <div class="p-2 bg-light rounded">{{ $barangMasuk->masterBarang->merk ?? '-' }}</div>
                </div>
                <div class="col-md-12 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Spesifikasi Detail</label>
                    <div class="p-3 bg-light rounded border-left-primary" style="min-height: 60px;">
                        {{ $barangMasuk->masterBarang->spesifikasi ?? '-' }}
                    </div>
                </div>
            </div>
            
            {{-- BAGIAN 3: FISIK & POSISI --}}
            <h5 class="font-weight-bold text-dark border-bottom pb-2 mt-4">
                <i class="fas fa-microchip mr-2 text-primary"></i> 3. Identitas Fisik & Posisi
            </h5>
            <div class="row mt-3">
                {{-- KODE ASET --}}
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Kode Aset</label>
                    <div class="p-2">
                        @if($barangMasuk->kode_asset)
                            <span class="badge badge-primary shadow-sm px-3 py-2" style="font-size: 1rem; letter-spacing: 1px;">
                                {{ $barangMasuk->kode_asset }}
                            </span>
                        @else
                            <span class="badge badge-secondary px-3 py-2">
                                <i class="fas fa-box-open mr-1"></i> Habis Pakai / Consumable
                            </span>
                        @endif
                    </div>
                </div>

                {{-- SERIAL NUMBER --}}
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Serial Number (SN)</label>
                    <div class="p-2 bg-light rounded font-monospace">
                        {{ $barangMasuk->serial_number ?? '-' }}
                    </div>
                </div>

                {{-- POSISI / PEMEGANG --}}
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Posisi / Pemegang Saat Ini</label>
                    <div class="p-2 bg-light rounded">
                        @if($barangMasuk->pemegang)
                            <i class="fas fa-user text-success mr-2"></i> 
                            <span class="font-weight-bold">{{ $barangMasuk->pemegang->nama }}</span>
                        @else
                            <i class="fas fa-warehouse text-muted mr-2"></i> 
                            <span class="text-muted">Gudang IT (Belum dipinjamkan)</span>
                        @endif
                    </div>
                </div>

                {{-- KETERANGAN --}}
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold text-xs text-uppercase text-gray-600">Keterangan Tambahan</label>
                    <div class="p-2 bg-light rounded font-italic text-muted">
                        {{ $barangMasuk->keterangan ?? 'Tidak ada keterangan tambahan.' }}
                    </div>
                </div>
            </div>

            <hr class="mt-4">
            
            <div class="d-flex justify-content-between">
                <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke List
                </a>
                
                <div>
                    {{-- Tombol Edit --}}
                    <a href="{{ route('barangmasuk.edit', $barangMasuk->id) }}" class="btn btn-warning text-dark mr-2">
                        <i class="fas fa-edit mr-1"></i> Edit Data
                    </a>
                    
                    {{-- Tombol Hapus (Opsional di view show) --}}
                    {{-- <button class="btn btn-danger" onclick="deleteItem()">
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </button> --}}
                </div>
            </div>

        </div>
    </div>
</div>
@endsection