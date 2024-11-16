<?php
// Include Google API client library
require_once 'vendor/autoload.php';

session_start();

// Initialize Google Client
$client = new Google_Client();
$client->setClientId('601321251012-afoslcvjmtgnqol8dh97nmmtbckbev2s.apps.googleusercontent.com'); // Replace with your client ID
$client->setClientSecret('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'); // Replace with your client secret
$client->setRedirectUri('http://localhost/ecom/userdb.php'); // Replace with your redirect URI
$client->addScope('email'); // Add the desired scopes for your app

// Generate the authentication URL
$authUrl = $client->createAuthUrl();
?>