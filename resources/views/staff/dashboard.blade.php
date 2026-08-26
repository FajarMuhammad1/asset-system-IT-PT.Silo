@extends('layouts.app')

@section('title', $title ?? 'Dashboard Teknisi')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
        <i class="fas fa-desktop mr-2 text-primary"></i> {{ $title ?? 'Dashboard' }}
    </h1>

    <a href="{{ route('staff.helpdesk.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm rounded-pill px-3">
        <i class="fas fa-list fa-sm text-white-50 mr-1"></i> Lihat Semua Tugas
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
        <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-0 border-left-warning shadow-sm h-100 py-2 rounded-lg bg-white">
            <div class="card-body position-relative">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            <a href="{{ route('staff.helpdesk.index') }}" class="stretched-link text-warning text-decoration-none">Tugas Baru (Pending)</a>
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $tugasPending ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-warning-light p-2 rounded-circle">
                            <i class="fas fa-inbox fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-0 border-left-info shadow-sm h-100 py-2 rounded-lg bg-white">
            <div class="card-body position-relative">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            <a href="{{ route('staff.helpdesk.index') }}" class="stretched-link text-info text-decoration-none">Tugas Sedang Dikerjakan</a>
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $tugasProses ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-info-light p-2 rounded-circle">
                            <i class="fas fa-tools fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-0 border-left-success shadow-sm h-100 py-2 rounded-lg bg-white">
            <div class="card-body position-relative">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <a href="{{ route('staff.helpdesk.index') }}" class="stretched-link text-success text-decoration-none">Tugas Selesai</a>
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $tugasSelesai ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-success-light p-2 rounded-circle">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- BAGIAN 1: DAFTAR PENUGASAN HELPDESK (TABEL RINCI) -->
<div class="card shadow-sm border-0 mb-4 rounded-lg bg-white">
    <div class="card-header py-3 bg-white d-flex align-items-center justify-content-between border-bottom">
        <div class="d-flex align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-headset mr-2"></i> Antrean & Penugasan Helpdesk
            </h6>
            <span class="badge badge-primary ml-2 rounded-pill px-2">Tiket Insidental</span>
        </div>
        <a href="{{ route('staff.helpdesk.index') }}" class="btn btn-sm btn-outline-primary shadow-sm rounded-pill px-3">
            <i class="fas fa-list mr-1"></i> Semua Tiket
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="bg-light text-dark text-uppercase text-xs">
                    <tr>
                        <th class="py-3 px-3">No. Tiket</th>
                        <th class="py-3">Pelapor</th>
                        <th class="py-3">Perangkat / Aset</th>
                        <th class="py-3">Keluhan & Masalah</th>
                        <th class="py-3 text-center">Prioritas</th>
                        <th class="py-3 text-center">Penugasan</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3">Waktu Masuk</th>
                        <th class="py-3 text-center" width="130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTugas as $t)
                        <tr>
                            {{-- No Tiket --}}
                            <td class="px-3 font-weight-bold text-primary text-nowrap">
                                <i class="fas fa-ticket-alt mr-1 text-primary"></i> {{ $t->no_tiket }}
                            </td>

                            {{-- Pelapor --}}
                            <td>
                                <div class="font-weight-bold text-dark">{{ $t->pelapor->nama ?? 'Tidak Diketahui' }}</div>
                                <small class="text-muted"><i class="fas fa-briefcase mr-1"></i>{{ $t->pelapor->jabatan ?? 'Pengguna' }}</small>
                            </td>

                            {{-- Perangkat / Aset --}}
                            <td>
                                @if($t->barangMasuk)
                                    <div class="font-weight-bold text-dark">{{ $t->barangMasuk->masterBarang->nama_barang ?? 'Aset' }}</div>
                                    <small class="badge badge-light border text-muted">
                                        <i class="fas fa-barcode mr-1"></i>{{ $t->barangMasuk->kode_asset ?? '-' }}
                                    </small>
                                @else
                                    <span class="text-muted small"><em>Non-Aset / Perangkat Umum</em></span>
                                @endif
                            </td>

                            {{-- Judul & Deskripsi --}}
                            <td>
                                <div class="font-weight-bold text-dark">{{ $t->judul_masalah }}</div>
                                <small class="text-secondary d-block" style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $t->deskripsi ?? '-' }}
                                </small>
                            </td>

                            {{-- Prioritas --}}
                            <td class="text-center">
                                @php
                                    $colorPrio = ['Low' => 'secondary', 'Normal' => 'info', 'High' => 'warning', 'Urgent' => 'danger'][$t->prioritas] ?? 'info';
                                @endphp
                                <span class="badge badge-{{ $colorPrio }} px-2 py-1 font-weight-bold">{{ $t->prioritas ?? 'Normal' }}</span>
                            </td>

                            {{-- Tipe Penugasan --}}
                            <td class="text-center">
                                @if($t->tipe_penyelesaian == 'Tim')
                                    <span class="badge badge-primary px-2 py-1"><i class="fas fa-users mr-1"></i> Tim</span>
                                    @if($t->teknisi_id == Auth::id())
                                        <div class="text-xs text-success font-weight-bold mt-1"><i class="fas fa-star text-warning"></i> PIC Utama</div>
                                    @endif
                                @else
                                    <span class="badge badge-secondary px-2 py-1"><i class="fas fa-user mr-1"></i> Individu</span>
                                @endif
                            </td>

                            {{-- Status Tiket --}}
                            <td class="text-center">
                                @if($t->status == 'Open')
                                    <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-envelope-open mr-1"></i> Open</span>
                                @elseif($t->status == 'Progres')
                                    <span class="badge badge-info px-2 py-1"><i class="fas fa-spinner fa-spin mr-1"></i> Progres</span>
                                @elseif($t->status == 'Closed')
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Closed</span>
                                @elseif($t->status == 'Ditolak' || $t->status == 'Reject')
                                    <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">{{ $t->status }}</span>
                                @endif
                            </td>

                            {{-- Waktu Masuk --}}
                            <td class="text-nowrap small text-muted">
                                <div><i class="far fa-calendar-alt mr-1"></i>{{ $t->created_at->format('d M Y') }}</div>
                                <div><i class="far fa-clock mr-1"></i>{{ $t->created_at->format('H:i') }} WIB</div>
                            </td>

                            {{-- Aksi --}}
                            <td class="text-center align-middle">
                                @if($t->teknisi_id == Auth::id() && $t->status == 'Open')
                                    <a href="{{ route('staff.helpdesk.show', $t->id) }}" class="btn btn-sm btn-primary shadow-sm font-weight-bold" title="Mulai & Buka Detail">
                                        <i class="fas fa-play mr-1"></i> Mulai
                                    </a>
                                @else
                                    <a href="{{ route('staff.helpdesk.show', $t->id) }}" class="btn btn-sm btn-info shadow-sm font-weight-bold" title="Lihat Detail Lengkap">
                                        <i class="fas fa-info-circle mr-1"></i> Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                Tidak ada penugasan tiket helpdesk aktif saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<hr class="my-4 border-gray-300">

<!-- BAGIAN 2: JADWAL PERAWATAN RUTIN ASET (FORMAT CARD DENGAN SOP & CHECKLIST) -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center">
        <h5 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-tools mr-2 text-warning"></i> Jadwal Perawatan Aset Hari Ini</h5>
        <span class="badge badge-warning text-dark ml-2 rounded-pill px-2">Maintenance Terjadwal</span>
    </div>
    <a href="{{ route('staff.maintenance.index') }}" class="btn btn-sm btn-outline-warning text-dark font-weight-bold rounded-pill px-3 shadow-sm">
        <i class="fas fa-calendar-alt mr-1"></i> Semua Jadwal
    </a>
</div>

<div class="row">
    @php $adaTugasMaintenance = false; @endphp
    
    @if(isset($tugasPerawatan) && count($tugasPerawatan) > 0)
        @foreach($tugasPerawatan as $ticket)
            @php $adaTugasMaintenance = true; @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-lg h-100 bg-white" style="border-left: 5px solid #f6c23e !important;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge badge-warning text-dark px-2 py-1 small font-weight-bold">
                                    <i class="fas fa-clock mr-1"></i> {{ $ticket->status ?? 'Menunggu' }}
                                </span>
                                <small class="text-muted font-weight-bold">
                                    <i class="fas fa-calendar-alt mr-1"></i> {{ $ticket->tanggal_jadwal ? \Carbon\Carbon::parse($ticket->tanggal_jadwal)->translatedFormat('d M Y') : '-' }}
                                </small>
                            </div>
                            
                            <h5 class="font-weight-bold text-dark mb-1">{{ $ticket->barangMasuk->masterBarang->nama_barang ?? 'Aset Tidak Diketahui' }}</h5>
                            <p class="text-secondary small mb-3">
                                <i class="fas fa-barcode mr-1"></i> Kode Aset: <span class="badge badge-light border text-dark">{{ $ticket->barangMasuk->kode_asset ?? '-' }}</span>
                            </p>
                            
                            <hr class="my-3 border-gray-200">

                            <form action="{{ route('staff.maintenance.tugas.selesai', $ticket->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <label class="text-xs font-weight-bold text-uppercase text-gray-600 d-block mb-2">Prosedur Perawatan (SOP):</label>
                                
                                <div class="bg-light p-3 rounded-lg mb-3 border">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="m_chk1_{{ $ticket->id }}" name="checklist[]" value="Pembersihan Fisik & Debu">
                                        <label class="custom-control-label text-sm w-100" style="cursor:pointer;" for="m_chk1_{{ $ticket->id }}">Pembersihan Fisik & Debu</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="m_chk2_{{ $ticket->id }}" name="checklist[]" value="Cek Kelistrikan & Kabel">
                                        <label class="custom-control-label text-sm w-100" style="cursor:pointer;" for="m_chk2_{{ $ticket->id }}">Cek Kelistrikan & Kabel</label>
                                    </div>
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="m_chk3_{{ $ticket->id }}" name="checklist[]" value="Cek Fungsionalitas Normal">
                                        <label class="custom-control-label text-sm w-100" style="cursor:pointer;" for="m_chk3_{{ $ticket->id }}">Cek Fungsionalitas Normal</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="m_chk4_{{ $ticket->id }}" name="checklist[]" value="Update Software / OS">
                                        <label class="custom-control-label text-sm w-100" style="cursor:pointer;" for="m_chk4_{{ $ticket->id }}">Update Software / OS</label>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="text-xs font-weight-bold text-uppercase text-gray-600">Catatan Temuan Lapangan:</label>
                                    <textarea name="catatan_perawatan" class="form-control form-control-sm rounded-lg shadow-none" rows="2" placeholder="Kondisi oke, kipas agak berisik..."></textarea>
                                </div>
                        </div>

                        <div class="mt-2">
                                <button type="submit" class="btn btn-warning text-dark btn-block font-weight-bold shadow-sm rounded-lg py-2" onclick="return confirm('Tandai perawatan aset ini selesai?');">
                                    <i class="fas fa-paper-plane mr-1"></i> Selesaikan Perawatan
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    @endif

    @if(!$adaTugasMaintenance)
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg text-center py-4 bg-white mb-4">
                <div class="card-body">
                    <i class="fas fa-calendar-check fa-2x text-warning mb-2"></i>
                    <h6 class="font-weight-bold text-dark">Jadwal Perawatan Kosong</h6>
                    <p class="text-muted small mb-0">Tidak ada jadwal perawatan aset rutin yang menunggu pengerjaan.</p>
                </div>
            </div>
        </div>
    @endif
</div>

@endsection

@push('styles')
<style>
    .bg-warning-light { background-color: rgba(246, 194, 62, 0.15); }
    .bg-info-light { background-color: rgba(54, 185, 204, 0.15); }
    .bg-success-light { background-color: rgba(28, 200, 138, 0.15); }
    .rounded-lg { border-radius: 0.5rem !important; }
</style>
@endpush