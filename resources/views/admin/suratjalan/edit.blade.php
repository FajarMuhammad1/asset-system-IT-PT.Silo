@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit mr-2"></i> {{ $title ?? 'Edit Surat Jalan' }}
        </h1>
        <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
    </div>

    {{-- ERROR ALERT --}}
    @if ($errors->any())
        <div class="alert alert-danger border-left-danger shadow-sm">
            <strong><i class="fas fa-exclamation-triangle mr-1"></i> Perhatian!</strong> Silakan cek inputan Anda:
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('surat-jalan.update', $suratJalan->id_sj) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- CARD 1: FORM EDIT HEADER --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">Form Edit Header Surat Jalan</h6>
            </div>
            <div class="card-body">
                
                {{-- SEGMENT 1: IDENTITAS UTAMA --}}
                <h6 class="font-weight-bold text-primary mb-3">1. Identitas Dokumen</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>ID Surat Jalan (Internal) <span class="text-danger">*</span></label>
                            <input type="text" name="id_suratjalan" 
                                   class="form-control @error('id_suratjalan') is-invalid @enderror" 
                                   value="{{ old('id_suratjalan', $suratJalan->id_suratjalan) }}" required>
                            @error('id_suratjalan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nomor Surat Jalan (Fisik) <span class="text-danger">*</span></label>
                            <input type="text" name="no_sj" class="form-control" 
                                   value="{{ old('no_sj', $suratJalan->no_sj) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tanggal Input <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_input" class="form-control" 
                                   value="{{ old('tanggal_input', $suratJalan->tanggal_input) }}" required>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- SEGMENT 2: REFERENSI --}}
                <h6 class="font-weight-bold text-primary mb-3">2. Referensi & Keterangan</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nomor PPI <span class="text-danger">*</span></label>
                            <select name="no_ppi" class="form-control select2-ppi" required>
                                <option value="">-- Pilih Nomor PPI --</option>
                                @foreach($daftarPpi as $ppi)
                                    <option value="{{ $ppi->no_ppi }}" 
                                        {{ old('no_ppi', $suratJalan->no_ppi) == $ppi->no_ppi ? 'selected' : '' }}>
                                        {{ $ppi->no_ppi }} - {{ $ppi->user->nama ?? 'User' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nomor PO</label>
                            <input type="text" name="no_po" class="form-control" 
                                   value="{{ old('no_po', $suratJalan->no_po) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Jenis Surat Jalan</label>
                            <input type="text" name="jenis_surat_jalan" class="form-control" 
                                   value="{{ old('jenis_surat_jalan', $suratJalan->jenis_surat_jalan) }}" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Keterangan Tambahan</label>
                            <textarea name="keterangan" class="form-control" rows="2" 
                                      placeholder="Tambahkan catatan jika perlu...">{{ old('keterangan', $suratJalan->keterangan) }}</textarea>
                        </div>
                    </div>
                </div>

                <hr>

                {{-- SEGMENT 3: STATUS & FILE --}}
                <h6 class="font-weight-bold text-primary mb-3">3. Status & Lampiran</h6>
                <div class="row">
                    {{-- Status BAST --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Status Serah Terima (BAST)</label>
                            @if($suratJalan->is_bast_submitted)
                                <div class="alert alert-success py-2 px-3 border-left-success" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle fa-2x mr-3"></i>
                                        <div>
                                            <h6 class="font-weight-bold mb-0">SELESAI (Completed)</h6>
                                            <small>Seluruh barang telah diserahterimakan.</small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-warning py-2 px-3 border-left-warning text-dark" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock fa-2x mr-3"></i>
                                        <div>
                                            <h6 class="font-weight-bold mb-0">PENDING / PROSES</h6>
                                            <small>Menunggu proses BAST di menu Barang Keluar.</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Upload File --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Update Lampiran (Opsional)</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="customFile" name="file">
                                    <label class="custom-file-label" for="customFile">Pilih file PDF/Gambar...</label>
                                </div>
                            </div>
                            @if ($suratJalan->file)
                                <div class="mt-2 p-2 bg-light rounded border">
                                    <small class="text-muted d-block">File saat ini:</small>
                                    <a href="{{ asset('storage/' . $suratJalan->file) }}" target="_blank" class="font-weight-bold text-info">
                                        <i class="fas fa-file-download mr-1"></i> Lihat Dokumen Terlampir
                                    </a>
                                </div>
                            @else
                                <small class="text-muted mt-1 d-block">Belum ada file yang diupload.</small>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- TOMBOL AKSI --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="reset" class="btn btn-secondary mr-2">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>

            </div>
        </div>
    </form>

    {{-- CARD 2: LIST ITEM (READ ONLY DI SINI) --}}
    <div class="card shadow mb-4">
        <a href="#collapseItem" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseItem">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-box mr-2"></i> Daftar Barang di Surat Jalan Ini</h6>
        </a>
        <div class="collapse show" id="collapseItem">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Merk</th>
                                <th class="text-center">Qty Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suratJalan->details as $detail)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="font-weight-bold">{{ $detail->masterBarang->nama_barang }}</td>
                                <td>{{ $detail->masterBarang->kategori->nama_kategori ?? '-' }}</td>
                                <td>{{ $detail->masterBarang->merk }}</td>
                                <td class="text-center">
                                    <span class="badge badge-primary px-2 py-1" style="font-size: 0.9rem">
                                        {{ $detail->qty }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">
                                    <i class="fas fa-box-open mb-2"></i><br>
                                    Belum ada detail barang.
                                </td>
                            </tr>
                            @endForelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-2 text-muted small">
                    <i class="fas fa-info-circle"></i> <em>Untuk mengubah item barang, silakan hapus data ini dan input ulang, atau hubungi administrator.</em>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Script agar nama file muncul di label input file
    $('.custom-file-input').on('change', function() { 
        let fileName = $(this).val().split('\\').pop(); 
        $(this).next('.custom-file-label').addClass("selected").html(fileName); 
    });
</script>
@endpush