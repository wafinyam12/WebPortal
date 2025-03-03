@extends('layouts.admin', [
    'title' => 'Department Details'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h3 mb-2 text-gray-800">{{ __('Details Department') }}</h1>
<p class="mb-4">
    this page is used to show details department
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="m-0 font-weight-bold text-primary">Department Details</h4>
        <a href="{{ route('departments.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="font-weight-bold text-primary">Department Information</h5>
                <div class="mb-2">
                    <strong>Code:</strong> <span class="text-muted">{{ $departments->code }}</span>
                </div>
                <div class="mb-2">
                    <strong>Name:</strong> <span class="text-muted">{{ $departments->name }}</span>
                </div>
                <div class="mb-2">
                    <strong>Description:</strong> <span class="text-muted">{{ $departments->description }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <h5 class="font-weight-bold text-primary">Company Information</h5>
                <div class="mb-2">
                    <strong>Code:</strong> <span class="text-muted">{{ $departments->company->code }}</span>
                </div>
                <div class="mb-2">
                    <strong>Name:</strong> <span class="text-muted">{{ $departments->company->name }}</span>
                </div>
                <div class="mb-2">
                    <strong>Email:</strong> <span class="text-muted">{{ $departments->company->email }}</span>
                </div>
                <div class="mb-2">
                    <strong>Phone:</strong> <span class="text-muted">{{ $departments->company->phone }}</span>
                </div>
                <div class="mb-2">
                    <strong>Address:</strong> <span class="text-muted">{{ $departments->company->address }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush