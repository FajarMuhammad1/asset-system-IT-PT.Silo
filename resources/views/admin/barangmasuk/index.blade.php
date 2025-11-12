@extends('layouts.app') {{-- (Sesuaikan layout lo) --}}

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-archive mr-2"></i> {{ $title }}
</h1>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header">
        <a href="{{ route('barangmasuk.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Input Aset (Barang Masuk) Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode Aset</th>
                        <th>Serial Number</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Merk</th>
                        <th>Status</th>
                        <th>Pengguna/User</th>
                        <th>Id SJ </th>
                        <th>No SJ</th>
                        <th><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Pastiin controller lo pake: BarangMasuk::with('masterBarang', 'suratJalan', 'pemegang') --}}
                    @forelse ($barangMasuk as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->kode_asset }}</td>
                        <td>{{ $item->serial_number }}</td>
                        {{-- Data dari Relasi 'masterBarang' --}}
                        <td>{{ $item->masterBarang->nama_barang ?? 'N/A' }}</td>
                        <td>{{ $item->masterBarang->kategori ?? 'N/A' }}</td>
                        <td>{{ $item->masterBarang->merk ?? 'N/A' }}</td>
                        
                        {{-- Status Aset --}}
                        <td class="text-center">
                            @if ($item->status == 'Stok')
                                <span class="badge badge-success">{{ $item->status }}</span>
                            @elseif ($item->status == 'Dipakai')
                                <span class="badge badge-warning">{{ $item->status }}</span>
                            @else
                                <span class="badge badge-danger">{{ $item->status }}</span>
                            @endif
                        </td>
                        
                        {{-- Data dari Relasi 'pemegang' (User) --}}
                        <td>{{ $item->pemegang->nama ?? '-' }}</td>
                        
                        {{-- Data dari Relasi 'suratJalan' --}}
                        <td>{{ $item->suratJalan->id_suratjalan ?? 'N/A' }}</td>
                        <td>{{ $item->suratJalan->no_sj ?? 'N/A' }}</td>
                        
                        <td class="text-center">
                            <a href="{{ route('barangmasuk.show', $item->id) }}" class="btn btn-sm btn-success mb-1" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('barangmasuk.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1" title="Edit Aset">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete mb-1" 
                                    data-id="{{ $item->id }}" 
                                    data-sn="{{ $item->serial_number }}" 
                                    title="Hapus Aset">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-form-{{ $item->id }}" 
                                  action="{{ route('barangmasuk.destroy', $item->id) }}" 
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center">Belum ada data aset. Silakan input barang masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script SweetAlert buat Hapus Aset --}}
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const sn = this.dataset.sn;

            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: `Aset dengan SN: "${sn}" akan dihapus permanen.`,
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
        });
    });
</script>
@endpush