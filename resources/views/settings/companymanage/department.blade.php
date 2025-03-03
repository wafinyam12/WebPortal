@extends('layouts.admin', [
    'title' => 'Department Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
                    
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Department Management</h1>
    <p class="mb-4">
        This page is used to manage department.
    </p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Department</h4>
                <div class="d-flex align-items-center flex-wrap">
                    <!-- Tombol Add Users -->
                    @can('create departments')
                    <button type="button" class="btn btn-primary btn-md ml-2 mb-2" data-toggle="modal" data-target="#addDepartmentsModal">
                        <i class="fas fa-building-user fa-md white-50"></i> Add Department
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
                                <th>Name</th>
                                <th>Description</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($departments as $department)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $department->code }}</td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->description }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            @can('show departments')
                                            <a href="{{ route('departments.show', $department->id) }}" class="btn btn-info btn-circle mr-1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('update departments')
                                            <button type="button" class="btn btn-warning btn-circle mr-1"
                                                data-toggle="modal"
                                                data-id="{{ $department->id }}"
                                                data-company_id="{{ $department->company_id }}"
                                                data-name="{{ $department->name }}"
                                                data-description="{{ $department->description }}"
                                                data-target="#editDepartmentsModal">
                                                <i class="fas fa-pencil"></i>
                                            </button>
                                            @endcan
                                            @can('delete departments')
                                            <form action="{{ route('departments.destroy', $department->id) }}" method="POST" id="deleteDepartmentsForm-{{ $department->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteDepartment({{ $department->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td> 
                                </tr>                               
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Departments Modal-->
    <div class="modal fade" id="addDepartmentsModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="addModalLabel">Create Departments</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('departments.store') }}" method="POST" id="addDepartmentsForm">
                        @csrf
                        <div class="form-group">
                            <label for="company_id">Company <span class="text-danger">*</span></label>
                            <select name="company_id" class="form-control @error('company_id') is-invalid @enderror" id="company_id" value="{{ old('company_id') }}" required>
                                <option value="" disabled selected>Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Description (optional)"></textarea>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmAddDepartments()"><i class="fas fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Departments Modal-->
    <div class="modal fade" id="editDepartmentsModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="editModalLabel">Update Departments</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('departments.update', ':id') }}" method="POST" id="editDepartmentsForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="company_id">Company <span class="text-danger">*</span></label>
                            <select name="company_id" class="form-control @error('company_id') is-invalid @enderror" id="company_id" value="{{ old('company_id') }}" required>
                                <option value="" disabled selected>Select Company</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="{{ old('name') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" placeholder="Description (optional)"></textarea>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmUpdateDepartments()"><i class="fas fa-check"></i> Save changes</button>
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

    function confirmAddDepartments() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, add it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addDepartmentsForm').submit();
            }
        })
    }

    $('#editDepartmentsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var company_id = button.data('company_id');
        var name = button.data('name');
        var description = button.data('description');

        var modal = $(this);
        modal.find('#id').val(id);
        modal.find('#company_id').val(company_id).trigger('change');
        modal.find('#name').val(name);
        modal.find('#description').val(description);

        // Ubah action form agar sesuai dengan id yang akan diupdate
        var formAction = '{{ route("departments.update", ":id") }}';
        formAction = formAction.replace(':id', id);
        $('#editDepartmentsForm').attr('action', formAction);
    });

    function confirmUpdateDepartments() {
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
                $('#editDepartmentsForm').submit();
            }
        })
    }

    function confirmDeleteDepartment(id) {
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
                $('#deleteDepartmentsForm-' + id).submit();
            }
        })
    }
</script>
@endpush