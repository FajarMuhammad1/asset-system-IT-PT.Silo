@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }} - Tahun {{ $tahunDipilih }}</h1>
        
        <form action="{{ route('rkab.index') }}" method="GET" class="d-flex align-items-center">
            <label for="tahun" class="me-2 mb-0 font-weight-bold text-dark">Tahun:</label>
            <select name="tahun" id="tahun" class="form-control form-control-sm" onchange="this.form.submit()">
                @foreach($listTahun as $t)
                    <option value="{{ $t }}" {{ $tahunDipilih == $t ? 'selected' : '' }}>{{ $t }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Alokasi Anggaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalAlokasi, 0, ',', '.') }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Realisasi / Terpakai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalTerpakai, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Sisa Anggaran</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Persentase Penyerapan</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $totalPersentase }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalPersentase }}%" aria-valuenow="{{ $totalPersentase }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-lg-5 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Visualisasi Komparasi Dana</h6>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center" style="height: 350px;">
                    <canvas id="budgetChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Rincian Anggaran RKAB Per Kategori</h6>
                    
                    <div>
                        <a href="{{ route('rkab.print', ['tahun' => $tahunDipilih]) }}" target="_blank" class="btn btn-sm btn-info shadow-sm mr-2">
                            <i class="fas fa-print fa-sm text-white-50 mr-1"></i> Cetak Laporan
                        </a>
                        
                        <button class="btn btn-sm btn-primary shadow-sm" data-toggle="modal" data-target="#modalTambahRKAB">
                            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Tambah RKAB
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Kategori Kegiatan/Barang</th>
                                    <th>Alokasi Anggaran</th>
                                    <th>Realisasi (Terpakai)</th>
                                    <th>Sisa Anggaran</th>
                                    <th>Penyerapan</th>
                                    <th width="12%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($budgets as $budget)
                                <tr>
                                    <td class="font-weight-bold text-secondary">{{ $budget->kategori }}</td>
                                    <td>Rp {{ number_format($budget->anggaran_alokasi, 0, ',', '.') }}</td>
                                    <td class="text-success">Rp {{ number_format($budget->anggaran_terpakai, 0, ',', '.') }}</td>
                                    <td class="{{ $budget->sisa_anggaran < 0 ? 'text-danger' : 'text-warning' }}">
                                        Rp {{ number_format($budget->sisa_anggaran, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = 'secondary';
                                            if($budget->persentase_penyerapan > 80) $badgeClass = 'success';
                                            elseif($budget->persentase_penyerapan > 40) $badgeClass = 'info';
                                        @endphp
                                        <span class="badge badge-{{ $badgeClass }} p-2">
                                            {{ $budget->persentase_penyerapan }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning mb-1" data-toggle="modal" data-target="#modalEditRKAB{{ $budget->id }}" title="Edit">
                                            <i class="fas fa-edit text-dark"></i>
                                        </button>
                                        
                                        <form action="{{ route('rkab.destroy', $budget->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus data RKAB ini?');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-danger mb-1" title="Hapus"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="modalEditRKAB{{ $budget->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning text-dark">
                                                <h5 class="modal-title font-weight-bold"><i class="fas fa-edit mr-1"></i> Edit Data RKAB</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('rkab.update', $budget->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Tahun Anggaran</label>
                                                        <input type="number" name="tahun" class="form-control" value="{{ $budget->tahun }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Kategori Kegiatan/Barang</label>
                                                        <input type="text" name="kategori" class="form-control" value="{{ $budget->kategori }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Total Alokasi (Rp)</label>
                                                        <input type="number" name="anggaran_alokasi" class="form-control" value="{{ $budget->anggaran_alokasi }}" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold text-success">Realisasi Terpakai (Rp)</label>
                                                        <input type="number" name="anggaran_terpakai" class="form-control" value="{{ $budget->anggaran_terpakai }}" required>
                                                        <small class="text-muted">Update nilai ini jika ada pengeluaran baru.</small>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="font-weight-bold">Keterangan (Opsional)</label>
                                                        <textarea name="keterangan" class="form-control" rows="2">{{ $budget->keterangan }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-warning text-dark font-weight-bold">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-folder-open fa-2x mb-2 text-gray-300"></i><br>
                                        Belum ada rencana kerja anggaran (RKAB) yang diinput untuk tahun ini.
                                    </td>
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

<div class="modal fade" id="modalTambahRKAB" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-1"></i> Input RKAB Baru</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('rkab.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tahun Anggaran</label>
                        <input type="number" name="tahun" class="form-control" value="{{ $tahunDipilih ?? date('Y') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Kategori Kegiatan/Barang</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Pengadaan PC / Maintenance Jaringan" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Total Alokasi Anggaran (Rp)</label>
                        <input type="number" name="anggaran_alokasi" class="form-control" placeholder="Contoh: 50000000" required>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Realisasi Terpakai (Awal)</label>
                        <input type="number" name="anggaran_terpakai" class="form-control" value="0">
                        <small class="text-muted">Biarkan 0 jika belum ada dana yang digunakan.</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary font-weight-bold">Simpan Anggaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById("budgetChart");
        if(ctx) {
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ["Realisasi (Terpakai)", "Sisa Anggaran Tersedia"],
                    datasets: [{
                        data: [{{ $totalTerpakai }}, {{ $totalSisa < 0 ? 0 : $totalSisa }}],
                        backgroundColor: ['#1cc88a', '#f6c23e'],
                        hoverBackgroundColor: ['#17a673', '#dda20a'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10
                    },
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    cutoutPercentage: 70,
                },
            });
        }
    });
</script>
@endpush