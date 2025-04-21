<?php
// DEBUG LOGGING
file_put_contents("debug.txt", file_get_contents("php://input"));

// This script handles incoming requests to add a product to the Supabase database.
$supabase_url = 'https://hlzxnmnurukavrybpbkm.supabase.co';
$secret_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImhsenhubW51cnVrYXZyeWJwYmttIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDQwMzcxOTAsImV4cCI6MjA1OTYxMzE5MH0.QUiEwiUVdKTQIdfNsg043Pw09j6fjB8sI9aFOCFzZh8';

$request = json_decode(file_get_contents("php://input"), true);

if (!$request) {
    http_response_code(400);
    echo json_encode(['message' => 'No input received or invalid JSON']);
    exit;
}

// Required fields
$requiredFields = ['email', 'password', 're_password', 'firstname', 'lastname'];

foreach ($requiredFields as $field) {
    if (empty($request[$field])) {
        http_response_code(400);
        echo json_encode(['message' => "Missing field: $field"]);
        exit;
    }
}

$email = $request['email'];
$password = $request['password'];
$re_password = $request['re_password'];
$firstname = $request['firstname'];
$lastname = $request['lastname'];
$phone_number = $request['phone_number'] ?? null;
$account_number = $request['account_number'] ?? null;
$address = $request['address'] ?? null;

// Check passwords match
if ($password !== $re_password) {
    http_response_code(400);
    echo json_encode(['message' => 'Passwords do not match']);
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_BCRYPT);

// Prepare user data
$data = json_encode([
    'usertype' => 'user',
    'email' => $email,
    'password' => $hashed_password,
    'firstname' => $firstname,
    'lastname' => $lastname,
    'phone_number' => $phone_number,
    'account_number' => $account_number,
    'address' => $address
]);

// cURL to Supabase
$ch = curl_init("$supabase_url/rest/v1/users");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "apikey: $secret_key",
    "Authorization: Bearer $secret_key",
    "Prefer: return=representation"
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code >= 200 && $http_code < 300) {
    http_response_code(201);
    echo $response;
} else {
    http_response_code($http_code);
    echo json_encode([
        'message' => 'Failed to register user',
        'details' => json_decode($response, true)
    ]);
}
