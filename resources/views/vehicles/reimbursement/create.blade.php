@extends('layouts.admin', [
    'title' => 'Vehicles Create Reimbursement'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Vehicles Create Reimbursement</h1>
<p class="mb-4">
    This page is used to manage vehicles reimbursements.
</p>

<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">List Cretae Reimbursements</h4>
            <div class="d-flex align-items-center flex-wrap">
                <button type="button" class="btn btn-success btn-md ml-2 mb-2" data-toggle="modal" data-target="#claimParkingModal">
                    <i class="fas fa-square-parking fa-md white-50"></i> Clime Parking
                </button>
                <button type="button" class="btn btn-warning btn-md ml-2 mb-2" data-toggle="modal" data-target="#claimTollModal">
                    <i class="fas fa-road fa-md white-50"></i> Clime E-Toll
                </button>
                <button type="button" class="btn btn-primary btn-md ml-2 mb-2" data-toggle="modal" data-target="#claimBBMModal">
                    <i class="fas fa-oil-can fa-md white-50"></i> Clime BBM
                </button>
            </div>
        </div> 
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Name Vehicle</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Fuel</th>
                            <th>Amount</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th class="text-center" width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reimbursements as $reimbursement)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $reimbursement->Vehicle->model }}</td>
                                <td>{{ date('d M Y', strtotime($reimbursement->date_recorded)) }}</td>
                                <td>{!! $reimbursement->typeName !!}</td>
                                <td>{{ $reimbursement->fuel ?? ' ' }}</td>
                                <td>{{ $reimbursement->amount ?? ' ' }} Liter</td>
                                <td>Rp {{ number_format($reimbursement->price, 2) }}</td>
                                <td>{!! $reimbursement->statusName !!}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex">
                                        @can('show vehicle reimbursements')
                                        <a href="{{ route('reimbursements.show', $reimbursement->id) }}" class="btn btn-info mr-1 btn-circle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('delete vehicle reimbursements')
                                        <form action="{{ route('reimbursements.destroy', $reimbursement->id) }}" method="post" id="destroyReimbursementForm-{{ $reimbursement->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger mr-1 btn-circle" onclick="confirmDeleteReimbursement({{ $reimbursement->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Claim Parking -->
<div class="modal fade" id="claimParkingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary d-flex justify-content-center">
                <h4 class="modal-title font-weight-bold text-white mx-auto" id="exampleModalLabel">Claim Parking</h4>
                <button class="close position-absolute" type="button" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reimbursements.store') }}" method="post" id="claimParkingForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="code">Code Vehicle</label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="Code Vehicle" value="{{ $vehicleCode }}" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date Receipt</label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" placeholder="Date" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" @readonly(true) required>
                            <option value="Parking" selected>Parking</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="Price" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Notes (optional)" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="receipt">Receipt</label>
                        <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') is-invalid @enderror" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-primary" type="button" onclick="confirmClaimParking()"><i class="fas fa-check"></i> Claim</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Claim Toll -->
<div class="modal fade" id="claimTollModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary d-flex justify-content-center">
                <h4 class="modal-title font-weight-bold text-white mx-auto" id="exampleModalLabel">Claim Toll</h4>
                <button class="close position-absolute" type="button" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reimbursements.store') }}" method="post" id="claimTollForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="code">Code Vehicle</label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="Code Vehicle" value="{{ $vehicleCode }}" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date Receipt</label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" placeholder="Date" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" @readonly(true) required>
                            <option value="E-Toll" selected>E-Toll</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="Price" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Notes (optional)" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="receipt">Receipt</label>
                        <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') is-invalid @enderror" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-primary" type="button" onclick="confirmClaimToll()"><i class="fas fa-check"></i> Claim</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Claim BBM -->
<div class="modal fade" id="claimBBMModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary d-flex justify-content-center">
                <h4 class="modal-title font-weight-bold text-white mx-auto" id="exampleModalLabel">Claim BBM</h4>
                <button class="close position-absolute" type="button" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('reimbursements.store') }}" method="post" id="claimBBMForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="code">Code Vehicle</label>
                        <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" placeholder="Code Vehicle" value="{{ $vehicleCode }}" readonly required>
                    </div>
                    <div class="form-group">
                        <label for="date">Date Receipt</label>
                        <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" placeholder="Date" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" @readonly(true) required>
                            <option value="Refueling" selected>Refueling</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_mileage">First Mileage</label>
                                <input type="number" name="first_mileage" id="first_mileage" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="fuel">Fuel</label>
                                <select name="fuel" id="fuel" class="form-control">
                                    <option value="" selected disabled>--Select Fuel---</option>
                                    <option value="Pertalite">Pertalite</option>
                                    <option value="Pertamax">Pertamax</option>
                                    <option value="Pertamax Turbo">Pertamax Turbo</option>
                                    <option value="Solar">Solar</option>
                                    <option value="Dex">Dex</option>
                                    <option value="Dex Lite">Dex Lite</option>
                                    <option value="Shell Super">Shell Super</option>
                                    <option value="Shell V-Power">Shell V-Power</option>
                                    <option value="Shell V-Power Diesel">Shell V-Power Diesel</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="attachment_mileage">Attachment Mileage</label>
                                <input type="file" name="attachment_mileage" id="attachment_mileage" class="form-control @error('attachment_mileage') is-invalid @enderror"  accept="image/*" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_mileage">Last Mileage</label>
                                <input type="number" name="last_mileage" id="last_mileage" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input type="number" name="amount" id="amount" class="form-control" placeholder="Liter">
                            </div>
                            <div class="form-group">
                                <label for="receipt">Receipt</label>
                                <input type="file" name="receipt" id="receipt" class="form-control @error('receipt') is-invalid @enderror" accept="image/*" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" placeholder="Price" required>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" placeholder="Notes (optional)" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" type="button" data-dismiss="modal"><i class="fas fa-times"></i> Cancel</button>
                <button class="btn btn-primary" type="button" onclick="confirmClaimBBM()"><i class="fas fa-check"></i> Claim</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Page level custom scripts  -->
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    function confirmClaimParking() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want claim this parking!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Claim it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#claimParkingForm').submit();
            }
        })
    }

    function confirmClaimBBM() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want claim this BBM!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Claim it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#claimBBMForm').submit();
            }
        })
    }
    
    function confirmClaimToll() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want claim this toll!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Claim it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#claimTollForm').submit();
            }
        })
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