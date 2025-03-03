<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon">
            {{-- <i class="fas fa-code"></i> --}}
            {{-- <i class="fas fa-star-of-david"></i> --}}
        </div>
        <div class="sidebar-brand-text mx-3">UDPORTAL</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ Nav::isRoute('home') }}">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{ __('Dashboard') }}</span></a>
    </li>

    <!-- Nav Item - Claim -->
    @can('create vehicle reimbursements')
    <li class="nav-item {{ Nav::isRoute('reimbursements.create') }}">
        <a class="nav-link" href="{{ route('reimbursements.create') }}">
            <i class="fas fa-fw fa-gas-pump"></i>
            <span>{{ __('Claiment') }}</span></a>
    </li>
    @endcan

    @can('view contracts')
    <!-- Nav Item - Kontract Management-->
    <li class="nav-item {{ Nav::isRoute('contract.*') }}">
        <a class="nav-link" href="{{ route('contract.index') }}">
            <i class="fas fa-fw fa-file-contract"></i>
            <span>{{ __('Contract') }}</span></a>
    </li>
    @endcan

    @can('create ticketing')
    <!-- Nav Item - Kontract Management-->
    <li class="nav-item {{ Nav::isRoute('ticketing.create') }}">
        <a class="nav-link" href="{{ route('ticketing.create') }}">
            <i class="fas fa-fw fa-receipt"></i>
            <span>{{ __('Create Ticketing') }}</span></a>
    </li>
    @endcan


    @can('view petty cash')
    <li class="nav-item {{ request()->routeIs(['petty-cash', 'petty-cash.coa-sku']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePettyCash"
            aria-expanded="true" aria-controls="collapsePettyCash">
            <i class="fas fa-hand-holding-usd"></i>
            <span>{{ __('Petty Cash') }}</span>
        </a>
        <div id="collapsePettyCash" class="collapse {{ request()->routeIs(['petty-cash', 'petty-cash.coa-sku']) ? 'show' : '' }}" aria-labelledby="headingPettyCash"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('petty-cash') ? 'active' : '' }}" href="{{ route('petty-cash') }}">{{ __('Petty Cash') }}</a>
                @can('add coa sku')
                <a class="collapse-item {{ request()->routeIs('petty-cash.coa-sku') ? 'active' : '' }}" href="{{ route('petty-cash.coa-sku') }}">{{ __('Add COA & SKU') }}</a>
                @endcan
            </div>
        </div>
    </li>
    @endcan

    
    <!-- Nav Item - Tickecting Management Collapse Menu -->
    @can('view ticketing')
    <li class="nav-item {{ request()->routeIs(['ticketing.index', 'ticketing-categories.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTicketing"
            aria-expanded="true" aria-controls="collapseTicketing">
            <i class="fas fa-fw fa-hands-holding-child"></i>
            <span>Ticketing</span>
        </a>
        <div id="collapseTicketing" class="collapse {{ request()->routeIs(['ticketing.index', 'ticketing-categories.*']) ? 'show' : '' }}" aria-labelledby="headingTicketing"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('ticketing.index') ? 'active' : '' }}" href="{{ route('ticketing.index') }}">{{ __('Ticketing') }}</a>
                @can('view ticketing categories')
                <a class="collapse-item {{ request()->routeIs('ticketing-categories.index') ? 'active' : '' }}" href="{{ route('ticketing-categories.index') }}">{{ __('Ticketing Category') }}</a>
                @endcan
            </div>
        </div>
    </li>
    @endcan

    <!-- Nav Item - Tools Management Collapse Menu -->
    @can('view tools')
    <li class="nav-item {{ request()->routeIs(['tools.*','transactions.*', 'projects.*','tools-maintenances.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTools"
            aria-expanded="true" aria-controls="collapseTools">
            <i class="fas fa-fw fa-wrench"></i>
            <span>Tools</span>
        </a>
        <div id="collapseTools" class="collapse {{ request()->routeIs(['tools.*', 'tools-maintenances.*','transactions.*' ,'projects.*']) ? 'show' : '' }}" aria-labelledby="headingTools"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('tools.*') ? 'active' : '' }}" href="{{ route('tools.index') }}">{{ __('Tools') }}</a>
                @can('view projects')
                <a class="collapse-item {{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">{{ __('Projects') }}</a>
                @endcan
                @can('view asuransi project')
                <a class="collapse-item {{ request()->routeIs('asuransi-project.*') ? 'active' : '' }}" href="{{ route('asuransi-project.index') }}">{{ __('Asuransi Project') }}</a>
                @endcan
                @can('view tools transactions')
                <a class="collapse-item {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}">{{ __('Transactions') }}</a>
                @endcan
                @can('view tools maintenances')
                <a class="collapse-item {{ request()->routeIs('tools-maintenances.index') ? 'active' : '' }}" href="{{ route('tools-maintenances.index') }}">{{ __('Maintenances') }}</a>
                @endcan
            </div>
        </div>
    </li>
    @endcan

    <!-- Nav Item - Vehicles Management Collapse Menu -->
    @can('view vehicles')
    <li class="nav-item {{ request()->routeIs(['vehicles.*', 'reimbursements.index', 'vehicles-maintenances.*', 'insurances.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseVehicles"
            aria-expanded="true" aria-controls="collapseVehicles">
            <i class="fas fa-fw fa-truck-fast"></i>
            <span>Vehicles</span>
        </a>
        <div id="collapseVehicles" class="collapse {{ request()->routeIs(['vehicles.*', 'reimbursements.index', 'vehicles-maintenances.*', 'insurances.*']) ? 'show' : '' }}" aria-labelledby="headingVehicles"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('vehicles.*') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">{{ __('Vehicles') }}</a>
                @can('view vehicle reimbursements')
                <a class="collapse-item {{ request()->routeIs('reimbursements.index') ? 'active' : '' }}" href="{{ route('reimbursements.index') }}">{{ __('Claim Management') }}</a>
                @endcan
                @can('view vehicle maintenances')
                <a class="collapse-item {{ request()->routeIs('vehicles-maintenances.*') ? 'active' : '' }}" href="{{ route('vehicles-maintenances.index') }}">{{ __('Maintenances') }}</a>
                @endcan
                @can('view vehicle insurances')
                <a class="collapse-item {{ request()->routeIs('insurances.*') ? 'active' : '' }}" href="{{ route('insurances.index') }}">{{ __('Insurances') }}</a>
                @endcan
                {{-- <a class="collapse-item" href="" >{{ __('Climent Insurances') }}</a> --}}
            </div>
        </div>
    </li>
    @endcan

    <!-- Nav Item - Incomings Inventory Plan Collapse Menu -->
    @can('view incoming plan')
    <li class="nav-item {{ request()->routeIs(['incomings-supplier.*', 'incomings-inventory.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseIncomings"
            aria-expanded="true" aria-controls="collapseIncomings">
            <i class="fas fa-fw fa-truck-fast"></i>
            <span>RBM</span>
        </a>
        <div id="collapseIncomings" class="collapse {{ request()->routeIs(['incomings-supplier.*', 'incomings-inventory.*']) ? 'show' : '' }}" aria-labelledby="headingIncomings"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                @can('view incoming suppliers')
                <a class="collapse-item {{ request()->routeIs('incomings-supplier.*') ? 'active' : '' }}" href="{{ route('incomings-supplier.index') }}">{{ __('Supplier') }}</a>
                @endcan
                <a class="collapse-item {{ request()->routeIs('incomings-inventory.*') ? 'active' : '' }}" href="{{ route('incomings-inventory.index') }}">{{ __('Incomings Inventory') }}</a>
            </div>
        </div>
    </li>
    @endcan

    <!-- Cost Bid Analysis Collapse Menu -->
    @can('view bids analysis')
    <li class="nav-item {{ request()->routeIs(['bids-analysis.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCostBidAnalysis" 
            aria-expanded="true" aria-controls="collapseCostBidAnalysis">
            <i class="fas fa-fw fa-chart-bar"></i>
            <span>Cost Bids Analysis</span>
        </a>
        <div id="collapseCostBidAnalysis" class="collapse {{ request()->routeIs(['bids-analysis.*']) ? 'show' : '' }}" aria-labelledby="headingCostBidAnalysis" 
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('bids-analysis.index') ? 'active' : '' }}" href="{{ route('bids-analysis.index') }}">{{ __('Bids Analysis') }}</a>
            </div>
        </div>
    </li>
    @endcan

    <!-- Nav Item - Business Master Collapse Menu -->
    {{-- <li class="nav-item ">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBusiness"
            aria-expanded="true" aria-controls="collapseBusiness">
            <i class="fas fa-fw fa-users"></i>
            <span>Business Partner</span>
        </a>
        <div id="collapseBusiness" class="collapse" aria-labelledby="headingBusiness"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('bussines-master.index') }}">{{ __('Business Master') }}</a>
                <a class="collapse-item" href="">{{ __('Activity') }}</a>
                <a class="collapse-item" href="">{{ __('Campaign Generation Wizard') }}</a>
                <a class="collapse-item" href="">{{ __('Campaign') }}</a>
                <a class="collapse-item" href="">{{ __('Internal Reconciliations') }}</a>
                <a class="collapse-item" href="">{{ __('Business Partner Report') }}</a>
            </div>
        </div>
    </li> --}}

    <hr class="sidebar-divider">
    <!-- Heading -->
    <div class="sidebar-heading">
        Settings
    </div>

    <!-- Nav Item - Companies Management Collapse Menu -->
    @can('view companies')
    <li class="nav-item {{ request()->routeIs(['companies.*', 'branches.*', 'warehouses.*','departments.*', 'employees.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCompenies"
            aria-expanded="true" aria-controls="collapseCompenies">
            <i class="fas fa-fw fa-building"></i>
            <span>Company Management</span>
        </a>
        <div id="collapseCompenies" class="collapse {{ request()->routeIs(['companies.*', 'branches.*','warehouses.*', 'departments.*', 'employees.*']) ? 'show' : '' }}" aria-labelledby="headingCompenies"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('companies.index') ? 'active' : '' }}" href="{{ route('companies.index') }}">Companies</a>
                @can('view branches')
                <a class="collapse-item {{ request()->routeIs('branches.index') ? 'active' : '' }}" href="{{ route('branches.index') }}">Branches</a>
                @endcan
                @can('view warehouses')
                <a class="collapse-item {{ request()->routeIs('warehouses.index') ? 'active' : '' }}" href="{{ route('warehouses.index') }}">Warehouses</a>
                @endcan
                @can('view departments')
                <a class="collapse-item {{ request()->routeIs('departments.index') ? 'active' : '' }}" href="{{ route('departments.index') }}">Departments</a>
                @endcan
                @can('view employees')
                <a class="collapse-item {{ request()->routeIs('employees.index') ? 'active' : '' }}" href="{{ route('employees.index') }}">Employees</a>
                @endcan
            </div>
        </div>
    </li>
    @endcan

    <!-- Nav Item - Users Management Collapse Menu -->
    @can('view users')
    <li class="nav-item {{ request()->routeIs(['users.*', 'roles.*', 'notifications.*']) ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers"
            aria-expanded="true" aria-controls="collapseUsers">
            <i class="fas fa-fw fa-users-gear"></i>
            <span>Users Management</span>
        </a>
        <div id="collapseUsers" class="collapse {{ request()->routeIs(['users.*', 'roles.*', 'notifications.*']) ? 'show' : '' }}" aria-labelledby="headingUsers"
            data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">Users Account</a>
                @can('view roles')
                <a class="collapse-item {{ request()->routeIs('roles.index') ? 'active' : '' }}" href="{{ route('roles.index') }}">Roles & Permissions</a>
                @endcan
                @can('view notifications')
                <a class="collapse-item {{ request()->routeIs('notifications.index') ? 'active' : '' }}" href="{{ route('notifications.index') }}">Notifications</a>
                @endcan
            </div>
        </div>
    </li>
    @endcan
    
    <!-- Nav Item - Profile -->
    @can('view profile')
    <li class="nav-item {{ Nav::isRoute('profile') }}">
        <a class="nav-link" href="{{ route('profile') }}">
            <i class="fas fa-fw fa-user"></i>
            <span>{{ __('Profile') }}</span>
        </a>
    </li>
    @endcan

    <!-- Nav Item - About -->
    {{-- <li class="nav-item {{ Nav::isRoute('about') }}">
        <a class="nav-link" href="{{ route('about') }}">
            <i class="fas fa-fw fa-hands-helping"></i>
            <span>{{ __('About') }}</span>
        </a>
    </li> --}}

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>