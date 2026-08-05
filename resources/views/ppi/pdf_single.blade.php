<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dokumen PPI - {{ $ppi->no_ppi }}</title>
    <style>
        @page {
            margin: 25px 35px;
        }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #333; 
            line-height: 1.4; 
        }
        
        /* HEADER TABLE */
        .header-table { 
            width: 100%; 
            border-bottom: 2px solid #000; 
            padding-bottom: 8px; 
            margin-bottom: 20px; 
            table-layout: fixed;
        }
        .header-logo { 
            width: 15%; 
            vertical-align: middle; 
            text-align: left;
        }
        .header-text { 
            width: 70%; 
            vertical-align: middle; 
            text-align: center; 
        }
        .header-dummy {
            width: 15%;
        }

        .header-text h2 { 
            margin: 0; 
            font-size: 15pt; 
            font-weight: bold; 
            color: #1a4d80; 
            text-transform: uppercase; 
        }
        .header-text p { 
            margin: 2px 0; 
            font-size: 9pt; 
            color: #555; 
        }

        /* DOCUMENT TITLE */
        .doc-title { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .doc-title h3 { 
            margin: 0; 
            font-size: 13pt; 
            text-transform: uppercase; 
            text-decoration: underline; 
            color: #2c3e50;
        }
        .doc-title p {
            margin: 3px 0 0 0;
            font-size: 9pt;
            color: #666;
        }

        /* DATA TABLE */
        .table-info { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
        }
        .table-info th, .table-info td { 
            border: 1px solid #ccc; 
            padding: 8px 10px; 
            vertical-align: top; 
        }
        .table-info th { 
            background-color: #f4f6f9; 
            color: #333; 
            font-weight: bold; 
            font-size: 9pt; 
            text-transform: uppercase;
        }

        .bg-section {
            background-color: #1a4d80 !important;
            color: #ffffff !important;
            font-weight: bold;
            font-size: 9.5pt;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* BADGES */
        .badge { 
            padding: 3px 8px; 
            border-radius: 4px; 
            font-size: 8pt; 
            font-weight: bold; 
            text-transform: uppercase; 
            display: inline-block; 
        }
        .badge-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .badge-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-danger { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .badge-primary { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .badge-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

        /* SIGNATURE SECTION */
        .signature-table { 
            width: 100%; 
            margin-top: 30px; 
            border-collapse: collapse; 
        }
        .signature-table th { 
            border: 1px solid #333; 
            padding: 6px; 
            background: #f0f4f8; 
            font-size: 9pt; 
            text-align: center;
            text-transform: uppercase;
        }
        .signature-table td { 
            border: 1px solid #333; 
            padding: 10px; 
            text-align: center; 
            vertical-align: bottom; 
            height: 110px; 
        }

        .sig-image {
            max-height: 75px;
            width: auto;
            max-width: 180px;
            display: block;
            margin: 0 auto;
        }

        /* FOOTER */
        footer { 
            position: fixed; 
            bottom: -15px; 
            left: 0px; 
            right: 0px; 
            height: 25px; 
            font-size: 8pt; 
            color: #888; 
            border-top: 1px solid #ddd; 
            padding-top: 4px; 
        }
        .page-number:after { content: counter(page); }
    </style>
</head>
<body>

    @php
        function formatTtdSrc($ttd) {
            if (empty($ttd)) return null;
            if (\Illuminate\Support\Str::startsWith($ttd, 'data:image')) {
                return $ttd;
            }
            $fullPath = public_path($ttd);
            if (\Illuminate\Support\Facades\File::exists($fullPath)) {
                return $fullPath;
            }
            return asset($ttd);
        }
        $srcPemohon = formatTtdSrc($ppi->ttd_pemohon);
        $srcSuperAdmin = formatTtdSrc($ppi->ttd_superadmin);
    @endphp

    {{-- KOP SURAT --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(file_exists(public_path('image/images.png')))
                    <img src="{{ public_path('image/images.png') }}" alt="Logo" style="width: 75px; height: auto;">
                @else
                    <strong style="font-size: 14pt; color: #1a4d80;">PT. SILO</strong>
                @endif
            </td>
            
            <td class="header-text">
                <h2>PT. SEBUKU IRON LATERITIC ORES</h2>
                <p>Departemen IT Support & Communication System</p>
            </td>

            <td class="header-dummy"></td>
        </tr>
    </table>
    
    {{-- JUDUL DOKUMEN --}}
    <div class="doc-title">
        <h3>FORM PERMINTAAN PERBAIKAN / ITEM (PPI)</h3>
        <p>No Dokumen: <strong>{{ $ppi->no_ppi }}</strong></p>
    </div>

    {{-- TABEL INFORMASI PPI --}}
    <table class="table-info">
        <tr>
            <td colspan="4" class="bg-section">I. INFORMASI PENGAJUAN & PEMOHON</td>
        </tr>
        <tr>
            <th width="20%">No. Tiket PPI</th>
            <td width="30%"><strong>{{ $ppi->no_ppi }}</strong></td>
            <th width="20%">Tanggal Pengajuan</th>
            <td width="30%">{{ \Carbon\Carbon::parse($ppi->created_at)->translatedFormat('d F Y, H:i') }} WIB</td>
        </tr>
        <tr>
            <th>Nama Pemohon</th>
            <td><strong>{{ $ppi->user->nama ?? '-' }}</strong></td>
            <th>NIK / Jabatan</th>
            <td>{{ $ppi->user->nik ?? '-' }} / {{ $ppi->user->jabatan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Departemen</th>
            <td>{{ $ppi->user->departemen ?? '-' }}</td>
            <th>Perusahaan</th>
            <td>{{ $ppi->user->perusahaan ?? '-' }}</td>
        </tr>
        <tr>
            <th>Status Dokumen</th>
            <td colspan="3">
                @if($ppi->status == 'pending') 
                    <span class="badge badge-info">Menunggu Cek Admin</span>
                @elseif($ppi->status == 'pending_superadmin') 
                    <span class="badge badge-warning">Menunggu Approval Super Admin</span>
                @elseif($ppi->status == 'disetujui') 
                    <span class="badge badge-primary">Disetujui (Sedang Diproses)</span>
                @elseif($ppi->status == 'selesai') 
                    <span class="badge badge-success">Selesai (Closed)</span>
                @elseif($ppi->status == 'ditolak') 
                    <span class="badge badge-danger">Ditolak</span>
                @else 
                    <span class="badge">{{ strtoupper($ppi->status) }}</span>
                @endif
            </td>
        </tr>

        <tr>
            <td colspan="4" class="bg-section">II. RINCIAN PERMINTAAN & SPESIFIKASI</td>
        </tr>
        <tr>
            <th>Perangkat / Aset</th>
            <td colspan="3"><strong>{{ $ppi->perangkat }}</strong></td>
        </tr>
        <tr>
            <th>Deskripsi Kerusakan / Keperluan</th>
            <td colspan="3" style="min-height: 60px;">
                {!! nl2br(e($ppi->ba_kerusakan)) !!}
            </td>
        </tr>
        @if($ppi->keterangan)
        <tr>
            <th>Keterangan Tambahan</th>
            <td colspan="3">
                {{ $ppi->keterangan }}
            </td>
        </tr>
        @endif
        @if($ppi->status == 'ditolak' && $ppi->alasan_tolak)
        <tr>
            <th>Alasan Penolakan</th>
            <td colspan="3" style="color: #721c24; background-color: #f8d7da;">
                <strong>{{ $ppi->alasan_tolak }}</strong>
            </td>
        </tr>
        @endif
    </table>

    {{-- TABEL TANDA TANGAN (PEMOHON & SUPER ADMIN APPROVAL) --}}
    <table class="signature-table">
        <tr>
            <th width="50%">Yang Meminta (Pemohon)</th>
            <th width="50%">Disetujui Oleh (Super Admin)</th>
        </tr>
        <tr>
            <td>
                @if($srcPemohon)
                    <img src="{{ $srcPemohon }}" class="sig-image" alt="TTD Pemohon">
                @else
                    <br><br><span style="color: #999; font-style: italic;">(Belum Tanda Tangan)</span><br><br>
                @endif
                <br>
                <strong style="text-decoration: underline;">{{ $ppi->user->nama ?? 'Pemohon' }}</strong><br>
                <span style="font-size: 8.5pt; color: #555;">{{ $ppi->user->jabatan ?? 'User' }} - {{ $ppi->user->departemen ?? '' }}</span>
            </td>
            <td>
                @if($srcSuperAdmin && in_array($ppi->status, ['disetujui', 'selesai']))
                    <img src="{{ $srcSuperAdmin }}" class="sig-image" alt="TTD Super Admin">
                    <br>
                    <strong style="text-decoration: underline;">Super Admin / Management</strong><br>
                    <span style="font-size: 8.5pt; color: #555;">
                        Approved: {{ $ppi->tgl_approve ? \Carbon\Carbon::parse($ppi->tgl_approve)->format('d/m/Y H:i') : date('d/m/Y') }}
                    </span>
                @elseif($ppi->status == 'ditolak')
                    <br><br><span style="color: #c9302c; font-weight: bold;">(DITOLAK)</span><br><br>
                    <strong style="text-decoration: underline;">Super Admin / Management</strong>
                @else
                    <br><br><span style="color: #999; font-style: italic;">(Menunggu Approval)</span><br><br>
                    <strong style="text-decoration: underline;">Super Admin / Management</strong>
                @endif
            </td>
        </tr>
    </table>

    <div style="margin-top: 25px; font-size: 8.5pt; color: #666;">
        <p><em>* Dokumen ini dibuat dan dikelola secara digital melalui Sistem IT Support Asset PT. Sebuku Iron Lateritic Ores.</em></p>
    </div>

    {{-- PAGE FOOTER --}}
    <footer>
        <table width="100%">
            <tr>
                <td align="left" width="60%"><i>IT Support System - Document #{{ $ppi->no_ppi }}</i></td>
                <td align="right" width="40%">Hal <span class="page-number"></span></td>
            </tr>
        </table>
    </footer>
</body>
</html>
