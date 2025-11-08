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
        <th rowspan="2">No</th>
        <th rowspan="2">ID SJ</th>
        <th rowspan="2">No SJ</th>
        <th rowspan="2">No PPI</th>
        <th rowspan="2">No PO</th>
        <th rowspan="2">Kategori</th>
        <th rowspan="2">Merk</th>
        <th rowspan="2">Model</th>
        <th rowspan="2">Spesifikasi</th>
        <th colspan="2">Kode & Serial</th>
        <th rowspan="2">Qty</th>
        <th rowspan="2">Jenis SJ</th>
        <th rowspan="2">Tanggal Input</th>
        <th rowspan="2">BAST</th>
        <th rowspan="2">File</th>
        <th rowspan="2">Keterangan</th>
        <th rowspan="2"><i class="fas fa-cog"></i></th>
    </tr>
    <tr>
        <th>Serial Number</th>
        <th>Kode Asset</th>
    </tr>
</thead>


                <tbody>
                    @foreach ($suratJalan as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ $item->id_sj }}</td>
                        <td>{{ $item->no_sj }}</td>
                        <td>{{ $item->no_ppi }}</td>
                        <td>{{ $item->no_po }}</td>
                        <td>{{ $item->kategori }}</td>
                        <td>{{ $item->merk }}</td>
                        <td>{{ $item->model }}</td>
                        <td>{{ $item->spesifikasi }}</td>
                        <td>{{ $item->serial_number }}</td>
                        <td>{{ $item->kode_asset }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td>{{ $item->jenis_surat_jalan }}</td>
                        <td>{{ $item->tanggal_input }}</td>
                        <td>{{ $item->bast }}</td>
                        <td class="text-center">
                            @if($item->file)
                                <a href="{{ asset('storage/' . $item->file) }}" target="_blank" class="btn btn-sm btn-info">Lihat</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('surat-jalan.update', $item->id_sj) }}" class="btn btn-sm btn-warning mb-1">
                                <i class="fas fa-edit"></i>
                            </a>

                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-id="{{ $item->id_sj }}" data-no="{{ $item->no_sj }}">
                                <i class="fas fa-trash"></i>
                            </button>

                            <form id="delete-form-{{ $item->id_sj }}" action="{{ route('surat-jalan.destroy', $item->id_sj) }}" method="POST" class="d-none">
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
