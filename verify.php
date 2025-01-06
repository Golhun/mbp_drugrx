<?php
require_once 'config.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = $db->prepare("UPDATE users SET is_active = 1, verification_token = NULL WHERE verification_token = :token");
    $stmt->execute(['token' => $token]);

    if ($stmt->rowCount() > 0) {
        echo 'Your account has been successfully verified! You can now log in.';
        header('refresh:3;url=login.php');
    } else {
        echo 'Invalid or expired token.';
    }
}
?>
