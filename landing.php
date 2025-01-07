<?php
require_once './components/navbar.php';
require_once './components/footer.php';
require_once './components/hero.php';
require_once './components/companies.php';
require_once './components/features.php';
require_once './components/how-it-works.php';
require_once './components/demo-request.php';
require_once './components/faq.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drug Interaction Checker</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
        body {
            font-family: 'Inter', sans-serif;
            scroll-behavior: smooth; /* Smooth scrolling using CSS */
        }
        [data-scroll] {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }
        [data-scroll-visible] {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
</head>
<body class="bg-gray-50">
    <!-- Render the navbar -->
    <?php renderNavbar(); ?>
        <!-- Hero Section -->
    <?php renderHeroSection(); ?>
        <!-- Companies Section -->
    <?php renderCompaniesSection(); ?>
        <!-- Features Section -->
    <?php renderFeaturesSection(); ?>
    <!-- How Does It Work Section -->
    <?php renderHowItWorksSection(); ?>
    <!-- Demo Request Section -->
    <?php renderDemoRequestSection(); ?>
    <!-- FAQ Section -->
    <?php renderFAQSection(); ?>
    <!-- Render the footer -->
    <?php renderFooter(); ?>
</body>
<script src="js/faq.js"></script>
</html>

