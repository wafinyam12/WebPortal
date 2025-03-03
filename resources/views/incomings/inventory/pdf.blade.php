<!DOCTYPE html>
<html>
<head>
    <title>Incoming Inventory - {{ $incomings->code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
        .qr-code img {
            max-height: 80px;
            max-width: 80px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .table-borderless td {
            border: none;
            padding: 5px;
        }
        .table-info {
            width: 100%;
            display: table;
        }
        .table-info-column {
            width: 48%;
            display: table-cell;
        }
        .table-bordered {
            border: 1px solid #ddd;
        }
        .table-bordered th {
            background-color: #ecebe1;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
            padding: 5px;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .bg-light {
            background-color: #f8f9fa;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('template/photo/company-logo.png') }}" alt="logo">
        </div>
        <div class="document-title">
            <h1>INCOMING INVENTORY</h1>
            <p>Document Number: {{ $incomings->code }}</p>
        </div>
        {{-- <div class="qr-code">
            {!! QrCode::size(80)->generate(route('costbid.detail', $costbid->id)) !!}
        </div> --}}
        <div class="company-logo text-right">
            <img src="{{ public_path('template/photo/company-logo.png') }}" alt="logo">
        </div>
    </div>
    <div class="card-body">
        <div class="table-info">
            <div class="table-info-column">
                <table class="table table-borderless">
                    <tr>
                        <td>Code</td>
                        <td>: {{ $incomings->code }}</td>
                    </tr>
                    <tr>
                        <td>Branch</td>
                        <td>: {{ $incomings->branch->name }}</td>
                    </tr>
                    <tr>
                        <td>Supplier</td>
                        <td>: {{ $incomings->supplier->name }}</td>
                    </tr>
                    <tr>
                        <td>ETA</td>
                        <td>: {{ date('d F Y', strtotime($incomings->eta)) }}</td>
                    </tr>
                </table>
            </div>
            <div class="table-info-column">
                <table class="table table-borderless">
                    <tr>
                        <td>Warehouses / Drop Site</td>
                        <td>: {{ $incomings->drop->name ?? '-' }} / {{ $incomings->drop_site ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>: {{ $incomings->status }}</td>
                    </tr>
                    <tr>
                        <td>Notes</td>
                        <td>: {{ $incomings->notes }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center">Item Name</th>
                            <th class="text-center" width="20%">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($incomings->item as $incoming)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $incoming->item_name }}</td>
                                <td class="text-center">{{ $incoming->quantity }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No Data</td>
                            </tr>
                        @endforelse
                    </tbody>               
                </table>
            </div>
        </div>
    </div>

    <div class="footer">
        This delivery note is computer generated and is valid without signature. | Page 1 of 1 | Generated on: <?php echo date('d/m/Y H:i:s'); ?>
    </div>
</body>
</html>