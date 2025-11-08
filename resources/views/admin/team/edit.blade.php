@extends('layouts.app')
@section('content')

<h3 class="h3 mb-4 text-gray-800">
    <i class="fas fa-user-edit mr-2"></i> {{ $title }}
</h3>

<div class="card">
    <div class="card-body">
        <!-- FORM EDIT MULAI -->
        <form action="{{ route('team.update', $team->id) }}" method="POST">
            @csrf
            @method('PUT')  <!-- ✅ WAJIB ditambah agar request menjadi PUT -->

            <div class="form-group">
                <label>NIK</label>
                <input type="text" name="nik" class="form-control" value="{{ $team->nik }}">
            </div>

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" value="{{ $team->nama }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $team->email }}">
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
        <!-- FORM EDIT SELESAI -->
    </div>
</div>

@endsection
