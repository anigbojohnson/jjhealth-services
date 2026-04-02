
let step = 1;
const totalSteps = 3;

function updateProgress() {
    const progress = (step / totalSteps) * 100;
    $('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress);
    $('.step-text').text(`Step ${step} of ${totalSteps}`);
}

$(document).ready(function () {


    $('#personal-detail-form').on('submit', function (e) {
        e.preventDefault();

        $('#validate-payment').prop('disabled', true).text('Processing...');

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
                
                if(step < totalSteps)  
                    step++;
                 
                updateProgress();

            },
            error: function (response) {
                // Handle errors
                
                var errors = response.responseJSON.errors;
                console.log(errors)
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
            },
            complete: function() {
              $('#validate-payment').prop('disabled', false).text('Continue');
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
        $('#validate-payment').prop('disabled', true).text('Processing...');

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
  
                        $('#card-errors').text(result.error.message);
                    } else {


                         // Display error message in #card-errors
                        let form = document.getElementById('consultation-special-refferals-form');
                        let formData = new FormData(form);

                        // 👇 THIS is what you're missing
                        let fileInput = document.getElementById('fileUpload');

                        if (fileInput && fileInput.files.length > 0) {
                            formData.append('fileUpload', fileInput.files[0]);
                        }

                 
                        // Payment succeeded, redirect to success page
                        $.ajax({
                            type: 'POST',
                            data: formData,
                            contentType: false,  // VERY IMPORTANT
                            processData: false,  // VERY IMPORTANT
                            url: '/save-specialist-refferals-details', // Adjust this route to your actual backend route
                            success: function(response) {
                                // Redirect to success page or handle successful response
                                window.location.href = response.redirect_url
                            },
                            error: function(xhr) {
                                // Handle error if something goes wrong with the post-payment processing
                                alert("Failed to complete backend processing");
                            },
                            complete: function() {
                                $('#validate-payment').prop('disabled', false).text('Continue');
                            }
                        });
                    }
                });
            },
            error: function(xhr) {
                // Handle error if the request fails
                console.error("Error creating PaymentIntent:", xhr);
            },
            complete: function() {
              $('#validate-payment').prop('disabled', false).text('Continue');
            }
        });
    });

    $('#consultation-special-refferals-form').on('submit', function (e) {
        e.preventDefault();
        $('#consult').prop('disabled', true).text('Processing...');

        $('#requestReason-error').text('');
        $('#medicalConditionImage-error').text('');
        $('#fileUpload-error').text('');
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
                if(step < totalSteps)  
                    step++;       
                updateProgress();
        
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
            
            },
            complete: function() {
              $('#consult').prop('disabled', false).text('Continue');
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
        if(step < totalSteps)  
            step--;
        updateProgress();
        
    })

});



// Initialize progress

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
      currentStep < steps.length - 1 ? "Next &rarr;" : "Select specialist referrals";
  }

  window.goToNext = function () {
    if (currentStep < steps.length - 1) {
      currentStep++;
      renderStep();
    } else {
   
    window.location.href = "/specialist-referral/select";
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
