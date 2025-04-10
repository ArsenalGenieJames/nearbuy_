<?php
// Include the database configuration
include('config.php');

// Get the form data
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Encrypt password
$re_password = $_POST['re_password'];
$phone_number = $_POST['phone_number'];
$address = $_POST['address'];

// Check if passwords match
if ($password !== password_hash($re_password, PASSWORD_DEFAULT)) {
    die('Passwords do not match!');
}

// API endpoint to insert new user into the `users` table
$url = $supabase_url . '/rest/v1/users';

// Data to insert
$data = [
    "usertype" => "customer",  // Default user type, adjust based on your requirements
    "email" => $email,
    "password" => $password,
    "re_password" => $re_password,
    "firstname" => $firstname,
    "lastname" => $lastname,
    "phone_number" => $phone_number,
    "address" => $address,
];

// Initialize cURL session
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

// Check if there was an error with the cURL request
if ($response === false) {
    echo "Error: " . curl_error($ch);
} else {
    echo "User registered successfully!";
}

curl_close($ch);
?>
