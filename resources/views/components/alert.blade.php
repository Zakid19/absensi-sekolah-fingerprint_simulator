<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session()->has('success'))
<script>
    Swal.fire({
        title: 'Berhasil',
        text: @json(session('success')),
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    });
</script>
@endif

@if (session()->has('warning') || session()->has('peringatan'))
<script>
    Swal.fire({
        title: 'Peringatan',
        text: @json(session('warning') ?? session('peringatan')),
        icon: 'warning',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    });
</script>
@endif

@if (session()->has('info'))
<script>
    Swal.fire({
        title: 'Informasi',
        text: @json(session('info')),
        icon: 'info',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    });
</script>
@endif

@if (session()->has('error'))
<script>
    Swal.fire({
        title: 'Error',
        text: @json(session('error')),
        icon: 'error',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    });
</script>
@endif
