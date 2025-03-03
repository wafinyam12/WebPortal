@extends('layouts.admin', [
    'title' => 'Bussines Master Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush


@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Bussines Master Management</h1>
<p class="mb-4">
    This page is used to manage bussines master.
</p>

<!-- List Bussines Master -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h6 class="m-0 font-weight-bold text-primary">List Bussines Master</h6>
        @can('create bussines master')
        <a href="{{ route('bussines-master.create') }}" class="btn btn-primary btn-md mr-2">
            <i class="fas fa-user-tie"></i> 
            Add Bussines Master
        </a>
        @endcan
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr class="text-center">
                        <th>No</th>
                        <th>Card Code</th>
                        <th>Card Name</th>
                        <th>Card Type</th>
                        <th>Card Credit Limit</th>
                        <th width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bussinesMasters as $bussinesMaster)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $bussinesMaster['CardCode'] }}</td>
                            <td>{{ $bussinesMaster['CardName'] }}</td>
                            <td>{{ $bussinesMaster['CardType'] }}</td>
                            <td>Rp {{ number_format($bussinesMaster['CreditLimit']) }}</td>
                            <td>
                                <div class="d-flex-inline">
                                    <a href="{{ route('bussines-master.show', $bussinesMaster['CardCode']) }}" class="btn btn-info btn-circle mr-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('bussines-master.edit', $bussinesMaster['CardCode']) }}" class="btn btn-warning btn-circle mr-1">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-circle">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#dataTable').DataTable();
    });
</script>
@endpush