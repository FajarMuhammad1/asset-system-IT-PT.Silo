@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light">
                        <tr>
                            <th>No Tiket</th>
                            <th>Judul</th>
                            <th>Pelapor</th>
                            <th>Jabatan</th>
                            <th>Departemen</th>
                            <th>Perusahaan</th>
                            <th>Status</th>
                            <th>Teknisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($tickets as $t)
                        <tr>
                            <td>{{ $t->no_tiket }}</td>
                            <td>{{ $t->judul_masalah }}</td>

                            <td>{{ $t->pelapor->nama }}</td>
                            <td>{{ $t->pelapor->jabatan ?? '-' }}</td>
                            <td>{{ $t->pelapor->departemen ?? '-' }}</td>
                            <td>{{ $t->pelapor->perusahaan ?? '-' }}</td>

                                <td>
                                    @if($t->status == 'Open')
                                        {{-- Warna Kuning --}}
                                        <span class="badge badge-warning text-dark">{{ $t->status }}</span>
                                    
                                    @elseif($t->status == 'Progres')
                                        {{-- Warna Biru Langit --}}
                                        <span class="badge badge-info">{{ $t->status }}</span>
                                    
                                    @elseif($t->status == 'Closed')
                                        {{-- Warna Hijau --}}
                                        <span class="badge badge-success">{{ $t->status }}</span>
                                    
                                    @elseif($t->status == 'Reject')
                                        {{-- Warna Merah --}}
                                        <span class="badge badge-danger">{{ $t->status }}</span>
                                    
                                    @else
                                        {{-- Warna Default (Abu-abu) --}}
                                        <span class="badge badge-secondary">{{ $t->status }}</span>
                                    @endif
                                </td>

                            <td>{{ $t->teknisi->nama ?? '-' }}</td>

                            <td>
                                <a href="{{ route('admin.helpdesk.show', $t->id) }}" 
                                   class="btn btn-primary btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>
@endsection
