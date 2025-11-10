@extends('layouts.app')
@section('content')

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-box mr-2"></i> {{ $title }}
</h1>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-center justify-content-xl-between">
        <div class="mb-1 mr-2">
            <a href="{{ route('barangmasuk.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus mr-2"></i> Tambah Barang Masuk
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
                        <th>No SJ</th>
                        <th>No PPI</th>
                        <th>No PO</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Jumlah</th>
                        <th>Tanggal Masuk</th>
                        <th>Keterangan</th>
                        <th><i class="fas fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barangMasuk as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->no_sj }}</td>
                        <td>{{ $item->no_ppi }}</td>
                        <td>{{ $item->no_po }}</td>
                        <td>{{ $item->nama_barang }}</td>
                        <td>{{ $item->kategori }}</td>
                        <td class="text-center">{{ $item->jumlah }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="text-center">
                           
      <div class="d-flex justify-content-center align-items-center">
    <a href="{{ route('barangmasuk.show', $item->id) }}" class="btn btn-sm btn-warning mr-1">
        <i class="fas fa-edit"></i>
    </a>

    <a href="#" class="btn btn-sm btn-danger btn-delete" 
       data-id="{{ $item->id }}" data-no="{{ $item->no_sj }}">
        <i class="fas fa-trash"></i>
    </a>
</div>



                            <form id="delete-form-{{ $item->id }}" 
                                action="{{ route('barangmasuk.destroy', $item->id) }}" 
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
<script>
    const deleteButtons = document.querySelectorAll('.btn-delete');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            const noSJ = this.dataset.no;

            Swal.fire({
                title: "Yakin ingin menghapus?",
                text: "Barang Masuk dengan No SJ: " + noSJ + " akan dihapus permanen.",
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
