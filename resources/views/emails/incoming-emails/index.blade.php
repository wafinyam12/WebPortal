<!-- resources/views/emails/incoming-inventory.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Incoming Inventory Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 20px;
        }
        .content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        .info-item {
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .warning {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="" alt="PT Utomodeck Metal Works" class="logo">
        <h1>Incoming Inventory Notification Date : {{ $incoming['expired'] }}</h1>
    </div>

    <div class="content">
        
        <div class="info-item">
            <span class="info-label">Branch:</span>
            <span>{{ $incoming['branch'] }}</span>
        </div>
        
        <div class="info-item">
            <span class="info-label">Item:</span>
            <span>{{ $incoming['item'] }}</span>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated notification from PT Utomodeck Metal Works Inventory System.</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} PT Utomodeck Metal Works. All rights reserved.</p>
    </div>
</body>
</html>
