@extends('layouts.app') {{-- Sesuaikan dengan nama master layouting Anda, misal: layouts.admin / layouts.main --}}

{{-- TAHAP AMAN: Set default title khusus untuk halaman ini jika dari controller kosong --}}
@php
    $title = $title ?? 'Maintenance & Perawatan Aset';
@endphp

@section('content')
<div class="container-fluid px-4 py-3">
    
    {{-- HEADER HALAMAN --}}
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h1 class="h3 font-weight-bold text-gray-800 mb-1">Maintenance & Perawatan Aset</h1>
            <ol class="breadcrumb bg-transparent p-0 small">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Maintenance</li>
            </ol>
        </div>
    </div>

    {{-- ALERT NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-left-success" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-left-danger" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> Mohon periksa kembali inputan Anda pada form.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- KARTU UTAMA DENGAN NAVIGASI TAB --}}
    <div class="card shadow-sm border-0 mb-4 rounded-lg">
        <div class="card-header bg-white border-bottom-0 pt-3">
            <ul class="nav nav-tabs card-header-tabs" id="maintenanceTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold" id="perawatan-tab" data-toggle="tab" href="#perawatan" role="tab" aria-controls="perawatan" aria-selected="true">
                        <i class="fas fa-tools mr-1 text-primary"></i> Tugas Perawatan Kerja
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" id="schedules-tab" data-toggle="tab" href="#schedules" role="tab" aria-controls="schedules" aria-selected="false">
                        <i class="fas fa-calendar-alt mr-1 text-success"></i> Master Jadwal Rutin
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <div class="tab-content" id="maintenanceTabContent">
                
                {{-- ========================================== --}}
                {{-- TAB TUGAS PERAWATAN                        --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade show active" id="perawatan" role="tabpanel" aria-labelledby="perawatan-tab">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle" id="dataTablePerawatan" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Informasi Aset</th>
                                    <th>Tanggal Jadwal</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Teknisi / Staff</th>
                                    <th>Status</th>
                                    <th width="12%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tugasPerawatan as $tugas)
                                    <tr>
                                        <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $tugas->barangMasuk->masterBarang->nama_barang ?? 'N/A' }}</strong><br>
                                            <span class="badge badge-light border text-dark mt-1">Kode: {{ $tugas->barangMasuk->kode_asset ?? '-' }}</span><br>
                                            <small class="text-muted d-block mt-1"><i class="fas fa-fingerprint mr-1"></i>S/N: {{ $tugas->barangMasuk->serial_number ?? '-' }}</small>
                                        </td>
                                        <td><span class="font-weight-500">{{ \Carbon\Carbon::parse($tugas->tanggal_jadwal)->translatedFormat('d M Y') }}</span></td>
                                        <td>
                                            {!! $tugas->tanggal_selesai ? '<span class="text-success font-weight-500">'.\Carbon\Carbon::parse($tugas->tanggal_selesai)->translatedFormat('d M Y').'</span>' : '<span class="text-muted">-</span>' !!}
                                        </td>
                                        <td>
                                            <span class="font-weight-bold text-secondary"><i class="fas fa-user-cog mr-1 small"></i>{{ $tugas->teknisi->nama ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($tugas->status == 'Menunggu')
                                                <span class="badge badge-warning text-dark px-2 py-1 font-weight-bold"><i class="fas fa-clock mr-1"></i>Menunggu</span>
                                            @elseif($tugas->status == 'Progres')
                                                <span class="badge badge-info px-2 py-1 font-weight-bold"><i class="fas fa-spinner fa-spin mr-1"></i>Progres</span>
                                            @else
                                                <span class="badge badge-success px-2 py-1 font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Selesai</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($tugas->status != 'Selesai')
                                                <button type="button" class="btn btn-sm btn-success font-weight-bold px-2" data-toggle="modal" data-target="#modalSelesaiPerawatan-{{ $tugas->id }}">
                                                    <i class="fas fa-check mr-1"></i> Selesaikan
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-dark font-weight-bold px-2" data-toggle="modal" data-target="#modalDetailPerawatan-{{ $tugas->id }}">
                                                    <i class="fas fa-eye mr-1"></i> Audit Lapangan
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    {{-- MODAL MANUALLY COMPLETE TASK (BACKUP JIKA ADMIN INGIN INTERVENSI) --}}
                                    <div class="modal fade" id="modalSelesaiPerawatan-{{ $tugas->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-success text-white border-0">
                                                    <h5 class="modal-title font-weight-bold"><i class="fas fa-clipboard-check mr-2"></i>Selesaikan Perawatan</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('admin.maintenance.tugas.selesai', $tugas->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <p class="text-secondary small">Anda melakukan penyelesaian tugas manual sebagai Admin. Tulis tindakan administrasi/perawatan untuk aset berikut:</p>
                                                        <div class="bg-light p-2.5 rounded mb-3 border text-sm">
                                                            <strong>{{ $tugas->barangMasuk->masterBarang->nama_barang ?? 'Aset' }}</strong><br>
                                                            <small class="text-muted">Kode: {{ $tugas->barangMasuk->kode_asset ?? '-' }}</small>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold text-dark text-sm">Catatan Perawatan / Hasil Servis <span class="text-danger">*</span></label>
                                                            <textarea name="catatan_perawatan" class="form-control" rows="4" placeholder="Sebutkan tindakan pencegahan atau perbaikan fisik yang dilakukan..." required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light border-0 py-2">
                                                        <button type="button" class="btn btn-sm btn-secondary font-weight-bold rounded-lg px-3" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-success font-weight-bold rounded-lg px-3">Simpan Laporan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- MODAL AUDIT DETAIL & HASIL CHECKLIST LAPANGAN --}}
                                    <div class="modal fade" id="modalDetailPerawatan-{{ $tugas->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content border-0 shadow">
                                                <div class="modal-header bg-dark text-white border-0">
                                                    <h5 class="modal-title font-weight-bold"><i class="fas fa-file-invoice mr-2"></i>Riwayat & SOP Lapangan</h5>
                                                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{-- TAMPILAN CHECKLIST JIKA ADA DATA DARI TEKNISI --}}
                                                    @if(!empty($tugas->checklist) && (is_array($tugas->checklist) || is_object($tugas->checklist) || is_string($tugas->checklist)))
                                                        <label class="font-weight-bold text-gray-700 mb-2"><i class="fas fa-clipboard-list mr-1 text-info"></i> SOP yang Diselesaikan Teknisi:</label>
                                                        <div class="list-group mb-3 shadow-none text-sm bg-light p-2 border rounded-lg">
                                                            @php
                                                                $items = is_string($tugas->checklist) ? json_decode($tugas->checklist, true) : $tugas->checklist;
                                                            @endphp
                                                            @if(is_array($items))
                                                                @foreach($items as $checkItem)
                                                                    <div class="d-flex align-items-center mb-1.5 text-success font-weight-500">
                                                                        <i class="fas fa-check-square mr-2 text-success"></i> {{ $checkItem }}
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <span class="text-muted text-xs p-2">Format checklist bermasalah.</span>
                                                            @endif
                                                        </div>
                                                    @endif

                                                    <label class="font-weight-bold text-gray-700 mb-1"><i class="fas fa-comment-alt mr-1 text-secondary"></i> Catatan Akhir Temuan Lapangan:</label>
                                                    <div class="p-3 bg-light border rounded text-dark text-sm font-weight-500">
                                                        {{ $tugas->catatan_perawatan ?? 'Tidak ada catatan tertulis.' }}
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light border-0 py-2">
                                                    <button type="button" class="btn btn-sm btn-secondary font-weight-bold px-3 rounded-lg" data-dismiss="modal">Tutup Dokumen</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada daftar tugas perawatan kerja saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- ========================================== --}}
                {{-- TAB MASTER JADWAL RUTIN                    --}}
                {{-- ========================================== --}}
                <div class="tab-pane fade" id="schedules" role="tabpanel" aria-labelledby="schedules-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                        <h5 class="text-secondary font-weight-bold m-0"><i class="fas fa-robot mr-1"></i> Aturan Penjadwalan Otomatis System</h5>
                        <button type="button" class="btn btn-sm btn-primary font-weight-bold px-3 shadow-sm rounded-lg py-2 mt-2 mt-md-0" data-toggle="modal" data-target="#modalTambahJadwal">
                            <i class="fas fa-plus-circle mr-1"></i> Buat Aturan Jadwal Baru
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle" id="dataTableSchedules" width="100%" cellspacing="0">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Barang / Aset Fisik</th>
                                    <th>Frekuensi Periode</th>
                                    <th>Tanggal Mulai</th>
                                    <th>Pemicu Berikutnya (Next Due)</th>
                                    <th>Deskripsi Ruang Lingkup SOP</th>
                                    <th width="10%">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                    <tr>
                                        <td class="text-center font-weight-bold">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong class="text-dark">{{ $schedule->barangMasuk->masterBarang->nama_barang ?? 'N/A' }}</strong><br>
                                            <span class="badge badge-light border text-dark mt-1">Kode: {{ $schedule->barangMasuk->kode_asset ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-secondary text-capitalize px-2 py-1 font-weight-bold">{{ $schedule->frekuensi }}</span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($schedule->tanggal_mulai)->translatedFormat('d M Y') }}</td>
                                        <td>
                                            <span class="text-danger font-weight-bold">
                                                <i class="fas fa-hourglass-half mr-1 small"></i>{{ \Carbon\Carbon::parse($schedule->tanggal_next_due)->translatedFormat('d M Y') }}
                                            </span>
                                        </td>
                                        <td><small class="text-dark font-weight-500">{{ Str::limit($schedule->deskripsi_tugas, 65) }}</small></td>
                                        <td class="text-center">
                                            @if($schedule->status == 'aktif')
                                                <span class="badge badge-success px-2 py-1 font-weight-bold"><i class="fas fa-check mr-1"></i>Aktif</span>
                                            @else
                                                <span class="badge badge-danger px-2 py-1 font-weight-bold">Non-Aktif</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">Belum ada aturan jadwal rutin otomatis yang terdaftar.</td>
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

{{-- MODAL BUAT JADWAL MAINTENANCE BARU --}}
<div class="modal fade" id="modalTambahJadwal" tabindex="-1" role="dialog" aria-labelledby="modalTambahJadwalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title font-weight-bold" id="modalTambahJadwalLabel"><i class="fas fa-calendar-plus mr-2"></i>Buat Aturan Jadwal Perawatan Rutin</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.maintenance.schedule.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-3">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label class="font-weight-bold text-dark small" for="barang_masuk_id">Pilih Barang / Aset Fisik <span class="text-danger">*</span></label>
                            <select name="barang_masuk_id" id="barang_masuk_id" class="form-control select2-aset" required>
                                <option value="">-- Ketik / Scan Kode Aset --</option>
                                @foreach($barangs as $b)
                                    <option value="{{ $b->id }}">
                                        {{ $b->kode_asset ?? 'KODE-KOSONG' }} | {{ $b->masterBarang->nama_barang ?? 'Aset' }} 
                                        @if($b->serial_number) (SN: {{ $b->serial_number }}) @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1.5 d-block"><i class="fas fa-barcode mr-1"></i> Ketik kata kunci kode aset instan seperti <b>AST/IT/...</b></small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark small" for="frekuensi">Frekuensi Perawatan <span class="text-danger">*</span></label>
                            <select name="frekuensi" class="form-control" required>
                                <option value="">-- Pilih Periode Rutin --</option>
                                <option value="mingguan">Mingguan (Setiap 7 Hari)</option>
                                <option value="bulanan">Bulanan (Setiap 1 Bulan)</option>
                                <option value="tahunan">Tahunan (Setiap 1 Tahun)</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold text-dark small" for="tanggal_mulai">Tanggal Mulai Berlaku <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>

                    <div class="form-group mb-1">
                        <label class="font-weight-bold text-dark small" for="deskripsi_tugas">Deskripsi Standar Operasional Perawatan (SOP) <span class="text-danger">*</span></label>
                        <textarea name="deskripsi_tugas" class="form-control" rows="3" placeholder="Sebutkan langkah pokok perawatan rutin wajib. Misal: Pembersihan saringan AC, ukur tegangan listrik kompresor, test remote control." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0 py-2">
                    <button type="button" class="btn btn-sm btn-secondary font-weight-bold rounded-lg px-3" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-sm btn-primary font-weight-bold rounded-lg px-3"><i class="fas fa-save mr-1"></i> Simpan Sistem Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // 1. Inisialisasi DataTables
        var tablePerawatan = null;
        var tableSchedules = null;
        
        if ($.fn.DataTable) {
            tablePerawatan = $('#dataTablePerawatan').DataTable({
                "pageLength": 10,
                "language": { "search": "Cari Cepat:" }
            });
            tableSchedules = $('#dataTableSchedules').DataTable({
                "pageLength": 10,
                "language": { "search": "Cari Cepat:" }
            });
        }

        // PERBAIKAN PENTING: Mengatasi masalah lebar kolom DataTables yang mengecil/berantakan di dalam tab tersembunyi
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            if ($.fn.DataTable) {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            }
        });

        // 2. Inisialisasi Select2 khusus di dalam modal Bootstrap
        if ($.fn.select2) {
            $('.select2-aset').select2({
                theme: 'bootstrap4', 
                width: '100%', 
                dropdownParent: $('#modalTambahJadwal'), // Solusi utama agar dropdown tidak tenggelam di belakang modal overlay
                placeholder: "-- Ketik / Scan Kode Aset --",
                allowClear: true,
                language: {
                    noResults: function() { return "Barang atau kode aset tidak ditemukan."; }
                }
            });

            // Fokus otomatis ke search-box saat Select2 diklik
            $('.select2-aset').on('select2:open', function (e) {
                var searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) { searchField.focus(); }
            });
        }
    });
</script>
@endpush