@extends('layouts.admin', [
    'title' => 'Employee Details'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h3 mb-2 text-gray-800">{{ __('Details Employee') }}</h1>
<p class="mb-4">
    this page is used to show details employee
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="m-0 font-weight-bold text-primary">Employee Details</h4>
        <a href="{{ route('employees.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="card-body">
            <div class="row mb-4">
                <!-- Employee Information -->
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-primary">Employee Information</h5>
                    @if($employees->photo)
                        <p><strong>Photo: </strong> <img src="{{ asset('storage/employees/photo/' . $employees->photo) }}" alt="{{ $employees->full_name }}" class="img-thumbnail" width="100"></p>
                    @endif
                    <p><strong>Code:</strong> <span class="text-muted">{{ $employees->code }}</span></p>
                    <p><strong>NIK:</strong> <span class="text-muted">{{ $employees->nik }}</span></p>
                    <p><strong>Full Name:</strong> <span class="text-muted">{{ $employees->full_name }}</span></p>
                    <p><strong>Gender:</strong> <span class="text-muted">{{ $employees->gender }}</span></p>
                    <p><strong>Age:</strong> <span class="text-muted">{{ $employees->age }}</span></p>
                    <p><strong>Phone:</strong> <span class="text-muted">{{ $employees->phone }}</span></p>
                    <p><strong>Position:</strong> <span class="text-muted">{{ $employees->position }}</span></p>
                    <p><strong>Address:</strong> <span class="text-muted">{{ $employees->address }}</span></p>
                    <p><strong>Status:</strong> {!! $employees->activeUsers !!}</p>
                </div>
    
                <!-- User Information -->
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-primary">User Information</h5>
                    <p><strong>Username:</strong> <span class="text-muted">{{ $employees->user->username }}</span></p>
                    <p><strong>Full Name:</strong> <span class="text-muted">{{ $employees->user->fullName }}</span></p>
                    <p><strong>Email:</strong> <span class="text-muted">{{ $employees->user->email }}</span></p>
                    <p><strong>Role:</strong> <span class="badge badge-{{ $employees->user->badgeClass }}">{{ $employees->user->roles[0]->name }}</span></p>
                </div>
            </div>
    
            <div class="row">
                <!-- Company Information -->
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-primary">Company Information</h5>
                    <p><strong>Code:</strong> <span class="text-muted">{{ $employees->company->code }}</span></p>
                    <p><strong>Name:</strong> <span class="text-muted">{{ $employees->company->name }}</span></p>
                    <p><strong>Email:</strong> <span class="text-muted">{{ $employees->company->email }}</span></p>
                    <p><strong>Phone:</strong> <span class="text-muted">{{ $employees->company->phone }}</span></p>
                    <p><strong>Address:</strong> <span class="text-muted">{{ $employees->company->address }}</span></p>
                </div>
    
                <!-- Branch Information -->
                <div class="col-md-6">
                    <h5 class="font-weight-bold text-primary">Branch Information</h5>
                    <p><strong>Code:</strong> <span class="text-muted">{{ $employees->branch->code }}</span></p>
                    <p><strong>Name:</strong> <span class="text-muted">{{ $employees->branch->name }}</span></p>
                    <p><strong>Email:</strong> <span class="text-muted">{{ $employees->branch->email }}</span></p>
                    <p><strong>Phone:</strong> <span class="text-muted">{{ $employees->branch->phone }}</span></p>
                    <p><strong>Address:</strong> <span class="text-muted">{{ $employees->branch->address }}</span></p>
                    <p><strong>Type:</strong> <span class="text-muted">{{ $employees->branch->type }}</span></p>
                    <p><strong>Status:</strong> {!! $employees->branch->activeBranch !!}</p>
                </div>
            </div>

            <div class="row">
                <!-- Department Information -->
                <div class="col-md-12">
                    <h5 class="font-weight-bold text-primary">Department Information</h5>
                    <p><strong>Code:</strong> <span class="text-muted">{{ $employees->department->code }}</span></p>
                    <p><strong>Name:</strong> <span class="text-muted">{{ $employees->department->name }}</span></p>
                    <p><strong>Description:</strong> <span class="text-muted">{{ $employees->department->description }}</span></p>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush