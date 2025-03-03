@extends('layouts.admin',[
    'title' => 'Ticket Details'
])

@push('css')
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.css" rel="stylesheet">
@endpush

@section('main-content')

    <h1 class="h3 mb-2 text-gray-800">{{ __('Ticket Details') }}</h1>
    <p class="mb-4">
        This page is used to view ticket details. Tickets are used to track issues and problems with the system.
    </p>

    <div class="row">
        <!-- Ticket Details Card -->
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="m-0 font-weight-bold">
                        <i class="fas fa-ticket-alt mr-2"></i>Ticket Information
                    </h4>
                    <div class="d-flex align-items-center flex-wrap">
                        @if($tickets->status == 'Open')
                            @can('canceled ticketing')
                            <button type="button" class="btn btn-danger mr-2" data-toggle="modal" data-id="{{ $tickets->id }}" data-target="#cancelModal">
                                <i class="fas fa-window-close"></i>
                                Cancelled
                            </button>
                            @endcan
                        @endif
                        
                        @if($tickets->status == 'In Progress')
                            @if ($tickets->status != 'Closed' && $tickets->status != 'Cancelled')
                                @can('solved ticketing')
                                <button type="button" class="btn btn-success mr-2" data-toggle="modal" data-id="{{ $tickets->id }}" data-target="#solvedModal">
                                    <i class="fas fa-check-circle"></i>
                                    Solved Ticket
                                </button>
                                @endcan
                            @endif
                        @endif

                        @if ($tickets->status == 'Open')
                            @can('handle ticketing')
                            <form action="{{ route('ticketing.handle', $tickets->id) }}" method="post" id="handleTicketForm{{ $tickets->id }}">
                                @csrf
                                @method('PATCH')
                                <button type="button" onclick="confirmHandleTicket({{ $tickets->id }})" class="btn btn-warning mr-2">
                                    <i class="fas fa-handshake"></i> Handle Tickets
                                </button>
                            </form>
                            @endcan
                        @endif

                        @if(Auth::user()->hasRole('Superadmin'))
                        <a href="{{ route('ticketing.index') }}" class="btn btn-light">
                            <i class="fas fa-reply"></i> Back
                        </a>
                        @else
                            <a href="{{ route('ticketing.create') }}" class="btn btn-light">
                                <i class="fas fa-reply"></i> Back
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Title:</dt>
                                <dd class="col-6">{{ $tickets->title }}</dd>

                                <dt class="col-6">Status:</dt>
                                <dd class="col-6">
                                    <span class="badge badge-{{ $tickets->badgeClass }} text-capitalize">
                                        {{ $tickets->status }}
                                    </span>
                                </dd>

                                <dt class="col-6">Priority:</dt>
                                <dd class="col-6">
                                    <span class="badge badge-{{ $tickets->priorityClass }} text-capitalize">
                                        {{ $tickets->priority }}
                                    </span>
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl class="row">
                                <dt class="col-6">Assigned To:</dt>
                                <dd class="col-6">{{ $tickets->department->name ?? 'Unassigned' }}</dd>

                                <dt class="col-6">Created By:</dt>
                                <dd class="col-6">{{ $tickets->user->fullName }}</dd>

                                <dt class="col-6">Created At:</dt>
                                <dd class="col-6">{{ $tickets->created_at->format('d M Y') }}</dd>
                            </dl>
                        </div>
                    </div>

                    <hr>

                    <h6 class="font-weight-bold mb-3">Description</h6>
                    <div class="card bg-light p-3">
                        <p class="text-gray-800 mb-0">{!! $tickets->description !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @if ($tickets->status != 'Closed' && $tickets->status != 'Cancelled' && $tickets->status == 'In Progress')
        <!-- Comments Section -->
        <div class="col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center bg-gradient-secondary text-white">
                    <h4 class="m-0 font-weight-bold">
                        <i class="fas fa-comments mr-2"></i>Comments
                    </h4>
                    <span class="badge badge-light text-dark">
                        {{ $tickets->comments->count() }} Comments
                    </span>
                </div>
                <div class="card-body">
                    <!-- Comment List -->
                    <div class="comments-container" style="max-height: 350px; overflow-y: auto;">
                        @forelse ($tickets->comments as $comment)
                            <div class="media mb-3 p-3 bg-light rounded">
                                <img class="mr-3 rounded-circle" src="https://ui-avatars.com/api/?name={{ $comment->user->name }}"
                                    alt="{{ $comment->user->name }}" width="45" height="45">
                                <div class="media-body">
                                    <h6 class="mt-0 mb-1 font-weight-bold">
                                        {{ $comment->user->name }}
                                        <small class="text-muted ml-2">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </small>
                                    </h6>
                                    <p class="mb-0 text-gray-800">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="alert alert-info text-center" role="alert">
                                <i class="fas fa-info-circle mr-2"></i>No comments yet
                            </div>
                        @endforelse
                    </div>

                    <!-- Add Comment Form -->
                    @can('comment ticketing')
                    <form action="{{ route('ticketing.comment', $tickets->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('POST')
                        <div class="form-group">
                            <label for="comment" class="font-weight-bold">Add a Comment</label>
                            <textarea id="comment" name="comment" class="form-control @error('comment') is-invalid @enderror" rows="3" placeholder="Write your comment here..."required></textarea>
                            @error('comment')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm float-right">
                            <i class="fas fa-paper-plane mr-1"></i>Submit Comment
                        </button>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
        @endif

        @if ($tickets->status == 'Closed')
        <!-- Solved ticket Section --> 
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-gradient-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-0 font-weight-bold">
                            <i class="fas fa-ticket-alt mr-2"></i>Solved Ticket by {{ $tickets->user->name }}
                        </h4>
                        <span class="badge badge-light text-dark">
                            {{ Carbon\Carbon::parse($tickets->closed_at)->diffForHumans() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-gray-800 mb-0">
                        {!! $tickets->solution !!}
                    </p>
                    
                    @if ($tickets->attachment)
                        <div class="mt-3">
                            {{-- <a href="{{ route('ticketing.download', $tickets->id) }}" class="btn btn-primary btn-sm"> --}}
                            <a href="" class="btn btn-primary btn-sm">
                                <i class="fas fa-download mr-2"></i>Download Attachment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
        
        @if ($tickets->status == 'Cancelled')
        <!-- Cancelled ticket Section --> 
        <div class="col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 bg-gradient-danger text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-ticket-alt mr-2"></i>Cancelled Ticket by {{ $tickets->fixed->fullName ?? 'Unassigned' }}
                        </h6>
                        <span class="badge badge-light text-dark">
                            {{ Carbon\Carbon::parse($tickets->cancelled_at)->diffForHumans() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-gray-800 mb-0">
                        {!! $tickets->reason !!}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Solved ticket -->
    <div class="modal fade" id="solvedModal" tabindex="-1" role="dialog" aria-labelledby="solvedModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header text-primary">
                    <h5 class="modal-title" id="solvedModalLabel">Solved Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ticketing.solved', ':id') }}" method="post" id="closeTicketForm">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="solution">Solution</label>
                            <textarea id="summernoteTree" class="form-control" name="solution"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="attachment">Attachment</label>
                            <input type="file" class="form-control" id="attachment" name="attachment">
                            <p class="text-danger">Format: .pdf .doc .docx .pdf</p>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="button" class="btn btn-primary" onclick="confirmCloseTicket()"><i class="fas fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancelled ticket -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header text-danger">
                    <h5 class="modal-title" id="cancelModalLabel">Cancelled Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('ticketing.cancled', ':id') }}" method="post" id="cancelTicketForm">
                        @csrf
                        @method('PATCH')
                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea name="reason" id="reason" class="form-control" cols="30" rows="5"></textarea>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                        <button type="button" class="btn btn-danger" onclick="confirmCancelTicket()"><i class="fas fa-check"></i> Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote-bs4.min.js"></script>
<script>
    $(document).ready(function () {

        $('#solvedModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            var modal = $(this);
            modal.find('.modal-body #summernoteTree').summernote('code', '');
            
            // replace action attribute
            var action = $('#closeTicketForm').attr('action');
            var newAction = action.replace(':id', id);
            $('#closeTicketForm').attr('action', newAction);
        });
    
        $('#summernoteTree').summernote({
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

    
    function confirmHandleTicket(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, handle it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('handleTicketForm' + id).submit();
            }
        })
    }

    function confirmCloseTicket() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, close it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('closeTicketForm').submit();
            }
        })
    }

    $('#cancelModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');

        var modal = $(this);

        // replace action attribute
        var action = $('#cancelTicketForm').attr('action');
        var newAction = action.replace(':id', id);
        $('#cancelTicketForm').attr('action', newAction);
    });

    function confirmCancelTicket() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('cancelTicketForm').submit();
            }
        })
    }
</script>
@endpush
