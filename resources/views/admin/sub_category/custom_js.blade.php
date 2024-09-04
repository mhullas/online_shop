<script>
    
    new DataTable('#myTable');

    //form_submit
    $('#subCategoryForm').submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: '{{ route('sub-category.store') }}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                if (response["status"] == true) {
                    window.location.href = "{{ route('sub-category.list') }}";
                }
                var errors = response['errors'];
                if (errors['name']) {
                    $('#name').addClass('is-invalid').siblings('p').addClass(
                            'invalid-feedback')
                        .html(errors['name']);
                } else {
                    $('#name').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
                }

                if (errors['slug']) {
                    $('#slug').addClass('is-invalid').siblings('p').addClass(
                            'invalid-feedback')
                        .html(errors['slug']);
                } else {
                    $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
                }

                if (errors['category']) {
                    $('#category').addClass('is-invalid').siblings('p').addClass(
                            'invalid-feedback')
                        .html(errors['category']);
                } else {
                    $('#category').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
                }

            },
            error: function(jqXHR, exception) {
                console.log('Something Went Wrong !');
            }
        });
    });

    //form validation
    $('#name').on('keyup', function() {
        $('#name').removeClass('is-invalid').siblings('p').removeClass(
            'invalid-feedback').html('');
    });
    $('#category').on('change', function() {
        $('#category').removeClass('is-invalid').siblings('p').removeClass(
            'invalid-feedback').html('');
    });

    //Get Slug
    $('#name').on('keyup', function() {
        $.ajax({
            url: '{{ route('getSlug') }}',
            type: 'get',
            data: {
                title: $(this).val()
            },
            dataType: 'json',
            success: function(response) {
                if (response["status"] == true) {
                    $('#slug').val(response["slug"]);
                    $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
                }
            }
        })
    });

    //Get Update Slug
    $('#up_name').on('keyup', function() {
        $.ajax({
            url: '{{ route('getSlug') }}',
            type: 'get',
            data: {
                title: $(this).val()
            },
            dataType: 'json',
            success: function(response) {
                if (response["status"] == true) {
                    $('#up_slug').val(response["slug"]);
                    $('#up_slug').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
                }
            }
        })
    });

    //get edit value
    $(document).on('click', '.edit_sub_cat', function() {

        var subCat_id = $(this).data('id');
        $('#edit_sub_category').modal('show');

        $.ajax({
            type: 'get',
            url: '/admin/sub-category/edit/' + subCat_id,
            success: function(response) {
                $('#up_category').val(response.getSubCategory.category_id);
                $('#up_name').val(response.getSubCategory.name);
                $('#up_slug').val(response.getSubCategory.slug);
                $('#up_status').val(response.getSubCategory.status);
                $('#updateId').val(response.getSubCategory.id);
            }
        });
    });

    //Update_Sub_Category_form
    $('#updateSubCategoryForm').submit(function(event) {
        event.preventDefault();

        var updateId = $('#updateId').val();

        $.ajax({
            url: '/admin/sub-category/update/' + updateId,
            type: 'put',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                if (response["status"] == true) {
                    window.location.href = "{{ route('sub-category.list') }}";
                } else {
                    if (response['notfound'] == true) {
                        window.location.href = "{{ route('sub-category.list') }}";
                    }
                    var errors = response['errors'];
                    if (errors['up_name']) {
                        $('#up_name').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback')
                            .html(errors['up_name']);
                    } else {
                        $('#up_name').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }

                    if (errors['up_slug']) {
                        $('#up_slug').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback')
                            .html(errors['up_slug']);
                    } else {
                        $('#up_slug').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }

                    if (errors['up_category']) {
                        $('#up_category').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback')
                            .html(errors['up_category']);
                    } else {
                        $('#up_category').removeClass('is-invalid').siblings('p').removeClass(
                            'invalid-feedback').html('');
                    }
                }
            },
            error: function(jqXHR, exception) {
                console.log('Something Went Wrong !!');
            }
        });
    });

    //delete blade
    $('#confirm-SubDelete').on('click', '.btn-ok', function(e) {
        var $modalDiv = $(e.delegateTarget);
        var id = $(this).data('recordId');
        $.ajax({
            url: '/admin/sub-category/delete/' + id,
            type: 'delete',
            data: {},
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response["status"]) {
                    window.location.href = "{{ route('sub-category.list') }}";
                }
            }
        });
        $modalDiv.addClass('loading');
        setTimeout(function() {
            $modalDiv.modal('hide').removeClass('loading');
        }, 1000)
    });
    $('#confirm-SubDelete').on('show.bs.modal', function(e) {
        var data = $(e.relatedTarget).data();
        $('.title', this).text(data.recordTitle);
        $('.btn-ok', this).data('recordId', data.recordId);
    });

    // //Delete Sub Category
    // function deleteSubCategory(id) {
    //     var url = '{{ route('sub-category.delete', 'ID') }}';
    //     var newUrl = url.replace("ID", id)
    //     if (confirm('Are you sure to delete Sub Category ?')) {
    //         $.ajax({
    //             url: newUrl,
    //             type: 'delete',
    //             data: {},
    //             dataType: 'json',
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             success: function(response) {
    //                 if (response["status"]) {
    //                     window.location.href = "{{ route('sub-category.list') }}";
    //                 }
    //             }
    //         });
    //     }
    // }
    //Delete Sub Category
    // function deleteSubCategory(id) {
    //     var url = '{{ route('sub-category.delete', 'ID') }}';
    //     var newUrl = url.replace("ID", id)
    //     if (confirm('Are you sure to delete Sub Category ?')) {
    //         $.ajax({
    //             url: newUrl,
    //             type: 'delete',
    //             data: {},
    //             dataType: 'json',
    //             headers: {
    //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //             },
    //             success: function(response) {
    //                 if (response["status"]) {
    //                     window.location.href = "{{ route('sub-category.list') }}";
    //                 }
    //             }
    //         });
    //     }
    // }
</script>
