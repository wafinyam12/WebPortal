@extends('layouts.admin', [
    'title' => 'Vehicles Reimbursements'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Vehicles Reimbursements Management</h1>
<p class="mb-4">
    This page is used to manage vehicles reimbursements.
</p>

<!-- List Vehicles reimbursements -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">List Vehicles Reimbursements</h4>
            <div class="d-flex align-items-center flex-wrap">
                @can('export vehicle reimbursements')
                <select name="user_name" class="form-control mr-2 mb-2 w-auto" id="userName">
                    <option value="" disabled selected>--Select Users--</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                    @endforeach
                </select>
                <input type="date" id="startDate" name="start_date" class="form-control mr-2 mb-2 w-auto" required>
                <span class="mx-2">to</span>
                <input type="date" id="endDate" name="end_date" class="form-control mx-2 mb-2 w-auto" required>
                <!-- Tombol dropdown Print Filter -->
                <button type="button" class="btn btn-secondary btn-md ml-2 mb-2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export fa-md white-50"></i> Export Filter
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="#" id="exportAll">Export All</a>
                    <a class="dropdown-item" href="#" id="exportPending">Export Pending</a>
                    <a class="dropdown-item" href="#" id="exportApproved">Export Approved</a>
                    <a class="dropdown-item" href="#" id="printFiltered">Export Filtered</a>
                </div>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Vehicle</th>
                            <th>Employees</th>
                            <th>Date Recorded</th>
                            <th>Type</th>
                            <th>Fuel</th>
                            <th>Amount</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-center" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reimbursements as $reimbursement)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $reimbursement->vehicle->code }} - {{ $reimbursement->vehicle->model }}</td>
                                <td>{{ $reimbursement->vehicle->assigned->last()->employe->full_name ?? '-' }}</td>
                                <td>{{ date('d M Y', strtotime($reimbursement->date_recorded)) }}</td>
                                <td>{!! $reimbursement->typeName !!}</td>
                                <td>{{ $reimbursement->fuel ?? '-' }}</td>
                                <td>{{ $reimbursement->amount ?? '- ' }} Liter</td>
                                <td>Rp {{ number_format($reimbursement->price, 2) }}</td>
                                <td>{!! $reimbursement->statusName !!}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex">
                                        @if ($reimbursement->status == 'Pending')
                                            @can('approved vehicle reimbursements')
                                            <form action="{{ route('reimbursements.approved', $reimbursement->id) }}" method="post" id="approveReimbursementForm-{{ $reimbursement->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="button" class="btn btn-success btn-circle mr-1" onclick="confirmApprovedReimbursement({{ $reimbursement->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endcan
                                            @can('rejected vehicle reimbursements')
                                            <form action="{{ route('reimbursements.rejected', $reimbursement->id) }}" method="post" id="rejectReimbursementForm-{{ $reimbursement->id }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="reason" id="rejectReason-{{ $reimbursement->id }}">
                                                <button type="button" class="btn btn-danger btn-circle mr-1" onclick="confirmRejectReimbursement({{ $reimbursement->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        @endif
                                        @can('show vehicle reimbursements')
                                        <a href="{{ route('reimbursements.show', $reimbursement->id) }}" class="btn btn-info btn-circle mr-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('delete vehicle reimbursements')
                                        <form action="{{ route('reimbursements.destroy', $reimbursement->id) }}" method="post" id="destroyReimbursementForm-{{ $reimbursement->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteReimbursement({{ $reimbursement->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>       
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No data available</td>
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

    $(document).ready(function() {

        // Export Filtered
        $('#printFiltered').on('click', function(e) {
            e.preventDefault();
            handleExport('filtered');
        });

        // Export All
        $('#exportAll').on('click', function(e) {
            e.preventDefault();
            handleExport('all');
        });

        // Export Pending
        $('#exportPending').on('click', function(e) {
            e.preventDefault();
            handleExport('Pending');
        });

        // Export Approved
        $('#exportApproved').on('click', function(e) {
            e.preventDefault();
            handleExport('Approved');
        });

        function handleExport(type) {
            const userName = $('#userName').val() || '';
            const startDate = $('#startDate').val() || '';
            const endDate = $('#endDate').val() || '';

            // Membuat URL dengan parameter
            const baseUrl = '{{ route("vehicles-reimbursements.exportExcel") }}';
            const params = new URLSearchParams({
                user_name: userName,
                start_date: startDate,
                end_date: endDate,
                export_type: type
            });

            // Debug: menampilkan URL final
            const finalUrl = `${baseUrl}?${params.toString()}`;

            // Redirect ke URL export
            window.location.href = finalUrl;
        }
    });

    function confirmApprovedReimbursement(id) {
        Swal.fire({
            title: 'Approve Reimbursement',
            text: "You want approve this reimbursements!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Approve it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#approveReimbursementForm-' + id).submit();
            }
        })
    }

    function confirmRejectReimbursement(id) {
        Swal.fire({
            title: 'Reject Reimbursement',
            text: 'Please provide a reason for rejection:',
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
                $('#rejectReason-' + id).val(result.value);
                document.getElementById('rejectReimbursementForm-' + id).submit();
            } else if (result.isDismissed) {
                // Action if dismissed
                Swal.fire('You cancelled the input.', '', 'info');
            }
        });
    }

    function confirmDeleteReimbursement(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want delete this reimbursements!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#destroyReimbursementForm-' + id).submit();
            }
        })
    }
</script>
@endpush