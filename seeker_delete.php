<?php
// Database credentials
define('DB_HOST', 'your_db_host');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'your_db_name');

// Connect to the database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Email of the job seeker to delete
$email = "john.doe@example.com";

// Begin transaction to ensure both delete operations are successful
$conn->begin_transaction();

try {
    // Step 1: Retrieve the `seeker_id` using the email
    $sql_get_id = "SELECT seeker_id FROM JobSeeker WHERE email = ?";
    $stmt = $conn->prepare($sql_get_id);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $seeker_id = $row['seeker_id'];

        // Step 2: Delete associated skills using `seeker_id`
        $sql_delete_skills = "DELETE FROM Skills WHERE seeker_id = ?";
        $stmt_skills = $conn->prepare($sql_delete_skills);
        $stmt_skills->bind_param("i", $seeker_id);
        $stmt_skills->execute();

        // Step 3: Delete the job seeker
        $sql_delete_seeker = "DELETE FROM JobSeeker WHERE seeker_id = ?";
        $stmt_seeker = $conn->prepare($sql_delete_seeker);
        $stmt_seeker->bind_param("i", $seeker_id);
        $stmt_seeker->execute();

        // Commit transaction if all deletions are successful
        $conn->commit();
        echo "Job seeker and associated skills deleted successfully.";

        // Close statements
        $stmt_skills->close();
        $stmt_seeker->close();

    } else {
        echo "Job seeker with the specified email not found.";
    }

    // Close the `seeker_id` query statement
    $stmt->close();

} catch (Exception $e) {
    // Rollback transaction if any part of the deletion fails
    $conn->rollback();
    echo "Error deleting job seeker or skills: " . $e->getMessage();
}

// Close the connection
$conn->close();
?>
