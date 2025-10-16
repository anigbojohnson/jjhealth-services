$(document).ready(function () {
    $('#personal-detail-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "/weight-loss-personal-details",
            method: 'POST',
            data: new FormData(this),
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                // Handle success, clear error messages
                $('#address-error').text('');
                $('#fname-error').text('');
                $('#lname-error').text('');
                $('#pnumber-error').text('');
                $('#indigene-error').text('');


                $('#pesonalDetails').hide('d-none')
                $('#consultationRequest').show()                
            },
            error: function (response) {
                // Handle errors
                
                var errors = response.responseJSON.errors;
                console.log(response)
                if (errors.address) {
                    $('#address-error').text(errors.address[0]);
                }
                if (errors.fname) {
                    $('#fname-error').text(errors.fname[0]);
                }
                if (errors.lname) {
                    $('#lname-error').text(errors.lname[0]);
                }
                if (errors.pnumber) {
                    $('#pnumber-error').text(errors.pnumber[0]);
                }
                if (errors.dob) {
                    $('#dob-error').text(errors.dob[0]);
                }
                if (errors.indigene) {
                    $('#indigene-error').text(errors.indigene[0]);
                }
            }
        });
    });

    var stripe = Stripe("pk_test_bMToQz9lq4TgR3V5Qe6jRygh00I6c2oSfG");
    var elements = stripe.elements();

    var style = {
      base: {
        color: '#32325d',
        fontSize: '16px',
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        '::placeholder': {
          color: '#aab7c4',
        },
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a',
      },
    };
    
    // Create individual Elements for card number, expiry, and CVC with the Bootstrap class 'form-control'
   let cardNumber = elements.create('cardNumber', { 
        style: style, 
        classes: {
            base: 'form-control'
        } 
    });
    var cardExpiry = elements.create('cardExpiry', { 
        style: style, 
        classes: {
            base: 'form-control'
        } 
    });
    var cardCvc = elements.create('cardCvc', { 
        style: style, 
        classes: {
            base: 'form-control'
        } 
    });
    
    // Mount the elements into their respective divs
    cardNumber.mount('#card-number');
    cardExpiry.mount('#card-expiry');
    cardCvc.mount('#card-cvc');



    $('#fileUpload').change(function(e) {
        var fileName = e.target.files[0].name; // Get the selected file name
        $('#file-name').text('Selected file: ' + fileName); // Display the file name
    });

    $('#consultation-loss-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "/weight-loss-consultation-details",
            method: 'POST',
            data: new FormData(this),
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                // Handle success, clear error messages
                $('#requestReason-error').text('');
                $('#height-error').text('');
                $('#weight-error').text('');
               
                $('#medicalDetails').show()
                $('#consultationRequest').hide('d-none')
            },
            error: function (response) {
                // Handle errors
                var errors = response.responseJSON.errors;
                if (errors.requestReason) {
                    $('#requestReason-error').text(errors.requestReason[0]);
                }
                if (errors.height) {
                    $('#height-error').text(errors.height[0]);
                }
                if (errors.weight) {
                    $('#weight-error').text(errors.weight[0]);
                }
            }
        });
    });            
    $('.option-btn').click(function () {
        var target = $(this).data('target');
        var value = $(this).data('value');
      


        $('#' + target).val(value);
        $('#' + target + '-error').text('');

        $('button[data-target="' + target + '"]').css('background-color', '');
        $('button[data-target="' + target + '"]').css('color','blue');


        $('button[data-target="' + target + '"]').removeClass('btn-primary btn-secondary text-white');
        $(this).addClass('btn-primary text-white');
    });

    $('#back-personalDetails').on('click', function () {
        $('#pesonalDetails').show()
        $('#consultationRequest').hide('d-none')
    })

    $('#back-consultDetails').on('click', function () {
        $('#medicalDetails').hide()
        $('#consultationRequest').show()
    })

    $('#medicalConditionImageYes').click(function() {
        $('#fileUploadGroup').show();
    });

    $('#medicalConditionImageNo').click(function() {
        $('#fileUploadGroup').hide();
    });

    $('#medical-detail-form').submit(function(e) {
        e.preventDefault(); // Prevent default form submission

        var formData = $(this).serialize(); // Serialize form data

        // Send AJAX request
        $.ajax({
            type: 'POST',
            url: '/weight-loss-medical-details',
            data: new FormData(this),
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                // Handle successful submission
                $('.text-danger').text('');
                console.log(response)
                console.log(); // Log the response
                // Optionally redirect to another page or show success message
                if(response.message=="invalid"){
                    $('#text-invalid').text('Sorry, You are not qualified for weight loss treatment');
                
                }
                else{
                    console.log('jesus is lord')
                    $('#paymentRequest').show('d-none')
                    $('#medicalDetails').hide('d-none') 
                }
            },
            error: function(xhr, status, error) {
                // Handle errors
                $('.text-danger').text('');
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]); // Display error message
                    });
                }
            }
        });
    });





    $('#validate-payment').click(function(e) {
        e.preventDefault(); // Prevent form submission

        // Step 1: Send an AJAX request to the backend to get the client secret
        $.ajax({
            type: 'POST',
            url: '/create-weight-loss-payment-intent',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // Include CSRF token
            },
            success: function(response) {

                // Step 2: Confirm the card payment with the client secret
                stripe.confirmCardPayment(response.secret_key, {
                    payment_method: {
                        card: cardNumber
                    }
                }).then(function(result) {
                    if (result.error) {
                        // Display error message in #card-errors
                        $('#card-errors').text(result.error.message);
                    } else {
                        // Payment succeeded, redirect to success page
                        $.ajax({
                            type: 'POST',
                            url: '/save-weight-loss-details', // Adjust this route to your actual backend route
                            data:$('#medical-detail-form').serialize() ,
                            success: function(response) {
                                // Redirect to success page or handle successful response
                                window.location.href = response.redirect_url
                            },
                            error: function(xhr) {
                                // Handle error if something goes wrong with the post-payment processing
                                alert("Failed to complete backend processing");
                            }
                        });
                    }
                });
            },
            error: function(xhr) {
                // Handle error if the request fails
                console.error("Error creating PaymentIntent:", xhr);
            }
        });
    });
    
});