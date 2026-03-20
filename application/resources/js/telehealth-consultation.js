$(document).ready(function() {
        let cardNumber="" 

        // Function to get today's date in YYYY-MM-DD format
        function getTodayDate() {
            var today = new Date();
            var year = today.getFullYear();
            var month = ('0' + (today.getMonth() + 1)).slice(-2); // Months are 0-based
            var day = ('0' + today.getDate()).slice(-2);
            return year + '-' + month + '-' + day;
        }
    
        // Set the max attribute to today's date
        $('#startDateSymptoms').attr('max', getTodayDate());
    });
    

    $('#personal-detail-form').on('click', function (e) {
        e.preventDefault();
        $('#address-error').text('');
        $('#fname-error').text('');
        $('#lname-error').text('');
        $('#pnumber-error').text('');
        $('#indigene-error').text('');

        var form = $('#register-form')[0]; // jQuery object -> raw DOM element

        // Create FormData object
        var formData = new FormData(form); // Pass the DOM form element to FormData


        console.log($('#register-form').serialize())
        $.ajax({
            url: "/telehealth-personal-details",
            method: 'POST',
            data: formData ,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (response) {
                // Handle success, clear error message
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

    $('#informationPreExistingHealthYes, #medicationsRegularlyInfo, #detailedSymptoms,#startDateSymptoms').on('input', function() {
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
            $('#informationPreExistingHealthYes').prop('required', true); // Make it required
        } else {
            $('#healthConditions').hide(); // Hide the health conditions input field
            $('#informationPreExistingHealthYes').prop('required', false); // Remove required
            $('#informationPreExistingHealthYes').val(''); // Clear the input field
        }
    });

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
            $('#medicationsRegularlyInfo').prop('required', true); // Make it required
        } else {
            $('#medicationRegimen').hide(); // Hide the medication regimen input field
            $('#medicationsRegularlyInfo').prop('required', false); // Remove required
            $('#medicationsRegularlyInfo').val(''); // Clear the input field
        }
    });



var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
  event.preventDefault();

  stripe.confirmCardPayment(clientSecret, {
    payment_method: {
      card: cardNumber, // You can use any of the card fields here
    },
  }).then(function(result) {
    if (result.error) {
      // Display error in the #card-errors div
      document.getElementById('card-errors').textContent = result.error.message;
    } else {
      // The payment succeeded!
      window.location.href = "/payment-success";
    }
  });
});



    $('#validate-medical').click(function(e) {
        e.preventDefault(); // Prevent default form submission

        // Gather form data
        var formData = {
            _token: $('input[name="_token"]').val(),
            preExistingHealth: $('#preExistingHealthYes').val(),
            informationPreExistingHealthYes: $('#informationPreExistingHealthYes').val(),
            medicationsRegularly: $('#medicationsRegularlyYes').val(),
            medicationsRegularlyInfo: $('#medicationsRegularlyInfo').val(),
            startDateSymptoms: $('#startDateSymptoms').val(),
            detailedSymptoms: $('#detailedSymptoms').val(),
            treatment_category: $('#treatment_category').val(),

        };

        // Clear previous error messages
        $('.text-danger').text('');

        // AJAX request
        $.ajax({
            type: 'POST',
            url: '/telehealth-consultation-details', // Adjust this URL to your actual route
            data: formData,
            success: function(response) {
                // Handle success response (redirect, show a success message, etc.)
          //      window.location.href = response.message.original[0]

          $('#paymentRequest').show('d-none')
          $('#consultationRequest').hide('d-none')    
          
          

          
            },
            error: function(xhr) {
                // Handle validation errors (Laravel validation errors)
                var errors = xhr.responseJSON.errors;

                console.log(xhr)
                // Show the error messages in the appropriate fields
                if (errors.preExistingHealth) {
                    $('#preExistingHealth-error').text(errors.preExistingHealth[0]);
                }
                if (errors.informationPreExistingHealthYes) {
                    $('#informationPreExistingHealthYes-error').text(errors.informationPreExistingHealthYes[0]);
                }
                if (errors.medicationsRegularly) {
                    $('#medicationsRegularly-error').text(errors.medicationsRegularly[0]);
                }
                if (errors.medicationsRegularlyInfo) {
                    $('#medicationsRegularlyInfo-error').text(errors.medicationsRegularlyInfo[0]);
                }
                if (errors.startDateSymptoms) {
                    $('#startDateSymptoms-error').text(errors.startDateSymptoms[0]);
                }
                if (errors.detailedSymptoms) {
                    $('#detailedSymptoms-error').text(errors.detailedSymptoms[0]);
                }
            }
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
                url: '/create-tele-consult-payment-intent',
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
                                url: '/save-tele-consult-details', // Adjust this route to your actual backend route
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


document.addEventListener("DOMContentLoaded", function () {

  const steps = [
    {
      step: "Step 01",
      title: "Inquire",
      text: "Once you apply here, we’ll schedule a consultation to discuss your current situation, your goals, and the details of your ideal partnership with a dating and image consultant."
    },
    {
      step: "Step 02",
      title: "Consult",
      text: "We’ll walk through your style, preferences, and relationship goals during a personalized consultation session."
    },
    {
      step: "Step 03",
      title: "Match & Style",
      text: "Together we’ll develop your personal brand and initiate curated matchmaking tailored just for you."
    }
  ];

  let currentStep = 0;

  const stepContent = document.getElementById("step-content");
  const prevBtn = document.getElementById("prevBtn");
  const nextBtn = document.getElementById("nextBtn");
  const stepNav = document.getElementById("stepNav");

  function renderStep() {

    const content = steps[currentStep];

    stepContent.innerHTML = `
      <p class="text-uppercase text-muted small fw-semibold mb-1">${content.step}</p>
      <h3 class="fw-bold mb-3">${content.title}</h3>
      <p class="text-secondary">${content.text}</p>
    `;

    // Handle previous button visibility
    if (currentStep === 0) {
      prevBtn.style.display = "none";
      stepNav.classList.remove("justify-content-between");
      stepNav.classList.add("justify-content-end");
    } else {
      prevBtn.style.display = "inline-block";
      stepNav.classList.remove("justify-content-end");
      stepNav.classList.add("justify-content-between");
    }

    // Change next button text
    nextBtn.innerHTML =
      currentStep < steps.length - 1 ? "Next &rarr;" : "Book now";
  }

  window.goToNext = function () {
    if (currentStep < steps.length - 1) {
      currentStep++;
      renderStep();
    } else {
      alert("You're done!");
    }
  };

  window.goToPrevious = function () {
    if (currentStep > 0) {
      currentStep--;
      renderStep();
    }
  };

  renderStep();

});