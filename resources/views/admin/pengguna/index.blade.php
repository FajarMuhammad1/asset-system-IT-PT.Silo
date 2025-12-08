@extends('layouts.app')
@section('content')

<h1 class="h3 mb-4 text-gray-800"><i class="fas fa-users mr-2"></i>{{ $title }}</h1>

<div class="card shadow mb-4">
    <div class="card-header">
        <a href="{{ route('pengguna.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus mr-2"></i> Tambah Pengguna
        </a>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th>Perusahaan/PT</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($penggunas as $p)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $p->nik }}</td>
                        <td>{{ $p->nama }}</td>
                        <td>{{ $p->email }}</td>
                        <td>{{ $p->jabatan }}</td>
                        <td>{{ $p->departemen }}</td>
                        <td>{{ $p->perusahaan ?? '' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('pengguna.edit', $p->id) }}" class="btn btn-sm btn-warning mr-1">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $p->id }}" data-nama="{{ $p->nama }}">
                                    <i class="fas fa-trash"></i>
                                </a>

                                <form id="delete-form-{{ $p->id }}" action="{{ route('pengguna.destroy', $p->id) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
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

@push('scripts')
<script>
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const nama = this.dataset.nama;
            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: nama + " akan dihapus permanen.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus",
                cancelButtonText: "Batal"
            }).then((result) => {
                if(result.isConfirmed){
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        });
    });
</script>
@endpush

@endsection
