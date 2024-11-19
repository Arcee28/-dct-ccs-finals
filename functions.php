<?php
// functions.php

// This function returns a hardcoded array of users with email and password.
function getUsers() {
    return [
        ["email" => "user1@gmail.com", "password" => "user1"],
        ["email" => "user2@gmail.com", "password" => "user2"],
        ["email" => "user3@example.com", "password" => "user3"],
        ["email" => "user4@example.com", "password" => "user4"],
        ["email" => "user5@example.com", "password" => "user5"]
    ];
}

// This function validates the login credentials: email and password.
function validateLoginCredentials($email, $password) {
    $errors = [];
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email format.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    return $errors;
}

// This function checks if the email and password match any of the predefined users.
function checkLoginCredentials($email, $password, $users) {
    foreach ($users as $user) {
        if ($user['email'] === $email && $user['password'] === $password) {
            return true;
        }
    }
    return false;
}

// This function formats and returns the error messages as an unordered list.
function displayErrors($errors) {
    $output = "<ul>";
    foreach ($errors as $error) {
        $output .= "<li>" . htmlspecialchars($error) . "</li>";
    }
    $output .= "</ul>";
    return $output;
}
?>
