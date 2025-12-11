@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

<div class="card shadow">
    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead class="bg-primary text-white">
                <tr>
                    <th>No</th>
                    <th>Kode Aset</th>
                    <th>Nama Barang</th>
                    <th>Tanggal</th>
                    <th>Admin</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bastList as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->aset->kode_asset }}</td>
                    <td>{{ $item->aset->masterBarang->nama_barang }}</td>
                    <td>{{ $item->tanggal_serah_terima }}</td>
                    <td>{{ $item->admin->nama }}</td>
                    <td>
                        <a href="{{ route('pengguna.userbast.sign', $item->id) }}" class="btn btn-success btn-sm">
                            Tanda Tangan
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center">Tidak ada dokumen BAST untuk ditandatangani.</td></tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection
