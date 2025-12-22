@extends('layouts.app')

@section('title', 'Riwayat Serah Terima Aset')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">
        <i class="fas fa-history mr-2"></i> {{ $title }}
    </h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Daftar BAST</h6>
            <a href="{{ route('barangkeluar.create') }}" class="btn btn-primary btn-sm shadow-sm">
                <i class="fas fa-plus mr-2"></i> Buat Serah Terima Baru
            </a>
        </div>

        <div class="card-body">
            {{-- Alert Sukses --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="5%">No</th>
                            <th>Tanggal</th>
                            <th>Aset</th>
                            <th>Penerima</th> 
                            <th>Petugas IT</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($log->tanggal_serah_terima)->format('d/m/Y') }}</td>
                            
                            {{-- KOLOM ASET --}}
                            <td>
                                <strong>{{ $log->aset->kode_asset ?? '-' }}</strong><br>
                                <small class="text-muted">{{ $log->aset->masterBarang->nama_barang ?? '-' }}</small>
                            </td>

                            {{-- KOLOM PENERIMA --}}
                            <td>
                                <strong>{{ $log->pemegang->nama ?? '-' }}</strong>
                                <br>
                                <small class="text-muted" style="font-size: 0.85em;">
                                    <i class="fas fa-briefcase mr-1"></i> {{ $log->pemegang->jabatan ?? 'Staff' }}
                                    <br>
                                    <i class="fas fa-building mr-1"></i> {{ $log->pemegang->perusahaan ?? 'PT. SILO' }}
                                </small>
                            </td>

                            <td>{{ $log->admin->nama ?? '-' }}</td>
                            
                            {{-- KOLOM STATUS --}}
                            <td class="text-center align-middle">
                                @if($log->status == 'selesai')
                                    <span class="badge badge-success px-2 py-1">
                                        <i class="fas fa-check-circle"></i> Selesai
                                    </span>
                                @elseif($log->status == 'menunggu_ttd_user')
                                    <span class="badge badge-warning px-2 py-1 text-dark">
                                        <i class="fas fa-clock"></i> Menunggu User
                                    </span>
                                @elseif($log->status == 'menunggu_ttd_admin')
                                    <span class="badge badge-info px-2 py-1">
                                        <i class="fas fa-pen-alt"></i> Menunggu Admin
                                    </span>
                                @else
                                    <span class="badge badge-secondary">{{ $log->status }}</span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI --}}
                            <td class="text-center align-middle">
                                <div class="btn-group" role="group">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('barangkeluar.show', $log->id) }}" class="btn btn-sm btn-info shadow-sm" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- Tombol Cetak PDF (Memicu Modal) --}}
                                    @php
                                        // Bersihkan _3, _2 dari kode aset untuk default value di inputan
                                        $cleanCode = preg_replace('/_\d+$/', '', $log->aset->kode_asset ?? 'BAST');
                                        if(!str_starts_with($cleanCode, 'BAST')) $cleanCode = 'BAST-' . $cleanCode;
                                    @endphp
                                    
                                    <button type="button" 
                                            class="btn btn-sm btn-danger shadow-sm ml-1 btn-cetak-modal" 
                                            data-toggle="modal" 
                                            data-target="#modalCetak"
                                            data-default-name="{{ $cleanCode }}"
                                            {{-- PERBAIKAN: Kita taruh URL route asli Laravel di sini --}}
                                            data-url="{{ route('barangkeluar.cetak', $log->id) }}"
                                            title="Cetak BAST (PDF)">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3"></i><br>
                                Belum ada riwayat serah terima aset.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCetak" tabindex="-1" role="dialog" aria-labelledby="modalCetakLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalCetakLabel"><i class="fas fa-print mr-2"></i> Cetak Dokumen BAST</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formCetak" onsubmit="return false;">
                    <div class="form-group">
                        <label for="customFilename" class="font-weight-bold">Judul File PDF:</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="customFilename" placeholder="Contoh: BAST-Laptop-Andi">
                            <div class="input-group-append">
                                <span class="input-group-text">.pdf</span>
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Sesuaikan nama file agar mudah dicari nanti.
                        </small>
                    </div>
                    
                    {{-- Input Hidden untuk menyimpan URL Route yang benar --}}
                    <input type="hidden" id="urlCetakAsli"> 
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="prosesCetak()">
                    <i class="fas fa-file-pdf mr-1"></i> Cetak Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- Script DataTables --}}
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [[ 0, "desc" ]],
            "language": {
                "emptyTable": "Tidak ada data tersedia",
                "search": "Cari Data:",
                "lengthMenu": "Tampilkan _MENU_ baris",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Lanjut",
                    "previous": "Mundur"
                }
            }
        });
    });

    // ==========================================
    // SCRIPT MODAL CETAK (FIXED 404)
    // ==========================================
    
    // 1. Saat tombol PDF di tabel diklik
    $(document).on('click', '.btn-cetak-modal', function () {
        var defaultName = $(this).data('default-name'); // Ambil Nama Default
        var urlRoute = $(this).data('url');             // Ambil URL Asli dari Laravel

        $('#customFilename').val(defaultName);  // Isi textbox nama
        $('#urlCetakAsli').val(urlRoute);       // Simpan URL ke hidden input
    });

    // 2. Saat tombol "Cetak Sekarang" diklik
    function prosesCetak() {
        // Ambil URL yang sudah benar tadi dari hidden input
        var url = $('#urlCetakAsli').val(); 
        var filename = $('#customFilename').val();
        
        // Cek jika URL kosong (jaga-jaga error)
        if(!url) {
            alert("Terjadi kesalahan URL. Silakan refresh halaman.");
            return;
        }

        // Tambahkan parameter custom_filename ke URL tersebut
        // Menggunakan tanda tanya (?) atau ampersand (&) tergantung apakah sudah ada query param
        if (url.indexOf('?') > -1) {
            url += "&custom_filename=" + encodeURIComponent(filename);
        } else {
            url += "?custom_filename=" + encodeURIComponent(filename);
        }

        // Buka di tab baru
        window.open(url, '_blank');

        // Tutup modal
        $('#modalCetak').modal('hide');
    }

    // Tambahan: Agar bisa tekan Enter langsung cetak
    $('#customFilename').keypress(function (e) {
        if (e.which == 13) {
            prosesCetak();
            return false;
        }
    });
</script>
@endpush