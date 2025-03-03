@extends('layouts.admin', [
    'title' => 'Vehicles Show Reimbursement'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> 
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
@endpush

@section('main-content')

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Vehicles Show Reimbursement</h1>
<p class="mb-4">
    This page is used to show vehicles reimbursements.
</p>

<!-- Card Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="m-0 font-weight-bold text-primary">Vehicles Reimbursement Details</h4>
        @if(Auth::user()->hasRole('Superadmin'))
            <a href="{{ route('reimbursements.index') }}" class="btn btn-primary btn-md mr-2">
                <i class="fas fa-reply"></i> 
                Back
            </a>
        @else
        <a href="{{ route('reimbursements.create') }}" class="btn btn-primary btn-md mr-2">
            <i class="fas fa-reply"></i> 
            Back
        </a>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <!-- Vehicle Photo -->
            <div class="col-md-4">
                <div class="mt-4 mb-3">
                    <img 
                        src="{{ asset('storage/vehicle/Photo/' . ($reimbursement->vehicle->photo ?? 'default.jpg')) }}" 
                        class="img-fluid rounded shadow-sm border border-primary w-100" 
                        alt="Vehicle Photo">
                </div>
            </div>
        
            <!-- Vehicle Details -->
            <div class="col-md-8">
                <!-- Approval Status -->
                <div class="text-left mb-4">
                    @if($reimbursement->approved_by)
                        <h3 class="font-weight-bold text-success">
                            Approved By: {{ optional($reimbursement->approvedBy)->fullName ?? '-' }} / 
                            {{ $reimbursement->approved_at ? date('d F Y', strtotime($reimbursement->approved_at)) : '-' }}
                        </h3>
                    @elseif($reimbursement->rejected_by)
                        <h3 class="font-weight-bold text-danger">
                            Rejected By: {{ optional($reimbursement->rejectedBy)->fullName ?? '-' }} / 
                            {{ $reimbursement->rejected_at ? date('d F Y', strtotime($reimbursement->rejected_at)) : '-' }}
                        </h3>
                    @else
                        <h3 class="font-weight-bold text-warning">{!! $reimbursement->statusName !!}</h3>
                    @endif
                </div>
        
                <!-- Vehicle Information -->
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        @foreach ([
                            'Type' => $reimbursement->typeName ?? '-',
                            'Status' => $reimbursement->statusName ?? '-',
                            'Fuel' => $reimbursement->fuel ?? '-',
                            'Date' => date('d F Y', strtotime($reimbursement->date_recorded ?? now()))
                        ] as $label => $value)
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="font-weight-bold text-muted">{{ $label }}</span>
                                <span>{!! $value !!}</span>
                            </div>
                        @endforeach
                    </div>
        
                    <!-- Right Column -->
                    <div class="col-md-6">
                        @foreach ([
                            'First Mileage' => ($reimbursement->first_mileage ?? '-') . ' Mileage',
                            'Last Mileage' => ($reimbursement->last_mileage ?? '-') . ' Mileage',
                            'Amount' => ($reimbursement->amount ?? '-') . ' Liter',
                            'Price' => 'Rp ' . number_format($reimbursement->price ?? 0, 2)
                        ] as $label => $value)
                            <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                <span class="font-weight-bold text-muted">{{ $label }}</span>
                                <span class="{{ $label === 'Price' ? 'font-weight-bold text-primary' : '' }}">
                                    {{ $value }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
        
                <!-- Notes and Reason -->
                <h5>Notes: {{ $reimbursement->notes ?? '-' }}</h5>
                <h5 class="text-danger mb-3">Reason: {{ $reimbursement->reason ?? '-' }}</h5>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="text-center mb-4">
                    <h3 class="font-weight-bold text-primary">Reimbursement Details</h3>
                </div>
                <div class="row">
                    @if(!empty($reimbursement->attachment_mileage))
                    <div class="col-md-6 text-center mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Mileage Attachment</h5>
                            </div>
                            <div class="card-body">
                                @if(!empty($reimbursement->attachment_mileage))
                                    <img 
                                        src="{{ asset('storage/vehicle/reimbursements/mileage/'.$reimbursement->attachment_mileage) }}" 
                                        alt="Mileage Attachment" 
                                        class="img-fluid rounded shadow"
                                        style="max-height: 300px; object-fit: cover;"
                                    >
                                @else
                                    <p class="text-muted">No attachment available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-center mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Receipt Attachment</h5>
                            </div>
                            <div class="card-body">
                                @if(!empty($reimbursement->attachment_receipt))
                                    <img 
                                        src="{{ asset('storage/vehicle/reimbursements/receipt/'.$reimbursement->attachment_receipt) }}" 
                                        alt="Receipt Attachment" 
                                        class="img-fluid rounded shadow"
                                        style="max-height: 300px; object-fit: cover;"
                                    >
                                @else
                                    <p class="text-muted">No attachment available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-12 text-center mb-4 justify-content-center">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Receipt Attachment</h5>
                            </div>
                            <div class="card-body">
                                @if(!empty($reimbursement->attachment_receipt))
                                    <img 
                                        src="{{ asset('storage/vehicle/reimbursements/receipt/'.$reimbursement->attachment_receipt) }}" 
                                        alt="Receipt Attachment" 
                                        class="img-fluid rounded shadow"
                                        style="max-height: 300px; object-fit: cover;"
                                    >
                                @else
                                    <p class="text-muted">No attachment available</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<!-- Page level custom scripts  -->
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endpush