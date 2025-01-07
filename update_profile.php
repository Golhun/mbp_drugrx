<?php
require_once 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize inputs
    $first_name = trim(filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING));
    $last_name = trim(filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING));
    $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validation
    $errors = [];
    if (empty($first_name)) $errors[] = "First Name is required.";
    if (empty($last_name)) $errors[] = "Last Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email address.";
    if (!empty($password) && $password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header("Location: dashboard.php");
        exit;
    }

    // Update the database
    try {
        $stmt = $db->prepare("
            UPDATE users 
            SET first_name = :first_name, last_name = :last_name, email = :email, updated_at = :updated_at
            WHERE id = :id
        ");

        $params = [
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':id' => $_SESSION['user_id']
        ];

        // If password is provided, hash it and update
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $db->prepare("
                UPDATE users 
                SET first_name = :first_name, last_name = :last_name, email = :email, password = :password, updated_at = :updated_at
                WHERE id = :id
            ");
            $params[':password'] = $hashed_password;
        }

        $stmt->execute($params);
        $_SESSION['success'] = "Profile updated successfully.";
        header("Location: dashboard.php");
        exit;

    } catch (PDOException $e) {
        error_log("Error updating profile: " . $e->getMessage());
        $_SESSION['errors'] = ["An error occurred. Please try again later."];
        header("Location: dashboard.php");
        exit;
    }
} else {
    header("Location: dashboard.php");
    exit;
}
