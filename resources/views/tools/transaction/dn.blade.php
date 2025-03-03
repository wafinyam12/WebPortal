<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $deliveryNote->type }} - {{ $deliveryNote->document_code }}</title>
    <style>
        @page {
            margin: 0.5cm 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .company-logo, .document-title, .qr-code {
            display: table-cell;
            vertical-align: middle;
        }
        .company-logo {
            width: 60px;
            text-align: left;
        }
        .company-logo img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        .document-title {
            text-align: center;
            padding: 0 20px;
        }
        .document-title h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .document-title p {
            margin: 5px 0 0;
            font-size: 12px;
            color: #666;
        }
        .qr-code {
            width: 60px;
            text-align: right;
        }
        .qr-code img {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        .doc-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .doc-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .doc-info td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .doc-info .label {
            font-weight: bold;
            width: 120px;
        }
        .address-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .address-box {
            border: 1px solid #999;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #f9f9f9;
        }
        .address-box h3 {
            margin: 0 0 5px;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
            font-size: 12px;
            color: #333;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #999;
            padding: 6px 8px;
            font-size: 11px;
        }
        .items-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        .items-table td {
            vertical-align: top;
        }
        .items-table .center {
            text-align: center;
        }
        .items-table .right {
            text-align: right;
        }
        .notes-section {
            margin-bottom: 30px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .notes-section h3 {
            margin: 0 0 5px;
            font-size: 12px;
        }
        .signatures {
            margin-top: 40px;
            page-break-inside: avoid;
        }
        .signature-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
        }
        .signature-box {
            display: table-cell;
            width: 33.33%;
            padding: 10px;
            text-align: center;
            vertical-align: bottom;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            padding: 10px 0;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
        }
        .stamp-box {
            position: absolute;
            right: 50px;
            top: 50%;
            border: 2px solid #999;
            width: 100px;
            height: 100px;
            text-align: center;
            line-height: 100px;
            font-style: italic;
            color: #999;
            transform: rotate(-15deg);
        }
        /* .qr-code {
            position: absolute;
            right: 10px;
            top: 10px;
            border: 1px solid #ccc;
            padding: 5px;
            background: white;
        } */
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="company-logo">
                <img src="{{ public_path('template/photo/company-logo.png') }}" alt="logo">
            </div>
            <div class="document-title">
                <h1>{{ strtoupper($deliveryNote->type) }}</h1>
                <p>Document Number: {{ $deliveryNote->document_code }}</p>
            </div>
            <div class="qr-code">
                <img src="{{ $qrCodeDataUri }}" alt="QR Code">
            </div>
        </div>
        <table class="doc-info">
            <tr>
                <td class="label">Document Date</td>
                <td>: {{ date('d-m-Y', strtotime($deliveryNote->document_date)) }}</td>
                <td class="label">Delivered Date</td>
                <td>: {{ date('d-m-Y', strtotime($deliveryNote->delivery_date)) }}</td>
            </tr>
            <tr>
                <td class="label">Driver Name</td>
                <td>: {{ $deliveryNote->driver }}</td>
                <td class="label">Driver Phone</td>
                <td>: {{ $deliveryNote->driver_phone }}</td>

            </tr>
            <tr>
                <td class="label">Transportation</td>
                <td>: {{ $deliveryNote->transportation ?? ' ' }} - {{ $deliveryNote->plate_number ?? ' ' }}</td>
                <td class="label">Type transaction</td>
                <td>: <b>{{ $deliveryNote->type }}</b></td>
            </tr>
        </table>

        <table class="address-section">
            <tr>
                <td width="48%">
                    <div class="address-box">
                        <h3>FROM (Source Location)</h3>
                        <strong>{{ $deliveryNote->sourceTransactions->name }} ({{ $deliveryNote->sourceTransactions->code }})</strong><br>
                        {{ $deliveryNote->sourceTransactions->address }}<br><br>
                        <strong>Contact Person:</strong> {{ $deliveryNote->sourceTransactions->ppic }} (PPIC)<br>
                        <strong>Phone:</strong> {{ $deliveryNote->sourceTransactions->phone }}<br>
                        <strong>Email:</strong> {{ $deliveryNote->sourceTransactions->email }}
                    </div>
                </td>
                <td width="4%"></td>
                <td width="48%">
                    <div class="address-box">
                        <h3>TO (Destination)</h3>
                        <strong>{{ $deliveryNote->destinationTransactions->name }} ({{ $deliveryNote->destinationTransactions->code }})</strong><br>
                        {{ $deliveryNote->destinationTransactions->address }}<br><br>
                        <strong>Contact Person:</strong> {{ $deliveryNote->destinationTransactions->ppic }}<br>
                        <strong>Phone:</strong> {{ $deliveryNote->destinationTransactions->phone }}<br>
                        <strong>Email:</strong> {{ $deliveryNote->destinationTransactions->email }}
                    </div>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="15%">Tool Code</th>
                    <th width="25%">Name</th>
                    <th width="15%">Brand</th>
                    <th width="15%">Serial Number</th>
                    <th width="7%">Qty</th>
                    <th width="8%">Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($deliveryNote->tools as $item)
                <tr>
                    <td class="center">{{ $loop->iteration }}</td>
                    <td>{{ $item->tool->code }}</td>
                    <td>{{ $item->tool->name }}</td>
                    <td>{{ $item->tool->brand }}</td>
                    <td>{{ $item->tool->serial_number }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="center">{{ $item->tool->unit }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="notes-section">
            <h3>Notes & Instructions:</h3>
            <p>{{ $deliveryNote->notes }}</p>
        </div>

        <hr>

        <div class="signatures">
            <div class="signature-grid">
                <div class="signature-box">
                    <strong>Delivered By: </strong>
                    <div class="signature-line">
                        Name: {{ $deliveryNote->driver }}<br>
                        Date: {{ date('d-m-Y', strtotime($deliveryNote->delivery_date)) }}<br>
                    </div>
                </div>
                <div class="signature-box">
                    <strong>Received By:</strong>
                    <div class="signature-line">
                        Name: _________________<br>
                        Date: __________________<br>
                    </div>
                </div>
                <div class="signature-box">
                    <strong>Approved By:</strong>
                    <div class="signature-line">
                        Name: _________________<br>
                        Date: __________________<br>
                    </div>
                </div>
                <div class="signature-box">
                    <strong>Approved By:</strong>
                    <div class="signature-line">
                        Name: _________________<br>
                        Date: __________________<br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        This delivery note is computer generated and is valid without signature. | Page 1 of 1 | Generated on: <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</body>
</html>