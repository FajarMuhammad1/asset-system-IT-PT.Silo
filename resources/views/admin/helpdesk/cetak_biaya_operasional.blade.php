<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Biaya Operasional</title>
    <style>
        @page {
            margin: 25px 35px;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9.5pt; 
            color: #333; 
            line-height: 1.4; 
        }
        
        /* HEADER TABLE (KOP SURAT PPI) */
        .header-table { 
            width: 100%; 
            border-bottom: 2px solid #000; 
            padding-bottom: 8px; 
            margin-bottom: 20px; 
            table-layout: fixed;
        }
        .header-logo { 
            width: 15%; 
            vertical-align: middle; 
            text-align: left;
        }
        .header-text { 
            width: 70%; 
            vertical-align: middle; 
            text-align: center; 
        }
        .header-dummy {
            width: 15%;
        }

        .header-text h2 { 
            margin: 0; 
            font-size: 15pt; 
            font-weight: bold; 
            color: #1a4d80; 
            text-transform: uppercase; 
        }
        .header-text p { 
            margin: 2px 0; 
            font-size: 9pt; 
            color: #555; 
        }

        /* DOCUMENT TITLE */
        .doc-title { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .doc-title h3 { 
            margin: 0; 
            font-size: 13pt; 
            text-transform: uppercase; 
            text-decoration: underline; 
            color: #2c3e50;
        }
        .doc-title p {
            margin: 4px 0 0 0;
            font-size: 9pt;
            color: #555;
        }

        /* DATA TABLE */
        .table-data { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .table-data th, .table-data td { 
            border: 1px solid #ccc; 
            padding: 7px 8px; 
            vertical-align: middle; 
        }
        .table-data th { 
            background-color: #1a4d80; 
            color: #ffffff; 
            font-weight: bold; 
            font-size: 8.5pt; 
            text-transform: uppercase;
            text-align: center;
        }
        .table-data tr:nth-child(even) {
            background-color: #f8fbfd;
        }
        .table-data tfoot td {
            background-color: #eef4f9;
            font-weight: bold;
            border: 1px solid #bbb;
            color: #1a4d80;
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

        /* FOOTER */
        footer { 
            position: fixed; 
            bottom: -15px; 
            left: 0px; 
            right: 0px; 
            height: 25px; 
            font-size: 8pt; 
            color: #888; 
            border-top: 1px solid #ddd; 
            padding-top: 4px; 
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    @php
        $logoPath = null;
        if (file_exists(public_path('image/images.png'))) {
            $logoPath = public_path('image/images.png');
        } elseif (file_exists(public_path('image/logo.png'))) {
            $logoPath = public_path('image/logo.png');
        } elseif (file_exists(public_path('img/logo-silo.png'))) {
            $logoPath = public_path('img/logo-silo.png');
        } elseif (file_exists(public_path('img/logo.png'))) {
            $logoPath = public_path('img/logo.png');
        }

        $logoData = $logoPath ? base64_encode(file_get_contents($logoPath)) : null;
    @endphp

    <!-- KOP SURAT (SESUAI STYLE PPI) -->
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if($logoData)
                    <img src="data:image/png;base64,{{ $logoData }}" alt="Logo SILO" style="width: 75px; height: auto;">
                @elseif($logoPath)
                    <img src="{{ $logoPath }}" alt="Logo SILO" style="width: 75px; height: auto;">
                @else
                    <strong style="font-size: 14pt; color: #1a4d80;">PT. SILO</strong>
                @endif
            </td>
            
            <td class="header-text">
                <h2>PT. SEBUKU IRON LATERITIC ORES</h2>
                <p>Departemen IT Support & Communication System</p>
            </td>

            <td class="header-dummy"></td>
        </tr>
    </table>

    <!-- JUDUL DOKUMEN -->
    <div class="doc-title">
        <h3>LAPORAN BIAYA OPERASIONAL TEKNISI</h3>
        <p>Periode: <strong>{{ $namaBulan }} {{ $tahun }}</strong></p>
    </div>

    <!-- DATA REKAP BIAYA OPERASIONAL -->
    <table class="table-data">
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
                    <td colspan="6" class="text-center" style="padding: 15px;">Tidak ada data biaya operasional pada periode ini.</td>
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

    <!-- KETERANGAN SISTEM -->
    <div style="margin-top: 15px; font-size: 8.5pt; color: #666;">
        <p><em>* Dokumen ini dibuat dan dikelola secara digital melalui Sistem IT Support Asset PT. Sebuku Iron Lateritic Ores.</em></p>
    </div>

    <!-- FOOTER HALAMAN -->
    <footer>
        <table width="100%">
            <tr>
                <td align="left" width="60%"><i>Dicetak pada: {{ now()->format('d-m-Y H:i') }} WIB</i></td>
                <td align="right" width="40%">Hal <span class="page-number"></span></td>
            </tr>
        </table>
    </footer>

</body>
</html>