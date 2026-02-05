@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800"><i class="fas fa-envelope mr-2"></i> {{ $title ?? 'Daftar Surat Jalan' }}</h1>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-left-danger shadow-sm" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Data Surat Jalan</h6>
            
            <div class="d-flex">
                {{-- TOMBOL INPUT SJ BARU --}}
                <a href="{{ route('surat-jalan.create') }}" class="btn btn-primary btn-sm shadow-sm mr-2">
                    <i class="fas fa-plus"></i> Input SJ
                </a>

                {{-- TOMBOL BUKA MODAL FILTER & EXPORT --}}
                <button type="button" class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalExport">
                    <i class="fas fa-print"></i> Filter & Export
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th>No SJ</th>
                            <th>No PPI</th>
                            <th>No PO</th>
                            <th>Tgl Input</th>
                            <th>Status BAST</th>
                            <th>Item</th>
                            <th>File</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suratJalan as $item)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle font-weight-bold">{{ $item->no_sj }}</td>
                            <td class="align-middle">{{ $item->no_ppi ?? '-' }}</td>
                            <td class="align-middle">{{ $item->no_po ?? '-' }}</td>
                            <td class="align-middle text-center">{{ \Carbon\Carbon::parse($item->tanggal_input)->format('d/m/Y') }}</td>
                            
                            {{-- Status BAST --}}
                            <td class="align-middle text-center">
                                @if($item->is_bast_submitted)
                                    <span class="badge badge-success px-2 py-1">Sudah BAST</span>
                                @else
                                    <span class="badge badge-warning px-2 py-1">Belum BAST</span>
                                @endif
                            </td>

                            <td class="align-middle text-center">{{ $item->details_count }} Brg</td>

                            {{-- File Attachment --}}
                            <td class="align-middle text-center">
                                @if($item->file)
                                    <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat File">
                                        <i class="fas fa-paperclip"></i>
                                    </a>
                                @else
                                    <span class="text-muted text-xs font-italic">No File</span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center">


                                    <a href="{{ route('surat-jalan.show', $item->id_sj) }}" class="btn btn-info btn-sm mr-1" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('surat-jalan.edit', $item->id_sj) }}" class="btn btn-warning btn-sm mr-1" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="{{ route('surat-jalan.destroy', $item->id_sj) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus Surat Jalan {{ $item->no_sj }}? Data yang dihapus tidak bisa dikembalikan.')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

{{-- MODAL FILTER EXPORT --}}
<div class="modal fade" id="modalExport" tabindex="-1" role="dialog" aria-labelledby="modalExportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalExportLabel"><i class="fas fa-filter mr-1"></i> Filter & Export Laporan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            {{-- Form Default Action ke Excel --}}
            <form action="{{ route('surat-jalan.export-excel') }}" method="GET" target="_blank">
                <div class="modal-body">
                    
                    {{-- OPSI 1: PER HARIAN --}}
                    <div class="alert alert-secondary p-2 mb-3">
                        <strong>Opsi 1: Laporan Harian</strong>
                        <div class="form-group mb-0 mt-1">
                            <input type="date" name="tanggal" class="form-control">
                            <small class="text-danger">*Isi ini jika ingin laporan per tanggal spesifik.</small>
                        </div>
                    </div>

                    <div class="text-center font-weight-bold mb-3 text-gray-500">--- ATAU ---</div>

                    {{-- OPSI 2: BULANAN --}}
                    <div class="alert alert-secondary p-2 mb-3">
                        <strong>Opsi 2: Laporan Bulanan</strong>
                        <div class="row mt-2">
                            <div class="col-6">
                                <label>Bulan</label>
                                <select name="bulan" class="form-control">
                                    <option value="">-- Pilih --</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>
                                            {{ date("F", mktime(0, 0, 0, $i, 10)) }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <label>Tahun</label>
                                <select name="tahun" class="form-control">
                                    @php $thn = date('Y'); @endphp
                                    @for ($y = $thn; $y >= $thn - 3; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- FILTER STATUS BAST --}}
                    <div class="form-group">
                        <label class="font-weight-bold">Filter Status BAST (Opsional)</label>
                        <select name="status_bast" class="form-control">
                            <option value="">-- Semua Status --</option>
                            <option value="sudah">Sudah BAST</option>
                            <option value="belum">Belum BAST</option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    
                    {{-- TOMBOL DOWNLOAD EXCEL (Menggunakan action default form) --}}
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>

                    {{-- TOMBOL DOWNLOAD PDF (Override Action ke route PDF) --}}
                    <button type="submit" class="btn btn-danger" formaction="{{ route('surat-jalan.export-pdf') }}">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
             "order": [[ 4, "desc" ]] // Default sort by Tanggal Input (Kolom ke-5 / index 4) descending
        });
    });
</script>
@endpush