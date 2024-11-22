<?php
session_start();
include('../partials/header.php'); // Include header from the 'partials' folder
require_once('../../functions.php'); // Include the functions file for database operations
include ('../partials/side-bar.php');
// Initialize error message
$errorMessage = "";
// Ensure student_id is set in the URL
if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Connect to the database
    $conn = dbConnect();
    
    // Fetch the student data from the database
    $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
    $stmt->bind_param("s", $student_id); // Change 'i' to 's' since student_id is a string
    $stmt->execute();
    $result = $stmt->get_result();

    // If student found, fetch the details
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
    } else {
        $errorMessage = "Student not found.";
    }

    $stmt->close();
    $conn->close();

    // Handle the deletion if confirmed
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
        // Connect to the database again to delete the student
        $conn = dbConnect();
        $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $student_id); // Again, use 's' for string binding

        if ($stmt->execute()) {
            // Redirect to the register.php after successful deletion
            header("Location: register.php");
            exit(); // Ensure the script stops execution after redirection
        } else {
            $errorMessage = "Failed to delete the student. Please try again.";
        }

        $stmt->close();
        $conn->close();
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

    <!-- Display any error message -->
    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <!-- If student data is found, display details for confirmation -->
    <?php if (isset($student)): ?>
        <div class="bordered-container">
            <strong>Are you sure you want to delete the following student?</strong><br>
            <ul>
                <li><strong>Student ID:</strong> <?= htmlspecialchars($student['student_id']) ?></li>
                <li><strong>First Name:</strong> <?= htmlspecialchars($student['first_name']) ?></li>
                <li><strong>Last Name:</strong> <?= htmlspecialchars($student['last_name']) ?></li>
            </ul>
            <form action="delete.php?student_id=<?= htmlspecialchars($student['student_id']) ?>" method="POST">
                <a href="register.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="confirm_delete" class="btn btn-danger">Delete Student Record</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include('../partials/footer.php'); // Include footer from the 'partials' folder
?>
