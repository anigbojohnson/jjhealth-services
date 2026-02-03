<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">



        <title>@yield("title","Video Explorer")</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
        <script src="https://js.stripe.com/v3/"></script>

        <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCUSt7aRlI0dowRdJn6ba9AYkjff8j1Vsw&libraries=places"></script>
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

        <!-- Styles -->
        <style>
            
        </style>
    </head>

    <body>

    <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <!-- Logo on the left -->
        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/logo.png') }}" alt="jjhealth-services logo" style="max-width: 100px; height: auto;">
        </a>

        <!-- Mobile toggle button -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Collapsible content for nav links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left-side navigation -->

            <ul class="navbar-nav mr-auto">
              <li class="nav-item">
                  <a class="nav-link {{ Route::currentRouteName() == 'telehealth' ? 'active' : '' }}" 
                    href="{{ route('telehealth') }}">Telehealth Consultations</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link {{ Route::currentRouteName() == 'certificate' ? 'active' : '' }}" 
                    href="{{ route('certificate') }}">Medical Certificates</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link {{ Route::currentRouteName() == 'weight-loss' ? 'active' : '' }}" 
                    href="{{ route('weight-loss') }}">Weight Loss Treatment</a>
              </li>
              <li class="nav-item">
                  <a class="nav-link {{ Route::currentRouteName() == 'specialist-referral-home' ? 'active' : '' }}" 
                    href="{{ route('specialist-referral-home') }}">Specialist Referrals</a>
              </li>
        </ul>


            <!-- Right-side navigation -->
            <ul class="navbar-nav ml-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login', ['param' => 'login_form', 'action' => '_']) }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register', ['param' => 'register_form', 'action' => '_']) }}">Register</a>
                    </li>
                @else
                    <li class="nav-item">
                        <span class="nav-link">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @endguest
            </ul>
        </div>
    </div>
</nav>



  

    @yield('content')


  <!-- Footer -->
  <footer
          class="text-center text-lg-start text-dark mt-5"
          style="background-color: #ECEFF1"
          >
    <!-- Section: Social media -->
    <section
             class="d-flex justify-content-between p-4 text-white"
             style="background-color: #21D192"
             >
      <!-- Left -->
      <div class="me-5">
        <span>Get connected with us on social networks:</span>
      </div>
      <!-- Left -->

      <!-- Right -->
      <div>
        <a href="" class="text-white me-6">
          <i class="fab fa-facebook-f"></i>
        </a>
        <a href="" class="text-white me-5">
          <i class="fab fa-twitter"></i>
        </a>
        <a href="" class="text-white me-4">
          <i class="fab fa-google"></i>
        </a>
        <a href="" class="text-white me-4">
          <i class="fab fa-instagram"></i>
        </a>
        <a href="" class="text-white me-4">
          <i class="fab fa-linkedin"></i>
        </a>
        <a href="" class="text-white me-4">
          <i class="fab fa-github"></i>
        </a>
      </div>
      <!-- Right -->
    </section>
    <!-- Section: Social media -->

    <!-- Section: Links  -->
    <section class="">
      <div class="container text-center text-md-start mt-5">
        <!-- Grid row -->
        <div class="row mt-3">
          <!-- Grid column -->
          <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
            <!-- Content -->
            <h6 class="text-uppercase fw-bold">JJHealth Services</h6>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto"
                style="width: 60px; background-color: #7c4dff; height: 2px"
                />
            <p>
              Here you can use rows and columns to organize your footer
              content. Lorem ipsum dolor sit amet, consectetur adipisicing
              elit.
            </p>
          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4 text-left">
            <!-- Links -->
            <h6 class="text-uppercase fw-bold">Solutions</h6>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto"
                
                />
          

            <p>
              <a href="{{ route('weight-loss') }}" >Weight Loss Treatments</a>
            </p>
            <p>
              <a href="{{ route('telehealth') }}" >Telehealth Consultation</a>
            </p>
            <p>
              <a  href="{{ route('certificate') }}" >Medical certificates</a>
            </p>
            <p>
              <a href="{{ route('specialist-referral-home') }}" >Specialist refferal</a>
            </p>
            <p>
              <a href="#!">Covid-19</a>
            </p>
          

          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4 text-left">
            <!-- Links -->
            <h6 class="text-uppercase fw-bold">Useful links</h6>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto "
                
                />
            <p>
              <a href="#!">Your Account</a>
            </p>
            <p>
              <a href="#!">Contact US</a>
            </p>
            
            <p>
              <a href="#!">Help</a>
            </p>
          </div>
          <!-- Grid column -->

          <!-- Grid column -->
          <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
            <!-- Links -->
            <h6 class="text-uppercase fw-bold">Contact</h6>
            <hr
                class="mb-4 mt-0 d-inline-block mx-auto"
                
                />
            <p><i class="fas fa-home mr-3"></i> New York, NY 10012, US</p>
            <p><i class="fas fa-envelope mr-3"></i> info@example.com</p>
            <p><i class="fas fa-phone mr-3"></i> + 01 234 567 88</p>
            <p><i class="fas fa-print mr-3"></i> + 01 234 567 89</p>
          </div>
          <!-- Grid column -->
        </div>
        <!-- Grid row -->
      </div>
    </section>
    <!-- Section: Links  -->

    <!-- Copyright -->
    <div
         class="text-center p-3"
         style="background-color: rgba(0, 0, 0, 0.2)"
         >
         &copy; {{ date('Y') }}
      <a class="text-dark" href="http://jjtelehealth.com.au/lander"
         >JJtelehealth.com.au  All rights reserved</a
        >
    </div>
    <!-- Copyright -->
  </footer>
  <!-- Footer -->
<!-- End of .container -->
       
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>





let formData = new Map();
let formDataObject=""

    
$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        
      $('#forgotten-password').submit(function (event) {
        event.preventDefault(); // Prevent default form submission

          // Get form data
          var formData = $(this).serialize();

          // Send AJAX request
          $.ajax({
              type: 'GET',
              headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
              url:  '{{ route("forgotten-password") }}',
              data: formData,
              success: function(response) {
                // Registration successful
                $('#forgottenMessage').text(response.message).removeClass('text-danger').addClass('text-success');
            },
            error: function(xhr, textStatus, errorThrown) {
                // Registration failed
                var errorMessage = xhr.responseJSON.error || 'Registration failed, please try again.';
                $('#forggottenMessage').text( xhr.responseJSON.error).removeClass('text-success').addClass('text-danger');
            }
          });
      });

      $('#change-password-form').on('submit', function(e) {
        e.preventDefault(); // Prevent the form from submitting the default way
        $('#password-errors').empty().hide(); // Clear the list and hide it initially


        let form = $(this);
        let actionUrl = form.attr('action'); // Get the form action URL
        let formData = form.serialize(); // Serialize form data

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: formData, // Serialized form data including email and token
            success: function(response) {
              $('#error-message').hide();
              if (response.status === 'success') {
                $('#success-message').html(response.message).show();

              }
            },
            error: function(xhr) {
              
                // Check for validation errors
                    let errors = xhr.responseJSON.errors;
                    if(xhr.status === 400){
                        console.log(xhr.responseJSON.message )
                          let errorList = $('#password-errors');
                          errorList.append('<li>' + xhr.responseJSON.message+ '</li>');
                          errorList.show(); 

                         
                    }
                    if(xhr.status === 422){
                        if (errors?.password) {
                            // Display password error message
                            let errorList = $('#password-errors');
                            errors.password.forEach(function(error) {
                                errorList.append('<li>' + error + '</li>');
                            });
                             errorList.show(); 
                        }
                      
                    }
      
                } 
        });
    });


    $('#faq .faq-button').click(function() {
        var icon = $(this).find('.icon');
        if ($(this).next().hasClass('show')) {
            icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
        } else {
            $('#faq .icon').removeClass('fa-chevron-up').addClass('fa-chevron-down'); // Reset all icons
            icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
        }
    });

</script>
<script src="https://js.stripe.com/v3/"></script>

</body>
</html>
