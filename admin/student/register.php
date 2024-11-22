<?php
session_start();
include('../partials/header.php'); // Include header from the 'partials' folder
require_once('../../functions.php'); // Include the functions file for database operations
include ('../partials/side-bar.php');
// Initialize error and success messages
$errorMessages = [];
$successMessage = "";

// Handle form submission for adding a student
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['action'] == 'add_student') {
    $student_id = trim($_POST['student_id']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);

    if (empty($student_id) || empty($first_name) || empty($last_name)) {
        $errorMessages[] = "All fields are required.";
    } else {
        // Check if student ID already exists in the database
        $conn = dbConnect();
        $stmt = $conn->prepare("SELECT * FROM students WHERE student_id = ?");
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errorMessages[] = "Student ID '$student_id' already exists!";
        } else {
            // Insert student into the database
            $stmt = $conn->prepare("INSERT INTO students (student_id, first_name, last_name) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $student_id, $first_name, $last_name);

            if ($stmt->execute()) {
                $successMessage = "Student added successfully!";
            } else {
                $errorMessages[] = "Failed to add the student. Please try again.";
            }
        }

        $stmt->close();
        $conn->close();
    }
}

// Fetch the list of students from the database
$conn = dbConnect();
$result = $conn->query("SELECT * FROM students");
$students = $result->fetch_all(MYSQLI_ASSOC);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
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
            <li class="breadcrumb-item active" aria-current="page">Register Student</li>
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

    <!-- Add student form inside a bordered container -->
    <div class="bordered-container">
        <form method="POST">
            <h3 class="text-left">Register a New Student</h3>
            <input type="hidden" name="action" value="add_student">

            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="number" class="form-control" id="student_id" name="student_id" required>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>

            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>

    <h3 class="mt-5">List of Students</h3>

    <!-- Table with a border around each cell -->
    <div class="bordered-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($students)): ?>
                    <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['student_id']) ?></td>
                            <td><?= htmlspecialchars($student['first_name']) ?></td>
                            <td><?= htmlspecialchars($student['last_name']) ?></td>
                            <td>
                                <!-- Edit and delete options -->
                                <a href="edit.php?student_id=<?= htmlspecialchars($student['student_id']) ?>" class="btn btn-info btn-sm">Edit</a>
                                <a href="delete.php?student_id=<?= htmlspecialchars($student['student_id']) ?>" class="btn btn-danger btn-sm">Delete</a>
                                <a href="attach-subject.php?student_id=<?= htmlspecialchars($student['student_id']) ?>" class="btn btn-warning btn-sm">Attach Subject</a>


                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No students added yet.</td>
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
