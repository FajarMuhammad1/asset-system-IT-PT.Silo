<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Cetak Laporan Kepuasan Pengguna' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #fff; color: #000; font-family: Arial, sans-serif; padding: 30px; }
        .table th { vertical-align: middle !important; }
        .star-yellow { color: #f6c23e; }
        .star-gray   { color: #d1d3e2; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <!-- Header Laporan -->
    <div class="text-center mb-4">
        <h2 class="font-weight-bold text-uppercase">Laporan Kepuasan Pengguna Helpdesk IT</h2>
        <h5 class="text-muted">Sistem Manajemen Aset & Helpdesk IT Support</h5>
        @if($startDate && $endDate)
            <p class="small">Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</strong></p>
        @endif
        <hr style="border-top: 2px solid #000;">
    </div>

    <!-- Ringkasan Data -->
    <div class="row mb-4">
        <div class="col-6">
            <div class="border p-3 text-center rounded">
                <small class="text-muted d-block text-uppercase font-weight-bold">Total Responden</small>
                <span class="h4 font-weight-bold">{{ number_format($totalFeedback, 0, ',', '.') }} Tiket</span>
            </div>
        </div>
        <div class="col-6">
            <div class="border p-3 text-center rounded">
                <small class="text-muted d-block text-uppercase font-weight-bold">Rata-rata Rating</small>
                <div>
                    <span class="h4 font-weight-bold text-warning">{{ number_format($averageRating, 2, ',', '.') }}</span>
                    <small class="text-muted h5"> / 5.00</small>
                </div>
                <div class="mt-1">
                    @for($i=1;$i<=5;$i++)
                        <i class="fas fa-star {{ $i<=round($averageRating) ? 'star-yellow' : 'star-gray' }}"
                           style="font-size: 1.2rem;"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Utama -->
    <table class="table table-bordered table-sm">
        <thead class="thead-light text-center">
            <tr>
                <th width="5%">No</th>
                <th>No Tiket</th>
                <th>Judul / Masalah</th>
                <th>Pelapor</th>
                <th>Teknisi</th>
                <th>Tgl Selesai</th>
                <th>Rating</th>
                <th>Komentar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $tik)
            @php $r = $tik->feedback->rating ?? 0; @endphp
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-center font-weight-bold">{{ $tik->no_tiket }}</td>
                <td>{{ $tik->judul ?? $tik->masalah ?? '-' }}</td>
                <td>{{ $tik->pelapor->name ?? $tik->pelapor->nama ?? '-' }}</td>
                <td>{{ $tik->teknisi->name ?? $tik->teknisi->nama ?? '-' }}</td>
                <td class="text-center">{{ $tik->tgl_selesai ? \Carbon\Carbon::parse($tik->tgl_selesai)->format('d-m-Y H:i') : '-' }}</td>
                <td class="text-center">
                    @for($i=1;$i<=5;$i++)
                        @if($i<=$r)
                            <span class="star-yellow">&#9733;</span>
                        @else
                            <span class="star-gray">&#9734;</span>
                        @endif
                    @endfor
                    <div><small>({{ $r }}/5)</small></div>
                </td>
                <td>
                    @if(!empty($tik->feedback->komentar))
                        <small>{{ $tik->feedback->komentar }}</small>
                    @else
                        <small class="text-muted font-italic">-</small>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center text-muted py-3">Tidak ada data feedback untuk periode ini.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr class="font-weight-bold bg-light">
                <td colspan="5" class="text-right">TOTAL / RATA-RATA:</td>
                <td colspan="2" class="text-center text-warning">
                    {{ number_format($averageRating, 2, ',', '.') }} / 5.00
                </td>
                <td class="text-right">
                    {{ $totalFeedback }} Feedback
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan -->
    <div class="row mt-5 pt-4">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <p>Banjarmasin, {{ \Carbon\Carbon::now()->format('d F Y') }}</p>
            <p class="mb-5 pb-3">Petugas IT Support,</p>
            <p class="font-weight-bold text-underline">_______________________</p>
        </div>
    </div>

    <!-- Font Awesome for stars -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <!-- Script Otomatis Mengaktifkan Jendela Cetak Browser -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
