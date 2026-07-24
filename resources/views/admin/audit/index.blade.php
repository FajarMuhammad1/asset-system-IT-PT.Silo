@extends('layouts.app') {{-- Ganti dengan nama layout Anda jika berbeda, misalnya layouts.admin --}}

@section('content')
<div class="container-fluid">
    
    {{-- Menampilkan Pesan Sukses atau Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">{{ $title }}</h6>
            {{-- Tombol untuk memunculkan Modal --}}
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#tambahAuditModal"> 
                <i class="fas fa-plus"></i> Buka Sesi Audit Baru
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Judul Audit</th>
                            <th>Keterangan</th>
                            <th>Dibuat Oleh</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th width="25%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($audits as $key => $audit)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><strong>{{ $audit->title }}</strong></td>
                                <td>{{ $audit->description ?? '-' }}</td>
                                <td>{{ $audit->pengaju->name ?? 'Sistem' }}</td>
                                <td>{{ \Carbon\Carbon::parse($audit->audit_date)->format('d M Y') }}</td>
                                <td>
                                    @if($audit->status == 'On Progress')
                                        <span class="badge badge-warning bg-warning">On Progress</span>
                                    @else
                                        <span class="badge badge-success bg-success">Completed</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    {{-- Tombol menuju halaman Show/Scan --}}
                                    <a href="{{ route('admin.audit.show', $audit->id) }}" class="btn btn-info btn-sm text-white mb-1">
                                        <i class="fas fa-eye"></i> Buka / Scan
                                    </a>

                                    {{-- Tombol Cetak PDF (Hanya Muncul Jika Sudah Selesai) --}}
                                    @if($audit->status == 'Completed')
                                        <button type="button" 
                                                class="btn btn-danger btn-sm text-white mb-1 btn-cetak-swal"
                                                data-url="{{ route('admin.audit.print', $audit->id) }}"
                                                data-title="Cetak Laporan Audit"
                                                data-desc="Anda akan mencetak laporan hasil audit opname:<br><b>{{ $audit->title }}</b><br><br>Dokumen PDF akan terbuka di tab baru. Lanjutkan?"
                                                data-icon="info"
                                                data-confirm="Ya, Cetak PDF"
                                                data-target="_blank">
                                            <i class="fas fa-file-pdf"></i> Cetak
                                        </button>
                                    @endif

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.audit.destroy', $audit->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sesi audit ini? Semua data pindaian di dalamnya juga akan terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-dark btn-sm text-white mb-1">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada data sesi audit. Silakan buat baru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH AUDIT -->
<div class="modal fade" id="tambahAuditModal" tabindex="-1" aria-labelledby="tambahAuditModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahAuditModalLabel">Buka Sesi Audit Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            {{-- Form mengarah ke route admin.audit.store --}}
            <form action="{{ route('admin.audit.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label">Judul Audit <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" placeholder="Contoh: Audit Tahunan 2026" required value="{{ old('title') }}">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="description" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Tambahkan catatan jika diperlukan...">{{ old('description') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Buka Sesi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection