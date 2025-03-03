@extends('layouts.admin', [
    'title' => 'Incoming Inventory Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Incoming Inventory Management</h1>
<p class="mb-4">
    This page is used to manage incoming inventory.
</p>

<!-- List Incoming Inventory -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">List Incoming Inventory</h4>
            @can('create incoming plan')
            <a href="{{ route('incomings-inventory.create') }}" class="btn btn-primary btn-md mr-2">
                <i class="fas fa-tent-arrows-down"></i> 
                Add Inventory
            </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Incoming Goods Plane</th>
                            <th>Branch</th>
                            <th>Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($incomings as $incoming)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $incoming->code }}</td>
                            <td>{{ $incoming->branch->name }}</td>
                            <td>{{ date('d F Y', strtotime($incoming->eta)) }}</td>
                            <td class="text-center">{!! $incoming->statusName !!}</td>
                            <td class="text-center">
                                <div class="d-inline-flex">
                                    @can('print incoming plan')
                                    <a href="{{ route('incomings-inventory.exportPdf', $incoming->id) }}" class="btn btn-success btn-circle mr-1">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @endcan
                                    @can('show incoming plan')
                                    <a href="{{ route('incomings-inventory.show', $incoming->id) }}" class="btn btn-info btn-circle mr-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @if($incoming->eta > date('Y-m-d'))
                                    @can('update incoming plan')
                                    <a href="{{ route('incomings-inventory.edit', $incoming->id) }}" class="btn btn-warning btn-circle mr-1">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                    @endcan
                                    @endif
                                    @can('delete incoming plan')
                                    <form action="{{ route('incomings-inventory.destroy', $incoming->id) }}" method="post" id="destroyIncomingForm-{{ $incoming->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-circle" onclick="confirDestroyIncoming({{ $incoming->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No data available</td>
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
        $('#dataTable').DataTable({});
    });

    function confirDestroyIncoming(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('destroyIncomingForm-' + id).submit();
            }
        })
    }
</script>
@endpush