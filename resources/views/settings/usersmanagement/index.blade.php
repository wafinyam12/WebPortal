@extends('layouts.admin', [
    'title' => 'Users Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Users Management</h1>
    <p class="mb-4">
        This page is used to manage Users All.
    </p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Users</h4>
                <div class="d-flex align-items-center flex-p">
                    <!-- Tombol Import Data -->
                    @can('create users')
                    {{-- <button type="button" class="btn btn-warning btn-md ml-2" data-toggle="modal" data-target="#importModal">
                        <i class="fas fa-file-import fa-md white-50"></i> Import Users
                    </button> --}}
                    <!-- Tombol Add Users -->
                    <button type="button" class="btn btn-primary btn-md ml-2" data-toggle="modal" data-target="#addUsersModal">
                        <i class="fas fa-user-tie fa-md white-50"></i> Add Users
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
                                <th>Full Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->fullName }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td><span class="badge badge-{{ $user->badgeClass }}">{{ $user->getRoleNames()[0] }}</span></td>
                                    <td class="text-center d-flex justify-content-center">
                                        @can('show users')
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info mr-2 btn-circle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('update users')
                                        <button type="button" class="btn btn-warning mr-2 btn-circle"
                                            data-id="{{ $user->id }}"
                                            data-name="{{ $user->name }}"
                                            data-username="{{ $user->username }}"
                                            data-last_name="{{ $user->last_name }}"
                                            data-email="{{ $user->email }}"
                                            data-role="{{ $user->roles->first()->id }}"
                                            data-company_id="{{ $user->employe->company_id }}"
                                            data-department_id="{{ $user->employe->department_id }}"
                                            data-branch_id="{{ $user->employe->branch_id }}"
                                            data-nik="{{ $user->employe->nik }}"
                                            data-full_name="{{ $user->fullName }}"
                                            data-phone="{{ $user->employe->phone }}"
                                            data-gender="{{ $user->employe->gender }}"
                                            data-age="{{ $user->employe->age }}"
                                            data-address="{{ $user->employe->address }}"
                                            data-position="{{ $user->employe->position }}"
                                            data-status="{{ $user->employe->status }}"
                                            data-toggle="modal" data-target="#editUsersModal">
                                            <i class="fas fa-pencil"></i>
                                        </button>
                                        @endcan
                                        @if ($user->id !== auth()->user()->id)
                                            @can('delete users')
                                                <form action="{{ route('users.destroy', $user->id) }}" method="post" id="deleteUsersForm-{{ $user->id }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmUsersDelete({{ $user->id }})" class="btn btn-danger btn-circle">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        @endif
                                    </td>
                                </tr>                        
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No Data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add -->
    <div class="modal fade" id="addUsersModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="addModalLabel">Create Users & Employees</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.store') }}" method="post" id="addUsersForm" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="form-group">
                            <label for="nik">Nik</label>
                            <input type="number" name="nik" id="nik" placeholder="Enter Nik Employee (Optional)" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}">
                        </div> --}}
                        <div class="form-group">
                            <label for="roles">Roles <span class="text-danger">*</span></label>
                            <select name="roles" id="roles" class="form-control @error('roles') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Roles</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name" placeholder="Enter First Name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="name">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="username" placeholder="Enter Username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password" id="password" placeholder="Enter Password" class="form-control @error('password') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="position">Position <span class="text-danger">*</span></label>
                                    <select name="position" id="position" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" required>
                                        <option value="" disabled selected>Select Position</option>
                                        <option value="admin">Admin</option>
                                        <option value="employee">Employee</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="company_id">Company <span class="text-danger">*</span></label>
                                    <select name="company_id" id="company_id" class="form-control @error('company_id') is-invalid @enderror" value="{{ old('company_id') }}" required>
                                        <option value="" disabled selected>Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_id" class="form-control @error('branch_id') is-invalid @enderror" value="{{ old('branch_id') }}" required>
                                        <option value="" disabled selected>Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name" placeholder="Enter Last Name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" placeholder="Enter Email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Enter Confirm Password" class="form-control @error('password') is-invalid @enderror">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="number" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Enter Phone" value="{{ old('phone') }}" required>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="number" name="age" id="age" class="form-control @error('age') is-invalid @enderror" placeholder="Enter Age" value="{{ old('age') }}" required>
                                </div> --}}
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" placeholder="Enter Gender" value="{{ old('gender') }}" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select name="department_id" id="department_id" class="form-control @error('department_id') is-invalid @enderror" value="{{ old('department_id') }}" required>
                                        <option value="" disabled selected>Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <input type="file" name="photo" id="photo" class="form-control @error('photo') is-invalid @enderror" placeholder="Select Photo" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" cols="30" rows="4" placeholder="Address (optional)" value="{{ old('address') }}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUsersAdd()"><i class="fas fa-check white-50"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editUsersModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="editModalLabel">Update Users</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('users.update', ':id') }}" method="post" id="editUsersForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="statusEdit" class="form-control @error('status') is-invalid @enderror" value="{{ old('status') }}" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="roles">Roles <span class="text-danger">*</span></label>
                            <select name="roles" id="rolesEdit" class="form-control @error('roles') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Roles</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        {{-- <div class="form-group">
                            <label for="nik">Nik</label>
                            <input type="text" name="nik" id="nikEdit" placeholder="Enter Nik Employee (Optional)" class="form-control @error('nik') is-invalid @enderror" value="{{ old('nik') }}" required>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_nameEdit" placeholder="Enter First Name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="name">Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" id="usernameEdit" placeholder="Enter Username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" name="password" id="password" placeholder="Enter Password" class="form-control @error('password') is-invalid @enderror">
                                </div>
                                <div class="form-group">
                                    <label for="position">Position <span class="text-danger">*</span></label>
                                    <select name="position" id="positionEdit" class="form-control @error('position') is-invalid @enderror" value="{{ old('position') }}" required>
                                        <option value="" disabled selected>Select Position</option>
                                        <option value="admin">Admin</option>
                                        <option value="employee">Employee</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="company_id">Company <span class="text-danger">*</span></label>
                                    <select name="company_id" id="company_idEdit" class="form-control @error('company_id') is-invalid @enderror" value="{{ old('company_id') }}" required>
                                        <option value="" disabled selected>Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" id="branch_idEdit" class="form-control @error('branch_id') is-invalid @enderror" value="{{ old('branch_id') }}" required>
                                        <option value="" disabled selected>Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_nameEdit" placeholder="Enter Last Name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="emailEdit" placeholder="Enter Email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                </div>
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Enter Confirm Password" class="form-control @error('password') is-invalid @enderror">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="number" name="phone" id="phoneEdit" placeholder="Enter Phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                                </div>
                                {{-- <div class="form-group">
                                    <label for="age">Age</label>
                                    <input type="number" name="age" id="ageEdit" placeholder="Enter Age" class="form-control @error('age') is-invalid @enderror" value="{{ old('age') }}" required>
                                </div> --}}
                                <div class="form-group">
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                    <select name="gender" id="genderEdit" class="form-control @error('gender') is-invalid @enderror" value="{{ old('gender') }}" required>
                                        <option value="" disabled selected>Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="department_id">Department <span class="text-danger">*</span></label>
                                    <select name="department_id" id="department_idEdit" class="form-control @error('department_id') is-invalid @enderror" value="{{ old('department_id') }}" required>
                                        <option value="" disabled selected>Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="photo">Photo</label>
                            <input type="file" name="photo" id="photoEdit" class="form-control" placeholder="Select Photo" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="addressEdit" class="form-control" cols="30" rows="4"></textarea>
                        </div>                     
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUsersUpdate()"><i class="fas fa-check white-50"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="importModalLabel">Import Users</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data" id="importUsresForm">
                        @csrf
                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required accept=".xlsx, .xls, .csv">
                            <p class="text-danger">*Format file .xlsx .xls .csv</p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmImportUsers()"><i class="fas fa-file-import white-50"></i> Save</button>
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

    function printPDF() {
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;
        if(startDate && endDate) {
            $.ajax({
                url: "",
                method: "GET",
                data: {
                    'start_date': startDate,
                    'end_date': endDate
                },
                success: function(data) {
                    window.open(data, '_blank');
                }
            })
        }
    }

    function printExcel() {
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;
        if(startDate && endDate) {
            $.ajax({
                url: "",
                method: "GET",
                data: {
                    'start_date': startDate,
                    'end_date': endDate
                },
                success: function(data) {
                    window.open(data, '_blank');
                }
            })
        }
    }

    function confirmUsersAdd() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to create this user!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addUsersForm').submit();
            } else {
                return false;
            }
        })
    }

    $('#editUsersModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('id')
        var userName = button.data('name');
        var usersName = button.data('username');
        var userLastName = button.data('last_name');
        var userEmail = button.data('email');
        var userRoles = button.data('role');
        var userCompany = button.data('company_id');
        var userDepartment = button.data('department_id');
        var userBranch = button.data('branch_id');
        var userNik = button.data('nik');
        var userFullName = button.data('full_name');
        var userPhone = button.data('phone');
        var userGender = button.data('gender');
        var userAge = button.data('age');
        var userAddress = button.data('address');
        var userPosition = button.data('position');
        var userStatus = button.data('status');
       
        var modal = $(this);
        modal.find('#first_nameEdit').val(userName);
        modal.find('#usernameEdit').val(usersName);
        modal.find('#last_nameEdit').val(userLastName);
        modal.find('#emailEdit').val(userEmail);
        modal.find('#rolesEdit').val(userRoles).trigger('change');
        modal.find('#company_idEdit').val(userCompany);
        modal.find('#department_idEdit').val(userDepartment);
        modal.find('#branch_idEdit').val(userBranch);
        modal.find('#nikEdit').val(userNik);
        modal.find('#full_nameEdit').val(userFullName);
        modal.find('#phoneEdit').val(userPhone);
        modal.find('#genderEdit').val(userGender).trigger('change');
        modal.find('#ageEdit').val(userAge);
        modal.find('#addressEdit').val(userAddress);
        modal.find('#positionEdit').val(userPosition);
        modal.find('#statusEdit').val(userStatus).trigger('change');

        // Ubah action form agar sesuai dengan id yang akan diupdate
        var formAction = '{{ route("users.update", ":id") }}';
        formAction = formAction.replace(':id', userId);
        $('#editUsersForm').attr('action', formAction);
    })
    
    function confirmUsersUpdate() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this user!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#editUsersForm').submit();
            } else {
                return false;
        }})
    }

    function confirmUsersDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this user!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteUsersForm-' + id).submit();
            } else {
                return false;
            }
        })
    }

    function confirmImportUsers() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to import this user!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Import it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#importUsresForm').submit();
            } else {
                return false;
            }
        })
    }
</script>
@endpush