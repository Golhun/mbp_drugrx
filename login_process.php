<?php
require_once 'config.php';
require_once 'log_activity.php'; // to log user actions
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']);

    try {
        // Fetch user data
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND is_active = 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Set session for logged-in user
            $_SESSION['user_id'] = $user['id'];

            // Update last_login in users table (optional)
            $stmt2 = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
            $stmt2->execute([':id' => $user['id']]);

            // Log "login" event in activity_log
            logActivity($db, $user['id'], 'login', null);

            // Handle "Remember Me" functionality
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $stmt3 = $db->prepare("
                    INSERT INTO remember_me (user_id, token_hash, expires_at)
                    VALUES (:user_id, :token_hash, :expires_at)
                ");
                $stmt3->execute([
                    ':user_id' => $user['id'],
                    ':token_hash' => hash('sha256', $token),
                    ':expires_at' => date('Y-m-d H:i:s', strtotime('+30 days'))
                ]);
                setcookie('remember_me', $token, time() + (30 * 86400), '/');
            }

            header('Location: dashboard.php');
            exit;
        } else {
            // Invalid credentials
            $_SESSION['error'] = 'Invalid credentials or account not activated.';
            header('Location: login.php');
            exit;
        }
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['error'] = 'An error occurred. Please try again later.';
        header('Location: login.php');
        exit;
    }
} else {
    // Redirect if accessed without POST
    header('Location: login.php');
    exit;
}
