<?php
// dbConnect function to connect to the database
function dbConnect() {
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    $dbname = "dct-ccs-finals"; // Database name

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection and handle errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// This function validates the login credentials: email and password.
function validateLoginCredentials($email, $password) {
    $errors = [];
    
    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Validate password
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    
    return $errors;
}

// This function checks if the email and password match a user in the database
function checkLoginCredentials($email, $password) {
    $conn = dbConnect();

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // If user is found, verify the password
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Compare the hashed password with the password provided
        if (password_verify($password, $user['password'])) {
            return true;
        } else {
            return false; // Invalid password
        }
    }
    return false; // No user found
}

// Function to get user ID by email (to store in session)
function getUserIdByEmail($email) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    return $user['id'];
}

// This function formats and returns the error messages as an unordered list.
function displayErrors($errors) {
    $output = "<ul>";
    foreach ($errors as $error) {
        $output .= "<li>" . htmlspecialchars($error) . "</li>";
    }
    $output .= "</ul>";
    return $output;
}

// Register new user function (for registering users with hashed passwords)
function registerUser($email, $password, $name) {
    $conn = dbConnect();

    // Hash the password before storing it
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement to insert the user into the users table
    $stmt = $conn->prepare("INSERT INTO users (email, password, name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $hashedPassword, $name);

    // Execute the query and check for success
    if ($stmt->execute()) {
        return true; // Registration successful
    } else {
        return false; // Registration failed
    }
}

// Check if the email is already registered
function isEmailRegistered($email) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    return ($result->num_rows > 0); // Returns true if email is found, false if not
}

// Manually register the user with email "user@gmail.com" and password "user"
$email = "user@gmail.com";
$password = "user";  // Plain-text password that will be hashed
$name = "Test User";  // You can change this to any name you'd like


// Function to add a new student
function addStudent($first_name, $last_name) {
    $conn = dbConnect();
    
    $stmt = $conn->prepare("INSERT INTO students (first_name, last_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $first_name, $last_name);
    
    return $stmt->execute(); // Return true if successful, false otherwise
}
// Function to update student information
function editStudent($student_id, $first_name, $last_name) {
    $conn = dbConnect();
    
    $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?");
    $stmt->bind_param("ssi", $first_name, $last_name, $student_id);
    
    return $stmt->execute(); // Return true if successful, false otherwise
}
// Function to delete a student by student_id
function deleteStudent($student_id) {
    $conn = dbConnect();
    
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    
    return $stmt->execute(); // Return true if successful, false otherwise
}
// Function to get student by student_id
function getStudentById($student_id) {
    $conn = dbConnect();

    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Return the student if found, otherwise return null
    return ($result->num_rows > 0) ? $result->fetch_assoc() : null;
}
function getSubjects() {
    $conn = dbConnect();  // Assuming dbConnect() is a function to connect to your database.
    $result = $conn->query("SELECT * FROM subjects");
    return $result->fetch_all(MYSQLI_ASSOC); // Fetch all subjects
}
// Function to add a new subject to the database
function addSubject($subject_code, $subject_name) {
    $conn = dbConnect();
    $stmt = $conn->prepare("INSERT INTO subjects (subject_code, subject_name) VALUES (?, ?)");
    $stmt->bind_param("ss", $subject_code, $subject_name);
    return $stmt->execute(); // Returns true on success, false on failure
}

// Function to check if a subject already exists
function checkIfSubjectExists($subject_code) {
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT 1 FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $stmt->store_result();
    return $stmt->num_rows > 0; // Returns true if subject exists, false otherwise
}


// Function to assign a subject to a student
function attachSubjectToStudent($student_id, $subject_id) {
    $conn = dbConnect();
    
    // Check if the subject is already assigned to the student
    $stmt = $conn->prepare("SELECT 1 FROM student_subjects WHERE student_id = ? AND subject_id = ?");
    $stmt->bind_param("ii", $student_id, $subject_id);
    $stmt->execute();
    $stmt->store_result();

    // If not already assigned, insert a new record
    if ($stmt->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO student_subjects (student_id, subject_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $student_id, $subject_id);
        return $stmt->execute();  // Return true if successful
    }
    
    return false;  // Return false if subject is already assigned
}
// Function to detach a subject from a student
function detachSubjectFromStudent($student_id, $subject_id) {
    $conn = dbConnect();
    
    // Remove the subject assignment
    $stmt = $conn->prepare("DELETE FROM student_subjects WHERE student_id = ? AND subject_id = ?");
    $stmt->bind_param("ii", $student_id, $subject_id);
    
    return $stmt->execute();  // Return true if successful
}

?>
