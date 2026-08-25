
    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up">{{ $title }}</i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Handak keluar kah pian ?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Klik "Logout" kalo pian handak keluar.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="{{ route('logout') }}">Logout</a>
                </div>
            </div>
        </div>
    </div>

      

  <!-- Bootstrap core JavaScript -->
<script src="{{ asset('sbadmin2/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('sbadmin2/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('sbadmin2/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- Custom scripts for all pages -->
<script src="{{ asset('sbadmin2/js/sb-admin-2.min.js') }}"></script>

<!-- SweetAlert -->
<script src="{{ asset('sweetalert2/dist/sweetalert2.all.min.js') }}"></script>

<!-- DataTables -->
<script src="{{ asset('sbadmin2/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('sbadmin2/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<!-- Page level custom scripts -->
<script src="{{ asset('sbadmin2/js/demo/datatables-demo.js') }}"></script>
<!-- Tambahkan di bawah sebelum </body> -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#kategori').select2({
            theme: 'classic',
            width: '100%',
            allowClear: true
        });
    });
</script>


     @if(session('success'))
    <script>
        Swal.fire({
            title: "Berhasil",
            text: "{!! session('success') !!}",
            icon: "success"
        });
    </script>
@endif

@if(session('error'))
    <script>
        Swal.fire({
            title: "Pemberitahuan",
            text: "{!! session('error') !!}",
            icon: "error"
        });
    </script>
@endif

{{-- ========================================================
     SCRIPT GLOBAL: SWEETALERT KONFIRMASI CETAK / EXPORT
     Tombol tinggal tambahkan class: btn-cetak-swal ATAU btn-export-swal
     + atribut: data-url, data-title, data-desc, data-icon (opsional), 
                data-confirm (opsional), data-target (opsional: _blank)
     ======================================================== --}}
<script>
$(document).ready(function() {

    /** Helper: Buka URL sesuai target */
    function __bukaUrl(url, target) {
        if (target === '_blank') {
            window.open(url, '_blank', 'noopener');
        } else {
            window.location.href = url;
        }
    }

    /** --- Konfirmasi CETAK (PDF / Print Window) --- */
    $(document).on('click', '.btn-cetak-swal', function(e) {
        e.preventDefault();
        var btn    = $(this);
        var url    = btn.data('url')     || '#';
        var title  = btn.data('title')   || 'Konfirmasi Cetak Laporan';
        var desc   = btn.data('desc')    || 'Anda yakin ingin mencetak laporan ini?';
        var icon   = btn.data('icon')    || 'info';
        var okText = btn.data('confirm') || 'Ya, Cetak Sekarang';
        var target = btn.data('target')  || '_self';

        Swal.fire({
            title: title,
            html:  desc,
            icon:  icon,
            showCancelButton: true,
            confirmButtonColor: '#1cc88a',
            cancelButtonColor:  '#858796',
            confirmButtonText:  '<i class="fas fa-print mr-1"></i> ' + okText,
            cancelButtonText:   '<i class="fas fa-times mr-1"></i> Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success shadow-sm',
                cancelButton:  'btn btn-light shadow-sm'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed) {
                __bukaUrl(url, target);
            }
        });
    });

    /** --- Konfirmasi EXPORT EXCEL --- */
    $(document).on('click', '.btn-export-swal', function(e) {
        e.preventDefault();
        var btn    = $(this);
        var url    = btn.data('url')     || '#';
        var title  = btn.data('title')   || 'Konfirmasi Export Data';
        var desc   = btn.data('desc')    || 'Anda yakin ingin meng-export data ini ke file Excel?';
        var icon   = btn.data('icon')    || 'success';
        var okText = btn.data('confirm') || 'Ya, Export Excel';
        var target = btn.data('target')  || '_self';

        Swal.fire({
            title: title,
            html:  desc,
            icon:  icon,
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor:  '#858796',
            confirmButtonText:  '<i class="fas fa-file-excel mr-1"></i> ' + okText,
            cancelButtonText:   '<i class="fas fa-times mr-1"></i> Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success shadow-sm',
                cancelButton:  'btn btn-light shadow-sm'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed) {
                __bukaUrl(url, target);
            }
        });
    });

    /** --- Konfirmasi sebelum SUBMIT form modal export / print --- */
    $(document).on('click', '.btn-form-submit-swal', function(e) {
        e.preventDefault();
        var btn       = $(this);
        var formId    = btn.data('form');
        var title     = btn.data('title')   || 'Konfirmasi Proses';
        var desc      = btn.data('desc')    || 'Anda yakin ingin memproses data ini?';
        var icon      = btn.data('icon')    || 'question';
        var okText    = btn.data('confirm') || 'Ya, Lanjutkan';
        var formactionOverride = btn.data('formaction') || null;

        if (!formId) {
            console.warn('btn-form-submit-swal: atribut data-form tidak ada');
            return;
        }

        Swal.fire({
            title: title,
            html:  desc,
            icon:  icon,
            showCancelButton: true,
            confirmButtonColor: '#1cc88a',
            cancelButtonColor:  '#858796',
            confirmButtonText:  '<i class="fas fa-check mr-1"></i> ' + okText,
            cancelButtonText:   '<i class="fas fa-times mr-1"></i> Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: 'btn btn-success shadow-sm',
                cancelButton:  'btn btn-light shadow-sm'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.isConfirmed) {
                var frm = document.getElementById(formId);
                if (!frm) return;
                // Jika tombol punya formaction sendiri, override action form sesaat sebelum submit
                // (mirip perilaku <button formaction="...">)
                if (formactionOverride) {
                    var originalAction = frm.getAttribute('action');
                    frm.setAttribute('action', formactionOverride);
                    frm.submit();
                    // kembalikan (tidak terpengaruh karena sudah berganti halaman, tapi untuk berjaga-jaga)
                    frm.setAttribute('action', originalAction || '');
                } else {
                    frm.submit();
                }
            }
        });
    });

});
</script>

@stack('scripts')

</body>

</html>