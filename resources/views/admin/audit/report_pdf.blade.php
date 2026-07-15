<!DOCTYPE html>
<html>
<head>
    <title>{{ $title ?? 'Laporan Audit' }}</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; }
        .header { text-align: center; margin-bottom: 20px; }
        .pt-name { font-size: 16px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table, th, td { border: 1px solid #333; }
        th { background-color: #f2f2f2; padding: 8px; text-align: center; }
        td { padding: 6px; }
        .text-center { text-align: center; }
        .badge-success { color: green; font-weight: bold; }
        .badge-danger { color: red; font-weight: bold; }
        .summary-table td { border: none; padding: 3px; }
        .summary-box { width: 40%; border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="header">
        <div class="pt-name">PT. SILO</div>
        <h2>LAPORAN HASIL AUDIT & OPNAME ASET IT</h2>
        {{-- Perbaikan error format() on null dengan pengecekan ternary dan Carbon parse --}}
        <p>Judul Sesi: <strong>{{ $audit->title }}</strong> | Tanggal Selesai: {{ $audit->end_date ? \Carbon\Carbon::parse($audit->end_date)->format('d M Y') : \Carbon\Carbon::parse($audit->updated_at)->format('d M Y') }}</p>
    </div>

    <div class="summary-box">
        <h4>Ringkasan Eksekutif</h4>
        <table class="summary-table">
            {{-- Penambahan null coalescing (?? 0) agar tidak error jika key array berbeda/kosong --}}
            <tr><td>Total Aset Aktif di DB</td><td>: {{ $summary['total_db'] ?? ($summary['total'] ?? 0) }} Unit</td></tr>
            <tr><td>Aset Ditemukan (Fisik)</td><td>: {{ $summary['total_scanned'] ?? ($summary['found'] ?? 0) }} Unit</td></tr>
            <tr><td class="badge-success">-> Sesuai Posisi DB</td><td>: {{ $summary['match'] ?? 0 }} Unit</td></tr>
            <tr><td class="badge-danger">-> Salah Posisi (Selisih)</td><td>: {{ $summary['mismatch'] ?? 0 }} Unit</td></tr>
            <tr><td>Kondisi Rusak (Saat Scan)</td><td>: {{ $summary['damaged'] ?? 0 }} Unit</td></tr>
            <tr><td class="badge-danger">Aset Hilang / Tidak Ditemukan</td><td>: <strong>{{ $summary['missing'] ?? 0 }} Unit</strong></td></tr>
        </table>
    </div>

    <h4>1. Detail Aset Salah Posisi (Mismatch Location)</h4>
    <table>
        <thead>
            <tr>
                <th>Kode Aset</th>
                <th>Nama Barang / SN</th>
                <th>Lokasi Seharusnya (DB)</th>
                <th>Lokasi Fisik (Scan)</th>
                <th>Scanner</th>
                <th>Waktu Scan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audit->items->where('is_found', true)->where('is_match', false) as $item)
            <tr>
                <td class="text-center">{{ $item->aset->kode_asset ?? '-' }}</td>
                <td>
                    {{ $item->aset->masterBarang->nama_barang ?? '-' }} <br>
                    <small>SN: {{ $item->aset->sn ?? '-' }}</small>
                </td>
                <td>{{ $item->aset->lokasi_saat_ini ?? '-' }}</td> <!-- Lokasi snapshot/lama -->
                <td class="badge-danger">{{ $item->scanned_location ?? '-' }}</td>
                <td>{{ $item->scanner->nama ?? ($item->scanner->name ?? '-') }}</td>
                {{-- Penanganan jika scanned_at null atau bukan object tanggal --}}
                <td class="text-center">{{ $item->scanned_at ? \Carbon\Carbon::parse($item->scanned_at)->format('d/m H:i') : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Tidak ada aset salah posisi.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="page-break-before: always;"></div> <!-- Pindah Halaman -->

    <h4>2. Detail Aset Hilang / Tidak Ditemukan</h4>
    <table>
        <thead>
            <tr>
                <th>Kode Aset</th>
                <th>Nama Barang / SN</th>
                <th>Lokasi Terakhir (DB)</th>
                <th>PIC Terakhir (DB)</th>
                <th>Keterangan Sistem</th>
            </tr>
        </thead>
        <tbody>
            @forelse($audit->items->where('is_found', false) as $item)
            <tr>
                <td class="text-center">{{ $item->aset->kode_asset ?? '-' }}</td>
                <td>
                    {{ $item->aset->masterBarang->nama_barang ?? '-' }} <br>
                    <small>SN: {{ $item->aset->sn ?? '-' }}</small>
                </td>
                <td>{{ $item->aset->lokasi_saat_ini ?? '-' }}</td>
                <td>{{ $item->pic_terakhir ?? '-' }}</td>
                <td class="badge-danger">{{ $item->notes ?? 'Belum di-scan' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Alhamdulillah, tidak ada aset hilang.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: right; width: 30%; float: right;">
        <p>Kota, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
        <p>Dilaporkan Oleh,</p>
        <br><br><br>
        {{-- Mengakomodasi kolom 'name' atau 'nama' pada tabel users --}}
        <p><strong>{{ $audit->pengaju->name ?? ($audit->pengaju->nama ?? 'Admin IT') }}</strong><br>Admin IT</p>
    </div>

</body>
</html>