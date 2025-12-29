@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER & NAVIGASI --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-file-alt mr-2"></i> Detail Surat Jalan
        </h1>
        <div>
            <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary btn-sm shadow-sm mr-1">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
            </a>
            <a href="{{ route('surat-jalan.edit', $suratJalan->id_sj) }}" class="btn btn-warning btn-sm shadow-sm text-dark">
                <i class="fas fa-edit fa-sm"></i> Edit Data
            </a>
        </div>
    </div>

    <div class="row">

        {{-- KOLOM KIRI: INFORMASI HEADER --}}
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Informasi Dokumen</h6>
                    <span class="badge badge-light text-primary font-weight-bold px-3 py-2">
                        {{ $suratJalan->id_suratjalan }}
                    </span>
                </div>
                <div class="card-body">
                    
                    <div class="row">
                        {{-- GROUP 1: IDENTITAS --}}
                        <div class="col-md-4">
                            <h6 class="font-weight-bold text-primary mb-3 border-bottom pb-2">Identitas Surat Jalan</h6>
                            
                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">Nomor Surat Jalan (Fisik)</label>
                                <div class="h6 font-weight-bold text-dark">{{ $suratJalan->no_sj }}</div>
                            </div>

                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">Tanggal Input</label>
                                <div class="h6 text-dark">
                                    <i class="far fa-calendar-alt mr-1"></i>
                                    {{ \Carbon\Carbon::parse($suratJalan->tanggal_input)->translatedFormat('d F Y') }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">Jenis Surat Jalan</label>
                                <div><span class="badge badge-info px-2 py-1">{{ $suratJalan->jenis_surat_jalan }}</span></div>
                            </div>
                        </div>

                        {{-- GROUP 2: REFERENSI --}}
                        <div class="col-md-4">
                            <h6 class="font-weight-bold text-primary mb-3 border-bottom pb-2">Referensi Dokumen</h6>
                            
                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">Nomor PPI</label>
                                <div class="h6 text-dark">{{ $suratJalan->no_ppi }}</div>
                            </div>

                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">Nomor PO</label>
                                <div class="h6 text-dark">{{ $suratJalan->no_po }}</div>
                            </div>

                            <div class="form-group">
                                <label class="small text-muted font-weight-bold">Keterangan</label>
                                <div class="p-2 bg-light rounded border small text-dark">
                                    {{ $suratJalan->keterangan ?? '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- GROUP 3: STATUS & FILE --}}
                        <div class="col-md-4">
                            <h6 class="font-weight-bold text-primary mb-3 border-bottom pb-2">Status & Lampiran</h6>

                            {{-- Status BAST --}}
                            <div class="mb-3">
                                <label class="small text-muted font-weight-bold">Status Serah Terima (BAST)</label>
                                @if ($suratJalan->is_bast_submitted)
                                    <div class="alert alert-success py-2 px-3 m-0 border-left-success shadow-sm">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-check-circle fa-2x mr-2"></i>
                                            <div>
                                                <div class="font-weight-bold">SELESAI</div>
                                                <small>Barang sudah diserahterimakan</small>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 px-3 m-0 border-left-warning text-dark shadow-sm">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock fa-2x mr-2"></i>
                                            <div>
                                                <div class="font-weight-bold">PENDING</div>
                                                <small>Menunggu proses BAST</small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- File Download --}}
                            <div>
                                <label class="small text-muted font-weight-bold">File Lampiran</label>
                                @if ($suratJalan->file)
                                    <a href="{{ asset('storage/' . $suratJalan->file) }}" target="_blank" class="btn btn-outline-primary btn-block text-left shadow-sm">
                                        <i class="fas fa-file-pdf mr-2 text-danger"></i> Lihat / Download File
                                    </a>
                                @else
                                    <div class="btn btn-light btn-block text-left text-muted border disabled">
                                        <i class="fas fa-times-circle mr-2"></i> Tidak ada file
                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM BAWAH: TABEL BARANG --}}
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <a href="#collapseCardItems" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardItems">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-box mr-2"></i> Daftar Barang (Items)</h6>
                </a>
                <div class="collapse show" id="collapseCardItems">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Merk</th>
                                        <th>Spesifikasi</th>
                                        <th class="text-center" width="10%">Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($suratJalan->details as $detail)
                                    <tr>
                                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                        <td class="align-middle font-weight-bold">{{ $detail->masterBarang->nama_barang }}</td>
                                        <td class="align-middle">
                                            {{ $detail->masterBarang->kategori->nama_kategori ?? $detail->masterBarang->kategori ?? '-' }}
                                        </td>
                                        <td class="align-middle">{{ $detail->masterBarang->merk }}</td>
                                        <td class="align-middle small">{{ $detail->masterBarang->spesifikasi ?? '-' }}</td>
                                        <td class="text-center align-middle">
                                            <span class="badge badge-primary px-3 py-2" style="font-size: 1rem">
                                                {{ $detail->qty }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-box-open fa-2x mb-2 text-gray-300"></i><br>
                                            Tidak ada barang detail di Surat Jalan ini.
                                        </td>
                                    </tr>
                                    @endForelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection