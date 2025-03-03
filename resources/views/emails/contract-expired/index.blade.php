<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background-color: #4e73df;
            color: white;
            border-radius: 8px 8px 0 0;
        }
        .header img {
            max-width: 120px;
        }
        .content {
            padding: 20px;
            text-align: center;
        }
        .content h2 {
            color: #4e73df;
            margin-bottom: 20px;
        }
        .content p {
            margin: 10px 0;
            color: #555;
        }
        .footer {
            text-align: center;
            padding: 15px;
            font-size: 0.9em;
            color: #777;
            border-top: 1px solid #eee;
            margin-top: 20px;
        }
        .footer a {
            color: #4e73df;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #4e73df;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .button:hover {
            background-color: #3b5bbf;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <img src="https://utomodeck.com/wp-content/uploads/2017/08/LOGO-TRANSPARANT-2.png" alt="Company Logo">
            <h1>Reminder Notification</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>Dear Legal Department</h2>
            <p>This is a reminder to renew the {{ $contract['name'] }} contract for <strong>{{ $contract['company'] }}</strong> is set to expire on <strong>{{ $contract['expired'] }}</strong>. There are <strong>{{ $contract['remaining_days'] }} days</strong> remaining until the expiration date.</p>
            <p>Please take the necessary actions to renew or address this matter before the deadline.</p>
            <a href="#" class="button">Renew Now</a>
            <p>Best regards,</p>
            <p><strong>Your Company Name</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
            <p>Contact us at <a href="mailto:support@yourcompany.com">support@yourcompany.com</a></p>
        </div>
    </div>
</body>
</html>