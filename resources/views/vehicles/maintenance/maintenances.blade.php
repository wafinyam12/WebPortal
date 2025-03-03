@extends('layouts.admin', [
    'title' => 'Vehicles Maintenances'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> 
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Vehicles Maintenances</h1>
    <p class="mb-4">
        This page is used to manage vehicles maintenances.
    </p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary d-flex justify-content-center">
            <h4 class="m-0 font-weight-bold text-white">Vehicles Maintenances</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- form add vehicle maintenances -->
                @can('create vehicle maintenances')
                <div class="col-4"> 
                    <form action="{{ route('vehicles-maintenances.store') }}" method="POST" id="addMaintenancesForm">
                        @csrf
                        <div class="form-group">
                            <label for="vehicle_code">Vehicle Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('vehicle_code') is-invalid @enderror" id="vehicle_code" name="vehicle_code" placeholder="Enter Vehicle Code">
                        </div>
                        <div class="form-group">
                            <label for="maintenances_date">Maintenances Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('maintenances_date') is-invalid @enderror" id="maintenances_date" name="maintenances_date" placeholder="Enter Maintenances Date">
                        </div>
                        <div class="form-group">
                            <label for="mileage">Mileage <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('mileage') is-invalid @enderror" id="mileage" name="mileage" placeholder="Enter Mileage">
                        </div>
                        <div class="form-group">
                            <label for="maintenances_description">Maintenances Description</label>
                            <textarea name="maintenances_description" id="maintenances_description" class="form-control" placeholder="Enter Maintenances Description (optional)" cols="30" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="maintenances_cost">Maintenances Cost <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('maintenances_cost') is-invalid @enderror" id="maintenances_cost" name="maintenances_cost" placeholder="Enter Maintenances Cost">
                        </div>
                        <div class="form-group">
                            <label for="next_maintenances">Next Maintenances <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('next_maintenances') is-invalid @enderror" id="next_maintenances" name="next_maintenances" placeholder="Enter Next Maintenances">
                        </div>
                    </form>
                    <div class="float-right mt-3">
                        <button type="button" class="btn btn-primary" onclick="confirmAddMaintenances()">
                            <i class="fas fa-check"></i> Save Maintenances
                        </button>
                    </div>
                </div>
                @endcan
                <!--- List Car Maintenances --->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="30%">Vehicle Code</th>
                                    <th width="25%">Date</th>
                                    <th width="20%">Cost</th>
                                    <th width="25%">Next</th>
                                    <th>Status</th>
                                    <th width="10%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($maintenances as $maintenance)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $maintenance->vehicle->code }} - {{ $maintenance->vehicle->model }}</td>
                                        <td>{{ date('d-M-Y', strtotime($maintenance->maintenance_date)) }}</td>
                                        <td>Rp {{ number_format(($maintenance->cost), 2) }}</td>
                                        <td>{{ date('d-M-Y', strtotime($maintenance->next_maintenance)) }}</td>
                                        <td>{!! $maintenance->statusName !!}</td>
                                        <td class="text-center">
                                            <div class="d-inline-flex">
                                                @can('print vehicle maintenances')
                                                <a href="{{ route('vehicles-maintenances.exportPdf', $maintenance->id) }}" class="btn btn-sm btn-info mr-1 btn-circle">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endcan
                                                @can('show vehicle maintenances')
                                                <a href="{{ route('vehicles-maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-primary mr-1 btn-circle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @if ($maintenance->status != 'Completed' && $maintenance->status != 'Cancelled')
                                                @can('complete vehicle maintenances')
                                                <form action="{{ route('vehicles-maintenances.completeMaintenance', $maintenance->id) }}" method="POST" id="completeMaintenancesForm-{{ $maintenance->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success mr-1 btn-circle" onclick="confirmCompleteMaintenances({{ $maintenance->id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                @can('cancel vehicle maintenances')
                                                <form action="{{ route('vehicles-maintenances.cancelMaintenance', $maintenance->id) }}" method="POST" id="cancelledMaintenancesForm-{{ $maintenance->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-danger mr-1 btn-circle" onclick="confirmCancelledMaintenances({{ $maintenance->id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                @can('update vehicle maintenances')
                                                <button type="button" class="btn btn-sm btn-warning mr-1 btn-circle"
                                                    data-toggle="modal"
                                                    data-id="{{ $maintenance->id }}"
                                                    data-code="{{ $maintenance->vehicle->code }}"
                                                    data-date="{{ $maintenance->maintenance_date }}"
                                                    data-mileage="{{ $maintenance->mileage }}"
                                                    data-description="{{ $maintenance->description }}"
                                                    data-cost="{{ $maintenance->cost }}"
                                                    data-next="{{ $maintenance->next_maintenance }}"
                                                    data-target="#updateMaintenancesModal">
                                                    <i class="fas fa-pencil"></i>
                                                </button>
                                                @endcan
                                                @endif
                                                @can('delete vehicle maintenances')
                                                <form action="{{ route('vehicles-maintenances.destroy', $maintenance->id) }}" method="POST" id="deleteMaintenancesForm-{{ $maintenance->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger btn-circle" onclick="confirmDeleteMaintenances({{ $maintenance->id }})">
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
    </div>

    <!-- Modal Update Maintenances -->
    <div class="modal fade" id="updateMaintenancesModal" tabindex="-1" role="dialog" aria-labelledby="updateMaintenancesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="updateMaintenancesModalLabel">Update Maintenances</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vehicles-maintenances.update', ':id') }}" method="POST" id="updateMaintenancesForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="vehicle_code">Vehicle Code</label>
                            <input type="text" class="form-control" id="vehiclecode" name="vehicle_code" placeholder="Enter Vehicle Code" readonly>
                        </div>
                        <div class="form-group">
                            <label for="maintenances_date">Maintenances Date</label>
                            <input type="date" class="form-control" id="maintenancesdate" name="maintenances_date" placeholder="Enter Maintenances Date">
                        </div>
                        <div class="form-group">
                            <label for="mileage">Mileage</label>
                            <input type="number" class="form-control" id="mileage" name="mileage" placeholder="Enter Mileage">
                        </div>
                        <div class="form-group">
                            <label for="maintenances_description">Maintenances Description</label>
                            <textarea name="maintenances_description" id="maintenancesdescription" class="form-control" id="" cols="30" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="maintenances_cost">Maintenances Cost</label>
                            <input type="text" class="form-control" id="maintenancescost" name="maintenances_cost" placeholder="Enter Maintenances Cost">
                        </div>
                        <div class="form-group">
                            <label for="next_maintenances">Next Maintenances</label>
                            <input type="date" class="form-control" id="nextmaintenances" name="next_maintenances" placeholder="Enter Next Maintenances">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateMaintenances()"><i class="fas fa-check"></i> Save changes</button>
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

    function confirmAddMaintenances() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want create this maintenances!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addMaintenancesForm').submit();
            }
        })
    }

    $('#updateMaintenancesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var code = button.data('code');
        var date = button.data('date');
        var mileage = button.data('mileage');
        var description = button.data('description');
        var cost = button.data('cost');
        var next = button.data('next');

        var modal = $(this);
        modal.find('.modal-body #vehiclecode').val(code);
        modal.find('.modal-body #maintenancesdate').val(date);
        modal.find('.modal-body #mileage').val(mileage);
        modal.find('.modal-body #maintenancesdescription').val(description);
        modal.find('.modal-body #maintenancescost').val(cost);
        modal.find('.modal-body #nextmaintenances').val(next);

        //replace action form
        var action = $('#updateMaintenancesForm').attr('action');
        var newAction = action.replace(':id', id);
        $('#updateMaintenancesForm').attr('action', newAction);
    })

    function confirmUpdateMaintenances() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want update this maintenances!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateMaintenancesForm').submit();
            }
        })
    }

    function confirmCompleteMaintenances(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want complete this maintenances!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Complete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('completeMaintenancesForm-' + id).submit();
            }
        })
    }

    function confirmCancelledMaintenances(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want cancel this maintenances!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelledMaintenancesForm-' + id).submit();
            }
        })
    }

    function confirmDeleteMaintenances(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want delete this maintenances!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteMaintenancesForm-' + id).submit();
            }
        })
    }
</script>
@endpush