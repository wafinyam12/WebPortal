@extends('layouts.admin',[
    'title' => 'Create Ticket'
])

@push('css')
   <!-- Custom styles for this page -->
   <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
   <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@section('main-content')                 
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Create Tickets</h1>
    <p class="mb-4">
        This page is used to create tickets. Tickets are used to track issues and problems with the system.
    </p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary d-flex justify-content-center">
            <h4 class="m-0 font-weight-bold text-white text-center">Create Tickets</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <form action="{{ route('ticketing.store') }}" method="POST" id="addTicketForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="category_id">Categories Tickets</label>
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="" disabled selected>--Select Categories--</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="assignee_to">Associated To Departments</label>
                            <select name="assignee_to" id="assignee_to" class="form-control">
                                <option value="" disabled selected>--Select Department--</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" {{ $department->name == 'IT' ? 'selected' : '' }}>{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select class="form-control" id="priority" name="priority">
                                <option value="" disabled selected>--Select Priority--</option>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Urgent">Urgent</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter Title">
                        </div>
                        <div class="form-group">
                            <label for="descriptionOne">Description Tickets</label>
                            <textarea id="summernoteOne" name="description"></textarea>
                        </div>
                    </form>
                    <div class="float-right">
                        <button type="submit" class="btn btn-primary" onclick="confirmAddTicket()">
                            <i class="fas fa-receipt"></i> Save
                        </button>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Title</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th class="text-center" width="20%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tickets as $ticket)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ strlen($ticket->title) > 20 ? substr($ticket->title, 0, 20) . '...' : $ticket->title }}</td>
                                        <td><span class="badge badge-{{ $ticket->priorityClass }}">{{ $ticket->priority }}</span></td>
                                        <td><span class="badge badge-{{ $ticket->badgeClass }}">{{ $ticket->status }}</span></td>
                                        <td class="text-center">
                                            <div class="d-inline-flex">
                                                @can('show ticketing')
                                                <a href="{{ route('ticketing.show', $ticket->id) }}" class="btn btn-info btn-circle mr-2">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('update ticketing')
                                                @if ($ticket->status != 'Closed' && $ticket->status == 'Open')
                                                <button type="button" class="btn btn-warning btn-circle mr-2"
                                                    data-toggle="modal" 
                                                    data-id="{{ $ticket->id }}"
                                                    data-category_id="{{ $ticket->category_id }}"
                                                    data-assigned_id="{{ $ticket->assigned_id }}"
                                                    data-priority="{{ $ticket->priority }}"
                                                    data-title="{{ $ticket->title }}"
                                                    data-description="{{ $ticket->description }}"
                                                    data-target="#updateTicketModal">
                                                    <i class="fas fa-pencil"></i>
                                                </button>
                                                @endif
                                                @endcan
                                                @can('delete ticketing')
                                                @if ($ticket->status == 'Open')
                                                <form action="{{ route('ticketing.destroy', $ticket->id) }}" method="POST" id="deleteTicketForm{{ $ticket->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteTicket({{ $ticket->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
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
    </div>

    <!-- Modal Update Tickets -->
    <div class="modal fade" id="updateTicketModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex align-items-center position-relative">
                    <h4 class="modal-title font-weight-bold text-white mx-auto" id="exampleModalLabel">Update Ticket</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>                
                <div class="modal-body">
                    <form action="{{ route('ticketing.update', ':id') }}" method="post" id="updateTicketForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_id">Categories Tickets</label>
                                    <select name="category_id" id="category_idUpdate" class="form-control">
                                        <option value="" disabled selected>Select Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="assignee_toUpdate">Associated To Departments</label>
                                    <select name="assignee_to" id="assignee_toUpdate" class="form-control">
                                        <option value="" disabled selected>Select Department</option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority">Priority</label>
                                    <select class="form-control" id="priorityUpdate" name="priority">
                                        <option value="" disabled selected>Select Priority</option>
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="titleUpdate" name="title" placeholder="Enter Title">
                        </div>
                        <div class="form-group">
                            <label for="descriptionTwo">Description Tickets</label>
                            <textarea id="summernoteTwo" class="form-control" name="description"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateTicket()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>    
@endsection

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- Page level custom scripts  -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    $(document).ready(function () {
        
        $('#summernoteOne').summernote({
            height: 300,                 // Set height
            tabsize: 2,                  // Tab size
            placeholder: 'Write your content here...',
            toolbar: [
                // Custom toolbar options
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
        });

        $('#summernoteTwo').summernote({
            height: 300,                 // Set height
            tabsize: 2,                  // Tab size
            placeholder: 'Write your content here...',
            toolbar: [
                // Custom toolbar options
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['fontsize', ['fontsize']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['height', ['height']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']],
            ],
        });
    });

    function confirmAddTicket() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to save this Ticket!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('addTicketForm').submit();
            }
        })
    }

    $('#updateTicketModal').on('show.bs.modal', function (e) {
        var button = $(e.relatedTarget);
        var id = button.data('id');
        var category= button.data('category_id');
        var assignee = button.data('assigned_id');
        var priority = button.data('priority');
        var title = button.data('title');
        var description = button.data('description');
        
        var modal = $(this);
        modal.find('.modal-body #id').val(id);
        modal.find('.modal-body #titleUpdate').val(title);
        modal.find('.modal-body #category_idUpdate').val(category).trigger('change');
        modal.find('.modal-body #assignee_toUpdate').val(assignee).trigger('change');
        modal.find('.modal-body #priorityUpdate').val(priority).trigger('change');
        modal.find('.modal-body #summernoteTwo').summernote('code', description);

        // replace action attribute
        var action = $('#updateTicketForm').attr('action');
        var newAction = action.replace(':id', id);
        $('#updateTicketForm').attr('action', newAction);
    });

    function confirmUpdateTicket() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be Update this Ticket!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateTicketForm').submit();
            }
        })
    }

    function confirmDeleteTicket(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be Delete this Ticket!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteTicketForm' + id).submit();
            }
        })
    }
</script>
@endpush