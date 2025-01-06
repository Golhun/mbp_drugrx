<!DOCTYPE html>
<html>
<head>
    <style>
        @import url('https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css');
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-xl font-bold mb-4">Reset Your Password</h1>
        <p class="mb-4">Hello,</p>
        <p class="mb-4">Please click the button below to reset your password:</p>
        <a href="{{ reset_link }}" class="inline-block bg-red-500 text-white px-4 py-2 rounded">Reset Password</a>
        <p class="mt-4 text-sm text-gray-600">If you did not request this, please ignore this email.</p>
    </div>
</body>
</html>
