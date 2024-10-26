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
} else {
    echo "Connected successfully!";
}

// Function to fetch job seeker data
function getJobSeekerData($email) {
    global $conn;
    // Query to fetch job seeker details
    $sql = "
        SELECT seeker_id, first_name, last_name, email, phone_number, location, profile_summary
        FROM JobSeeker
        WHERE email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $jobSeeker = $result->fetch_assoc();
        $jobSeeker['skills']      = getSkills     ($jobSeeker['seeker_id']);
        $jobSeeker['experiences'] = getExperiences($jobSeeker['seeker_id']);
        $jobSeeker['education']   = getEducation  ($jobSeeker['seeker_id']);
        return $jobSeeker;
    } else {
        return null;
    }
}


function getSkills($seeker_id) {
    global $conn;
    $sql_skills = "SELECT skill_name FROM Skills WHERE seeker_id = ?";
    $stmt = $conn->prepare($sql_skills);
    $stmt->bind_param("i", $seeker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $skills = [];
    while ($row = $result->fetch_assoc()) {
        $skills[] = $row['skill_name'];
    }
    return $skills;
}


function getExperiences($seeker_id) {
    global $conn;
    $sql_experience = "
        SELECT company_name, role_title, start_date, end_date, description 
        FROM Experience 
        WHERE seeker_id = ?";
    
    $stmt = $conn->prepare($sql_experience);
    $stmt->bind_param("i", $seeker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $experiences = [];
    while ($row = $result->fetch_assoc()) {
        $experiences[] = [
            'company_name' => $row['company_name'],
            'role_title' => $row['role_title'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date'],
            'description' => $row['description']
        ];
    }
    return $experiences;
}

function getEducation($seeker_id) {
    global $conn;
    $sql_education = "
        SELECT degree, institution, start_year, end_year
        FROM Education 
        WHERE seeker_id = ?";
    
    $stmt = $conn->prepare($sql_education);
    $stmt->bind_param("i", $seeker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $education = [];
    while ($row = $result->fetch_assoc()) {
        $education[] = [
            'degree' => $row['degree'],
            'institution' => $row['institution'],
            'start_year' => $row['start_year'],
            'end_year' => $row['end_year']
        ];
    }
    return $education;
}




// Example usage
$email = "john.doe@example.com";
$data = getJobSeekerData($email);

if ($data) {
    echo "<h2>Job Seeker Details</h2>";
    echo "Name: " . $data['first_name'] . " " . $data['last_name'] . "<br>";
    echo "Email: " . $data['email'] . "<br>";
    echo "Phone: " . $data['phone_number'] . "<br>";
    echo "Location: " . $data['location'] . "<br>";
    echo "Profile Summary: " . $data['profile_summary'] . "<br>";
    
    echo "<h3>Skills</h3>";
    if (!empty($data['skills'])) {
        foreach ($data['skills'] as $skill) {
            echo "- " . $skill . "<br>";
        }
    } else {
        echo "No skills listed.";
    }

    echo "<h3>Work Experience</h3>";
    if (!empty($data['experiences'])) {
        foreach ($data['experiences'] as $experience) {
            echo "<strong>Company:</strong> " . $experience['company_name'] . "<br>";
            echo "<strong>Role:</strong> " . $experience['role_title'] . "<br>";
            echo "<strong>Start Date:</strong> " . $experience['start_date'] . "<br>";
            echo "<strong>End Date:</strong> " . $experience['end_date'] . "<br>";
            echo "<strong>Description:</strong> " . $experience['description'] . "<br><br>";
        }
    } else {
        echo "No work experience listed.";
    }
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} else {
    echo "No job seeker found with the specified email.";
}
?>
