<!DOCTYPE html>
<html>
<head>
    <title>Welcome to {{ config('app.name') }} - Your Account Details</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }
        .credentials {
            background-color: #f8f9fa;
            border-left: 4px solid #3498db;
            padding: 15px;
            margin: 20px 0;
        }
        .credential-label {
            font-weight: bold;
            color: #2c3e50;
        }
        .credential-value {
            font-family: monospace;
            background-color: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 0.9em;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Welcome to {{ config('app.name') }}, {{ $user->name }}!</h2>
        
        <p>We're excited to have you join our automobile discussion community. Your account has been successfully verified!</p>
        
        <p>As requested, we've generated secure login credentials for you to access your account:</p>
        
        <div class="credentials">
            <p><span class="credential-label">Email Address:</span><br>
            <span class="credential-value">{{ $user->email }}</span></p>
            
            <p><span class="credential-label">Password:</span><br>
            <span class="credential-value">{{ $password }}</span></p>
        </div>
        
        <p>For security reasons, we recommend changing this temporary password after your first login.</p>
        
        <p><a href="{{ url('/login') }}" class="button">Login to Your Account</a></p>
        
        <p>If you have any questions or need assistance, feel free to reach out to our support team.</p>
        
        <div class="footer">
            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
            
            <p><small>This email contains important account information. Please keep it secure.</small></p>
        </div>
    </div>
</body>
</html>