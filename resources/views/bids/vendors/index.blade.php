@extends('layouts.admin', [
    'title' => 'Bids Vendors Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Bids Vendors Management</h1>
<p class="mb-4">
    This page is used to manage bids vendors.
</p>

<!-- List Bids Vendors -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-gradient-primary d-flex justify-content-center">
        <h4 class="m-0 font-weight-bold text-white">{{ __('Create Bids Vendors & List Bids Vendors') }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('bids-vendors.store') }}" method="POST" id="addBidsVendorsForm">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="category_name">Name</label>
                        <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Category Name">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" placeholder="Phone">
                </div>
                <div class="col-md-4">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email">
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea name="address" class="form-control" id="address" cols="30" rows="4" placeholder="Address (optional)"></textarea>
                    </div>
                </div>
            </div>
        </form>
        <div class="float-right my-3">
            <button type="button" class="btn btn-primary" onclick="confirmAddTicketCategory()">
                <i class="fas fa-check"></i> Save
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th class="text-center" width="15%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vendor->name }}</td>
                        <td>{{ $vendor->phone }}</td>
                        <td>{{ $vendor->email }}</td>
                        <td>{{ $vendor->address }}</td>
                        <td class="text-center">
                            <div class="d-inline-flex">
                                <a href="{{ route('bids-vendors.edit', $vendor->id) }}" class="btn btn-warning btn-circle mr-1">
                                    <i class="fas fa-pencil"></i>
                                </a>
                                <form action="{{ route('bids-vendors.destroy', $vendor->id) }}" method="post" id="destroyTicketCategoryForm-{{ $vendor->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-circle" onclick="confirmDestroyTicketCategory({{ $vendor->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Data Not Found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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