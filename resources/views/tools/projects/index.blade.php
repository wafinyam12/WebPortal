@extends('layouts.admin', [
    'title' => 'Projects Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ __('Projects Management') }}</h1>
    <p class="mb-4">
        This page is used to manage projects.
    </p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary d-flex justify-content-center">
            <h4 class="m-0 font-weight-bold text-white">Create Project</h4>
        </div>
        <div class="card-body">
            @can('create projects')
            <form action="{{ route('projects.store') }}" method="post" id="addProjectForm">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter name">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Enter phone">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="email">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email">
                        </div>
                        <div class="form-group">
                            <label for="ppic">PPIC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ppic') is-invalid @enderror" id="ppic" name="ppic" placeholder="Enter ppic">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="address">Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address @error('address') is-invalid @enderror" class="form-control" cols="30" rows="5" placeholder="Enter address"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="5" placeholder="Enter description (optional)"></textarea>
                        </div>
                    </div> 
                </div>
            </form>
            <div class="float-right mb-4">
                <button type="button" class="btn btn-primary" onclick="confirmAddProject()">
                    <i class="fas fa-check"></i> Save Project
                </button>
            </div>
            @endcan
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>PPIC</th>
                            <th>Address</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($projects as $project)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->phone }}</td>
                                <td>{{ $project->email }}</td>
                                <td>{{ $project->ppic }}</td>
                                <td>{{ $project->address }}</td>
                                <td class="text-center d-flex justify-content-center">
                                    @can('update projects')
                                    <button type="button" class="btn btn-warning btn-circle mr-2" data-toggle="modal"
                                        data-id="{{ $project->id }}"
                                        data-name="{{ $project->name }}"
                                        data-phone="{{ $project->phone }}"
                                        data-email="{{ $project->email }}"
                                        data-ppic="{{ $project->ppic }}"
                                        data-address="{{ $project->address }}"
                                        data-description="{{ $project->description }}"
                                        data-target="#updateProjectModal">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    @endcan
                                    @can('delete projects')
                                    <form action="{{ route('projects.destroy', $project->id) }}" method="post" id="deleteProjectForm{{ $project->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteProject({{ $project->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Update Project -->
    <div class="modal fade" id="updateProjectModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold" id="updateModalLabel">Update Project</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('projects.update', ':id') }}" method="post" id="updateProjectForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" required>
                                </div>
                                <div class="form-group">
                                    <label for="ppic">PPIC <span class="text-danger">*</span></label>
                                    <input type="text" name="ppic" id="ppic" class="form-control @error('ppic') is-invalid @enderror" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" cols="30" rows="5" required placeholder="Enter address"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea name="description" id="description" class="form-control" cols="30" rows="5" placeholder="Enter description (optional)"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmUpdateProject()"><i class="fas fa-check"></i> Save changes</button>
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
    
    function confirmDeleteProject(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this project data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#deleteProjectForm' + id).submit();
            }
        })
    }

    $('#updateProjectModal').on('show.bs.modal', function(e) {
        var button = $(e.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var phone = button.data('phone');
        var email = button.data('email');
        var ppic = button.data('ppic');
        var address = button.data('address');
        var description = button.data('description');

        var modal = $(this);
        modal.find('#name').val(name);
        modal.find('#phone').val(phone);
        modal.find('#email').val(email);
        modal.find('#ppic').val(ppic);
        modal.find('#address').val(address);
        modal.find('#description').val(description);
        modal.find('#updateProjectForm').attr('action', modal.find('#updateProjectForm').attr('action').replace(':id', id));
    });

    function confirmUpdateProject() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this project data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateProjectForm').submit();
            }
        })
    }

    function confirmAddProject() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to save this project data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addProjectForm').submit();
            }
        })
    }
</script>
@endpush