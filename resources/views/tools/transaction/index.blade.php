@extends('layouts.admin', [
    'title' => 'Delivery Note & Transfer'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ __('Delivery Note & Transfer') }}</h1>
    <p class="mb-4">
        This page is for use in Delivery Note and Transfer
    </p>

    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">Delivery Note & Transfer</h4>
                @can('create tools transactions')
                <a href="{{ route('transactions.create') }}" class="btn btn-primary btn-md">
                    <i class="fas fa-truck-moving"></i> 
                    Add Delivery Note
                </a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Delivery Note</th>
                                <th>Delivery Date</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th width="20%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $transaction->document_code }}</td>
                                    <td>{{ date('d M Y', strtotime($transaction->delivery_date)) }}</td>
                                    <td>{!! $transaction->typeName !!}</td>
                                    <td>{!! $transaction->statusName !!}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            @can('print tools transactions')
                                            <a href="{{ route('transactions.generateDN', $transaction->id) }}" class="btn btn-success btn-circle mr-2">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            @endcan
                                            @can('show tools transactions')
                                            <a href="{{ route('transactions.show', $transaction->id) }}" class="btn btn-info btn-circle mr-2">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('update tools transactions')
                                            @if($transaction->delivery_date > date('Y-m-d'))
                                            <a href="{{ route('transactions.edit', $transaction->id) }}" class="btn btn-warning btn-circle mr-2"><i class="fas fa-pencil"></i></a>
                                            @endif
                                            @endcan
                                            @can('delete tools transactions')
                                            <form action="{{ route('transactions.destroy', $transaction->id) }}" method="post" id="deleteDeliveryNoteForm-{{ $transaction->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteDeliveryNote({{ $transaction->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    function confirmDeleteDeliveryNote(deliveryNoteId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this delivery note!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteDeliveryNoteForm-' + deliveryNoteId).submit();
            }
        });
    }
</script>
@endpush