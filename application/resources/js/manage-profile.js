$(document).ready(function () {

    $('#requestBtn').on('click', function (event) {

        event.stopPropagation();

        $('#requestMenu').toggle();

    });

    $(document).on('click', function (event) {

        if (!$(event.target).closest('.dropdown').length) {
            $('#requestMenu').hide();
        }

    });


// Show / hide password
$('.toggle-password').on('click', function () {

    const target = $('#' + $(this).data('target'));

    if (target.attr('type') === 'password') {
        target.attr('type', 'text');
        $(this).text('🙈');
    } else {
        target.attr('type', 'password');
        $(this).text('👁');
    }
});


// Change password
$('#change-password-form').on('submit', function (e) {

    e.preventDefault();

    // Clear previous errors
    $('#current_password-error').text('');
    $('#password-error').text('');
    $('#password_confirmation-error').text('');
    $('#success-message').hide().text('');

    $('#change-password-button').prop('disabled', true).text('Proccessing');
   
    const form = this;
    const formData = new FormData(form);

    $.ajax({
        url: '/password-update',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'Accept': 'application/json'
        },

        success: function (response) {
          sessionStorage.setItem('profileMessage', response.message);
          window.location.href = response.redirect;

        },

        error: function (xhr) {

                if (xhr.status === 422) {

                    const errors = xhr.responseJSON.errors;

                    Object.keys(errors).forEach(function (field) {

                        const message = errors[field][0];

                        $('#' + field + '-error').text(message);

                    });
                }
        },

        complete: function () {
              $('#change-password-button').prop('disabled', false).text('Continue');
        }
    });

});


     $('#profile_picture').on('change', function (event) {

        const file = event.target.files[0];

        if (!file) {
            return;
        }

        const allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!allowedTypes.includes(file.type)) {
            $('#profile_picture-error').text(
                'Please select a JPG, PNG, or WebP image.'
            );

            this.value = '';
            return;
        }

        $('#profile_picture-error').text('');

        const imageUrl = URL.createObjectURL(file);

        // Replace the current avatar with the selected image
        $('.profile-avatar').first().replaceWith(`
            <img
                src="${imageUrl}"
                alt="Profile picture preview"
                class="profile-avatar"
            >
        `);
    });

    $('#edit-profile-form-data').on('submit', function (e) {

        e.preventDefault();

        // Clear previous errors
        $('.error').text('');

        const form = this;
        const button = $('#edit-profile-form');

        const formData = new FormData(form);

        $.ajax({
            url: $(form).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json'
            },

            success: function (response) {

                sessionStorage.setItem('profileMessage', response.message);

                window.location.href = response.redirect;
            },

            error: function (xhr) {

                if (xhr.status === 422) {

                    const errors = xhr.responseJSON.errors;

                    Object.keys(errors).forEach(function (field) {

                        const message = errors[field][0];

                        $('#' + field + '-error').text(message);

                    });
                }
            },

        });

    });

});