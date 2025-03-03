@extends('layouts.admin', [
    'title' => 'User Details'
])

@push('css')

@endpush

@section('main-content')

<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">User Details</h1>
<p class="mb-4">
    This page is used to show user.
</p>

<!-- Card Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h4 class="m-0 font-weight-bold text-primary">User Details</h4>
        <a href="{{ route('users.index') }}" class="btn btn-primary btn-md mr-2"><i class="fas fa-reply"></i> Back</a>
    </div>
    <div class="card-body">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    @if($users->employe->photo != null)
                        <img src="{{ asset('storage/employees/photo/'.$users->employe->photo) }}" alt="Profile Photo" class="img-fluid rounded-circle" style="max-width: 200px;">
                    @else
                        <img src="https://www.w3schools.com/w3css/img_avatar3.png" alt="Default Profile" class="img-fluid rounded-circle" style="max-width: 200px;">
                    @endif
                </div>
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Full Name</th>
                                <td>{{ $users->employe->full_name }}</td>
                            </tr>
                            <tr>
                                <th>Username</th>
                                <td>{{ $users->username }}</td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td>{{ $users->email }}</td>
                            </tr>
                            <tr>
                                <th>Role</th>
                                <td>
                                    @foreach($users->roles as $role)
                                        <span class="badge badge-{{ $users->badgeClass }}">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>Employee Code</th>
                                <td>{{ $users->employe->code }}</td>
                            </tr>
                            <tr>
                                <th>NIK</th>
                                <td>{{ $users->employe->nik }}</td>
                            </tr>
                            <tr>
                                <th>Gender</th>
                                <td>{{ $users->employe->gender }}</td>
                            </tr>
                            <tr>
                                <th>Age</th>
                                <td>{{ $users->employe->age }} Years</td>
                            </tr>
                            <tr>
                                <th>Phone</th>
                                <td>{{ $users->employe->phone }}</td>
                            </tr>
                            <tr>
                                <th>Position</th>
                                <td>{{ $users->employe->position }}</td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>{!! $users->employe->activeUsers !!}</td>
                            </tr>
                        </table>
                    </div>
    
                    <h5 class="font-weight-bold mt-4 mb-3">Company Information</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th width="30%">Company</th>
                                <td>{{ $users->employe->company->name }}</td>
                            </tr>
                            <tr>
                                <th>Branch</th>
                                <td>{{ $users->employe->branch->name }} {!! $users->employe->branch->activeBranch !!}</td>
                            </tr>
                            <tr>
                                <th>Department</th>
                                <td>{{ $users->employe->department->name }}</td>
                            </tr>
                            <tr>
                                <th>Address</th>
                                <td>{{ $users->employe->address }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush