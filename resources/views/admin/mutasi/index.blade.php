@extends('layouts.app') 

@section('content')
<div class="container-fluid px-4">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-exchange-alt mr-2"></i> {{ $title }}</h1>
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

    @if ($errors->any())
        <div class="alert alert-danger shadow-sm">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Hanya Admin yang bisa melihat Form Input Mutasi --}}
    @if(strtolower(Auth::user()->role) === 'admin')
    <div class="card mb-4 shadow border-left-primary">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-edit mr-1"></i> Form Pengajuan Mutasi Perangkat IT</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('mutasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="barang_masuk_id" class="font-weight-bold text-dark">Pilih Perangkat / Aset IT <span class="text-danger">*</span></label>
                            <select class="form-control select2-enable" id="barang_masuk_id" name="barang_masuk_id" required>
                                <option value="" selected disabled>-- Pilih Perangkat --</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" 
                                            data-user-asal="{{ $asset->userPemegang ? $asset->userPemegang->nama : 'IT Storage / Gudang' }}">
                                        [{{ $asset->masterBarang->kode_barang ?? 'N/A' }}] - {{ $asset->masterBarang->nama_barang }} (S/N: {{ $asset->no_serial ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label for="user_asal" class="font-weight-bold text-dark">Penanggung Jawab / User Asal</label>
                            <input type="text" class="form-control bg-light" id="user_asal" readonly placeholder="Akan terisi otomatis setelah memilih aset">
                        </div>

                        <div class="form-group mb-3">
                            <label for="user_tujuan_id" class="font-weight-bold text-dark">Karyawan Penerima Baru (Tujuan) <span class="text-danger">*</span></label>
                            <select class="form-control select2-enable" id="user_tujuan_id" name="user_tujuan_id" required>
                                <option value="" selected disabled>-- Pilih Karyawan Penerima --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->nama }} - [{{ $user->jabatan ?? 'No Jabatan' }}]</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="lokasi_baru" class="font-weight-bold text-dark">Lokasi Penempatan Baru <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="lokasi_baru" name="lokasi_baru" placeholder="Contoh: Ruang Meeting Lt. 2 / HO Purchasing" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="tanggal_mutasi" class="font-weight-bold text-dark">Tanggal Mutasi <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_mutasi" name="tanggal_mutasi" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="file_bast" class="font-weight-bold text-dark">Upload Dokumen BAST <small class="text-muted">(PDF/JPG/PNG, Maks 2MB)</small></label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file_bast" name="file_bast" accept=".pdf,.jpg,.jpeg,.png">
                                <label class="custom-file-label" for="file_bast">Pilih file opsional...</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="form-group mb-3">
                            <label for="keterangan" class="font-weight-bold text-dark">Keterangan / Alasan Mutasi <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Tuliskan alasan pemindahan perangkat (misal: Rotasi jabatan kerja / Penggantian unit rusak)..." required></textarea>
                        </div>
                        <div class="text-right mt-4">
                            <button type="reset" class="btn btn-secondary mr-2"><i class="fas fa-undo mr-1"></i> Reset</button>
                            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-save mr-1"></i> Proses Mutasi Aset</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif

    <div class="card mb-4 shadow">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-history mr-1"></i> Log Historis Mutasi Perangkat</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover mb-0" id="datatablesSimple" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr class="text-center align-middle">
                            <th width="5%" class="border-top-0">No</th>
                            <th class="border-top-0">Info Perangkat IT</th>
                            <th class="border-top-0">Karyawan Penyerah (Asal)</th>
                            <th class="border-top-0">Karyawan Penerima (Tujuan)</th>
                            <th class="border-top-0">Lokasi Baru</th>
                            <th class="border-top-0">Tanggal</th>
                            <th class="border-top-0">Keterangan</th>
                            <th width="8%" class="border-top-0">BAST</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800">
                        @forelse($riwayatMutasi as $index => $mutasi)
                        <tr class="align-middle">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $mutasi->barangMasuk->masterBarang->nama_barang ?? 'N/A' }}</strong><br>
                                <small class="text-muted">Code: <span class="text-primary font-weight-bold">{{ $mutasi->barangMasuk->masterBarang->kode_barang ?? '-' }}</span></small><br>
                                <small class="text-muted">S/N: {{ $mutasi->barangMasuk->no_serial ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge badge-secondary p-2 shadow-sm w-100 text-left">
                                    <i class="fas fa-user-minus mr-1"></i> {{ $mutasi->userAsal ? $mutasi->userAsal->nama : 'IT Storage / Gudang' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-success p-2 shadow-sm w-100 text-left">
                                    <i class="fas fa-user-plus mr-1"></i> {{ $mutasi->userTujuan->nama ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $mutasi->lokasi_baru ?? '-' }}</td>
                            <td class="text-center">{{ date('d M Y', strtotime($mutasi->tanggal_mutasi)) }}</td>
                            <td><small>{{ $mutasi->keterangan }}</small></td>
                            <td class="text-center">
                                @if($mutasi->file_bast)
                                    <a href="{{ asset('storage/' . $mutasi->file_bast) }}" target="_blank" class="btn btn-sm btn-outline-info font-weight-bold shadow-sm" title="Lihat BAST">
                                        <i class="fas fa-download mr-1"></i> BAST
                                    </a>
                                @else
                                    <span class="text-muted small"><em>-</em></span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-exchange-alt fa-3x mb-3 text-gray-300"></i><br>
                                Belum ada riwayat transaksi mutasi aset yang tercatat.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        // 1. Inisialisasi Select2
        if ($('.select2-enable').length) {
            $('.select2-enable').select2({
                theme: 'bootstrap4'
            });
        }

        // 2. Tampilkan nama file upload BAST di label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // 3. Logic Auto-Fill User Asal saat pilih Aset
        $('#barang_masuk_id').on('change', function () {
            // Mengambil attribute 'data-user-asal' dari option yang dipilih (Select2 safe via jQuery)
            const userAsal = $(this).find(':selected').data('user-asal');
            
            // Memasukkan nama user ke dalam input read-only
            $('#user_asal').val(userAsal);
        });
    });
</script>
@endpush