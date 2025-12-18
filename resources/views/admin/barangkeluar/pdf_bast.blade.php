<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Times New Roman', sans-serif; font-size: 11pt; margin: 0; padding: 0; }
        
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; text-transform: uppercase; font-size: 14pt; font-weight: bold; }
        .header p { margin: 2px; font-size: 10pt; }
        
        .judul { text-align: center; font-weight: bold; margin-bottom: 20px; font-size: 12pt; text-decoration: underline; text-transform: uppercase; }
        
        /* Layout Informasi Pihak */
        .table-info { width: 100%; margin-bottom: 10px; }
        .table-info td { vertical-align: top; padding: 2px; }

        /* Tabel Detail Barang */
        .table-barang { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 15px; }
        .table-barang th, .table-barang td { border: 1px solid #000; padding: 6px; }
        .table-barang th { background: #eee; text-align: center; font-weight: bold; }

        /* Checklist Box (Khusus Laptop/Radio) */
        .checklist-box { border: 1px solid #000; padding: 10px; margin-top: 10px; font-size: 10pt; }
        .checklist-item { display: inline-block; width: 32%; margin-bottom: 5px; }
        .square { display: inline-block; width: 12px; height: 12px; border: 1px solid #000; margin-right: 5px; vertical-align: middle; }

        /* Area Tanda Tangan */
        .ttd-wrapper { margin-top: 40px; width: 100%; }
        .ttd-box { width: 45%; float: left; text-align: center; }
        .ttd-box.right { float: right; }
        
        /* Gambar TTD - Pastikan Height fix biar rapi */
        .ttd-img { height: 70px; width: auto; display: block; margin: 5px auto; }
        .ttd-space { height: 80px; } /* Space kosong kalau belum TTD */
        
        .footer { font-size: 9pt; font-style: italic; margin-top: 30px; text-align: right; border-top: 1px solid #ccc; padding-top: 5px; clear: both; }
    </style>
</head>
<body>
    @php
        // 1. Ambil Kategori dan normalize ke huruf kecil
        $kategori = strtolower($log->aset->masterBarang->kategori ?? '');

        // 2. Logic Cek Laptop/PC
        $isLaptop = (str_contains($kategori, 'laptop') || str_contains($kategori, 'pc') || str_contains($kategori, 'computer'));

        // 3. Logic Cek Radio/HT (Handie Talkie)
        $isRadio = (str_contains($kategori, 'radio') || str_contains($kategori, 'ht') || str_contains($kategori, 'walkie') || str_contains($kategori, 'rig'));
        
        // 4. Tentukan Judul Dokumen
        $jenisAset = 'ASET';
        if($isLaptop) $jenisAset = 'PERANGKAT IT';
        if($isRadio)  $jenisAset = 'PERANGKAT RADIO';
    @endphp

    <div class="header">
        <h2>PT. NAMA PERUSAHAAN</h2>
        <p>Jl. Contoh No. 123, Jakarta Selatan | Telp: (021) 555-666</p>
        <p>Email: it-support@perusahaan.com</p>
    </div>

    <div class="judul">
        BERITA ACARA SERAH TERIMA {{ $jenisAset }}
    </div>

    <div class="content">
        <p>Pada hari ini <strong>{{ \Carbon\Carbon::parse($log->tanggal_serah_terima)->translatedFormat('l, d F Y') }}</strong>, kami yang bertanda tangan di bawah ini:</p>

        <table class="table-info">
            <tr>
                <td width="30">1.</td>
                <td width="100">Nama</td>
                <td>: <strong>{{ $log->admin->nama ?? 'Admin IT' }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td>Jabatan</td>
                <td>: IT Support</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">Selanjutnya disebut sebagai <strong>PIHAK PERTAMA</strong> (Yang Menyerahkan).</td>
            </tr>
        </table>
        
        <br>

        <table class="table-info">
            <tr>
                <td width="30">2.</td>
                <td width="100">Nama</td>
                <td>: <strong>{{ $log->pemegang->nama ?? 'User' }}</strong></td>
            </tr>
            <tr>
                <td></td>
                <td>Departemen</td>
                <td>: {{ $log->pemegang->departemen ?? '-' }} ({{ $log->pemegang->perusahaan ?? '-' }})</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="2">Selanjutnya disebut sebagai <strong>PIHAK KEDUA</strong> (Yang Menerima).</td>
            </tr>
        </table>

        <p>PIHAK PERTAMA menyerahkan barang kepada PIHAK KEDUA dengan rincian:</p>

        <table class="table-barang">
            <thead>
                <tr>
                    <th>Kode Aset</th>
                    <th>Nama Barang / Model</th>
                    <th>Serial Number (SN)</th>
                    <th>Kondisi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center">{{ $log->aset->kode_asset }}</td>
                    <td>
                        {{ $log->aset->masterBarang->nama_barang ?? '-' }}<br>
                        <small>{{ $log->aset->masterBarang->spesifikasi ?? '-' }}</small>
                    </td>
                    <td align="center">{{ $log->aset->serial_number ?? '-' }}</td>
                    <td align="center">{{ ucfirst($log->kondisi_saat_serah) }}</td>
                </tr>
            </tbody>
        </table>

        {{-- ======================================================== --}}
        {{-- LOGIC CHECKLIST BERDASARKAN KATEGORI --}}
        {{-- ======================================================== --}}

        @if($isLaptop)
            <div class="checklist-box">
                <strong>Kelengkapan Unit (Harap Diperiksa):</strong><br><br>
                
                <div class="checklist-item"><span class="square"></span> Unit Laptop/PC</div>
                <div class="checklist-item"><span class="square"></span> Adaptor Charger</div>
                <div class="checklist-item"><span class="square"></span> Kabel Power</div>
                <div class="checklist-item"><span class="square"></span> Mouse</div>
                <div class="checklist-item"><span class="square"></span> Tas Laptop</div>
                <div class="checklist-item"><span class="square"></span> Kartu Garansi</div>
                
                <br><br>
                <strong>Syarat & Ketentuan Penggunaan Perangkat IT:</strong>
                <ol style="margin-top: 5px; padding-left: 15px;">
                    <li>PIHAK KEDUA bertanggung jawab penuh atas keamanan fisik perangkat.</li>
                    <li>Dilarang menginstal software ilegal/bajakan pada perangkat ini.</li>
                    <li>Segala data perusahaan di dalam perangkat bersifat rahasia.</li>
                    <li>Jika terjadi kerusakan akibat kelalaian (jatuh, kena air), biaya perbaikan dibebankan kepada PIHAK KEDUA.</li>
                </ol>
            </div>

        @elseif($isRadio)
            <div class="checklist-box">
                <strong>Kelengkapan Radio Komunikasi (HT):</strong><br><br>
                
                <div class="checklist-item"><span class="square"></span> Unit HT</div>
                <div class="checklist-item"><span class="square"></span> Antena</div>
                <div class="checklist-item"><span class="square"></span> Baterai</div>
                <div class="checklist-item"><span class="square"></span> Belt Clip</div>
                <div class="checklist-item"><span class="square"></span> Desktop Charger</div>
                <div class="checklist-item"><span class="square"></span> Handstrap</div>
                
                <br><br>
                <strong>Ketentuan Penggunaan Radio:</strong>
                <ol style="margin-top: 5px; padding-left: 15px;">
                    <li>Gunakan frekuensi perusahaan yang telah ditentukan.</li>
                    <li>Dilarang menggunakan untuk percakapan pribadi atau SARA.</li>
                    <li>Pastikan unit dimatikan saat sedang di-charge.</li>
                    <li>Jagalah antena agar tidak patah/bengkok.</li>
                </ol>
            </div>

        @else
            <p style="text-align: justify;">
                Barang tersebut telah diterima dalam keadaan baik, berfungsi normal, dan lengkap. 
                PIHAK KEDUA bertanggung jawab untuk merawat barang tersebut dan mengembalikannya 
                apabila sudah tidak digunakan atau terjadi mutasi/resign.
            </p>
        @endif
    </div>

    <div class="ttd-wrapper">
        
        <div class="ttd-box">
            <p>PIHAK PERTAMA</p>
            @if(!empty($log->ttd_petugas))
                @php
                    $imgPetugas = $log->ttd_petugas;
                    // Hapus enter/spasi yg bikin error
                    $imgPetugas = str_replace(["\r", "\n", " "], "", $imgPetugas);
                    // Cek prefix base64
                    if (!str_contains($imgPetugas, 'data:image')) {
                         $imgPetugas = 'data:image/png;base64,' . $imgPetugas;
                    }
                @endphp
                <img src="{{ $imgPetugas }}" class="ttd-img">
            @else
                <div class="ttd-space"></div>
            @endif
            <p><u>{{ $log->admin->nama ?? 'Admin IT' }}</u></p>
        </div>

        <div class="ttd-box right">
            <p>PIHAK KEDUA</p>
            @if(!empty($log->ttd_penerima))
                @php
                    $imgPenerima = $log->ttd_penerima;
                    // Hapus enter/spasi yg bikin error
                    $imgPenerima = str_replace(["\r", "\n", " "], "", $imgPenerima);
                    // Cek prefix base64
                    if (!str_contains($imgPenerima, 'data:image')) {
                         $imgPenerima = 'data:image/png;base64,' . $imgPenerima;
                    }
                @endphp
                <img src="{{ $imgPenerima }}" class="ttd-img">
            @else
                <div class="ttd-space"></div>
            @endif
            <p><u>{{ $log->pemegang->nama ?? 'User' }}</u></p>
        </div>
    </div>

    <div class="footer">
        Dicetak otomatis dari Sistem IT Asset pada: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>