<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cetak Laporan Nilai Aset' }}</title>
    <!-- Menggunakan Bootstrap 4 CDN untuk menyelaraskan dengan layout SB Admin -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #fff; color: #000; font-family: Arial, sans-serif; padding: 30px; }
        .table th { vertical-align: middle !important; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Header Laporan -->
    <div class="text-center mb-4">
        <h2 class="font-weight-bold text-uppercase">Laporan Nilai Aset Inventaris IT</h2>
        <h5 class="text-muted">Sistem Manajemen Aset IT Support</h5>
        @if($startDate && $endDate)
            <p class="small">Periode Penilaian: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</strong></p>
        @endif
        <hr style="border-top: 2px solid #000;">
    </div>

    <!-- Ringkasan Data Finansial -->
    <div class="row mb-4">
        <div class="col-4">
            <div class="border p-2 text-center rounded">
                <small class="text-muted d-block text-uppercase font-weight-bold">Total Kuantitas</small>
                <span class="h5 font-weight-bold">{{ number_format($totalQty, 0, ',', '.') }} Unit</span>
            </div>
        </div>
        <div class="col-4">
            <div class="border p-2 text-center rounded">
                <small class="text-muted d-block text-uppercase font-weight-bold">Total Nilai Perolehan</small>
                <span class="h5 font-weight-bold text-secondary">Rp {{ number_format($totalNilaiAwal, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="col-4">
            <div class="border p-2 text-center rounded">
                <small class="text-muted d-block text-uppercase font-weight-bold">Total Nilai Buku Saat Ini</small>
                <span class="h5 font-weight-bold text-success">Rp {{ number_format($totalNilaiSekarang, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Tabel Data Utama -->
    <table class="table table-bordered table-sm">
        <thead class="thead-light text-center">
            <tr>
                <th width="5%">No</th>
                <th>Kode/SN Aset</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Tgl Penilaian</th>
                <th>Status Fisik</th>
                <th>Nilai Perolehan (Rp)</th>
                <th>Nilai Buku (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $asset)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td>{{ $asset->barangMasuk->kode_masuk ?? $asset->barangMasuk->no_sj ?? '-' }}</td>
                <td class="font-weight-bold">{{ $asset->barangMasuk->nama_barang ?? 'Barang Terhapus' }}</td>
                <td class="text-center">{{ isset($asset->barangMasuk->kategori) ? strtoupper($asset->barangMasuk->kategori) : '-' }}</td>
                <td class="text-center">{{ $asset->tanggal_penilaian ? $asset->tanggal_penilaian->format('d-m-Y') : '-' }}</td>
                <td class="text-center text-uppercase"><small>{{ $asset->barangMasuk->status ?? '-' }}</small></td>
                <td class="text-right">{{ number_format($asset->nilai_awal, 0, ',', '.') }}</td>
                <td class="text-right font-weight-bold text-success">{{ number_format($asset->nilai_sekarang, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-3">Tidak ada data aset terfilter.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="font-weight-bold bg-light">
                <td colspan="6" class="text-right">TOTAL KESELURUHAN:</td>
                <td class="text-right text-secondary">Rp {{ number_format($totalNilaiAwal, 0, ',', '.') }}</td>
                <td class="text-right text-success">Rp {{ number_format($totalNilaiSekarang, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan / Verifikasi Dokumen (Opsional untuk Laporan Resmi) -->
    <div class="row mt-5 pt-4">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p>Banjarmasin, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p class="mb-5 pb-3">Petugas IT Support,</p>
            <p class="font-weight-bold text-underline">_______________________</p>
        </div>
    </div>

    <!-- Script Otomatis Mengaktifkan Jendela Cetak Browser -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>