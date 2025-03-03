@extends('layouts.admin', [
    'title' => 'Vehicles Show Maintenances'
])

@push('css')

@endpush

@section('main-content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Vehicles Show Maintenances</h1>
<p class="mb-4">
    This page is used to show vehicles maintenances.
</p>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Vehicles Maintenances Details</h4>
            <a href="{{ route('vehicles-maintenances.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
        </div>
        <div class="card-body">
            
        </div>
    </div>
</div>


@endsection

@push('scripts')

@endpush