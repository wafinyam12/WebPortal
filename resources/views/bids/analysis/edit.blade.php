@extends('layouts.admin', [
    'title' => 'Edit Bids Analysis'
])

@push('css')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
<h1 class="h3 mb-4 text-gray-800">Edit Bids Analysis</h1>
<p class="mb-4">
    This page is used to edit bids analysis.
</p>

<!-- Edit Bids Analysis -->
<div class="card shadow mb-4">
    <div class="card border-left-primary shadow h-100 py-2">
        <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
            <h4 class="m-0 font-weight-bold text-primary">Edit Bids Analysis</h4>
            <a href="{{ route('bids-analysis.index') }}" class="btn btn-primary btn-md mr-2">
                <i class="fas fa-reply"></i> 
                Back
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('bids-analysis.update', $bidAnalysis->id) }}" method="POST" id="bidForm">
                @csrf
                @method('PUT')

                <!-- Bid Information -->
                <div class="form-group">
                    <label for="branch">Branch</label>
                    <select name="branch" id="branch" class="form-control">
                        <option value="" disabled selected>Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }} " {{ $branch->id == $bidAnalysis->branch_id ? 'selected' : '' }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Project Name</label>
                            <input type="text" class="form-control" name="project_name" value="{{ $bidAnalysis->project_name }}" placeholder="Project Name" required>
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="bid_date" value="{{ $bidAnalysis->bid_date }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Number of Vendors Bids</label>
                            <select class="form-control" id="vendorCount" name="vendor_count">
                                @for($i = 2; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ count($bidAnalysis->vendors) == $i ? 'selected' : '' }}>
                                      Bids {{ $i }} Vendors
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
    
                <!-- Vendor Names -->
                <div class="row mb-4" id="vendorNamesContainer">
                    <!-- Vendor name inputs will be dynamically added here -->
                </div>
    
                <!-- Items Table -->
                <div class="table-responsive mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="font-weight-bold">Items</h6>
                        <button type="button" class="btn btn-primary btn-sm m-1" id="addItem">
                            <i class="fas fa-plus-circle mr-1"></i> Add Item
                        </button>
                    </div>
                    <table class="table table-bordered" id="bidsTable">
                        <thead>
                            <tr id="headerRow">
                                <th rowspan="2" class="align-middle">No</th>
                                <th rowspan="2" class="align-middle">Description</th>
                                <th rowspan="2" class="align-middle">QTY</th>
                                <th rowspan="2" class="align-middle">UOM</th>
                                <!-- Vendor columns will be added dynamically -->
                                <th rowspan="2" class="align-middle">Action</th>
                            </tr>
                            <tr id="subHeaderRow">
                                <!-- Price/Unit and Total columns will be added dynamically -->
                            </tr>
                        </thead>
                        <tbody id="itemsContainer">
                            <!-- Item rows will be added here -->
                        </tbody>
                        <tfoot id="tableFooter">
                            <!-- Footer rows will be added dynamically -->
                        </tfoot>
                    </table>
                    <div class="form-group">
                        <label for="selected">Selected Vendor</label>
                        <select class="form-control" id="selected-vendor" name="selected_vendor">
                            <!--- selected will be added dynamically vendors -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Notes">{{ $bidAnalysis->notes }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Attachment</label>
                        <input type="file" name="file" id="file" class="form-control" accept="application/pdf">
                    </div>
                    <!-- Tampilkan tautan file jika file sebelumnya ada -->
                    @if (!empty($bidAnalysis->attachment))
                        <a href="{{ asset('storage/bids/attachment/' . $bidAnalysis->attachment) }}" target="_blank" class="mt-2 d-block">
                            Lihat File Saat Ini
                        </a>
                    @endif
                </div>
            </form>
            <div class="float-right">
                <button type="button" class="btn btn-primary" onclick="confirmUpdateAnalysis()">
                    <i class="fas fa-save"></i> Save Update
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        let vendors = [];
        const existingBidAnalysis = @json($bidAnalysis);
        
        // Initialize the form
        function initializeForm() {
            const vendorCount = parseInt($('#vendorCount').val());
            vendors = [];
            
            // Clear existing vendor inputs
            $('#vendorNamesContainer').empty();
            
            // Add vendor name inputs
            for(let i = 0; i < vendorCount; i++) {
                const vendor = existingBidAnalysis.vendors[i] || {};
                const vendorInput = `
                    <div class="col-md-${12/vendorCount}">
                        <div class="form-group">
                            <label>Vendor ${i + 1} Name</label>
                            <input type="text" class="form-control vendor-name" 
                                name="vendor_names[]" 
                                value="${vendor.name || ''}"
                                data-vendor-index="${i}"
                                placeholder="Vendor ${i + 1} Name"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Vendor ${i + 1} Email</label>
                            <input type="email" class="form-control" 
                                name="vendor_emails[]"
                                value="${vendor.email || ''}"
                                placeholder="Vendor ${i + 1} Email"
                                required>
                        </div>
                        <div class="form-group">
                            <label>Vendor ${i + 1} Phone</label>
                            <input type="text" class="form-control" 
                                name="vendor_phones[]"
                                value="${vendor.phone || ''}"
                                placeholder="Vendor ${i + 1} Phone"
                                required>
                        </div>
                    </div>`;
                $('#vendorNamesContainer').append(vendorInput);
            }
            
            updateTableStructure();
            loadExistingItems();
            updateSelectedVendorDropdown(); // Add this line to initialize the dropdown
        }

        // Update selected vendor dropdown
        function updateSelectedVendorDropdown() {
            const $select = $('#selected-vendor');
            $select.empty();
            
            // Add default option
            $select.append('<option value="">Select Vendor</option>');
            
            // get selected vendor
            const selectedVendor = existingBidAnalysis.selected_vendor;
            
            // Add vendor options
            vendors.forEach((vendor, index) => {
                const vendorName = vendor.name.trim() || `Vendor ${index + 1}`;
                $select.append(`<option value="${vendorName}" ${selectedVendor === vendorName ? 'selected' : ''}>${vendorName}</option>`);
            });
        }
        
        function loadExistingItems() {
            $('#itemsContainer').empty();
            
            existingBidAnalysis.items.forEach((item, index) => {
                const rowHtml = createItemRow(index, item);
                $('#itemsContainer').append(rowHtml);
            });
            
            if (existingBidAnalysis.items.length === 0) {
                addInitialRow();
            }
            
            calculateTotals();
        }
        
        function createItemRow(index, item = null) {
            let rowHtml = `
                <tr class="item-row">
                    <td>${index + 1}</td>
                    <td>
                        <input type="text" class="form-control form-control-sm" 
                            name="items[${index}][description]" 
                            value="${item ? item.description : ''}"
                            placeholder="Item Description" required>
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm quantity" 
                            name="items[${index}][quantity]" 
                            value="${item ? item.quantity : ''}"
                            placeholder="Quantity" required>
                    </td>
                    <td>
                        <select class="form-control form-control-sm" name="items[${index}][uom]" required>
                            <option value="PCS" ${item && item.uom === 'PCS' ? 'selected' : ''}>PCS</option>
                            <option value="UNIT" ${item && item.uom === 'UNIT' ? 'selected' : ''}>UNIT</option>
                            <option value="SET" ${item && item.uom === 'SET' ? 'selected' : ''}>SET</option>
                        </select>
                    </td>`;
            
            vendors.forEach(vendor => {
                // Find the price for this vendor in cost_bids_analysis
                let vendorPrice = 0;
                if (item && item.cost_bids_analysis) {
                    const priceData = item.cost_bids_analysis.find(
                        analysis => analysis.cost_bids_vendor_id === existingBidAnalysis.vendors[vendor.index].id
                    );
                    vendorPrice = priceData ? priceData.price : 0;
                }

                rowHtml += `
                    <td>
                        <input type="number" class="form-control form-control-sm price vendor${vendor.index}-price" 
                            name="items[${index}][vendor${vendor.index}_price]" 
                            value="${vendorPrice}"
                            placeholder="Rp" required>
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
        
        // Update table structure based on vendor count
        function updateTableStructure() {
            const vendorCount = parseInt($('#vendorCount').val());
            
            // Update vendors array
            vendors = Array.from($('.vendor-name')).map(input => ({
                name: input.value || `Vendor ${$(input).data('vendor-index') + 1}`,
                index: $(input).data('vendor-index')
            }));
            
            // Update header
            let headerHtml = `
                <th rowspan="2" class="align-middle">No</th>
                <th rowspan="2" class="align-middle">Description</th>
                <th rowspan="2" class="align-middle">QTY</th>
                <th rowspan="2" class="align-middle">UOM</th>`;
                
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
            
            // Update footer structure
            updateFooterStructure();

            // Update selected vendor dropdown
            updateSelectedVendorDropdown();
        }
        
        // Update footer structure with existing values
        function updateFooterStructure() {
            let footerHtml = `
                <tr class="bg-light font-weight-bold">
                    <td colspan="4" class="text-right">TOTAL</td>`;
                    
                    vendors.forEach(vendor => {
                        const grandTotal = existingBidAnalysis.vendors[vendor.index]?.grand_total || 0;
                        footerHtml += `
                            <td colspan="2" id="vendor${vendor.index}-grand-total">
                                <span class="grand-total-text">${parseFloat(grandTotal).toLocaleString()}</span>
                                <input type="hidden"
                                name="grand_total${vendor.index}"
                                value="${grandTotal}">
                            </td>`;
                    });
            
            footerHtml += '<td></td></tr>';
            
            // Add discount row with existing values
            footerHtml += `
                <tr>
                    <td colspan="4" class="text-right">DISCOUNT (%)</td>`;
                    
            vendors.forEach(vendor => {
                const discount = existingBidAnalysis.vendors[vendor.index]?.discount || 0;
                footerHtml += `
                    <td colspan="2">
                        <input type="number" class="form-control form-control-sm" 
                            name="vendor${vendor.index}_discount" 
                            value="${discount}"
                            placeholder="Discount (%)">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';
            
            // Add final total row
            footerHtml += `
                <tr class="bg-light font-weight-bold">
                    <td colspan="4" class="text-right">TOTAL After Discount</td>`;
                    
            vendors.forEach(vendor => {
                const finalTotal = existingBidAnalysis.vendors[vendor.index]?.final_total || 0;
                footerHtml += `
                    <td colspan="2" id="vendor${vendor.index}-final-total">
                        <span class="final-total-text">${parseFloat(finalTotal).toLocaleString()}</span>
                        <input type="hidden" 
                        name="final_total${vendor.index}"
                        value="${finalTotal}">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';
            
            // Add terms of payment row with existing values
            footerHtml += `
                <tr>
                    <td colspan="4" class="text-right">Terms of Payment (Days)</td>`;
                    
            vendors.forEach(vendor => {
                const terms = existingBidAnalysis.vendors[vendor.index]?.terms_of_payment || '';
                footerHtml += `
                    <td colspan="2">
                        <input type="text" class="form-control form-control-sm" 
                            name="terms_of_payment_vendor${vendor.index}" 
                            value="${terms}"
                            placeholder="Terms of Payment (Days)">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';
            
            // Add lead time row with existing values
            footerHtml += `
                <tr>
                    <td colspan="4" class="text-right">Lead Time (Days)</td>`;
                    
            vendors.forEach(vendor => {
                const leadTime = existingBidAnalysis.vendors[vendor.index]?.lead_time || '';
                footerHtml += `
                    <td colspan="2">
                        <input type="text" class="form-control form-control-sm" 
                            name="lead_time_vendor${vendor.index}" 
                            value="${leadTime}"
                            placeholder="Lead Time (Days)">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';

            // Add Note with existing values
            footerHtml += `
                <tr>
                    <td colspan="4" class="text-right">Note</td>`;
                    
            vendors.forEach(vendor => {
                const note = existingBidAnalysis.vendors[vendor.index]?.notes || '';
                footerHtml += `
                    <td colspan="2">
                        <input type="text" class="form-control form-control-sm" 
                            name="notes_vendor${vendor.index}" 
                            value="${note}"
                            placeholder="Note">
                    </td>`;
            });
            
            footerHtml += '<td></td></tr>';
            
            $('#tableFooter').html(footerHtml);
        }
        
        // Event Handlers
        $('#vendorCount').change(initializeForm);
        
        $(document).on('input', '.vendor-name', updateTableStructure);
        
        $('#addItem').click(function() {
            const rowCount = $('.item-row').length;
            const newRow = createItemRow(rowCount);
            $('#itemsContainer').append(newRow);
            calculateTotals();
        });
        
        $(document).on('click', '.remove-item', function() {
            if ($('.item-row').length > 1) {
                $(this).closest('tr').remove();
                // reset index for remaining rows
                $('.item-row').each(function(index) {
                    $(this).find('td:first').text(index + 1);
                })
                calculateTotals();
            }
        });
        
        $(document).on('input', '.quantity, .price', calculateTotals);
        $(document).on('input', 'input[name$="_discount"]', calculateTotals);
        
        // Calculate totals
        function calculateTotals() {
            vendors.forEach(vendor => {
                let vendorTotal = 0;
                
                $('.item-row').each(function() {
                    const quantity = parseFloat($(this).find('.quantity').val()) || 0;
                    const price = parseFloat($(this).find(`.vendor${vendor.index}-price`).val()) || 0;
                    const rowTotal = quantity * price;
                    
                    $(this).find(`.vendor${vendor.index}-total`).text(rowTotal.toLocaleString());
                    vendorTotal += rowTotal;
                });
                
                // Update grand total text and hidden input
                $(`#vendor${vendor.index}-grand-total`).text(vendorTotal.toLocaleString());
                
                // Ensure the grand total hidden input exists and update its value
                if ($(`input[name="vendor${vendor.index}_grand_total"]`).length) {
                    $(`input[name="vendor${vendor.index}_grand_total"]`).val(vendorTotal);
                } else {
                    $(`#vendor${vendor.index}-grand-total`).append(
                        `<input type="hidden" name="grand_total[${vendor.index}]" value="${vendorTotal}">`
                    );
                }
                
                const discount = parseFloat($(`input[name="vendor${vendor.index}_discount"]`).val()) || 0;
                const finalTotal = vendorTotal * (1 - discount/100);
                
                // Update final total text and hidden input
                $(`#vendor${vendor.index}-final-total`).text(finalTotal.toLocaleString());
                
                // Ensure the final total hidden input exists and update its value
                if ($(`input[name="vendor${vendor.index}_final_total"]`).length) {
                    $(`input[name="vendor${vendor.index}_final_total"]`).val(finalTotal);
                } else {
                    $(`#vendor${vendor.index}-final-total`).append(
                        `<input type="hidden" name="final_total[${vendor.index}]" value="${finalTotal}">`
                    );
                }
            });
        }
        function addInitialRow() {
            const newRow = createItemRow(0);
            $('#itemsContainer').html(newRow);
            calculateTotals();
        }

        // Initialize the form when document is ready
        initializeForm();
    });

    function confirmUpdateAnalysis() {
        Swal.fire({
            title: 'Confirmation',
            text: 'Are you sure you want to update this bid analysis?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#bidForm').submit();
            }
        });
    }
</script>
@endpush
