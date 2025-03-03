@extends('layouts.admin', [
    'title' => 'Incoming Supplier Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Incoming Supplier Management</h1>
<p class="mb-4">
    This page is used to manage incoming supplier.
</p>

<!-- List Incoming Inventory -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">List Incoming Supplier</h4>
            @can('create incoming suppliers')
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSupplierModal">
                <i class="fas fa-truck-arrow-right"></i>
                Add Supplier
            </button>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Name Supplier</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th class="text-center" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $supplier->name }}</td>
                                <td>{{ $supplier->phone }}</td>
                                <td>{{ $supplier->email }}</td>
                                <td>{{ $supplier->address }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex">
                                        @can('show incoming suppliers')
                                        <a href="{{ route('incomings-supplier.show', $supplier->id) }}" class="btn btn-info btn-circle mr-2">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('update incoming suppliers')
                                        <button class="btn btn-warning btn-circle mr-2"
                                            data-toggle="modal"
                                            data-id="{{ $supplier->id }}"
                                            data-name="{{ $supplier->name }}"
                                            data-phone="{{ $supplier->phone }}"
                                            data-email="{{ $supplier->email }}"
                                            data-address="{{ $supplier->address }}"
                                            data-target="#editSupplierModal">
                                            <i class="fas fa-pencil"></i>
                                        </button>
                                        @endcan
                                        @can('delete incoming suppliers')
                                        <form action="{{ route('incomings-supplier.destroy', $supplier->id) }}" method="POST" id="deleteSupplierForm-{{ $supplier->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteSupplier({{ $supplier->id }})">
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

<!-- Add Supplier Modal-->
<div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                <h4 class="modal-title text-white font-weight-bold mx-auto" id="addModalLabel">Create Supplier</h4>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('incomings-supplier.store') }}" method="post" id="addSupplierForm">
                    @csrf
                    <div class="form-group">
                        <label for="name">Name Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name Supplier" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone Supplier" value="{{ old('phone') }}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email Supplier</label>
                        <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Supplier" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label for="address">Addres Supplier</label>
                        <textarea name="address" id="address" cols="30" rows="4" class="form-control @error('address') is-invalid @enderror" placeholder="Enter Address Supplier (Optional)" value="{{ old('address') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                <button type="submit" class="btn btn-primary" onclick="confirmAddSupplier()"><i class="fas fa-check"></i> Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                <h4 class="modal-title text-white font-weight-bold mx-auto" id="addModalLabel">Update Supplier</h4>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('incomings-supplier.update', ':id') }}" method="post" id="updateSupplierForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name">Name Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name Supplier" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Supplier <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Phone Supplier" value="{{ old('phone') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Supplier</label>
                        <input type="text" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email Supplier" value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label for="address">Addres Supplier</label>
                        <textarea name="address" id="address" cols="30" rows="4" class="form-control @error('address') is-invalid @enderror" placeholder="Enter Address Supplier (Optional)" value="{{ old('address') }}"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                <button type="submit" class="btn btn-primary" onclick="confirmUpdateSupplier()"><i class="fas fa-check"></i> Save changes</button>
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

    function confirmAddSupplier() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want create this supplier!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Create it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addSupplierForm').submit();
            }
        })
    }

    $('#editSupplierModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var supplierId = button.data('id');
        var supplierName = button.data('name');
        var supplierPhone = button.data('phone');
        var supplierEmail = button.data('email');
        var supplierAddress = button.data('address');

        var modal = $(this);
        modal.find('#name').val(supplierName);
        modal.find('#phone').val(supplierPhone);
        modal.find('#email').val(supplierEmail);
        modal.find('#address').val(supplierAddress);

        // Ubah action form agar sesuai dengan id yang akan diupdate
        var formAction = '{{ route("incomings-supplier.update", ":id") }}';
        formAction = formAction.replace(':id', supplierId); 
        $('#updateSupplierForm').attr('action', formAction);
    });
    
    function confirmUpdateSupplier() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want update this supplier!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateSupplierForm').submit();
            }
        })
    }

    function confirmDeleteSupplier(supplierId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteSupplierForm-' + supplierId).submit();
            }
        })
    }
</script>
@endpush