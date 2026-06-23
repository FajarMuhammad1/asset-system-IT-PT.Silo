@php
    // UPDATE: Mendefinisikan $title langsung di Blade agar otomatis masuk ke header.blade.php
    $title = '- Area Staff / Teknisi';
@endphp

@extends('layouts.app') {{-- Ganti dengan nama master layout khusus staff/teknisi Anda --}}

@section('content')
<div class="container-fluid px-3 py-4">

    <div class="mb-4">
        {{-- UPDATE: Penyesuaian judul halaman agar lebih spesifik untuk Staff --}}
        <h1 class="h4 mb-1 text-gray-800 font-weight-bold">
            <i class="fas fa-hard-hat mr-2 text-primary"></i> Area Staff: Tugas Perawatan
        </h1>
        <p class="text-muted small">Silakan cek fisik aset di lapangan dan centang prosedur yang sudah selesai dikerjakan hari ini.</p>
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
        @forelse($tugasPerawatan as $tugas)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-0 shadow-sm rounded-lg" style="border-left: 5px solid #f6c23e !important;">
                    <div class="card-body">
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge badge-warning text-dark px-2 py-1 small font-weight-bold">
                                <i class="fas fa-clock mr-1"></i> Menunggu
                            </span>
                            <small class="text-muted font-weight-bold">
                                <i class="fas fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($tugas->tanggal_jadwal)->translatedFormat('d M Y') }}
                            </small>
                        </div>
                        
                        <h5 class="font-weight-bold text-dark mb-1">{{ $tugas->barangMasuk->masterBarang->nama_barang ?? 'Nama Barang Tidak Ditemukan' }}</h5>
                        <p class="text-secondary small mb-3">
                            <i class="fas fa-barcode mr-1"></i> Kode Aset: <span class="badge badge-light border text-dark">{{ $tugas->barangMasuk->kode_asset ?? '-' }}</span>
                        </p>
                        
                        <hr class="my-3">

                        <form action="{{ route('staff.maintenance.tugas.selesai', $tugas->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <label class="text-xs font-weight-bold text-uppercase text-gray-600 d-block mb-2">Prosedur Perawatan (SOP):</label>
                            
                            <div class="bg-light p-3 rounded-lg mb-3 border">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="chk1_{{ $tugas->id }}" name="checklist[]" value="Pembersihan Fisik & Debu">
                                    <label class="custom-control-label text-sm w-100" style="cursor:pointer; font-weight: 500;" for="chk1_{{ $tugas->id }}">Pembersihan Fisik & Debu</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="chk2_{{ $tugas->id }}" name="checklist[]" value="Cek Kelistrikan & Kabel">
                                    <label class="custom-control-label text-sm w-100" style="cursor:pointer; font-weight: 500;" for="chk2_{{ $tugas->id }}">Cek Kelistrikan & Kabel</label>
                                </div>
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="chk3_{{ $tugas->id }}" name="checklist[]" value="Cek Fungsionalitas Normal">
                                    <label class="custom-control-label text-sm w-100" style="cursor:pointer; font-weight: 500;" for="chk3_{{ $tugas->id }}">Cek Fungsionalitas Normal</label>
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="chk4_{{ $tugas->id }}" name="checklist[]" value="Update Software / OS">
                                    <label class="custom-control-label text-sm w-100" style="cursor:pointer; font-weight: 500;" for="chk4_{{ $tugas->id }}">Update Software / OS</label>
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

        @empty
            <div class="col-12 text-center my-5">
                <div class="p-5 bg-white rounded-lg shadow-sm mx-auto" style="max-width: 500px;">
                    <i class="fas fa-mug-hot fa-4x mb-3 text-success"></i>
                    <h5 class="font-weight-bold text-dark">Santai Dulu, Kerjaanmu Beres!</h5>
                    <p class="text-muted small mb-0">Semua aset dalam kondisi aman dan tidak ada jadwal maintenance yang menunggu hari ini.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection