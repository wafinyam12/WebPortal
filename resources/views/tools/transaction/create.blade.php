@extends('layouts.admin', [
    'title' => 'Delivery Note Management'   
])

@push('css')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
@endpush

@section('main-content')
    <h1 class="h3 mb-0 text-gray-800">{{ __('Create Delivery Note') }}</h1>
    <p class="mb-4">
        This page is used to create delivery note.
    </p>

    <div class="card shadow">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h4 class="m-0 font-weight-bold text-primary">Delivery Note Details</h4>
                <a href="{{ route('transactions.index') }}" class="btn btn-primary">
                    <i class="fas fa-reply fa-sm"></i> Back
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('transactions.store') }}" method="POST" id="deliveryNoteForm">
                    @csrf
                    @method('POST')
                    <div class="row mb-4">
                        <!-- Delivery Type -->
                        <div class="col-12 mb-3">
                            <label for="type_delivery">Type of Delivery <span class="text-danger">*</span></label>
                            <select name="type_delivery" class="form-control @error('type_delivery') is-invalid @enderror" id="type_delivery">
                                <option value="">Select Type Delivery</option>
                                <option value="Delivery Note" selected>Delivery Note</option>
                                <option value="Transfer">Transfer</option>
                                <option value="Return">Return</option>
                            </select>
                        </div>
    
                        <!-- Driver Info -->
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="driver_name">Driver Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('driver_name') is-invalid @enderror" id="driver_name" name="driver_name" placeholder="Driver Name" value="{{ old('driver_name') }}" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="source_project_id">Source Project <span class="text-danger">*</span></label>
                                <select name="source_project_id" id="source_project_id" class="form-control @error('source_project_id') is-invalid @enderror">
                                    <option disabled selected>Select Source Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }} - {{ $project->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="driver_phone" class="form-label">Driver Phone <span class="text-danger">*</span></label>   
                                <input type="text" class="form-control @error('driver_phone') is-invalid @enderror" id="driver_phone" name="driver_phone" placeholder="Driver Phone" value="{{ old('driver_phone') }}" required>
                            </div>
                            <div class="text-center mt-4">
                                <i class="fas fa-arrow-right fa-4x text-primary"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="delivery_date" class="form-label">Delivery Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('delivery_date') is-invalid @enderror" id="delivery_date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" required autocomplete="off">
                            </div>
                            <div class="form-group">
                                <label for="destination_project_id">Destination Project <span class="text-danger">*</span></label>
                                <select name="destination_project_id" id="destination_project_id" class="form-control @error('destination_project_id') is-invalid @enderror">
                                    <option disabled selected>Select Destination Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }} - {{ $project->code }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
    
                    <!-- QR Scanner -->
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <div id="reader" class="d-none border rounded p-3"></div>
                        </div>
                    </div>
    
                    <!-- Tools Table -->
                    <div class="form-group">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tools-table">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th width="20%">Tool Code</th>
                                        <th>Last Location</th>
                                        <th width="10%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>        
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-info mt-2" id="add-row-scan">
                            <i class="fas fa-camera"></i> Scan Tool
                        </button>
                    </div>
    
                    <!-- Notes -->
                    <div class="form-group">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" placeholder="Enter notes (optional)" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </form>
                <div class="text-right">
                    <button type="button" class="btn btn-success" onclick="confirmAddTransaction()">
                        <i class="fas fa-truck-moving"></i> Create Delivery Note
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script>
// DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {

    // Initialize variables
    const scannedItems = new Set();
    const reader = document.getElementById('reader');
    const addRowScanBtn = document.getElementById('add-row-scan');
    const toolsTableBody = document.querySelector('#tools-table tbody');
    let html5QrCode = null;

    // Handle QR Scanner
    addRowScanBtn.addEventListener('click', async function() {
        if (!navigator.geolocation) {
            showError("Geolocation is not supported by this browser.");
            return;
        }

        try {
            const position = await getCurrentPosition();
            const location = await reverseGeocode(position.coords);
            startQrCodeScanner(location);
        } catch (error) {
            showError("Error accessing location: " + error.message);
        }
    });

    // Get current position
    function getCurrentPosition() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject);
        });
    }

    // Reverse geocode coordinates
    async function reverseGeocode(coords) {
        try {
            const response = await fetch(
                `https://nominatim.openstreetmap.org/reverse?lat=${coords.latitude}&lon=${coords.longitude}&format=json`
            );
            if (!response.ok) throw new Error('Geocoding failed');
            const data = await response.json();
            return data.display_name || `${coords.latitude}, ${coords.longitude}`;
        } catch (error) {
            console.error("Geocoding error:", error);
            return `${coords.latitude}, ${coords.longitude}`;
        }
    }

    // Start QR Scanner
    function startQrCodeScanner(location) {
        reader.classList.remove('d-none');
        
        if (html5QrCode === null) {
            html5QrCode = new Html5Qrcode("reader");
        }

        html5QrCode.start(
            { facingMode: "environment" },
            {
                fps: 10,
                qrbox: { width: 250, height: 250 }
            },
            (decodedText) => handleSuccessfulScan(decodedText, location),
            (error) => console.warn("QR Code scanning error:", error)
        ).catch(error => {
            showError("Failed to start scanner: " + error);
            reader.classList.add('d-none');
        });
    }

    // Handle successful QR scan
    function handleSuccessfulScan(decodedText, location) {
        stopScanner().then(() => {
            if (scannedItems.has(decodedText)) {
                showError("This tool has already been scanned!");
                return;
            }

            scannedItems.add(decodedText);
            addToolToTable(decodedText, location);
        });
    }

    // Stop QR Scanner
    function stopScanner() {
        if (html5QrCode && html5QrCode.isScanning) {
            return html5QrCode.stop().then(() => {
                reader.classList.add('d-none');
            });
        }
        return Promise.resolve();
    }

    // Add tool to table
    function addToolToTable(code, location) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${code}</td>
            <td>${location}</td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-circle" onclick="removeToolRow(this, '${code}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        toolsTableBody.appendChild(row);
        
        // Add hidden input for form submission
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tools[]';
        input.value = JSON.stringify({ code, location });
        document.getElementById('deliveryNoteForm').appendChild(input);
    }

    // Remove tool row
    window.removeToolRow = function(button, code) {
        const row = button.closest('tr');
        row.remove();
        scannedItems.delete(code);
        
        // Remove hidden input
        const input = document.querySelector(`input[value='${JSON.stringify({ code, location, quantity })}']`);
        if (input) input.remove();
    };

    // Show error message
    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: message,
            timer: 3000,
            showConfirmButton: false
        });
    }
});

// Confirm transaction creation
function confirmAddTransaction() {
    const form = document.getElementById('deliveryNoteForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    Swal.fire({
        title: 'Confirm Creation',
        text: "Are you sure you want to create this delivery note?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, Create it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
}

</script>
@endpush