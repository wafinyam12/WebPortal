@extends('layouts.admin', [
    'title' => 'Warehouse Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
                    
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Warehouse Management</h1>
    <p class="mb-4">
        This page is used to manage warehouse.
    </p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Warehouses</h4>
                <div class="d-flex align-items-center flex-wrap">
                    <!-- Tombol Add Users -->
                    @can('create warehouses')
                    <button type="button" class="btn btn-primary btn-md ml-2 mb-2" data-toggle="modal" data-target="#addWarehousesModal">
                        <i class="fas fa-warehouse fa-md white-50"></i> Add Warehouse
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
                                <th>Warehouse</th>
                                <th width="15%" class="text-center">Type</th>
                                <th width="10%" class="text-center">Status</th>
                                <th width="15%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($warehouses as $warehouse)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $warehouse->code }}</td>
                                    <td>{{ $warehouse->name }}</td>
                                    <td class="text-center">{!! $warehouse->typeName !!}</td>
                                    <td class="text-center">{!! $warehouse->statusName !!}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            @can('show warehouses')
                                            <a href="{{ route('warehouses.show', $warehouse->id) }}" class="btn btn-info mr-1 btn-circle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('update warehouses')
                                            <button class="btn btn-warning mr-1 btn-circle"
                                                data-toggle="modal"
                                                data-id="{{ $warehouse->id }}"
                                                data-companies="{{ $warehouse->company_id }}"
                                                data-branch="{{ $warehouse->branch_id }}"
                                                data-name="{{ $warehouse->name }}"
                                                data-email="{{ $warehouse->email }}"
                                                data-phone="{{ $warehouse->phone }}"
                                                data-address="{{ $warehouse->address }}"
                                                data-description="{{ $warehouse->description }}"
                                                data-type="{{ $warehouse->type }}"
                                                data-status="{{ $warehouse->status }}"
                                                data-target="#editWarehouseModal">
                                                <i class="fas fa-pencil"></i>
                                            </button>
                                            @endcan
                                            @can('delete warehouses')
                                            <form action="{{ route('warehouses.destroy', $warehouse->id) }}" method="POST" id="deleteWarehouseForm-{{ $warehouse->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteWarehouse({{ $warehouse->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Warehouse Modal-->
    <div class="modal fade" id="addWarehousesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="exampleModalLabel">Create Warehouse</h4>
                    <button class="close position-absolute" type="button" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('warehouses.store') }}" method="post" id="addWarehouseForm">
                        @csrf
                        <div class="form-group">
                            <label for="type">Type Warehouse <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" id="type" required value="{{ old('type') }}">
                                <option value="" disabled selected>Select Type Warehouse</option>
                                <option value="Warehouse">Warehouse</option>
                                <option value="Raw Material">Raw Material</option>
                                <option value="Finished Goods">Finished Goods</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_id">Company <span class="text-danger">*</span></label>
                                    <select name="company_id" class="form-control @error('company_id') is-invalid @enderror" id="company_id" required value="{{ old('company_id') }}">
                                        <option value="" disabled selected>Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" id="branch_id" required value="{{ old('branch_id') }}">
                                        <option value="" disabled selected>Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Name Warehouse <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name Warehouse" value="{{ old('name') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone" value="{{ old('phone') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" cols="30" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Address (optional)" value="{{ old('address') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" cols="30" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Description (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddWarehouse()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Warehouse Modal-->
    <div class="modal fade" id="editWarehouseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="exampleModalLabel">Update Warehouse</h4>
                    <button class="close position-absolute" type="button" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('warehouses.update', ':id') }}" method="post" id="editWarehouseForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status') }}" required>
                                <option value="" disabled selected>Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type Warehouse <span class="text-danger">*</span></label>
                            <select name="type" class="form-control @error('type') is-invalid @enderror" id="type" value="{{ old('type') }}" required>
                                <option value="" disabled selected>Select Type Warehouse</option>
                                <option value="Warehouse">Warehouse</option>
                                <option value="Raw Material">Raw Material</option>
                                <option value="Finished Goods">Finished Goods</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_id">Company <span class="text-danger">*</span></label>
                                    <select name="company_id" class="form-control @error('company_id') is-invalid @enderror" id="company_id" value="{{ old('company_id') }}" required>
                                        <option value="" disabled selected>Select Company</option>
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="branch_id">Branch <span class="text-danger">*</span></label>
                                    <select name="branch_id" class="form-control @error('branch_id') is-invalid @enderror" id="branch_id" value="{{ old('branch_id') }}" required>
                                        <option value="" disabled selected>Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name">Name Warehouse <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name Warehouse" value="{{ old('name') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone" value="{{ old('phone') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" cols="30" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Address" value="{{ old('address') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" cols="30" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Description (optional)" ></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateWarehouse()"><i class="fas fa-check"></i> Save changes</button>
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

    function confirmAddWarehouse() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to create this warehouse!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addWarehouseForm').submit();
            }
        });
    }

    $('#editWarehouseModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var company_id = button.data('companies');
        var branch_id = button.data('branch');
        var name = button.data('name');
        var phone = button.data('phone');
        var email = button.data('email');
        var address = button.data('address');
        var description = button.data('description');
        var type = button.data('type');
        var status = button.data('status');

        var modal = $(this);
        modal.find('#id').val(id);
        modal.find('#company_id').val(company_id).trigger('change');
        modal.find('#branch_id').val(branch_id).trigger('change');
        modal.find('#name').val(name);
        modal.find('#phone').val(phone);
        modal.find('#email').val(email);
        modal.find('#description').val(description);
        modal.find('#address').val(address);
        modal.find('#type').val(type).trigger('change');
        modal.find('#status').val(status).trigger('change');

        // Ubah action form agar sesuai dengan id yang akan diupdate
        var formAction = '{{ route("warehouses.update", ":id") }}';
        formAction = formAction.replace(':id', id);
        $('#editWarehouseForm').attr('action', formAction);
    });
    
    function confirmUpdateWarehouse() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this warehouse!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#editWarehouseForm').submit();
            }
        });
    }
    
    function confirmDeleteWarehouse(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this warehouse!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteWarehouseForm-' + id).submit();
            }
        });
    }
</script>
@endpush