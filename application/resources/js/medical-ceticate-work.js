let formData = new Map();
let formDataObject=""

    
$.ajaxSetup({
  headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});

    
// Get today's date
var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); // January is 0!
var yyyy = today.getFullYear();
today = yyyy + '-' + mm + '-' + dd;

// Get tomorrow's date
var tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
var ddTomorrow = String(tomorrow.getDate()).padStart(2, '0');
var mmTomorrow = String(tomorrow.getMonth() + 1).padStart(2, '0');
var yyyyTomorrow = tomorrow.getFullYear();
tomorrow = yyyyTomorrow + '-' + mmTomorrow + '-' + ddTomorrow;

// Get the date 3 days from today
var threeDaysFromToday = new Date();
threeDaysFromToday.setDate(threeDaysFromToday.getDate() + 3);
var ddThree = String(threeDaysFromToday.getDate()).padStart(2, '0');
var mmThree = String(threeDaysFromToday.getMonth() + 1).padStart(2, '0');
var yyyyThree = threeDaysFromToday.getFullYear();
threeDaysFromToday = yyyyThree + '-' + mmThree + '-' + ddThree;

// Set min and max attributes for the date inputs
$('#validFrom').attr('min', today);
$('#validFrom').attr('max', tomorrow);
$('#validTo').attr('min', today);
$('#validTo').attr('max', threeDaysFromToday);






      
document.getElementById('back-personal-details').addEventListener('click', function() {
    $('#pesonalDetails').show();   
    $('#work').hide();
  });

  document.getElementById('back-work').addEventListener('click', function() {
    $('#medicalDetails').hide();   
    $('#work').show();
  });

  document.getElementById('back-medicals').addEventListener('click', function() {
    $('#medicalDetails').show();   
    $('#previewDetails').hide();
  });

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


var today = new Date();
var tomorrow = new Date(today);
tomorrow.setDate(tomorrow.getDate() + 1);

// Convert dates to YYYY-MM-DD format
function formatDate(date) {
    var dd = String(date.getDate()).padStart(2, '0');
    var mm = String(date.getMonth() + 1).padStart(2, '0'); // January is 0!
    var yyyy = date.getFullYear();
    return yyyy + '-' + mm + '-' + dd;
}
document.getElementById('back-home').addEventListener('click', function() {
    // Redirect to the 'certificate' route
    window.location.href = "/certificate";

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


$('#validFrom, #validTo').on('input', function() {
    var validFrom = $('#validFrom').val();
    var validTo = $('#validTo').val();

    // Clear previous errors
    $('#validFrom-error').text('');
    $('#validTo-error').text('');


    if (!validFrom) {
        $('#validFrom-error').text("Please select a valid start date.");
    } 

    // Validation for 'validTo'
    if (validTo) {
        var validToDate = new Date(validTo);

        // Ensure 'validTo' is between today and 3 days from today
         if (validFrom && validToDate <= new Date(validFrom)) {
            $('#validTo-error').text("End date must be after start date.");
        } else {
            $('#validTo-error').text("");
          
        }
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


      
$('#medical-form').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    console.log($(this).serialize());

    $.ajax({
        url: '/validate-medicalDetails-work-medical-certificate', // URL to handle the medical details form submission
        type: 'POST',
        data: $(this).serialize(), // Serialize form data
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
                    workDetails: "Work Details"
                };
                const fieldLabelMap = {
                    sickLeave: "Sick leave from work",
                    FitToReturn: "Fit to return to work",
                    startWork: "Fit to start work",
                    adjustWork: "Adjusting work duties"
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
});

$('#submit-work-medical-certificate').on('click', function(e) {
    $('#paymentRequest').show()
    $('#previewDetails').hide('d-none')
});



    $('#FitToReturn, #startWork,#adjustWork,#sickLeave').on('change', function() {
        $('#seeking').text('');
        const isWorkChecked = $("#adjustWork").is(":checked") || $("#sickLeave").is(":checked");
        if (isWorkChecked) {
            // Uncheck IAgree and clear the error message
            $("#IAgree").prop("checked", false);
        } 
    });

    $('#IAgree').on('change', function() {
        var errorMessage = "This field is required";
        $('#IAgree-error').text(!$("#IAgree").is(":checked") ? errorMessage : '');
        
    });
    $('#adjustmentsReasons').on('input', function() {
        var errorMessage = "This field is required";
        var isAdjustWorkChecked = $("#adjustWork").is(":checked");
        var value = $(this).val();
    
        if (isAdjustWorkChecked && !value) {
            $('#adjustmentsReasons-error').text(errorMessage);
        } else {
            $('#adjustmentsReasons-error').text('');
        }
    });
    
    
$(document).ready(function () {
  
    $('#work-register-form').on('submit', function(e) {
        e.preventDefault();  // Prevent the default form submission
    
        var formData = $(this).serialize();  // Automatically serialize the form fields
    
        $.ajax({
            url: '/validate-workDetails-work-medical-certificate',  // Replace with your endpoint URL
            method: 'POST',
            data: formData,  // Send serialized form data
            success: function(response) {
                // Handle success, for example:
                $('#work').hide();
                $('#medicalDetails').show();
            },
            error: function(xhr) {
                // Handle validation errors and other errors
                // You can display the errors in the appropriate fields
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    // Display validation errors for medical details
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });
                    if(errors?.work[0]){
                        $('#seeking').text("please select one option below");

                    }
                    console.log()
                } else {
                    alert('An error occurred while submitting medical details.');
                }
            }
        });
    });
$('#sickLeave').click(function() {
          $('#workAdjustments').hide();
          $('#acknoledgement').hide();
          $('#IAgree-error').text('');
          $('#adjustmentsReasons-error').text('');
          $('.medicalLetterReasons').show();
          $('.previewMedicalLetterReasons').show();




      });

$('#startWork').click(function() {
          $('#workAdjustments').hide();
          $('#acknoledgement').show();
          $('#IAgree-error').text('');
          $('#adjustmentsReasons-error').text('');
          $('.medicalLetterReasons').hide();
          $('.previewMedicalLetterReasons').show();




      });

$('#FitToReturn').click(function() {
          $('#workAdjustments').hide();
          $('#acknoledgement').show();
          $('#IAgree-error').text('');
          $('#adjustmentsReasons-error').text('');
          $('.medicalLetterReasons').hide();
          $('.previewMedicalLetterReasons').hide();



      });

      $('#adjustWork').click(function() {
          $('#workAdjustments').show();
          $('#acknoledgement').hide();
          $('#IAgree-error').text('');
          $('#adjustmentsReasons-error').text('');
          $('.medicalLetterReasons').hide();
          $('.previewMedicalLetterReasons').hide();

      });
    });

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
      $('#dob').on('input', function() {
          let value = $(this).val();
          let today = new Date();
          let birthDate = new Date(value);
          let age = today.getFullYear() - birthDate.getFullYear();
          let month = today.getMonth() - birthDate.getMonth();
          if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
              age--;
          }
          if (!value) {
              $('#dob-error').text('The date of birth field is required.');
          } else if (age < 18) {
              $('#dob-error').text('You must be 18 years or older.');
          } else {
              $('#dob-error').text('');
          }
      });
  
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
            url: '/validate-personalDetails-work-medical-certificate', // Replace with your server endpoint URL
            type: 'POST',
            data: formData,
            success: function(response) {
        
                // Hide and show sections as needed
                $('#pesonalDetails').hide();
                $('#work').show();
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
                console.log(result.paymentIntent.status)

                if (result.error) {
                    // Display error message in #card-errors
                    $('#card-errors').text(result.error.message);
                } else {
                    // Payment succeeded, redirect to success page
                    $.ajax({
                        type: 'POST',
                        url: '/submit-work-medical-certificate', // Adjust this route to your actual backend route
                        data: {
                            _token: $('input[name="_token"]').val(), // Include CSRF token if needed
                            paymentIntentId: result.paymentIntent.id, // Send the Payment Intent ID or any other data
                        },
                        success: function(response) {
                            // Redirect to success page or handle successful response
                            console.log(response)
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
  
  