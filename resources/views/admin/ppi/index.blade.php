@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Monitoring Request PPI (Admin IT)</h1>

    @if(session('success'))
        <div class="alert alert-success border-left-success shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-left-danger shadow-sm alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- KITA KELOMPOKKAN DATA MENGGUNAKAN LARAVEL COLLECTION AGAR LEBIH RAPI --}}
    @php
        $tabs = [
            ['id' => 'all', 'label' => 'Semua', 'icon' => 'fa-list', 'color' => 'primary', 'data' => $dataPpi],
            ['id' => 'pending', 'label' => 'Cek Admin', 'icon' => 'fa-search', 'color' => 'info', 'data' => $dataPpi->where('status', 'pending')],
            ['id' => 'spv', 'label' => 'Menunggu SPV/SA', 'icon' => 'fa-user-tie', 'color' => 'warning', 'data' => $dataPpi->where('status', 'pending_superadmin')],
            ['id' => 'disetujui', 'label' => 'Disetujui', 'icon' => 'fa-check-circle', 'color' => 'success', 'data' => $dataPpi->where('status', 'disetujui')],
            ['id' => 'selesai', 'label' => 'Selesai', 'icon' => 'fa-flag-checkered', 'color' => 'dark', 'data' => $dataPpi->where('status', 'selesai')],
        ];
    @endphp

    {{-- NAVIGASI TABS --}}
    <ul class="nav nav-tabs mb-3 shadow-sm bg-white rounded" role="tablist" style="border: none; padding: 5px;">
        @foreach($tabs as $index => $tab)
        <li class="nav-item">
            <a class="nav-link {{ $index == 0 ? 'active' : '' }} font-weight-bold px-4 text-{{ $tab['color'] }}" data-toggle="tab" href="#tab-{{ $tab['id'] }}" role="tab">
                <i class="fas {{ $tab['icon'] }} mr-1"></i> {{ $tab['label'] }} ({{ $tab['data']->count() }})
            </a>
        </li>
        @endforeach
    </ul>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Permintaan Masuk</h6>
            <button type="button" class="btn btn-success btn-sm shadow-sm" data-toggle="modal" data-target="#modalExport">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Filter & Export Excel
            </button>
        </div>
        
        <div class="card-body">
            <div class="tab-content">
                {{-- KONTEN TABS (TABEL OTOMATIS DIGENERATE SESUAI KATEGORI) --}}
                @foreach($tabs as $index => $tab)
                <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="tab-{{ $tab['id'] }}" role="tabpanel">
                    <div class="table-responsive">
                        {{-- Menggunakan class unik datatable-multi agar tidak bentrok dengan template JS Anda --}}
                        <table class="table table-bordered table-hover datatable-multi" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>No PPI</th>
                                    <th>Tanggal</th>
                                    <th>Pemohon</th>
                                    <th>Dept / PT</th>
                                    <th>Perangkat / Aset</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" width="18%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tab['data'] as $item)
                                <tr>
                                    <td class="font-weight-bold text-primary align-middle">{{ $item->no_ppi }}</td>
                                    <td class="align-middle" data-sort="{{ Carbon\Carbon::parse($item->created_at)->timestamp }}">
                                        {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
                                    </td>
                                    <td class="align-middle">
                                        <div class="font-weight-bold">{{ $item->user->nama ?? 'User Hapus' }}</div>
                                        <small class="text-muted">{{ $item->user->email ?? '-' }}</small>
                                    </td>

                                    <td class="align-middle">
                                        <div class="small font-weight-bold">{{ $item->user->departemen ?? '-' }}</div>
                                        <span class="badge badge-light border">{{ $item->user->perusahaan ?? '-' }}</span>
                                    </td>

                                    <td class="align-middle">
                                        <span class="text-dark font-weight-bold">{{ $item->perangkat ?? '-' }}</span>
                                        @if($item->file_ppi)
                                            <br>
                                            <a href="{{ asset('storage/'.$item->file_ppi) }}" target="_blank" class="badge badge-info mt-1">
                                                <i class="fas fa-paperclip"></i> Lampiran
                                            </a>
                                        @endif
                                    </td>
                                    
                                    <td class="text-center align-middle">
                                        @if($item->status == 'pending')
                                            <span class="badge badge-info px-2 py-1"><i class="fas fa-search"></i> Cek Admin</span>
                                        @elseif($item->status == 'pending_superadmin')
                                            <span class="badge badge-warning px-2 py-1 text-dark"><i class="fas fa-user-tie"></i> Menunggu SPV/SA</span>
                                        @elseif($item->status == 'disetujui')
                                            <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Disetujui</span>
                                        @elseif($item->status == 'selesai')
                                            <span class="badge badge-dark px-2 py-1"><i class="fas fa-flag-checkered"></i> Selesai</span>
                                        @elseif($item->status == 'ditolak')
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle"></i> Ditolak</span>
                                        @endif
                                    </td>
                                    
                                    <td class="align-middle">
                                        <div class="d-flex flex-column">
                                            <a href="{{ route('admin.ppi.show', $item->id) }}" class="btn btn-sm btn-outline-primary mb-2">
                                                <i class="fas fa-eye"></i> Detail
                                            </a>

                                            @if($item->status == 'pending')
                                                <form action="{{ route('admin.ppi.forward', $item->id) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <button type="submit" class="btn btn-primary btn-sm btn-block shadow-sm" onclick="return confirm('Teruskan ke SuperAdmin?')">
                                                        <i class="fas fa-paper-plane"></i> Ajukan ke SA
                                                    </button>
                                                </form>
                                            @elseif($item->status == 'pending_superadmin')
                                                <button class="btn btn-secondary btn-sm btn-block" disabled style="opacity: 0.7;">
                                                    <i class="fas fa-clock"></i> Menunggu SA
                                                </button>
                                            @elseif($item->status == 'ditolak')
                                                <button class="btn btn-danger btn-sm btn-block" disabled><i class="fas fa-ban"></i> Ditolak</button>
                                            @elseif($item->status == 'selesai')
                                                <button class="btn btn-dark btn-sm btn-block" disabled><i class="fas fa-check"></i> Closed</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-3x mb-3"></i><br>Belum ada data permintaan PPI.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

{{-- MODAL EXPORT EXCEL --}}
<div class="modal fade" id="modalExport" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-excel"></i> Filter & Export PPI</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('admin.ppi.export') }}" method="GET">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Filter Harian (Tanggal):</label>
                        <input type="date" name="tanggal" class="form-control">
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="font-weight-bold">Atau Filter Bulanan:</label>
                        <div class="row">
                            <div class="col-6">
                                <select name="bulan" class="form-control">
                                    <option value="">-- Pilih Bulan --</option>
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <select name="tahun" class="form-control">
                                    <option value="">-- Pilih Tahun --</option>
                                    @for($y=date('Y'); $y>=2020; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label class="font-weight-bold">Filter Perusahaan:</label>
                        <select name="perusahaan" class="form-control">
                            <option value="">Semua Perusahaan</option>
                            @php
                                $daftarPerusahaan = $dataPpi->map(function($item) {
                                    return $item->user->perusahaan ?? null;
                                })->filter()->unique()->sort();
                            @endphp
                            @foreach($daftarPerusahaan as $pt)
                                <option value="{{ $pt }}">{{ $pt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-download"></i> Download Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tunggu jQuery & DataTables selesai dimuat
        var initDataTables = setInterval(function() {
            if (window.jQuery && $.fn.DataTable) {
                clearInterval(initDataTables);
                
                // Inisialisasi tabel khusus untuk tab ini
                $('.datatable-multi').DataTable({
                    "pageLength": 10,
                    "language": {
                        "emptyTable": "Belum ada data permintaan untuk status ini."
                    }
                });

                // Memperbaiki masalah lebar kolom DataTables saat tab berpindah
                $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                    $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
                });
            }
        }, 100);
    });
</script>
@endsection