@extends('layouts.app') {{-- (Sesuaikan layout lo) --}}

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-book mr-2"></i> {{ $title }}
</h1>

{{-- Notifikasi Sukses/Error --}}
@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header ">
        <a href="{{ route('master-barang.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Tambah Item Katalog Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Merk</th>
                        <th>Spesifikasi</th>
                        <th><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($masterBarangList as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->kategori }}</td>
                        <td>{{ $item->merk }}</td>
                        <td>{{ $item->spesifikasi }}</td>
                        <td class="text-center">
                            <a href="{{ route('master-barang.edit', $item->id) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete mb-1" data-id="{{ $item->id }}" data-nama="{{ $item->nama_barang }}" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-form-{{ $item->id }}" 
                                  action="{{ route('master-barang.destroy', $item->id) }}" 
                                  method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Katalog masih kosong. Silakan tambah data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Link Paginasi --}}
        <div class="d-flex justify-content-center">
            {{ $masterBarangList->links() }}
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- Script SweetAlert buat Hapus --}}
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;

            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: `Item "${nama}" akan dihapus. (Hanya bisa jika item tidak sedang dipakai)`,
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