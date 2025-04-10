<?php
// Include the database configuration
include('config.php');

// Get the form data
$email = $_POST['email'];
$password = $_POST['password'];

// API endpoint to check the user’s credentials
$url = $supabase_url . '/rest/v1/users?email=eq.' . $email;

// Initialize cURL session
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

// Check if there was an error with the cURL request
if ($response === false) {
    echo "Error: " . curl_error($ch);
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the user exists
    if (count($data) > 0) {
        // Check if the password is correct
        if (password_verify($password, $data[0]['password'])) {
            echo "Login successful!";
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}

curl_close($ch);
?>
