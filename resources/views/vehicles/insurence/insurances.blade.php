@extends('layouts.admin', [
    'title' => 'Vehicles Insurances'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> 
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Vehicles Insurances</h1>
    <p class="mb-4">
        This page is used to manage vehicles insurances.
    </p>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary d-flex justify-content-center">
            <h4 class="m-0 font-weight-bold text-white">Vehicles Insurances</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- form add vehicle insurance -->
                @can('create vehicle insurances')
                <div class="col-4">
                    <form action="{{ route('insurances.store') }}" method="POST" id="addInsurancesForm">
                        @csrf
                        <div class="form-group">
                            <label for="vehicle_code">Vehicle Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('vehicle_code') is-invalid @enderror" id="vehicle_code" name="vehicle_code" placeholder="Enter Vehicle Code">
                        </div>
                        <div class="form-group">
                            <label for="insurance_provider">Insurance Provider <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('insurance_provider') is-invalid @enderror" id="insurance_provider" name="insurance_provider" placeholder="Enter Insurance Company">
                        </div>
                        <div class="form-group">
                            <label for="policy_number">Policy Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('policy_number') is-invalid @enderror" id="policy_number" name="policy_number" placeholder="Enter Policy Number">
                        </div>
                        <div class="form-group">
                            <label for="premium">Premium <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('premium') is-invalid @enderror" id="premium" name="premium" placeholder="Enter Premium">
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" placeholder="Enter notes (optional)" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="coverage_start">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('coverage_start') is-invalid @enderror" id="coverage_start" name="coverage_start" placeholder="Enter Start Date">
                        </div>
                        <div class="form-group">
                            <label for="coverage_end">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('coverage_end') is-invalid @enderror" id="coverage_end" name="coverage_end" placeholder="Enter End Date">
                        </div>
                    </form>
                    <div class="float-right mt-3">
                        <button type="button" class="btn btn-primary" onclick="confirmAddInsurance()">
                            <i class="fas fa-check"></i> Save Insurance
                        </button>
                    </div>
                </div>
                @endcan
                <!--- List Car Insurance --->
                <div class="col-8">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Vehicle Code</th>
                                    <th>Insurance Number</th>
                                    <th>Insurance Provider</th>
                                    <th>Premium</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th width="10%" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($insurances as $insurance)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $insurance->vehicle->code }}</td>
                                        <td>{{ $insurance->policy_number }}</td>
                                        <td>{{ $insurance->insurance_provider }}</td>
                                        <td>Rp {{ number_format($insurance->premium, 2) }}</td>
                                        <td>{{ $insurance->coverage_start }}</td>
                                        <td>{{ $insurance->coverage_end }}</td>
                                        <td><span class="badge badge-{{ $insurance->status == 'Active' ? 'success' : 'danger' }}">{{ $insurance->status }}</span></td>
                                        <td class="text-center">
                                            <div class="d-inline-flex">
                                                @can('print vehicle insurances')
                                                <a href="{{ route('insurances.exportPdf', $insurance->id) }}" class="btn btn-sm btn-success mr-1 btn-circle">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                @endcan
                                                @can('show vehicle insurances')
                                                <a href="{{ route('insurances.show', $insurance->id) }}" class="btn btn-sm btn-info mr-1 btn-circle">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('update vehicle insurances')
                                                <button type="button" class="btn btn-sm btn-warning mr-1 btn-circle"
                                                    data-toggle="modal"
                                                    data-id="{{ $insurance->id }}"
                                                    data-code="{{ $insurance->vehicle->code }}"
                                                    data-policy="{{ $insurance->policy_number }}"
                                                    data-insurance="{{ $insurance->insurance_provider }}"
                                                    data-premium="{{ $insurance->premium }}"
                                                    data-notes="{{ $insurance->notes }}"
                                                    data-start="{{ $insurance->coverage_start }}"
                                                    data-end="{{ $insurance->coverage_end }}"
                                                    data-target="#updateInsurancesModal">
                                                    <i class="fas fa-pencil"></i>
                                                </button>
                                                @endcan
                                                @can('delete vehicle insurances')
                                                <form action="{{ route('insurances.destroy', $insurance->id) }}" method="post" id="deleteInsurancesForm-{{ $insurance->id }}">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="button" class="btn btn-sm btn-danger btn-circle" onclick="confirmDeleteInsurance({{ $insurance->id }})">
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

    <!-- Modal Update Insurances -->
    <div class="modal fade" id="updateInsurancesModal" tabindex="-1" role="dialog" aria-labelledby="updateInsurancesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="updateInsurancesModalLabel">Update Insurances</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('insurances.update', ':id') }}" method="post" id="updateInsurancesForm">
                        @csrf
                        @method('put')
                        <div class="form-group">
                            <label for="vehicle_code">Vehicle Code</label>
                            <input type="text" class="form-control" id="code" name="vehicle_code" placeholder="Enter Vehicle Code" readonly>
                        </div>
                        <div class="form-group">
                            <label for="insurance_provider">Insurance Provider</label>
                            <input type="text" class="form-control" id="insurance" name="insurance_provider" placeholder="Enter Insurance Provider">
                        </div>
                        <div class="form-group">
                            <label for="policy_number">Policy Number</label>
                            <input type="text" class="form-control" id="policy" name="policy_number" placeholder="Enter Policy Number">
                        </div>
                        <div class="form-group">
                            <label for="premium">Premium</label>
                            <input type="number" class="form-control" id="premium" name="premium" placeholder="Enter Premium">
                        </div>
                        <div class="form-group">
                            <label for="coverage_start">Start Date</label>
                            <input type="date" class="form-control" id="start" name="coverage_start" placeholder="Enter Start Date">
                        </div>
                        <div class="form-group">
                            <label for="coverage_end">End Date</label>
                            <input type="date" class="form-control" id="end" name="coverage_end" placeholder="Enter End Date">
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" placeholder="Enter notes (optional)" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmUpdateInsurances()"><i class="fas fa-check"></i> Save changes</button>
                    </div>
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
</script>

<script>
    function confirmAddInsurance(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to create this insurance!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addInsurancesForm').submit();
            }
        })
    }

    $('#updateInsurancesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var code = button.data('code');
        var policy = button.data('policy');
        var insurance = button.data('insurance');
        var premium = button.data('premium');
        var notes = button.data('notes');
        var start = button.data('start');
        var end = button.data('end');

        var modal = $(this);
        modal.find('.modal-body #code').val(code);
        modal.find('.modal-body #policy').val(policy);
        modal.find('.modal-body #insurance').val(insurance);
        modal.find('.modal-body #premium').val(premium);
        modal.find('.modal-body #notes').val(notes);
        modal.find('.modal-body #start').val(start);
        modal.find('.modal-body #end').val(end);

        var action = $('#updateInsurancesForm').attr('action');
        var newAction = action.replace(':id', id);
        $('#updateInsurancesForm').attr('action', newAction);
    });

    function confirmUpdateInsurances(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateInsurancesForm').submit();
            }
        })
    }

    function confirmDeleteInsurances(insuranceId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this insurance!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteInsurancesForm-' + insuranceId).submit();
            }
        });
    }
</script>
@endpush