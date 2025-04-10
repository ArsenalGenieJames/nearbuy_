<?php  
include '../nearbuy/supabase_config.php';  

function registerUser($username, $email, $password) {  
    global $supabase_url, $headers;  

    $data = [  
        'username' => $username,  
        'email' => $email,  
        'password' => password_hash($password, PASSWORD_DEFAULT)  
    ];  

    $ch = curl_init("$supabase_url/auth/v1/signup");  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
    curl_setopt($ch, CURLOPT_POST, true);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  

    $response = curl_exec($ch);  
    curl_close($ch);  

    return json_decode($response, true);  
}  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $username = $_POST['username'];  
    $email = $_POST['email'];  
    $password = $_POST['password'];  
    
    $result = registerUser($username, $email, $password);  
    if (isset($result['error'])) {  
        echo "Registration failed: " . $result['error']['message'];  
    } else {  
        echo "Registration successful!";  
    }  
}  
?>  

<!-- HTML Registration Form -->  
<form action="register.php" method="POST">  
    <input type="text" name="username" placeholder="Username" required>  
    <input type="email" name="email" placeholder="Email" required>  
    <input type="password" name="password" placeholder="Password" required>  
    <button type="submit">Register</button>  
</form>