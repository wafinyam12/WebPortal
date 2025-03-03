<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Tracking - QR Scanner</title>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .scanner-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        #reader {
            width: 100%;
            max-width: 400px;
            margin: auto;
            display: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .start-button {
            padding: 1rem 2rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .start-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .page-title {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        .result-section {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin: 1rem 0;
        }

        .nav-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
        }

        .tool-details {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-top: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .details-section {
            margin-bottom: 2rem;
        }

        .details-title {
            color: #2c3e50;
            border-bottom: 2px solid #3498db;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .info-row {
            display: flex;
            margin-bottom: 0.8rem;
            align-items: center;
        }

        .info-label {
            font-weight: 600;
            color: #34495e;
            width: 120px;
        }

        .info-details-column {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            height: 100%;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 1.5rem;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table th {
            background-color: #3498db;
            color: white;
            padding: 1rem;
            font-weight: 500;
        }

        .custom-table td {
            padding: 1rem;
            border-bottom: 1px solid #e0e0e0;
        }

        .custom-table tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .loading-spinner {
            display: none;
            text-align: center;
            margin: 2rem 0;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }

            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-label {
                width: 100%;
                margin-bottom: 0.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <h1 class="page-title text-center">
            <i class="fas fa-qrcode me-2"></i>Asset Tracking Scanner
        </h1>

        <div class="scanner-section text-center">
            <button id="startButton" class="btn btn-primary start-button">
                <i class="fas fa-camera me-2"></i>Start Scanning
            </button>
            <div id="reader" class="mt-4"></div>
        </div>

        <div class="result-section">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-search me-2"></i>Scan Result
                </h5>
                <span id="result" class="text-secondary">No QR Code scanned</span>
            </div>
        </div>

        <div class="nav-buttons">
            <a href="{{ url('/') }}" class="btn btn-outline-primary nav-btn">
                <i class="fas fa-home me-2"></i>Home
            </a>
            <a href="{{ route('trackings.tools') }}" class="btn btn-outline-secondary nav-btn">
                <i class="fas fa-redo me-2"></i>Reload
            </a>
        </div>

        <div id="tool-container"></div>

        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Processing scan result...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        const loadingSpinner = document.querySelector('.loading-spinner');
    
        document.getElementById("startButton").addEventListener("click", function () {
            const readerDiv = document.getElementById("reader");
            readerDiv.style.display = "block";
            this.style.display = "none";
    
            html5QrCode.start({ facingMode: "environment" }, {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            }, (qrCodeMessage) => {
                document.getElementById("result").innerHTML = `<span class="text-success">
                    <i class="fas fa-check-circle me-2"></i>${qrCodeMessage}</span>`;
    
                loadingSpinner.style.display = 'block';
    
                html5QrCode.stop().then(() => {
                    readerDiv.style.display = "none";
    
                    const url = `/tracking/tools/${encodeURIComponent(qrCodeMessage)}`;
    
                    $.ajax({
                        url: url,
                        method: "GET",
                        success: function (response) {
                            loadingSpinner.style.display = 'none';
                        
                            if (response.success) {
                                const data = response.data;
                                console.log(data);
                                
                                const toolContainer = document.getElementById('tool-container');
                                toolContainer.innerHTML = `
                                    <div class="tool-details">
                                        <div class="row">
                                            <!-- Left Column - Tool Details -->
                                            <div class="col-md-6 mb-4">
                                                <div class="info-details-column">
                                                    <h4 class="details-title">
                                                        <i class="fas fa-tools me-2"></i>Item Details
                                                    </h4>
                                                    <div class="info-row">
                                                        <span class="info-label">Tool Name:</span>
                                                        <span>${data[0]?.tools?.name || 'N/A'}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Code:</span>
                                                        <span>${data[0]?.tools?.code || 'N/A'}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Brand:</span>
                                                        <span>${data[0]?.tools?.brand || 'N/A'}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Condition:</span>
                                                        <span class="status-badge ${getConditionClass(data[0]?.tools?.condition)}">
                                                            ${data[0]?.tools?.condition || 'N/A'}
                                                        </span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Model:</span>
                                                        <span>${data[0]?.tools?.model || 'N/A'}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Year:</span>
                                                        <span>${data[0]?.tools?.year || 'N/A'}</span>
                                                    </div>
                                                </div>
                                            </div>
    
                                            <!-- Right Column - Transaction History -->
                                            <div class="col-md-6 mb-4">
                                                <div class="info-details-column">
                                                    <h4 class="details-title">
                                                        <i class="fas fa-exchange-alt me-2"></i>Last Transaction
                                                    </h4>
                                                    <div class="info-row">
                                                        <span class="info-label">Activity:</span>
                                                        <span>
                                                            <i class="fas ${getActivityIcon(data[0]?.type)} me-2"></i>
                                                            ${data[0]?.type || 'N/A'}
                                                        </span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">From:</span>
                                                        <span>${data[0]?.source_transactions.name || 'N/A'}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">To:</span>
                                                        <span>${data[0]?.destination_transactions.name || 'N/A'}</span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Location:</span>
                                                        <span>
                                                            <i class="fas fa-map-marker-alt me-2"></i>
                                                            ${data[0]?.last_location || 'N/A'}
                                                        </span>
                                                    </div>
                                                    <div class="info-row">
                                                        <span class="info-label">Date:</span>
                                                        <span>
                                                            <i class="far fa-calendar-alt me-2"></i>
                                                            ${formatDate(data[0]?.created_at) || 'N/A'}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- Transaction History Table Below -->
                                        <div class="mt-4">
                                            <h4 class="details-title">
                                                <i class="fas fa-history me-2"></i>Transaction History
                                            </h4>
                                            <div class="table-container">
                                                <table class="custom-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Activity</th>
                                                            <th>From</th>
                                                            <th>To</th>
                                                            <th>Location</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        ${data.map(item => `
                                                            <tr>
                                                                <td>
                                                                    <i class="fas ${getActivityIcon(item.type)} me-2"></i>
                                                                    ${item.type || 'N/A'}
                                                                </td>
                                                                <td>${item.source_transactions.name || 'N/A'}</td>
                                                                <td>${item.destination_transactions.name || 'N/A'}</td>
                                                                <td>
                                                                    <i class="fas fa-map-marker-alt me-2"></i>
                                                                    ${item.last_location || 'N/A'}
                                                                </td>
                                                                <td>
                                                                    <i class="far fa-calendar-alt me-2"></i>
                                                                    ${formatDate(item.created_at) || 'N/A'}
                                                                </td>
                                                            </tr>
                                                        `).join('')}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            } else {
                                alert(response.message || "Failed to fetch data.");
                            }
                        },
                        error: function (xhr, status, error) {
                            loadingSpinner.style.display = 'none';
                            console.error(error);
                            alert(xhr.responseJSON.message || "An error occurred. Please try again.");
                        }
                    });
                }).catch(err => {
                    loadingSpinner.style.display = 'none';
                    console.error("Failed to stop scanner:", err);
                });
            }, (errorMessage) => {
                console.error(errorMessage);
            });
        });
    
        function getConditionClass(condition) {
            switch (condition?.toLowerCase()) {
                case 'good':
                    return 'bg-success text-white';
                case 'fair':
                    return 'bg-warning text-dark';
                case 'poor':
                    return 'bg-danger text-white';
                default:
                    return 'bg-secondary text-white';
            }
        }
    
        function getActivityIcon(type) {
            switch (type?.toLowerCase()) {
                case 'transfer':
                    return 'fa-exchange-alt';
                case 'return':
                    return 'fa-undo';
                case 'checkout':
                    return 'fa-sign-out-alt';
                default:
                    return 'fa-history';
            }
        }
    
        function formatDate(dateString) {
            if (!dateString) return 'N/A';
            const options = { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }
    </script>
    
</body>
</html>