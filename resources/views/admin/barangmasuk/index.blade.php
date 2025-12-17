@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-archive mr-2"></i> {{ $title }}
</h1>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Data Barang Masuk</h6>
        
        <div>
            {{-- TOMBOL EXPORT EXCEL (BARU) --}}
            <button type="button" class="btn btn-success btn-sm mr-2" data-toggle="modal" data-target="#exportModal">
                <i class="fas fa-file-excel mr-1"></i> Export Excel
            </button>

            <a href="{{ route('barangmasuk.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus mr-2"></i> Input Aset Baru
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th style="width: 5%">No</th>
                        <th style="width: 15%">Kode Aset</th> 
                        <th>Nama Barang</th>
                        <th>Serial Number</th>
                        <th>Kategori</th>
                        <th>Merk</th>
                        <th>Status</th>
                        <th>Pemegang</th>
                        <th>No. SJ</th>
                        <th style="width: 12%">Aksi</th> 
                    </tr>
                </thead>
                <tbody>
                    @forelse ($barangMasuk as $item)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->iteration }}</td>
                        
                        {{-- 1. LOGIKA KODE ASET --}}
                        <td class="align-middle text-center">
                            @if($item->kode_asset)
                                {{-- Jika Aset Tetap --}}
                                <span class="badge badge-primary shadow-sm px-2 py-1" style="font-size: 12px; letter-spacing: 0.5px;">
                                    <i class="fas fa-barcode mr-1"></i> {{ $item->kode_asset }}
                                </span>
                            @else
                                {{-- Jika Consumable --}}
                                <span class="badge badge-secondary px-2 py-1" style="font-size: 11px;">
                                    <i class="fas fa-box-open mr-1"></i> Habis Pakai
                                </span>
                            @endif
                        </td>

                        <td class="align-middle font-weight-bold">{{ $item->masterBarang->nama_barang ?? '-' }}</td>
                        
                        {{-- 2. LOGIKA SERIAL NUMBER --}}
                        <td class="align-middle text-center">
                            @if($item->serial_number)
                                <span class="text-dark">{{ $item->serial_number }}</span>
                            @else
                                <span class="text-muted font-italic small">-</span>
                            @endif
                        </td>

                        <td class="align-middle">{{ $item->masterBarang->kategori ?? '-' }}</td>
                        
                        <td class="align-middle">{{ $item->masterBarang->merk ?? '-' }}</td>
                        
                        {{-- 3. LOGIKA STATUS --}}
                        <td class="align-middle text-center">
                            @if ($item->status == 'Stok')
                                <span class="badge badge-success">Tersedia</span>
                            @elseif ($item->status == 'Dipakai')
                                <span class="badge badge-warning text-dark">Dipakai</span>
                            @elseif ($item->status == 'Rusak')
                                <span class="badge badge-danger">Rusak</span>
                            @elseif ($item->status == 'Hilang')
                                <span class="badge badge-dark">Hilang</span>
                            @else
                                <span class="badge badge-secondary">{{ $item->status }}</span>
                            @endif
                        </td>
                        
                        <td class="align-middle">
                            @if($item->pemegang)
                                <i class="fas fa-user text-muted mr-1"></i> {{ $item->pemegang->nama ?? $item->pemegang->name }}
                            @else
                                <small class="text-muted">Gudang IT</small>
                            @endif
                        </td>
                        
                        <td class="align-middle text-center">
                            <small>{{ $item->suratJalan->no_sj ?? '-' }}</small>
                        </td>
                        
                        <td class="align-middle text-center">
                            <div class="btn-group" role="group">
                                
                                {{-- TOMBOL CETAK LABEL --}}
                                @if($item->kode_asset)
                                    <a href="{{ route('barangmasuk.cetak_label', $item->id) }}" 
                                       target="_blank" 
                                       class="btn btn-sm btn-dark" 
                                       title="Cetak Label Barcode">
                                        <i class="fas fa-print"></i>
                                    </a>
                                @endif

                                <a href="{{ route('barangmasuk.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-danger btn-delete" 
                                        data-id="{{ $item->id }}" 
                                        data-nama="{{ $item->masterBarang->nama_barang ?? 'Barang' }}"
                                        data-kode="{{ $item->kode_asset ?? 'Consumable' }}" 
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                                
                                <form id="delete-form-{{ $item->id }}" 
                                      action="{{ route('barangmasuk.destroy', $item->id) }}" 
                                      method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-4 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                            Belum ada data aset masuk.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL FILTER EXPORT (BARU) --}}
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="exportModalLabel"><i class="fas fa-file-excel mr-2"></i>Filter Export Excel</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('barangmasuk.export') }}" method="GET">
                <div class="modal-body">
                    
                    {{-- Filter Kondisi --}}
                    <div class="form-group">
                        <label>Status / Kondisi Aset</label>
                        <select name="kondisi" class="form-control">
                            <option value="semua">Semua Status</option>
                            <option value="Stok">Tersedia (Stok)</option>
                            <option value="Dipakai">Sedang Dipakai</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>

                    {{-- Filter Tahun --}}
                    <div class="form-group">
                        <label>Tahun Masuk</label>
                        <select name="tahun" class="form-control">
                            <option value="">Semua Tahun</option>
                            @for ($y = date('Y'); $y >= 2020; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    {{-- Filter Kategori --}}
                    <div class="form-group">
                        <label>Kategori Barang</label>
                        <select name="jenis" class="form-control">
                            <option value="semua">Semua Kategori</option>
                            <option value="Laptop">Laptop</option>
                            <option value="PC">PC / Komputer</option>
                            <option value="Printer">Printer</option>
                            <option value="Server">Server</option>
                            <option value="Network">Network (Switch/Router)</option>
                            <option value="Monitor">Monitor</option>
                        </select>
                        <small class="text-muted">*Pastikan nama kategori sesuai dengan Master Barang.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download mr-1"></i> Download Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- SweetAlert2 Logic --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Inisialisasi DataTable
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    // Logic Tombol Hapus
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            const kode = this.dataset.kode;

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
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@endpush