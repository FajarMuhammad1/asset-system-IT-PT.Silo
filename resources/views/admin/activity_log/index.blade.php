@extends('layouts.app')

@section('title', $title)

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-list mr-2"></i> {{ $title }}
</h1>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Menampilkan 50 aktivitas terakhir</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Waktu</th>
                        <th>User (Pengguna)</th>
                        <th>Deskripsi</th>
                        <th>Model</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activities as $activity)
                    <tr>
                        {{-- WAKTU --}}
                        <td>{{ $activity->created_at->format('d-m-Y H:i:s') }}</td>
                        
                        {{-- SIAPA --}}
                        <td>{{ $activity->causer->nama ?? 'System' }}</td>

                        {{-- APA --}}
                        <td>
                            @if($activity->description == 'created')
                                <span class="badge badge-success">MEMBUAT</span>
                            @elseif($activity->description == 'updated')
                                <span class="badge badge-warning">MENGUPDATE</span>
                            @elseif($activity->description == 'deleted')
                                <span class="badge badge-danger">MENGHAPUS</span>
                            @else
                                {{ $activity->description }}
                            @endif
                        </td>
                        
                        {{-- DI MANA --}}
                        <td>
                            {{-- Ini bakal nampilin 'App\Models\Ppi' --}}
                            {{ $activity->subject_type }}
                            (ID: {{ $activity->subject_id }})
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada aktivitas.</td>
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