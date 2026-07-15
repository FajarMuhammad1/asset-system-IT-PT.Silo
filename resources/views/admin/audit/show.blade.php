@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">{{ $title }} ({{ $audit->status }})</h1>

    <div class="row mb-4">
        <!-- Ringkasan Kartu -->
        <div class="col-md-3"><div class="card border-left-primary shadow p-3">Total Scan: {{ $summary['total_scanned'] }}</div></div>
        <div class="col-md-3"><div class="card border-left-success shadow p-3">Cocok: {{ $summary['match'] }}</div></div>
        <div class="col-md-3"><div class="card border-left-warning shadow p-3">Posisi Salah: {{ $summary['mismatch'] }}</div></div>
        <div class="col-md-3"><div class="card border-left-danger shadow p-3">Hilang (Belum Scan): <span id="missing-count">Menghitung...</span></div></div>
    </div>

    @if($audit->status === 'On Progress')
    <!-- Form Scan (Pakai JS) -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form id="scanForm" class="form-inline">
                <input type="text" id="kode_asset" class="form-control mr-2" placeholder="Scan Barcode Aset..." required autofocus>
                <select id="condition" class="form-control mr-2">
                    <option value="Good">Bagus</option>
                    <option value="Damaged">Rusak</option>
                </select>
                <input type="text" id="current_location" class="form-control mr-2" placeholder="Lokasi Saat Ini (Fisik)">
                <button type="submit" class="btn btn-primary">Catat</button>
            </form>
            <div id="scan-feedback" class="mt-2"></div>
        </div>
    </div>
    @endif

    <!-- Tabel Hasil Scan -->
    <div class="card shadow">
        <div class="card-body">
            <table class="table table-bordered">
                <thead><tr><th>Kode</th><th>Nama Aset</th><th>Kondisi</th><th>Lokasi Fisik</th><th>Match?</th><th>Scanner</th></tr></thead>
                <tbody>
                    @foreach($audit->items->where('is_found', true) as $item)
                    <tr>
                        <td>{{ $item->aset->kode_asset }}</td>
                        <td>{{ $item->aset->masterBarang->nama_barang }}</td>
                        <td>{{ $item->condition }}</td>
                        <td>{{ $item->scanned_location }}</td>
                        <td>{!! $item->is_match ? '<span class="text-success">Yes</span>' : '<span class="text-danger">No (DB: '.$item->aset->lokasi_saat_ini.')</span>' !!}</td>
                        <td>{{ $item->scanner->nama }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            @if($audit->status === 'On Progress')
            <form action="{{ route('admin.audit.complete', $audit->id) }}" method="POST" class="text-right mt-3">
                @csrf
                <button type="submit" class="btn btn-danger" onclick="return confirm('Selesaikan audit? Aset yang tidak discan akan dianggap hilang.')">Selesaikan & Tutup Sesi</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('#scanForm').submit(function(e){
        e.preventDefault();
        let data = {
            _token: "{{ csrf_token() }}",
            kode_asset: $('#kode_asset').val(),
            condition: $('#condition').val(),
            current_location: $('#current_location').val(),
        };

        $.ajax({
            url: "{{ route('admin.audit.scan', $audit->id) }}",
            type: "POST",
            data: data,
            success: function(resp){
                $('#scan-feedback').html('<div class="alert alert-success">'+resp.success+' ('+resp.data.nama+')</div>');
                $('#kode_asset').val('').focus(); // Reset input
                // Reload halaman untuk update tabel (cara simple) atau pakai JS untuk append row baru
                setTimeout(()=> location.reload(), 1000);
            },
            error: function(err){
                let msg = err.responseJSON ? err.responseJSON.error : 'Terjadi kesalahan';
                $('#scan-feedback').html('<div class="alert alert-danger">'+msg+'</div>');
                $('#kode_asset').val('').focus();
            }
        });
    });
</script>
@endpush