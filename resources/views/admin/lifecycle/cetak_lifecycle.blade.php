<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>Laporan Siklus Hidup Aset (Lifecycle)</h2>
        <p>Sistem Inventaris IT Support</p>
    </div>

    <table class="info-table">
        <tr>
            <td>Kode Aset</td>
            <td>: {{ $asset->kode_asset }}</td>
            <td>Kategori</td>
            <td>: {{ $asset->masterBarang->kategori ?? '-' }}</td>
        </tr>
        <tr>
            <td>Nama Barang</td>
            <td>: {{ $asset->masterBarang->nama_barang ?? 'Aset Tidak Diketahui' }}</td>
            <td>Merek/Tipe</td>
            <td>: {{ $asset->masterBarang->merk ?? '-' }} / {{ $asset->masterBarang->tipe ?? '-' }}</td>
        </tr>
        <tr>
            <td>Serial Number</td>
            <td>: {{ $asset->serial_number ?? '-' }}</td>
            <td>Tanggal Masuk</td>
            <td>: {{ $asset->tanggal_masuk ? \Carbon\Carbon::parse($asset->tanggal_masuk)->format('d F Y') : '-' }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Tanggal</th>
                <th width="20%">Status Riwayat</th>
                <th width="15%">Pihak Terkait</th>
                <th width="45%">Keterangan & Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($timeline as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item['tanggal'])->format('d/m/Y') }}</td>
                <td><strong>{{ $item['status'] }}</strong></td>
                <td>{{ $item['oleh'] ?? '-' }}</td>
                <td>{{ $item['keterangan'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Belum ada riwayat tercatat untuk aset ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; padding-right: 20px;">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</p>
    </div>

</body>
</html>