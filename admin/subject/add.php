<?php
session_start();
include('../partials/header.php'); // Include header from the 'partials' folder
include('../partials/side-bar.php');
include('../../functions.php'); // Include the function.php file

// Initialize error and success messages
$errorMessages = [];
$successMessage = "";

// Handle form submission for adding a subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_subject') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);

    // Check if fields are empty
    if (empty($subject_code) || empty($subject_name)) {
        $errorMessages[] = "All fields are required.";
    } else {
        // Check if the subject code already exists using a helper function from function.php
        if (checkIfSubjectExists($subject_code)) {
            $errorMessages[] = "Subject code '$subject_code' already exists!";
        } else {
            // Insert the new subject into the database using the helper function from function.php
            if (addSubject($subject_code, $subject_name)) {
                $successMessage = "Subject added successfully!";
            } else {
                $errorMessages[] = "Error adding subject. Please try again later.";
            }
        }
    }
}
?>

<!-- HTML Form for Adding Subject -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bordered-container {
            border: 2px solid #ddd;
            padding: 20px;
            margin-top: 20px;
            border-radius: 8px;
        }
        .subject-table th, .subject-table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<div class="container mt-3">
    <!-- Breadcrumb navigation -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mt-5">
            <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Subjects</li>
        </ol>
    </nav>

    <!-- Success message -->
    <?php if ($successMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($successMessage) ?></div>
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

    <!-- Add subject form -->
    <div class="bordered-container">
        <form method="POST">
            <h3 class="text-left">Add New Subject</h3>
            <input type="hidden" name="action" value="add_subject">

            <div class="form-group">
                <label for="subject_code">Subject Code</label>
                <input type="text" class="form-control" id="subject_code" name="subject_code" required>
            </div>

            <div class="form-group">
                <label for="subject_name">Subject Name</label>
                <input type="text" class="form-control" id="subject_name" name="subject_name" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Subject</button>
        </form>
    </div>

    <h3 class="mt-5">Subject List</h3>

    <!-- Subject List Table -->
    <div class="bordered-container">
        <table class="table subject-table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch subjects from the database using the function from function.php
                $subjects = getSubjects();

                // Check if the subjects array is not empty
                if (!empty($subjects)): ?>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?= htmlspecialchars($subject['subject_code']) ?></td>
                            <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                            <td>
                                <a href="edit.php?subject_code=<?= htmlspecialchars($subject['subject_code']) ?>" class="btn btn-info btn-sm">Edit</a>
                                <a href="delete.php?subject_code=<?= htmlspecialchars($subject['subject_code']) ?>" class="btn btn-danger btn-sm">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No subjects added yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
include('../partials/footer.php'); // Include footer from the 'partials' folder
?>
