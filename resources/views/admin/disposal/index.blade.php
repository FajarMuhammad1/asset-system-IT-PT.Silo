@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-trash-alt mr-2"></i> Manajemen Disposal (Pemusnahan Aset)</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-1"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="font-weight-bold"><i class="fas fa-ban mr-1"></i> Pengajuan Gagal:</h6>
            <ul class="mb-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        {{-- 1. FORM PENGAJUAN (HANYA MUNCUL JIKA YANG LOGIN ADALAH ADMIN) --}}
        @if(Auth::check() && Auth::user()->role === 'Admin')
        <div class="col-12 mb-4">
            <div class="card shadow border-left-primary">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-plus mr-1"></i> Ajukan Pemusnahan Aset Baru</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('disposal.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-row">
                            {{-- Pilih Aset --}}
                            <div class="form-group col-md-4">
                                <label class="font-weight-bold text-gray-800">Pilih Perangkat / Aset</label>
                                <select name="barang_masuk_id" class="form-control select2 @error('barang_masuk_id') is-invalid @enderror" required style="width: 100%;">
                                    <option value="">-- Pilih Aset Aktif --</option>
                                    @foreach($availableAssets as $asset)
                                        <option value="{{ $asset->id }}" {{ old('barang_masuk_id') == $asset->id ? 'selected' : '' }}>
                                            {{ $asset->kode_asset ?? 'No-Kode' }} - {{ $asset->masterBarang->nama_barang ?? 'Unknown' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('barang_masuk_id')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Alasan Disposal (DIUBAH JADI DROPDOWN) --}}
                            <div class="form-group col-md-4">
                                <label class="font-weight-bold text-gray-800">Alasan Pemusnahan / Status</label>
                                <select name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" required>
                                    <option value="">-- Pilih Alasan --</option>
                                    <option value="Rusak Total" {{ old('reason') == 'Rusak Total' ? 'selected' : '' }}>Rusak Total</option>
                                    <option value="Dijual / Dilelang" {{ old('reason') == 'Dijual / Dilelang' ? 'selected' : '' }}>Dijual / Dilelang</option>
                                    <option value="Dihibahkan" {{ old('reason') == 'Dihibahkan' ? 'selected' : '' }}>Dihibahkan</option>
                                    <option value="Hilang" {{ old('reason') == 'Hilang' ? 'selected' : '' }}>Hilang (Dicuri / Tidak Ditemukan)</option>
                                </select>
                                @error('reason')
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            {{-- Upload Bukti Wiping (DIBERI ID UNTUK JAVASCRIPT) --}}
                            <div class="form-group col-md-4" id="wipingProofDiv">
                                <label class="font-weight-bold text-gray-800">Bukti Data Wiping (PDF/JPG/PNG)</label>
                                <div class="custom-file">
                                    <input type="file" name="data_wiping_proof" class="custom-file-input @error('data_wiping_proof') is-invalid @enderror" id="customFile" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <label class="custom-file-label" for="customFile">Pilih file...</label>
                                    @error('data_wiping_proof')
                                        <span class="invalid-feedback" role="alert" style="display: block;"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                                <small class="text-muted" id="wipingProofHelp">Wajib dilampirkan kecuali status barang Hilang.</small>
                            </div>
                        </div>
                        
                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="fas fa-paper-plane mr-1"></i> Kirim Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        {{-- 2. TABEL DAFTAR DISPOSAL (DILIHAT OLEH ADMIN & SUPER ADMIN) --}}
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-light d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary"><i class="fas fa-history mr-1"></i> Riwayat & Status Pengajuan</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0" width="100%" cellspacing="0">
                            <thead class="bg-gray-100 text-gray-800">
                                <tr>
                                    <th class="border-top-0 pl-4">Kode Aset</th>
                                    <th class="border-top-0">Nama Perangkat</th>
                                    <th class="border-top-0">Alasan</th>
                                    <th class="border-top-0">Diajukan Oleh</th>
                                    <th class="border-top-0 text-center">Bukti Hapus Data</th>
                                    <th class="border-top-0 text-center">Status</th>
                                    <th class="border-top-0 text-center pr-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @forelse($disposals as $item)
                                <tr>
                                    <td class="pl-4 font-weight-bold text-primary align-middle">
                                        {{ $item->barangMasuk->kode_asset ?? '-' }}
                                    </td>
                                    <td class="align-middle">
                                        {{ $item->barangMasuk->masterBarang->nama_barang ?? 'Data Hilang' }}
                                    </td>
                                    <td class="align-middle">
                                        @if($item->reason == 'Hilang')
                                            <span class="badge badge-danger px-2 py-1"><i class="fas fa-search mr-1"></i> Hilang</span>
                                        @else
                                            {{ $item->reason }}
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <span class="font-weight-bold">{{ $item->pengaju->nama ?? 'Unknown' }}</span><br>
                                        <small class="text-muted"><i class="far fa-calendar-alt mr-1"></i>{{ $item->created_at->format('d M Y') }}</small>
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($item->data_wiping_proof)
                                            <a href="{{ asset('storage/' . $item->data_wiping_proof) }}" target="_blank" class="btn btn-xs btn-outline-info shadow-sm py-1 font-weight-bold">
                                                <i class="fas fa-eye mr-1"></i> Lihat File
                                            </a>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($item->status == 'Pending')
                                            <span class="badge badge-warning p-2 font-weight-bold shadow-sm" style="min-width: 90px;"><i class="fas fa-clock mr-1"></i>Pending</span>
                                        @elseif($item->status == 'Approved')
                                            <span class="badge badge-success p-2 font-weight-bold shadow-sm" style="min-width: 90px;"><i class="fas fa-check-circle mr-1"></i>Approved</span>
                                        @else
                                            <span class="badge badge-danger p-2 font-weight-bold shadow-sm" style="min-width: 90px;"><i class="fas fa-times-circle mr-1"></i>Rejected</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle pr-4">
                                        {{-- LOGIKA AKSI APPROVAL KHUSUS SUPER ADMIN --}}
                                        @if(Auth::check() && Auth::user()->role === 'SuperAdmin' && $item->status === 'Pending')
                                            <div class="d-flex justify-content-center align-items-center">
                                                <form action="{{ route('superadmin.disposal.approve', $item->id) }}" method="POST" class="mr-2">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success font-weight-bold shadow-sm" onclick="return confirm('Yakin ingin MENYETUJUI pemusnahan aset ini?')">
                                                        <i class="fas fa-check mr-1"></i> Approve
                                                    </button>
                                                </form>
                                                <form action="{{ route('superadmin.disposal.reject', $item->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger font-weight-bold shadow-sm" onclick="return confirm('Yakin ingin MENOLAK pemusnahan aset ini?')">
                                                        <i class="fas fa-times mr-1"></i> Reject
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($item->status === 'Approved')
                                            <span class="text-success small font-weight-bold d-block mb-1"><i class="fas fa-dumpster mr-1"></i> Selesai Dimusnahkan</span>
                                            
                                            {{-- TOMBOL CETAK DITAMBAHKAN DI SINI --}}
                                            <a href="{{ route('disposal.print', $item->id) }}" target="_blank" class="btn btn-sm btn-secondary shadow-sm mt-1" title="Cetak Berita Acara">
                                                <i class="fas fa-print"></i> Cetak BAPA
                                            </a>
                                            
                                        @elseif($item->status === 'Rejected')
                                            <span class="text-danger small font-weight-bold"><i class="fas fa-ban mr-1"></i> Ditolak SuperAdmin</span>
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="fas fa-folder-open fa-3x mb-3 text-gray-300"></i><br>
                                        Belum ada data pengajuan pemusnahan aset.
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
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // 1. Inisialisasi Select2 agar combobox pilihan Aset bisa dicari (Searchable)
        if ($('.select2').length) {
            $('.select2').select2({
                placeholder: "-- Pilih Aset Aktif --",
                allowClear: true,
                theme: 'bootstrap4' 
            });
        }

        // 2. Script agar nama file yang diupload otomatis tertera di label Bootstrap Custom File Input
        $('#customFile').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        // 3. LOGIKA UNTUK MENYEMBUNYIKAN UPLOAD BUKTI JIKA ALASAN = HILANG
        $('#reason').on('change', function() {
            var selectedReason = $(this).val();
            if(selectedReason === 'Hilang') {
                // Sembunyikan div upload dan hapus atribut required
                $('#wipingProofDiv').hide();
                $('#customFile').removeAttr('required');
            } else {
                // Tampilkan kembali dan jadikan required
                $('#wipingProofDiv').show();
                $('#customFile').attr('required', 'required');
            }
        });

        // Jalankan saat halaman pertama kali diload (mengakomodasi nilai old('reason') jika validasi error)
        $('#reason').trigger('change');
    });
</script>
@endpush