<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan Audit' }}</title>
    <style>
        /* Typography & Body */
        body { 
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; 
            font-size: 11px; 
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        /* Header Layout */
        .header-table { width: 100%; border: none; margin-bottom: 10px; }
        .header-table td { border: none; padding: 0; vertical-align: top; }
        .company-name { font-size: 24px; font-weight: bold; color: #2C3E50; margin: 0 0 5px 0; letter-spacing: 1px; }
        .report-title { font-size: 16px; color: #7F8C8D; margin: 0; text-transform: uppercase; }
        .doc-info { text-align: right; font-size: 11px; color: #555; }
        .doc-info strong { color: #2C3E50; }
        
        .divider { border: 0; border-top: 2px solid #2C3E50; border-bottom: 1px solid #ddd; height: 3px; margin-bottom: 20px; }

        /* Section Titles */
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #2C3E50;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            text-transform: uppercase;
        }

        /* Summary Box */
        .summary-container { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            background-color: #F8F9F9;
            border: 1px solid #E5E7E9;
        }
        .summary-container td { border: none; padding: 10px 15px; vertical-align: top; }
        .summary-list { width: 100%; border-collapse: collapse; }
        .summary-list td { padding: 4px 0; font-size: 12px; border-bottom: 1px dashed #E5E7E9; }
        .summary-list tr:last-child td { border-bottom: none; }
        .summary-label { color: #555; }
        .summary-value { font-weight: bold; text-align: right; color: #2C3E50; }

        /* Data Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #E5E7E9; padding: 8px 10px; }
        .data-table th { 
            background-color: #2C3E50; 
            color: #ffffff; 
            font-size: 10px; 
            text-transform: uppercase; 
            text-align: left; 
        }
        .data-table th.text-center { text-align: center; }
        .data-table tbody tr:nth-child(even) { background-color: #F9F9F9; }
        .data-table tbody tr:hover { background-color: #F1F4F6; }
        
        /* Utility Classes */
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        
        /* Badges */
        .badge { 
            padding: 3px 6px; 
            border-radius: 3px; 
            font-size: 10px; 
            font-weight: bold; 
            display: inline-block;
        }
        .badge-success { background-color: #E8F8F5; color: #117A65; border: 1px solid #D1F2EB; }
        .badge-danger { background-color: #FDEDEC; color: #C0392B; border: 1px solid #FADBD8; }
        .badge-warning { background-color: #FEF9E7; color: #B9770E; border: 1px solid #FCF3CF; }
        .text-danger { color: #C0392B; font-weight: bold; }
        .text-success { color: #117A65; font-weight: bold; }

        /* Signature Section */
        .signature-section { width: 100%; margin-top: 40px; border: none; }
        .signature-section td { border: none; width: 33%; text-align: center; padding: 0; }
        .signature-name { font-weight: bold; text-decoration: underline; margin-bottom: 2px; color: #2C3E50; }
        .signature-role { color: #7F8C8D; font-size: 10px; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td style="width: 50%;">
                <h1 class="company-name">PT. SILO</h1>
                <h2 class="report-title">Laporan Audit & Opname Aset IT</h2>
            </td>
            <td style="width: 50%;" class="doc-info">
                <p>
                    <strong>Sesi Audit:</strong> {{ $audit->title }}<br>
                    <strong>Tgl Selesai:</strong> {{ $audit->end_date ? \Carbon\Carbon::parse($audit->end_date)->format('d M Y') : \Carbon\Carbon::parse($audit->updated_at)->format('d M Y') }}<br>
                    <strong>Tgl Cetak:</strong> {{ \Carbon\Carbon::now()->format('d M Y, H:i') }}
                </p>
            </td>
        </tr>
    </table>
    <hr class="divider">

    <!-- Executive Summary -->
    <div class="section-title">Ringkasan Eksekutif</div>
    <table class="summary-container">
        <tr>
            <td style="width: 50%; border-right: 1px solid #E5E7E9;">
                <table class="summary-list">
                    <tr>
                        <td class="summary-label">Total Aset Aktif di DB</td>
                        <td class="summary-value">{{ $summary['total_db'] ?? ($summary['total'] ?? 0) }} Unit</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Aset Ditemukan (Fisik)</td>
                        <td class="summary-value">{{ $summary['total_scanned'] ?? ($summary['found'] ?? 0) }} Unit</td>
                    </tr>
                    <tr>
                        <td class="summary-label">Kondisi Rusak (Saat Scan)</td>
                        <td class="summary-value">{{ $summary['damaged'] ?? 0 }} Unit</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%;">
                <table class="summary-list">
                    <tr>
                        <td class="summary-label"><span class="badge badge-success">✓ Sesuai Posisi DB</span></td>
                        <td class="summary-value text-success">{{ $summary['match'] ?? 0 }} Unit</td>
                    </tr>
                    <tr>
                        <td class="summary-label"><span class="badge badge-warning">⚠ Salah Posisi (Selisih)</span></td>
                        <td class="summary-value" style="color: #B9770E; font-weight: bold;">{{ $summary['mismatch'] ?? 0 }} Unit</td>
                    </tr>
                    <tr>
                        <td class="summary-label"><span class="badge badge-danger">✗ Hilang / Tidak Ditemukan</span></td>
                        <td class="summary-value text-danger">{{ $summary['missing'] ?? 0 }} Unit</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Mismatch Items Table -->
    <div class="section-title">1. Detail Aset Salah Posisi (Mismatch Location)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 12%;">Kode Aset</th>
                <th style="width: 28%;">Nama Barang / SN</th>
                <th style="width: 18%;">Lokasi (DB)</th>
                <th style="width: 18%;">Lokasi (Fisik)</th>
                <th style="width: 12%;">Scanner</th>
                <th class="text-center" style="width: 12%;">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audit->items->where('is_found', true)->where('is_match', false) as $item)
            <tr>
                <td class="text-center" style="font-weight: bold;">{{ $item->aset->kode_asset ?? '-' }}</td>
                <td>
                    <strong>{{ $item->aset->masterBarang->nama_barang ?? '-' }}</strong><br>
                    <span style="color: #7F8C8D; font-size: 9px;">SN: {{ $item->aset->sn ?? '-' }}</span>
                </td>
                <td>{{ $item->aset->lokasi_saat_ini ?? '-' }}</td>
                <td><span class="badge badge-warning">{{ $item->scanned_location ?? '-' }}</span></td>
                <td>{{ $item->scanner->nama ?? ($item->scanner->name ?? '-') }}</td>
                <td class="text-center" style="font-size: 9px;">
                    {{ $item->scanned_at ? \Carbon\Carbon::parse($item->scanned_at)->format('d/m/y H:i') : '-' }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center" style="padding: 20px; color: #7F8C8D;">
                    <em>Tidak ada aset salah posisi. Semuanya sesuai dengan database.</em>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-before: always;"></div> <!-- Pindah Halaman -->

    <!-- Missing Items Table -->
    <div class="section-title">2. Detail Aset Hilang / Tidak Ditemukan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 12%;">Kode Aset</th>
                <th style="width: 30%;">Nama Barang / SN</th>
                <th style="width: 20%;">Lokasi Terakhir (DB)</th>
                <th style="width: 18%;">PIC Terakhir</th>
                <th style="width: 20%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audit->items->where('is_found', false) as $item)
            <tr>
                <td class="text-center" style="font-weight: bold;">{{ $item->aset->kode_asset ?? '-' }}</td>
                <td>
                    <strong>{{ $item->aset->masterBarang->nama_barang ?? '-' }}</strong><br>
                    <span style="color: #7F8C8D; font-size: 9px;">SN: {{ $item->aset->sn ?? '-' }}</span>
                </td>
                <td>{{ $item->aset->lokasi_saat_ini ?? '-' }}</td>
                <td>{{ $item->pic_terakhir ?? '-' }}</td>
                <td><span class="badge badge-danger">{{ $item->notes ?? 'Belum di-scan' }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center" style="padding: 20px; color: #117A65; font-weight: bold;">
                    <em>Alhamdulillah, tidak ada aset yang hilang.</em>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Signature Block (PDF Safe using tables) -->
    <table class="signature-section">
        <tr>
            <td></td>
            <td></td>
            <td>
                <p style="margin-bottom: 50px;">Kota, {{ \Carbon\Carbon::now()->format('d F Y') }}<br>Dilaporkan Oleh,</p>
                <div class="signature-name">{{ $audit->pengaju->name ?? ($audit->pengaju->nama ?? 'Admin IT') }}</div>
                <div class="signature-role">Admin IT / Auditor</div>
            </td>
        </tr>
    </table>

</body>
</html>