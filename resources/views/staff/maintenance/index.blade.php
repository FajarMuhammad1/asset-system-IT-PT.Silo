@extends('layouts.app') {{-- Ganti dengan nama master layout khusus staff/teknisi Anda --}}

@section('content')
<div class="container-fluid px-3 py-4">

    <div class="mb-4">
        <h1 class="h4 mb-1 text-gray-800 font-weight-bold"><i class="fas fa-tools mr-2 text-primary"></i> Tugas Perawatan Hari Ini</h1>
        <p class="text-muted small">Silakan cek fisik aset di lapangan dan centang prosedur yang sudah selesai dikerjakan.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        @php $adaTugas = false; @endphp
        
        @foreach($tickets as $ticket)
            @php $adaTugas = true; @endphp
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-lg" style="border-left: 5px solid #f6c23e !important;">
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge badge-warning text-dark px-2 py-1 small font-weight-bold">
                                <i class="fas fa-clock mr-1"></i> Menunggu
                            </span>
                            <small class="text-muted font-weight-bold">
                                <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($ticket->tanggal_jadwal)->translatedFormat('d M Y') }}
                            </small>
                        </div>
                        
                        <h5 class="font-weight-bold text-dark mb-1">{{ $ticket->barangMasuk->masterBarang->nama_barang ?? 'Nama Barang Tidak Ditemukan' }}</h5>
                        <p class="text-secondary small mb-3">
                            <i class="fas fa-barcode mr-1"></i> Kode Aset: <span class="badge badge-light border text-dark">{{ $ticket->barangMasuk->kode_asset ?? '-' }}</span>
                        </p>
                        
                        <hr class="my-3">

                        {{-- UPDATE: Route admin diganti menjadi staff --}}
                        <form action="{{ route('staff.maintenance.ticket.selesai', $ticket->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <label class="text-xs font-weight-bold text-uppercase text-gray-600 d-block mb-2">Prosedur Perawatan (SOP):</label>
                            
                            <div class="bg-light p-3 rounded-lg mb-3 border">
                                <div class="custom-control custom-checkbox mb-2.5">
                                    <input type="checkbox" class="custom-control-input" id="chk1_{{ $ticket->id }}" name="checklist[]" value="Pembersihan Fisik & Debu">
                                    <label class="custom-control-label text-sm w-100 font-weight-500" style="cursor:pointer;" for="chk1_{{ $ticket->id }}">Pembersihan Fisik & Debu</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2.5">
                                    <input type="checkbox" class="custom-control-input" id="chk2_{{ $ticket->id }}" name="checklist[]" value="Cek Kelistrikan & Kabel">
                                    <label class="custom-control-label text-sm w-100 font-weight-500" style="cursor:pointer;" for="chk2_{{ $ticket->id }}">Cek Kelistrikan & Kabel</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2.5">
                                    <input type="checkbox" class="custom-control-input" id="chk3_{{ $ticket->id }}" name="checklist[]" value="Cek Fungsionalitas Normal">
                                    <label class="custom-control-label text-sm w-100 font-weight-500" style="cursor:pointer;" for="chk3_{{ $ticket->id }}">Cek Fungsionalitas Normal</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chk4_{{ $ticket->id }}" name="checklist[]" value="Update Software / OS">
                                    <label class="custom-control-label text-sm w-100 font-weight-500" style="cursor:pointer;" for="chk4_{{ $ticket->id }}">Update Software / OS</label>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="text-xs font-weight-bold text-uppercase text-gray-600">Catatan Temuan Lapangan (Opsional):</label>
                                <textarea name="catatan_perawatan" class="form-control form-control-sm rounded-lg shadow-none" rows="2" placeholder="Contoh: Kondisi oke, cuma kipas agak berisik..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-success btn-block font-weight-bold shadow-sm rounded-lg py-2" onclick="return confirm('Apakah Anda yakin semua checklist sudah sesuai? Tugas akan ditandai selesai.');">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Laporan Selesai
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach

        @if(!$adaTugas)
            <div class="col-12 text-center my-5">
                <div class="p-5 bg-white rounded-lg shadow-sm max-width-md mx-auto" style="max-width: 500px;">
                    <i class="fas fa-mug-hot fa-4x mb-3 text-success animate__animated animate__bounce"></i>
                    <h5 class="font-weight-bold text-dark">Santai Dulu, Kerjaamu Beres!</h5>
                    <p class="text-muted small mb-0">Semua aset dalam kondisi aman dan tidak ada jadwal maintenance yang menunggu hari ini.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection