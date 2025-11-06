@extends('layouts.app')
@section('content')

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-users mr-2"></i> {{ $title }}
</h1>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
        
        <div class="mb-1 mr-2">
            <a href="{{ route('team.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i> Tambah Data Team
            </a>
        </div>

        <div>
            <a href="#" class="btn btn-sm btn-success">
                <i class="fas fa-file-excel mr-2"></i> Excel
            </a>
            <a href="#" class="btn btn-sm btn-danger">
                <i class="fas fa-file-pdf mr-2"></i> Pdf
            </a>
        </div>

    </div>

    <div class="card-body">
        <div class="table-responsive">
            
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>Nik</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Jabatan</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($team as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->nik }}</td>
                        <td>{{ $item->nama }}</td>

                        <td class="text-center">
                            <span class="badge badge-primary">{{ $item->email }}</span>
                        </td>

                        <td class="text-center">
                            @switch($item->jabatan)
                                @case('Head IT')
                                    <span class="badge badge-success">{{ $item->jabatan }}</span>
                                    @break
                                @case('IT Support')
                                    <span class="badge badge-primary">{{ $item->jabatan }}</span>
                                    @break
                                @case('Teknisi')
                                    <span class="badge badge-dark">{{ $item->jabatan }}</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ $item->jabatan }}</span>
                            @endswitch
                        </td>

                        <td class="text-center">
                            @switch($item->role)
                                @case('SuperAdmin')
                                    <span class="badge badge-success">{{ $item->role }}</span>
                                    @break
                                @case('Admin')
                                    <span class="badge badge-primary">{{ $item->role }}</span>
                                    @break
                                @case('Staff')
                                    <span class="badge badge-dark">{{ $item->role }}</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ $item->role }}</span>
                            @endswitch
                        </td>

                        <td class="text-center">
                            @switch($item->status)
                                @case('Aktif')
                                    <span class="badge badge-success">{{ $item->status }}</span>
                                    @break
                                @case('Off')
                                    <span class="badge badge-secondary">{{ $item->status }}</span>
                                    @break
                                @case('Izin')
                                    <span class="badge badge-info">{{ $item->status }}</span>
                                    @break
                                @case('Sakit')
                                    <span class="badge badge-warning">{{ $item->status }}</span>
                                    @break
                                @case('Cuti')
                                    <span class="badge badge-primary">{{ $item->status }}</span>
                                    @break
                                @case('Resign')
                                    <span class="badge badge-danger">{{ $item->status }}</span>
                                    @break
                                @case('Nonaktif')
                                    <span class="badge badge-dark">{{ $item->status }}</span>
                                    @break
                                @default
                                    <span class="badge badge-light">{{ $item->status }}</span>
                            @endswitch
                        </td>

                        <td class="text-center">

                            <a href="{{ route('team.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>

                           <a href="#" 
   class="btn btn-sm btn-danger btn-delete" 
   data-id="{{ $item->id }}" 
   data-nama="{{ $item->nama }}">
   <i class="fas fa-trash"></i>
</a>


                            <form id="delete-form-{{ $item->id }}" action="{{ route('team.destroy', $item->id) }}" method="POST" class="d-none">
                                @csrf
                                @method('DELETE')
                            </form>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
                

            </table>
            

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {

            const id = this.dataset.id;
            const nama = this.dataset.nama; // ambil nama dari tombol

            Swal.fire({
                title: "Yakin ingin menghapus " + nama + " ?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
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

});
</script>
@endpush

