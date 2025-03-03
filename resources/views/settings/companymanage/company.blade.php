@extends('layouts.admin', [
    'title' => 'Company Management'
])

@push('css')
   <!-- Custom styles for this page -->
   <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
                    
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Company Management</h1>
    <p class="mb-4">
        This page is used to manage company.
    </p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Companies</h4>
                <div class="d-flex align-items-center flex-wrap">
                    <!-- Tombol Add Users -->
                    @can('create companies')
                    <button type="button" class="btn btn-primary btn-md ml-2 mb-2" data-toggle="modal" data-target="#addCompaniesModal">
                        <i class="fas fa-building fa-md white-50"></i> Add Companies
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
                                <th>Address</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($companies as $company)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $company->code }}</td>
                                    <td>{{ $company->name }}</td>
                                    <td>{{ $company->address }}</td>
                                    <td class="text-center d-flex">
                                        @can('show companies')
                                        <a href="{{ route('companies.show', $company->id) }}" class="btn btn-info mr-1 btn-circle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        @can('update companies')
                                        <button class="btn btn-warning mr-1 btn-circle"
                                            data-toggle="modal"
                                            data-id="{{ $company->id }}"
                                            data-name="{{ $company->name }}"
                                            data-company="{{ $company->company }}"
                                            data-password="{{ $company->password }}"
                                            data-email="{{ $company->email }}"
                                            data-phone="{{ $company->phone }}"
                                            data-address="{{ $company->address }}"
                                            data-description="{{ $company->description }}"
                                            data-target="#editCompaniesModal">
                                            <i class="fas fa-pencil"></i>
                                        </button>
                                        @endcan
                                        @can('delete companies')
                                        <form action="{{ route('companies.destroy', $company->id) }}" method="POST" id="deleteCompaniesForm-{{ $company->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-circle" onclick="deleteCompanies({{ $company->id }})" ><i class="fas fa-trash"></i></button>
                                        </form>
                                        @endcan
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

    <!-- Modal Add Companies -->
    <div class="modal fade" id="addCompaniesModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex align-items-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="addModalLabel">Create Companies</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('companies.store') }}" method="POST" id="addCompaniesForm">
                        @csrf
                        <div class="form-group">
                            <label for="name">Company <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" placeholder="Company" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="number" name="phone" id="phone" placeholder="Phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" cols="30" rows="4" placeholder="Address (optional)" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="4" placeholder="Description (optional)"></textarea>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmAddCompanies()"><i class="fas fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Companies -->
    <div class="modal fade" id="editCompaniesModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex align-items-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold mx-auto" id="editModalLabel">Update Companies</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('companies.update', ':id') }}" method="POST" id="editCompaniesForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Company <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" placeholder="Company" class="form-control @error('name') is-invalid @enderror">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="number" name="phone" id="phone" placeholder="Phone" class="form-control @error('phone') is-invalid @enderror">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" cols="30" rows="4" placeholder="Address" class="form-control @error('address') is-invalid @enderror"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" cols="30" rows="4" placeholder="Description (optional)" class="form-control @error('description') is-invalid @enderror"></textarea>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="submit" class="btn btn-primary" onclick="confirmUpdateCompanies()"><i class="fas fa-check"></i> Save changes</button>
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

    function confirmAddCompanies() {
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
                $('#addCompaniesForm').submit();
            }
        })
    }

    $('#editCompaniesModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var company = button.data('company');
        var name = button.data('name');
        var password = button.data('password');
        var email = button.data('email');
        var phone = button.data('phone');
        var address = button.data('address');
        var description = button.data('description');

        var modal = $(this);
        modal.find('#id').val(id);
        modal.find('#company').val(company);
        modal.find('#name').val(name);
        modal.find('#password').val(password);
        modal.find('#email').val(email);
        modal.find('#phone').val(phone);
        modal.find('#address').val(address);
        modal.find('#description').val(description);

        // Ubah action form agar sesuai dengan id yang akan diupdate
        var formAction = '{{ route("companies.update", ":id") }}';
        formAction = formAction.replace(':id', id);
        $('#editCompaniesForm').attr('action', formAction);
    });
    

    function confirmUpdateCompanies() {
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
                $('#editCompaniesForm').submit();
            }
        })
    }

    function deleteCompanies(id) {
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
                document.getElementById('deleteCompaniesForm-' + id).submit();
            }
        })
    }
</script>
@endpush