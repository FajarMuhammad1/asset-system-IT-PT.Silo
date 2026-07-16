<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak_Laporan_Nilai_Aset</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #fff; color: #000; font-size: 12px; }
        .line-db { border-top: 3px double #000; margin-top: 5px; margin-bottom: 20px; }
        @media print {
            .btn-print { display: none; }
            body { margin: 10mm; }
        }
    </style>
</head>
<body>

    <div class="container-fluid mt-3">
        {{-- KOP SURAT --}}
        <div class="text-center">
            <h3 class="font-weight-bold mb-1 text-uppercase text-dark">LAPORAN REKAPITULASI NILAI ASET DOKUMEN</h3>
            <p class="mb-0">Sistem Informasi Manajemen Inventaris & Manajemen Aset Perusahaan</p>
            @if($startDate && $endDate)
                <p class="small text-muted mb-0">Periode Perolehan Beli: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</strong></p>
            @endif
            @if($status)
                <p class="small text-muted mb-0">Filter Status Kondisi: <span class="badge badge-dark">{{ $status }}</span></p>
            @endif
        </div>
        <div class="line-db"></div>

        <div class="text-right mb-3 btn-print">
            <button onclick="window.print()" class="btn btn-dark btn-sm"><i class="fas fa-print"></i> Klik Cetak / Save PDF</button>
        </div>

        {{-- TABEL UTAMA --}}
        <table class="table table-bordered table-sm text-dark">
            <thead class="thead-light text-center font-weight-bold">
                <tr>
                    <th width="40">No</th>
                    <th>Kode / No Aset</th>
                    <th>Nama Aset / Barang</th>
                    <th>Merek / Spesifikasi</th>
                    <th>Tanggal Beli</th>
                    <th>Status</th>
                    <th class="text-right" width="200">Harga Perolehan</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @forelse($assets as $asset)
                    <tr>
                        <td class="text-center">{{ $no++ }}</td>
                        <td class="font-weight-bold">{{ $asset->kode_barang ?? $asset->no_aset }}</td>
                        <td>{{ $asset->nama_barang }}</td>
                        <td>{{ $asset->merek ?? '-' }}</td>
                        <td class="text-center">{{ $asset->tgl_beli ? \Carbon\Carbon::parse($asset->tgl_beli)->format('d/m/Y') : '-' }}</td>
                        <td class="text-center">{{ $asset->status ?? 'Aktif' }}</td>
                        <td class="text-right font-weight-bold">Rp {{ number_format($asset->harga_beli, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">Tidak ada aset terdaftar.</td>
                    </tr>
                @endforelse
                <tr class="font-weight-bold bg-light" style="font-size: 14px;">
                    <td colspan="6" class="text-right">TOTAL KAPITALISASI NILAI BUNDLE ASET:</td>
                    <td class="text-right text-dark">Rp {{ number_format($totalValue, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        {{-- TANDA TANGAN DOKUMEN --}}
        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p class="mb-5">Dibuat Oleh,<br><strong>Manajemen Admin Logistik</strong></p>
                <div class="mt-5"></div>
                <hr style="border-top: 1px solid #000; width: 80%;">
                <p class="small text-muted" style="margin-top: -15px;">Dicetak otomatis pada: {{ \Carbon\Carbon::now()->format('d-m-Y H:i') }} WIB</p>
            </div>
        </div>
    </div>

    <script>
        // Otomatis trigger print dialog saat halaman selesai dibuka
        window.onload = function() { window.print(); }
    </script>
</body>
</html>