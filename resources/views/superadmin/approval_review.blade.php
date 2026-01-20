@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <a href="{{ route('superadmin.approval.index') }}" class="btn btn-secondary btn-sm mb-3">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="m-0 font-weight-bold">Review Permintaan PPI: {{ $ppi->no_ppi }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <tr>
                            <th width="30%">Nama Pemohon</th>
                            <td>{{ $ppi->user->nama }}</td>
                        </tr>
                        <tr>
                            <th>Departemen / PT</th>
                            <td>{{ $ppi->user->departemen }} / {{ $ppi->user->perusahaan }}</td>
                        </tr>
                        <tr>
                            <th>Perangkat Diminta</th>
                            <td class="font-weight-bold text-dark">{{ $ppi->perangkat }}</td>
                        </tr>
                        <tr>
                            <th>Spesifikasi</th>
                            <td>{{ $ppi->spesifikasi }}</td>
                        </tr>
                        <tr>
                            <th>Keperluan</th>
                            <td>{{ $ppi->keterangan }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-5 border-left-primary">
                <div class="card-body">
                    <h5 class="font-weight-bold text-gray-800 mb-3">Keputusan Approval</h5>
                    <p class="text-muted small">Silakan tanda tangan di kotak di bawah ini jika menyetujui permintaan.</p>

                    <form action="{{ route('superadmin.approval.approve', $ppi->id) }}" method="POST" id="formApprove">
                        @csrf @method('PUT')
                        
                        <div class="wrapper-signature mb-3" style="border: 2px dashed #ccc; background: #fdfdfd; position: relative;">
                            <canvas id="signature-pad" class="signature-pad" style="width: 100%; height: 200px; touch-action: none;"></canvas>
                            <div class="text-muted text-center small position-absolute" style="bottom: 5px; width: 100%; pointer-events: none;">Area Tanda Tangan</div>
                        </div>

                        <input type="hidden" name="ttd_superadmin" id="ttd_image">

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <button type="button" class="btn btn-secondary btn-block" id="clear">
                                    <i class="fas fa-eraser"></i> Hapus TTD
                                </button>
                            </div>
                            <div class="col-md-6 mb-2">
                                <button type="submit" class="btn btn-success btn-block btn-lg" onclick="return saveSignature()">
                                    <i class="fas fa-check-circle"></i> SETUJUI & TANDA TANGAN
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr>

                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#modalTolak">
                        <i class="fas fa-times-circle"></i> TOLAK PERMINTAAN
                    </button>

                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Tolak Permintaan</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('superadmin.approval.reject', $ppi->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="alasan_tolak" class="form-control" rows="3" required placeholder="Contoh: Stok barang habis, atau spesifikasi terlalu tinggi."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var canvas = document.getElementById('signature-pad');
        
        // Atur resolusi canvas agar tidak pecah di layar HP
        function resizeCanvas() {
            var ratio =  Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
        }
        window.onresize = resizeCanvas;
        resizeCanvas();

        var signaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgba(255, 255, 255, 0)' // Transparan
        });

        // Tombol Hapus
        document.getElementById('clear').addEventListener('click', function () {
            signaturePad.clear();
        });

        // Fungsi Simpan ke Input Hidden
        window.saveSignature = function() {
            if (signaturePad.isEmpty()) {
                alert("Silakan tanda tangan terlebih dahulu!");
                return false;
            }
            
            // Masukkan data gambar base64 ke input hidden
            var data = signaturePad.toDataURL('image/png');
            document.getElementById('ttd_image').value = data;
            
            return confirm('Apakah Anda yakin menyetujui dokumen ini?');
        };
    });
</script>
@endsection