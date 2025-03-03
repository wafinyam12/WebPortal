@extends('layouts.admin', [
    'title' => 'Warehouse Details'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h3 mb-2 text-gray-800">{{ __('Details Warehouse') }}</h1>
<p class="mb-4">
    this page is used to show details Warehouse
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="m-0 font-weight-bold text-primary">Warehouse Details</h4>
        <a href="{{ route('warehouses.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="font-weight-bold text-primary">Warehouse Information</h5>
                <div class="mb-2">
                    <strong>Code:</strong> <span class="text-muted">{{ $warehouses->code }}</span>
                </div>
                <div class="mb-2">
                    <strong>Name:</strong> <span class="text-muted">{{ $warehouses->name }}</span>
                </div>
                <div class="mb-2">
                    <strong>Phone:</strong> <span class="text-muted">{{ $warehouses->phone }}</span>
                </div>
                <div class="mb-2">
                    <strong>Email:</strong> <span class="text-muted">{{ $warehouses->email }}</span>
                </div>
                <div class="mb-2">
                    <strong>Address:</strong> <span class="text-muted">{{ $warehouses->address }}</span>
                </div>
                <div class="mb-2">
                    <strong>Description:</strong> <span class="text-muted">{{ $warehouses->description }}</span>
                </div>
                <div class="mb-2">
                    <strong>Status:</strong> {!! $warehouses->statusName !!}
                </div>
                <div class="mb-2">
                    <strong>Type:</strong> {!! $warehouses->typeName !!}
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="font-weight-bold text-primary">Company Information</h5>
                <div class="mb-2">
                    <strong>Code:</strong> <span class="text-muted">{{ $warehouses->company->code }}</span>
                </div>
                <div class="mb-2">
                    <strong>Name:</strong> <span class="text-muted">{{ $warehouses->company->name }}</span>
                </div>
                <div class="mb-2">
                    <strong>Email:</strong> <span class="text-muted">{{ $warehouses->company->email }}</span>
                </div>
                <div class="mb-2">
                    <strong>Phone:</strong> <span class="text-muted">{{ $warehouses->company->phone }}</span>
                </div>
                <div class="mb-2">
                    <strong>Address:</strong> <span class="text-muted">{{ $warehouses->company->address }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h5 class="font-weight-bold text-primary">Branch Information</h5>
                <div class="mb-2">
                    <strong>Code:</strong> <span class="text-muted">{{ $warehouses->branch->code }}</span>
                </div>
                <div class="mb-2">
                    <strong>Name:</strong> <span class="text-muted">{{ $warehouses->branch->name }}</span>
                </div>
                <div class="mb-2">
                    <strong>Email:</strong> <span class="text-muted">{{ $warehouses->branch->email }}</span>
                </div>
                <div class="mb-2">
                    <strong>Phone:</strong> <span class="text-muted">{{ $warehouses->branch->phone }}</span>
                </div>
                <div class="mb-2">
                    <strong>Address:</strong> <span class="text-muted">{{ $warehouses->branch->address }}</span>
                </div>
                <div class="mb-2">
                    <strong>Description:</strong> <span class="text-muted">{{ $warehouses->branch->description }}</span>
                </div>
                <div class="mb-2">
                    <strong>Type:</strong> <span class="text-muted">{{ $warehouses->branch->type }}</span>
                </div>
                <div class="mb-2">
                    <strong>Status:</strong> {!! $warehouses->branch->activeBranch !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush