@extends('layouts.app')
@section('content')

<h3 class="h3 mb-4 text-gray-800">
    <i class="fas fa-user-edit mr-2"></i> {{ $title }}
</h3>

<div class="card">
    <div class="card-body">
        <form action="{{ route('team.update', $team->id) }}" method="POST">
            @csrf
            @method('PUT')  <div class="form-group">
                <label>NIK</label>
                <input type="text" name="nik" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik', $team->nik) }}">
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $team->nama) }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $team->email) }}">
            </div>

            <div class="form-group">
                <label>Nomor WhatsApp (Aktif)</label>
                <input type="text" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $team->no_hp) }}" placeholder="Contoh: 081234567890">
                @error('no_hp')
                <small class="text-danger">{{ $message }}</small>
                @enderror
                <small class="text-muted">Nomor WA ini akan digunakan untuk menerima notifikasi penugasan tiket Helpdesk.</small>
            </div>
            <div class="form-group">
                <label>Jabatan</label>
                <select name="jabatan" class="form-control">
                    <option {{ $team->jabatan == 'Head IT' ? 'selected' : '' }}>Head IT</option>
                    <option {{ $team->jabatan == 'IT Support' ? 'selected' : '' }}>IT Support</option>
                    <option {{ $team->jabatan == 'Teknisi' ? 'selected' : '' }}>Teknisi</option>
                </select>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option {{ $team->role == 'SuperAdmin' ? 'selected' : '' }}>SuperAdmin</option>
                    <option {{ $team->role == 'Admin' ? 'selected' : '' }}>Admin</option>
                    <option {{ $team->role == 'Staff' ? 'selected' : '' }}>Staff</option>
                </select>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option {{ $team->status == 'Aktif' ? 'selected' : '' }}>Aktif</option>
                    <option {{ $team->status == 'Off' ? 'selected' : '' }}>Off</option>
                    <option {{ $team->status == 'Izin' ? 'selected' : '' }}>Izin</option>
                    <option {{ $team->status == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                    <option {{ $team->status == 'Cuti' ? 'selected' : '' }}>Cuti</option>
                    <option {{ $team->status == 'Resign' ? 'selected' : '' }}>Resign</option>
                    <option {{ $team->status == 'Nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mt-3">
                <button type="submit" class="btn btn-warning mr-3">Update</button>
                <a href="{{ route('team') }}" class="btn btn-secondary mr-3">Kembali</a>
            </div>

        </form>
        </div>
</div>

@endsection