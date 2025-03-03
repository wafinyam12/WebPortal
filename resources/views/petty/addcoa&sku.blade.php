@extends('layouts.admin', [
    'title' => 'addcoa&sku',
])

@push('css')
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
@endpush


@section('main-content')
    <h1 class="h3 mb-2 text-gray-800">Add COA, SKU, Item Group, & Project</h1>
    <p class="mb-4">
        This page is used InputCOA, SKU, Item Group, & Project.
    </p>
    {{-- =========================================================== --}}

    {{-- List Project --}}
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Project</h4>
                <button type="button" class="btn btn-primary btn-md mr-2" data-toggle="modal"
                    data-target="#addProjectModal">
                    <i class="fa fa-plus"></i> Add Project
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th class="text-center" width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projects as $key => $project)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $project->code_project }}</td>
                                    <td>{{ $project->keterangan }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm edit-btn" data-id="{{ $project->id }}"
                                            data-code="{{ $project->code_project }}" data-name="{{ $project->keterangan }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('petty-cash.destroy-project', $project->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add Project --}}
    <div class="modal fade" id="addProjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('petty-cash.add-project') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code">Code</label>
                            <input type="text" class="form-control" id="code_project" name="code_project" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Project --}}
    <div class="modal fade" id="editProjectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editForm" action="" method="POST">
                    @csrf
                    @method('PUT') <!-- Laravel akan mengenali ini sebagai metode PUT -->
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_code">Code</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- =========================================================== --}}

    <!-- List COA -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List COA</h4>
                <button type="button" class="btn btn-primary btn-md mr-2" data-toggle="modal"
                    data-target="#addCoaModal">
                    <i class="fa fa-plus"></i> Add COA
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable1" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>COA</th>
                                <th>Keterangan COA</th>
                                <th class="text-center" width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($coa as $key => $coas)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $coas->coa }}</td>
                                    <td>{{ $coas->keterangan }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm editcoa-btn" data-id="{{ $coas->id }}"
                                            data-code="{{ $coas->coa }}" data-name_coa="{{ $coas->keterangan }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('petty-cash.destroy-coa', $coas->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add COA --}}
    <div class="modal fade" id="addCoaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add COA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('petty-cash.add-coa') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code">COA</label>
                            <input type="text" class="form-control" id="coa" name="coa" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit COA --}}
    <div class="modal fade" id="editCoaModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit COA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editFormCoa" action="" method="POST">
                    @csrf
                    @method('PUT') <!-- Laravel akan mengenali ini sebagai metode PUT -->
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_code">COA</label>
                            <input type="text" class="form-control" id="edit_coa" name="coa" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_name">Name</label>
                            <input type="text" class="form-control" id="coa_name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- =========================================================== --}}

    <!-- List Item Group -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Item Group</h4>
                <button type="button" class="btn btn-primary btn-md mr-2" data-toggle="modal"
                    data-target="#addGroupModal">
                    <i class="fa fa-plus"></i> Add Item Group
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Code Item Group</th>
                                <th>Name Item Group</th>
                                <th>COA</th>
                                <th class="text-center" width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($item_group as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->code }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->coa->coa }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm editgroup-btn"
                                            data-id_group="{{ $item->id }}" data-code_group="{{ $item->code }}"
                                            data-name_group="{{ $item->keterangan }}"
                                            data-coa_group="{{ $item->coa->coa }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('petty-cash.destroy-group', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add Item Group --}}
    <div class="modal fade" id="addGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Item Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('petty-cash.add-group') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="code">Group code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Group Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="code">COA</label>
                            <select name="coa_id" id="coa_id" class="form-control">
                                @foreach ($coa as $coas)
                                    <option value="{{ $coas->id }}">{{ $coas->coa }} - {{ $coas->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit Item Group --}}
    <div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Item Group</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editFormGroup" action="" method="POST">
                    @csrf
                    @method('PUT') <!-- Laravel akan mengenali ini sebagai metode PUT -->
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_code">Group Code</label>
                            <input type="text" class="form-control" id="edit_group_code" name="edit_group_code"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="edit_name">Group Name</label>
                            <input type="text" class="form-control" id="edit_name_group" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="code">COA</label>
                            <select name="edit_coa_id" id="coa_id" class="form-control">
                                @foreach ($coa as $coas)
                                    <option value="{{ $coas->id }}">{{ $coas->coa }} - {{ $coas->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}

    <!-- List SKU -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List SKU</h4>
                <button type="button" class="btn btn-primary btn-md mr-2" data-toggle="modal"
                    data-target="#addSkuModal">
                    <i class="fa fa-plus"></i> Add SKU
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Code SKU</th>
                                <th>Deskripsi</th>
                                <th>Item Group</th>
                                <th class="text-center" width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sku as $key => $item)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->keterangan }}</td>
                                    <td>{{ $item->itemGroup->keterangan }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm editskugroup-btn"
                                            data-id_sku="{{ $item->id }}" data-sku="{{ $item->sku }}"
                                            data-name_sku="{{ $item->keterangan }}" data-group_sku="{{ $item->itemGroup->keterangan }}">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <form action="{{ route('petty-cash.destroy-sku', $item->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- add sku --}}
    <div class="modal fade" id="addSkuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add SKU</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('petty-cash.add-sku') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name">Item Group</label>
                            <select class="form-control" id="itemGroup" name="itemGroup">
                                @foreach ($item_group as $key => $item)
                                    <option value="{{ $item->id }}">{{ $item->code }} - {{ $item->keterangan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times"></i> Close
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal edit sku --}}
    <div class="modal fade" id="editSkuModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit SKU</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editSkuForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="sku">SKU</label>
                            <input type="text" class="form-control" id="sku" name="sku" readonly>
                        </div>
                        <div class="form-group">
                            <label for="name">Keterangan</label>
                            <input type="text" class="form-control" id="name_sku" name="name">
                        </div>
                        <div class="form-group">
                            <label for="name">Item Group</label>
                            <input type="text" class="form-control" id="group_sku" name="group" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
            $('#dataTable1').DataTable();
            $('#dataTable2').DataTable();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.edit-btn').on('click', function() {
                let id = $(this).data('id');
                let code = $(this).data('code');
                let name = $(this).data('name');

                $('#edit_id').val(id);
                $('#edit_code').val(code);
                $('#edit_name').val(name);

                // Menggunakan route Laravel dan mengganti :id dengan id sebenarnya
                let url = "{{ route('petty-cash.update-project', ':id') }}";
                url = url.replace(':id', id);
                $('#editForm').attr('action', url);

                $('#editProjectModal').modal('show');
            });

            $('.editcoa-btn').on('click', function() {
                let id = $(this).data('id');
                let code = $(this).data('code');
                let name = $(this).data('name_coa');

                $('#edit_id').val(id);
                $('#edit_coa').val(code);
                $('#coa_name').val(name);

                // Menggunakan route Laravel dan mengganti :id dengan id sebenarnya
                let url = "{{ route('petty-cash.update-coa', ':id') }}";
                url = url.replace(':id', id);
                $('#editFormCoa').attr('action', url);

                $('#editCoaModal').modal('show');
            });

            $('.editgroup-btn').on('click', function() {
                let id = $(this).data('id_group');
                let code = $(this).data('code_group');
                let name = $(this).data('name_group');
                let coa = $(this).data('coa_group');

                $('#edit_id').val(id);
                $('#edit_group_code').val(code);
                $('#edit_name_group').val(name);
                $('#coa_id').val(coa);

                // Menggunakan route Laravel dan mengganti :id dengan id sebenarnya
                let url = "{{ route('petty-cash.update-group', ':id') }}";
                url = url.replace(':id', id);
                $('#editFormGroup').attr('action', url);

                $('#editGroupModal').modal('show');
            });

            $('.editskugroup-btn').on('click', function() {
                let id = $(this).data('id_sku');
                let sku = $(this).data('sku');
                let name = $(this).data('name_sku');
                let group = $(this).data('group_sku');

                $('#edit_id').val(id);
                $('#sku').val(sku);
                $('#name_sku').val(name);
                $('#group_sku').val(group);

                // Menggunakan route Laravel dan mengganti :id dengan id sebenarnya
                let url = "{{ route('petty-cash.update-sku', ':id') }}";
                url = url.replace(':id', id);
                $('#editSkuForm').attr('action', url);

                $('#editSkuModal').modal('show');
            });
        });
    </script>
@endpush
