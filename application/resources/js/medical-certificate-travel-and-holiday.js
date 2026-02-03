
    
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});



document.getElementById('back-home').addEventListener('click', function() {
    // Redirect to the 'certificate' route
    window.location.href = "/certificate";

});

document.getElementById('back-personal').addEventListener('click', function() {
    $('#medicalDetails').hide('d-none');   
    $('#pesonalDetails').show();
});

document.getElementById('back-medicals').addEventListener('click', function() {
    $('#medicalDetails').show();   
    $('#previewDetails').hide('d-none');
});


$('#medicationsRegularlyYes').click(function() {
    $('#medicationRegimen').show();
});

$('#medicationsRegularlyNo').click(function() {
    $('#medicationRegimen').hide();
});


$('#preExistingHealthYes').click(function() {
    $('#healthConditions').show();
});

$('#preExistingHealthNo').click(function() {
  $('#healthConditions').hide();
});













// personal Details


$(document).ready(function() {
            // Function to perform validation for the first name field while typing
            $('#fname').on('input', function() {
                let value = $(this).val();
                if (!value) {
                    $('#fname-error').text('The first name field is required.');
                } else {
                    $('#fname-error').text('');
                }
            });

            // Function to perform validation for the last name field while typing
            $('#lname').on('input', function() {
                let value = $(this).val();
                if (!value) {
                    $('#lname-error').text('The last name field is required.');
                } else {
                    $('#lname-error').text('');
                }
            });

            // Function to perform validation for the date of birth field while typing
            var today = new Date().toISOString().split('T')[0];
            // Set the max attribute of the input field to today's date
            $('#dob').attr('max', today);
            // Function to perform validation for the phone number field while typing
            $('#pnumber').on('input', function() {
                let value = $(this).val();
                const phoneRegex = /^(?:\+61|0)[2-478](?:[ -]?[0-9]){8}$/;
                if (!value || !phoneRegex.test(value)) {
                    $('#pnumber-error').text('Please enter a valid Australian phone number.');
                } else {
                    $('#pnumber-error').text('');
                }
            });

            // Function to perform validation for the gender field while typing
            $('#gender').on('input', function() {
                let value = $(this).val();
                if (!value) {
                    $('#gender-error').text('The gender field is required.');
                } else {
                    $('#gender-error').text('');
                }
            });

            // Function to perform validation for the indigenous origin field while typing
            $('#indigene').on('input', function() {
                let value = $(this).val();
                if (!value) {
                    $('#indigene-error').text('The indigenous origin field is required.');
                } else {
                    $('#indigene-error').text('');
                }
            });

            // Function to perform validation for the address field while typing
            $('#address').on('input', function() {
                let value = $(this).val();
                if (!value) {
                    $('#address-error').text('The address field is required.');
                } else {
                    $('#address-error').text('');
                }
            });


        $(document).on('click', '#validate-button', function() {
            // Prepare form data
                let formData = {
                fname: $('#fname').val(),
                lname: $('#lname').val(),
                dob: $('#dob').val(),
                pnumber: $('#pnumber').val(),
                gender: $('#gender').val(),
                indigene: $('#indigene').val(),
                address: $('#address').val()
                };

            // Perform AJAX request
        $.ajax({

            url: '/validate-personalDetails-travelAndHoliday-medical-certificate', // Replace with your server endpoint URL
            type: 'POST',
            data: formData,
            success: function(response) {

                // Hide and show sections as needed
                $('#pesonalDetails').hide();
                $('#medicalDetails').show();
            },
            error: function(xhr, status, error) {
                // Handle error response
                var errors = xhr.responseJSON.errors;
                
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

});











// carer details









//medical Details 

$('#informationPreExistingHealthYes,#currentStatus, #medicationsRegularlyInfo, #detailedSymptoms,#startDateSymptoms').on('input', function() {
        var fieldId = this.id;
        var value = $(this).val();
        var errorElement = '#' + fieldId + '-error';

        // Check if the field is required and empty
        if ( !value) {
            $(errorElement).text('This field is required.');
        } else {
        // Clear the error message if the field is filled or not required
                $(errorElement).text('');
            }
        });





$('button[data-target="preExistingHealth"]').click(function () {
        var value = $(this).data('value'); // Get the value (Yes or No)
        $('#preExistingHealthYes').val(value); // Update hidden input field

        $('#preExistingHealth-error').text('');
        $('#informationPreExistingHealthYes-error').text('');


        // Remove btn-primary from both buttons and add btn-outline-primary
        $('button[data-target="preExistingHealth"]').removeClass('btn-primary').addClass('btn-outline-primary');

        // Add btn-primary to the clicked button
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');
        
        if (value === 'Yes') {
            $('#healthConditions').show(); // Show the health conditions input field
            } else {
                $('#healthConditions').hide(); // Hide the health conditions input field
                $('#informationPreExistingHealthYes').prop('required', false); // Remove required
                $('#informationPreExistingHealthYes').val(''); // Clear the input field
                }
            
});



const futureDate = new Date();

// Get today's date in YYYY-MM-DD format
const formattedDate = futureDate.toISOString().split('T')[0];

// Set the max attribute of the date input to today's date
$('#startDateSymptoms').attr('max', formattedDate);

        // Handle "Yes" or "No" button click for medications
$('button[data-target="medicationsRegularly"]').click(function () {
        var value = $(this).data('value'); // Get the value (Yes or No)
        $('#medicationsRegularlyYes').val(value); // Update hidden input field

        $('#medicationsRegularlyInfo-error').text('');
        $('#medicationsRegularly-error').text('');

        // Remove btn-primary from both buttons and add btn-outline-primary
        $('button[data-target="medicationsRegularly"]').removeClass('btn-primary').addClass('btn-outline-primary');

        // Add btn-primary to the clicked button
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');

        if (value === 'Yes') {
        $('#medicationRegimen').show(); // Show the medication regimen input field
        } else {
        $('#medicationRegimen').hide(); // Hide the medication regimen input field
        $('#medicationsRegularlyInfo').prop('required', false); // Remove required
        $('#medicationsRegularlyInfo').val(''); // Clear the input field
        
    }
});

$('#submit-holidayAndTravelCouncillation-medical-certificate').on('click', function(e) {
    $('#paymentRequest').show(); 
    $('#previewDetails').hide('d-none')

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



$('#validate-medical').on('click', function(e) {
        e.preventDefault(); // Prevent default form submission

    $.ajax({

        url: '/validate-medicalDetails-travelAndHoliday-medical-certificate', // URL to handle the medical details form submission
        type: 'POST',
        data: $("#register-medical-and-holiday-cancellation-form").serialize(), // Serialize form data
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
        },
    success: function(response) {

    if (response.message === 'success') {

        let data = response.data
        // Map keys to display titles
        const sectionTitles = {
            personalDetails: "Personal Details",
            medicalDetails: "Medical Details",
        };

        const fieldLabelMap = {
            sickLeave: "Sick leave from studies",
            resumeStudies: " Fit to resume studies",

        };
        function getValueByKey(key) {
            return fieldLabelMap[key] || key; // Use mapped label or fallback to key if no label is found
        }

        // Function to get the label text by input ID
        function getLabelById(inputId) {
            const label = document.querySelector(`label[for='${inputId}']`);
            return label ? label.textContent : inputId;  // Return the key if label is not found
        }

        // Function to dynamically generate the review section for all data
        function generateReviewSection() {
            let reviewHtml = '';

            // Loop through each section (personalDetails, medicalDetails, workDetails)
            for (const section in data) {
                // Use the section title from the map
                const sectionTitle = sectionTitles[section] || section;

                reviewHtml += `<h3>${sectionTitle}</h3>`;  // Add section title with proper name

                
                const details = data[section];
                // Loop through each field within the section
                for (const key in details) {
                    
                    const value = getValueByKey(details[key]);
                    const label = getLabelById(key);  // Get the label or use the key if no label is found
                    
                    if (value !== null) {
                        // Append label and value to review HTML

                        reviewHtml += `                                   
                    <div class="row">
                        <div class="col-md-6">
                            <label>${label}:</label>
                        </div>
                        <div class="col-md-6">
                            <label>${value}</label>
                        </div>
                    </div><br>
    `;
                    }
                }
            }

            // Insert the generated HTML into a review area in the document
            document.getElementById('reviewDetails').innerHTML = reviewHtml;
        }

        // Call the function to generate the review section
        generateReviewSection();
        $('#previewDetails').show();
        $('#medicalDetails').hide();

        // Optionally redirect or show a success message
        }
    },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                // Display validation errors for medical details
                $.each(errors, function(key, value) {
                    $('#' + key + '-error').text(value[0]);
                });
            } else {
                alert('An error occurred while submitting medical details.');
            }
        }
    });




    $('#validate-payment').click(function(e) {
        e.preventDefault(); // Prevent form submission

        // Step 1: Send an AJAX request to the backend to get the client secret
        $.ajax({
            type: 'POST',
            url: '/create-mc-travelAndHoliday-payment-intent',
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
                            url: '/submit-travelAndHoliday-medical-certificate', // Adjust this route to your actual backend route
                            data: {
                                _token: $('input[name="_token"]').val(), // Include CSRF token if needed
                                paymentIntentId: result.paymentIntent.id, // Send the Payment Intent ID or any other data
                            },
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
