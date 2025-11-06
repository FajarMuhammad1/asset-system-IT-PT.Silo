@extends('layouts.app')
@section('content')

<h3 class="h3 mb-4 text-gray-800">
    <i class="fas fa-plus mr-2"></i> {{ $title }}
</h3>

<div class="card">
    <div class="card-body">
        <form action="{{ route('team.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>NIK</label>
                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}">
                @error('nik')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}">
                @error('nama')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Jabatan</label>
                <select name="jabatan" class="form-control @error('jabatan') is-invalid @enderror">
                    <option value="">-- Pilih Jabatan --</option>
                    <option>Head IT</option>
                    <option>IT Support</option>
                    <option>Teknisi</option>
                </select>
                @error('jabatan')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control @error('role') is-invalid @enderror">
                    <option value="">-- Pilih Role --</option>
                    <option>SuperAdmin</option>
                    <option>Admin</option>
                    <option>Staff</option>
                </select>
                @error('role')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control @error('status') is-invalid @enderror">
                    <option value="Aktif">Aktif</option>
                    <option value="Off">Off</option>
                    <option value="Izin">Izin</option>
                    <option value="Sakit">Sakit</option>
                    <option value="Cuti">Cuti</option>
                    <option value="Resign">Resign</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
                @error('status')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                @error('password')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                @error('password_confirmation')
                <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mt-3">
                <button class="btn btn-primary mr-3">Simpan</button>
                <a href="{{ route('team.index') }}" class="btn btn-secondary mr-3">Kembali</a>
            </div>

        </form>
    </div>
</div>

@endsection
