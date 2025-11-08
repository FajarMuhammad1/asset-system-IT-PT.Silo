@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4 text-gray-800">
    <i class="fas fa-edit mr-2"></i> Edit Surat Jalan
</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('surat-jalan.update', $suratJalan->id_sj) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Nomor Surat Jalan --}}
                <div class="col-md-4 mb-3">
                    <label>No Surat Jalan</label>
                    <input type="text" name="no_sj" class="form-control" 
                           value="{{ old('no_sj', $suratJalan->no_sj) }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label>No PPI</label>
                    <input type="text" name="no_ppi" class="form-control" 
                           value="{{ old('no_ppi', $suratJalan->no_ppi) }}" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label>No PO</label>
                    <input type="text" name="no_po" class="form-control" 
                           value="{{ old('no_po', $suratJalan->no_po) }}" required>
                </div>

                {{-- Kategori --}}
                <div class="col-md-6 mb-3">
                    <label>Kategori</label>
                    <select name="kategori[]" id="kategori" class="form-control select2" multiple required>
                        @php
                            $kategoriList = [
                                'access point','analogue telephone','antena radio rig','camera','consumable',
                                'cctv','dongle','extender','fingerprint','gps','headset','hard disk eksternal',
                                'ip telephone','keyboard','laptop','modem','monitor','mouse','mobile phone',
                                'pc desktop','power supply','proyektor','print server','printer','radio ht',
                                'radio rig','router','scanner','ssd eksternal','stavolt','switch hub','ups',
                                'tv','webcam'
                            ];
                            $selectedKategori = explode(', ', $suratJalan->kategori);
                        @endphp
                        @foreach($kategoriList as $item)
                            <option value="{{ $item }}" {{ in_array($item, $selectedKategori) ? 'selected' : '' }}>
                                {{ ucfirst($item) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label>Merk</label>
                    <input type="text" name="merk" class="form-control" 
                           value="{{ old('merk', $suratJalan->merk) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Model</label>
                    <input type="text" name="model" class="form-control" 
                           value="{{ old('model', $suratJalan->model) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Spesifikasi</label>
                    <input type="text" name="spesifikasi" class="form-control" 
                           value="{{ old('spesifikasi', $suratJalan->spesifikasi) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" 
                           value="{{ old('serial_number', $suratJalan->serial_number) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label>Kode Asset</label>
                    <input type="text" name="kode_asset" class="form-control" 
                           value="{{ old('kode_asset', $suratJalan->kode_asset) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label>Qty</label>
                    <input type="number" name="qty" class="form-control" 
                           value="{{ old('qty', $suratJalan->qty) }}" required>
                </div>

                {{-- Jenis Surat Jalan --}}
                <div class="col-md-4 mb-3">
                    <label>Jenis Surat Jalan</label>
                    <select name="jenis_surat_jalan" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="Penambahan" {{ $suratJalan->jenis_surat_jalan == 'Penambahan' ? 'selected' : '' }}>Penambahan</option>
                        <option value="Perbaikan" {{ $suratJalan->jenis_surat_jalan == 'Perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                    </select>
                </div>

                <div class="col-md-4 mb-3">
                    <label>Tanggal Input</label>
                    <input type="date" name="tanggal_input" class="form-control" 
                           value="{{ old('tanggal_input', $suratJalan->tanggal_input) }}" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label>BAST</label>
                    <input type="text" name="bast" class="form-control" 
                           value="{{ old('bast', $suratJalan->bast) }}">
                </div>

                {{-- Upload File --}}
                <div class="col-md-6 mb-3">
                    <label>Upload File (jika ada)</label>
                    <input type="file" name="file" class="form-control">

                    @if ($suratJalan->file)
                        <small class="text-success">
                            File saat ini: 
                            <a href="{{ asset('storage/' . $suratJalan->file) }}" target="_blank">Lihat / Download</a>
                        </small>
                    @endif
                </div>

                <div class="col-md-12 mb-3">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ old('keterangan', $suratJalan->keterangan) }}</textarea>
                </div>
            </div>

            <div class="text-end">
                <a href="{{ route('surat-jalan.index') }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#kategori').select2({
            placeholder: "Cari dan pilih kategori...",
            allowClear: true,
            width: '100%'
        });
    });
</script>
@endpush
