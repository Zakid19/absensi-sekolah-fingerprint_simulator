<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@if (session()->has('success'))
<script>
    Swal.fire({
        title: 'Berhasil',
        text: "{{ session('success') }}",
        icon: 'success',
        customClass: {
        confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
</script>
@endif

@if (session()->has('success'))
<script>
    Swal.fire({
        title: 'Berhasil',
        text: "{{ session('success') }}",
        icon: 'success',
        customClass: {
        confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
</script>
@endif

@if (session()->has('peringatan'))
<script>
    Swal.fire({
        title: 'Warning!',
        text: "{{ session('peringatan') }}",
        icon: 'warning',
        customClass: {
        confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
</script>
@endif

@if (session()->has('warning'))
<script>
    Swal.fire({
        title: 'Warning!',
        text: "{{ session('warning') }}",
        icon: 'warning',
        customClass: {
        confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
</script>
@endif

@if (session()->has('info'))
<script>
   Swal.fire({
        title: 'Information!',
        text: "{{ session('info') }}",
        icon: 'info',
        customClass: {
        confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
</script>
@endif

@if (session()->has('error'))
<script>
   Swal.fire({
        title: 'Error!',
        text: "{{ session('error') }}",
        icon: 'error',
        customClass: {
        confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
</script>
@endif

@if (session()->has('error'))
<script>
   Swal.fire({
        title: 'Error!',
        text: "{{ session('error') }}",
        icon: 'error',
        customClass: {
        confirmButton: 'btn btn-primary'
        },
        buttonsStyling: false
    })
</script>
@endif
