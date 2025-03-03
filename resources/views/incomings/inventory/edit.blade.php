@extends('layouts.admin', [
    'title' => 'Incoming Inventory Update'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Incoming Inventory Update</h1>
<p class="mb-4">
    This page is used to update incoming inventory.
</p>

<!-- Update Incoming Inventory -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Update Incoming Inventory</h4>
            <a href="{{ route('incomings-inventory.index') }}" class="btn btn-primary btn-md mr-2">
                <i class="fas fa-reply"></i> 
                Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('incomings-inventory.update', $incomings->id) }}" method="POST" id="updateIncomingInventoryForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="branch_id">Branch <span class="text-danger">*</span></label>
                            <select name="branch_id" id="branch_id" class="form-control @error('branch_id') is-invalid @enderror" required>
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branch_id', $incomings->branch->id) == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label for="supplier_id">Supplier <span class="text-danger">*</span></label>
                            <select name="supplier_id" id="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror" required>
                                <option value="">Select Supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', $incomings->supplier->id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="eta">Expected Arrival Date <span class="text-danger">*</span></label>
                            <input type="date" name="eta" id="eta" class="form-control @error('eta') is-invalid @enderror" 
                                value="{{ old('eta', $incomings->eta) }}" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="warehouse_id">Warehouse <span class="text-danger">*</span></label>
                            <select name="warehouse_id" id="warehouse_id" class="form-control">
                                <option value="">Select Warehouse</option>
                                @foreach ($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}" {{ old('warehouse_id', $incomings->drop->id) == $warehouse->id ? 'selected' : '' }}>
                                        {{ $warehouse->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="drop_site">Drop Site <span class="text-danger">*</span></label>
                            <input type="text" name="drop_site" id="drop_site" class="form-control @error('drop_site') is-invalid @enderror" 
                                value="{{ old('drop_site', $incomings->drop_site) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="email_drop_site">Email Drop Site <span class="text-danger">*</span></label>
                            <input type="text" name="email_drop_site" id="email_drop_site" class="form-control @error('email_drop_site') is-invalid @enderror" 
                                value="{{ old('email_drop_site', $incomings->email_drop_site) }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="phone_drop_site">Phone Drop Site <span class="text-danger">*</span></label>
                            <input type="number" name="phone_drop_site" id="phone_drop_site" class="form-control @error('phone_drop_site') is-invalid @enderror" 
                                value="{{ old('phone_drop_site', $incomings->phone_drop_site) }}">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="note">Notes <span class="text-danger">*</span></label> 
                    <textarea name="notes" id="notes" class="form-control" cols="30" rows="5" placeholder="Notes (optional)">{{ old('notes', $incomings->notes) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="attachment">File</label>
                    <input type="file" name="attachment" id="attachment" class="form-control @error('attachment') is-invalid @enderror" accept="application/pdf">
                
                    <!-- Tampilkan tautan file jika file sebelumnya ada -->
                    @if (!empty($incomings->attachment))
                        <a href="{{ asset('storage/incomings/file/' . $incomings->attachment) }}" target="_blank" class="mt-2 d-block">
                            Lihat File Saat Ini
                        </a>
                    @endif
                </div>

                <hr>
    
                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <table class="table table-bordered" id="items-table">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th width="5%">No</th>
                                <th>Item Name</th>
                                <th width="20%">Quantity</th>
                                <th width="10%" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($incomings->item as $index => $incoming)
                                <tr data-row-id="{{ $incoming->id }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <input type="hidden" name="items[{{ $index }}][id]" value="{{ $incoming->id }}">
                                        <input type="text" name="items[{{ $index }}][item_name]" 
                                            class="form-control @error('items.' . $index . '.item_name') is-invalid @enderror"
                                            value="{{ old('items.' . $index . '.item_name', $incoming->item_name) }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $index }}][quantity]" 
                                            class="form-control @error('items.' . $index . '.quantity') is-invalid @enderror"
                                            value="{{ old('items.' . $index . '.quantity', $incoming->quantity) }}" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-circle" onclick="deleteRow(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
    
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-info" id="add-item">
                            <i class="fas fa-plus-circle"></i> Add Item
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="text-right my-3 mr-3">
            <button type="button" class="btn btn-success" onclick="confirmUpdateIncomingInventory()">
                <i class="fas fa-truck-moving"></i> Update Incoming Inventory
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable with specific configuration
    const table = $('#items-table').DataTable({
        paging: false,
        ordering: false,
        info: false,
        searching: false,
        language: {
            emptyTable: "No items added yet"
        }
    });

    // Add new item row
    $('#add-item').click(function() {
        const rowCount = table.rows().count();
        const newRow = `
            <tr>
                <td>${rowCount + 1}</td>
                <td>
                    <input type="text" name="items[${rowCount}][item_name]" class="form-control" required>
                </td>
                <td>
                    <input type="text" name="items[${rowCount}][quantity]" class="form-control" required>
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-circle" onclick="deleteRow(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        table.row.add($(newRow)).draw();
        updateRowNumbers();
    });
    
});

// Delete row function
function deleteRow(button) {
    const table = $('#items-table').DataTable();
    const row = $(button).closest('tr');
    
    if (table.rows().count() > 1) {
        table.row(row).remove().draw();
        updateRowNumbers();
    } else {
        Swal.fire({
            title: 'Cannot Delete',
            text: 'At least one item is required',
            icon: 'warning'
        });
    }
}

// Update row numbers
function updateRowNumbers() {
    $('#items-table tbody tr').each(function(index) {
        $(this).find('td:first').text(index + 1);
    });
}

function confirmUpdateIncomingInventory() {
    if (validateForm()) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this incoming inventory data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#updateIncomingInventoryForm').submit();
            }
        })
    }
}

// Form validation
function validateForm() {
    let isValid = true;
    const requiredFields = ['branch_id', 'supplier_id', 'eta'];
    
    requiredFields.forEach(field => {
        const element = $(`#${field}`);
        if (!element.val()) {
            element.addClass('is-invalid');
            isValid = false;
        } else {
            element.removeClass('is-invalid');
        }
    });

    // Validate items
    const items = $('#items-table tbody tr');
    if (items.length === 0) {
        Swal.fire({
            title: 'Validation Error',
            text: 'At least one item is required',
            icon: 'error'
        });
        return false;
    }

    if (!isValid) {
        Swal.fire({
            title: 'Validation Error',
            text: 'Please fill in all required fields',
            icon: 'error'
        });
    }

    return isValid;
}
</script>
@endpush