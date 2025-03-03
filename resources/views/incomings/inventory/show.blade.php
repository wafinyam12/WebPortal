@extends('layouts.admin', [
    'title' => 'Inventory Incoming Details'
])

@push('css')

@endpush

@section('main-content')
<h1 class="h3 mb-2 text-gray-800">Inventory Incoming Details</h1>
<p class="mb-4">
    This page is used to show inventory incoming.
</p>

<!-- Show Incoming Inventory -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Inventory Incoming Details</h4>
            <a href="{{ route('incomings-inventory.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="font-weight-bold">Document Information</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">PO Code</td>
                            <td>: {{ $incomings->code }}</td>
                        </tr>
                        <tr>
                            <td>Branch</td>
                            <td>: {{ $incomings->branch->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Supplier</td>
                            <td>: {{ $incomings->supplier->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>ETA</td>
                            <td>: {{ date('d F Y', strtotime($incomings->eta ?? '-' )) }}</td>
                        </tr>
                        
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless mt-4">
                        <tr>
                            <td>Warehouses / Drop Site</td>
                            <td>: {{ $incomings->drop->name ?? '-' }} / {{ $incomings->drop_site ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Notes</td>
                            <td>: {{ $incomings->notes ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>: {!! $incomings->statusName ?? '-' !!}</td>
                        </tr>
                        <tr>
                            <td>Attachment</td>
                            <td>: <a href="{{ asset('storage/incomings/file/' . $incomings->attachment) }}" target="_blank">{{ $incomings->attachment }}</a></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h5 class="font-weight-bold">Inventory List</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Name</th>
                                    <th width="20%">Quantity</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($incomings->item as $incoming)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $incoming->item_name }}</td>
                                        <td>{{ $incoming->quantity }}</td>
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
        </div>
    </div>
@endsection

@push('scripts')

@endpush

