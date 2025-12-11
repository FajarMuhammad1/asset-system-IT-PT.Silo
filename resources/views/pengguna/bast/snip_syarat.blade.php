@if ($kategori == 'Radio Rig')
<ul>
    <li>Pemegang wajib menjaga alat dan menggunakannya sesuai SOP komunikasi perusahaan.</li>
    <li>Tidak boleh memodifikasi frekuensi tanpa izin IT.</li>
    <li>Bertanggung jawab atas kehilangan atau kerusakan.</li>
</ul>

@elseif ($kategori == 'Laptop')
<ul>
    <li>Pemegang wajib menjaga kerahasiaan data.</li>
    <li>Dilarang menginstall software ilegal.</li>
    <li>Dilarang meminjamkan ke orang lain.</li>
</ul>

@else
<ul>
    <li>Pemegang bertanggung jawab atas barang ini.</li>
</ul>
@endif
