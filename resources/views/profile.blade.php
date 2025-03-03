@extends('layouts.admin', [
    'title' => 'Profile'
])

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Profile') }}</h1>

    @if (session('success'))
        <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger border-left-danger" role="alert">
            <ul class="pl-4 my-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">

        <div class="col-lg-4 order-lg-2">

            <div class="card shadow mb-4">
                <div class="card-profile-image mt-4">
                    {{-- <figure class="rounded-circle avatar avatar font-weight-bold" style="font-size: 60px; height: 180px; width: 180px;" data-initial="{{ Auth::user()->name[0] }}"></figure> --}}
                    <img src="{{ asset('storage/employees/photo/' . auth()->user()->employe->photo) }}" class="card-img-top rounded-circle" alt="profile">
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <h5 class="font-weight-bold">{{  Auth::user()->fullName }}</h5>
                                <p>{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- input foto profile & button update -->
                    @can('update profile')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#updatePhotoModal">
                                    <i class="fas fa-upload"></i> 
                                    Update Photo
                                </button>
                            </div>
                        </div>
                    </div> 
                    @endcan
                </div>
            </div>

        </div>

        <div class="col-lg-8 order-lg-1">

            <div class="card shadow mb-4">

                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">My Account</h6>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('profile.update') }}" id="updateProfileForm" autocomplete="off">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <input type="hidden" name="_method" value="PUT">

                        <h6 class="heading-small text-muted mb-4">User information</h6>

                        <div class="pl-lg-4">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="name">Name<span class="small text-danger">*</span></label>
                                        <input type="text" id="name" class="form-control" name="name" placeholder="Name" value="{{ old('name', Auth::user()->name) }}">
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="last_name">Last name</label>
                                        <input type="text" id="last_name" class="form-control" name="last_name" placeholder="Last name" value="{{ old('last_name', Auth::user()->last_name) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-control-label" for="email">Email address<span class="small text-danger">*</span></label>
                                        <input type="email" id="email" class="form-control" name="email" placeholder="example@example.com" value="{{ old('email', Auth::user()->email) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="current_password">Current password</label>
                                        <input type="password" id="current_password" class="form-control" name="current_password" placeholder="Current password">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="new_password">New password</label>
                                        <input type="password" id="new_password" class="form-control" name="new_password" placeholder="New password">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group focused">
                                        <label class="form-control-label" for="confirm_password">Confirm password</label>
                                        <input type="password" id="confirm_password" class="form-control" name="password_confirmation" placeholder="Confirm password">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Button -->
                    @can('update profile')
                    <div class="pl-lg-4">
                        <div class="row">
                            <div class="col text-center">
                                <button type="button" class="btn btn-primary" onclick="confirmUpdateProfile()">
                                    <i class="fas fa-check"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </div>
                    @endcan

                </div>

            </div>

        </div>

    </div>

<!-- Modal Update Photo -->
<div class="modal fade" id="updatePhotoModal" tabindex="-1" role="dialog" aria-labelledby="updatePhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                <h4 class="modal-title font-weight-bold text-white" id="updatePhotoModalLabel">Update Photo</h4>
                <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 10px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('profile.updateImage') }}" autocomplete="off" enctype="multipart/form-data" id="updatePhotoForm">
                    @csrf
                    @method('PUT')
                    <div class="form-group focused">
                        <label class="form-control-label" for="photo">Photo</label>
                        <input type="file" id="photo" name="photo" class="form-control @error('photo') is-invalid @enderror" accept="image/*">
                        <p class="text-danger text-left mb-0"><small>*Maximum file size: 2MB and format: .jpg, .jpeg, .png</small></p>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                <button type="button" class="btn btn-primary" onclick="confirmUpdatePhoto()"><i class="fas fa-check"></i> Save changes</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        function confirmUpdateProfile() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to update the profile!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('updateProfileForm').submit();
                }
            })
        }

        function confirmUpdatePhoto() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to update the photo!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, Update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('updatePhotoForm').submit();
                }
            })
        }
    </script>
@endpush
