@extends('layouts.admin', [
    'title' => 'Update Bussines Master'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h3 mb-4 text-gray-800">Update Bussines Master</h1>
<p class="mb-4">
    This page is used to create bussines master.
</p>

<!-- Update Bussines Master -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">Update Bussines Master</h6>
        <a href="{{ route('bussines-master.index') }}" class="btn btn-primary btn-md mr-2">
            <i class="fas fa-reply"></i> 
            Back
        </a>
    </div>
    <div class="card-body">
        
    </div>
</div>

@endsection

@push('scripts')

@endpush