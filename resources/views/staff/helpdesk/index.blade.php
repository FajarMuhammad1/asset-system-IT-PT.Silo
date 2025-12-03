@extends('layouts.app')

@section('content')
<div class="container">

    <h1 class="h3 mb-4 text-gray-800">{{ $title }}</h1>

    <div class="card shadow">
        <div class="card-body">
            @if($tickets->isEmpty())
                <div class="alert alert-info">Tidak ada tiket yang ditugaskan.</div>
            @else
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>No Tiket</th>
                            <th>Judul</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $t)
                        <tr>
                            <td>{{ $t->no_tiket }}</td>
                            <td>{{ $t->judul_masalah }}</td>
                            <td><span class="badge badge-info">{{ $t->status }}</span></td>
                            <td>
                                <a href="{{ route('staff.helpdesk.show', $t->id) }}" class="btn btn-sm btn-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
