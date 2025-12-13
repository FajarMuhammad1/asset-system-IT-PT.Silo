<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Label Normal - {{ $aset->kode_asset }}</title>
    <style>
        /* 1. RESET & BACKGROUND */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #2c3e50; /* Background layar gelap */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #000;
        }

        /* 2. DESAIN STIKER (UKURAN NORMAL) */
        .label-sticker {
            background-color: #f1c40f; /* Kuning */
            width: 350px; /* <--- KEMBALI NORMAL */
            padding: 20px;
            border: 1px solid #ccc;
            text-align: center;
            box-shadow: 0 0 30px rgba(0,0,0,0.5); 
            border-radius: 8px;
            box-sizing: border-box;
            position: relative;
        }

        .header-pt {
            font-size: 14px; /* Font Header Normal */
            font-weight: 800;
            text-transform: uppercase;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            letter-spacing: 1px;
        }

        .nama-barang {
            font-size: 18px; /* Nama Barang Standar */
            font-weight: bold;
            margin: 12px 0;
            line-height: 1.2;
            text-transform: uppercase;
            white-space: nowrap; 
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* 3. AREA BARCODE (PUTIH & NORMAL) */
        .barcode-box {
            background: #f1c40f; /* Wajib Putih */
            padding: 10px;
            border-radius: 5px; 
            display: inline-block;
            border: 2px solid #000;
            min-width: 80%;
            margin: 5px auto;
        }

        /* RESET: Tidak ada Zoom/Scale lagi */
        .barcode-box > div, .barcode-box > svg, .barcode-box > img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
            
            /* Tetap jaga ketajaman garis */
            image-rendering: pixelated; 
        }

        .kode-text {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 20px; /* Ukuran Kode Pas */
            margin-top: 10px;
            letter-spacing: 2px;
            color: #000;
        }

        .tgl-masuk {
            font-size: 11px;
            margin-top: 10px;
            font-weight: bold;
            color: #333;
        }

        /* BUTTON PRINT */
        .btn-print {
            margin-top: 25px;
            padding: 10px 25px;
            background: #fff;
            color: #2c3e50;
            font-weight: bold;
            border: none;
            cursor: pointer;
            border-radius: 50px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            transition: transform 0.2s;
        }

        .btn-print:hover {
            transform: scale(1.05);
            background: #ecf0f1;
        }

        /* PRINT SETTINGS */
        @media print {
            body {
                background: white;
                display: block;
            }
            .btn-print { display: none; }
            
            .label-sticker {
                /* Reset posisi ke pojok kiri atas saat print */
                position: absolute;
                top: 0; left: 0;
                margin: 0;
                
                box-shadow: none;
                border: 1px solid #000;
                
                /* Print Warna Wajib Nyala */
                background-color: #f1c40f !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .barcode-box {
                background-color: #ffffff !important;
                border: 2px solid #000 !important;
            }
        }
    </style>
</head>
<body>

    <div>
        <div class="label-sticker">
            <div class="header-pt">Inventory IT&Comm - PT SILO</div>
            
            <div class="nama-barang">
                {{ $aset->masterBarang->nama_barang }}
            </div>

            <div class="barcode-box">
                {!! $barcode !!}
            </div>

            <div class="kode-text">{{ $aset->kode_asset }}</div>
            
            <div class="tgl-masuk">
                Tgl Masuk: {{ \Carbon\Carbon::parse($aset->tanggal_masuk)->format('d/m/Y') }}
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