@extends('layouts.app')

@section('title', 'Riwayat Serah Terima Aset')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-history mr-2"></i> {{ $title }}
    </h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar BAST</h6>
            <a href="{{ route('barangkeluar.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus mr-2"></i> Buat Serah Terima Baru
            </a>
        </div>

        <div class="card-body">
            {{-- Alert Sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Aset</th>
                            <th>Penerima</th> 
                            <th>Petugas IT</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Aksi</th> {{-- Lebar kolom aksi ditambah sedikit --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->tanggal_serah_terima)->format('d/m/Y') }}</td>
                            
                            {{-- KOLOM ASET --}}
                            <td>
                                <strong>{{ $log->aset->kode_asset ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $log->aset->masterBarang->nama_barang ?? '-' }}</small>
                            </td>

                            {{-- KOLOM PENERIMA --}}
                            <td>
                                <strong>{{ $log->pemegang->nama ?? '-' }}</strong>
                                <br>
                                <small class="text-muted" style="font-size: 0.85em;">
                                    <i class="fas fa-briefcase mr-1"></i> {{ $log->pemegang->jabatan ?? 'Staff' }}
                                    <br>
                                    <i class="fas fa-building mr-1"></i> {{ $log->pemegang->perusahaan ?? 'PT. SILO' }}
                                </small>
                            </td>

                            <td>{{ $log->admin->nama ?? '-' }}</td>
                            
                            {{-- KOLOM STATUS --}}
                            <td class="text-center align-middle">
                                @if($log->status == 'selesai')
                                    <span class="badge badge-success px-2 py-1">
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </span>
                                @elseif($log->status == 'menunggu_ttd_user')
                                    <span class="badge badge-warning px-2 py-1 text-dark">
                                        <i class="fas fa-clock"></i> Menunggu User
                                    </span>
                                @elseif($log->status == 'menunggu_ttd_admin')
                                    <span class="badge badge-info px-2 py-1">
                                        <i class="fas fa-pen-alt"></i> Menunggu Admin
                                    </span>
                                @else
                                    <span class="badge badge-secondary">{{ $log->status }}</span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI (UPDATE DI SINI) --}}
                            <td class="text-center align-middle">
                                <div class="btn-group" role="group">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('barangkeluar.show', $log->id) }}" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- [BARU] Tombol Cetak PDF --}}
                                    <a href="{{ route('barangkeluar.cetak', $log->id) }}" target="_blank" class="btn btn-sm btn-danger shadow-sm ml-1" title="Cetak BAST (PDF)">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                                Belum ada riwayat serah terima aset.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script DataTables --}}
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[ 0, "desc" ]], // Urutkan dari yang terbaru
            "language": {
                "emptyTable": "Tidak ada data tersedia",
                "search": "Cari Data:",
                "lengthMenu": "Tampilkan _MENU_ baris",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Lanjut",
                    "previous": "Mundur"
                }
            }
        });
    });
</script>
@endpush