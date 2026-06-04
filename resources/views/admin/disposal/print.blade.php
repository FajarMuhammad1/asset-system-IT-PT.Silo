<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita Acara Pemusnahan Aset - PT. SILO</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #000;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2, .header h3, .header p {
            margin: 0;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14pt;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        .content {
            text-align: justify;
        }
        table.detail-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table.detail-table th, table.detail-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table.detail-table th {
            background-color: #f2f2f2;
            text-align: center;
        }
        .signature-section {
            margin-top: 50px;
            width: 100%;
            display: table;
        }
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-space {
            height: 100px; /* Ruang untuk tanda tangan */
        }
        
        /* Pengaturan khusus untuk Print */
        @media print {
            body {
                padding: 0;
            }
            @page {
                size: A4 portrait;
                margin: 2cm;
            }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="container">
        <div class="header">
            <h2>PT. SILO (RESOURCE ASSET SYSTEM IT)</h2>
            <p>Jl. Contoh Alamat Perusahaan No. 123, Kota, Provinsi</p>
            <p>Telp: (021) 1234567 | Email: it.support@silo.co.id</p>
        </div>

        <div class="title">
            BERITA ACARA PEMUSNAHAN ASET (DISPOSAL)
        </div>

        <div class="content">
            <p>Pada hari ini, tanggal <strong>{{ \Carbon\Carbon::parse($disposal->updated_at)->isoFormat('D MMMM Y') }}</strong>, telah dilakukan persetujuan dan eksekusi pemusnahan aset inventaris IT PT. SILO, dengan rincian sebagai berikut:</p>

            <table class="detail-table">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="35%">Nama Barang / Tipe</th>
                        <th width="20%">No. Inventaris / SN</th>
                        <th width="40%">Alasan Pemusnahan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: center;">1</td>
                        <td>{{ $disposal->barangMasuk->masterBarang->nama_barang ?? 'Unknown' }}</td>
                        <td>{{ $disposal->barangMasuk->sn ?? '-' }}</td>
                        <td>{{ $disposal->reason }}</td>
                    </tr>
                </tbody>
            </table>

            <p>Barang tersebut di atas telah dinyatakan rusak berat / tidak dapat digunakan kembali dan/atau telah melewati masa pakai (usang). Data pada perangkat penyimpanan (jika ada) telah dibersihkan secara permanen (*Data Wiping*).</p>
            
            <p>Demikian Berita Acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya dan sebagai bukti perubahan status aset menjadi "Dimusnahkan" pada Sistem Aset IT.</p>
        </div>

        <div class="signature-section">
            <div class="signature-box">
                <p>Dibuat & Dieksekusi Oleh,<br><strong>Admin IT (Lokal)</strong></p>
                <div class="signature-space"></div>
                <p><u>{{ $disposal->pengaju->nama ?? '...................................' }}</u></p>
            </div>
            
            <div class="signature-box">
                <p>Disetujui & Diketahui Oleh,<br><strong>Super Admin (HO)</strong></p>
                <div class="signature-space"></div>
                <p><u>...................................</u></p>
            </div>
        </div>
    </div>

</body>
</html>