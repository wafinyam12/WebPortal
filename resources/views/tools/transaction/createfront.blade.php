@extends('layouts.auth', [
    'title' => 'Scan Delivery Note'
])

@section('main-content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white text-center">
                    <h4 class="mb-0">Scan Delivery Note</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('transactions.store') }}" method="POST" id="deliveryNoteForm">
                        @csrf
                        <!-- QR Reader Section -->
                        <div id="reader" class="d-none text-center mb-3"></div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="codeEmploye">Code Pegawai</label>
                                    <input type="text" class="form-control" id="codeEmploye" name="codeEmploye" placeholder="Code Pegawai" required>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="type_delivery">Select Type</label>
                                    <select name="type_delivery" id="type_delivery" class="form-control">
                                        <option value="">-- Select Type Delivery --</option>
                                        <option value="Delivery Note">Delivery Note</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="Return">Return</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="driver_name">Driver Name</label>
                                    <input type="text" class="form-control" id="driver_name" name="driver_name" placeholder="Driver Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="source_project_id">Source Project</label>
                                    <select name="source_project_id" id="source_project_id" class="form-control">
                                        <option value="" disabled selected>--Select Source Project--</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }} - {{ $project->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="driver_phone">Driver Phone</label>
                                    <input type="text" class="form-control" id="driver_phone" name="driver_phone" placeholder="Driver Phone" required>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <span>
                                        <i class="fas fa-arrow-right text-primary  fa-3x mt-4"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_date">Delivery Date</label>
                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="destination_project_id">Destination Project</label>
                                    <select class="form-control" id="destination_project_id" name="destination_project_id" required>
                                        <option value="">-- Select Destination Project --</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }} - {{ $project->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group">
                            <label for="notes">Note</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any additional notes (optional)"></textarea>
                        </div>

                        <!-- Items Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tools-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Tool Code</th>
                                        <th>Last Location</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Dynamic Rows -->
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="{{ url('/') }}" class="btn btn-info mr-2">
                            <i class="fas fa-reply"></i> Back
                        </a>
                        <button type="button" class="btn btn-success mr-2" id="startScan">
                            <i class="fas fa-qrcode"></i> Start Scan
                        </button>
                        <button type="button" class="btn btn-primary" onclick="saveAndPrint()">
                            <i class="fas fa-save"></i> Save & Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

document.addEventListener('DOMContentLoaded', function() {

    // Initialize variables
    const scannedItems = new Set();
    const reader = document.getElementById('reader');
    const addRowScanBtn = document.getElementById('startScan');
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

    // Reverse Geocoding
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

    // Handle successful scan
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
})

function saveAndPrint() {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be save and print this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, save it!'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deliveryNoteForm').submit();

            // Print the page
            // window.print();
        }
    });
}

</script>
@endpush