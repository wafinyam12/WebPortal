@extends('layouts.admin', [
    'title' => 'Role & Permission Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Role & Permission Management</h1>
    <p class="mb-4">
        This page is used to manage roles and permissions.
    </p>

    <div class="row">
        <div class="col-lg-6">
            @can('view roles')
            <div class="card shadow mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h4 class="m-0 font-weight-bold text-primary">Role</h4>
                        @can('create roles')
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addRoleModal">
                            <i class="fas fa-user-shield"></i> Add Role
                        </button>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Name</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $role)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $role->name }}</td>
                                            <td>
                                                <div class="text-center d-flex justify-content-center">
                                                    @can('show roles')
                                                    <button type="button" class="btn btn-info mr-1 btn-circle" data-toggle="modal" 
                                                            data-id="{{ $role->id }}" 
                                                            data-name="{{ $role->name }}" 
                                                            data-permissions="{{ $role->permissions->pluck('id')->join(',') }}" 
                                                            data-target="#viewRolePermissionModal">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    @endcan
                                                    @if ($role->name != 'Superadmin')
                                                        @can('update roles')
                                                        <button type="button" class="btn btn-warning mr-1 btn-circle" data-toggle="modal"
                                                            data-id="{{ $role->id }}"
                                                            data-name="{{ $role->name }}" 
                                                            data-target="#editRoleModal">
                                                            <i class="fas fa-pencil-alt"></i>
                                                        </button>
                                                        @endcan
                                                        @can('delete roles')
                                                        <form action="{{ route('roles.destroy', $role->id) }}" method="post" id="deleteRoleForm-{{ $role->id }}" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button" onclick="confirmDeleteRole({{ $role->id }})" class="btn btn-danger btn-circle">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                        @endcan
                                                    @endif
                                                </div>
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
            @endcan

            @can('view permissions')
            <div class="card shadow mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h4 class="m-0 font-weight-bold text-primary">Permission</h4>
                        @can('create permissions')
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPermissionModal">
                            <i class="fas fa-user-lock"></i> Add Permission
                        </button>
                        @endcan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Name</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($permissions as $permission)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $permission->name }}</td>
                                            <td>
                                                <div class="text-center d-flex justify-content-center">
                                                    @can('update permissions')
                                                    <button type="button" class="btn btn-warning mr-2 btn-circle" data-toggle="modal"
                                                        data-id="{{ $permission->id }}"
                                                        data-name="{{ $permission->name }}"
                                                        data-target="#editPermissionModal">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                    @endcan
                                                    @can('delete permissions')
                                                    <form action="{{ route('permissions.destroy', $permission->id) }}" method="post" id="deletePermissionForm-{{ $permission->id }}" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" onclick="confirmPermissionDelete({{ $permission->id }})" class="btn btn-danger btn-circle">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No Data</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endcan
        </div>
        @can('assign roles')
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-gradient-warning text-white d-flex justify-content-center">
                    <h4 class="m-0 font-weight-bold">Assign Role to Permission</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('assign-roles-permissions') }}" method="post" id="assignRoleToPermissionForm">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select name="role_id" id="role" class="form-control @error('role_id') is-invalid @enderror" required>
                                <option value="" disabled selected>Select Role</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" data-permissions="{{ $role->permissions->pluck('id')->join(',') }}">
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
        
                        <div class="form-group">
                            <label for="permissions">Permissions</label>
                            <div class="row" id="permissionCheckboxes">
                                @foreach ($permissions as $permission)
                                    <div class="col-md-6">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input @error('permissions') is-invalid @enderror" type="checkbox" name="permissions[]" id="permission{{ $permission->id }}" value="{{ $permission->id }}">
                                            <label class="form-check-label" for="permission{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <div class="float-right mt-2">
                            <button type="button" class="btn btn-primary" onclick="confirmAssignRoleToPermission()">
                                <i class="fas fa-lock"></i> Assign Permissions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div> 
        @endcan          
    </div>

    <!-- Modal Add Role -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="addRoleModalLabel">Create Role</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('roles.store') }}" method="post" id="addRoleForm">
                        @csrf
                        <div class="form-group">
                            <label for="roleName">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="roleName" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Role Name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" onclick="confirmAddRole()" class="btn btn-primary"> <i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Role -->
    <div class="modal fade" id="editRoleModal" tabindex="-1" aria-labelledby="editRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="editRoleModalLabel">Update Role</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('roles.update', ':id') }}" method="post" id="updateRoleForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="editRoleName">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editRoleName" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Role Name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateRole()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal View Role Permission -->
    <div class="modal fade" id="viewRolePermissionModal" tabindex="-1" aria-labelledby="viewRolePermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="viewRolePermissionModalLabel">View Role Permission</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Role Name</label>
                        <input type="text" name="name" id="nameRoleed" class="form-control" required readonly>
                    </div>
                    <label for="permissions">Permissions</label>
                    <div class="row" id="permissionCheckboxesModal">
                        @foreach ($permissions as $permission)
                            <div class="col-md-6">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" id="permissions{{ $permission->id }}" value="{{ $permission->id }}" readonly>
                                    <label class="form-check-label" for="permissions{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Permission -->
    <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="addPermissionModalLabel">Create Permission</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('permissions.store') }}" method="post" id="addPermissionsForm">
                        @csrf
                        <div class="form-group">
                            <label for="permissionName">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="permissionName" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Permission Name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddPermission()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit Permission -->
    <div class="modal fade" id="editPermissionModal" tabindex="-1" aria-labelledby="editPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="editPermissionModalLabel">Update Permission</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('permissions.update', ':id') }}" method="post" id="updatePermissionForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="editPermissionName">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="editPermissionName" class="form-control @error('name') is-invalid @enderror" placeholder="Enter Permission Name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdatePermission()"><i class="fas fa-check"></i> Save changes</button>
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

    // Pastikan script ini dijalankan setelah halaman selesai dimuat
    document.addEventListener("DOMContentLoaded", function () {
        // Pilih semua tombol yang akan membuka modal
        const modalButtons = document.querySelectorAll('button[data-toggle="modal"][data-target="#viewRolePermissionModal"]');
        const modalNameInput = document.getElementById('nameRoleed');
        const permissionCheckboxes = document.querySelectorAll('#permissionCheckboxesModal input[type="checkbox"]');

        // Fungsi untuk mengisi modal
        function populateModal(button) {
            // Ambil data dari tombol
            const roleName = button.getAttribute('data-name');
            const rolePermissions = button.getAttribute('data-permissions').split(',');            

            // Set nama role di modal
            modalNameInput.value = roleName;

            // Reset semua checkbox sebelum mengisi kembali
            permissionCheckboxes.forEach(checkbox => checkbox.checked = false);

            // Centang checkbox yang sesuai dengan permissions
            rolePermissions.forEach(permissionId => {
                const checkbox = document.getElementById(`permissions${permissionId}`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }

        // Menambahkan event listener pada setiap tombol
        modalButtons.forEach(button => {
            button.addEventListener('click', function () {
                populateModal(this); // Panggil fungsi untuk mengisi modal dengan data
            });
        });
    });

    // Update checkbox permissions when role changes
    function updateCheckboxPermissions(roleSelectId, checkboxContainerId) {
        const roleSelect = document.getElementById(roleSelectId);
        const checkboxContainer = document.getElementById(checkboxContainerId);

        roleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const permissions = selectedOption.getAttribute('data-permissions');

            const checkboxes = checkboxContainer.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            if (permissions) {
                const permissionIds = permissions.split(',');
                permissionIds.forEach(id => {
                    const checkbox = document.getElementById('permission' + id);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
        });
    }

    updateCheckboxPermissions('role', 'permissionCheckboxes');

    function confirmAddRole(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to create this role!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addRoleForm').submit();
            } else {
                return false;
            }
        })
    }

    $('#editRoleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var roleId = button.data('id')
        var roleName = button.data('name');

        var modal = $(this);
        document.getElementById('editRoleName').value = roleName;
        
        var formAction = '{{ route("roles.update", ":id") }}';
        formAction = formAction.replace(':id', roleId);
        modal.find('#updateRoleForm').attr('action', formAction);
    });
    
    function confirmUpdateRole(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this role!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateRoleForm').submit();
            } else {
                return false;
            }
        })
    }

    function confirmDeleteRole(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this role!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteRoleForm-' + id).submit();
            } else {
                return false;
            }
        })
    }

    function confirmAddPermission() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to create this permission!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('addPermissionsForm').submit();
            }else {
                return false;
            }
        });
    }

    
    $('#editPermissionModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var permissionId = button.data('id')
        var permissionName = button.data('name');

        var modal = $(this);
        document.getElementById('editPermissionName').value = permissionName;
        
        var formAction = '{{ route("permissions.update", ":id") }}';
        formAction = formAction.replace(':id', permissionId);
        modal.find('#updatePermissionForm').attr('action', formAction);
    });

    function confirmUpdatePermission(){
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this permission!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('updatePermissionForm').submit();
            } else {
                return false;
            }
        })
    }

    function confirmPermissionDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this permission!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deletePermissionForm-' + id).submit();
            }
        })
    }

    function confirmAssignRoleToPermission() {
        // Check if role is selected
        const roleSelect = document.getElementById('role');
        if (!roleSelect.value) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select a role first!'
            });
            return;
        }

        // Check if at least one permission is selected
        const permissions = document.querySelectorAll('input[name="permissions[]"]:checked');
        if (permissions.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select at least one permission!'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to assign these permissions to the selected role!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Assign it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('assignRoleToPermissionForm').submit();
            }
        });
    }
</script>
@endpush