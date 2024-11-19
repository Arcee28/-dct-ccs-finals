<?php
session_start();
include('../partials/header.php'); // Include header from the 'partials' folder

// Initialize error message
$errorMessage = "";

// Ensure the subject_code is set in the URL
if (isset($_GET['subject_code'])) {
    $subject_code = $_GET['subject_code'];

    // Find the subject by code
    $subject = null;
    foreach ($_SESSION['subjects'] as $s) {
        if ($s['subject_code'] == $subject_code) {
            $subject = $s;
            break;
        }
    }

    // If subject is not found
    if ($subject === null) {
        $errorMessage = "Subject not found.";
    }

    // Handle the deletion if confirmed
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_delete'])) {
        // Remove the subject from session
        foreach ($_SESSION['subjects'] as $key => $s) {
            if ($s['subject_code'] == $subject_code) {
                unset($_SESSION['subjects'][$key]);
                break;
            }
        }

        // Redirect to add.php after successful deletion
        header("Location: add.php");
        exit();
    }
} else {
    $errorMessage = "Subject code is missing.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Subject</title>
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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="add.php">Add Subjects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Delete Subject</li>
        </ol>
    </nav>

    <h3 class="text-left">Delete Subject Confirmation</h3>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger"><?= $errorMessage ?></div>
    <?php endif; ?>

    <?php if ($subject): ?>
        <div class="bordered-container">
            <strong>Are you sure you want to delete the following subject?</strong><br>
            <ul>
                <li><strong>Subject Code:</strong> <?= $subject['subject_code'] ?></li>
                <li><strong>Subject Name:</strong> <?= $subject['subject_name'] ?></li>
            </ul>
            <form action="delete.php?subject_code=<?= $subject['subject_code'] ?>" method="POST">
                <a href="add.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" name="confirm_delete" class="btn btn-primary">Delete Subject</button>
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
