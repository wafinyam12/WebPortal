@extends('layouts.admin', [
    'title' => 'Tools Management'
])

@push('css')
   <!-- Custom styles for this page -->
   <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
                    
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Tools Management</h1>
    <p class="mb-4">
        This page is used to manage tools.
    </p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Tools</h4>
                <div class="d-flex align-items-center flex-wrap">
                    @can('create tools')
                    <!-- Tombol Import Data -->
                    {{-- <button type="button" class="btn btn-warning btn-md ml-2 mb-2" data-toggle="modal" data-target="#importToolsModal">
                        <i class="fas fa-file-import fa-md white-50"></i> Import Tools
                    </button> --}}
                    <!-- Tombol Add Users -->
                    <button type="button" class="btn btn-primary btn-md ml-2 mb-2" data-toggle="modal" data-target="#addToolsModal">
                        <i class="fas fa-wrench fa-md white-50"></i> Add Tools
                    </button>
                    @endcan
                </div>
            </div> 
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th class="text-center" width="10%">Code</th>
                                <th>Serial Number</th>
                                <th>Name</th>
                                <th width="5%">Unit</th>
                                <th width="5%">Quantity</th>
                                <th width="5%">Condition</th>
                                <th width="5%">Status</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tools as $tool)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('tools.show', $tool->code) }}">
                                            <img src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(80)->generate(route('tools.show', $tool->code))) }}" 
                                                 alt="QR Code">
                                        </a>                                        
                                        <br>
                                        <b>
                                            {{ $tool->code }}
                                        </b>
                                    </td>
                                    <td>{{ $tool->serial_number }}</td>
                                    <td>{{ $tool->name }}</td>
                                    <td class="text-center">{{ $tool->unit }}</td>
                                    <td class="text-center">{{ $tool->quantity }}</td>
                                    <td class="text-center">{!! $tool->conditionName !!}</span></td>
                                    <td class="text-center">{!! $tool->statusName !!}</td>
                                    <td>
                                        <div class="d-inline-flex">
                                            @can('show tools')
                                            <a href="{{ route('tools.show', $tool->id) }}" class="btn btn-info mr-1 btn-circle">
                                                <i class="fas fa-eye fa-md white-50"></i>
                                            </a>
                                            @endcan
                                            @can('update tools')
                                            <button type="button" class="btn btn-warning mr-1 btn-circle"
                                                data-toggle="modal"
                                                data-id="{{ $tool->id }}"
                                                data-owner="{{ $tool->owner->name }}"
                                                data-categorie="{{ $tool->categorie->name }}"
                                                data-serial="{{ $tool->serial_number }}"
                                                data-name="{{ $tool->name }}"
                                                data-brand="{{ $tool->brand }}"
                                                data-type="{{ $tool->type }}"
                                                data-model="{{ $tool->model }}"
                                                data-year="{{ $tool->year }}"
                                                data-origin="{{ $tool->origin }}"
                                                data-quantity="{{ $tool->quantity }}"
                                                data-unit="{{ $tool->unit }}"
                                                data-status="{{ $tool->status }}"
                                                data-description ="{{ $tool->description }}"
                                                data-purchase_date="{{ $tool->purchase_date }}"
                                                data-purchase_price="{{ $tool->purchase_price }}"
                                                data-warranty="{{ $tool->warranty }}"
                                                data-warranty_start="{{ $tool->warranty_start }}"
                                                data-warranty_end="{{ $tool->warranty_end }}"
                                                data-photo="{{ $tool->photo }}"
                                                data-target="#editToolsModal">
                                                <i class="fas fa-pencil fa-md white-50"></i>
                                            </button>
                                            @endcan
                                            @can('delete tools')
                                            <form action="{{ route('tools.destroy', $tool->id) }}" method="post" id="formDeleteTools-{{ $tool->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger mr-1 btn-circle" onclick="confirmToolsDelete({{ $tool->id }})">
                                                    <i class="fas fa-trash fa-md white-50"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @can('view tools categories')
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">Tools Categories</h4>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                    <i class="fas fa-list fa-md white-50"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Code</th>
                                <th width="15%">Name</th>
                                <th>Description</th>
                                <th class="text-center" width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->code }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            @can('update tools categories')
                                            <button type="button" class="btn btn-warning mr-1 btn-circle"
                                                data-toggle="modal"
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                data-description="{{ $category->description }}"
                                                data-target="#editCategoryModal">
                                                <i class="fas fa-pencil fa-md white-50"></i>
                                            </button>
                                            @endcan
                                            @can('delete tools categories')
                                            <form action="{{ route('categories.destroy', $category->id) }}" method="post" id="deleteCategoryForm-{{ $category->id }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDeleteCategory({{ $category->id }})" class="btn btn-danger btn-circle">
                                                    <i class="fas fa-trash fa-md white-50"></i>
                                                </button>
                                            </form>
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
    @endcan

    <!-- Modal Add Category Tools -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative ">
                    <h4 class="modal-title text-white font-weight-bold" id="addModalLabel">Create Category</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('categories.store') }}" method="post" id="addCategoryForm">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter Name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="4" placeholder="Enter Description (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddCategory()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Add Tools -->
    <div class="modal fade" id="addToolsModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold" id="addModalLabel">Create Tools</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tools.store') }}" method="post" id="addToolsForm" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ownership">Ownership <span class="text-danger">*</span></label>
                                    <select name="ownership" id="ownership" class="form-control @error('ownership') is-invalid @enderror">
                                        <option value="" disabled selected>Select Ownership</option>
                                        @foreach ($ownerships as $owner)
                                            <option value="{{ $owner->id }}">{{ $owner->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="serial_number">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_number" name="serial_number" placeholder="Enter Serial Number">
                                </div>
                                <div class="form-group">
                                    <label for="model">Model</label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" placeholder="Enter Model">
                                </div>
                                <div class="form-group">
                                    <label for="origin">Origin</label>
                                    <input type="text" class="form-control @error('origin') is-invalid @enderror" id="origin" name="origin" placeholder="Enter Origin">
                                </div>
                                {{-- <div class="form-group">
                                    <label for="warranty">Warranty</label>
                                    <input type="text" class="form-control @error('warranty') is-invalid @enderror" id="warranty" name="warranty" placeholder="Enter Warranty">
                                </div> --}}
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="categories">Categories <span class="text-danger">*</span></label>
                                    <select name="categories" id="categories" class="form-control @error('categories') is-invalid @enderror">
                                        <option value="" disabled selected>Select Categories</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" id="type" name="type" placeholder="Enter Type" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" min="1" value="1" placeholder="Enter Quantity" name="quantity">
                                </div>
                                {{-- <div class="form-group">
                                    <label for="warranty_start">Warranty Start</label>
                                    <input type="date" class="form-control @error('warranty_start') is-invalid @enderror" id="warranty_start" name="warranty_start">
                                </div>
                                <div class="form-group">
                                    <label for="warranty_end">Warranty End</label>
                                    <input type="date" class="form-control @error('warranty_end') is-invalid @enderror" id="warranty_end" name="warranty_end">
                                </div> --}}
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" id="unit" class="form-control @error('unit') is-invalid @enderror">
                                        <option value="" disabled selected>Select Unit</option>
                                        <option value="ROL">ROL</option>
                                        <option value="PCS">PCS</option>
                                        <option value="SET">SET</option>
                                        <option value="UNIT">UNIT</option>
                                        <option value="METER">METER</option>
                                        <option value="LITER">LITER</option>
                                        <option value="KG">KG</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="brand">Brand</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brand" name="brand" placeholder="Enter Brand">
                                </div>
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <input type="number" class="form-control @error('year') is-invalid @enderror" id="year" name="year" placeholder="Enter Year">
                                </div>
                                {{-- <div class="form-group">
                                    <label for="purchase_price">Purchase Price</label>
                                    <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_price" name="purchase_price" placeholder="Enter Purchase Price">
                                </div>
                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_date" name="purchase_date">
                                </div> --}}
                                <div class="form-group">
                                    <label for="photo">Photo</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo" name="photo" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="3" placeholder="Enter Description (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddTools()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Category Tools -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editModalLable" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center">
                    <h4 class="modal-title text-white font-weight-bold" id="editModalLable">Update Category</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('categories.update', ':id') }}" method="post" id="updateCategoryForm">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Enter Name" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30" rows="4" placeholder="Enter Description (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateCategory()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Tools -->
    <div class="modal fade" id="editToolsModal" tabindex="-1" aria-labelledby="editModalLable" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold" id="editModalLable">Update Tools</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('tools.update', ':id') }}" method="post" id="updateToolsForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') 
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select name="status" id="statusEdit" class="form-control @error('status') is-invalid @enderror">
                                <option value="" disabled selected>Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="ownership">Ownership <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('ownership') is-invalid @enderror" id="ownershipEdit" name="ownership"  placeholder="Enter Ownership" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="serial_number">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('serial_number') is-invalid @enderror" id="serial_numberEdit" name="serial_number" placeholder="Enter Serial Number" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="model">Model</label>
                                    <input type="text" class="form-control @error('model') is-invalid @enderror" id="modelEdit" placeholder="Enter Model" name="model">
                                </div>
                                <div class="form-group">
                                    <label for="origin">Origin</label>
                                    <input type="text" class="form-control @error('origin') is-invalid @enderror" id="originEdit" placeholder="Enter Origin" name="origin">
                                </div>
                                {{-- <div class="form-group">
                                    <label for="warranty">Warranty</label>
                                    <input type="text" class="form-control @error('warranty') is-invalid @enderror" id="warrantyEdit" placeholder="Enter Warranty" name="warranty">
                                </div> --}}
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="categories">Categories <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('categories') is-invalid @enderror" id="categoriesEdit" name="categories" placeholder="Enter Categories" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameEdit" name="name" placeholder="Enter Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="type">Type</label>
                                    <input type="text" class="form-control @error('type') is-invalid @enderror" id="typeEdit" name="type" placeholder="Enter Type" required>
                                </div>
                                <div class="form-group">
                                    <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantityEdit" min="1" value="1" name="quantity">
                                </div>
                                {{-- <div class="form-group">
                                    <label for="warranty_start">Warranty Start</label>
                                    <input type="date" class="form-control @error('warranty_start') is-invalid @enderror" id="warranty_startEdit" name="warranty_start">
                                </div>
                                <div class="form-group">
                                    <label for="warranty_end">Warranty End</label>
                                    <input type="date" class="form-control @error('warranty_end') is-invalid @enderror" id="warranty_endEdit" name="warranty_end">
                                </div> --}}
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="unit">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" id="unitEdit" class="form-control @error('unit') is-invalid @enderror">
                                        <option value="" disabled selected>Select Unit</option>
                                        <option value="ROL">ROL</option>
                                        <option value="PCS">PCS</option>
                                        <option value="SET">SET</option>
                                        <option value="UNIT">UNIT</option>
                                        <option value="METER">METER</option>
                                        <option value="LITER">LITER</option>
                                        <option value="KG">KG</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="brand">Brand</label>
                                    <input type="text" class="form-control @error('brand') is-invalid @enderror" id="brandEdit" name="brand">
                                </div>
                                <div class="form-group">
                                    <label for="year">Year</label>
                                    <input type="number" class="form-control @error('year') is-invalid @enderror" id="yearEdit" name="year" placeholder="Enter Year">
                                </div>
                                {{-- <div class="form-group">
                                    <label for="purchase_price">Purchase Price</label>
                                    <input type="number" class="form-control @error('purchase_price') is-invalid @enderror" id="purchase_priceEdit" name="purchase_price" placeholder="Enter Purchase Price">
                                </div>
                                <div class="form-group">
                                    <label for="purchase_date">Purchase Date</label>
                                    <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" id="purchase_dateEdit" name="purchase_date">
                                </div> --}}
                                <div class="form-group">
                                    <label for="photo">Photo</label>
                                    <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photoEdit" name="photo" accept="image/*">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="descriptionEdit" class="form-control" cols="30" rows="3" placeholder="Enter Description (optional)"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateTools()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Tools -->
    <div class="modal fade" id="importToolsModal" tabindex="-1" aria-labelledby="importModalLable" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title text-white font-weight-bold" id="importModalLable">Import Tools</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="importToolsForm" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" required>
                            <p class="text-danger">*Format file .xlsx .xls .csv</p></p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmImportTools()"><i class="fas fa-upload"></i> Upload</button>
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
        $('#dataTable2').DataTable();
    })

    function confirmAddCategory() {
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
                $('#addCategoryForm').submit();
            }
        });
    }

    function confirmAddTools() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to save this tools data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#addToolsForm').submit();
            }
        });
    }

    $('#editCategoryModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var categoryId = button.data('id');
        var categoryName = button.data('name');
        var categoryDescription = button.data('description');

        var modal = $(this);
        modal.find('#name').val(categoryName);
        modal.find('#description').val(categoryDescription);

        var FormAction = '{{ route("categories.update", ":id") }}';
        FormAction = FormAction.replace(':id', categoryId);
        $('#updateCategoryForm').attr('action', FormAction);
    });

    function confirmUpdateCategory() {
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
                $('#updateCategoryForm').submit();
            }
        });
    }

    $('#editToolsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var toolsId = button.data('id')
        var toolsOwner = button.data('owner')
        var toolsCategorie = button.data('categorie')
        var toolsSerial = button.data('serial')
        var toolsName = button.data('name')
        var toolsBrand = button.data('brand')
        var toolsType = button.data('type')
        var toolsCode = button.data('code')
        var toolsModel = button.data('model')
        var toolsYear = button.data('year')
        var toolsOrigin = button.data('origin')
        var toolsQuantity = button.data('quantity')
        var toolsUnit = button.data('unit')
        var toolsStatus = button.data('status')
        var toolsDescription = button.data('description')
        var toolsPurchaseDate = button.data('purchase_date')
        var toolsPurchasePrice = button.data('purchase_price')
        var toolsWarranty = button.data('warranty')
        var toolsWarrantyStart = button.data('warranty_start')
        var toolsWarrantyEnd = button.data('warranty_end')
        var toolsPhoto = button.data('photo')

        var modal = $(this);
        modal.find('#ownershipEdit').val(toolsOwner);
        modal.find('#categoriesEdit').val(toolsCategorie);
        modal.find('#serial_numberEdit').val(toolsSerial);
        modal.find('#nameEdit').val(toolsName);
        modal.find('#brandEdit').val(toolsBrand);
        modal.find('#typeEdit').val(toolsType);
        modal.find('#codeEdit').val(toolsCode);
        modal.find('#modelEdit').val(toolsModel);
        modal.find('#yearEdit').val(toolsYear);
        modal.find('#originEdit').val(toolsOrigin);
        modal.find('#quantityEdit').val(toolsQuantity);
        modal.find('#unitEdit').val(toolsUnit).trigger('change');
        modal.find('#statusEdit').val(toolsStatus).trigger('change');
        modal.find('#descriptionEdit').val(toolsDescription);
        modal.find('#purchase_dateEdit').val(toolsPurchaseDate);
        modal.find('#purchase_priceEdit').val(toolsPurchasePrice);
        modal.find('#warrantyEdit').val(toolsWarranty);
        modal.find('#warranty_startEdit').val(toolsWarrantyStart);
        modal.find('#warranty_endEdit').val(toolsWarrantyEnd);
        modal.find('#photoEdit').val(toolsPhoto);

        var FormAction = '{{ route("tools.update", ":id") }}';
        FormAction = FormAction.replace(':id', toolsId);
        $('#updateToolsForm').attr('action', FormAction);
    })
    
    function confirmUpdateTools() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this tools data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateToolsForm').submit();
            }
        });
    }

    function confirmDeleteCategory(id) {
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
                $('#deleteCategoryForm-' + id).submit();
            }
        });
    }

    function confirmToolsDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this tools data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formDeleteTools-' + id).submit();
            }
        });
    }

</script>
@endpush