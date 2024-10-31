<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'JobSeeker');

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {

    $data=seeker_login("john.doe@example.com");
    echo "Name: " . $data['email'] . "<br>";
    echo "Name: " . $data['password'] . "<br>";
    
}



function seeker_login($email){
    global $conn;
    $sql = "
        SELECT email, password
        FROM JobSeeker
        WHERE email = ?";
        
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}


?>