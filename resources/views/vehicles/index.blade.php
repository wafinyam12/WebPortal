@extends('layouts.admin', [
    'title' => 'Vehicles Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
                    
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Vehicles</h1>
    <p class="mb-4">
        This page is used to manage vehicles. 
    </p>

    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Vehicles</h4>
                <div class="d-flex align-items-center flex-wrap">
                    <!-- Tombol Add Users -->
                    @can('create vehicles')
                    <button type="button" class="btn btn-primary btn-md ml-2 mb-2" data-toggle="modal" data-target="#addVehiclesModal">
                        <i class="fas fa-truck-fast fa-md white-50"></i> Add Vehicles
                    </button>
                    @endcan
                </div>
            </div> 
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Code</th>
                                <th>Model</th>
                                <th>License Plate</th>
                                <th>Tax Year</th>
                                <th>Tax Five Year</th>
                                <th>Inspected</th>
                                <th>Assigned</th>
                                <th>Status</th>
                                <th width="10%" class="text-center" >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicles as $vehicle)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('tools.show', $vehicle->code) }}">
                                            <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(80)->generate(route('vehicles.show', $vehicle->code))) }}" 
                                                 alt="QR Code">
                                        </a>    
                                        <br>
                                        <b>
                                            {{ $vehicle->code }}
                                        </b>
                                    </td>
                                    <td>{{ $vehicle->model }}</td>
                                    <td>{{ $vehicle->license_plate }}</td>
                                    <td>{{ date('d M Y', strtotime($vehicle->tax_year)) }}</td>
                                    <td>{{ date('d M Y', strtotime($vehicle->tax_five_year)) }}</td>
                                    <td>{{ $vehicle->inspected ? date('d M Y', strtotime($vehicle->inspected)) : '-' }}</td>
                                    <td>{{ $vehicle->assigned->last()->employe->full_name ?? '-' }}</td>
                                    <td>{!! $vehicle->badgeClass !!}</td>
                                    <td>
                                        <div class="d-inline-flex">
                                            @can('show vehicles')
                                            <a href="{{ route('vehicles.show', $vehicle->id) }}" class="btn btn-info mr-1 btn-circle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('assign vehicles')
                                            <button type="button" class="btn btn-primary mr-1 btn-circle"  
                                                data-toggle="modal"
                                                data-id="{{ $vehicle->id }}"
                                                data-target="#assignVehiclesModal">
                                                <i class="fas fa-users"></i>
                                            </button>
                                            @endcan
                                            @can('update vehicles')
                                            <button type="button" class="btn btn-warning mr-1 btn-circle"
                                                data-toggle="modal"
                                                data-id="{{ $vehicle->id }}"
                                                data-owner="{{ $vehicle->ownership->id }}"
                                                data-type="{{ $vehicle->type->id }}"
                                                data-brand="{{ $vehicle->brand }}"
                                                data-model="{{ $vehicle->model }}"
                                                data-color="{{ $vehicle->color }}"
                                                data-transmisi="{{ $vehicle->transmission }}"
                                                data-fuel="{{ $vehicle->fuel }}"
                                                data-year="{{ $vehicle->year }}"
                                                data-license_plate="{{ $vehicle->license_plate }}"
                                                data-tax_year="{{ $vehicle->tax_year }}"
                                                data-tax_five_year="{{ $vehicle->tax_five_year }}"
                                                data-inspected="{{ $vehicle->inspected }}"
                                                data-purchase_date="{{ $vehicle->purchase_date }}"
                                                data-purchase_price="{{ $vehicle->purchase_price }}"
                                                data-description="{{ $vehicle->description }}"
                                                data-status="{{ $vehicle->status }}"
                                                data-origin="{{ $vehicle->origin }}"
                                                data-target="#updateVehiclesModal">
                                                <i class="fas fa-pencil"></i>
                                            </button>
                                            @endcan
                                            @can('delete vehicles')
                                            <form action="{{ route('vehicles.destroy', $vehicle->id) }}" method="post" id="deleteVehiclesForm" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmVehiclesDelete()" class="btn btn-danger btn-circle">
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

    @can('view vehicle types')
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">List Type Vehicles</h4>
                <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#addVehiclesTypeModal">
                    <i class="fas fa-list fa-md white-50"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                        <thead class="bg-light">
                            <tr>
                                <td width="5%">No</td>
                                <td>Name</td>
                                <td>Description</td>
                                <td class="text-center" width="10%">Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicleTypes as $vehiclesType)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $vehiclesType->name }}</td>
                                    <td>{{ $vehiclesType->description }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            @can('update vehicle types')
                                            <button type="button" class="btn btn-warning mr-2 btn-circle"
                                                data-toggle="modal"
                                                data-id="{{ $vehiclesType->id }}"
                                                data-name="{{ $vehiclesType->name }}"
                                                data-description="{{ $vehiclesType->description }}"
                                                data-target="#editVehiclesTypeModal">
                                                <i class="fas fa-pencil"></i>
                                            </button>
                                            @endcan
                                            @can('delete vehicle types')
                                            <form action="{{ route('types.destroy', $vehiclesType->id) }}" method="post" id="deleteVehiclesTypeForm" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-circle" onclick="confirmVehiclesTypeDelete()">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Modal Add Vehicles -->
    <div class="modal fade" id="addVehiclesModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="addModalLabel">Create Vehicles</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vehicles.store') }}" id="addVehiclesForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand">Brand <span class="text-danger">*</span></label>
                                    <input type="text" name="brand" id="brand" class="form-control @error('brand') is-invalid @enderror" placeholder="Brand" value="{{ old('brand') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model">Model <span class="text-danger">*</span></label>
                                    <input type="text" name="model" id="model" class="form-control @error('model') is-invalid @enderror" placeholder="Model" value="{{ old('model') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ownership">Ownership <span class="text-danger">*</span></label>
                                    <select name="ownership" id="ownership" class="form-control @error('ownership') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Ownership</option>
                                        @foreach ($vehicleOwnerships as $ownership)
                                            <option value="{{ $ownership->id }}">{{ $ownership->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="license_plate">License Plate <span class="text-danger">*</span></label>
                                    <input type="text" name="license_plate" id="license_plate" class="form-control @error('license_plate') is-invalid @enderror" placeholder="License Plate" value="{{ old('license_plate') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="origin">Origin</label>
                                    <input type="text" name="origin" id="origin" class="form-control @error('origin') is-invalid @enderror" placeholder="Origin" value="{{ old('origin') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="photo">Photo</label>
                                    <input type="file" name="photo" id="photo" class="form-control" placeholder="Select Photo" accept="image/*">
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vehicle_type">Vehicle Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_type" id="vehicle_type" class="form-control @error('vehicle_type') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Vehicle Type</option>
                                        @foreach ($vehicleTypes as $vehicleType)
                                            <option value="{{ $vehicleType->id }}">{{ $vehicleType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="fuel">Fuel <span class="text-danger">*</span></label>
                                    <select name="fuel" id="fuel" class="form-control @error('fuel') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Fuel</option>
                                        <option value="Gasoline">Gasoline</option>
                                        <option value="Diesel">Diesel</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="year">Year <span class="text-danger">*</span></label>
                                    <input type="number" name="year" id="year" class="form-control @error('year') is-invalid @enderror" placeholder="Year" value="{{ old('year') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="color">Color <span class="text-danger">*</span></label>
                                    <input type="text" name="color" id="color" class="form-control @error('color') is-invalid @enderror" placeholder="Color" value="{{ old('color') }}" required>
                                </div>   
                                {{-- <div class="form-group">
                                    <label for="purchase_price">Purchase Price</label>
                                    <input type="text" name="purchase_price" id="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror" placeholder="Purchase Price" value="{{ old('purchase_price') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" name="purchase_date" id="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror" placeholder="Purchase Date" value="{{ old('purchase_date') }}" required>
                                </div>  --}}
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="transmission">Transmission <span class="text-danger">*</span></label>
                                    <select name="transmission" id="transmission" class="form-control @error('transmission') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Transmission</option>
                                        <option value="Automatic">Automatic</option>
                                        <option value="Manual">Manual</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tax_year">Tax Year <span class="text-danger">*</span></label>
                                    <input type="date" name="tax_year" id="tax_year" class="form-control @error('tax_year') is-invalid @enderror" placeholder="Tax Year" value="{{ old('tax_year') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="tax_five_year">Tax Five Years <span class="text-danger">*</span></label>
                                    <input type="date" name="tax_five_year" id="tax_five_year" class="form-control @error('tax_five_year') is-invalid @enderror" placeholder="Tax Five Years" value="{{ old('tax_five_years') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="inspected">Inspected <span class="text-danger">*</span></label>
                                    <input type="date" name="inspected" id="inspected" class="form-control @error('inspected') is-invalid @enderror" placeholder="Inspected" value="{{ old('inspected') }}">
                                    <p class="text-danger">for trucks only</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="5" placeholder="Description (optional)" value="{{ old('description') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddVehicles()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Type Vehicles -->
    <div class="modal fade" id="addVehiclesTypeModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="addModalLabel">Create Type Vehicles</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('types.store') }}" id="addVehiclesTypeForm" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="name">Vehicle Type</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Vehicle Type" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description" cols="30" rows="3" placeholder="Description Vehicle Type (Optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddTypeVehicles()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Assign Vehicles -->
    <div class="modal fade" id="assignVehiclesModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="assignModalLabel">Assign Vehicles to Employee</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vehicles.assign', ':id') }}" method="POST" id="assignVehiclesForm">
                        @csrf
                        <div class="form-group">
                            <label for="employee">Employee <span class="text-danger">*</span></label>
                            <select name="employee" id="employee" class="form-control @error('employee') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Employee</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->fullName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assignment_date">Assingn Date <span class="text-danger">*</span></label>
                            <input type="date" name="assignment_date" id="assignment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control" cols="30" rows="5" placeholder="Notes (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAssignVehicles()"><i class="fas fa-check"></i> Assign Vehicle</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Vehicles -->
    <div class="modal fade" id="updateVehiclesModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="editModalLabel">Update Vehicles</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('vehicles.update', ':id') }}" id="updateVehiclesForm" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="status">Statu <span class="text-danger">*</span></label>
                            <select name="status" id="vehiclesStatus" class="form-control @error('status') is-invalid @enderror">
                                <option value="" disabled selected>Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="brand">Brand <span class="text-danger">*</span></label>
                                    <input type="text" name="brand" id="vehiclesBrand" class="form-control @error('brand') is-invalid @enderror" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="model">Model <span class="text-danger">*</span></label>
                                    <input type="text" name="model" id="vehiclesModel" class="form-control @error('model') is-invalid @enderror" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ownership">Ownership <span class="text-danger">*</span></label>
                                    <select name="ownership" id="vehiclesOwner" class="form-control">
                                        <option value="" disabled selected>Select Ownership</option>
                                        @foreach ($vehicleOwnerships as $ownership)
                                            <option value="{{ $ownership->id }}">{{ $ownership->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="license_plate">License Plate <span class="text-danger">*</span></label>
                                    <input type="text" name="license_plate" id="vehicleslicense" class="form-control @error('license_plate') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="origin">Origin</label>
                                    <input type="text" name="origin" id="vehiclesOrigin" class="form-control @error('origin') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="photo">Photo</label>
                                    <input type="file" name="photo" id="vehiclesPhoto" class="form-control" placeholder="Select Photo" accept="image/*">
                                </div>
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="vehicle_type">Vehicle Type <span class="text-danger">*</span></label>
                                    <select name="vehicle_type" id="vehiclesType" class="form-control @error('vehicle_type') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Vehicle Type</option>
                                        @foreach ($vehicleTypes as $vehicleType)
                                            <option value="{{ $vehicleType->id }}">{{ $vehicleType->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="fuel">Fuel <span class="text-danger">*</span></label>
                                    <select name="fuel" id="vehiclesFuel" class="form-control @error('fuel') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Fuel</option>
                                        <option value="Gasoline">Gasoline</option>
                                        <option value="Diesel">Diesel</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="year">Year <span class="text-danger">*</span></label>
                                    <input type="number" name="year" id="vehiclesYear" class="form-control @error('year') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="color">Color <span class="text-danger">*</span></label>
                                    <input type="text" name="color" id="vehiclesColor" class="form-control @error('color') is-invalid @enderror" required>
                                </div>  
                                {{-- <div class="form-group">
                                    <label for="purchase_price">Purchase Price</label>
                                    <input type="text" name="purchase_price" id="vehiclesPurchasePrice" class="form-control @error('purchase_price') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" name="purchase_date" id="vehiclesPurchaseDate" class="form-control @error('purchase_date') is-invalid @enderror" required>
                                </div>  --}}
                            </div> 
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="transmission">Transmission <span class="text-danger">*</span></label>
                                    <select name="transmission" id="vehiclesTransmission" class="form-control @error('transmission') is-invalid @enderror" required>
                                        <option value="" disabled selected>Select Transmission</option>
                                        <option value="Automatic">Automatic</option>
                                        <option value="Manual">Manual</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="tax_year">Tax Year <span class="text-danger">*</span></label>
                                    <input type="date" name="tax_year" id="vehiclesTaxYear" class="form-control @error('tax_year') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="tax_five_year">Tax Five Years <span class="text-danger">*</span></label>
                                    <input type="date" name="tax_five_year" id="vehiclesTaxFive" class="form-control @error('tax_five_year') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="inspected">Inspected <span class="text-danger">*</span></label>
                                    <input type="date" name="inspected" id="vehiclesInspected" class="form-control @error('inspected') is-invalid @enderror" required>
                                    <p class="text-danger">for trucks only</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="vehiclesDescription" class="form-control" cols="30" rows="5"></textarea>
                        </div> 
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmVehiclesUpdate()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Type Vehicles -->
    <div class="modal fade" id="editVehiclesTypeModal" tabindex="-1" aria-labelledby="editTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="editTypeModalLabel">Update Type Vehicles</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('types.update', ':id') }}" id="editVehiclesTypeForm" method="post">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Vehicle Type Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="vehiclesTypeName" class="form-control @error('name') is-invalid @enderror" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="vehiclesTypeDescription" cols="30" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateType()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Vehicles -->
    <div class="modal fade" id="importVehiclesModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="importModalLabel">Import Vehicles</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" id="importVehiclesForm" method="post">
                        @csrf
                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                            <p class="text-danger">*Format file .xlsx .xls .csv</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmImport()"><i class="fas fa-check"></i> Save</button>
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

        $('#dataTable1').DataTable();
    });

    function confirmAddVehicles() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want create this vehicle!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addVehiclesForm').submit();
            }
        })
    }

    function confirmAddTypeVehicles(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You want create this vehicle type!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addVehiclesTypeForm').submit();
            }
        })
    }

    $('#editVehiclesTypeModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var vehiclesTypeId = button.data('id');
        var vehiclesTypeName = button.data('name');
        var vehiclesTypeDescription = button.data('description');

        var modal = $(this);
        modal.find('.modal-body #vehiclesTypeName').val(vehiclesTypeName);
        modal.find('.modal-body #vehiclesTypeDescription').val(vehiclesTypeDescription);
        
        //replace action form
        var action = $('#editVehiclesTypeForm').attr('action');
        var newAction = action.replace(':id', vehiclesTypeId);
        $('#editVehiclesTypeForm').attr('action', newAction);
    })

    function confirmUpdateType() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want update this vehicle type",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#editVehiclesTypeForm').submit();
            }
        })
    }

    $('#updateVehiclesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var vehiclesId = button.data('id');
        var vehiclesType = button.data('type');
        var vehiclesOwner = button.data('owner');
        var vehiclesTransmission = button.data('transmisi');
        var vehiclesOrigin = button.data('origin');
        var vehiclesName = button.data('name');
        var vehiclesBrand = button.data('brand');
        var vehiclesModel = button.data('model');
        var vehiclesColor = button.data('color');
        var vehiclesYear = button.data('year');
        var vehiclesFuel = button.data('fuel');
        var vehicleslicense = button.data('license_plate');
        var vehiclesTaxYear = button.data('tax_year');
        var vehiclesTaxFive = button.data('tax_five_year');
        var vehiclesInspected = button.data('inspected');
        var vehiclesPurchaseDate = button.data('purchase_date');
        var vehiclesPurchasePrice = button.data('purchase_price');
        var vehiclesPhoto = button.data('photo');
        var vehiclesDescription = button.data('description');
        var vehiclesStatus = button.data('status');
        
        var modal = $(this);
        modal.find('.modal-body #vehiclesType').val(vehiclesType).trigger('change');
        modal.find('.modal-body #vehiclesOwner').val(vehiclesOwner).trigger('change');
        modal.find('.modal-body #vehiclesTransmission').val(vehiclesTransmission).trigger('change');
        modal.find('.modal-body #vehiclesOrigin').val(vehiclesOrigin);
        modal.find('.modal-body #vehiclesName').val(vehiclesName);
        modal.find('.modal-body #vehiclesBrand').val(vehiclesBrand);
        modal.find('.modal-body #vehiclesModel').val(vehiclesModel);
        modal.find('.modal-body #vehiclesColor').val(vehiclesColor);
        modal.find('.modal-body #vehiclesYear').val(vehiclesYear);
        modal.find('.modal-body #vehiclesFuel').val(vehiclesFuel).trigger('change');
        modal.find('.modal-body #vehicleslicense').val(vehicleslicense);
        modal.find('.modal-body #vehiclesTaxYear').val(vehiclesTaxYear);
        modal.find('.modal-body #vehiclesTaxFive').val(vehiclesTaxFive);
        modal.find('.modal-body #vehiclesInspected').val(vehiclesInspected);
        modal.find('.modal-body #vehiclesPurchaseDate').val(vehiclesPurchaseDate);
        modal.find('.modal-body #vehiclesPurchasePrice').val(vehiclesPurchasePrice);
        // modal.find('.modal-body #vehiclesPhoto').val(vehiclesPhoto);
        modal.find('.modal-body #vehiclesDescription').val(vehiclesDescription);
        modal.find('.modal-body #vehiclesStatus').val(vehiclesStatus).trigger('change');

        //replace action form
        var action = $('#updateVehiclesForm').attr('action');
        var newAction = action.replace(':id', vehiclesId);
        $('#updateVehiclesForm').attr('action', newAction);
    })

    function confirmVehiclesUpdate() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want update this vehicle!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('updateVehiclesForm').submit();
            }
        })
    }    

    function confirmVehiclesDelete() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want delete this vehicle!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteVehiclesForm').submit();
            }
        })
    }

    function confirmVehiclesTypeDelete() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want delete this vehicle type!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteVehiclesTypeForm').submit();
            }
        })
    }

    $('#assignVehiclesModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var vehiclesId = button.data('id');
        
        var modal = $(this);
        //replace action form
        var action = $('#assignVehiclesForm').attr('action');
        var newAction = action.replace(':id', vehiclesId);
        $('#assignVehiclesForm').attr('action', newAction);
    })

    function confirmAssignVehicles() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want assign this vehicle!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Assign it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('assignVehiclesForm').submit();
            }
        })
    }
</script>
@endpush