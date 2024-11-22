<?php
session_start();
include('../partials/header.php'); // Corrected path to header.php
require_once('../../functions.php'); // Include the functions file for database operations

// Initialize error and success messages
$errorMessages = [];
$successMessage = "";

// Ensure student_id is passed in the URL
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Fetch student data from the database
    $conn = dbConnect();
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        $errorMessages[] = "Student not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    $errorMessages[] = "Student ID is missing.";
}

// Handle form submission for editing the student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'edit_student') {
    $new_first_name = trim($_POST['first_name']);
    $new_last_name = trim($_POST['last_name']);

    // Validate input
    if (empty($new_first_name) || empty($new_last_name)) {
        $errorMessages[] = "All fields are required.";
    } else {
        // Update the student data in the database
        $conn = dbConnect();
        $stmt = $conn->prepare("UPDATE students SET first_name = ?, last_name = ? WHERE student_id = ?");
        $stmt->bind_param("ssi", $new_first_name, $new_last_name, $student_id);

        if ($stmt->execute()) {
            $successMessage = "Student details updated successfully!";
            header('Location: register.php');
            exit(); // Stop further execution after redirect
        } else {
            $errorMessages[] = "Failed to update the student. Please try again.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            border: 2px solid #dee2e6;
            padding: 30px;
            border-radius: 8px;
            background-color: #ffffff;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container mt-3">
    <h3 class="text-left">Edit Student</h3>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mt-5">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Student</li>
        </ol>
    </nav>

    <!-- Success message -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= $successMessage ?></div>
    <?php endif; ?>

    <!-- Error messages -->
    <?php if (!empty($errorMessages)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errorMessages as $message): ?>
                    <li><?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Edit student form inside the bordered container -->
    <?php if (isset($student)): ?>
        <div class="form-container">
            <form method="POST">
                <input type="hidden" name="action" value="edit_student">
                <input type="hidden" name="student_id" value="<?= $student['student_id'] ?>">

                <div class="form-group">
                    <label for="student_id">Student ID</label>
                    <input type="text" class="form-control" id="student_id" name="student_id" value="<?= $student['student_id'] ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?= $student['first_name'] ?>" required>
                </div>

                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?= $student['last_name'] ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Student</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include('../partials/footer.php'); // Corrected path to footer.php
?>
