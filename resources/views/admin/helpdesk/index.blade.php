@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow">
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No Tiket</th>
                        <th>Judul</th>
                        <th>Pelapor</th>
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
                        <td>
                            <span class="badge badge-info">{{ $t->status }}</span>
                        </td>
                        <td>
                            {{ $t->teknisi->nama ?? '-' }}
                        </td>
                        <td>
                            <a href="{{ route('admin.helpdesk.show', $t->id) }}" class="btn btn-primary btn-sm">
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
@endsection
