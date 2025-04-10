<?php  
include 'supabase_config.php';  

function loginUser($email, $password) {  
    global $supabase_url, $headers;  

    $data = [  
        'email' => $email,  
        'password' => $password  
    ];  

    $ch = curl_init("$supabase_url/auth/v1/token");  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
    curl_setopt($ch, CURLOPT_POST, true);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));  

    $response = curl_exec($ch);  
    curl_close($ch);  

    return json_decode($response, true);  
}  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    $email = $_POST['email'];  
    $password = $_POST['password'];  

    $result = loginUser($email, $password);  
    
    if (isset($result['access_token'])) {  
        $userId = $result['user']['id'];  
        $user_type = getUserType($userId);  

        switch ($user_type) {  
            case 'rider':  
                header('Location: rider.php');  
                break;  
            case 'admin':  
                header('Location: admin.php');  
                break;  
            case 'customer':  
                header('Location: customer.php');  
                break;  
            case 'seller':  
                header('Location: seller.php');  
                break;  
            default:  
                echo "User type not recognized.";  
        }  
        exit();  
    } else {  
        echo "Login failed: " . $result['error']['message'];  
    }  
}  

function getUserType($userId) {  
    global $supabase_url, $headers;  

    $ch = curl_init("$supabase_url/rest/v1/users?id=eq.$userId");  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);  
    
    $response = curl_exec($ch);  
    $user_data = json_decode($response, true);  
    curl_close($ch);  

    return $user_data[0]['user_type'] ?? null; // Adjust 'user_type' based on your actual field name  
}  
?>  

<!-- HTML Login Form -->  
<form action="login.php" method="POST">  
    <input type="email" name="email" placeholder="Email" required>  
    <input type="password" name="password" placeholder="Password" required>  
    <button type="submit">Login</button>  
</form>