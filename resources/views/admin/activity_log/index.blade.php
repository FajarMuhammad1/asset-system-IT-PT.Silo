@extends('layouts.app')

@section('title', $title)

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    {{ $title }}
</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Menampilkan 50 aktivitas terakhir</h6>
    </div>

    <div class="card-body">
        <div class="table-responsive">
           <table class="table table-hover table-striped">
    <thead class="thead-light">
        <tr>
            <th width="160">Waktu</th>
            <th width="180">User</th>
            <th width="150">Aksi</th>
            <th>Model</th>
        </tr>
    </thead>

    <tbody>
        @forelse($activities as $activity)
        <tr>
            {{-- WAKTU --}}
            <td class="text-muted">
                {{ $activity->created_at->format('d-m-Y H:i') }}
                <br>
                <small>{{ $activity->created_at->diffForHumans() }}</small>
            </td>

            {{-- USER --}}
            <td>
                <strong>{{ $activity->causer->nama ?? 'System' }}</strong>
                <br>
                <small class="text-muted">{{ $activity->causer->role ?? '' }}</small>
            </td>

            {{-- DESKRIPSI --}}
            <td>
                @switch($activity->description)
                    @case('created')
                        <span class="badge badge-success">Membuat</span>
                        @break
                    @case('updated')
                        <span class="badge badge-warning">Mengupdate</span>
                        @break
                    @case('deleted')
                        <span class="badge badge-danger">Menghapus</span>
                        @break
                    @default
                        <span class="badge badge-secondary">{{ $activity->description }}</span>
                @endswitch
            </td>

            {{-- MODEL --}}
            <td>
                @php
                    $modelName = class_basename($activity->subject_type);
                @endphp

                <span class="badge badge-info">{{ $modelName }}</span>

                <br>
                <small class="text-muted">
                    ID: {{ $activity->subject_id }}
                </small>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center text-muted">Belum ada aktivitas.</td>
        </tr>
        @endforelse
    </tbody>
</table>

        </div>

        {{-- Tombol Paginasi --}}
        <div class="mt-3">
            {{ $activities->links() }}
        </div>
    </div>
</div>
@endsection
