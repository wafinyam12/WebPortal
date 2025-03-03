@extends('layouts.admin', [
    'title' => 'Branch Details'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h3 mb-2 text-gray-800">{{ __('Details Branch') }}</h1>
<p class="mb-4">
    this page is used to show details branch
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="m-0 font-weight-bold text-primary">Branch Details</h4>
        <a href="{{ route('branches.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">
                <img src="{{ asset('storage/branch/photo/' . $branches->photo) }}" alt="{{ $branches->name }}" class="img-thumbnail mb-3" style="max-width: 200px;">
            </div>
            <div class="col-md-8">
                <h5><strong>{{ $branches->name }}</strong></h5>
                <p>{{ $branches->address }}</p>
                <ul class="list-unstyled">
                    <li><strong>Code:</strong> {{ $branches->code }}</li>
                    <li><strong>Email:</strong> <a href="mailto:{{ $branches->email }}">{{ $branches->email }}</a></li>
                    <li><strong>Phone:</strong> <a href="tel:{{ $branches->phone }}">{{ $branches->phone }}</a></li>
                    <li><strong>Type:</strong> {{ $branches->type }}</li>
                    <li><strong>Status:</strong> {!! $branches->activeBranch !!}</li>
                </ul>
                <p><strong>Description:</strong> {{ $branches->description }}</p>
            </div>
        </div>
        <hr>
        <h5 class="mt-4">Parent Company</h5>
        <p><strong>{{ $branches->company->name }}</strong></p>
        <ul class="list-unstyled">
            <li><strong>Code:</strong> {{ $branches->company->code }}</li>
            <li><strong>Email:</strong> <a href="mailto:{{ $branches->company->email }}">{{ $branches->company->email }}</a></li>
            <li><strong>Phone:</strong> <a href="tel:{{ $branches->company->phone }}">{{ $branches->company->phone }}</a></li>
            <li><strong>Address:</strong> {{ $branches->company->address }}</li>
        </ul>
    </div>
</div>

@endsection

@push('scripts')

@endpush