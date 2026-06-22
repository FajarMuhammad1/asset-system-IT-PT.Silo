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


<div class="d-flex align-items-center mb-3">
    <h5 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-headset mr-2 text-primary"></i> Antrean Helpdesk Hari Ini</h5>
    <span class="badge badge-primary ml-2 rounded-pill px-2">Insidental</span>
</div>

<div class="row">
    @forelse($recentTugas as $t)
        @if($t->status !== 'Closed' && $t->status !== 'Ditolak')
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm rounded-lg h-100 bg-white" style="border-top: 4px solid {{ $t->status == 'Open' ? '#f6c23e' : '#36b9cc' }} !important;">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-xs font-weight-bold text-muted"><i class="fas fa-hashtag"></i> {{ $t->no_tiket }}</span>
                            <small class="text-secondary font-weight-bold"><i class="far fa-clock"></i> {{ $t->created_at->format('d M Y') }}</small>
                        </div>

                        <div class="mb-2">
                            <span class="badge badge-light border text-dark text-xs mb-1">
                                <i class="fas fa-user mr-1 text-primary"></i> Pelapor: {{ $t->pelapor->nama ?? 'Tidak Diketahui' }}
                            </span>
                            <h5 class="font-weight-bold text-dark mb-2 mt-1">{{ $t->judul_masalah }}</h5>
                        </div>

                        <div class="mb-3">
                            @if($t->status == 'Open')
                                <span class="badge badge-warning text-dark"><i class="fas fa-folder-open mr-1"></i> Tugas Baru (Open)</span>
                            @else
                                <span class="badge badge-info"><i class="fas fa-spinner fa-spin mr-1"></i> Sedang Dikerjakan</span>
                            @endif
                        </div>

                        <hr class="my-3 border-gray-200">

                        <form action="{{ route('staff.helpdesk.update', $t->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <p class="text-xs font-weight-bold text-uppercase text-gray-600 mb-2">Checklist Penyelesaian:</p>
                            <div class="bg-light p-3 rounded border mb-3">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="chk1_{{ $t->id }}" name="checklist[]" value="Pemeriksaan Awal Gejala">
                                    <label class="custom-control-label text-sm w-100 text-dark" style="cursor:pointer;" for="chk1_{{ $t->id }}">Pemeriksaan Awal Gejala</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="chk2_{{ $t->id }}" name="checklist[]" value="Perbaikan Komponen / Sistem">
                                    <label class="custom-control-label text-sm w-100 text-dark" style="cursor:pointer;" for="chk2_{{ $t->id }}">Perbaikan Komponen / Sistem</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chk3_{{ $t->id }}" name="checklist[]" value="Uji Coba Fungsional Akhir">
                                    <label class="custom-control-label text-sm w-100 text-dark" style="cursor:pointer;" for="chk3_{{ $t->id }}">Uji Coba Fungsional Akhir</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold text-uppercase text-gray-600">Catatan Perbaikan:</label>
                                <textarea name="catatan_staff" class="form-control form-control-sm" rows="2" placeholder="Tulis tindakan atau suku cadang yang diganti..." required></textarea>
                            </div>
                    </div> <div class="mt-2">
                            <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm py-2" onclick="return confirm('Kirim laporan penyelesaian untuk tiket ini?');">
                                <i class="fas fa-check-circle mr-1"></i> Selesaikan Helpdesk
                            </button>
                        </form> <a href="{{ route('staff.helpdesk.show', $t->id) }}" class="btn btn-light btn-block btn-sm text-muted font-weight-bold mt-2 border">
                            <i class="fas fa-info-circle mr-1"></i> Detail Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-lg text-center py-4 bg-white mb-4">
                <div class="card-body">
                    <i class="fas fa-mug-hot fa-2x text-success mb-2"></i>
                    <h6 class="font-weight-bold text-dark">Antrean Helpdesk Kosong</h6>
                    <p class="text-muted small mb-0">Tidak ada keluhan baru dari pengguna saat ini.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>

<hr class="my-4 border-gray-300">

<div class="d-flex align-items-center mb-3">
    <h5 class="m-0 font-weight-bold text-gray-800"><i class="fas fa-tools mr-2 text-warning"></i> Jadwal Perawatan Hari Ini</h5>
    <span class="badge badge-warning text-dark ml-2 rounded-pill px-2">Rutin / Terjadwal</span>
</div>

<div class="row">
    @php $adaTugasMaintenance = false; @endphp
    
    @if(isset($tickets) && count($tickets) > 0)
        @foreach($tickets as $ticket)
            @php $adaTugasMaintenance = true; @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-lg h-100 bg-white" style="border-left: 5px solid #f6c23e !important;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge badge-warning text-dark px-2 py-1 small font-weight-bold">
                                    <i class="fas fa-clock mr-1"></i> Menunggu
                                </span>
                                <small class="text-muted font-weight-bold">
                                    <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($ticket->tanggal_jadwal)->translatedFormat('d M Y') }}
                                </small>
                            </div>
                            
                            <h5 class="font-weight-bold text-dark mb-1">{{ $ticket->barangMasuk->masterBarang->nama_barang ?? 'Aset Tidak Diketahui' }}</h5>
                            <p class="text-secondary small mb-3">
                                <i class="fas fa-barcode mr-1"></i> Kode Aset: <span class="badge badge-light border text-dark">{{ $ticket->barangMasuk->kode_asset ?? '-' }}</span>
                            </p>
                            
                            <hr class="my-3 border-gray-200">

                            <form action="{{ route('admin.maintenance.ticket.selesai', $ticket->id) }}" method="POST">
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
                    <p class="text-muted small mb-0">Tidak ada jadwal perawatan aset rutin untuk hari ini.</p>
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