@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h4 class="mb-4 text-gray-800"><i class="fas fa-pencil-alt mr-2"></i>{{$title}}</h4>

    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('barangmasuk.update', $barangMasuk->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label>No Surat Jalan</label>
                    <input type="text" name="no_sj" value="{{ $barangMasuk->no_sj }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>No PPI</label>
                    <input type="text" name="no_ppi" value="{{ $barangMasuk->no_ppi }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>No PO</label>
                    <input type="text" name="no_po" value="{{ $barangMasuk->no_po }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Nama Barang</label>
                    <input type="text" name="nama_barang" value="{{ $barangMasuk->nama_barang }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    @php
                        $selectedKategori = explode(',', $barangMasuk->kategori);
                    @endphp
                    <select id="kategori" name="kategori[]" class="form-control select2" multiple="multiple" required>
                        @foreach ([
                            'Access Point','Analogue Telephone','Antena Radio Rig','Camera','Consumable','CCTV','Dongle',
                            'Extender','Fingerprint','GPS','Headset','Hard Disk Eksternal','IP Telephone','Keyboard',
                            'Laptop','Modem','Monitor','Mouse','Mobile Phone','PC Desktop','Power Supply','Proyektor',
                            'Print Server','Printer','Radio HT','Radio Rig','Router','Scanner','SSD Eksternal','Stavolt',
                            'Switch Hub','UPS','TV','Webcam'
                        ] as $kategori)
                            <option value="{{ $kategori }}" {{ in_array($kategori, $selectedKategori) ? 'selected' : '' }}>
                                {{ $kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Jumlah</label>
                    <input type="number" name="jumlah" value="{{ $barangMasuk->jumlah }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Masuk</label>
                    <input type="date" name="tanggal_masuk" value="{{ $barangMasuk->tanggal_masuk }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="3">{{ $barangMasuk->keterangan }}</textarea>
                </div>

                <button type="submit" class="btn btn-success">Update</button>
                <a href="{{ route('barangmasuk.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

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
