<?php
session_start();
include('../partials/header.php'); // Include header from the 'partials' folder

// Initialize error and success messages
$errorMessages = [];
$successMessage = "";

// Handle form submission for adding a subject
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'add_subject') {
    $subject_code = trim($_POST['subject_code']);
    $subject_name = trim($_POST['subject_name']);

    // Check if fields are empty
    if (empty($subject_code) || empty($subject_name)) {
        $errorMessages[] = "All fields are required.";
    } else {
        // Prevent re-adding subject with the same code
        $exists = false;
        foreach ($_SESSION['subjects'] as $subject) {
            if ($subject['subject_code'] == $subject_code) {
                $exists = true;
                break;
            }
        }

        if ($exists) {
            $errorMessages[] = "Subject code '$subject_code' already exists!";
        } else {
            // Add the new subject to session
            $_SESSION['subjects'][] = [
                'subject_code' => $subject_code,
                'subject_name' => $subject_name
            ];
            $successMessage = "Subject added successfully!";
            header('Location: add.php'); // Refresh the page after adding the subject
            exit(); // Stop further execution after redirect
        }
    }
}

?>

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
                <?php if (!empty($_SESSION['subjects'])): ?>
                    <?php foreach ($_SESSION['subjects'] as $subject): ?>
                        <tr>
                            <td><?= htmlspecialchars($subject['subject_code']) ?></td>
                            <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                            <td>
                                <!-- Edit button (blue) -->
                                <a href="edit.php?subject_code=<?= htmlspecialchars($subject['subject_code']) ?>" class="btn btn-info btn-sm">Edit</a>

                                <!-- Delete button (red) -->
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
