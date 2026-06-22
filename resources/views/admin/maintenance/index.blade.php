@extends('layouts.app') {{-- Sesuaikan dengan nama master layouting Anda, misal: layouts.admin / layouts.main --}}

{{-- TAHAP AMAN: Set default title khusus untuk halaman ini jika dari controller kosong --}}
@php
    $title = $title ?? 'Maintenance & Perawatan Aset';
@endphp

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Maintenance & Perawatan Aset</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Maintenance</li>
    </ol>

    {{-- Alert Success / Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> Mohon periksa kembali inputan Anda.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" id="maintenanceTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="tickets-tab" data-toggle="tab" href="#tickets" role="tab" aria-controls="tickets" aria-selected="true">
                        <i class="fas fa-tools mr-1"></i> Tiket Perawatan Kerja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="schedules-tab" data-toggle="tab" href="#schedules" role="tab" aria-controls="schedules" aria-selected="false">
                        <i class="fas fa-calendar-alt mr-1"></i> Master Jadwal Rutin
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <div class="tab-content" id="maintenanceTabContent">
                
                {{-- TAB TIKET PERAWATAN --}}
                <div class="tab-pane fade show active" id="tickets" role="tabpanel" aria-labelledby="tickets-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTableTickets" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Informasi Aset</th>
                                    <th>Tanggal Jadwal</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Teknisi</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tickets as $ticket)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $ticket->barangMasuk->masterBarang->nama_barang ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">Kode: {{ $ticket->barangMasuk->kode_asset ?? '-' }}</small><br>
                                            <small class="text-muted">S/N: {{ $ticket->barangMasuk->serial_number ?? '-' }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($ticket->tanggal_jadwal)->translatedFormat('d F Y') }}</td>
                                        <td>
                                            {{ $ticket->tanggal_selesai ? \Carbon\Carbon::parse($ticket->tanggal_selesai)->translatedFormat('d F Y') : '-' }}
                                        </td>
                                        <td>{{ $ticket->teknisi->nama ?? '-' }}</td>
                                        <td>
                                            @if($ticket->status == 'Menunggu')
                                                <span class="badge badge-warning text-dark px-2 py-1">Menunggu</span>
                                            @elseif($ticket->status == 'Progres')
                                                <span class="badge badge-info px-2 py-1">Progres</span>
                                            @else
                                                <span class="badge badge-success px-2 py-1">Selesai</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->status != 'Selesai')
                                                <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#modalSelesaiTiket-{{ $ticket->id }}">
                                                    <i class="fas fa-check"></i> Selesaikan
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-secondary" data-toggle="modal" data-target="#modalDetailTiket-{{ $ticket->id }}">
                                                    <i class="fas fa-eye"></i> Catatan
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- MODAL SELESAIKAN TIKET --}}
                                    <div class="modal fade" id="modalSelesaiTiket-{{ $ticket->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-success text-white">
                                                    <h5 class="modal-title">Selesaikan Perawatan Aset</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.maintenance.ticket.selesai', $ticket->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <p>Tuliskan tindakan perawatan yang telah dilakukan pada barang <strong>{{ $ticket->barangMasuk->masterBarang->nama_barang ?? 'Aset' }}</strong>:</p>
                                                        <div class="form-group">
                                                            <label for="catatan_perawatan">Catatan Perawatan / Hasil Servis <span class="text-danger">*</span></label>
                                                            <textarea name="catatan_perawatan" class="form-control" rows="4" placeholder="Contoh: Pembersihan debu kipas, ganti thermal paste processor, dan optimasi OS." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-success">Simpan & Tutup Tiket</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- MODAL DETAIL CATATAN TIKET --}}
                                    <div class="modal fade" id="modalDetailTiket-{{ $ticket->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title">Riwayat Hasil Perawatan</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <label class="font-weight-bold">Catatan dari Teknisi:</label>
                                                    <div class="p-3 bg-light border rounded">
                                                        {{ $ticket->catatan_perawatan ?? 'Tidak ada catatan.' }}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada tiket perawatan pengerjaan saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB MASTER JADWAL RUTIN --}}
                <div class="tab-pane fade" id="schedules" role="tabpanel" aria-labelledby="schedules-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-secondary m-0">Aturan Penjadwalan Otomatis</h5>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahJadwal">
                            <i class="fas fa-plus-circle mr-1"></i> Buat Jadwal Rutin Baru
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover" id="dataTableSchedules" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Barang / Aset</th>
                                    <th>Frekuensi</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Pemicu Berikutnya (Next Due)</th>
                                    <th>Deskripsi Tugas</th>
                                    <th>Status Aturan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $schedule->barangMasuk->masterBarang->nama_barang ?? 'N/A' }}</strong><br>
                                            <small class="text-muted">Kode: {{ $schedule->barangMasuk->kode_asset ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary text-capitalize px-2 py-1">{{ $schedule->frekuensi }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->tanggal_mulai)->translatedFormat('d F Y') }}</td>
                                        <td>
                                            <span class="text-danger font-weight-bold">
                                                {{ \Carbon\Carbon::parse($schedule->tanggal_next_due)->translatedFormat('d F Y') }}
                                            </span>
                                        </td>
                                        <td>{{ Str::limit($schedule->deskripsi_tugas, 50) }}</td>
                                        <td>
                                            @if($schedule->status == 'aktif')
                                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> Aktif</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1">Non-Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada aturan jadwal rutin yang dibuat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH JADWAL --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTambahJadwalLabel"><i class="fas fa-calendar-plus mr-2"></i>Buat Aturan Jadwal Perawatan Rutin</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.maintenance.schedule.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="barang_masuk_id">Pilih Barang / Aset Fisik <span class="text-danger">*</span></label>
                            <select name="barang_masuk_id" class="form-control select2" style="width: 100%;" required>
                                <option value="">-- Ketik / Scan Kode Aset --</option>
                                @foreach($barangs as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->kode_asset ?? 'KODE-KOSONG' }} | {{ $b->masterBarang->nama_barang ?? 'Aset' }} (SN: {{ $b->serial_number ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block"><i class="fas fa-barcode mr-1"></i> Ketik kode aset seperti <b>AST/IT/...</b> untuk hasil instan.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="frekuensi">Frekuensi Perawatan <span class="text-danger">*</span></label>
                            <select name="frekuensi" class="form-control" required>
                                <option value="">-- Pilih Periode Rutin --</option>
                                <option value="mingguan">Mingguan (Setiap 7 Hari)</option>
                                <option value="bulanan">Bulanan (Setiap 1 Bulan)</option>
                                <option value="tahunan">Tahunan (Setiap 1 Tahun)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="tanggal_mulai">Tanggal Mulai Berlaku <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi_tugas">Deskripsi Tugas Teknisi / Standar Operasional Perawatan <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_tugas" class="form-control" rows="3" placeholder="Sebutkan langkah wajib pengerjaan. Misal: Cek kondisi tinta, bersihkan head printer, test print page." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i> Simpan Sistem Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

{{-- TAMBAHAN: Script Initialization --}}
@push('scripts')
<script>
    $(document).ready(function() {
        // Inisialisasi DataTables untuk tabel Tiket dan Jadwal
        if ($.fn.DataTable) {
            $('#dataTableTickets').DataTable();
            $('#dataTableSchedules').DataTable();
        }

        // Inisialisasi Select2
        if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap4', // (Opsional) Sesuaikan jika Anda menggunakan theme bootstrap4
                dropdownParent: $('#modalTambahJadwal'), // PENTING: Agar Select2 bisa diklik saat berada di dalam Modal Bootstrap
                placeholder: "-- Ketik / Scan Kode Aset --",
                allowClear: true
            });
        }
    });
</script>
@endpush