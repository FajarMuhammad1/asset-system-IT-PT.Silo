@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 text-gray-800"><i class="fas fa-plus m-2" ></i>{{ $title }}</h4>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('barangmasuk.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>No Surat Jalan</label>
                    <input type="text" name="no_sj" class="form-control" placeholder="Masukkan No Surat Jalan" required>
                </div>

                <div class="form-group">
                    <label>No PPI</label>
                    <input type="text" name="no_ppi" class="form-control" placeholder="Masukkan No PPI" required>
                </div>

                <div class="form-group">
                    <label>No PO</label>
                    <input type="text" name="no_po" class="form-control" placeholder="Masukkan No PO" required>
                </div>

                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" placeholder="Masukkan nama barang" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select id="kategori" name="kategori[]" class="form-control select2" multiple="multiple" data-placeholder="Pilih kategori..." required>
                        <option value="Access Point">Access Point</option>
                        <option value="Analogue Telephone">Analogue Telephone</option>
                        <option value="Antena Radio Rig">Antena Radio Rig</option>
                        <option value="Camera">Camera</option>
                        <option value="Consumable">Consumable</option>
                        <option value="CCTV">CCTV</option>
                        <option value="Dongle">Dongle</option>
                        <option value="Extender">Extender</option>
                        <option value="Fingerprint">Fingerprint</option>
                        <option value="GPS">GPS</option>
                        <option value="Headset">Headset</option>
                        <option value="Hard Disk Eksternal">Hard Disk Eksternal</option>
                        <option value="IP Telephone">IP Telephone</option>
                        <option value="Keyboard">Keyboard</option>
                        <option value="Laptop">Laptop</option>
                        <option value="Modem">Modem</option>
                        <option value="Monitor">Monitor</option>
                        <option value="Mouse">Mouse</option>
                        <option value="Mobile Phone">Mobile Phone</option>
                        <option value="PC Desktop">PC Desktop</option>
                        <option value="Power Supply">Power Supply</option>
                        <option value="Proyektor">Proyektor</option>
                        <option value="Print Server">Print Server</option>
                        <option value="Printer">Printer</option>
                        <option value="Radio HT">Radio HT</option>
                        <option value="Radio Rig">Radio Rig</option>
                        <option value="Router">Router</option>
                        <option value="Scanner">Scanner</option>
                        <option value="SSD Eksternal">SSD Eksternal</option>
                        <option value="Stavolt">Stavolt</option>
                        <option value="Switch Hub">Switch Hub</option>
                        <option value="UPS">UPS</option>
                        <option value="TV">TV</option>
                        <option value="Webcam">Webcam</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" class="form-control" placeholder="Masukkan jumlah barang" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

{{-- load select2 --}}
@push('scripts')
<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });
    });
</script>
@endpush
@endsection
