@extends('layouts.admin', [
    'title' => 'Show Delivery Note'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h-3 mb-0 text-gray-800">{{ __('Show Delivery Note') }}</h1>
<p class="mb-4">
    This page is used to show delivery note.
</p>

<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">Delivery Note Details</h4>
            <a href="{{ route('transactions.index') }}" class="btn btn-primary btn-md">
                <i class="fas fa-reply"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Document Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Document Code</td>
                            <td>: {{ $deliveryNote->document_code }}</td>
                        </tr>
                        <tr>
                            <td>Document Date</td>
                            <td>: {{ date('d F Y', strtotime($deliveryNote->document_date)) }}</td>
                        </tr>
                        <tr>
                            <td>Delivery Date</td>
                            <td>: {{ date('d F Y', strtotime($deliveryNote->delivery_date)) }}</td>
                        </tr>
                        <tr>
                            <td>Type</td>
                            <td>: {{ $deliveryNote->type }}</td>
                        </tr>
                        <tr>
                            <td>Notes</td>
                            <td>: {{ $deliveryNote->notes ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Driver Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Driver Name</td>
                            <td>: {{ $deliveryNote->driver }}</td>
                        </tr>
                        <tr>
                            <td>Phone Number</td>
                            <td>: {{ $deliveryNote->driver_phone }}</td>
                        </tr>
                        <tr>
                            <td>Transport</td>
                            <td>: {{ $deliveryNote->transportation ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Plat Number </td>
                            <td>: {{ $deliveryNote->plate_number ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
    
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Source Project</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Project Code</td>
                            <td>: {{ $deliveryNote->sourceTransactions->code }}</td>
                        </tr>
                        <tr>
                            <td>Project Name</td>
                            <td>: {{ $deliveryNote->sourceTransactions->name }}</td>
                        </tr>
                        <tr>
                            <td>PPIC</td>
                            <td>: {{ $deliveryNote->sourceTransactions->ppic }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>: {{ $deliveryNote->sourceTransactions->address }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Destination Project</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Project Code</td>
                            <td>: {{ $deliveryNote->destinationTransactions->code }}</td>
                        </tr>
                        <tr>
                            <td>Project Name</td>
                            <td>: {{ $deliveryNote->destinationTransactions->name }}</td>
                        </tr>
                        <tr>
                            <td>PPIC</td>
                            <td>: {{ $deliveryNote->destinationTransactions->ppic }}</td>
                        </tr>
                        <tr>
                            <td>Address</td>
                            <td>: {{ $deliveryNote->destinationTransactions->address }}</td>
                        </tr>
                    </table>
                </div>
            </div>
    
            <div class="row">
                <div class="col-12">
                    <h5 class="font-weight-bold">Tools List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Tool Code</th>
                                    <th>Serial Number</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deliveryNote->tools as $note)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $note->tool->code }}</td>
                                   <td>{{ $note->tool->serial_number }}</td>
                                   <td>{{ $note->tool->name }}</td>
                                   <td>{{ $note->tool->brand }}</td>
                                   <td>{{ $note->tool->type }}</td>
                                   <td>{{ $note->quantity }}</td>
                                   <td>{{ $note->tool->unit }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>    
    </div>
</div>

@endsection

@push('script')
    
@endpush