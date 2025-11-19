@extends('layouts.app') {{-- (Sesuaikan layout lo) --}}

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-history mr-2"></i> {{ $title }}
</h1>

<div class="card shadow mb-4">
    <div class="card-header">
        <a href="{{ route('barangkeluar.create') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-2"></i> Buat (Serah Terima) Baru
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th>No</th>
                        <th>Tgl. Serah Terima</th>
                        <th>Kode Aset</th>
                        <th>Nama Barang</th>
                        <th>Serial Number</th>
                        <th>Penerima (Pemegang)</th>
                        <th>Petugas IT (Admin)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td>{{ \Carbon\Carbon::parse($log->tanggal_serah_terima)->format('d-m-Y') }}</td>
                        <td>{{ $log->aset->kode_asset ?? 'N/A' }}</td>
                        <td>{{ $log->aset->masterBarang->nama_barang ?? 'N/A' }}</td>
                        <td>{{ $log->aset->serial_number ?? 'N/A' }}</td>
                        <td>{{ $log->pemegang->nama ?? 'N/A' }}</td>
                        <td>{{ $log->admin->nama ?? 'N/A' }}</td>
                        <td class="text-center">
                            <a href="{{ route('barangkeluar.show', $log->id) }}" class="btn btn-sm btn-success" title="Lihat Detail BAST">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Belum ada riwayat serah terima.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection