<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Reminder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }
        .header {
            font-size: 24px;
            color: #004aad;
            margin-bottom: 20px;
        }
        .content {
            font-size: 16px;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #004aad;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .footer {
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            Reminder Notification
        </div>
        <div class="content">
            <p>Dear Users</p>
            <p>Please renew <strong>{{ $vehicle['type'] }} - {{ $vehicle['model'] }} - {{ $vehicle['nopol'] }}</strong> your extend before it expires on <strong>{{ $vehicle['expired'] }}</strong>.</p>
            <a href="#" class="button">Renew Now</a>
        </div>
        <div class="footer">
            <p>Need help? Call us at <strong>1-800-SAMPLE</strong>.</p>
        </div>
    </div>
</body>
</html>