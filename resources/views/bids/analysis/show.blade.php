@extends('layouts.admin', [
    'title' => 'Bids Analysis Details'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')

<h1 class="h3 mb-4 text-gray-800">Bids Analysis Details</h1>
<p class="mb-4">
    This page is used to show bids analysis.
</p>

<!-- View Bids Analysis -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Bids Analysis Details</h4>
            <a href="{{ route('bids-analysis.index') }}" class="btn btn-primary btn-md mr-2">
                <i class="fas fa-reply"></i>
                Back
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Document Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td>Code</td>
                            <td>: {{ $costbid->code }}</td>
                        </tr>
                        <tr>
                            <td>Document Date</td>
                            <td>: {{ $costbid->document_date }}</td>
                        </tr>
                        <tr>
                            <td>Bid Date</td>
                            <td>: {{ $costbid->bid_date }}</td>
                        </tr>
                        <tr>
                            <td>Project Name</td>
                            <td>: {{ $costbid->project_name }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mt-4">
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
                            <td>: {{ $costbid->reason ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered" id="bidsTable">
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
                            <!-- Total -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right">TOTAL</td>
                                @foreach ($costbid->vendors as $vendor)
                                    <td colspan="2" class="text-right">Rp {{ number_format($vendor->grand_total, 2) }}</td>
                                @endforeach
                            </tr>
                            <!-- Discount -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right">DISCOUNT (%)</td>
                                @foreach ($costbid->vendors as $vendor)
                                    <td colspan="2" class="text-right">{{ $vendor->discount }}%</td>
                                @endforeach
                            </tr>
                            <!-- Total After Discount -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right">TOTAL After Discount</td>
                                @foreach ($costbid->vendors as $vendor)
                                    <td colspan="2" class="text-right">Rp {{ number_format($vendor->final_total, 2) }}</td>
                                @endforeach
                            </tr>
                            <!-- Terms of Payment -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right">Terms of Payment (TOP)</td>
                                @foreach ($costbid->vendors as $vendor)
                                    <td colspan="2" class="text-right">{{ $vendor->terms_of_payment }}</td>
                                @endforeach
                            </tr>
                            <!-- Lead Time -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right">Lead Time (Days)</td>
                                @foreach ($costbid->vendors as $vendor)
                                    <td colspan="2" class="text-right">{{ $vendor->lead_time }}</td>
                                @endforeach
                            </tr>

                            <!-- Note -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right">Note Vendors</td>
                                @foreach ($costbid->vendors as $vendor)
                                    <td colspan="2" class="text-right">{{ $vendor->notes }}</td>
                                @endforeach
                            </tr>

                            <!-- Selected Vendor -->
                            <tr class="bg-light font-weight-bold">
                                <td colspan="4" class="text-right">Vendor Selected</td>
                                <td colspan="4" class="text-middle text-center font-weight-bold text-uppercase text-primary">{{ $costbid->selected_vendor }}</td>
                            </tr>
                        </tfoot>                
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <h5 class="font-weight-bold">Note</h5>
                    <p>{{ $costbid->notes }}</p>

                    <h5 class="font-weight-bold">Attachment</h5>
                    <a href="{{ asset('storage/bids/attachment/' . $costbid->attachment) }}" target="_blank">{{ $costbid->attachment }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Page level custom scripts  -->
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endpush