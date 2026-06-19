@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Detail Tiket: {{ $ticket->no_tiket }}</h1>
        
        <a href="{{ route('admin.helpdesk.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">

            <h4 class="font-weight-bold">{{ $ticket->judul_masalah }}</h4>
            <p class="text-muted">{{ $ticket->deskripsi }}</p>

            @if($ticket->foto_masalah)
                <div class="mb-3">
                    <img src="{{ asset('storage/' . $ticket->foto_masalah) }}"
                         class="img-thumbnail rounded shadow-sm"
                         style="max-height: 250px; width: auto;">
                </div>
            @endif

            <table class="table table-bordered mb-4">
                <tr>
                    <th width="200" class="bg-light">Pelapor</th>
                    <td>{{ $ticket->pelapor->nama }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Jabatan</th>
                    <td>{{ $ticket->pelapor->jabatan ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Departemen</th>
                    <td>{{ $ticket->pelapor->departemen ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Perusahaan</th>
                    <td>{{ $ticket->pelapor->perusahaan ?? '-' }}</td>
                </tr>
                <tr>
                    <th class="bg-light">Prioritas</th>
                    <td>
                        @php
                            $badgePrioritas = [
                                'Low'    => 'secondary', 
                                'Normal' => 'info', 
                                'High'   => 'warning', 
                                'Urgent' => 'danger'
                            ];
                            $colorPrio = $badgePrioritas[$ticket->prioritas] ?? 'info';
                        @endphp
                        <span class="badge badge-{{ $colorPrio }} px-2 py-1">{{ $ticket->prioritas ?? 'Normal' }}</span>
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Tipe Pengerjaan</th>
                    <td>
                        @if(isset($ticket->tipe_penyelesaian) && $ticket->tipe_penyelesaian == 'Tim')
                            <span class="badge badge-primary px-2 py-1"><i class="fas fa-users mr-1"></i> Tim</span>
                        @else
                            <span class="badge badge-info px-2 py-1"><i class="fas fa-user mr-1"></i> Individu</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Status</th>
                    <td>
                        @if($ticket->status == 'Open')
                            <span class="badge badge-warning text-dark">{{ $ticket->status }}</span>
                        @elseif($ticket->status == 'Progres')
                            <span class="badge badge-info">{{ $ticket->status }}</span>
                        @elseif($ticket->status == 'Closed')
                            <span class="badge badge-success">{{ $ticket->status }}</span>
                        @elseif($ticket->status == 'Reject' || $ticket->status == 'Ditolak')
                            <span class="badge badge-danger">{{ $ticket->status }}</span>
                        @else
                            <span class="badge badge-secondary">{{ $ticket->status }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="bg-light">Teknisi (PIC)</th>
                    <td>
                        @if($ticket->teknisi)
                            <span class="badge badge-success">{{ $ticket->teknisi->nama }}</span>
                        @else
                            <span class="badge badge-secondary">Belum di-assign</span>
                        @endif
                    </td>
                </tr>
            </table>

            <hr class="my-4">

            <div class="row">
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-left-primary shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-cog mr-1"></i> Assign Teknisi (PIC)</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.helpdesk.assign', $ticket->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label><strong>Pilih Teknisi / PIC Utama</strong></label>
                                    <select name="teknisi_id" class="form-control" required>
                                        <option value="">-- Pilih Teknisi --</option>
                                        @foreach($staffList as $s)
                                            <option value="{{ $s->id }}" {{ $ticket->teknisi_id == $s->id ? 'selected' : '' }}>
                                                {{ $s->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted mt-1 d-block">*Teknisi ini akan menjadi penanggung jawab tiket.</small>
                                </div>
                                <button class="btn btn-primary mt-2">
                                    <i class="fas fa-check-circle mr-1"></i> Simpan Teknisi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-left-warning shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-sliders-h mr-1"></i> Pengaturan Tambahan</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.helpdesk.settings', $ticket->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="prioritas"><strong>Tingkat Prioritas</strong></label>
                                            <select name="prioritas" id="prioritas" class="form-control" required>
                                                <option value="Low" {{ $ticket->prioritas == 'Low' ? 'selected' : '' }}>Low (Rendah)</option>
                                                <option value="Normal" {{ ($ticket->prioritas == 'Normal' || empty($ticket->prioritas)) ? 'selected' : '' }}>Normal (Biasa)</option>
                                                <option value="High" {{ $ticket->prioritas == 'High' ? 'selected' : '' }}>High (Tinggi)</option>
                                                <option value="Urgent" {{ $ticket->prioritas == 'Urgent' ? 'selected' : '' }}>Urgent (Mendesak)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="tipe_penyelesaian"><strong>Tipe Pengerjaan</strong></label>
                                            <select name="tipe_penyelesaian" id="tipe_penyelesaian" class="form-control" required>
                                                <option value="Individu" {{ (!isset($ticket->tipe_penyelesaian) || $ticket->tipe_penyelesaian == 'Individu') ? 'selected' : '' }}>Individu</option>
                                                <option value="Tim" {{ (isset($ticket->tipe_penyelesaian) && $ticket->tipe_penyelesaian == 'Tim') ? 'selected' : '' }}>Tim (Kolaborasi)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning mt-2 font-weight-bold text-dark">
                                    <i class="fas fa-save mr-1"></i> Simpan Pengaturan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div> <hr>

            <div class="text-center mt-4">
                <a href="{{ route('admin.helpdesk.index') }}" class="btn btn-secondary px-4">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Tiket
                </a>
            </div>

        </div>
    </div>

</div>

@endsection