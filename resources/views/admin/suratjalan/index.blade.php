@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER & TOMBOL --}}
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-2 mb-md-0 text-gray-800">
            <i class="fas fa-envelope mr-2"></i> {{ $title }}
        </h1>
        
        <div class="d-flex flex-wrap justify-content-center">
            <a href="{{ route('surat-jalan.create') }}" class="btn btn-sm btn-primary shadow-sm mr-2 mb-2 mb-md-0">
                <i class="fas fa-plus mr-1"></i> Input SJ
            </a>
            <a href="#" class="btn btn-sm btn-success shadow-sm mr-2 mb-2 mb-md-0">
                <i class="fas fa-file-excel mr-1"></i> Excel
            </a>
            <a href="#" class="btn btn-sm btn-danger shadow-sm mb-2 mb-md-0">
                <i class="fas fa-file-pdf mr-1"></i> PDF
            </a>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- TAMPILAN DESKTOP (TABEL) - Hidden di HP           --}}
    {{-- ================================================= --}}
    <div class="d-none d-md-block">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-list mr-1"></i> Daftar Surat Jalan</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th>No SJ</th>
                                <th>No PPI</th>
                                <th>No PO</th>
                                <th>Tgl Input</th>
                                <th>Jenis</th>
                                <th>BAST</th>
                                <th>Item</th>
                                <th>File</th>
                                <th width="12%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($suratJalan as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle font-weight-bold text-primary">{{ $item->no_sj }}</td>
                                <td class="align-middle">{{ $item->no_ppi }}</td>
                                <td class="align-middle">{{ $item->no_po }}</td>
                                <td class="align-middle">{{ \Carbon\Carbon::parse($item->tanggal_input)->format('d/m/Y') }}</td>
                                <td class="align-middle">{{ $item->jenis_surat_jalan }}</td>
                                
                                <td class="text-center align-middle">
                                    @if($item->is_bast_submitted)
                                        <span class="badge badge-success px-2 py-1">Sudah</span>
                                    @else
                                        <span class="badge badge-danger px-2 py-1">Belum</span>
                                    @endif
                                </td>
                                
                                <td class="text-center align-middle">{{ $item->details_count }}</td>
                                
                                <td class="text-center align-middle">
                                    @if($item->file)
                                        <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-sm btn-info" title="Lihat File">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('surat-jalan.show', $item->id_sj) }}" class="btn btn-success" title="Detail"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('surat-jalan.edit', $item->id_sj) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-danger btn-delete" 
                                                data-id="{{ $item->id_sj }}" 
                                                data-no="{{ $item->no_sj }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <form id="delete-form-{{ $item->id_sj }}" action="{{ route('surat-jalan.destroy', $item->id_sj) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================================================= --}}
    {{-- TAMPILAN MOBILE (KARTU) - Hidden di Desktop       --}}
    {{-- ================================================= --}}
    <div class="d-md-none">
        @forelse($suratJalan as $item)
        
        {{-- Logic Warna Border (Hijau jika BAST sudah, Kuning jika belum) --}}
        @php
            $borderClass = $item->is_bast_submitted ? 'border-left-success' : 'border-left-warning';
        @endphp

        <div class="card shadow mb-3 {{ $borderClass }}">
            <div class="card-body">
                
                {{-- Baris 1: No SJ & Tanggal --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="font-weight-bold text-primary" style="font-size: 1.1rem;">{{ $item->no_sj }}</span>
                    <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($item->tanggal_input)->format('d M Y') }}</small>
                </div>

                <hr class="my-2">

                {{-- Baris 2: Detail Info --}}
                <div class="row small text-dark mb-2">
                    <div class="col-6 mb-1">
                        <span class="text-muted d-block">No PPI:</span>
                        <strong>{{ $item->no_ppi ?? '-' }}</strong>
                    </div>
                    <div class="col-6 mb-1 text-right">
                        <span class="text-muted d-block">No PO:</span>
                        <strong>{{ $item->no_po ?? '-' }}</strong>
                    </div>
                    <div class="col-6 mt-2">
                         <span class="text-muted d-block">Jenis:</span>
                         {{ $item->jenis_surat_jalan }}
                    </div>
                    <div class="col-6 mt-2 text-right">
                        <span class="text-muted d-block">Jml Item:</span>
                        <span class="badge badge-light border">{{ $item->details_count }} Barang</span>
                    </div>
                </div>

                {{-- Baris 3: Status & File --}}
                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-3">
                    <div>
                        <small class="text-muted mr-1">Status BAST:</small>
                        @if($item->is_bast_submitted)
                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Sudah</span>
                        @else
                            <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> Belum</span>
                        @endif
                    </div>
                    @if($item->file)
                        <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="text-info font-weight-bold small">
                            <i class="fas fa-file-pdf fa-lg"></i> Lihat File
                        </a>
                    @endif
                </div>

                {{-- Baris 4: Tombol Aksi --}}
                <div class="d-flex justify-content-end">
                    <a href="{{ route('surat-jalan.show', $item->id_sj) }}" class="btn btn-sm btn-outline-success mr-2">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                    <a href="{{ route('surat-jalan.edit', $item->id_sj) }}" class="btn btn-sm btn-outline-warning mr-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                        data-id="{{ $item->id_sj }}" 
                        data-no="{{ $item->no_sj }}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                    {{-- Form Delete Hidden (Re-use ID yang sama dengan desktop gpp karena hidden/show beda view) --}}
                </div>

            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
            <p class="text-muted">Belum ada Surat Jalan.</p>
        </div>
        @endforelse
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Init DataTable (Khusus Desktop)
        if($('#dataTable').length) {
            $('#dataTable').DataTable();
        }
    });

    // Event Delegation untuk Tombol Hapus (Bisa untuk Desktop & Mobile)
    document.body.addEventListener('click', function (e) {
        let target = e.target.closest('.btn-delete');
        if (target) {
            const id = target.dataset.id;
            const noSJ = target.dataset.no;

            Swal.fire({
                title: "Hapus Surat Jalan?",
                html: `Nomor SJ: <b>${noSJ}</b><br>Data yang dihapus tidak bisa dikembalikan!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e74a3b",
                cancelButtonColor: "#858796",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    });
</script>
@endpush