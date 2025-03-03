@extends('layouts.admin', [
    'title' => 'Tools Details'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> 
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Tools Details</h1>
    <p class="mb-4">
        This page is used to show tools.
    </p>

    <!-- Card Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">Tools Details</h4>
                <a href="{{ route('tools.index') }}" class="btn btn-primary btn-md"><i class="fas fa-reply"></i> Back</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mt-4 mb-3 text-center">
                            @if(!empty($tools->photo) && file_exists(storage_path('app/public/tools/photo/' . $tools->photo)))
                                <img src="{{ asset('storage/tools/photo/' . $tools->photo) }}" 
                                     class="img-fluid rounded shadow-sm w-100" 
                                     alt="Tools Photo">
                            @else
                                <img src="https://www.w3schools.com/w3css/img_avatar3.png" 
                                     class="img-fluid rounded shadow-sm w-100"
                                     alt="Default Image">
                            @endif
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="text-center mb-4">
                            <h3 class="font-weight-bold text-primary">Specification Tools</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Name</span>
                                    <span>{{ $tools->brand }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Model</span>
                                    <span>{{ $tools->model }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Year</span>
                                    <span>{{ $tools->year }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Brand</span>
                                    <span>{{ $tools->brand }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Origin</span>
                                    <span>{{ $tools->origin }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Unit</span>
                                    <span>{{ $tools->unit }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Quantity</span>
                                    <span>{{ $tools->quantity }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Type</span>
                                    <span>{{ $tools->categorie->name }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Condition</span>
                                    <span>{!! $tools->conditionName !!}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Status</span>
                                    <span>{!! $tools->statusName !!}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Purchase Price</span>
                                    <span>{{ $tools->purchase_price }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Purchase Date</span>
                                    <span>{{ $tools->purchase_date }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Warranty</span>
                                    <span>{{ $tools->warranty }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="font-weight-bold text-muted">Warranty End</span>
                                    <span>{{ $tools->warranty_end }}</span>
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
                        <p>{{ $tools->description }}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="text-center mb-4">
                            <h3 class="font-weight-bold text-primary">History Of Tools</h3>
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
                                            @if (!empty($activities) && count($activities) > 0)
                                                @foreach ($activities as $index => $activity)
                                                    <tr>
                                                        <td class="text-center">{{ $index + 1 }}</td>
                                                        <td>
                                                            <strong>Type:</strong>
                                                            <span class="text-{{ $activity['type'] == 'Maintenance' ? 'warning' : 'info' }} font-weight-bold">
                                                                {{ $activity['type'] }}
                                                            </span><br>
                                    
                                                            @if ($activity['type'] === 'Maintenance')
                                                                <strong>Date:</strong> {{ $activity['details']['date'] ?? '-' }}<br>
                                                                <strong>Description:</strong> {{ $activity['details']['description'] ?? '-' }}<br>
                                                                <strong>Cost:</strong> {{ $activity['details']['cost'] ?? '-' }}<br>
                                                                <strong>Status:</strong>
                                                                <span class="text-{{ ($activity['details']['status'] ?? '') == 'Completed' ? 'success' : 'secondary' }} font-weight-bold">
                                                                    {{ $activity['details']['status'] ?? '-' }}
                                                                </span><br>
                                                                <strong>Completed At:</strong> {{ $activity['details']['completed_at'] ?? '-' }}
                                                            @elseif ($activity['type'] === 'Activity')
                                                                <strong>Transaction:</strong>
                                                                <span class="text-{{ $activity['transaction'] == 'Delivery Note' ? 'primary' : 'secondary' }} font-weight-bold">
                                                                    {{ $activity['transaction'] }}
                                                                </span><br>
                                                                <strong>Date:</strong> {{ $activity['date'] ?? '-' }}<br>
                                                                <strong>Driver:</strong> {{ $activity['driver'] ?? '-' }}<br>
                                                                <strong>Driver Phone:</strong> {{ $activity['driver_phone'] ?? '-' }}<br>
                                                                <strong>Status:</strong> {!! $activity['status'] ?? '-' !!}<br>
                                                                <strong>Notes:</strong> {{ $activity['notes'] ?? '-' }}<br>
                                    
                                                                <strong>Details:</strong>
                                                                <ul>
                                                                    <li><strong>Source Project:</strong> {{ $activity['details']['source_project'] ?? '-' }}</li>
                                                                    <li><strong>Destination Project:</strong> {{ $activity['details']['destination_project'] ?? '-' }}</li>
                                                                    <li><strong>Last Location:</strong> {{ $activity['details']['last_location'] ?? '-' }}</li>
                                                                </ul>
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