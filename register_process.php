<?php
require_once 'config.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize user inputs
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
    $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validation checks
    $errors = [];
    if (empty($first_name)) {
        $errors[] = "First Name is required.";
    }
    if (empty($last_name)) {
        $errors[] = "Last Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email address.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // If there are validation errors, redirect back with errors
    if (!empty($errors)) {
        session_start();
        $_SESSION['errors'] = $errors;
        header("Location: register.php");
        exit;
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into the database
    try {
        $db = new PDO('mysql:host=localhost;dbname=mbp_drugrx', 'root', ''); // Update with your DB credentials
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare("
            INSERT INTO users (first_name, last_name, email, password, is_active, created_at, updated_at)
            VALUES (:first_name, :last_name, :email, :password, :is_active, :created_at, :updated_at)
        ");

        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':password' => $hashed_password,
            ':is_active' => 1, // Set active by default, adjust as needed
            ':created_at' => date('Y-m-d H:i:s'),
            ':updated_at' => date('Y-m-d H:i:s')
        ]);

        // Redirect to login page after successful registration
        header("Location: login.php?success=1");
        exit;

    } catch (PDOException $e) {
        // Log error and show a generic message
        error_log($e->getMessage());
        header("Location: register.php?error=Something went wrong. Please try again.");
        exit;
    }
} else {
    // Redirect if accessed without form submission
    header("Location: register.php");
    exit;
}
?>
