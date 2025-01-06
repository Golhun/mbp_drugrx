<!DOCTYPE html>
<html>
<head>
    <style>
        @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css');
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-xl font-bold mb-4">Verify Your Account</h1>
        <p class="mb-4">Hello,</p>
        <p class="mb-4">Please click the button below to verify your email address:</p>
        <a href="{{ verification_link }}" class="inline-block bg-blue-500 text-white px-4 py-2 rounded">Verify Account</a>
        <p class="mt-4 text-sm text-gray-600">If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>
