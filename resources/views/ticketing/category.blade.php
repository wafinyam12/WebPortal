@extends('layouts.admin',[
    'title' => 'Ticket Category'
])

@push('css')
    <!-- Custom styles for this page -->
   <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ __('Ticket Category') }}</h1>
    <p class="mb-4">
        This page is used to manage ticket category.
    </p>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary d-flex justify-content-center">
            <h4 class="m-0 font-weight-bold text-white">{{ __('Create Ticket Category & List Ticket Category') }}</h4>
        </div>
        <div class="card-body">
            @can('create ticketing categories')
            <form action="{{ route('ticketing-categories.store') }}" method="POST" id="addTicketCategoryForm">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Category Name</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Category Name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" name="description" id="description" placeholder="Description (optional)">
                        </div>
                    </div>
                </div>
            </form>
            <div class="float-right my-3">
                <button type="button" class="btn btn-primary" onclick="confirmAddTicketCategory()">
                    <i class="fas fa-receipt"></i> Save
                </button>
            </div>
            @endcan
            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th class="text-center" width="15%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->description }}</td>
                                <td class="text-center">
                                    <div class="d-inline-flex">
                                        @can('update ticketing categories')
                                        <button type="button" class="btn btn-warning btn-circle mr-2" 
                                            data-toggle="modal"
                                            data-target="#updateModal"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-description="{{ $category->description }}">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        @endcan
                                        @can('delete ticketing categories')
                                        <form action="{{ route('ticketing-categories.destroy', $category->id) }}" method="POST" class="d-inline" id="deleteTicketCategoryForm-{{ $category->id }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteTicketCategory({{ $category->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan                                    
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Update Ticket Category -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold" id="exampleModalLabel">Update Ticket Category</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ticketing-categories.update', ':id') }}" method="POST" id="updateTicketCategoryForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" name="name" id="nameUpdate" placeholder="Category Name">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <input type="text" class="form-control" name="description" id="descriptionUpdate" placeholder="Description">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="submit" class="btn btn-primary" onclick="confirmUpdateTicketCategory()"><i class="fas fa-check"></i> Save changes</button>
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

        function confirmAddTicketCategory() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to save this category data!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('addTicketCategoryForm').submit();
                }
            });
        }

        $('#updateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var description = button.data('description');

            var modal = $(this);
            modal.find('.modal-body #id').val(id);
            modal.find('.modal-body #nameUpdate').val(name);
            modal.find('.modal-body #descriptionUpdate').val(description);

            // replace action attribute
            var action = $('#updateTicketCategoryForm').attr('action');
            var newAction = action.replace(':id', id);
            $('#updateTicketCategoryForm').attr('action', newAction);
        })

        function confirmUpdateTicketCategory() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to update this category data!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('updateTicketCategoryForm').submit();
                }
            });
        }

        function confirmDeleteTicketCategory(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this category data!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteTicketCategoryForm-' + id).submit();
                }
            });
        }
    </script>
@endpush