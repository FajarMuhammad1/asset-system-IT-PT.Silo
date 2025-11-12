@extends('layouts.app')
@section('content')

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-envelope mr-2"></i> {{ $title }}
</h1>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
        <div class="mb-1 mr-2">
            <a href="{{ route('surat-jalan.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i> Tambah Surat Jalan
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
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white text-center">
                    <tr>
                        <th>No</th>
                        <th>Id SJ </th> {{-- KOLOM BARU LO --}}
                        <th>No SJ</th>
                        <th>No PPI</th>
                        <th>No PO</th>
                        <th>Tanggal Input</th>
                        <th>Jenis SJ</th>
                        <th> BAST</th>
                        <th>Jml. Item</th>
                        <th>File</th>
                        <th><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suratJalan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->id_suratjalan }}</td> {{-- DATA KOLOM BARU LO --}}
                        <td>{{ $item->no_sj }}</td>
                        <td>{{ $item->no_ppi }}</td>
                        <td>{{ $item->no_po }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_input)->format('d-m-Y') }}</td>
                        <td>{{ $item->jenis_surat_jalan }}</td>
                        
                        <td class="text-center">
                            @if($item->is_bast_submitted)
                                <span class="badge badge-success">Sudah</span>
                            @else
                                <span class="badge badge-danger">Belum</span>
                            @endif
                        </td>
                        
                        <td class="text-center">{{ $item->details_count }} Jenis</td>
                        
                        <td class="text-center">
                            @if($item->file)
                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('surat-jalan.show', $item->id_sj) }}" class="btn btn-sm btn-success mb-1" title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('surat-jalan.edit', $item->id_sj) }}" class="btn btn-sm btn-warning mb-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-delete mb-1" 
                                    data-id="{{ $item->id_sj }}" 
                                    data-no="{{ $item->no_sj }}" 
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                            <form id="delete-form-{{ $item->id_sj }}" 
                                    action="{{ route('surat-jalan.destroy', $item->id_sj) }}" 
                                    method="POST" class="d-none">
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
{{-- (Kode SweetAlert lo udah bener, biarin aja) --}}
<script>
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const noSJ = this.dataset.no;
            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Surat Jalan No: " + noSJ + " akan dihapus permanen.",
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