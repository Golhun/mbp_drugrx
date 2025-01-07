<?php
require_once './components/navbar.php';
require_once './components/footer.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - mbp_drugrx</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-bg {
            background-image: url('https://images.unsplash.com/photo-1544991875-5dc1b05f607d?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <?php renderNavbar(); ?>

    <!-- Hero Section -->
    <section class="relative hero-bg">
        <div class="bg-gradient-to-b from-pink-600/20 to-pink-600/40 py-24 sm:py-32">
            <div class="container mx-auto px-6 sm:px-12 text-center">
                <h1 class="text-4xl font-bold text-white sm:text-5xl lg:text-6xl">The mbp_drugrx Story</h1>
                <p class="mt-4 text-lg text-pink-200 sm:mt-6">Making healthcare safer, one interaction at a time.</p>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-6 sm:px-12">
            <p class="text-gray-700 text-lg leading-relaxed sm:text-xl text-center max-w-4xl mx-auto">
                At mbp_drugrx, we believe in empowering individuals and healthcare professionals to make informed decisions about medication. Our mission is to provide a seamless platform that identifies potential drug interactions and offers suitable substitutes. 
                <br><br>
                With cutting-edge technology and a dedication to accuracy, mbp_drugrx aims to enhance medication safety. Whether you're managing complex prescriptions or exploring alternatives, we're here to simplify the process with reliable and transparent tools. 
                <br><br>
                We're committed to making healthcare better for everyone, through our innovative software, trusted data, and a supportive community.
            </p>
        </div>
    </section>

    <!-- Footer -->
    <?php renderFooter(); ?>

</body>
</html>
