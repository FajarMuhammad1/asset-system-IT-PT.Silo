@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Detail Tiket: {{ $ticket->no_tiket }}</h1>

    <div class="card shadow mb-4">
        <div class="card-body">

            <!-- Informasi Tiket -->
            <h4 class="font-weight-bold">{{ $ticket->judul_masalah }}</h4>
            <p class="text-muted">{{ $ticket->deskripsi }}</p>

            @if($ticket->foto_masalah)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $ticket->foto_masalah) }}"
                         class="img-thumbnail rounded"
                         style="max-width: 300px;">
                </div>
            @endif

            <table class="table table-bordered">
                <tr>
                    <th width="200">Pelapor</th>
                    <td>{{ $ticket->pelapor->nama }}</td>
                </tr>
                <tr>
                    <th>Prioritas</th>
                    <td>{{ $ticket->prioritas }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge badge-info">{{ $ticket->status }}</span>
                    </td>
                </tr>
                <tr>
                    <th>Teknisi</th>
                    <td>
                        @if($ticket->teknisi)
                            <span class="badge badge-success">{{ $ticket->teknisi->nama }}</span>
                        @else
                            <span class="badge badge-secondary">Belum di-assign</span>
                        @endif
                    </td>
                </tr>
            </table>

            <hr>

            <!-- FORM ASSIGN TEKNISI -->
            <h4 class="font-weight-bold mb-3">Assign Teknisi</h4>

            <form action="{{ route('admin.helpdesk.assign', $ticket->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label><strong>Pilih Teknisi</strong></label>
                    <select name="teknisi_id" class="form-control" required>
                        <option value="">-- Pilih Teknisi --</option>

                        @foreach($staffList as $s)
                            <option value="{{ $s->id }}"
                                {{ $ticket->teknisi_id == $s->id ? 'selected' : '' }}>
                                {{ $s->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-primary mt-2">
                    <i class="fas fa-user-cog mr-1"></i> Assign Teknisi
                </button>
            </form>

        </div>
    </div>

</div>

@endsection
