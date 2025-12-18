<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Aset - {{ $aset->kode_asset }}</title>
    <style>
        /* 1. SETUP HALAMAN */
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: #2c3e50;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        /* 2. PEMBUNGKUS UTAMA */
        .sticker-wrapper {
            background-color: #ffe600;
            width: 480px; 
            padding: 8px; 
            box-shadow: 0 0 25px rgba(0,0,0,0.5);
            box-sizing: border-box;
        }

        /* 3. FRAME HITAM TEBAL */
        .inner-box {
            border: 3px solid #000;
            display: flex;
            flex-direction: column;
            background: #ffe600;
            height: 180px;
        }

        /* 4. BAGIAN ATAS */
        .top-section {
            display: flex;
            border-bottom: 3px solid #000;
            flex-grow: 1;
            overflow: hidden;
        }

        /* --- KOLOM KIRI: BARCODE --- */
        .area-barcode {
            flex: 70%;
            border-right: 3px solid #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            padding: 5px 5px 5px 12px; 
        }

        .barcode-container {
            width: 100%;
            height: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 5px;
        }

        .barcode-container > div, 
        .barcode-container > img, 
        .barcode-container > svg {
            width: 85% !important;
            height: 100% !important;
            object-fit: contain;
            mix-blend-mode: multiply;
            display: block;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        .text-kode {
            font-family: 'Courier New', Courier, monospace;
            font-weight: 900;
            font-size: 26px;
            letter-spacing: 2px;
            color: #000;
            line-height: 1;
            margin-top: 5px;
            text-align: center;
        }

        /* --- KOLOM KANAN: INFO --- */
        .area-info {
            flex: 30%;
            display: flex;
            flex-direction: column;
            align-items: center; 
            text-align: center;
            padding: 5px;
            background-color: #ffe600;
        }

        .pt-name { 
            font-size: 14px; 
            font-weight: 900; 
            text-transform: uppercase;
            border-bottom: 2px solid #000;
            padding-bottom: 2px;
            margin-bottom: 5px;
            width: 90%;
        }

        .dept-name { 
            font-size: 10px; 
            font-weight: bold; 
            margin-bottom: 8px;
        }
        
        .nama-barang { 
            font-size: 11px; 
            font-weight: bold;
            font-style: italic;
            line-height: 1.1;
            max-width: 95%;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* --- UPDATE STYLE LOGO --- */
        .logo-img {
            margin-top: auto; /* Dorong ke bawah */
            
            /* 1. PERBESAR UKURAN */
            width: auto;
            max-width: 95%;   /* Mentok lebar kolom */
            max-height: 60px; /* Tinggi dinaikkan jadi 60px (sebelumnya 40px) */
            object-fit: contain;
            margin-bottom: 8px;

            /* 2. UBAH JADI HITAM */
            /* Ubah jadi grayscale dulu */
            filter: grayscale(100%) contrast(120%); 
            /* Blend dengan background kuning agar putih jadi transparan dan gelap jadi hitam */
            mix-blend-mode: multiply; 
        }

        /* 5. FOOTER (WARNING) */
        .footer-warning {
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #ffe600;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #000;
        }

        .btn-print {
            margin-top: 20px;
            padding: 12px 30px;
            background: #fff;
            border: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            color: #333;
            display: inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
                display: block;
            }
            .btn-print, center { display: none; }
            
            .sticker-wrapper {
                box-shadow: none;
                width: 100%;
                max-width: 480px;
                background-color: #ffe600 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                position: absolute;
                top: 5px; left: 5px;
            }
        }
    </style>
</head>
<body>

    <div>
        <div class="sticker-wrapper">
            <div class="inner-box">
                
                <div class="top-section">
                    
                    <div class="area-barcode">
                        <div class="barcode-container">
                            {!! $barcode !!}
                        </div>
                        <div class="text-kode">{{ $aset->kode_asset }}</div>
                    </div>

                    <div class="area-info">
                        <div class="pt-name">PT. SILO</div>
                        <div class="dept-name">IT & COMM</div>
                        
                        <div class="nama-barang">
                            {{ $aset->masterBarang->nama_barang }}
                        </div>

                        <img src="{{ asset('image/images.png') }}" 
                             alt="Logo" 
                             class="logo-img">
                             
                    </div>
                </div>

                <div class="footer-warning">
                    Dilarang Melepas Label Asset Ini
                </div>
            </div>
        </div>

        <center>
            <a href="#" onclick="window.print()" class="btn-print">
                🖨️ Cetak Label
            </a>
        </center>
    </div>

</body>
</html>