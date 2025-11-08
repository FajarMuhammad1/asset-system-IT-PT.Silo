@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Judul Halaman -->
    <h1 class="h3 mb-4 text-gray-800 d-flex align-items-center">
        <i class="fas fa-file-alt text-primary mr-2"></i> 
        Detail Surat Jalan
    </h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row">
                <!-- Kolom Kiri -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-primary">No Surat Jalan</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->no_sj }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">No PPI</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->no_ppi }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">No PO</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->no_po }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Kategori</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->kategori }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Merk</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->merk }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Model</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->model }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Spesifikasi</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->spesifikasi ?? '-' }}</div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="fw-bold text-primary">Serial Number</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->serial_number ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Kode Asset</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->kode_asset ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Qty</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->qty }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Jenis Surat Jalan</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->jenis_surat_jalan }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">Tanggal Input</label>
                        <div class="p-2 border rounded bg-light">
                            {{ \Carbon\Carbon::parse($suratJalan->tanggal_input)->format('d-m-Y') }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">BAST</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->bast ?? '-' }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-primary">File</label>
                        <div class="p-2 border rounded bg-light">
                            @if ($suratJalan->file)
                                <a href="{{ asset('storage/' . $suratJalan->file) }}" target="_blank" class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Lihat / Download File
                                </a>
                            @else
                                <span class="text-muted">Tidak ada file diunggah</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Full Width: Keterangan -->
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="fw-bold text-primary">Keterangan</label>
                        <div class="p-2 border rounded bg-light">{{ $suratJalan->keterangan ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <hr>

<!-- Tombol Aksi -->
<div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mt-4">
    <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary mr-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>

    <a href="{{ route('surat-jalan.edit', $suratJalan->id_sj) }}" class="btn btn-primary mr-3">
        <i class="fas fa-edit"></i> Edit
    </a>

    <button type="button" class="btn btn-danger mr-3" id="btnDelete">
        <i class="fas fa-trash"></i> Hapus
    </button>

    <form id="formDelete" action="{{ route('surat-jalan.destroy', $suratJalan->id_sj) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>


        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('btnDelete').addEventListener('click', function () {
    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: "Data ini akan dihapus secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formDelete').submit();
        }
    });
});
</script>

@if (session('success'))
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil!',
    timer: 2000,
    showConfirmButton: false
});
</script>
@endif
@endpush
