<script>
    // new DataTable('#myTable');

    //form_submit
    $('#categoryForm').submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: '{{ route('category.store') }}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                if (response["status"] == true) {
                    window.location.href = "{{ route('category.list') }}";
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


    //Get Slug
    $('#name').on('keyup', function() {
        $.ajax({
            url: "{{ route('getSlug') }}",
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

    //search
    $('#search').on('keyup', function(e) {
        e.preventDefault();
        let search = $('#search').val();
        // console.log(search);
        $.ajax({
            url: "{{ route('category.search') }}",
            type: 'get',
            data: {
                search: search
            },
            success: function(res) {
                $('.table').html(res);
            }
        });
    });

    //image_upload
    Dropzone.autoDiscover = false;
    const dropzone = $("#image").dropzone({
        init: function() {
            this.on('addedfile', function(file) {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
        },
        url: "{{ route('temp-images.create') }}",
        maxFiles: 1,
        paramName: 'image',
        addRemoveLinks: true,
        acceptedFiles: "image/jpeg,image/png,image/gif",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(file, response) {
            $("#image_id").val(response.image_id);
            //console.log(response)
        }
    });

    //form validation
    $('#up_name').on('keyup', function() {
        $('#up_name').removeClass('is-invalid').siblings('p').removeClass(
            'invalid-feedback').html('');
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
    $(document).on('click', '.edit_cat', function() {
        var cat_id = $(this).data('id');
        //alert(cat_id);
        $('#edit_category').modal('show');
        $.ajax({
            type: 'get',
            url: '/admin/category/edit/' + cat_id,
            success: function(response) {
                // console.log(response.getCategory.name);
                $('#up_imageId').val(response.getCategory.image_id);
                $('#up_name').val(response.getCategory.name);
                $('#up_slug').val(response.getCategory.slug);
                $('#showimg').prop('src', '/Uploads/Category/thumb/' + response.getCategory.image);
                $('#up_status').val(response.getCategory.status);
                $('#up_showHome').val(response.getCategory.showHome);
                $('#cat_up_id').val(response.getCategory.id);
                $('#getImgId').val(response.getCategory.image_id);
                $('#oldUpName').val(response.getCategory.name);
                $('#oldUpSlug').val(response.getCategory.slug);
                $('#oldUpStatus').val(response.getCategory.status);
                $('#oldUpshowHome').val(response.getCategory.showHome);

            }
        });


        // var name = $(this).data('name');
        // var slug = $(this).data('slug');
        // var image = $(this).data('image');
        // var status = $(this).data('status');

        // //set value
        // $('#up_name').val(name);
        // $('#up_slug').val(slug);
        // $('#showimg').prop('src', '/Uploads/Category/' + image);
        // $('#up_status').val(status);
    });

    //Update_Category_form
    $('#updateCategoryForm').submit(function(event) {
        event.preventDefault();

        var editCatId = $('#cat_up_id').val();

        $.ajax({
            url: '/admin/category/update/' + editCatId,
            type: 'put',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                if (response.status === true || response.notUpdate === true) {
                    window.location.href = "{{ route('category.list') }}";
                } else {
                    if (response['notfound'] == true) {
                        window.location.href = "{{ route('category.list') }}";
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
                }
            },
            error: function(jqXHR, exception) {
                console.log('Something Went Wrong !');
            }
        });
    });

    //delete blade
    $('#confirm_delete').on('click', '.btn_ok', function(e) {
        var $modalDiv = $(e.delegateTarget);
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/category/delete/' + id,
            type: 'delete',
            data: {},
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if ((response["status"] == false)) {
                    $('#confirm_category .title').text(response.getCategory.name);
                    $('#confirm_category').modal('show');
                    $('#confirm_category').on('click', '.category_ok',
                        function() {
                            $('#confirm_category').modal('hide');
                        });
                }
                if (response["status"] == true) {
                    window.location.href = "{{ route('category.list') }}";
                }
            }
        });
        $modalDiv.addClass('loading');
        setTimeout(function() {
            $modalDiv.modal('hide').removeClass('loading');
        }, 100)
    });
    $('#confirm_delete').on('show.bs.modal', function(e) {
        var data = $(e.relatedTarget).data();
        $('.title', this).text(data.recordTitle);
        $('.tag', this).text(data.recordTag);
        $('.btn_ok', this).data('id', data.recordId);
    });


    // //delete confirmation
    // function deleteCategory(id) {
    //     var url = '{{ route('category.delete', 'ID') }}';
    //     var newUrl = url.replace("ID", id)

    //     if (confirm(
    //         "Are you sure to delete this category ? It will delete all Sub Category relate to this Category ! ")) {
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
    //                     window.location.href = "{{ route('category.list') }}";
    //                 }
    //             }
    //         });
    //     }
    // }

    //image_upload
    Dropzone.autoDiscover = false;
    const mydropzone = $("#up_image").dropzone({
        init: function() {
            this.on('addedfile', function(file) {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
            });
        },
        url: "{{ route('temp-images.create') }}",
        maxFiles: 1,
        paramName: 'image',
        addRemoveLinks: true,
        acceptedFiles: "image/jpeg,image/png,image/gif",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(file, response) {
            $("#up_imageId").val(response.image_id);
            //console.log(response)
        }
    });
</script>
