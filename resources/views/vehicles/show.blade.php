@extends('layouts.admin', [
    'title' => 'Vehicles Details'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> 
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Vehicles Details</h1>
    <p class="mb-4">
        This page is used to show vehicles.
    </p>

    <!-- Card Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">Vehicles Details</h4>
                <a href="{{ route('vehicles.index') }}" class="btn btn-primary btn-md"><i class="fas fa-reply"></i> Back</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mt-4 mb-3">
                            @if(!empty($vehicle->photo) && file_exists(storage_path('app/public/vehicles/photo/' . $vehicle->photo)))
                                <img src="{{ asset('storage/vehicles/photo/' . $vehicle->photo) }}" 
                                     class="img-fluid rounded shadow-sm border border-primary w-100"
                                     alt="Vehicle Photo">
                            @else
                                <img src="https://www.w3schools.com/w3css/img_avatar3.png" 
                                     class="img-fluid rounded shadow-sm w-100"
                                     alt="Default Image">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="text-center mb-4">
                            <h3 class="font-weight-bold text-primary">Specification Car</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Name</span>
                                    <span>{{ $vehicle->brand }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Model</span>
                                    <span>{{ $vehicle->model }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Year</span>
                                    <span>{{ $vehicle->year }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Color</span>
                                    <span>{{ $vehicle->color }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Annual Tax</span>
                                    <span>{{ $vehicle->tax_year }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Five Annual Tax</span>
                                    <span>{{ $vehicle->tax_five_year }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Inspected</span>
                                    <span>{{ $vehicle->inspected }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Type</span>
                                    <span>{{ $vehicle->type->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">License Plate</span>
                                    <span>{{ $vehicle->license_plate }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Transmission</span>
                                    <span>{{ $vehicle->transmission }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Fuel Type</span>
                                    <span>{{ $vehicle->fuel }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Price</span>
                                    <span>{{ $vehicle->purchase_price }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Date Purchase</span>
                                    <span>{{ $vehicle->purchase_date }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Status</span>
                                    {!! $vehicle->badgeClass !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center mb-4">
                            <h3 class="font-weight-bold text-primary">Description</h3>
                        </div>
                        <p>{{ $vehicle->description }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center mb-4">
                            <h3 class="font-weight-bold text-primary">History Car</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th width="5%">#</th>
                                                <th class="text-center">Activity</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($activities) && count($activities) > 0)
                                                @foreach ($activities as $index => $activity)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>
                                                            <strong>Type:</strong> 
                                                            <span class="text-{{ $activity['type'] == 'Maintenance' ? 'warning' : 'info' }} font-weight-bold">{{ $activity['type'] }}</span><br>
                                                            @if($activity['type'] === 'Maintenance')
                                                                <strong>Date:</strong> {{ $activity['date'] ?? '-' }}
                                                                <i class="fas fa-arrow-right"></i>
                                                                <strong>Description:</strong> {{ $activity['description'] ?? '-' }}
                                                                <i class="fas fa-arrow-right"></i>
                                                                <strong>Status:</strong>
                                                                <span class="text-{{ $activity['status'] == 'Completed' ? 'success' : 'secondary' }} font-weight-bold">{{ $activity['status'] }}</span>
                                                            @elseif($activity['type'] === 'Assigned')
                                                                <strong>Assigned:</strong> {{ $activity['user'] ?? '-' }}
                                                                <i class="fas fa-arrow-right"></i>
                                                                <strong>Date:</strong> {{ date('d M Y', strtotime($activity['date'])) ?? '-' }}
                                                                <i class="fas fa-arrow-right"></i>
                                                                <strong>Return Date:</strong> {{ $activity['return_date'] ?? '-' }}
                                                                <i class="fas fa-arrow-right"></i>
                                                                <strong>Notes:</strong> {{ $activity['notes'] ?? '-' }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="2" class="text-center">No activities available.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
@endpush