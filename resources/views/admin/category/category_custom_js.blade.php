<script>
    //form_validation
    $('#categoryForm').submit(function(event) {
        event.preventDefault();
        $.ajax({
            url: '{{ route('category.store') }}',
            type: 'post',
            data: $(this).serializeArray(),
            dataType: 'json',
            success: function(response) {
                var errors = response['errors'];
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
            },
            error: function(jqXHR, exception) {
                console.log('Something Went Wrong !');
            }
        })
    })

    //form validation
    $('#name').on('keyup', function(){
        $('#name').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
    })
    $('#slug').on('keyup', function(){
        $('#slug').removeClass('is-invalid').siblings('p').removeClass(
                        'invalid-feedback').html('');
    })

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
</script>
