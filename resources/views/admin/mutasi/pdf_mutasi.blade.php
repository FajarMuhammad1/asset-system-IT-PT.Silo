<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        /* Reset & Base Styles */
        body { 
            font-family: 'Times New Roman', Times, serif; 
            font-size: 11pt; 
            line-height: 1.35; 
            color: #000; 
            margin: 0; 
            padding: 0; 
        }
        
        /* Layout Helpers */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .w-100 { width: 100%; }
        .mt-1 { margin-top: 5px; }
        .mt-2 { margin-top: 10px; }
        .mt-3 { margin-top: 15px; }
        .mt-4 { margin-top: 20px; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-3 { margin-bottom: 15px; }
        .mb-4 { margin-bottom: 20px; }
        
        /* Header Logo Section */
        .header-table { 
            width: 100%; 
            border-bottom: 2px solid #000; 
            margin-bottom: 15px; 
            padding-bottom: 5px; 
        }
        .logo-img { 
            height: 70px; 
            width: auto; 
            margin-bottom: 5px; 
        }
        .company-title { 
            font-size: 13pt; 
            font-weight: bold; 
            color: #111; 
            letter-spacing: 0.5px;
        }
        .dept-title { 
            font-size: 9.5pt; 
            color: #444; 
            letter-spacing: 1px; 
        }

        /* Document Title Box */
        .doc-title {
            text-align: center;
            margin-bottom: 15px;
        }
        .doc-title h2 {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0 0 4px 0;
            text-transform: uppercase;
        }
        .doc-title .doc-number {
            font-size: 10pt;
            color: #333;
        }

        /* Tables */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 12px;
        }
        table.data-table th, table.data-table td { 
            border: 1px solid #000; 
            padding: 5px 8px; 
            font-size: 10pt;
            vertical-align: middle;
        }
        table.data-table th { 
            background-color: #f2f2f2; 
            text-align: center; 
            font-weight: bold;
        }

        /* Party Info Box */
        table.party-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.party-table td {
            vertical-align: top;
            padding: 3px 6px;
            font-size: 10pt;
        }

        /* Signature Section */
        .signature-table { 
            width: 100%; 
            margin-top: 20px; 
            border: 1px solid #000; 
            border-collapse: collapse; 
        }
        .signature-table th { 
            border: 1px solid #000; 
            padding: 6px 4px; 
            background: #f0f0f0; 
            font-size: 9.5pt; 
            text-align: center;
        }
        .signature-table td { 
            border: 1px solid #000; 
            padding: 6px; 
            text-align: center; 
            vertical-align: bottom; 
            height: 95px; 
            font-size: 9.5pt;
        } 
        
        .manager-signature-table {
            width: 50%;
            margin: 15px auto 0 auto;
            border: 1px solid #000;
            border-collapse: collapse;
        }
        .manager-signature-table th {
            border: 1px solid #000;
            padding: 5px;
            background: #f0f0f0;
            font-size: 9.5pt;
            text-align: center;
        }
        .manager-signature-table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            vertical-align: bottom;
            height: 75px;
            font-size: 9.5pt;
        }

        .ttd-img {
            max-height: 55px;
            max-width: 130px;
            width: auto;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>

    {{-- KOP SURAT RESMI --}}
    <table class="header-table">
        <tr>
            <td align="center">
                @if($logo)
                    <img src="{{ $logo }}" class="logo-img" alt="Logo">
                @else
                    <span style="font-size:18pt; font-weight:bold;">PT. SILO</span>
                @endif
                <br>
                <span class="company-title">PT. SEBUKU IRON LATERITIC ORES</span><br>
                <span class="dept-title">IT & COMMUNICATION SYSTEM DEPARTMENT</span>
            </td>
        </tr>
    </table>

    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title">
        <h2>BERITA ACARA MUTASI & SERAH TERIMA ASET</h2>
        <div class="doc-number">
            Nomor: BA-MUT/{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi ?? $mutasi->created_at)->format('Ym') }}/{{ sprintf('%04d', $mutasi->id) }}
        </div>
    </div>

    {{-- PARAGRAF PENGANTAR --}}
    <p class="text-justify mb-2" style="font-size: 10.5pt;">
        Pada hari ini, <strong>{{ \Carbon\Carbon::parse($mutasi->tanggal_mutasi ?? $mutasi->created_at)->translatedFormat('l') }}</strong>, 
        tanggal <strong>{{ $tanggal_cetak }}</strong>, telah dilaksanakan proses mutasi/pemindahan hak pemegang dan tanggung jawab operasional atas aset IT PT. SILO di antara pihak-pihak berikut:
    </p>

    {{-- INFORMASI PIHAK-PIHAK --}}
    <table class="party-table" style="background-color: #fafafa; border: 1px solid #ddd; margin-bottom: 12px;">
        <tr>
            <td width="48%" style="border-right: 1px dashed #ccc;">
                <strong style="text-decoration: underline; color: #b30000;">PIHAK I (PENGGUNA SEBELUMNYA / ASAL)</strong><br>
                <table style="width: 100%; margin-top: 3px; font-size: 9.5pt;">
                    <tr>
                        <td width="90" style="padding: 1px 0;">Nama Lengkap</td>
                        <td width="10" style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;"><strong>{{ $mutasi->userAsal->nama ?? 'Stok Gudang IT' }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0;">NIK</td>
                        <td style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;">{{ $mutasi->userAsal->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0;">Departemen</td>
                        <td style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;">{{ $mutasi->userAsal->departemen ?? 'IT Warehouse' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0;">Jabatan</td>
                        <td style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;">{{ $mutasi->userAsal->jabatan ?? '-' }}</td>
                    </tr>
                </table>
            </td>
            <td width="4%"></td>
            <td width="48%">
                <strong style="text-decoration: underline; color: #0056b3;">PIHAK II (PENGGUNA BARU / TUJUAN)</strong><br>
                <table style="width: 100%; margin-top: 3px; font-size: 9.5pt;">
                    <tr>
                        <td width="90" style="padding: 1px 0;">Nama Lengkap</td>
                        <td width="10" style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;"><strong>{{ $mutasi->userTujuan->nama ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0;">NIK</td>
                        <td style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;">{{ $mutasi->userTujuan->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0;">Departemen</td>
                        <td style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;">{{ $mutasi->userTujuan->departemen ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 1px 0;">Jabatan</td>
                        <td style="padding: 1px 0;">:</td>
                        <td style="padding: 1px 0;">{{ $mutasi->userTujuan->jabatan ?? '-' }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- RINCIAN ASET YANG DIMUTASI --}}
    <div style="font-weight: bold; font-size: 10.5pt; margin-bottom: 4px;">
        1. Rincian Data Aset IT yang Dimutasi:
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Kode Aset</th>
                <th width="25%">Nama & Merk Barang</th>
                <th width="20%">Serial Number (SN)</th>
                <th width="30%">Spesifikasi / Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center">1</td>
                <td><strong>{{ $mutasi->barangMasuk->kode_asset ?? '-' }}</strong></td>
                <td>
                    {{ $mutasi->barangMasuk->masterBarang->nama_barang ?? 'Perangkat IT' }}<br>
                    <small style="color: #444;">{{ $mutasi->barangMasuk->masterBarang->merk ?? '' }}</small>
                </td>
                <td>{{ $mutasi->barangMasuk->serial_number ?? '-' }}</td>
                <td>
                    {{ $mutasi->barangMasuk->masterBarang->spesifikasi ?? '-' }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- INFORMASI TAMBAHAN / KETERANGAN --}}
    <div style="font-weight: bold; font-size: 10.5pt; margin-bottom: 4px;">
        2. Keterangan & Lokasi Penempatan:
    </div>
    <table style="width: 100%; border: 1px solid #000; border-collapse: collapse; margin-bottom: 12px; font-size: 10pt;">
        <tr>
            <td width="25%" style="border: 1px solid #000; padding: 4px 8px; background-color: #f9f9f9; font-weight: bold;">Alasan Mutasi</td>
            <td style="border: 1px solid #000; padding: 4px 8px;">{{ $mutasi->keterangan ?? 'Pemindahan hak penggunaan aset' }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 4px 8px; background-color: #f9f9f9; font-weight: bold;">Lokasi Baru</td>
            <td style="border: 1px solid #000; padding: 4px 8px;">{{ $mutasi->lokasi_baru ?? ($mutasi->userTujuan->departemen ?? 'Lokasi User Baru') }}</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 4px 8px; background-color: #f9f9f9; font-weight: bold;">Status Transaksi</td>
            <td style="border: 1px solid #000; padding: 4px 8px;">
                <strong>{{ $mutasi->status }}</strong> 
                @if($mutasi->logSerahTerima)
                    (BAST Ref #{{ $mutasi->logSerahTerima->id }} - Status: {{ ucfirst($mutasi->logSerahTerima->status) }})
                @endif
            </td>
        </tr>
    </table>

    <p class="text-justify mb-2" style="font-size: 10pt;">
        Pihak I telah menyerahkan aset dalam kondisi baik kepada Pihak II melalui koordinasi dengan Tim IT Support. Pihak II bertanggung jawab penuh atas pemeliharaan fisik, keamanan, serta penggunaan aset sesuai kebijakan perusahaan.
    </p>

    <div class="text-right" style="font-size: 10pt; margin-top: 10px;">
        Sebuku, {{ $tanggal_cetak }}
    </div>

    {{-- BAGIAN TANDA TANGAN 3 PIHAK --}}
    <table class="signature-table">
        <tr>
            <th width="33.33%">Yang Menyerahkan<br><span style="font-size: 8.5pt; font-weight: normal;">(Pengguna Sebelumnya)</span></th>
            <th width="33.33%">Yang Menerima<br><span style="font-size: 8.5pt; font-weight: normal;">(Pengguna Baru)</span></th>
            <th width="33.33%">Yang Mengesahkan<br><span style="font-size: 8.5pt; font-weight: normal;">(Admin IT Support)</span></th>
        </tr>
        <tr>
            {{-- TTD PENGGUNA SEBELUMNYA --}}
            <td>
                <br><br><br>
                <strong style="text-decoration: underline;">{{ $mutasi->userAsal->nama ?? 'Stok Gudang IT' }}</strong><br>
                <span>{{ $mutasi->userAsal->jabatan ?? ($mutasi->userAsal->departemen ?? 'User Lama') }}</span>
            </td>

            {{-- TTD PENGGUNA BARU --}}
            <td>
                @if(!empty($mutasi->logSerahTerima->ttd_penerima))
                    <img src="{{ $mutasi->logSerahTerima->ttd_penerima }}" class="ttd-img" alt="TTD User Baru">
                @else
                    <br><br><br>
                @endif
                <strong style="text-decoration: underline;">{{ $mutasi->userTujuan->nama ?? '-' }}</strong><br>
                <span>{{ $mutasi->userTujuan->jabatan ?? ($mutasi->userTujuan->departemen ?? 'User Baru') }}</span>
            </td>

            {{-- TTD ADMIN IT --}}
            <td>
                @if(!empty($mutasi->logSerahTerima->ttd_petugas))
                    <img src="{{ $mutasi->logSerahTerima->ttd_petugas }}" class="ttd-img" alt="TTD Admin IT">
                @else
                    <br><br><br>
                @endif
                <strong style="text-decoration: underline;">{{ $mutasi->logSerahTerima->admin->nama ?? ($mutasi->approver->nama ?? 'Admin IT Support') }}</strong><br>
                <span>IT Support</span>
            </td>
        </tr>
    </table>

    {{-- TANDA TANGAN MENGETAHUI (SUPER ADMIN / MANAGER) --}}
    @if($mutasi->approver)
    <table class="manager-signature-table">
        <tr>
            <th>Mengetahui & Menyetujui<br><span style="font-size: 8.5pt; font-weight: normal;">(Head Manager / Super Admin)</span></th>
        </tr>
        <tr>
            <td>
                <br><br>
                <strong style="text-decoration: underline;">{{ $mutasi->approver->nama ?? 'Super Admin' }}</strong><br>
                <span>{{ $mutasi->approver->jabatan ?? 'Head Manager IT' }}</span>
            </td>
        </tr>
    </table>
    @endif

</body>
</html>
