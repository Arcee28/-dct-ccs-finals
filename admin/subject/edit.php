<?php
session_start();

// Start output buffering to prevent "headers already sent" error
ob_start();

include('../partials/header.php'); // Include header from the 'partials' folder
include ('../partials/side-bar.php');

// Initialize error and success messages
$errorMessages = [];
$successMessage = "";

// Check if subject_code is passed in the URL
if (isset($_GET['subject_code'])) {
    $subject_code = $_GET['subject_code'];

    // Connect to the database and fetch subject details using subject_code
    include('../../functions.php'); // Include the necessary function to fetch subject data from the database
    $conn = dbConnect(); // Assuming dbConnect() is a function to connect to your database
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE subject_code = ?");
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();
    $subject = $result->fetch_assoc();

    // If subject not found, show error
    if ($subject === null) {
        $errorMessages[] = "Subject not found.";
    }

    // Handle form submission to update the subject
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'edit_subject') {
        $new_subject_code = trim($_POST['subject_code']);
        $new_subject_name = trim($_POST['subject_name']);

        // Validate inputs
        if (empty($new_subject_code) || empty($new_subject_name)) {
            $errorMessages[] = "All fields are required.";
        } else {
            // Check if the new subject code already exists (other than the current one)
            $exists = false;
            $stmt = $conn->prepare("SELECT subject_code FROM subjects WHERE subject_code = ? AND subject_code != ?");
            $stmt->bind_param("ss", $new_subject_code, $subject_code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $exists = true;
            }

            if ($exists) {
                $errorMessages[] = "Subject code '$new_subject_code' already exists!";
            } else {
                // Update the subject in the database
                $stmt = $conn->prepare("UPDATE subjects SET subject_code = ?, subject_name = ? WHERE subject_code = ?");
                $stmt->bind_param("sss", $new_subject_code, $new_subject_name, $subject_code);
                $stmt->execute();

                // Success message and redirect
                $successMessage = "Subject updated successfully!";
                // Redirect after successful update
                header('Location: add.php'); // Redirect back to the subject list
                exit(); // Stop further execution after redirect
            }
        }
    }
} else {
    $errorMessages[] = "Subject code is missing.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Subject</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bordered-container {
            border: 2px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container mt-3">
    <!-- Breadcrumb navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mt-5">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Manage Subjects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Subject</li>
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

    <!-- Edit subject form -->
    <?php if ($subject !== null): ?>
        <div class="bordered-container">
            <form method="POST">
                <h3 class="text-left">Edit Subject</h3>
                <input type="hidden" name="action" value="edit_subject">
                <input type="hidden" name="subject_code" value="<?= htmlspecialchars($subject['subject_code']) ?>">

                <div class="form-group">
                    <label for="subject_code">Subject Code</label>
                    <input type="text" class="form-control" id="subject_code" name="subject_code" value="<?= htmlspecialchars($subject['subject_code']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="subject_name">Subject Name</label>
                    <input type="text" class="form-control" id="subject_name" name="subject_name" value="<?= htmlspecialchars($subject['subject_name']) ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Subject</button>
                <a href="add.php" class="btn btn-secondary">Cancel</a>
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

