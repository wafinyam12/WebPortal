@extends('layouts.admin', [
    'title' => 'Employee Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
                    
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Employee Management</h1>
    <p class="mb-4">
        This page is used to manage employee.
    </p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Employess</h4>
                <div class="d-flex align-items-center flex-wrap">
                    <!-- Tombol Add Users -->
                    @can('create users')
                    <a href="{{ route('users.index') }}" class="btn btn-primary btn-md ml-2 mb-2">
                        <i class="fas fa-address-card fa-md white-50"></i> Add Employess
                    </a>
                    @endcan
                </div>
            </div> 
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Photo</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Phone</th>
                                <th width="5%">Gander</th>
                                <th width="5%">Age</th>
                                <th width="5%" class="text-center">Status</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($employees as $employee)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <img src="{{ asset('storage/employees/photo/'.$employee->photo) }}" alt="Photo" width="100px" height="100px" class="rounded-circle">
                                    </td>
                                    <td>{{ $employee->code }}</td>
                                    <td>{{ $employee->full_name }}</td>
                                    <td>{{ $employee->position }}</td>
                                    <td>{{ $employee->phone }}</td>
                                    <td class="text-center">{{ $employee->gender }}</td>
                                    <td class="text-center">{{ $employee->age }}</td>
                                    <td class="text-center">{!! $employee->activeUsers !!}</td>
                                    <td class="text-center">
                                        @can('show employees')
                                        <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-info btn-circle"><i class="fas fa-eye"></i></a>
                                        @endcan
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