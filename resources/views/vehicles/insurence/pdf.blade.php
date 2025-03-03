<!DOCTYPE html>
<html>
<head>
    <title>Insurence Report</title>
    <style>
        /* PDF Styling */
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .header > div {
            display: table-cell;
            vertical-align: middle;
        }
        .company-logo img {
            max-height: 80px;
            max-width: 200px;
        }
        .document-title {
            text-align: center;
        }
        .document-title h1 {
            margin: 0;
            font-size: 18px;
        }
        .document-title p {
            margin: 5px 0 0;
        }
        .container {
            max-width: 100%;
            padding: 25px;
        }
        
        .section {
            margin-bottom: 25px;
            padding: 15px;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
        }
        
        .grid-2col {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .data-item {
            margin-bottom: 8px;
        }
        
        .label {
            font-weight: 600;
            color: #2c3e50;
            display: inline-block;
            width: 140px;
        }
        
        .value {
            color: #666;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 500;
        }
        
        .badge-success {
            background: #28a745;
            color: white;
        }
        
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        
        .photo-box {
            margin-top: 15px;
            max-width: 180px;
        }
        
        .photo-box img {
            width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .notes-section {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('template/photo/company-logo.png') }}" alt="logo">
        </div>
        <div class="document-title">
            <h1>Insurence Report</h1>
            <p>Document Number: VMR-0JWTOS</p>
        </div>
        {{-- <div class="qr-code">
            {!! QrCode::size(80)->generate(route('costbid.detail', $costbid->id)) !!}
        </div> --}}
        <div class="company-logo text-right">
            <img src="{{ public_path('template/photo/company-logo.png') }}" alt="logo">
        </div>
    </div>
    <div class="container">
        <!-- Maintenance Details -->
        <div class="section">
            <h3 style="margin-bottom: 15px;">Insurenc Information</h3>
            <div class="grid-2col">
                <div>
                    <div class="data-item">
                        <span class="label">Maintenance Code:</span>
                        <span class="value"></span>
                    </div>
                    <div class="data-item">
                        <span class="label">Date:</span>
                        <span class="value">March 19, 2024</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Status:</span>
                        <span class="badge badge-success">Completed</span>
                    </div>
                </div>
                <div>
                    {{-- <div class="data-item">
                        <span class="label">Mileage:</span>
                        <span class="value">23,541 km</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Cost:</span>
                        <span class="value">$894.25</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Next Service:</span>
                        <span class="value">January 26, 2026</span>
                    </div> --}}
                </div>
            </div>

            <div class="notes-section">
                <div class="data-item">
                    <div class="label">Service Description:</div>
                    <p>Sunt quod temporibus.</p>
                </div>
                
                <div class="data-item">
                    <div class="label">Technician Notes:</div>
                    <p>Et molestiae suscipit aperiam distinctio ipsam dicta ex. Architecto fuga eum impedit quia. Inventore reiciendis omnis expedita laborum. Quaerat rerum ut enim et ea soluta dignissimos.</p>
                </div>
            </div>
        </div>

        <!-- Vehicle Details -->
        <div class="section">
            <h3 style="margin-bottom: 15px;">Vehicle Information</h3>
            
            <div class="grid-2col">
                <div>
                    <div class="data-item">
                        <span class="label">Brand/Model:</span>
                        <span class="value">Toyota Focus</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Year:</span>
                        <span class="value">2001</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Color:</span>
                        <span class="value">MediumSlateBlue</span>
                    </div>
                </div>
                
                <div>
                    <div class="data-item">
                        <span class="label">License Plate:</span>
                        <span class="value">AWJ 50</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Transmission:</span>
                        <span class="value">Automatic</span>
                    </div>
                    <div class="data-item">
                        <span class="label">Status:</span>
                        <span class="badge badge-danger">Inactive</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            Generated on February 4, 2025 01:32<br>
            © 2025 AutoCare Service Center - Confidential Document
        </div>
    </div>
</body>
</html>