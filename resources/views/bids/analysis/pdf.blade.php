<!DOCTYPE html>
<html>
<head>
    <title>Cost Bid Details - {{ $costbid->code }}</title>
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
    </style>
</head>
<body>
    <div class="header">
        <div class="company-logo">
            <img src="{{ public_path('template/photo/company-logo.png') }}" alt="logo">
        </div>
        <div class="document-title">
            <h1>COST BID DOCUMENT</h1>
            <p>Document Number: {{ $costbid->code }}</p>
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
                        <td>: {{ $costbid->code }}</td>
                    </tr>
                    <tr>
                        <td>Document Date</td>
                        <td>: {{ Carbon\Carbon::parse($costbid->document_date)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Bid Date</td>
                        <td>: {{ Carbon\Carbon::parse($costbid->bid_date)->translatedFormat('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Project Name</td>
                        <td>: {{ $costbid->project_name }}</td>
                    </tr>
                </table>
            </div>
            <div class="table-info-column">
                <table class="table table-borderless">
                    <tr>
                        <td>Created By</td>
                        <td>: {{ $costbid->createdBy->fullName }}</td>
                    </tr>
                    <tr>
                        @if ($costbid->status != 'Open')
                            <td> {!! $costbid->statusName ?? '-' !!} By</td>
                            <td>: {{ $costbid->approvedBy->fullName ?? '-' }} / {{ $costbid->rejectedBy->fullName ?? '-' }}</td>
                        @else
                            <td> By</td>
                            <td>: N/A</td>
                        @endif
                    </tr>
                    <tr>
                        <td>Approved At / Rejected At</td>
                        <td>: {{ $costbid->approved_at ?? 'N/A' }} / {{ $costbid->rejected_at ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td>Reason</td>
                        <td>: {{ $costbid->reason }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" class="align-middle">No</th>
                            <th rowspan="2" class="align-middle">Description</th>
                            <th rowspan="2" class="align-middle">QTY</th>
                            <th rowspan="2" class="align-middle">UOM</th>
                            @foreach ($costbid->vendors as $vendor)
                                <th colspan="2" class="text-center">{{ $vendor->name }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach ($costbid->vendors as $vendor)
                                <th class="text-center">Price/Unit</th>
                                <th class="text-center">Total</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($costbid->items as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->uom }}</td>
                                @foreach ($costbid->vendors as $vendor)
                                    @php
                                        $analysis = $item->costBidsAnalysis->firstWhere('cost_bids_vendor_id', $vendor->id);
                                        $price = $analysis ? $analysis->price : '-';
                                        $total = $analysis ? $analysis->price * $item->quantity : '-';
                                    @endphp
                                    <td class="text-center">Rp {{ number_format($price, 2) }}</td>
                                    <td class="text-center">Rp {{ number_format($total, 2) }}</td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + (count($costbid->vendors) * 2) }}" class="text-center">No Items Found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">TOTAL</td>
                            @foreach ($costbid->vendors as $vendor)
                                <td colspan="2" class="text-right">Rp {{ number_format($vendor->grand_total, 2) }}</td>
                            @endforeach
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">DISCOUNT (%)</td>
                            @foreach ($costbid->vendors as $vendor)
                                <td colspan="2" class="text-right">{{ $vendor->discount }}%</td>
                            @endforeach
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">TOTAL After Discount</td>
                            @foreach ($costbid->vendors as $vendor)
                                <td colspan="2" class="text-right">Rp {{ number_format($vendor->final_total, 2) }}</td>
                            @endforeach
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">Terms of Payment (TOP)</td>
                            @foreach ($costbid->vendors as $vendor)
                                <td colspan="2" class="text-right">{{ $vendor->terms_of_payment }}</td>
                            @endforeach
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">Lead Time (Days)</td>
                            @foreach ($costbid->vendors as $vendor)
                                <td colspan="2" class="text-right">{{ $vendor->lead_time }}</td>
                            @endforeach
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">Note Vendors</td>
                            @foreach ($costbid->vendors as $vendor)
                                <td colspan="2" class="text-right">{{ $vendor->notes }}</td>
                            @endforeach
                        </tr>
                        <tr class="bg-light font-weight-bold">
                            <td colspan="4" class="text-right">Vendor Selected</td>
                            <td colspan="{{ (count($costbid->vendors) * 2) }}" class="text-middle text-center font-weight-bold text-uppercase text-primary">{{ $costbid->selected_vendor }}</td>
                        </tr>
                    </tfoot>                
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h5 class="font-weight-bold">Note</h5>
                <p>{{ $costbid->notes }}</p>
            </div>
        </div>
    </div>
</body>
</html>