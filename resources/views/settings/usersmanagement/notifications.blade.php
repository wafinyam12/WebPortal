@extends('layouts.admin', [
    'title' => 'Notification Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Notification Management</h1>
<p class="mb-4">
    This page is used to manage notifications.
</p>

<!-- DataTales Example -->
<div class="card">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header">
            <div class="justify-content-between d-flex align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Notification List</h4>
                @can('create notifications')
                <button type="button" class="btn btn-primary btn-md" data-toggle="modal" data-target="#addNotificationModal">
                    <i class="fas fa-bell"></i> Add Notification
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light text-center">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Notification Title</th>
                            <th>Notification For</th>
                            <th>Template</th>
                            <td width="15%">Action</td>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notification)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $notification->name }}</td>
                            <td>{!! $notification->nameRole !!}</td>
                            <td>{{ $notification->template }}</td>
                            <td class="text-center">
                                <div class="d-inline-flex">
                                    @can('show notifications')
                                    <button type="button" class="btn btn-circle btn-info mr-1"
                                        data-toggle="modal" data-id="{{ $notification->id }}"
                                        data-name="{{ $notification->name }}"
                                        data-description="{{ $notification->description }}"
                                        data-template="{{ $notification->template }}"
                                        data-roles="{{ implode(',', json_decode($notification->roles)) }}"
                                        data-target="#viewNotificationModal">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @endcan
                                    @can('update notifications')
                                    <button type="button" class="btn btn-circle btn-warning mr-1"
                                        data-toggle="modal"
                                        data-id="{{ $notification->id }}"
                                        data-name="{{ $notification->name }}"
                                        data-description="{{ $notification->description }}"
                                        data-template="{{ $notification->template }}"
                                        data-roles="{{ implode(',', json_decode($notification->roles)) }}"
                                        data-target="#editNotificationModal">
                                        <i class="fas fa-pencil"></i>
                                    </button>
                                    @endcan
                                    @can('delete notifications')
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="d-inline" id="deleteNotificationForm-{{ $notification->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-circle btn-danger" onclick="confirmDeleteNotification({{ $notification->id }})"><i class="fas fa-trash"></i></button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center font-weight-bold">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Notification Modal -->
<div class="modal fade" id="addNotificationModal" tabindex="-1" role="dialog" aria-labelledby="addNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                <h4 class="modal-title text-white font-weight-bold mx-auto" id="addNotificationModalLabel">Add Notification</h4>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('notifications.store') }}" method="POST" id="addNotificationForm">
                    @csrf
                    <div class="form-group">
                        <label for="name" class="form-label">Notification Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter the notification title" required>
                    </div>
                    <div class="form-group">
                        <label for="template" class="form-label">Notification Template <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('template') is-invalid @enderror" id="template" name="template" placeholder="Enter the notification template">
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Notification Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter the notification description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="roles" class="form-label">Notification For <span class="text-danger">*</span></label>
                        <select class="form-control @error('roles') is-invalid @enderror" id="select2insidemodal1" name="roles[]" multiple="multiple" required>
                            <option value="" disabled>Select roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddNotification()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Notification Modal -->
<div class="modal fade" id="editNotificationModal" tabindex="-1" role="dialog" aria-labelledby="editNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                <h4 class="modal-title text-white font-weight-bold mx-auto" id="editNotificationModalLabel">Edit Notification</h4>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('notifications.update', ':id') }}" method="POST" id="editNotificationForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="name" class="form-label">Notification Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter the notification title"required>
                    </div>
                    <div class="form-group">
                        <label for="template" class="form-label">Notification Template <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('template') is-invalid @enderror" id="template" name="template" placeholder="Enter the notification template">
                    </div>
                    <div class="form-group">
                        <label for="description" class="form-label">Notification Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter the notification description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="roles" class="form-label">Notification For <span class="text-danger">*</span></label>
                        <select class="form-control @error('roles') is-invalid @enderror" id="select2insidemodal2" name="roles[]" multiple="multiple" required>
                            <option value="" disabled>Select roles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}">
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmEditNotification()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Show Notification Modal -->
<div class="modal fade" id="viewNotificationModal" tabindex="-1" role="dialog" aria-labelledby="viewNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary d-flex justify-content-center align-items-center">
                <h4 class="modal-title text-white font-weight-bold mx-auto" id="viewNotificationModalLabel">Notification Details</h4>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="form-label">Notification Title</label>
                    <input type="text" class="form-control" id="name" name="name" readonly>
                </div>
                <div class="form-group">
                    <label for="template" class="form-label">Notification Template</label>
                    <input type="text" class="form-control" id="template" name="template" readonly>
                </div>
                <div class="form-group">
                    <label for="description" class="form-label">Notification Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" readonly></textarea>
                </div>
                <div class="form-group">
                    <label for="roles" class="form-label">Notification For</label>
                    <input type="text" class="form-control" id="roles" name="roles" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<!-- Page level plugins -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();

        $("#select2insidemodal1").select2({
            placeholder: "Select roles",
            width: '100%',
            dropdownParent: $('#addNotificationModal')
        });

        $("#select2insidemodal2").select2({
            placeholder: "Select roles",
            width: '100%',
            dropdownParent: $('#editNotificationModal')
        });
    });

    $("#viewNotificationModal").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var template = button.data('template');
        var description = button.data('description');
        var roles = button.data('roles');

        console.log(roles);
        
        var modal = $(this);
        modal.find('#name').val(name);
        modal.find('#template').val(template);
        modal.find('#description').val(description);
        modal.find('#roles').val(roles);
    });
    
    function confirmAddNotification() {
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
                document.getElementById('addNotificationForm').submit();
            }
        });
    }

    $("#editNotificationModal").on("show.bs.modal", function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var name = button.data('name');
        var template = button.data('template');
        var description = button.data('description');
        var roles = button.data('roles').split(',');

        var modal = $(this);
        modal.find('#name').val(name);
        modal.find('#template').val(template);
        modal.find('#description').val(description);
        modal.find('#select2insidemodal2').val(roles).trigger('change');

        // Ubah action form agar sesuai dengan id yang akan diupdate
        var formAction = '{{ route("notifications.update", ":id") }}';
        formAction = formAction.replace(':id', id);
        $('#editNotificationForm').attr('action', formAction); 
    });

    function confirmEditNotification() {
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
                document.getElementById('editNotificationForm').submit();
            }
        });
    }

    function confirmDeleteNotification(notificationId) {
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
                document.getElementById('deleteNotificationForm-' + notificationId).submit();
            }
        });
    }
</script>
@endpush