<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
      body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    background-color: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.email-content {
    padding: 20px;
}

h1 {
    color: #333;
    font-size: 24px;
}

p {
    color: #555;
    line-height: 1.6;
}

.verify-button {
    display: inline-block;
    padding: 10px 20px;
    margin: 20px 0;
    background-color: #28a745;
    color: #ffffff;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.verify-button:hover {
    background-color: #218838;
}

@media (max-width: 600px) {
    .container {
        padding: 10px;
    }
}

    </style>
</head>
<body>
    <div class="container">
        <div class="email-content">
            <h1>Verify Your Email Address</h1>
            <p>Hello [User's Name],</p>
            <p>Thank you for signing up! To complete your registration, please verify your email address by clicking the link below:</p>
            <a href="http://127.0.0.1:8000/api/reset-password/{{$token}}" class="verify-button">Verify Email</a>
            <p>If you did not create an account, no further action is required.</p>
            <p>Best Regards,<br>Astha Foundation Trust</p>
        </div>
    </div>
</body>
</html>
