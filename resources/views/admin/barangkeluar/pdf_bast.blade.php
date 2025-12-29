<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    {{-- UPDATE: Menghapus string '_2' dari judul jika ada --}}
    <title>{{ str_replace('_3', '', $title) }}</title>
    <style>
        /* Reset & Base Styles */
        body { font-family: 'Times New Roman', Times, serif; font-size: 11pt; line-height: 1.3; color: #000; margin: 0; padding: 0; }
        .page-break { page-break-after: always; }
        
        /* Layout Helpers */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .text-justify { text-align: justify; }
        .text-bold { font-weight: bold; }
        .text-uppercase { text-transform: uppercase; }
        .w-100 { width: 100%; }
        .mt-2 { margin-top: 10px; }
        .mb-2 { margin-bottom: 10px; }
        .mt-4 { margin-top: 20px; }
        .mb-4 { margin-bottom: 20px; }
        
        /* Header Logo Section */
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 5px; }
        
        /* Logo diperbesar */
        .logo-img { 
            height: 200px; 
            width: auto; 
            margin-bottom: 10px; 
        }
        
        .company-title { font-size: 14pt; font-weight: bold; color: #333; }
        .dept-title { font-size: 10pt; color: #666; letter-spacing: 1px; }

        /* Tables */
        table.data-table { width: 100%; border-collapse: collapse; }
        table.data-table td { vertical-align: top; padding: 2px 0; }
        
        /* Box Spesifikasi */
        .spec-box { margin-left: 20px; margin-bottom: 15px; }
        .spec-table td { padding: 1px 5px; font-size: 11pt; }

        /* Signature Section */
        .signature-table { width: 100%; margin-top: 30px; border: 1px solid #000; border-collapse: collapse; }
        .signature-table th { border: 1px solid #000; padding: 5px; background: #f0f0f0; font-size: 10pt; }
        .signature-table td { border: 1px solid #000; padding: 5px; text-align: center; vertical-align: bottom; height: 100px; } 
        
        /* Lampiran Table Style */
        .lampiran-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .lampiran-table th { border: 1px solid #000; padding: 8px; font-size: 9pt; background-color: #e3e3e3; text-align: center; vertical-align: middle; }
        .lampiran-table td { border: 1px solid #000; padding: 5px; font-size: 9pt; text-align: center; vertical-align: middle; }

        /* List Pernyataan */
        ol.pernyataan { margin-left: 0; padding-left: 20px; }
        ol.pernyataan li { margin-bottom: 8px; text-align: justify; }
    </style>
</head>
<body>

    {{-- ================================================================= --}}
    {{-- HALAMAN 1: BERITA ACARA SERAH TERIMA (BAST) --}}
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
        <span class="text-bold text-uppercase" style="font-size: 13pt;">BERITA ACARA SERAH TERIMA ASSET</span>
    </div>

    <p class="text-justify">
        Bersama ini Kami serah terima Asset <strong>{{ $log->aset->masterBarang->nama_barang ?? 'PERANGKAT IT' }}</strong>, 
        kepada Bapak / Ibu <strong>{{ $log->pemegang->nama }}</strong> dari Department 
        <strong>{{ $log->pemegang->departemen ?? 'Inbound Logistics' }}</strong> 
        pada tanggal {{ $tanggal_cetak }}.
    </p>

    <div class="spec-box">
        <strong class="text-uppercase">{{ $log->aset->masterBarang->merk ?? 'LENOVO' }} {{ $log->aset->masterBarang->tipe ?? '90SM' }}</strong>
        <table class="spec-table">
            <tr>
                <td width="10">-</td>
                <td width="100">SN</td>
                <td>: {{ $log->aset->serial_number }}</td>
            </tr>
            <tr>
                <td>-</td>
                <td>Spesifikasi</td>
                <td>: {{ $log->aset->masterBarang->spesifikasi ?? 'Intel Core i3, HDD 1000GB, RAM 4GB' }}</td>
            </tr>
            <tr>
                <td>-</td>
                <td>OS</td>
                <td>: WINDOWS 10 = 7CVN4-PH79Q-T6CX2-72TPK-MG9TR (OEM)</td>
            </tr>
            <tr>
                <td>-</td>
                <td>Office</td>
                <td>: HOME & BUSINESS 2021</td>
            </tr>
        </table>
    </div>

    <div class="spec-box">
        <strong class="text-uppercase">PERANGKAT LAINNYA :</strong><br>
        <span style="margin-left: 15px;">KEYBOARD, MOUSE, MONITOR, POWER CABLE</span>
    </div>

    <p class="text-justify">
        Peralatan IT diterima dalam kondisi baik dan berfungsi dengan baik. Demikian berita acara ini kami buat dan dapat digunakan sebagaimana mestinya.
    </p>

    <p class="mt-4">Sebuku, {{ $tanggal_cetak }}</p>

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
    {{-- HALAMAN 2: LAMPIRAN LIST SOFTWARE --}}
    {{-- ================================================================= --}}

    <div class="text-center mb-4" style="margin-top: 20px;">
        <span class="text-bold text-uppercase">LIST SOFTWARE YANG DI-INSTALL (LAMPIRAN)</span>
    </div>

    <table style="width: 80%; margin-bottom: 15px; font-size: 11pt;">
        <tr><td width="150">NAMA USER</td><td>: {{ $log->pemegang->nama }}</td></tr>
        <tr><td>NIK</td><td>: {{ $log->pemegang->nik ?? '-' }}</td></tr>
        <tr><td>DEPARTMENT</td><td>: {{ $log->pemegang->departemen ?? '-' }}</td></tr>
    </table>

    <table class="lampiran-table">
        <thead>
            <tr>
                <th width="15%">JENIS PERANGKAT IT</th>
                <th width="15%">SERIAL NUMBER</th>
                <th width="20%">NAMA SOFTWARE</th>
                <th width="10%">LICENSE TYPE</th>
                <th width="20%">LICENSE KEY /<br>PRODUCT KEY</th>
                <th width="10%">TANDA TANGAN USER</th>
                <th width="10%">TANDA TANGAN STAF IT</th>
            </tr>
        </thead>
       <tbody>
        {{-- BARIS 1: WINDOWS (TANDA TANGAN DIMASUKKAN DI SINI PAKE ROWSPAN) --}}
        <tr>
            <td>PC DESKTOP<br>{{ $log->aset->masterBarang->merk ?? 'LENOVO' }}</td>
            <td>{{ $log->aset->serial_number }}</td>
            <td>WINDOWS 10</td>
            <td>OEM</td>
            <td>7CVN4-PH79Q-<br>T6CX2-72TPK-<br>MG9TR</td>
            
            {{-- KOLOM TANDA TANGAN USER (PENERIMA) --}}
            {{-- rowspan="5" artinya kolom ini menggabungkan 5 baris ke bawah --}}
            <td rowspan="5" class="text-center align-middle" style="vertical-align: middle;">
                @if(!empty($log->ttd_penerima))
                    <img src="{{ $log->ttd_penerima }}" style="height: 60px; width: auto;" alt="TTD User">
                @else
                    <br><small class="text-muted" style="font-size: 8pt;">(Belum TTD)</small>
                @endif
            </td>

            {{-- KOLOM TANDA TANGAN STAFF IT (PETUGAS) --}}
            {{-- Pastikan nama kolom di DB kamu benar, biasanya 'ttd_petugas' atau 'ttd_admin' --}}
            <td rowspan="5" class="text-center align-middle" style="vertical-align: middle;">
                @if(!empty($log->ttd_petugas))
                    <img src="{{ $log->ttd_petugas }}" style="height: 60px; width: auto;" alt="TTD Staff">
                @else
                    <br><small class="text-muted" style="font-size: 8pt;">(Belum TTD)</small>
                @endif
            </td>
        </tr>

        {{-- BARIS 2: OFFICE --}}
        {{-- PERHATIAN: Di baris ini JANGAN buat <td> untuk TTD lagi, karena sudah dicover rowspan di atas --}}
        <tr>
            <td>PC DESKTOP<br>{{ $log->aset->masterBarang->merk ?? 'LENOVO' }}</td>
            <td>{{ $log->aset->serial_number }}</td>
            <td>HOME & BUSINESS 2021</td>
            <td>OEM</td>
            <td>(Terlampir di Akun)</td>
        </tr>

        {{-- BARIS LOOP KOSONG --}}
        {{-- Di sini juga JANGAN buat <td> TTD --}}
        @for($i=0; $i<3; $i++)
        <tr>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        @endfor
    </tbody>
    </table>

    <div class="page-break"></div>

    {{-- ================================================================= --}}
    {{-- HALAMAN 3: SURAT PERNYATAAN --}}
    {{-- ================================================================= --}}

    <div class="text-center mb-4" style="margin-top: 30px;">
        <span class="text-bold text-uppercase" style="text-decoration: underline; font-size: 13pt;">SURAT PERNYATAAN</span>
    </div>

    <p>Yang bertanda tangan di bawah ini :</p>

    <table style="width: 90%; margin-left: 30px; margin-bottom: 20px;">
        <tr><td width="120">NAMA</td><td>: {{ $log->pemegang->nama }}</td></tr>
        <tr><td>NIK</td><td>: {{ $log->pemegang->nik ?? '-' }}</td></tr>
        <tr><td>DEPARTMENT</td><td>: {{ $log->pemegang->departemen ?? '-' }}</td></tr>
    </table>

    <p>Melalui surat ini menyatakan bahwa :</p>

    <ol class="pernyataan">
        <li>Saya akan mematuhi aturan yang diberlakukan di Negara Republik Indonesia terkait aturan penggunaan software berlisensi.</li>
        <li>Saya akan menggunakan software yang berlisensi pada <strong>{{ $log->aset->masterBarang->nama_barang }}</strong> dengan Serial Number = <strong>{{ $log->aset->serial_number }}</strong> milik perusahaan yang saya gunakan selama masa kerja di perusahaan.</li>
        <li>Saya akan menjaga keberadaan stiker lisensi yang ditempelkan pada PC DESKTOP milik perusahaan yang saya gunakan selama masa kerja di perusahaan. Jika terjadi kehilangan stiker maka menjadi tanggung jawab karyawan bersangkutan.</li>
        <li>List standard software yang ter-instal resmi terlampir, yang telah ditandatangani oleh user dan staf IT yang meng-instal software tersebut pada perangkat IT milik perusahaan. Apabila ada penambahan software berlisensi harus diketahui dan diinstall oleh IT.</li>
        <li>Segala penyalahgunaan dan penyimpangan yang terjadi dalam penggunaan aplikasi yang tidak berlisensi merupakan tindakan melawan hukum dan akan menjadi tanggung jawab saya pribadi dan saya bersedia untuk diproses sesuai hukum perundangan yang berlaku.</li>
        <li>Jika terjadi kehilangan PC DESKTOP oleh karyawan, maka biaya pembelian lisensi original yang sudah terinstal di PC DESKTOP bersangkutan akan dibebankan ke karyawan.</li>
    </ol>

    <p class="text-justify mt-4">
        Demikianlah surat pernyataan ini dibuat dengan penuh kesadaran dan tanpa tekanan ataupun paksaan dari pihak manapun, sekiranya agar surat pernyataan ini dipergunakan sebagaimana mestinya.
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