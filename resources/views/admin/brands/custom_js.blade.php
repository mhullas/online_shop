<script>
    // new DataTable('#myTable');

    $('#edit_brand').on('hidden.bs.modal', function() {
        $('#name').removeClass('is-invalid').siblings('p').removeClass(
            'invalid-feedback').html('');

    });

    //Form_Submit
    $('#brandForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('brand.store') }}",
            type: "post",
            data: $(this).serializeArray(),
            dataType: "json",
            success: function(response) {
                if (response['status'] == true) {
                    window.location.href = "{{ route('brand.list') }}";
                } else {
                    let errors = response['errors'];
                    if (errors['name']) {
                        $('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                            .html(errors['name']);
                    } else {
                        $('#name').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }

                    if (errors['slug']) {
                        $('#slug').addClass('is-invalid').siblings('p').addClass('invalid-feedback')
                            .html(errors['slug']);
                    } else {
                        $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }
                }
            },
            error: function(jqXHR, exception) {
                console.log('Something Went Wrong !');
            }
        })
    })

    //form_validation
    $('#name').on('keyup', function() {
        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
    });
    $('#up_name').on('keyup', function() {
        $('#up_name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
    });


    //get_slug
    $('#name').on('keyup', function() {
        $.ajax({
            url: "{{ route('getSlug') }}",
            type: 'get',
            data: {
                title: $(this).val()
            },
            dataType: "json",
            success: function(response) {
                if (response['status'] == true) {
                    $('#slug').val(response['slug']);
                    $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
                }
            }
        });
    });

    //get_edit_data
    $(document).on('click', '.edit_brand', function() {
        let edit_id = $(this).data('id');
        let url = "{{ route('brand.edit', 'ID') }}";
        let newUrl = url.replace('ID', edit_id);
        $('#edit_brand').modal('show');

        $.ajax({
            url: newUrl,
            type: 'get',
            success: function(ullas) {
                $('#up_name').val(ullas.getBrand.name);
                $('#up_slug').val(ullas.getBrand.slug);
                $('#up_status').val(ullas.getBrand.status);
                $('#brand_up_id').val(edit_id);
            }
        });
    });

    //update_brand
    $('#editBrandForm').submit(function(e) {
        e.preventDefault();
        let update_id = $('#brand_up_id').val();
        let url = "{{ route('brand.update', 'ID') }}";
        let newUrl = url.replace('ID', update_id);

        $.ajax({
            url: newUrl,
            type: 'put',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                if (response['status'] == true) {
                    window.location.href = "{{ route('brand.list') }}";
                } else {
                    let error = response['errors']
                    if (error['up_name']) {
                        $('#up_name').addClass('is-invalid').siblings('p').addClass(
                            'invalid-feedback').html(error['up_name']);
                    } else {
                        $('#up_name').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }
                    if (error['up_slug']) {
                        $('#up_slug').addClass('is-invalid').siblings('p').addClass(
                            'invalid-feedback').html(error['up_slug']);
                    } else {
                        $('#up_slug').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }
                }
            },
            error: function(jqXHR, exception) {
                console.log('Something Went Wrong !');
            }
        });

    });

    //get_update_slug
    $('#up_name').on('keyup', function() {
        $.ajax({
            url: "{{ route('getSlug') }}",
            type: 'get',
            data: {
                title: $(this).val()
            },
            dataType: 'json',
            success: function(response) {
                if (response['status'] == true) {
                    $('#up_slug').val(response['slug']);
                    $('#up_slug').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
                }
            }
        });
    });

    //delete blade
    $('#confirm_delete').on('click', '.btn_ok', function(e) {
        var $modalDiv = $(e.delegateTarget);
        var id = $(this).data('recordId');
        let url = "{{ route('brand.delete', 'ID') }}";
        let newUrl = url.replace('ID', id);

        $.ajax({
            url: newUrl,
            type: 'delete',
            data: {},
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response["status"]) {
                    window.location.href = "{{ route('brand.list') }}";
                }
            }
        });
        $modalDiv.addClass('loading');
        setTimeout(function() {
            $modalDiv.modal('hide').removeClass('loading');
        }, 1000)
    });
    $('#confirm_delete').on('show.bs.modal', function(e) {
        var data = $(e.relatedTarget).data();
        $('.title', this).text(data.recordTitle);
        $('.btn-ok', this).data('recordId', data.recordId);
    });

    //Paginate
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        let page = $(this).attr('href').split('page=')[1];
        //console.log(page);
        product(page)
    });

    function product(page) {
        let _token = $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: "{{ route('brand.paginate') }}",
            method: 'get',
            data: {
                _token: _token,
                page: page
            },
            success: function(data) {
                $('.table-data').html(data);
            }
        });
    }
</script>
