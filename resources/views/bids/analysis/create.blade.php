@extends('layouts.admin', [
    'title' => 'Create Bids Analysis'
])

@push('css')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Create Bids Analysis</h1>
<p class="mb-4">This page is used to create bids analysis.</p>

<!-- List Bids Analysis -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Create Bids Analysis</h4>
            <a href="{{ route('bids-analysis.index') }}" class="btn btn-primary btn-md mr-2">
                <i class="fas fa-reply"></i> 
                Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('bids-analysis.store') }}" method="POST" id="bidForm" enctype="multipart/form-data">
                @csrf
                <!-- Bid Information -->
                <div class="form-group">
                    <label for="branch">Branch  <span class="text-danger">*</span></label>
                    <select name="branch_id" id="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                        <option value="" disabled selected>Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Project Name   <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('project_name') is-invalid @enderror" name="project_name" value="{{ old('project_name') }}" placeholder="Project Name" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date   <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('bid_date') is-invalid @enderror" name="bid_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Number of Vendors Bids <span class="text-danger">*</span></label>
                            <select class="form-control @error('vendor_count') is-invalid @enderror" id="vendorCount" name="vendor_count">
                                <option value="2" selected>Bids 2 Vendors</option>
                                <option value="3">Bids 3 Vendors</option>
                                <option value="4">Bids 4 Vendors</option>
                                <option value="5">Bids 5 Vendors</option>
                            </select>
                        </div>
                    </div>
                </div>
    
                <!-- Vendor Names Container -->
                <div class="row mb-4" id="vendorNamesContainer">
                    <!-- Vendor inputs will be added here dynamically -->
                </div>
    
                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold">Items</h6>
                        <button type="button" class="btn btn-primary btn-sm m-1" id="addItem">
                            <i class="fas fa-cart-arrow-down mr-1"></i> Add Item
                        </button>
                    </div>
                    <table class="table table-bordered" id="bidsTable">
                        <thead>
                            <tr id="headerRow">
                                <th rowspan="2" class="align-middle">No</th>
                                <th rowspan="2" class="align-middle">Description</th>
                                <th rowspan="2" class="align-middle">QTY</th>
                                <th rowspan="2" class="align-middle">UOM</th>
                            </tr>
                            <tr id="subHeaderRow">
                                <!-- Price/Unit and Total columns will be added dynamically -->
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Item rows will be added here -->
                        </tbody>
                        <tfoot id="tableFooter">
                            <!-- Footer will be added dynamically -->
                        </tfoot>
                    </table>
                    <div class="form-group">
                        <label for="selected">Selected Vendor <span class="text-danger">*</span></label>
                        <select class="form-control @error('selected_vendor') is-invalid @enderror" id="selected-vendor" name="selected_vendor">
                            <!-- Options will be added dynamically -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Notes"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="file">Attachment <span class="text-danger">*</span></label>
                        <input type="file" name="file" id="file" class="form-control" accept="application/pdf">
                    </div>
                </div>
            </form>
            <div class="float-right">
                <button type="button" class="btn btn-primary" onclick="confirmAddAnalysis()">
                    <i class="fas fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const existingVendors = @json($vendors);

    $(document).ready(function() {
        let vendors = [];
        
        // Initialize the form
        function initializeForm() {
            const vendorCount = parseInt($('#vendorCount').val());
            vendors = [];
            
            // Clear existing vendor inputs
            $('#vendorNamesContainer').empty();
            
            // Add vendor name inputs
            for(let i = 0; i < vendorCount; i++) {
                const vendorInput = `
                    <div class="col-md-${12/vendorCount}">
                        <div class="form-group">
                            <label>Vendor ${i + 1} Name  <span class="text-danger">*</span></label>
                            <select class="form-control select-vendor" 
                                name="vendor_names[]" 
                                data-vendor-index="${i}"
                                required>
                                <option value="">Select Vendor</option>
                                <option value="new">+ Add New Vendor</option>
                                ${existingVendors.map(vendor => 
                                    `<option value="${vendor.id}">${vendor.name}</option>`
                                ).join('')}
                            </select>
                        </div>
                        
                        <!-- New vendor form -->
                        <div class="new-vendor-form" style="display:none;">
                            <div class="form-group">
                                <input type="text" class="form-control vendor-name" 
                                    name="new_vendor_names[]" 
                                    data-vendor-index="${i}"
                                    placeholder="New Vendor Name">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" 
                                    name="new_vendor_email[]"
                                    placeholder="Vendor Email">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" 
                                    name="new_vendor_phone[]"
                                    placeholder="Vendor Phone">
                            </div>
                        </div>
                        
                        <!-- Existing vendor info -->
                        <div class="existing-vendor-info" style="display:none;">
                            <div class="form-group">
                                <input type="email" class="form-control" readonly 
                                    name="vendor_email[]">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" readonly 
                                    name="vendor_phone[]">
                            </div>
                        </div>
                    </div>`;
                $('#vendorNamesContainer').append(vendorInput);
            }

            // Handle vendor selection change
            $('.select-vendor').on('change', function() {
                const $container = $(this).closest('.col-md-' + (12/vendorCount));
                const $newForm = $container.find('.new-vendor-form');
                const $existingInfo = $container.find('.existing-vendor-info');
                const vendorIndex = $(this).data('vendor-index');
                
                if ($(this).val() === 'new') {
                    $newForm.show();
                    $existingInfo.hide();
                    $newForm.find('input').prop('disabled', false);
                    
                    $newForm.find('.vendor-name').on('input', function() {
                        updateVendorsList();
                        updateTableStructure();
                    });
                } else if ($(this).val()) {
                    $newForm.hide();
                    $existingInfo.show();
                    $newForm.find('input').prop('disabled', true);
                    
                    const vendorId = $(this).val();
                    const vendor = existingVendors.find(v => v.id == vendorId);
                    if (vendor) {
                        $existingInfo.find('input[name="vendor_email[]"]').val(vendor.email);
                        $existingInfo.find('input[name="vendor_phone[]"]').val(vendor.phone);
                    }
                } else {
                    $newForm.hide();
                    $existingInfo.hide();
                }
                
                updateVendorsList();
                updateTableStructure();
            });
            
            updateTableStructure();
            addInitialRow();
            updateSelectedVendorDropdown();
        }
        
        // Update vendors list based on selections
        function updateVendorsList() {
            vendors = [];
            $('.select-vendor').each(function() {
                const $select = $(this);
                const vendorIndex = $select.data('vendor-index');
                let vendorName;
                
                if ($select.val() === 'new') {
                    const $newForm = $select.closest('div').siblings('.new-vendor-form');
                    vendorName = $newForm.find('.vendor-name').val() || `Vendor ${vendorIndex + 1}`;
                } else if ($select.val()) {
                    const selectedVendor = existingVendors.find(v => v.id == $select.val());
                    vendorName = selectedVendor.name;
                } else {
                    vendorName = `Vendor ${vendorIndex + 1}`;
                }
                
                vendors.push({
                    name: vendorName,
                    index: vendorIndex
                });
            });
            
            updateSelectedVendorDropdown();
            calculateTotals();
        }
        
        // Update selected vendor dropdown
        function updateSelectedVendorDropdown() {
            const $select = $('#selected-vendor');
            $select.empty();
            $select.append('<option value="">Select Vendor</option>');
            
            vendors.forEach((vendor) => {
                const vendorName = vendor.name.trim() || `Vendor ${vendor.index + 1}`;
                $select.append(`<option value="${vendorName}">${vendorName}</option>`);
            });
        }
        
        // Update table structure
        function updateTableStructure() {
            updateVendorsList();
            
            let headerHtml = `
                <th rowspan="2" class="align-middle">No</th>
                <th rowspan="2" class="align-middle">Description</th>
                <th rowspan="2" class="align-middle text-center">QTY</th>
                <th rowspan="2" class="align-middle text-center">UOM</th>`;
                
            let subHeaderHtml = '';
            
            vendors.forEach(vendor => {
                headerHtml += `<th colspan="2" class="text-center vendor-header-${vendor.index}">${vendor.name}</th>`;
                subHeaderHtml += `
                    <th class="text-center">Price/Unit</th>
                    <th class="text-center">Total</th>`;
            });
            
            headerHtml += '<th rowspan="2" class="align-middle">Action</th>';
            
            $('#headerRow').html(headerHtml);
            $('#subHeaderRow').html(subHeaderHtml);
            
            updateFooterStructure();
            updateSelectedVendorDropdown();
        }
        
        // Create item row
        function createItemRow(index) {
            let rowHtml = `
                <tr class="item-row">
                    <td>${index + 1}</td>
                    <td>
                        <input type="text" class="form-control form-control-sm" 
                            name="items[${index}][description]" placeholder="Item Description" required>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm quantity" 
                            name="items[${index}][quantity]" placeholder="Quantity" required>
                    </td>
                    <td>
                        <select class="form-control form-control-sm" name="items[${index}][uom]" required>
                            <option value="PCS">PCS</option>
                            <option value="UNIT">UNIT</option>
                            <option value="SET">SET</option>
                        </select>
                    </td>`;
            
            vendors.forEach(vendor => {
                rowHtml += `
                    <td>
                        <input type="number" class="form-control form-control-sm price vendor${vendor.index}-price" 
                            name="items[${index}][vendor${vendor.index}_price]" placeholder="Rp" required>
                    </td>
                    <td class="vendor${vendor.index}-total">0</td>`;
            });
            
            rowHtml += `
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-circle remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>`;
                
            return rowHtml;
        }
        
        // Add initial row
        function addInitialRow() {
            const newRow = createItemRow(0);
            $('#itemsContainer').html(newRow);
            calculateTotals();
        }
        
        // Update footer structure - IMPROVED
        function updateFooterStructure() {
            let footerHtml = `
                <tr class="bg-light font-weight-bold">
                    <td colspan="4" class="text-right">TOTAL</td>`;
                    
            vendors.forEach(vendor => {
                footerHtml += `
                    <td colspan="2">
                        <span id="vendor${vendor.index}-grand-total">0</span>
                        <input type="hidden" name="vendor${vendor.index}_grand_total" value="0">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';
            
            // Discount row
            footerHtml += `
                <tr>
                    <td colspan="4" class="text-right">DISCOUNT (%)</td>`;
                    
            vendors.forEach(vendor => {
                footerHtml += `
                    <td colspan="2">
                        <input type="number" class="form-control form-control-sm discount-input" 
                            name="vendor${vendor.index}_discount" 
                            data-vendor-index="${vendor.index}"
                            placeholder="Discount (%)">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';
            
            // Total after discount row
            footerHtml += `
                <tr class="bg-light font-weight-bold">
                    <td colspan="4" class="text-right">TOTAL After Discount</td>`;
                    
            vendors.forEach(vendor => {
                footerHtml += `
                    <td colspan="2">
                        <span id="vendor${vendor.index}-final-total">0</span>
                        <input type="hidden" name="vendor${vendor.index}_final_total" value="0">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';
            
            // Add remaining rows (Terms of Payment, Lead Time, Note)
            footerHtml += addAdditionalFooterRows();
            
            $('#tableFooter').html(footerHtml);
        }
        
        // Helper function for additional footer rows
        function addAdditionalFooterRows() {
            let html = '';
            
            // Terms of payment row
            html += createFooterRow('Terms of Payment (TOP)', 'terms_of_payment_vendor', 'Terms of Payment');
            
            // Lead time row
            html += createFooterRow('Lead Time (Days)', 'lead_time_vendor', 'Lead Time');
            
            // Note row
            html += createFooterRow('Note', 'notes_vendor', 'Note');
            
            return html;
        }
        
        // Helper function to create footer rows
        function createFooterRow(label, namePrefix, placeholder) {
            let html = `
                <tr>
                    <td colspan="4" class="text-right">${label}</td>`;
                    
            vendors.forEach(vendor => {
                html += `
                    <td colspan="2">
                        <input type="text" class="form-control form-control-sm" 
                            name="${namePrefix}${vendor.index}" 
                            placeholder="${placeholder}">
                    </td>`;
            });
            
            html += '<td></td></tr>';
            return html;
        }
        
        // Calculate totals - IMPROVED
        function calculateTotals() {
            vendors.forEach(vendor => {
                let vendorTotal = 0;
                
                // Calculate row totals
                $('.item-row').each(function() {
                    const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                    const price = parseFloat($(this).find(`.vendor${vendor.index}-price`).val()) || 0;
                    const rowTotal = quantity * price;
                    
                    $(this).find(`.vendor${vendor.index}-total`).text(rowTotal.toLocaleString());
                    vendorTotal += rowTotal;
                });
                
                // Update grand total
                $(`#vendor${vendor.index}-grand-total`).text(vendorTotal.toLocaleString());
                $(`input[name="vendor${vendor.index}_grand_total"]`).val(vendorTotal);
                
                // Calculate final total with discount
                const discount = parseFloat($(`input[name="vendor${vendor.index}_discount"]`).val()) || 0;
                const finalTotal = vendorTotal * (1 - discount/100);
                
                // Update final total
                $(`#vendor${vendor.index}-final-total`).text(finalTotal.toLocaleString());
                $(`input[name="vendor${vendor.index}_final_total"]`).val(finalTotal);
                
                // Debug log
                // console.log(`Vendor ${vendor.index} totals:`, {
                //     grandTotal: vendorTotal,
                //     discount: discount,
                //     finalTotal: finalTotal
                // });
            });
        }
        
        // Event Handlers
        $('#vendorCount').change(initializeForm);
        
        $('#addItem').click(function() {
            const rowCount = $('.item-row').length;
            const newRow = createItemRow(rowCount);
            $('#itemsContainer').append(newRow);
            calculateTotals();
        });
        
        // Remove item handler
        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                
                // Reset row numbers
                $('.item-row').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                });
                
                calculateTotals();
            }
        });
        
        // Input change handlers
        $(document).on('input', '.quantity, .price', calculateTotals);
        $(document).on('input', 'input[name$="_discount"]', calculateTotals);
        
        // // Form submit handler for debugging
        // $('#bidForm').on('submit', function(e) {
        //     // e.preventDefault(); // Uncomment for testing
        //     console.log('Form data being submitted:', $(this).serialize());
        // });
        
        // Initialize the form
        initializeForm();
    });

    // Confirmation dialog
    function confirmAddAnalysis() {
        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to add this bid analysis?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Add',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#bidForm').submit();
            }
        });
    }
</script>
@endpush