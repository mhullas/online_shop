@if (Session::has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-ban"></i> Error!</h5>
        {{ Session::get('error') }}
    </div>
@endif

@if (Session::has('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <h5><i class="icon fas fa-ban"></i> Success!</h5>
        {{ Session::get('success') }}
    </div>
@endif

{{-- @if (Session::has('success'))
    <script>
        Swal.fire({
            icon: "success",
            title: "Wow...",
            text: "{{ session('success') }}"
        });
    </script>
@endif --}}

{{-- @if (Session::has('success'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-right',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast',
            },
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false,
        });
        (async () => {
            await Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            })
        })()
    </script>
@endif

@if (Session::has('error'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-right',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast',
            },
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: false,
        });
        (async () => {
            await Toast.fire({
                icon: 'error',
                title: "{{ Session::get('error') }}",
            })
        })()
    </script>
@endif --}}


{{-- <script>
    @if (Session::has('success'))
        toastr.success("{{ session('success') }}", 'Success !', {
            timeOut: 3000
        });
    @endif
</script> --}}
