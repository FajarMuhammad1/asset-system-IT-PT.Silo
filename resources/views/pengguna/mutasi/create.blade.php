@extends('layouts.app')

@section('title', $title)

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit mr-2 text-primary"></i>{{ $title }}</h1>
    <a href="{{ route('pengguna.mutasi.index') }}" class="btn btn-secondary btn-icon-split shadow-sm">
        <span class="icon text-white-50"><i class="fas fa-arrow-left"></i></span>
        <span class="text">Kembali</span>
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-paper-plane mr-1"></i> Form Permintaan Mutasi Aset</h6>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pengguna.mutasi.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="barang_masuk_id" class="font-weight-bold text-dark">
                            Pilih Aset yang Ingin Dimutasi <span class="text-danger">*</span>
                        </label>
                        <select name="barang_masuk_id" id="barang_masuk_id" class="form-control select2" required>
                            <option value="">-- Pilih Aset --</option>
                            @foreach($myAssets as $asset)
                                <option value="{{ $asset->id }}" {{ old('barang_masuk_id') == $asset->id ? 'selected' : '' }}>
                                    [{{ $asset->kode_asset }}] {{ $asset->masterBarang->nama_barang ?? 'Aset' }} (SN: {{ $asset->serial_number ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Aset yang saat ini dipegang oleh Anda atau terdaftar di sistem.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="user_tujuan_id" class="font-weight-bold text-dark">
                            Penerima Baru / User Tujuan <span class="text-danger">*</span>
                        </label>
                        <select name="user_tujuan_id" id="user_tujuan_id" class="form-control select2" required>
                            <option value="">-- Pilih Karyawan Tujuan --</option>
                            @foreach($users as $usr)
                                <option value="{{ $usr->id }}" {{ old('user_tujuan_id') == $usr->id ? 'selected' : '' }}>
                                    {{ $usr->nama }} ({{ $usr->jabatan ?? 'Staf' }} - {{ $usr->departemen ?? 'Umum' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="lokasi_baru" class="font-weight-bold text-dark">Lokasi Baru (Opsional)</label>
                        <input type="text" name="lokasi_baru" id="lokasi_baru" class="form-control" placeholder="Contoh: Ruang IT Lt. 2, Kantor Cabang B" value="{{ old('lokasi_baru') }}">
                    </div>

                    <div class="form-group mb-4">
                        <label for="keterangan" class="font-weight-bold text-dark">Alasan Mutasi <span class="text-danger">*</span></label>
                        <textarea name="keterangan" id="keterangan" rows="4" class="form-control" placeholder="Jelaskan alasan pemindahan barang atau penyerahan ke karyawan baru..." required>{{ old('keterangan') }}</textarea>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-1"></i> 
                        <strong>Alur Proses:</strong> Pengajuan ini akan dikirimkan ke <strong>Manager / Atasan</strong> untuk direview. Setelah disetujui, Admin IT akan memproses mutasi barang dan menerbitkan <strong>BAST Digital</strong> untuk ditandatangani oleh penerima baru.
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary px-4 btn-lg">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan Mutasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
