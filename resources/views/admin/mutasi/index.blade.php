@extends('layouts.app') 

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-exchange-alt mr-2 text-primary"></i> {{ $title }}</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Cards Stats Ringkasan Mutasi -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Approval Manager</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingApprovals->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Disetujui (Perlu Eksekusi IT)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $readyForIT->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Menunggu TTD BAST</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedMutations->where('status', 'Menunggu TTD BAST')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-signature fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Mutasi Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $completedMutations->where('status', 'Selesai')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TABS ALUR MUTASI -->
    <ul class="nav nav-tabs id-mutasi-tabs mb-4" id="mutasiTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active font-weight-bold" id="approval-tab" data-toggle="tab" href="#approval" role="tab">
                <i class="fas fa-user-check mr-1 text-warning"></i> Review Manager ({{ $pendingApprovals->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold" id="eksekusi-tab" data-toggle="tab" href="#eksekusi" role="tab">
                <i class="fas fa-truck-loading mr-1 text-primary"></i> Eksekusi IT ({{ $readyForIT->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link font-weight-bold" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab">
                <i class="fas fa-history mr-1 text-secondary"></i> Semuar Riwayat Mutasi ({{ $riwayatMutasi->count() }})
            </a>
        </li>
        <li class="nav-item ml-auto">
            <button class="btn btn-primary btn-sm shadow-sm" data-toggle="collapse" data-target="#formDirectMutasi">
                <i class="fas fa-plus mr-1"></i> Form Mutasi IT Langsung
            </button>
        </li>
    </ul>

    <!-- FORM MUTASI LANGSUNG IT (COLLAPSIBLE) -->
    <div class="collapse mb-4" id="formDirectMutasi">
        <div class="card shadow border-left-primary">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-1"></i> Form Mutasi Perangkat IT Langsung</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('mutasi.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="barang_masuk_id" class="font-weight-bold text-dark">Pilih Perangkat / Aset IT <span class="text-danger">*</span></label>
                                <select class="form-control select2-enable" id="barang_masuk_id" name="barang_masuk_id" required>
                                    <option value="" selected disabled>-- Pilih Perangkat --</option>
                                    @foreach($assets as $asset)
                                        <option value="{{ $asset->id }}">
                                            [{{ $asset->kode_asset ?? 'N/A' }}] - {{ $asset->masterBarang->nama_barang ?? 'Aset' }} (S/N: {{ $asset->serial_number ?? '-' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="user_tujuan_id" class="font-weight-bold text-dark">User Tujuan Baru <span class="text-danger">*</span></label>
                                <select class="form-control select2-enable" id="user_tujuan_id" name="user_tujuan_id" required>
                                    <option value="" selected disabled>-- Pilih User Penerima --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->nama }} ({{ $user->jabatan ?? 'Staf' }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="lokasi_baru" class="font-weight-bold text-dark">Lokasi Penempatan Baru</label>
                                <input type="text" class="form-control" id="lokasi_baru" name="lokasi_baru" placeholder="Contoh: Ruang Purchasing Lt. 2">
                            </div>

                            <div class="form-group mb-3">
                                <label for="keterangan" class="font-weight-bold text-dark">Keterangan Mutasi <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2" placeholder="Alasan pemindahan perangkat..." required></textarea>
                            </div>
                        </div>

                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm"><i class="fas fa-paper-plane mr-1"></i> Simpan & Mutasikan Aset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- TAB CONTENTS -->
    <div class="tab-content" id="mutasiTabContent">
        
        <!-- TAB 1: PENDING APPROVAL MANAGER -->
        <div class="tab-pane fade show active" id="approval" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-warning"><i class="fas fa-user-clock mr-1"></i> Permintaan Mutasi Menunggu Persetujuan Manager / Atasan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Aset</th>
                                    <th>Pemohon</th>
                                    <th>User Asal</th>
                                    <th>User Tujuan</th>
                                    <th>Alasan Mutasi</th>
                                    <th>Tanggal Pengajuan</th>
                                    <th width="15%">Aksi Manager</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingApprovals as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->barangMasuk->kode_asset ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $item->barangMasuk->masterBarang->nama_barang ?? 'Aset' }}</small>
                                        </td>
                                        <td>{{ $item->pemohon->nama ?? 'Pengguna' }}</td>
                                        <td>{{ $item->userAsal->nama ?? 'Stok Gudang' }}</td>
                                        <td><span class="badge badge-info">{{ $item->userTujuan->nama ?? '-' }}</span></td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y H:i') }}</td>
                                        <td>
                                            @if(in_array(Auth::user()->role, ['SuperAdmin', 'Admin']) || str_contains(Auth::user()->jabatan, 'Manager'))
                                                <form action="{{ route('mutasi.approve', $item->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success mb-1" onclick="return confirm('Setujui pengajuan mutasi ini?')">
                                                        <i class="fas fa-check mr-1"></i> Approve
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger mb-1" data-toggle="modal" data-target="#rejectModal{{ $item->id }}">
                                                    <i class="fas fa-times mr-1"></i> Reject
                                                </button>

                                                <!-- MODAL REJECT -->
                                                <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('mutasi.reject', $item->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title"><i class="fas fa-times-circle mr-1"></i> Penolakan Mutasi</h5>
                                                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                                                </div>
                                                                <div class="modal-body text-left">
                                                                    <p>Berikan alasan penolakan pengajuan mutasi ini untuk diinfokan ke pemohon:</p>
                                                                    <div class="form-group">
                                                                        <textarea name="alasan_penolakan" class="form-control" rows="3" required placeholder="Tuliskan alasan penolakan..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="badge badge-warning">Menunggu Manager</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">TIDAK ADA PENGAJUAN MUTASI YANG MENUNGGU APPROVAL MANAGER.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: EKSEKUSI IT (DISETUJUI MANAGER) -->
        <div class="tab-pane fade" id="eksekusi" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-tools mr-1"></i> Mutasi Disetujui Manager - Siap Eksekusi Fisik & Terbitkan BAST Digital</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Aset</th>
                                    <th>User Lama</th>
                                    <th>User Tujuan Baru</th>
                                    <th>Disetujui Oleh</th>
                                    <th>Alasan Mutasi</th>
                                    <th width="15%">Aksi Admin IT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($readyForIT as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->barangMasuk->kode_asset ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $item->barangMasuk->masterBarang->nama_barang ?? 'Aset' }}</small>
                                        </td>
                                        <td>{{ $item->userAsal->nama ?? 'Stok Gudang' }}</td>
                                        <td><span class="badge badge-info">{{ $item->userTujuan->nama ?? '-' }}</span></td>
                                        <td><small class="text-success"><i class="fas fa-user-check mr-1"></i>{{ $item->approver->nama ?? 'Manager' }}</small></td>
                                        <td>{{ $item->keterangan }}</td>
                                        <td>
                                            <form action="{{ route('mutasi.process', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-primary btn-block shadow-sm" onclick="return confirm('Eksekusi mutasi fisik & kirim BAST digital ke user tujuan?')">
                                                    <i class="fas fa-truck-loading mr-1"></i> Eksekusi & Terbitkan BAST
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada mutasi yang menunggu eksekusi IT.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 3: RIWAYAT LENGKAP -->
        <div class="tab-pane fade" id="riwayat" role="tabpanel">
            <div class="card shadow mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-history mr-1"></i> Semuar Log Riwayat Mutasi Aset</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0">
                            <thead class="bg-light text-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Aset</th>
                                    <th>User Asal</th>
                                    <th>User Tujuan</th>
                                    <th>Pemohon</th>
                                    <th>Status Mutasi</th>
                                    <th>Tanggal</th>
                                    <th>BAST Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($riwayatMutasi as $idx => $item)
                                    <tr>
                                        <td>{{ $idx + 1 }}</td>
                                        <td>
                                            <strong>{{ $item->barangMasuk->kode_asset ?? '-' }}</strong><br>
                                            <small class="text-muted">{{ $item->barangMasuk->masterBarang->nama_barang ?? 'Aset' }}</small>
                                        </td>
                                        <td>{{ $item->userAsal->nama ?? 'Stok' }}</td>
                                        <td><span class="badge badge-info">{{ $item->userTujuan->nama ?? '-' }}</span></td>
                                        <td>{{ $item->pemohon->nama ?? 'Admin' }}</td>
                                        <td>
                                            @if($item->status == 'Menunggu Approval Manager')
                                                <span class="badge badge-warning">Menunggu Manager</span>
                                            @elseif($item->status == 'Disetujui Manager')
                                                <span class="badge badge-primary">Disetujui Manager</span>
                                            @elseif($item->status == 'Menunggu TTD BAST')
                                                <span class="badge badge-info">Menunggu TTD BAST</span>
                                            @elseif($item->status == 'Selesai')
                                                <span class="badge badge-success">Selesai</span>
                                            @elseif($item->status == 'Ditolak Manager')
                                                <span class="badge badge-danger">Ditolak</span>
                                            @else
                                                <span class="badge badge-secondary">{{ $item->status }}</span>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($item->tanggal_mutasi ?? $item->created_at)->format('d M Y') }}</td>
                                        <td>
                                            @if($item->logSerahTerima)
                                                <a href="{{ route('barangkeluar.show', $item->logSerahTerima->id) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-file-contract mr-1"></i> BAST #{{ $item->logSerahTerima->id }}
                                                </a>
                                            @else
                                                <span class="text-muted small">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">Belum ada riwayat mutasi.</td>
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
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        if ($('.select2-enable').length) {
            $('.select2-enable').select2({
                theme: 'bootstrap4'
            });
        }
    });
</script>
@endpush