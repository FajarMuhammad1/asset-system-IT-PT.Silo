<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ str_replace('_3', '', $title) }}</title>
    <style>
        /* MENGGUNAKAN STYLE YANG SAMA PERSIS AGAR KONSISTEN */
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; color: #000; margin: 0; padding: 0; }
        .page-break { page-break-after: always; }
        .text-center { text-align: center; }
        .text-justify { text-align: justify; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .mt-4 { margin-top: 20px; }
        .mb-4 { margin-bottom: 20px; }
        
        /* Header */
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 5px; }
        .logo-img { height: 100px; width: auto; margin-bottom: 10px; }
        .company-title { font-size: 14pt; font-weight: bold; color: #333; }
        .dept-title { font-size: 10pt; color: #666; letter-spacing: 1px; }

        /* Tables */
        .spec-box { margin-left: 20px; margin-bottom: 15px; }
        .spec-table td { padding: 1px 5px; font-size: 11pt; vertical-align: top; }

        /* Signature */
        .signature-table { width: 100%; margin-top: 30px; border: 1px solid #000; border-collapse: collapse; }
        .signature-table th { border: 1px solid #000; padding: 5px; background: #f0f0f0; font-size: 10pt; }
        .signature-table td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: bottom; height: 100px; } 
        
        /* Checklist Table (Ganti Style Lampiran Software) */
        .checklist-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .checklist-table th { border: 1px solid #000; padding: 8px; font-size: 10pt; background-color: #e3e3e3; text-align: center; }
        .checklist-table td { border: 1px solid #000; padding: 5px; font-size: 10pt; text-align: center; }

        /* Pernyataan */
        ol.pernyataan { margin-left: 0; padding-left: 20px; }
        ol.pernyataan li { margin-bottom: 8px; text-align: justify; }
    </style>
</head>
<body>

    {{-- ================================================================= --}}
    {{-- HALAMAN 1: BAST RADIO --}}
    {{-- ================================================================= --}}
    
    <table class="header-table">
        <tr>
            <td align="center">
                @if($logo)
                    <img src="{{ $logo }}" class="logo-img" alt="Logo">
                @else
                    <span style="font-size:20pt; font-weight:bold;">PT. SILO</span>
                @endif
                <br>
                <span class="company-title">PT. SEBUKU IRON LATERITIC ORES</span><br>
                <span class="dept-title">IT & COMMUNICATION SYSTEM DEPARTMENT</span>
            </td>
        </tr>
    </table>

    <div class="text-center mb-4">
        <span class="text-bold text-uppercase" style="font-size: 13pt;">BERITA ACARA SERAH TERIMA RADIO KOMUNIKASI</span>
    </div>

    <p class="text-justify">
        Bersama ini Kami serah terima Unit <strong>{{ $log->aset->masterBarang->nama_barang ?? 'RADIO KOMUNIKASI' }}</strong>, 
        kepada Bapak / Ibu <strong>{{ $log->pemegang->nama }}</strong> dari Department 
        <strong>{{ $log->pemegang->departemen ?? '-' }}</strong> 
        pada tanggal {{ $tanggal_cetak }}.
    </p>

    <div class="spec-box">
        {{-- Menampilkan Merk dan Tipe --}}
        <strong class="text-uppercase">{{ $log->aset->masterBarang->merk ?? 'MOTOROLA' }} {{ $log->aset->masterBarang->tipe ?? '' }}</strong>
        <table class="spec-table">
            <tr>
                <td width="10">-</td>
                <td width="130">Serial Number (SN)</td>
                <td>: {{ $log->aset->serial_number }}</td>
            </tr>
            <tr>
                <td>-</td>
                <td>Kode Aset / Back No</td>
                <td>: {{ $log->aset->kode_aset ?? '-' }}</td>
            </tr>
            <tr>
                <td>-</td>
                <td>Jenis Perangkat</td>
                {{-- Contoh logika sederhana: jika nama barang mengandung HT maka Handy Talky --}}
                <td>: {{ stripos($log->aset->masterBarang->nama_barang, 'RIG') !== false ? 'RADIO RIG (BASE STATION)' : 'HANDY TALKY (PORTABLE)' }}</td>
            </tr>
            <tr>
                <td>-</td>
                <td>Kondisi Fisik</td>
                <td>: BAIK / LAYAK PAKAI</td>
            </tr>
        </table>
    </div>

    <div class="spec-box">
        <strong class="text-uppercase">KELENGKAPAN PERANGKAT :</strong><br>
        {{-- List manual atau ambil dari DB jika ada --}}
        <ul style="margin-top: 5px; list-style-type: square;">
            <li>Unit Radio Main Body</li>
            <li>Antena (Original/Modifikasi)</li>
            <li>Baterai Pack</li>
            <li>Belt Clip (Jepit Pinggang)</li>
            <li>Desktop Charger & Adapter</li>
        </ul>
    </div>

    <p class="text-justify">
        Perangkat komunikasi diterima dalam kondisi baik, sinyal normal, dan berfungsi dengan baik. Demikian berita acara ini kami buat dan dapat digunakan sebagaimana mestinya.
    </p>

    <p class="mt-4">Sebuku, {{ $tanggal_cetak }}</p>

    {{-- TABEL TANDA TANGAN (SAMA DENGAN SEBELUMNYA) --}}
    <table class="signature-table">
        <tr>
            <th width="50%">Yang Menerima</th>
            <th width="50%">Yang Menyerahkan</th>
        </tr>
        <tr>
            <td>
                <br>
                @if(!empty($log->ttd_penerima))
                    <img src="{{ $log->ttd_penerima }}" style="height: 60px; width: auto; max-width: 150px;" alt="TTD User">
                @else
                    <br><br><br>
                @endif
                <br>
                <strong style="text-decoration: underline;">{{ $log->pemegang->nama }}</strong><br>
                {{ $log->pemegang->jabatan ?? 'User' }}
            </td>
            <td>
                <br>
                @if(!empty($log->ttd_petugas))
                    <img src="{{ $log->ttd_petugas }}" style="height: 60px; width: auto; max-width: 150px;" alt="TTD Admin">
                @else
                    <br><br><br>
                @endif
                <br>
                <strong style="text-decoration: underline;">{{ $log->admin->nama ?? 'Admin IT' }}</strong><br>
                IT Support
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    {{-- ================================================================= --}}
    {{-- HALAMAN 2: CHECKLIST KELENGKAPAN (PENGGANTI LIST SOFTWARE) --}}
    {{-- ================================================================= --}}

    <div class="text-center mb-4" style="margin-top: 20px;">
        <span class="text-bold text-uppercase">LAMPIRAN: CHECKLIST KELENGKAPAN & KONDISI</span>
    </div>

    <table style="width: 80%; margin-bottom: 15px; font-size: 11pt;">
        <tr><td width="150">NAMA USER</td><td>: {{ $log->pemegang->nama }}</td></tr>
        <tr><td>UNIT / DIVISI</td><td>: {{ $log->pemegang->departemen ?? '-' }}</td></tr>
        <tr><td>MODEL RADIO</td><td>: {{ $log->aset->masterBarang->merk ?? '' }} {{ $log->aset->masterBarang->tipe ?? '' }}</td></tr>
    </table>

    <table class="checklist-table">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="35%">ITEM KELENGKAPAN</th>
                <th width="15%">KONDISI<br>(BAIK/RUSAK)</th>
                <th width="25%">KETERANGAN</th>
                <th width="20%">PARAF PETUGAS</th>
            </tr>
        </thead>
        <tbody>
            {{-- ITEM 1: MAIN UNIT --}}
            <tr>
                <td>1</td>
                <td class="text-left">Main Unit Radio (Body)</td>
                <td>BAIK</td>
                <td>SN: {{ $log->aset->serial_number }}</td>
                <td rowspan="6" style="vertical-align: middle;">
                    @if(!empty($log->ttd_petugas))
                        <img src="{{ $log->ttd_petugas }}" style="height: 50px; width: auto;" alt="Paraf">
                    @else
                        <br><br>
                    @endif
                </td>
            </tr>
            {{-- ITEM 2: ANTENA --}}
            <tr>
                <td>2</td>
                <td class="text-left">Antena</td>
                <td>BAIK</td>
                <td>Original</td>
            </tr>
            {{-- ITEM 3: BATERAI --}}
            <tr>
                <td>3</td>
                <td class="text-left">Baterai</td>
                <td>BAIK</td>
                <td>-</td>
            </tr>
            {{-- ITEM 4: BELT CLIP --}}
            <tr>
                <td>4</td>
                <td class="text-left">Belt Clip (Penjepit)</td>
                <td>BAIK</td>
                <td>-</td>
            </tr>
            {{-- ITEM 5: CHARGER --}}
            <tr>
                <td>5</td>
                <td class="text-left">Desktop Charger / Adaptor</td>
                <td>BAIK</td>
                <td>-</td>
            </tr>
            {{-- ITEM 6: KNOB --}}
            <tr>
                <td>6</td>
                <td class="text-left">Knob Volume & Channel</td>
                <td>BAIK</td>
                <td>Berfungsi Normal</td>
            </tr>
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 10pt; font-style: italic;">
        * Pastikan seluruh item di atas diperiksa saat serah terima.
    </div>

    <div class="page-break"></div>

    {{-- ================================================================= --}}
    {{-- HALAMAN 3: SURAT PERNYATAAN PENGGUNA RADIO --}}
    {{-- ================================================================= --}}

    <div class="text-center mb-4" style="margin-top: 30px;">
        <span class="text-bold text-uppercase" style="text-decoration: underline; font-size: 13pt;">SURAT PERNYATAAN PENGGUNA RADIO</span>
    </div>

    <p>Yang bertanda tangan di bawah ini :</p>

    <table style="width: 90%; margin-left: 30px; margin-bottom: 20px;">
        <tr><td width="120">NAMA</td><td>: {{ $log->pemegang->nama }}</td></tr>
        <tr><td>NIK</td><td>: {{ $log->pemegang->nik ?? '-' }}</td></tr>
        <tr><td>DEPARTMENT</td><td>: {{ $log->pemegang->departemen ?? '-' }}</td></tr>
    </table>

    <p>Melalui surat ini menyatakan bahwa :</p>

    <ol class="pernyataan">
        <li>Saya menerima fasilitas alat komunikasi (Radio HT/Rig) milik <strong>PT. SEBUKU IRON LATERITIC ORES</strong> dalam kondisi baik dan siap pakai untuk mendukung operasional kerja.</li>
        
        <li>Saya bertanggung jawab penuh atas keamanan dan perawatan fisik perangkat tersebut selama berada dalam penguasaan saya.</li>
        
        <li>Saya hanya akan menggunakan perangkat radio ini untuk keperluan komunikasi kedinasan / operasional tambang dan <strong>TIDAK</strong> akan menggunakannya untuk hal-hal yang bersifat pribadi, provokatif, atau melanggar norma kesopanan.</li>
        
        <li>Saya tidak akan mengubah frekuensi, memprogram ulang, atau memodifikasi perangkat keras/lunak radio tanpa seijin Departemen IT & Communication.</li>
        
        <li>Apabila terjadi kerusakan akibat kelalaian (jatuh, terkena air, hilang) maka segala biaya perbaikan atau penggantian unit akan menjadi <strong>tanggung jawab saya pribadi</strong> sesuai kebijakan perusahaan.</li>
        
        <li>Saya bersedia mengembalikan perangkat ini kapan saja jika diminta oleh perusahaan, atau jika saya dimutasi, resign, atau tidak lagi membutuhkan alat komunikasi tersebut.</li>
    </ol>

    <p class="text-justify mt-4">
        Demikianlah surat pernyataan ini dibuat dengan penuh kesadaran dan tanpa tekanan ataupun paksaan dari pihak manapun.
    </p>

    <p class="mt-4">Sebuku, {{ $tanggal_cetak }}</p>

    <div style="margin-top: 40px; text-align: center; width: 40%; margin-left: auto; margin-right: 50px;">
        <p>Yang Membuat Pernyataan,</p>
        <br>
        @if(!empty($log->ttd_penerima))
            <img src="{{ $log->ttd_penerima }}" style="height: 60px; width: auto;" alt="TTD User">
        @else
            <br><br><br>
        @endif
        <br>
        <strong style="text-decoration: underline;">{{ $log->pemegang->nama }}</strong>
    </div>

</body>
</html>