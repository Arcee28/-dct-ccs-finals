<?php
session_start();
include('../partials/header.php');
require_once('../../functions.php');
include('../partials/side-bar.php');

// Initialize error and success messages
$errorMessage = "";
$successMessage = "";

// Ensure student_id is set in the URL
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Connect to the database and fetch student data
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        $errorMessage = "Student not found.";
    }

    // Fetch the subjects assigned to the student along with the subject code and name
    $subjects_query = $conn->prepare("
        SELECT subjects.subject_code, subjects.subject_name, student_subjects.grade
        FROM subjects
        JOIN student_subjects ON subjects.id = student_subjects.subject_id
        WHERE student_subjects.student_id = ?");
    $subjects_query->bind_param("s", $student_id);
    $subjects_query->execute();
    $subjects_result = $subjects_query->get_result();
    $subjects = [];
    while ($row = $subjects_result->fetch_assoc()) {
        $subjects[] = $row;
    }
    // Handle the detachment if confirmed
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['detach_subject'])) {
        $subject_code = $_POST['subject_code'];

        // Delete the subject association from student_subject table
        $stmt = $conn->prepare("
            DELETE FROM student_subjects
            WHERE student_id = ? AND subject_id = (SELECT id FROM subjects WHERE subject_code = ? LIMIT 1)
        ");
        $stmt->bind_param("ss", $student_id, $subject_code);
        if ($stmt->execute()) {
            $successMessage = "Subject successfully detached.";
            // Redirect after detaching the subject
            header("Location: dettach-subject.php?student_id=$student_id");
            exit(); // Ensure the script stops execution after redirection
        } else {
            $errorMessage = "Failed to detach the subject. Please try again.";
        }
        $stmt->close();
    }

    $stmt->close();
    $conn->close();
} else {
    $errorMessage = "Student ID is missing.";
}

// Retrieve the subject details from GET parameters (subject code and name)
$subject_code = isset($_GET['subject_code']) ? $_GET['subject_code'] : '';
$subject_name = isset($_GET['subject_name']) ? $_GET['subject_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detach Subject - Student Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bordered-container {
            border: 2px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }
        body {
            background-color: white;
        }
        .subject-list li {
            margin-bottom: 10px;
        }
        .btn-custom {
            margin-top: 5px;
        }
        .cancel-btn {
            background-color: #f0ad4e;
            color: white;
        }
        .detach-btn {
            background-color: #d9534f;
            color: white;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container mt-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detach Subject</li>
        </ol>
    </nav>

    <h3 class="text-left">Detach Subject from Student</h3>

    <!-- Display any error or success message -->
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php elseif ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>

    <!-- If student data is found, display details -->
    <?php if (isset($student)): ?>
        <div class="bordered-container">
            <strong>Student Details:</strong>
            <ul>
                <li><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></li>
                <li><strong>First Name:</strong> <?= htmlspecialchars($student['first_name']) ?></li>
                <li><strong>Last Name:</strong> <?= htmlspecialchars($student['last_name']) ?></li>
                <!-- Display the Subject Details below the Last Name -->
                <?php if (count($subjects) > 0): ?>
                    <li><strong>Assigned Subjects:</strong></li>
                    <?php foreach ($subjects as $subject): ?>
                        <li>
                            <strong>Subject Code:</strong> <?= htmlspecialchars($subject['subject_code']) ?>
                            <br>
                            <strong>Subject Name:</strong> <?= htmlspecialchars($subject['subject_name']) ?>
                            <br>
                            <strong>Grade:</strong> <?= htmlspecialchars($subject['grade']) ?>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="subject_code" value="<?= htmlspecialchars($subject['subject_code']) ?>">
                                <!-- Cancel button instead of Detach -->
                                <button type="button" class="btn btn-warning btn-sm cancel-btn">Cancel</button>
                                <!-- New button to detach subject -->
                                <button type="submit" name="detach_subject" class="btn btn-danger btn-sm detach-btn">Detach Subject from Student</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

            <!-- Display subject code and name if passed from attach-subject.php -->
            <?php if ($subject_code && $subject_name): ?>
                <div class="mt-3">
                    <strong>Subject to Detach:</strong>
                    <ul>
                        <li><strong>Subject Code:</strong> <?= htmlspecialchars($subject_code) ?></li>
                        <li><strong>Subject Name:</strong> <?= htmlspecialchars($subject_name) ?></li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Detach Subject buttons -->
            <div class="btn-container">
                <!-- Cancel button on the left -->
                <a href="register.php" class="btn btn-secondary">Cancel</a>
                <!-- Detach button on the right -->
                <button type="submit" name="detach_subject" class="btn btn-danger">
                    Detach Subject from Student
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include('../partials/footer.php');
?>
