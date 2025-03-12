<!DOCTYPE html>
<html>
<head>
    <title>Password Reset Request</title>
</head>
<body>
    <h1>Innoscripta AG</h1>
    <p>Hello {{ $user->name }},</p>
    <p>We received a request to reset your password. Click the button below to reset it:</p>
    <p>
        <a href="{{ $resetUrl }}" style="padding:10px 20px; background:#007bff; color:white; text-decoration:none;">
            Reset Password
        </a>
    </p>
    <p>If you did not request this, you can ignore this email.</p>
</body>
</html>
