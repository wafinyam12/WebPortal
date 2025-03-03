@extends('layouts.admin', [
    'title' => 'Maintenances Tools Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> 
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Tools Maintenances Management</h1>
    <p class="mb-4">
        This page is used to manage tools maintenances.
    </p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-center bg-gradient-primary">
            <h4 class="m-0 font-weight-bold text-white">Tools Maintenances</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- form add vehicle insurance -->
                <div class="col-4">
                    @can('create tools maintenances')
                    <form action="{{ route('tools-maintenances.store') }}" method="POST" id="addMaintenancesForm">
                        @csrf
                        <div class="form-group">
                            <label for="vehicle_code">Tools Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" placeholder="Enter Tools Code" required>
                        </div>
                        <div class="form-group">
                            <label for="cost">Cost <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('cost') is-invalid @enderror" id="cost" name="cost" placeholder="Enter Cost">
                        </div>
                        <div class="form-group">
                            <label for="maintenance_date">Maintenances Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('maintenance_date') is-invalid @enderror" id="maintenance_date" name="maintenance_date" placeholder="Enter Maintenances Date">
                        </div>
                        <div class="form-group">
                            <label for="description">Maintenances Description</label>
                            <textarea name="description" id="description" class="form-control" id="description" cols="30" rows="4" placeholder="Enter Maintenances Description (optional)"></textarea>
                        </div>
                    </form>
                    <div class="float-right my-3">
                        <button type="button" class="btn btn-primary" onclick="confirmAddMaintenances()"><i class="fas fa-check"></i> Save Maintenances</button>
                    </div>
                    @endcan
                </div>
                <!--- List Car Insurance --->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Tools Code</th>
                                    <th width="20%" class="text-center">Date</th>
                                    <th class="text-center">Status</th>
                                    <th width="10%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($maintenances as $maintenance)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $maintenance->tools->code }} - {{ $maintenance->tools->name }}</td>
                                        <td class="text-center">{{ date('d-m-Y', strtotime($maintenance->maintenance_date)) }}</td>
                                        <td class="text-center"><span class="badge badge-{{ $maintenance->badgeClass }}">{{ $maintenance->status }}</span></td>
                                        <td class="text-center">
                                            <div class="d-inline-flex">
                                                @can('print tools maintenances')
                                                <a href="{{ route('tools-maintenances.exportPdf', $maintenance->id) }}" class="btn btn-sm btn-info mr-1 btn-circle">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endcan
                                                @can('show tools maintenances')
                                                <a href="{{ route('tools-maintenances.show', $maintenance->id) }}" class="btn btn-sm btn-primary mr-1 btn-circle">
                                                    <i class="fas fa-eye fa-md white-50"></i>
                                                </a>
                                                @endcan
                                                @if ($maintenance->status != 'Completed' && $maintenance->status != 'Cancelled')
                                                @can('complete tools maintenances')
                                                <form action="{{ route('tools-maintenances.completeMaintenance', $maintenance->id) }}" method="POST" id="completeMaintenancesForm-{{ $maintenance->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-success mr-1 btn-circle" onclick="confirmCompleteMaintenances({{ $maintenance->id }})">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                @can('cancel tools maintenances')
                                                <form action="{{ route('tools-maintenances.cancelMaintenance', $maintenance->id) }}" method="POST" id="cencelMaintenancesForm-{{ $maintenance->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" class="btn btn-sm btn-danger mr-1 btn-circle" onclick="confirmCencelMaintenances({{ $maintenance->id }})">
                                                        <i class="fas fa-xmark"></i>
                                                    </button>
                                                </form>
                                                @endcan
                                                @can('update tools maintenances')
                                                <button type="button" class="btn btn-sm btn-warning mr-1 btn-circle"
                                                    data-toggle="modal"
                                                    data-id="{{ $maintenance->id }}"
                                                    data-date="{{ $maintenance->maintenance_date }}"
                                                    data-cost="{{ $maintenance->cost }}"
                                                    data-description="{{ $maintenance->description }}"
                                                    data-target="#updateMaintenancesModal">
                                                    <i class="fas fa-pencil fa-md white-50"></i>
                                                </button>
                                                @endcan
                                                @endif
                                                @can('delete tools maintenances')
                                                <form action="{{ route('tools-maintenances.destroy', $maintenance->id) }}" method="post" id="deleteMaintenancesForm-{{ $maintenance->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-sm btn-danger mr-1 btn-circle" onclick="confirmDeleteMaintenances({{ $maintenance->id }})">
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
    </div>

    <!-- Modal update maintenance  -->
    <div class="modal fade" id="updateMaintenancesModal" tabindex="-1" role="dialog" aria-labelledby="updateMaintenancesModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="updateMaintenancesModalLabel">Update Maintenance</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tools-maintenances.update', ':id') }}" method="POST" id="updateMaintenancesForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="cost">Cost</label>
                            <input type="number" class="form-control @error('cost') is-invalid @enderror" id="costEdit" name="cost" placeholder="Enter Cost">
                        </div>
                        <div class="form-group">
                            <label for="maintenance_date">Maintenances Date</label>
                            <input type="date" class="form-control @error('maintenance_date') is-invalid @enderror" id="maintenance_dateEdit" name="maintenance_date" placeholder="Enter Maintenances Date">
                        </div>
                        <div class="form-group">
                            <label for="description">Maintenances Description</label>
                            <textarea name="description" id="descriptionEdit" class="form-control" id="" cols="30" rows="4"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateMaintenances()"><i class="fas fa-check"></i> Save</button>
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
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addMaintenancesForm').submit();
            }
        });
    }

    $('#updateMaintenancesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var date = button.data('date');
        var cost = button.data('cost');
        var description = button.data('description');

        var modal = $(this);
        modal.find('#maintenance_dateEdit').val(date);
        modal.find('#costEdit').val(cost);
        modal.find('#descriptionEdit').val(description);
        modal.find('#updateMaintenancesForm').attr('action', modal.find('#updateMaintenancesForm').attr('action').replace(':id', id));
    })

    function confirmUpdateMaintenances() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateMaintenancesForm').submit();
            }
        });
    }

    function confirmCompleteMaintenances(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Complete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('completeMaintenancesForm-' + id).submit();
            }
        });
    }

    function confirmCencelMaintenances(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Cancel it!',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cencelMaintenancesForm-' + id).submit();
            }
        });
    }

    function confirmDeleteMaintenances(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteMaintenancesForm-' + id).submit();
            }
        });
    }
</script>
@endpush