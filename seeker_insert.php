<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'JobSeeker');

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sample job seeker data
$firstName = "John";
$lastName = "Doe";
$email = "john345678.doe@example.com";
$phoneNumber = "1234567890";
$location = "New York";
$profileSummary = "Experienced developer skilled in PHP and MySQL.";
$password = password_hash("password123", PASSWORD_DEFAULT);

// Sample skills data
$skills = ["PHP", "JavaScript", "SQL"];

// Step 1: Insert Job Seeker and Get seeker_id
$conn->begin_transaction(); // Start transaction for rollback in case of an error
try {
    // Prepare the main job seeker insert query
    $sql = "INSERT INTO JobSeeker (first_name, last_name, email, phone_number, location, profile_summary, password)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $firstName, $lastName, $email, $phoneNumber, $location, $profileSummary, $password);
    $stmt->execute();

    // Get the newly inserted seeker_id
    $seeker_id = $stmt->insert_id;

    // Step 2: Insert Skills for Job Seeker
    $sql_skills = "INSERT INTO Skills (seeker_id, skill_name) VALUES (?, ?)";
    $stmt_skills = $conn->prepare($sql_skills);

    foreach ($skills as $skill) {
        $stmt_skills->bind_param("is", $seeker_id, $skill);
        $stmt_skills->execute();
    }

    // Commit transaction
    $conn->commit();
    echo "Job seeker and skills inserted successfully!";

    // Close statements
    $stmt->close();
    $stmt_skills->close();

} catch (Exception $e) {
    // Rollback transaction if an error occurs
    $conn->rollback();
    echo "Error inserting job seeker or skills: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>
