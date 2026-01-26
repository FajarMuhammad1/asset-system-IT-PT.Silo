<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Surat Jalan - PT Silo</title>
    <style>
        /* PENGATURAN HALAMAN */
        @page {
            margin: 20px 30px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }

        /* KOP SURAT / HEADER DENGAN LOGO */
        .header-table {
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-logo {
            width: 15%;
            vertical-align: middle;
        }

        .header-text {
            width: 85%;
            text-align: center;
            vertical-align: middle;
        }

        .header-text h2 {
            margin: 0;
            font-size: 16pt;
            font-weight: bold;
            color: #2c3e50; /* Warna Biru Tua Professional */
            text-transform: uppercase;
        }

        .header-text p {
            margin: 2px 0;
            font-size: 8pt;
            color: #555;
        }

        /* JUDUL DOKUMEN */
        .doc-title {
            text-align: center;
            margin-bottom: 20px;
        }
        .doc-title h3 {
            margin: 0;
            font-size: 14pt;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .doc-meta {
            font-size: 8pt;
            text-align: center;
            margin-top: 5px;
        }

        /* TABEL DATA UTAMA */
        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table-data th, .table-data td {
            border: 1px solid #444;
            padding: 6px 8px;
            vertical-align: middle;
        }

        .table-data th {
            background-color: #1a4d80; /* Corporate Blue PT Silo */
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            text-align: center;
        }

        .table-data tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        /* BADGES STATUS */
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7pt;
            font-weight: bold;
            text-transform: uppercase;
            display: inline-block;
        }
        .badge-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

        /* SUMMARY & TANDA TANGAN */
        .footer-section {
            width: 100%;
            margin-top: 30px;
        }
        
        .signature-table {
            width: 100%;
            border: none;
        }
        .signature-table td {
            border: none;
            text-align: center;
            vertical-align: top;
            width: 33%;
        }
        .sign-space {
            height: 60px;
        }
        .sign-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* UTILITY */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }

        /* FOOTER HALAMAN OTOMATIS */
        footer {
            position: fixed;
            bottom: -20px;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 8pt;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 5px;
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    {{-- KOP SURAT (HEADER) --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                {{-- 
                    TIPS LOGO DI PDF:
                    Gunakan public_path() agar DOMPDF bisa membaca file lokal.
                    Pastikan file ada di folder: public/images/logo-silo.png
                --}}
                <img src="{{ public_path('image/images.png') }}" alt="Logo Silo" style="width: 80px; height: auto;">
            </td>
            <td class="header-text,text-center">
                <h2>PT. SEBUKU IRON LATERITIC ORES</h2>
                
            </td>
        </tr>
    </table>

    {{-- JUDUL LAPORAN --}}
    <div class="doc-title">
        <h3>Rekapitulasi Surat Jalan</h3>
        <div class="doc-meta">
            Dicetak Oleh: {{ auth()->user()->name ?? 'Admin Sistem' }} | Tanggal: {{ \Carbon\Carbon::now()->format('d F Y') }}
        </div>
    </div>

    {{-- TABEL DATA --}}
    <table class="table-data">
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="15%">No. Surat Jalan</th>
                <th width="12%">No. PPI</th>
                <th width="12%">No. PO</th>
                <th width="10%">Tgl Input</th>
                <th width="10%">Jenis</th>
                <th width="7%">Jml</th>
                <th width="10%">Status BAST</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-bold text-uppercase">{{ $item->no_sj }}</td>
                <td class="text-center text-uppercase">{{ $item->no_ppi ?? '-' }}</td>
                <td class="text-center text-uppercase">{{ $item->no_po ?? '-' }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($item->tanggal_input)->format('d/m/Y') }}</td>
                <td class="text-center text-uppercase">{{ $item->jenis_surat_jalan }}</td>
                <td class="text-center">{{ $item->details_count }}</td>
                
                <td class="text-center">
                    @if($item->is_bast_submitted)
                        <span class="badge badge-success">Selesai</span>
                    @else
                        <span class="badge badge-danger">Pending</span>
                    @endif
                </td>
                
                <td style="font-size: 8pt;">{{ Str::limit($item->keterangan, 50) ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data surat jalan untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div style="margin-bottom: 20px; font-size: 9pt;">
        <strong>Total Dokumen:</strong> {{ $data->count() }} Surat Jalan
    </div>

    {{-- KOLOM TANDA TANGAN (SIGNATURE) --}}
    <div class="footer-section">
        <table class="signature-table">
            <tr>
                <td>
                    Dibuat Oleh,<br>
                    <small>Admin Logistik</small>
                    <div class="sign-space"></div>
                    <div class="sign-name">{{ auth()->user()->name ?? '(..................)' }}</div>
                </td>
                <td>
                    Diketahui Oleh,<br>
                    <small>Supervisor</small>
                    <div class="sign-space"></div>
                    <div class="sign-name">(..............................)</div>
                </td>
                <td>
                    Disetujui Oleh,<br>
                    <small>Manager Operasional</small>
                    <div class="sign-space"></div>
                    <div class="sign-name">(..............................)</div>
                </td>
            </tr>
        </table>
    </div>

    {{-- FOOTER BAWAH (Halaman) --}}
    <footer>
        <table width="100%">
            <tr>
                <td align="left" width="50%"><i>Confidential Document - Internal Use Only</i></td>
                <td align="right" width="50%">Hal <span class="page-number"></span></td>
            </tr>
        </table>
    </footer>

</body>
</html>