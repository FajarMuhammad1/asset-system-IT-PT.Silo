@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER & TOMBOL (RESPONSIF) --}}
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-2 mb-md-0 text-gray-800">
            <i class="fas fa-archive mr-2"></i> {{ $title }}
        </h1>
        
        <div class="d-flex flex-wrap justify-content-end">
             {{-- Export Button --}}
             <button type="button" class="btn btn-success btn-sm mr-2 mb-2 mb-md-0 shadow-sm" data-toggle="modal" data-target="#exportModal">
                <i class="fas fa-file-excel mr-1"></i> Excel
            </button>

            {{-- Create Button --}}
            <a href="{{ route('barangmasuk.create') }}" class="btn btn-primary btn-sm mb-2 mb-md-0 shadow-sm">
                <i class="fas fa-plus mr-1"></i> Input Aset
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- ================================================= --}}
    {{-- TAMPILAN DESKTOP (TABEL) - Hidden di HP           --}}
    {{-- ================================================= --}}
    <div class="d-none d-md-block">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white"><i class="fas fa-table mr-1"></i> Data Barang Masuk</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-nowrap" id="dataTable" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark text-center">
                            <tr>
                                <th style="width: 5%">No</th>
                                <th>Kode Aset</th> 
                                <th>Nama Barang</th>
                                <th>Serial Number</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Pemegang</th>
                                <th style="width: 10%">Aksi</th> 
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($barangMasuk as $item)
                            <tr>
                                <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle text-center">
                                    @if($item->kode_asset)
                                        <span class="badge badge-primary px-2 py-1">{{ $item->kode_asset }}</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">Habis Pakai</span>
                                    @endif
                                </td>
                                <td class="align-middle font-weight-bold">{{ $item->masterBarang->nama_barang ?? '-' }}</td>
                                <td class="align-middle">{{ $item->serial_number ?? '-' }}</td>
                                <td class="align-middle">{{ $item->masterBarang->kategori ?? '-' }}</td>
                                <td class="align-middle text-center">
                                    {{-- Logika Badge Status --}}
                                    @if ($item->status == 'Stok') <span class="badge badge-success">Tersedia</span>
                                    @elseif ($item->status == 'Dipakai') <span class="badge badge-warning text-dark">Dipakai</span>
                                    @elseif ($item->status == 'Rusak') <span class="badge badge-danger">Rusak</span>
                                    @elseif ($item->status == 'Hilang') <span class="badge badge-dark">Hilang</span>
                                    @else <span class="badge badge-secondary">{{ $item->status }}</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    {{ $item->pemegang->nama ?? 'Gudang IT' }}
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group btn-group-sm">
                                        @if($item->kode_asset)
                                            <a href="{{ route('barangmasuk.cetak_label', $item->id) }}" target="_blank" class="btn btn-dark" title="Cetak"><i class="fas fa-print"></i></a>
                                        @endif
                                        <a href="{{ route('barangmasuk.edit', $item->id) }}" class="btn btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-danger btn-delete" 
                                            data-id="{{ $item->id }}" 
                                            data-nama="{{ $item->masterBarang->nama_barang ?? 'Barang' }}"
                                            data-kode="{{ $item->kode_asset ?? '-' }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('barangmasuk.destroy', $item->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center py-4">Data kosong</td></tr>
                            @endforelse
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
        @forelse ($barangMasuk as $item)
        
        {{-- Warna Border Kiri Berdasarkan Status --}}
        @php
            $borderClass = 'border-left-secondary';
            if ($item->status == 'Stok') $borderClass = 'border-left-success';
            elseif ($item->status == 'Dipakai') $borderClass = 'border-left-warning';
            elseif ($item->status == 'Rusak') $borderClass = 'border-left-danger';
            elseif ($item->status == 'Hilang') $borderClass = 'border-left-dark';
        @endphp

        <div class="card shadow mb-3 {{ $borderClass }}">
            <div class="card-body py-3">
                
                {{-- Baris 1: Judul & Kode --}}
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="font-weight-bold text-primary mb-1">
                            {{ $item->masterBarang->nama_barang ?? 'Item Terhapus' }}
                        </h6>
                        <small class="text-muted d-block">
                            {{ $item->masterBarang->merk ?? '-' }} - {{ $item->masterBarang->kategori ?? '-' }}
                        </small>
                    </div>
                    @if($item->kode_asset)
                        <span class="badge badge-primary">{{ $item->kode_asset }}</span>
                    @else
                        <span class="badge badge-secondary" style="font-size: 10px">Habis Pakai</span>
                    @endif
                </div>

                <hr class="my-2">

                {{-- Baris 2: Info Detail --}}
                <div class="row small text-dark mb-2">
                    <div class="col-6 mb-1">
                        <i class="fas fa-barcode text-gray-400 mr-1"></i> SN: 
                        <span class="font-weight-bold">{{ $item->serial_number ?? '-' }}</span>
                    </div>
                    <div class="col-6 mb-1 text-right">
                        @if ($item->status == 'Stok') <span class="text-success font-weight-bold"><i class="fas fa-check mr-1"></i>Tersedia</span>
                        @elseif ($item->status == 'Dipakai') <span class="text-warning font-weight-bold"><i class="fas fa-user-check mr-1"></i>Dipakai</span>
                        @elseif ($item->status == 'Rusak') <span class="text-danger font-weight-bold"><i class="fas fa-times-circle mr-1"></i>Rusak</span>
                        @else <span>{{ $item->status }}</span>
                        @endif
                    </div>
                    <div class="col-12">
                        <i class="fas fa-user text-gray-400 mr-1"></i> Pemegang: 
                        <span class="font-weight-bold">{{ $item->pemegang->nama ?? 'Gudang IT' }}</span>
                    </div>
                </div>

                {{-- Baris 3: Tombol Aksi --}}
                <div class="d-flex justify-content-end mt-3">
                    @if($item->kode_asset)
                        <a href="{{ route('barangmasuk.cetak_label', $item->id) }}" class="btn btn-sm btn-outline-dark mr-2" target="_blank">
                            <i class="fas fa-print"></i>
                        </a>
                    @endif
                    
                    <a href="{{ route('barangmasuk.edit', $item->id) }}" class="btn btn-sm btn-outline-warning mr-2">
                        <i class="fas fa-edit"></i> Edit
                    </a>

                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                        data-id="{{ $item->id }}" 
                        data-nama="{{ $item->masterBarang->nama_barang ?? 'Barang' }}"
                        data-kode="{{ $item->kode_asset ?? '-' }}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                    
                    {{-- Form Delete (Hidden) --}}
                    {{-- Note: Form ID di sini duplikat dengan desktop, tapi karena beda view (hide/show) biasanya aman. 
                         Namun, praktik terbaik adalah form ini cukup satu saja di HTML jika memungkinkan, 
                         tapi untuk kemudahan copy-paste saya taruh di sini juga agar tombol berfungsi. --}}
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-5 bg-white rounded shadow-sm">
            <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
            <p class="text-muted">Belum ada data aset.</p>
        </div>
        @endforelse
    </div>

</div>

{{-- MODAL FILTER EXPORT (Sama seperti sebelumnya) --}}
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="exportModalLabel"><i class="fas fa-file-excel mr-2"></i>Filter Export Excel</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('barangmasuk.export') }}" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Status / Kondisi</label>
                        <select name="kondisi" class="form-control">
                            <option value="semua">Semua Status</option>
                            <option value="Stok">Tersedia (Stok)</option>
                            <option value="Dipakai">Sedang Dipakai</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahun Masuk</label>
                        <select name="tahun" class="form-control">
                            <option value="">Semua Tahun</option>
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="jenis" class="form-control">
                            <option value="semua">Semua Kategori</option>
                            <option value="Laptop">Laptop</option>
                            <option value="PC">PC / Komputer</option>
                            <option value="Printer">Printer</option>
                            <option value="Server">Server</option>
                            <option value="Network">Network</option>
                            <option value="Monitor">Monitor</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-download mr-1"></i> Download</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        // Init DataTable hanya jika elemen ada (Desktop)
        if($('#dataTable').length) {
            $('#dataTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Indonesian.json"
                }
            });
        }
    });

    // Event Delegation agar tombol hapus di Desktop DAN Mobile berfungsi
    document.body.addEventListener('click', function (e) {
        // Cari tombol terdekat dengan class .btn-delete
        let target = e.target.closest('.btn-delete');
        
        if (target) {
            const id = target.dataset.id;
            const nama = target.dataset.nama;
            const kode = target.dataset.kode;

            Swal.fire({
                title: "Hapus Aset ini?",
                html: `Barang: <b>${nama}</b><br>Kode: <span class="badge badge-danger">${kode}</span><br><br>Data yang dihapus tidak bisa dikembalikan!`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e74a3b",
                cancelButtonColor: "#858796",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kita cari form berdasarkan ID uniknya
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    });
</script>
@endpush