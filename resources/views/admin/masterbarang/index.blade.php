@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-book mr-2"></i> {{ $title ?? 'Master Data Katalog' }}
    </h1>

    {{-- Notifikasi Sukses/Error --}}
    @if (session('success'))
        <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger border-left-danger shadow-sm alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- 1. KELOMPOKKAN DATA SEPERTI SISTEM TABS --}}
    @php
        $isPaginator = $masterBarangList instanceof \Illuminate\Pagination\AbstractPaginator;
        $collection = $isPaginator ? collect($masterBarangList->items()) : $masterBarangList;
        
        // Ambil nama kategori unik
        $kategoriUnik = $collection->pluck('kategori')->filter()->unique()->sort();

        // Setup wadah data (Sama seperti Tabs)
        $wadahTabel = [
            ['id' => 'all', 'label' => 'Semua Barang', 'color' => 'primary', 'data' => $collection],
        ];

        $warna = ['info', 'success', 'warning', 'secondary', 'dark', 'danger'];
        $i = 0;
        foreach($kategoriUnik as $kat) {
            $wadahTabel[] = [
                'id' => Str::slug($kat),
                'label' => strtoupper($kat),
                'color' => $warna[$i % count($warna)], 
                'data' => $collection->where('kategori', $kat)
            ];
            $i++;
        }
    @endphp

    <div class="card shadow mb-4 border-bottom-primary">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Item Katalog</h6>
            <a href="{{ route('master-barang.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Item Baru
            </a>
        </div>
        
        <div class="card-body">
            
            {{-- 2. DROPDOWN SEBAGAI PENGGANTI TOMBOL TABS --}}
            <div class="row mb-4">
                <div class="col-md-4 col-sm-6">
                    <label for="dropdownTabFilter" class="font-weight-bold text-gray-800">
                        <i class="fas fa-filter text-primary"></i> Pilih Kategori Aset:
                    </label>
                    <select id="dropdownTabFilter" class="form-control border-left-primary shadow-sm font-weight-bold">
                        @foreach($wadahTabel as $index => $tab)
                            <option value="#pane-{{ $tab['id'] }}">{{ $tab['label'] }} ({{ $tab['data']->count() }} Item)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- 3. KONTEN TABEL (Tabel Terpisah Seperti Tabs) --}}
            <div class="tab-content" id="myTabContent">
                @foreach($wadahTabel as $index => $tab)
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="pane-{{ $tab['id'] }}">
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover datatable-multi" width="100%" cellspacing="0">
                            <thead class="bg-primary text-white text-center">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Merk</th>
                                    <th>Spesifikasi</th>
                                    <th width="15%"><i class="fas fa-cog"></i> Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tab['data'] as $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold text-dark align-middle">{{ $item->nama_barang }}</td>
                                    
                                    <td class="align-middle text-center">
                                        <span class="badge badge-light border border-{{ $tab['color'] }} text-{{ $tab['color'] }} px-2 py-1">
                                            {{ strtoupper($item->kategori) }}
                                        </span>
                                    </td>
                                    
                                    <td class="align-middle">{{ $item->merk ?? '-' }}</td>
                                    <td class="align-middle small">{{ Str::limit($item->spesifikasi ?? '-', 50) }}</td>
                                    
                                    <td class="text-center align-middle">
                                        <a href="{{ route('master-barang.edit', $item->id) }}" class="btn btn-sm btn-warning shadow-sm mb-1" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button type="button" class="btn btn-sm btn-danger btn-delete shadow-sm mb-1" data-id="{{ $item->id }}" data-nama="{{ $item->nama_barang }}" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        
                                        <form id="delete-form-{{ $item->id }}" action="{{ route('master-barang.destroy', $item->id) }}" method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-5">
                                        <i class="fas fa-box-open fa-3x mb-3 text-gray-300"></i><br>
                                        Katalog kategori ini masih kosong.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
                @endforeach
            </div>

            {{-- Link Paginasi Bawaan Laravel --}}
            @if($isPaginator)
            <div class="d-flex justify-content-center mt-3">
                {{ $masterBarangList->links() }}
            </div>
            @endif

        </div>
    </div>
</div>

{{-- SCRIPT PENGGERAK DROPDOWN, DATATABLES, & SWEETALERT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // 1. LOGIKA DROPDOWN SEBAGAI TABS FILTER
        const dropdownFilter = document.getElementById('dropdownTabFilter');
        dropdownFilter.addEventListener('change', function() {
            // Ambil target ID wadah tabel dari value option yang dipilih
            const targetPaneId = this.value; 

            // Sembunyikan semua tabel
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.classList.remove('show', 'active');
            });

            // Tampilkan tabel yang sesuai dengan dropdown
            const targetPane = document.querySelector(targetPaneId);
            if(targetPane) {
                targetPane.classList.add('show', 'active');
            }

            // Perbaiki tampilan DataTables agar lebarnya menyesuaikan saat wadah baru terbuka
            if (window.jQuery && $.fn.DataTable) {
                $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });

        // 2. INISIALISASI DATATABLES
        var initDataTables = setInterval(function() {
            if (window.jQuery && $.fn.DataTable) {
                clearInterval(initDataTables);
                
                $('.datatable-multi').DataTable({
                    "pageLength": 10,
                    // Karena sudah ada paginasi Laravel, kita matikan paging bawaan datatables 
                    // agar tidak terjadi paginasi ganda (Paginasi di dalam paginasi)
                    "paging": {{ $isPaginator ? 'false' : 'true' }}, 
                    "info": {{ $isPaginator ? 'false' : 'true' }},
                    "language": {
                        "search": "Cari data:",
                        "emptyTable": "Belum ada data barang."
                    }
                });
            }
        }, 100);

        // 3. INISIALISASI SWEET ALERT UNTUK HAPUS
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const id = this.dataset.id;
                const nama = this.dataset.nama;

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: "Yakin ingin menghapus?",
                        text: `Item "${nama}" akan dihapus permanen.`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, Hapus",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('delete-form-' + id).submit();
                        }
                    });
                } else {
                    if(confirm(`Yakin ingin menghapus item "${nama}"?`)) {
                        document.getElementById('delete-form-' + id).submit();
                    }
                }
            });
        });

    });
</script>
@endsection