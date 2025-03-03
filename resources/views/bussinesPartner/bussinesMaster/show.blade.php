@extends('layouts.admin', [
    'title' => 'Show Bussines Master'
])

@push('css')

@endpush

@section('main-content')

<h1 class="h3 mb-4 text-gray-800">Show Bussines Master</h1>
<p class="mb-4">
    This page is used to show bussines master.
</p>

<!-- Show Bussines Master -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">Show Bussines Master</h6>
        <a href="{{ route('bussines-master.index') }}" class="btn btn-primary btn-md mr-2">
            <i class="fas fa-reply"></i> 
            Back
        </a>
    </div>
    <div class="card-body">
        <strong>Card Code:</strong> {{ $bussinesMasters['CardCode'] ?? '-' }}
        <br>
        <strong>Card Name:</strong> {{ $bussinesMasters['CardName'] ?? '-' }}
        <br>
        <strong>Card Type:</strong> {{ $bussinesMasters['CardType'] ?? '-' }}
        <br>
        <strong>Credit Limit:</strong> {{ number_format($bussinesMasters['CreditLimit'] ?? 0, 2) }}
        <br>
    
        <strong>Addresses:</strong>  
        <ul>
            @foreach($bussinesMasters['BPAddresses'] ?? [] as $bpAddress)
                <li>
                    <strong>{{ $bpAddress['AddressName'] ?? '-' }}</strong><br>
                    Street: {{ $bpAddress['Street'] ?? '-' }}<br>
                    City: {{ $bpAddress['City'] ?? '-' }}<br>
                    Zip Code: {{ $bpAddress['ZipCode'] ?? '-' }}<br>
                </li>
            @endforeach
        </ul>
    </div>
</div>

@endsection

@push('scripts')

@endpush