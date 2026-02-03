@extends('welcome')
@section('title',"solution")
@section('content')
    
   <!-- resources/views/auth/register.blade.php -->

   <div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="dropdown text-center">
                <button class="btn btn-primary btn-lg w-100 dropdown-toggle" type="button" id="providerDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Select Files
                </button>
                <form id="myForm">
                    <!-- Other form fields -->
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </form>
                
                <div class="dropdown-menu w-100" aria-labelledby="providerDropdown">
                    <a class="dropdown-item" href="{{ route('auth.google-drive.redirect') }}"  data-provider="google_drive">Google Drive</a>
                    <a class="dropdown-item"  onclick="triggerDropboxChooser()" data-provider="dropbox">Dropbox</a>
                    <!-- Add other cloud storage providers here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

  <script src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key='<?php echo config('services.dropbox.client_id'); ?>'></script>

    <script>
        function handleFiles(files) {
            // Process the selected files (e.g., download them)
            // You can use the Dropbox API with the obtained access token to perform actions on the selected files
            console.log('Selected files:', files);


            const downloadUrls = files.map(file => file.link);
            console.log('Selected jjjjjfiles:', downloadUrls);

            // Send the download URLs to the server using AJAX
            var csrfToken = $('input[name="_token"]').val();

            $.ajax({
                type: 'POST',
                url: '/dropbox-downloaded-files',
                headers: {
                  'X-CSRF-TOKEN': csrfToken
               },
                data: { downloadUrls: downloadUrls },
                success: function(response) {
                    console.log('Files downloaded successfully:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Failed to download files:', error);
                }
            })
        }

        function openDropboxFileChooser() {

            Dropbox.choose({
                success: function(files) {
                    // Call the handleFiles function to handle the selected files
                    handleFiles(files);
                },
                cancel: function() {
                    // Handle cancellation
                    console.log('Chooser was canceled');
                },
                linkType: 'preview',
                multiselect: true
            });
        }

        // Function to trigger Dropbox chooser when button is clicked
        function triggerDropboxChooser() {
            openDropboxFileChooser();
        }
    </script>