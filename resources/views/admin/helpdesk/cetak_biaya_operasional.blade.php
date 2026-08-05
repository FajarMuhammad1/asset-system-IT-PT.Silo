<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Biaya Operasional</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.4;
        }
        /* KOP SURAT / HEADER STYLE */
        .header-table {
            width: 100%;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .logo-container {
            width: 15%;
            text-align: center;
        }
        .logo-container img {
            width: 80px;
        }
        .company-info {
            width: 85%;
            text-align: left;
            padding-left: 10px;
        }
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
            color: #000;
        }
        .dept-name {
            font-size: 12px;
            margin: 3px 0 0 0;
            color: #333;
        }
        
        /* JUDUL DOKUMEN */
        .doc-title-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            text-transform: uppercase;
        }
        .doc-no {
            font-size: 11px;
            margin: 4px 0 0 0;
        }
        
        /* TABEL DATA */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table, table.data-table th, table.data-table td {
            border: 1px solid #000;
        }
        table.data-table th {
            background-color: #f2f2f2;
            padding: 8px 6px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
        }
        table.data-table td {
            padding: 6px;
            vertical-align: top;
            font-size: 10px;
        }

        /* UTILITY CLASSES */
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-weight-bold {
            font-weight: bold;
        }

        /* FOOTER STYLE */
        .footer {
            margin-top: 30px;
            font-size: 9px;
            border-top: 1px solid #000;
            padding-top: 5px;
        }
        .footer table {
            width: 100%;
            border: none;
        }
        .footer td {
            border: none;
            padding: 0;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT -->
    <table class="header-table">
        <tr>
            <td class="logo-container">
                <!-- LOGO DIMUAT MENGGUNAKAN BASE64 AGAR TERBACA OLEH DOMPDF -->
                @php
                    $imagePath = public_path('img/logo.png');
                    $imageData = file_exists($imagePath) ? base64_encode(file_get_contents($imagePath)) : '';
                @endphp
                @if($imageData)
                    <img src="data:image/png;base64,{{ $imageData }}" alt="Logo SILO">
                @else
                    <span>[LOGO]</span>
                @endif
            </td>
            <td class="company-info">
                <h1 class="company-name">PT. SEBUKU IRON LATERITIC ORES</h1>
                <p class="dept-name">Departemen IT Support & Communication System</p>
            </td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title-container">
        <h2 class="doc-title">LAPORAN BIAYA OPERASIONAL TEKNISI</h2>
        <p class="doc-no">Periode: {{ $namaBulan }} {{ $tahun }}</p>
    </div>

    <!-- DATA REKAP BIAYA OPERASIONAL -->
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="12%">Tanggal</th>
                <th width="18%">No Tiket</th>
                <th width="20%">Teknisi</th>
                <th width="30%">Keterangan</th>
                <th width="15%">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dataRekap as $item)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-center">
                        {{ $item->tanggal_pemberian ? \Carbon\Carbon::parse($item->tanggal_pemberian)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="text-center">
                        {{ $item->ticket->no_tiket ?? '-' }}
                    </td>
                    <td>{{ $item->staff->name ?? $item->staff->nama ?? '-' }}</td>
                    <td>{{ $item->keterangan ?: '-' }}</td>
                    <td class="text-right">
                        Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data biaya operasional pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="text-right font-weight-bold">GRAND TOTAL</td>
                <td class="text-right font-weight-bold">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- FOOTER DOKUMEN -->
    <div class="footer">
        <table>
            <tr>
                <td>* Dokumen ini dibuat dan dikelola secara digital melalui Sistem IT Support Asset PT. Sebuku Iron Lateritic Ores.</td>
                <td class="text-right">Dicetak pada: {{ now()->format('d-m-Y H:i') }} WIB</td>
            </tr>
        </table>
    </div>

</body>
</html>