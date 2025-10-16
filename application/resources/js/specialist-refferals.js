$(document).ready(function () {

    $('#personal-detail-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: "/specialist-referral-personal-details",
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

    $('#validate-payment').click(function(e) {
        e.preventDefault(); // Prevent form submission

        // Step 1: Send an AJAX request to the backend to get the client secret
        $.ajax({
            type: 'POST',
            url: '/create-specialist-refferals-payment-intent',
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
                            url: '/save-specialist-refferals-details', // Adjust this route to your actual backend route
                            data: $('#consultation-special-refferals-form').serialize() ,
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

    $('#consultation-special-refferals-form').on('submit', function (e) {
        e.preventDefault();
        $('#requestReason-error').text('');
        $('#medicalConditionImage-error').text('');
        $('#fileUpload-error').text('');
        console.log(this)
        $.ajax({
            url: "/special-refferals-consultation-details",
            method: 'POST',
            data: new FormData(this),
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                // Handle success, clear error messages
                $('#consultationRequest').hide('d-none')
                $('#paymentRequest').show()
            },
            error: function (response) {
                // Handle errors
                var errors = response.responseJSON.errors;
                if (errors.requestReason) {
                    $('#requestReason-error').text(errors.requestReason[0]);
                }
       
                if (errors.medicalConditionImage ) {
                    $('#medicalConditionImage-error').text(errors.medicalConditionImage[0]);
                } else{
                    if (errors.fileUpload) {
                        $('#fileUpload-error').text(errors.fileUpload[0]);
                    }
                }
            
            }
        });
    }); 

    
        $('#fileUpload').change(function(e) {
            var fileName = e.target.files[0].name; // Get the selected file name
            $('#file-name').text('Selected file: ' + fileName); // Display the file name
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

    $('#medicalConditionImageYes').click(function() {
        $('#fileUploadGroup').show();
    });

    $('#medicalConditionImageNo').click(function() {
        $('#fileUploadGroup').hide();
    });
    $('#back-personalDetails').on('click', function () {
        $('#pesonalDetails').show()
        $('#consultationRequest').hide('d-none')
    })

    $('#back-home').on('click', function () {
        $('#pesonalDetails').hide('d-none')
        window.location.href = '/specialist_referrals'
    })

});
