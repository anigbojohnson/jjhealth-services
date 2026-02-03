<?php

namespace App\Http\Controllers\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;
use Illuminate\Support\Facades\Http;

use Google_Service_Drive;
use GuzzleHttp\Client as HttpClient;






class AuthGoogleDriveController extends Controller
{
    //
    public function showProvider() { 
        return view('auth.provider');
        }
    
public function googleRedirect() { 
    $client = $this->getClient();
    $client->setRedirectUri(route('auth.google-drive.callback'));
    $client->setAccessType('offline');
    $client->setApprovalPrompt('force');
    $client->setIncludeGrantedScopes(true);
    $client->addScope(Google_Service_Drive::DRIVE_READONLY);

    return redirect()->to($client->createAuthUrl()); 
   }

public function googleCallback(Request $request) {
    try {
        $client = $this->getClient();
        $client->setRedirectUri(route('auth.google-drive.callback'));
        $client->fetchAccessTokenWithAuthCode($request->get('code'));
    
        // Store access token and refresh token associated with user
        $accessToken = $client->getAccessToken();
    ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Google Drive Picker</title>
            <meta name="csrf-token" content="{{ csrf_token() }}">

            <script type="text/javascript" src="https://apis.google.com/js/api.js"></script>
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
            <script type="text/javascript">
                // Function to initialize the Google Picker
                function initializePicker() {
                    gapi.load('picker', {
                        'callback': function() {
                            // Callback function to be called after Picker API is loaded
                            var picker = new google.picker.PickerBuilder()
                                .addView(google.picker.ViewId.DOCS)
                                .setOAuthToken('<?php echo $accessToken['access_token']; ?>')
                                .setDeveloperKey('<?php config('services.google_drive.api_key') ?>') // Replace with your developer key
                                .setCallback(function(data) {
                                    pickerCallback(data, '<?php echo $accessToken['access_token']; ?>');
                                })
                                .enableFeature(google.picker.Feature.MULTISELECT_ENABLED) // Allow multiple file selection
                                .build();
                            picker.setVisible(true);
                        }
                    });
                }

                // Callback function for handling selected files
                function pickerCallback(data, accessToken) {
                console.log(data);
                if (data.action == google.picker.Action.PICKED) {
                    var files = data.docs;
                    var files = data.docs;
                    var fileIds = files.map(function(file) {
                        return file.id;
                    });
                }
                    
                    downloadFiles(fileIds, accessToken);
                }

                // Call initializePicker() function immediately after getting the access token
                initializePicker();

                function downloadFiles(fileIds,token) {
                    $.ajax({
                        url: '/google-drive-downloaded-files',
                        method: 'POST',
                        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
                        data: { fileIds: fileIds,token:token },
                        success: function(response) {
                            console.log('Files downloaded successfully');
                            // Optionally, display a message or update UI
                        },
                        error: function(xhr, status, error) {
                            console.error('Error downloading files:', error);
                        }
                    });
                }
            </script>
        </head>
        <body>
            <!-- Your HTML content here -->
        </body>
        </html>

        <?php
    } catch (Exception $e) {
        // Log or handle the exception
        dd($e->getMessage()); // Output the error message for debugging
    }

}

    private function getClient()
    {

        $client = new Google_Client();
        $client->setClientId(config('services.google_drive.client_id'));
        $client->setClientSecret(config('services.google_drive.client_secret'));
        $client->setAccessType('offline');
        return $client;
    }

    public function downloadGoogleDriveFiles(Request $request)
    {
        // Get file IDs from the request
        $fileIds = $request->fileIds;
        dd($request);
        // Initialize Google Client
        $client = new Google_Client();
        $client->setAccessToken($request->token);

        // Initialize Drive service
        $driveService = new Google_Service_Drive($client);

        // Download files using Google Drive API
        foreach ($fileIds as $fileId) {
            // Download file content
            $fileContent = $driveService->files->get($fileId, array('alt' => 'media'));

            // Save file to storage folder
            file_put_contents(storage_path('app/' . $fileId . '.pdf'), $fileContent);
        }

        return response()->json(['message' => 'Files downloaded successfully']);

    }

    public function dropboxRedirect() { 

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => urlencode(config('services.dropbox.client_id')),
            'redirect_uri' => config('services.dropbox.redirect'),
        ]);

        
        return redirect("https://www.dropbox.com/oauth2/authorize?$query");
    }

    public function dropboxCallback(Request $request) { 
        $code = $request->input('code');
        

        // Exchange authorization code for access token
        $httpClient = new HttpClient();
        $response = $httpClient->post('https://api.dropboxapi.com/oauth2/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'client_id' => config('services.dropbox.client_id'),
                'redirect_uri' => config('services.dropbox.redirect'),
                'client_secret' => config('services.dropbox.client_secret'),
              
            ],
        ]);
    
        $data = json_decode($response->getBody(), true);
    
        // Store access token in database
        $accessToken = $data['access_token'];
        ?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropbox File Chooser</title>
    <script src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key='<?php config('services.dropbox.client_id') ?>'></script>

    <script>
         function handleFiles(files) {
            // Process the selected files (e.g., download them)
            // You can use the Dropbox API with the obtained access token to perform actions on the selected files
            console.log('Selected files:', files);

            const downloadUrls = files.map(file => file.link);

            // Send the download URLs to the server using AJAX
            $.ajax({
                type: 'POST',
                url: '/dropbox-downloaded-files',
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
                multiselect: true,
                extensions: ['.pdf', '.docx', '.jpg']
            });
        }

        // Automatically trigger Dropbox chooser when the page loads
        window.onload = function() {
            openDropboxFileChooser();
        };
    </script>
</head>
<body>
    <!-- No button is needed -->
</body>
</html>

        <?php
        
    }

    public function downloadDropboxFiles(Request $request)
    {
        try {
            $downloadUrls = $request->input('downloadUrls');
    
            // Directory where downloaded files will be stored
            $downloadDirectory = storage_path('storage/app/downloads');
    
            // Ensure the directory exists, create it if necessary
            if (!file_exists($downloadDirectory)) {
                mkdir($downloadDirectory, 0755, true);
            }
    
            // Loop through each download URL and fetch the file contents
            foreach ($downloadUrls as $url) {
                // Fetch file contents from the provided URL
                $response = Http::get($url);
    
                // Check if the request was successful
                if ($response->successful()) {
                    // Extract file name from the URL
                    $fileName = basename($url);
    
                    // Write file contents to a new file in the download directory
                    file_put_contents($downloadDirectory . '/' . $fileName, $response->body());
                } else {
                    // Log error if the request fails
                    Log::error(json_encode(['error' => 'Failed to download file from URL: ' . $url]));
                }
            }
    
            return response()->json(['message' => 'Files downloaded successfully']);
        } catch (\Exception $e) {
            // Log any other exceptions that may occur
            Log::error(json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]));
    
            return response()->json(['error' => 'An error occurred while downloading files.']);
        }
    }
    
}
