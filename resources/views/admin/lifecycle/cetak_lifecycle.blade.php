<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Riwayat Aset - {{ $asset->kode_asset ?? '' }}</title>
    
    <link href="{{ asset('sbadmin2/css/sb-admin-2.min.css') }}" rel="stylesheet">
    
    <style>
        body {
            color: #000;
            background-color: #fff;
            font-family: 'Arial', sans-serif;
            font-size: 13px;
        }
        
        /* Desain Kop Surat */
        .kop-container {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .kop-logo {
            width: 70px;
            height: 70px;
            border: 2px solid #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            margin-right: 20px;
        }
        .kop-text h2 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .kop-text p {
            font-size: 11px;
            margin-bottom: 0;
            color: #333;
        }

        /* Judul Laporan */
        .judul-laporan {
            text-align: center;
            margin-bottom: 30px;
        }
        .judul-laporan h4 {
            font-size: 15px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 3px;
        }

        /* Detail Aset Info */
        .info-grid {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .info-column {
            width: 48%;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            width: 110px;
        }
        .info-value {
            flex: 1;
        }

        /* Tabel Custom */
        .table-lifecycle {
            border: 1.5px solid #000 !important;
            color: #000 !important;
        }
        .table-lifecycle th {
            border: 1.5px solid #000 !important;
            background-color: #fff !important;
            color: #000 !important;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            vertical-align: middle !important;
        }
        .table-lifecycle td {
            border: 1px solid #000 !important;
            font-size: 11px;
            color: #000 !important;
            vertical-align: middle !important;
        }

        /* Tanda Tangan */
        .ttd-container {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            width: 200px;
            text-align: center;
        }
        .ttd-space {
            height: 75px;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 2px;
        }

        /* CSS Print Setup */
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            .container { max-width: 100% !important; width: 100% !important; padding: 0 !important; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container mt-4">
        
        <div class="mb-4 text-right no-print">
            <button onclick="window.print()" class="btn btn-sm btn-dark shadow-sm">
                <i class="fas fa-print mr-1"></i> Cetak Dokumen
            </button>
            <button onclick="window.close()" class="btn btn-sm btn-secondary shadow-sm">Tutup</button>
        </div>

        <div class="kop-container">
            <div class="kop-logo">SILO</div>
            <div class="kop-text">
                <h2>PT. SUMBER INTI LOGISTIK</h2>
                <p>Jl. Tembang Makmur No. 123, Kawasan Industri Site Area, Kalimantan Selatan</p>
                <p>Telp: (0511) 123456 | Email: it.support@silo.co.id</p>
            </div>
        </div>

        <div class="judul-laporan">
            <h4>LAPORAN RIWAYAT ASET (LIFE CYCLE)</h4>
            <span class="text-muted">Tanggal Cetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
        </div>

        <div class="info-grid">
            <div class="info-column">
                <div class="info-row">
                    <div class="info-label">Kode Aset</div>
                    <div class="info-value">: <strong>{{ $asset->kode_asset ?? '-' }}</strong></div>
                </div>
                <div class="info-row">
                    <div class="info-label">Nama Barang</div>
                    <div class="info-value">: {{ $asset->masterBarang->nama_barang ?? '-' }}</div>
                </div>
            </div>
            <div class="info-column">
                <div class="info-row">
                    <div class="info-label">Tanggal Beli</div>
                    <div class="info-value">: {{ $asset->tanggal_masuk ? \Carbon\Carbon::parse($asset->tanggal_masuk)->translatedFormat('d F Y') : '-' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status Akhir</div>
                    <div class="info-value">: {{ $asset->kondisi ?? 'Aktif Digunakan' }}</div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-lifecycle text-dark">
            <thead>
                <tr class="text-center">
                    <th width="5%">NO</th>
                    <th width="15%">TANGGAL</th>
                    <th width="25%">JENIS AKTIVITAS</th>
                    <th width="20%">OLEH</th>
                    <th width="35%">KETERANGAN</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data Pertama: Log Pembelian/Masuk Baru --}}
                <tr>
                    <td class="text-center">1</td>
                    <td class="text-center">{{ $asset->tanggal_masuk ? \Carbon\Carbon::parse($asset->tanggal_masuk)->format('d/m/Y') : '-' }}</td>
                    <td>Pembelian Baru</td>
                    <td>Procurement</td>
                    <td>Masuk ke Gudang IT Utama</td>
                </tr>

                {{-- Data Selanjutnya: Diambil dari database riwayat perawatan --}}
                @foreach($asset->perawatanBarang as $index => $history)
                <tr>
                    <td class="text-center">{{ $index + 2 }}</td>
                    <td class="text-center">
                        {{ $history->tanggal_selesai ? \Carbon\Carbon::parse($history->tanggal_selesai)->format('d/m/Y') : '-' }}
                    </td>
                    <td>Service (Maintenance)</td>
                    <td>{{ $history->teknisi->name ?? 'Admin IT' }}</td>
                    <td>{{ $history->catatan_perawatan ?? 'Perawatan berkala selesai dilaksanakan.' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="ttd-container">
            <div class="ttd-box">
                <p class="mb-0">Mengetahui,</p>
                <div class="ttd-space"></div>
                <p class="ttd-nama">( Fahrin )</p>
                <p class="text-muted small mb-0">IT Manager</p>
            </div>
        </div>

    </div>

</body>
</html>