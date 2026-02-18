<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan PPI - {{ auth()->user()->nama ?? 'User' }}</title>
    <style>
        /* CSS adapted from Surat Jalan PDF */
        @page { margin: 20px 30px; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 9pt; 
            color: #333; 
            line-height: 1.4; 
        }
        
        /* HEADER - DIPERBAIKI AGAR CENTER */
        .header-table { 
            width: 100%; 
            border-bottom: 2px solid #000; 
            padding-bottom: 10px; 
            margin-bottom: 20px; 
            table-layout: fixed; /* Kunci agar kolom presisi */
        }
        .header-logo { 
            width: 15%; 
            vertical-align: middle; 
            text-align: left;
        }
        .header-text { 
            width: 70%; /* 70% agar imbang dengan kiri kanan */
            vertical-align: middle; 
            text-align: center; 
        }
        .header-dummy {
            width: 15%; /* Penyeimbang logo agar teks pas di tengah */
        }

        .header-text h2 { 
            margin: 0; 
            font-size: 16pt; 
            font-weight: bold; 
            color: #2c3e50; 
            text-transform: uppercase; 
            text-align: center; /* Paksa rata tengah */
            display: block;
        }
        .header-text p { 
            margin: 2px 0; 
            font-size: 9pt; 
            color: #555; 
            text-align: center; /* Paksa rata tengah */
            display: block;
        }

        /* TITLE */
        .doc-title { text-align: center; margin-bottom: 20px; }
        .doc-title h3 { 
            margin: 0; 
            font-size: 14pt; 
            text-transform: uppercase; 
            text-decoration: underline; 
        }
        .doc-meta { font-size: 8pt; text-align: center; margin-top: 5px; }

        /* TABLE */
        .table-data { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .table-data th, .table-data td { 
            border: 1px solid #444; 
            padding: 6px 8px; 
            vertical-align: middle; 
        }
        .table-data th { 
            background-color: #1a4d80; 
            color: #ffffff; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 8pt; 
            text-align: center; 
        }
        .table-data tr:nth-child(even) { background-color: #f2f2f2; }

        /* BADGES */
        .badge { 
            padding: 2px 6px; 
            border-radius: 4px; 
            font-size: 7pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            display: inline-block; 
        }
        .badge-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .badge-primary { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }

        /* UTILS */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }

        /* FOOTER */
        footer { 
            position: fixed; 
            bottom: -20px; 
            left: 0px; 
            right: 0px; 
            height: 30px; 
            font-size: 8pt; 
            color: #888; 
            border-top: 1px solid #ddd; 
            padding-top: 5px; 
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>
    {{-- HEADER DIPERBAIKI --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                <img src="{{ public_path('image/images.png') }}" alt="Logo Silo" style="width: 80px; height: auto;">
            </td>
            
            <td class="header-text" align="center">
                <h2>PT. SEBUKU IRON LATERITIC ORES</h2>
                <p>Departemen IT Support</p>
            </td>

            <td class="header-dummy"></td>
        </tr>
    </table>
    
    {{-- TITLE --}}
    <div class="doc-title">
        <h3>Laporan Riwayat Permintaan PPI</h3>
        <div class="doc-meta">
            Pemohon: <strong>{{ auth()->user()->nama ?? '-' }}</strong> | 
            Departemen: {{ auth()->user()->departemen ?? '-' }} |
            Periode: {{ $label ?? 'Semua Data' }}
        </div>
    </div>

    {{-- TABLE --}}
    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">No PPI</th>
                <th width="12%">Tanggal</th>
                <th width="20%">Perangkat</th>
                <th>Keluhan / Kerusakan</th>
                <th width="15%">Ket. Admin</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-bold">{{ $item->no_ppi }}</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($item->tanggal)) }}</td>
                <td>{{ $item->perangkat }}</td>
                <td>{{ $item->ba_kerusakan ?? '-' }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td class="text-center">
                    @if($item->status == 'selesai') 
                        <span class="badge badge-success">Selesai</span>
                    @elseif($item->status == 'pending') 
                        <span class="badge badge-warning">Pending</span>
                    @elseif($item->status == 'disetujui') 
                        <span class="badge badge-primary">Proses</span>
                    @elseif($item->status == 'ditolak') 
                        <span class="badge badge-danger">Ditolak</span>
                    @else 
                        <span class="badge">{{ $item->status }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada riwayat permintaan PPI.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <p style="font-size: 9pt;">
            Dicetak Tanggal: {{ now()->format('d F Y H:i') }}
        </p>
    </div>

    {{-- PAGE FOOTER --}}
    <footer>
        <table width="100%">
            <tr>
                <td align="left" width="50%"><i>Generated by System IT Support</i></td>
                <td align="right" width="50%">Hal <span class="page-number"></span></td>
            </tr>
        </table>
    </footer>
</body>
</html>