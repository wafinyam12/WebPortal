@extends('layouts.admin', [
    'title' => 'Company Details'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h3 mb-2 text-gray-800">{{ __('Details Company') }}</h1>
<p class="mb-4">
    this page is used to show details company
</p>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="m-0 font-weight-bold text-primary">Company Details</h4>
        <a href="{{ route('companies.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Code</th>
                    <td>{{ $companies->code }}</td>
                </tr>
                <tr>
                    <th>Name</th>
                    <td>{{ $companies->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $companies->email }}</td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>{{ $companies->phone }}</td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>{{ $companies->address }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $companies->description ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td>{{ $companies->created_at }}</td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td>{{ $companies->updated_at }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush