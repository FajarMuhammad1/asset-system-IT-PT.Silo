<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan RKAB Tahun {{ $tahunDipilih }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #2d3748; line-height: 1.5; font-size: 10pt; margin: 0; padding: 20px; }
        
        /* Kop Laporan */
        .header-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; border-bottom: 3px solid #1a365d; }
        .company-name { font-size: 16pt; font-weight: bold; color: #1a365d; text-transform: uppercase; margin: 0; }
        .company-sub { font-size: 9pt; color: #4a5568; margin: 2px 0 15px 0; }
        
        /* Judul */
        .report-title { text-align: center; font-size: 14pt; font-weight: bold; margin-top: 10px; text-transform: uppercase; }
        .report-subtitle { text-align: center; font-size: 11pt; color: #4a5568; margin-bottom: 25px; }

        /* Meta Tabel */
        .meta-table { width: 100%; font-size: 9.5pt; margin-bottom: 20px; }
        .meta-table td { padding: 3px 0; }
        
        /* Card Ringkasan (Bentuk Tabel) */
        .summary-table { width: 100%; border-spacing: 10px 0; margin-left: -10px; margin-right: -10px; margin-bottom: 25px; }
        .summary-cell { width: 25%; background-color: #f7fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px; }
        .summary-title { font-size: 8pt; font-weight: bold; text-transform: uppercase; color: #718096; margin-bottom: 5px; }
        .summary-value { font-size: 11pt; font-weight: bold; color: #2d3748; }

        /* Tabel Data Utama */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .data-table th { background-color: #1a365d; color: #ffffff; padding: 8px; border: 1px solid #1a365d; font-size: 8.5pt; text-transform: uppercase; }
        .data-table td { padding: 8px; border: 1px solid #e2e8f0; font-size: 9.5pt; }
        .data-table tr:nth-child(even) td { background-color: #fcfdfd; }
        
        .text-center { text-align: center; } .text-right { text-align: right; } .font-bold { font-weight: bold; }
        
        /* Tanda Tangan */
        .signature-table { width: 100%; margin-top: 40px; text-align: center; font-size: 10pt; page-break-inside: avoid; }
        .signature-space { height: 70px; }
        .signature-name { font-weight: bold; text-decoration: underline; }

        @media print {
            body { padding: 0; }
            .summary-cell { border: 1px solid #000; }
            .data-table th { background-color: #eaeaea !important; color: #000 !important; border: 1px solid #000; }
            .data-table td { border: 1px solid #000; }
        }
    </style>
</head>
<body onload="window.print()">

    <table class="header-table">
        <tr>
            <td>
                <div class="company-name">IT Support & Asset Management System</div>
                <div class="company-sub">Laporan Internal Realisasi Anggaran & Rencana Kerja Anggaran Biaya</div>
            </td>
            <td style="text-align: right; vertical-align: bottom; padding-bottom: 15px; color: #718096; font-size: 9pt;">
                Dokumen Rahasia Internal
            </td>
        </tr>
    </table>

    <div class="report-title">Laporan Analisis Anggaran & RKAB</div>
    <div class="report-subtitle">Tahun Anggaran: {{ $tahunDipilih }}</div>

    <table class="meta-table">
        <tr>
            <td width="15%" style="color: #718096;">Tanggal Cetak</td>
            <td width="35%" class="font-bold">: {{ \Carbon\Carbon::now()->isoFormat('D MMMM Y') }}</td>
            <td width="15%" style="color: #718096;">Periode Data</td>
            <td width="35%" class="font-bold">: Jan - Des {{ $tahunDipilih }}</td>
        </tr>
    </table>

    <table class="summary-table">
        <tr>
            <td class="summary-cell" style="border-left: 4px solid #3182ce;">
                <div class="summary-title">Total Alokasi</div>
                <div class="summary-value">Rp {{ number_format($totalAlokasi, 0, ',', '.') }}</div>
            </td>
            <td class="summary-cell" style="border-left: 4px solid #38a169;">
                <div class="summary-title">Realisasi</div>
                <div class="summary-value">Rp {{ number_format($totalTerpakai, 0, ',', '.') }}</div>
            </td>
            <td class="summary-cell" style="border-left: 4px solid #dd6b20;">
                <div class="summary-title">Sisa Anggaran</div>
                <div class="summary-value">Rp {{ number_format($totalSisa, 0, ',', '.') }}</div>
            </td>
            <td class="summary-cell" style="border-left: 4px solid #319795;">
                <div class="summary-title">Penyerapan</div>
                <div class="summary-value">{{ $totalPersentase }}%</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Kategori Kegiatan / Barang</th>
                <th width="15%">Alokasi Anggaran</th>
                <th width="15%">Realisasi</th>
                <th width="15%">Sisa Anggaran</th>
                <th width="15%">%</th>
            </tr>
        </thead>
        <tbody>
            @forelse($budgets as $index => $budget)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold">{{ $budget->kategori }}</td>
                <td class="text-right">Rp {{ number_format($budget->anggaran_alokasi, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($budget->anggaran_terpakai, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($budget->sisa_anggaran, 0, ',', '.') }}</td>
                <td class="text-center">{{ $budget->persentase_penyerapan }}%</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Belum ada data RKAB di tahun ini.</td></tr>
            @endforelse
            <tr style="background-color: #edf2f7; font-weight: bold;">
                <td colspan="2" class="text-center">TOTAL KESELURUHAN</td>
                <td class="text-right">Rp {{ number_format($totalAlokasi, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalTerpakai, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
                <td class="text-center">{{ $totalPersentase }}%</td>
            </tr>
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td width="50%">
                <div>Dilaporkan Oleh,</div><div class="signature-space"></div>
                <div class="signature-name">{{ Auth::user()->nama ?? 'Admin IT' }}</div>
                <div style="font-size: 9pt; color:#718096">Staf Operasional IT</div>
            </td>
            <td width="50%">
                <div>Mengetahui & Menyetujui,</div><div class="signature-space"></div>
                <div class="signature-name">Super Admin / IT Manager</div>
                <div style="font-size: 9pt; color:#718096">Head of IT Department</div>
            </td>
        </tr>
    </table>
</body>
</html>