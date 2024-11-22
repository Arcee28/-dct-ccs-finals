<?php
session_start();
include('../partials/header.php');
require_once('../../functions.php');
include('../partials/side-bar.php');

// Get the student ID and subject ID from the URL query parameters
$student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
$subject_id = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;

if (!$student_id || !$subject_id) {
    echo "Invalid student or subject ID.";
    exit;
}

// Fetch student details based on the student_id
$conn = dbConnect();
$stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_result = $stmt->get_result();
$student = $student_result->fetch_assoc();

// If no student found, exit
if (!$student) {
    echo "Student not found.";
    exit;
}

// Fetch subject details based on the subject_id
$stmt = $conn->prepare("SELECT * FROM subjects WHERE id = ?");
$stmt->bind_param("i", $subject_id);
$stmt->execute();
$subject_result = $stmt->get_result();
$subject = $subject_result->fetch_assoc();

// If no subject found, exit
if (!$subject) {
    echo "Subject not found.";
    exit;
}

// Handle form submission for assigning grade
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_grade'])) {
    $grade = isset($_POST['grade']) ? $_POST['grade'] : '';

    // Insert or update the grade for the student and subject
    $stmt = $conn->prepare("REPLACE INTO student_subjects (student_id, subject_id, grade) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $student_id, $subject_id, $grade);

    if ($stmt->execute()) {
        // Redirect back to attach-subject.php with the student_id
        header("Location: attach-subject.php?student_id=" . urlencode($student_id));
        exit;
    } else {
        echo "<script>alert('Failed to assign grade.');</script>";
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Grade - Student Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mt-5">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assign Grade</li>
        </ol>
    </nav>

    <!-- Display Student and Subject Information -->
    <div class="bordered-container">
        <h3>Student and Subject Information</h3>
        <ul>
            <li><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></li>
            <li><strong>Name:</strong> <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></li>
            <li><strong>Subject Code:</strong> <?= htmlspecialchars($subject['subject_code']) ?></li>
            <li><strong>Subject Name:</strong> <?= htmlspecialchars($subject['subject_name']) ?></li>
        </ul>
    </div>

    <hr>

    <!-- Assign Grade Form -->
    <div class="bordered-container">
        <h3>Assign Grade</h3>
        <form method="POST">
            <div class="form-group">
                <label for="grade">Grade</label>
                <input type="text" name="grade" class="form-control" id="grade" required>
            </div>
            <button type="submit" class="btn btn-primary" name="assign_grade">Assign Grade</button>
        </form>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include('../partials/footer.php');
?>
