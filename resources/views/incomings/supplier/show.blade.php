@extends('layouts.admin', [
    'title' => 'Incoming Supplier Details'
])

@push('css')

@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Incoming Supplier Details</h1>
<p class="mb-4">
    This page is used to show incoming supplier.
</p>

<!-- View Incoming Supplier -->
<div class="card">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Incoming Supplier Details</h4>
            <a href="{{ route('incomings-supplier.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
        </div>
        <div class="card-body">
            
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush