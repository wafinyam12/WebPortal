<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <img src="{{ asset('template/photo/company-logo.png') }}" alt="PT Utomodeck Metal Works" class="logo">
        <h1>Attention Cost Bid Notifications</h1>
    </div>

    <div class="content">
        <div class="info-item">
            <span class="info-label">Project Name:</span>
            <span>{{ $costbid['project_name'] }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Bid Date:</span>
            <span>{{ $costbid['bid_date'] }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Selected Vendor:</span>
            <span>{{ $costbid['selected_vendor'] }}</span>
        </div>
        <div class="info-item">
            <span class="info-label">Notes:</span>
            <span>{{ $costbid['notes'] ?? 'No notes' }}</span>
        </div>

        <!-- Add button here Approve or Reject -->
        <div class="info-item">
            <a href="{{ url('all-approval/costbid/'. $costbid['token'] .'/approved') }}" class="button btn btn-success">Approve</a>
            <a href="{{ url('all-approval/costbid/'. $costbid['token'] .'/rejected') }}" class="button btn btn-danger">Reject</a>
        </div>

    </div>

    <div class="footer">
        <p>This is an automated notification from PT Utomodeck Metal Works Inventory System.</p>
        <p>If you have any questions, please contact our support team.</p>
        <p>&copy; {{ date('Y') }} PT Utomodeck Metal Works. All rights reserved.</p>
    </div>
</body>
</html>