<?php
session_start();
include('../partials/header.php'); // Corrected path to header.php from the 'partials' folder
// Initialize error message
$errorMessage = "";

// Ensure the student_id is set in the URL
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Find the student by ID
    $student = null;
    foreach ($_SESSION['students'] as $s) {
        if ($s['student_id'] == $student_id) {
            $student = $s;
            break;
        }
    }

    // If student is not found
    if ($student === null) {
        $errorMessage = "Student not found.";
    }

    // Handle the deletion if confirmed
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
        // Remove the student from session
        foreach ($_SESSION['students'] as $key => $s) {
            if ($s['student_id'] == $student_id) {
                unset($_SESSION['students'][$key]);
                break;
            }
        }

        // Redirect back to register.php after successful deletion
        header("Location: register.php");
        exit();
    }
} else {
    $errorMessage = "Student ID is missing.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Student</title>
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
    </style>
</head>
<body>

<div class="container mt-3">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="register.php">Register Student</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Student</li>
        </ol>
    </nav>

    <h3 class="text-left">Delete Student</h3>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <?php if ($student): ?>
        <div class="bordered-container">
            <strong>Are you sure you want to delete the following student?</strong><br>
            <ul>
                <li><strong>Student ID:</strong> <?= $student['student_id'] ?></li>
                <li><strong>First Name:</strong> <?= $student['first_name'] ?></li>
                <li><strong>Last Name:</strong> <?= $student['last_name'] ?></li>
            </ul>
            <form action="delete.php?student_id=<?= $student['student_id'] ?>" method="POST">
                <a href="register.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="confirm_delete" class="btn btn-primary">Delete Student Record</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include('../partials/footer.php'); // Corrected path to footer.php from the 'partials' folder
?>
