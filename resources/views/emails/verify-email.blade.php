<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
    <h1>Innoscripta</h1>
    <p>Hi {{ $user->name }},</p>
    <p>Please click the link below to verify your email address:</p>
    <p><a href="{{ $verificationUrl }}">Verify Email</a></p>
    <p>This link will expire in 60 minutes.</p>
</body>
</html>
