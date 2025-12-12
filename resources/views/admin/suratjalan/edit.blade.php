@extends('layouts.app')
@section('content')

<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit mr-2"></i> {{ $title }}
</h1>

<div class="card shadow-sm">
    <div class="card-body">
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan! Silakan cek kembali input Anda:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('surat-jalan.update', $suratJalan->id_sj) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <h5 class="font-weight-bold text-primary">Informasi Dokumen (Header)</h5>
            <p class="text-muted small">Catatan: Mengedit di sini hanya akan mengubah data header surat jalan.</p>
            <hr>
            
            <div class="row">
                
                {{-- 1. ID Surat Jalan --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>ID Surat Jalan  <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="id_suratjalan" 
                               class="form-control @error('id_suratjalan') is-invalid @enderror" 
                               value="{{ old('id_suratjalan', $suratJalan->id_suratjalan) }}" 
                               required>
                        @error('id_suratjalan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- 2. Nomor SJ --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor Surat Jalan (SJ)</label>
                        <input type="text" name="no_sj" class="form-control" value="{{ old('no_sj', $suratJalan->no_sj) }}" required>
                    </div>
                </div>
                
                {{-- 3. Nomor PPI (Dropdown) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PPI</label>
                        <select name="no_ppi" class="form-control select2-ppi" required>
                            <option value="">-- Pilih Nomor PPI --</option>
                            @foreach($daftarPpi as $ppi)
                                <option value="{{ $ppi->no_ppi }}" {{ old('no_ppi', $suratJalan->no_ppi) == $ppi->no_ppi ? 'selected' : '' }}>
                                    {{ $ppi->no_ppi }} - {{ $ppi->user->name ?? 'User' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- 4. Nomor PO --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Nomor PO</label>
                        <input type="text" name="no_po" class="form-control" value="{{ old('no_po', $suratJalan->no_po) }}" required>
                    </div>
                </div>

                {{-- 5. Tanggal Input --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tanggal Input</label>
                        <input type="date" name="tanggal_input" class="form-control" value="{{ old('tanggal_input', $suratJalan->tanggal_input) }}" required>
                    </div>
                </div>

                {{-- 6. Jenis SJ --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Jenis Surat Jalan</label>
                        <input type="text" name="jenis_surat_jalan" class="form-control" value="{{ old('jenis_surat_jalan', $suratJalan->jenis_surat_jalan) }}" required>
                    </div>
                </div>
                
                {{-- 7. Keterangan --}}
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="2">{{ old('keterangan', $suratJalan->keterangan) }}</textarea>
                    </div>
                </div>

                {{-- 8. STATUS BAST (READ ONLY / OTOMATIS) --}}
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Status Serah Terima (BAST)</label>
                        <div>
                            @if($suratJalan->is_bast_submitted)
                                {{-- Tampilan Jika Sudah Selesai --}}
                                <div class="alert alert-success py-2 px-3 m-0 shadow-sm border-left-success">
                                    <i class="fas fa-check-circle mr-2"></i> 
                                    <strong>COMPLETE (Selesai)</strong>
                                </div>
                                <small class="d-block text-muted mt-1">
                                    Semua barang sudah diserahterimakan.
                                </small>
                            @else
                                {{-- Tampilan Jika Belum Selesai --}}
                                <div class="alert alert-warning py-2 px-3 m-0 text-dark shadow-sm border-left-warning">
                                    <i class="fas fa-clock mr-2"></i> 
                                    <strong>PENDING / SEBAGIAN</strong>
                                </div>
                                <div class="mt-1">
                                    <small class="text-muted">
                                        Status otomatis berubah saat BAST selesai di menu 
                                        <a href="{{ route('barangkeluar.index') }}" target="_blank">Serah Terima</a>.
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- 9. File Upload --}}
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Upload File Baru (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="customFile" name="file">
                            <label class="custom-file-label" for="customFile">Pilih file PDF/Gambar...</label>
                        </div>
                        @if ($suratJalan->file)
                            <small class="text-success mt-1 d-block">
                                <i class="fas fa-file-alt mr-1"></i> 
                                File saat ini: <a href="{{ asset('storage/' . $suratJalan->file) }}" target="_blank" class="font-weight-bold">Lihat Dokumen</a>
                            </small>
                        @endif
                    </div>
                </div>

            </div>

            <div class="d-flex justify-content-end mt-3">
                <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary mr-2">Batal</a>
                <button type="submit" class="btn btn-primary shadow-sm">
                    <i class="fas fa-save mr-2"></i> Simpan Perubahan
                </button>
            </div>
        </form>

        <h5 class="font-weight-bold mt-5 text-primary">List Barang di Surat Jalan Ini</h5>
        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="bg-light text-dark">
                    <tr>
                        <th width="5%">No.</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Merk</th>
                        <th class="text-center">Qty</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($suratJalan->details as $detail)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detail->masterBarang->nama_barang }}</td>
                        <td>{{ $detail->masterBarang->kategori->nama_kategori ?? '-' }}</td>
                        <td>{{ $detail->masterBarang->merk }}</td>
                        <td class="text-center font-weight-bold">{{ $detail->qty }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada barang detail di Surat Jalan ini.</td>
                    </tr>
                    @endForelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection

@push('scripts')
{{-- Script agar nama file muncul saat upload --}}
<script>
    $('.custom-file-input').on('change', function() { 
        let fileName = $(this).val().split('\\').pop(); 
        $(this).next('.custom-file-label').addClass("selected").html(fileName); 
    });
</script>
@endpush