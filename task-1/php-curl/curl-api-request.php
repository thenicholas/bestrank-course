<?php

declare(strict_types=1);

$url = 'https://b24-ppqfwe.bitrix24.ru/rest/1/uy41llabw8evqdnx/'.'user.current';

// Initialize a cURL session
$ch = curl_init();

// I get "self-signed certificate" error without these options
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_CAINFO, 'C:\Windows\cacert.pem');

// Set the URL
curl_setopt($ch, CURLOPT_URL, $url);

// Return the transfer as a string
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Set network timeouts
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  // Connection timeout in seconds
curl_setopt($ch, CURLOPT_TIMEOUT, 10);        // Maximum execution time in seconds

// Execute the session and store the result in $response
$response = curl_exec($ch);
// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Error:'.curl_error($ch);
    curl_close($ch);
    exit();
}
curl_close($ch);
// Print the response
var_dump(json_decode($response, true));