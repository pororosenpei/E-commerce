<?php
// Include Composer's autoloader
require_once 'vendor/autoload.php';

// Correct namespace import
use Google\Client;
use Google\Service\Oauth2;  // Correct namespace for Google_Service_Oauth2

// Initialize configuration
$clientID = '601321251012-afoslcvjmtgnqol8dh97nmmtbckbev2s.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/login/';

// Create Google Client object
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope('email');
$client->addScope('profile');

// Check if the user is returning from Google
if (isset($_GET['code'])) {
    // Authenticate and fetch the access token using the code
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // Check if the token is valid
    if ($client->getAccessToken()) {
        // Fetch user profile info from Google
        $google_oauth = new Google\Service\Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Get user info (email, name, etc.)
        $email = $google_account_info->email;
        $name = $google_account_info->name;
        
        // Example: Store user info in session
        session_start();
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $name;

        // Redirect to a logged-in page or dashboard
        header('Location: dashboard.php');  // Redirect to a page of your choice
        exit;
    } else {
        // If there's an issue with the access token
        echo "Error: Unable to fetch access token.";
    }
} else {
    // If no 'code' parameter is returned, display an error or redirect to login
    echo "Error: Missing code in the URL.";
}
?>