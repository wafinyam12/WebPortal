@extends('layouts.admin', [
    'title' => 'Bids Analysis Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Bids Analysis Management</h1>
<p class="mb-4">
    This page is used to manage bids analysis.
</p>

<!-- List Bids Analysis -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">List Bids Analysis</h4>
            @can('create bids analysis')
            <a href="{{ route('bids-analysis.create') }}" class="btn btn-primary btn-md mr-2">
                <i class="fas fa-chart-bar"></i> 
                Add Analysis
            </a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th class="text-center" width="15%">Code</th>
                            <th>Branch</th>
                            <th>Projetc Name</th>
                            <th class="text-center" width="15%">Date</th>
                            <th class="text-center" width="10%">Status</th>
                            <th class="text-center" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bids as $bid)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $bid->code }}</td>
                            <td>{{ $bid->branch->name }}</td>
                            <td>{{ $bid->project_name }}</td>
                            <td class="text-center">{{ date('d-m-Y', strtotime($bid->bid_date)) }}</td>
                            <td class="text-center">{!! $bid->statusName !!}</td>
                            <td class="text-center">
                                <div class="d-inline-flex">
                                    @can('print bids analysis')
                                    <a href="{{ route('bids-analysis.exportPdf', $bid->id) }}" class="btn btn-success btn-circle mr-1">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @endcan
                                    @if($bid->status == 'Open')
                                    @can('approve bids analysis')
                                    <form action="{{ route('bids-analysis.approved', $bid->id) }}" method="post" id="approvedBidForm-{{ $bid->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-primary btn-circle mr-1" onclick="confirmApprovedBid({{ $bid->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endcan
                                    @can('rejected bids analysis')
                                    <form action="{{ route('bids-analysis.rejected', $bid->id) }}" method="post" id="rejectedBidForm-{{ $bid->id }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="reason" id="rejectedReason-{{ $bid->id }}">
                                        <button type="button" class="btn btn-danger btn-circle mr-1" onclick="confirmRejectedBid({{ $bid->id }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                    @endcan
                                    @endif
                                    @can('show bids analysis')
                                    <a href="{{ route('bids-analysis.show', $bid->id) }}" class="btn btn-info btn-circle mr-1">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    @if($bid->status == 'Open')
                                    @can('update bids analysis')
                                    <a href="{{ route('bids-analysis.edit', $bid->id) }}" class="btn btn-warning btn-circle mr-1">
                                        <i class="fas fa-pencil"></i>
                                    </a>
                                    @endcan
                                    @endif
                                    @can('delete bids analysis')
                                    <form action="{{ route('bids-analysis.destroy', $bid->id) }}" method="post" id="destroyBidForm-{{ $bid->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-circle" onclick="confirmDestroyBid({{ $bid->id }})">
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


@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Page level custom scripts  -->
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    function confirmApprovedBid(bidId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You wont be approved this bids!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approved it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('approvedBidForm-' + bidId).submit();
            }
        })
    }

    function confirmRejectedBid(bidId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You wont be rejected this bids!",
            input: 'text',
            inputPlaceholder: 'Enter reason here...',
            showCancelButton: true,
            confirmButtonText: 'Submit',
            confirmButtonColor: '#d33',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Reason is required!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Action if confirmed and send data
                $('#rejectedReason-' + bidId).val(result.value);
                document.getElementById('rejectedBidForm-' + bidId).submit();
            } else if (result.isDismissed) {
                // Action if dismissed
                Swal.fire('You cancelled the input.', '', 'info');
            }
        })
    }

    function confirmDestroyBid(bidId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You wont be delete this bids!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('destroyBidForm-' + bidId).submit();
            }
        })
    }
</script>
@endpush

@endsection