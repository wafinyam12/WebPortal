@extends('layouts.admin', [
    'title' => 'Dashboard'
])

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Dashboard') }}</h1>

    @if(auth()->user()->hasRole('Superadmin'))
    <div class="row">
        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tools</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $widget['tools'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-wrench fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Vehicles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $widget['vehicles'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck-fast fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Contracts</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $widget['contracts'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Users') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $widget['users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Content may profile photo end decription for Users -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title">{{ __('Profile Photo & Description') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div class="profile-photo-container mb-3">
                                <img src="{{ asset('storage/employees/photo/' . auth()->user()->employe->photo) }}" alt="Profile Photo" class="rounded-circle img-thumbnail"style="width: 200px; height: 200px; object-fit: cover;">
                            </div>
                        </div> 
                        <div class="col-md-8">
                            <div class="profile-info">
                                <h4 class="mb-3">{{ auth()->user()->fullName }}</h4>
                                
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <p class="text-muted">{{ auth()->user()->email }}</p>
                                </div>
    
                                <div class="mb-3">
                                    <label class="form-label">Role</label>
                                    <p class="text-muted">{{ auth()->user()->roles->pluck('name')->implode(', ') }}</p>
                                </div>
    
                                <div class="mb-3">
                                    <label class="form-label">Joined Date</label>
                                    <p class="text-muted">{{ auth()->user()->created_at->format('d F Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@push('scripts')
    
@endpush