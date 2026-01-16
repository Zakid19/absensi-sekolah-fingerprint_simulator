<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.datatable-yajra').on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var form = $('#form-delete');
        form.attr('action', $(this).attr('href'))
        Swal.fire({
            title: 'Apakah anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Hapus',
            canceButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    $('.datatable-yajra').on('click', '.btn-verifikasi', function(e) {
        e.preventDefault();
        var form = $('#form-verifikasi');
        form.attr('action', $(this).attr('href'))
        Swal.fire({
            title: 'Apakah anda yakin?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            canceButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    function formatNumber (num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }

    function nilaiMinMax(el) {
        if (el.value != "") {
            if (parseInt(el.value) < parseInt(el.min)) {
            el.value = el.min;
            }
            if (parseInt(el.value) > parseInt(el.max)) {
            el.value = el.max;
            }
        }
    }

    //tooltif
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
