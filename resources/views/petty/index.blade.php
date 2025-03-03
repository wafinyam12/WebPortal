@extends('layouts.admin', [
    'title' => 'Petty Cash Management',
])

@push('css')
    <!-- Custom styles for this page -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('main-content')
    <h1 class="h3 mb-2 text-gray-800">Petty Cash</h1>
    <p class="mb-4">
        This page is used Input record pettycash.
    </p>

    
    <div class="card shadow mb-4">
        <div class="card-header py-3 text-white d-flex justify-content-between align-items-center">
            <h5 class="mr-4 font-weight-bold text-success">Saldo: Rp.{{ number_format($saldo, 2, ',', '.') }}</h5>
            {{-- <h5 class="mr-4 font-weight-bold text-primary">Debit: Rp.{{ number_format($debit, 2, ',', '.') }}</h5>
            <h5 class="mr-4 font-weight-bold text-danger">Kredit: Rp.{{ number_format($kredit, 2, ',', '.') }}</h5> --}}
            <div class="d-flex align-items-center flex-wrap">
                @can('add saldo')
                <button type="button" class="btn btn-primary btn-md mr-2" data-toggle="modal" data-target="#addSaldoModal">
                    <i class="fas fa-plus"></i> Add Saldo
                </button>
                @endcan
                @can('show petty cash')
                    <button type="button" class="btn btn-primary btn-md mr-2" data-toggle="modal" data-target="#addReportModal">
                        <i class="fas fa-file-export"></i> Report
                    </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            @can('create petty cash')
            <form action="{{ route('petty-cash.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nama_file" class="form-label">No Nota</label>
                            <input type="text" class="form-control" id="no_nota" name="no_nota"
                                placeholder="{{ $nota }}" ondblclick="fillInput()" required>
                        </div>
                        <div class="form-group">
                            <label for="tanggal" class="form-label">Tanggal Nota</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="nama_file" class="form-label">Nama File</label>
                            <input type="file" class="form-control" id="nama_file" name="nama_file" accept="image/*" required>
                            <p class="text-danger">*Format .jpg .jpeg .png Max 5MB</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">Tambah Transaksi</button>
                </div>
            </form>
            @endcan
            <hr>

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="10%">No Nota</th>
                                    <th width="15%">Nama</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="15%">Tanggal Nota</th>
                                    <th width="15%">SKU</th>
                                    <th width="15%">COA</th>
                                    <th width="15%">Description</th>
                                    <th width="15%">Debet</th>
                                    <th width="15%">Kredit</th>
                                    <th width="15%">Nama File</th>
                                    <th width="15%">Status AP</th>
                                    <th width="15%">Status Budget Control</th>
                                    <th width="15%">Action</th>
                                    <th width="15%">Alasan Menolak</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pettycash as $item)
                                <tr id="list_{{ $item->id }}">
    
                                    <td>{{ $item->no_nota }}</td>
                                    <td>{{ $item->owner->fullName }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->tanggal_nota }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->coa }}</td>
                                    <td class="truncate-text" data-fulltext="{{ $item->keterangan }}">
                                        {{ \Illuminate\Support\Str::limit($item->keterangan, 50, '...') }}
                                    </td>
                                    {{-- <td>Rp {{ number_format($item->debet ?? 0, 2, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->kredit ?? 0, 2, ',', '.') }}</td> --}}
                                    <td>
                                        <a href="{{ asset('storage/storage/pattycash/' . $item->file_name) }}"
                                            data-lightbox="pettycash">
    
                                            <img src="{{ asset('storage/storage/pattycash/' . $item->file_name) }}" width="100"
                                                height="100">
                                        </a>
                                    </td>
                                    <td id="status_ap_{{ $item->id }}">{{ $item->status_ap }} &nbsp;
                                        {{ $item->approved_ap_date }} &nbsp; {{ $item->approved_ap_by }}
                                    </td>
                                    <td id="status_budget_control_{{ $item->id }}">{{ $item->status_budget_control }}
                                        &nbsp;
                                        {{ $item->approved_date }} &nbsp; {{ $item->approved_by }}
                                    </td>
    
    
                                    <td class="text-center">
                                        @can('approve petty cash')
                                            @if (!in_array($item->status_ap, ['Rejected']) && !in_array($item->status_budget_control, ['Done', 'Rejected']))
                                                <form action="{{ route('petty-cash.approve', $item->id) }}" method="POST">
                                                    @csrf
                                                    <button class="btn btn-success mb-2" name="approve{{ $item->id }}"
                                                        id="approve{{ $item->id }}" type="submit">
                                                        Approve
                                                    </button>
                                                </form>
    
                                                <form action="{{ route('petty-cash.reject', $item->id) }}" method="POST"
                                                    id="reject-{{ $item->id }}">
                                                    @csrf
                                                    <input type="hidden" name="reason" id="rejectReason-{{ $item->id }}">
                                                    <button class="btn btn-danger" name="reject{{ $item->id }}"
                                                        onclick="confirmReject({{ $item->id }})" type="button">
                                                        Reject
                                                    </button>
                                                </form>
                                            @endif
                                        @endcan
                                    </td>
                                    <td class="truncate-text" data-fulltext="{{ $item->reject_reason }}">
                                        {{ \Illuminate\Support\Str::limit($item->reject_reason, 50, '...') }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    {{-- add saldo --}}
    <div class="modal fade" id="addSaldoModal" tabindex="-1" role="dialog" aria-labelledby="addSaldoModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSaldoModalLabel">Add Saldo</h5>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close"
                        style="right: 10px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('petty-cash.saldo') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="transaksi" class="form-label">Transaksi</label>
                                <input type="text" class="form-control" id="transaksi" name="transaksi" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for='name' class="form-label">Name</label>
                                <select class="form-control" name="name" id="name">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->fullName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="debet" class="form-label">Uang Masuk</label>
                                <input type="number" class="form-control" id="debet" name="debet">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="nama_file" class="form-label">Nama File</label>
                                <input type="file" class="form-control" id="nama_file" name="nama_file" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- modal report --}}
    <div class="modal fade" id="addReportModal" tabindex="-1" aria-labelledby="addReportModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReportModalLabel">Report Petty Cash</h5>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close"
                        style="right: 10px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('petty-cash.report') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for='name' class="form-label">Name</label>
                                <select class="form-control" name="name" id="name">
                                    @foreach ($pettycash->unique('owner_id') as $user)
                                        <option value="{{ $user->owner->id }}">{{ $user->owner->fullName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Ok</button>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
            $('.js-example-basic-multiple').select2();

            // Ketika form disubmit
            $('form').on('submit', function() {
                let selectedValues = $('.js-example-basic-multiple').val(); // Ambil nilai yang dipilih
                if (selectedValues) {
                    let formattedString = selectedValues.map((val, index) => {
                        let [sku, keterangan] = val.split('-'); // Pisahkan SKU dan keterangan
                        return `${index + 1}).  ${sku} - ${keterangan}`; // Format data
                    }).join("\n");

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'formatted_states',
                        value: formattedString
                    }).appendTo(this);
                }
            });

            $('form').on('submit', function() {
                let selectedValues = $('.js-example-basic-multiple').val(); // Ambil nilai yang dipilih
                if (selectedValues) {
                    let skuValues = selectedValues.map((val) => {
                        let [sku, keterangan] = val.split('-'); // Pisahkan SKU dan keterangan
                        return sku; // Ambil hanya SKU
                    }).join("\n");

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'sku_values', // Nama input untuk SKU
                        value: skuValues // Nilai hanya SKU
                    }).appendTo(this);
                }
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('tr[id^="list_"]').forEach(row => {
                let id = row.id.split("_")[1]; // Ambil ID item dari tr

                let statusBudgetControlEl = document.getElementById(`status_budget_control_${id}`);
                let statusAPEl = document.getElementById(`status_ap_${id}`);

                if (statusBudgetControlEl && statusAPEl) { // Pastikan elemen ditemukan
                    let statusBudgetControl = statusBudgetControlEl.textContent.trim().toLowerCase();
                    let statusAP = statusAPEl.textContent.trim().toLowerCase();

                    // Jika salah satu status adalah "Rejected", warnai keduanya
                    if (statusBudgetControl.includes("rejected") || statusAP.includes("rejected")) {
                        statusBudgetControlEl.style.backgroundColor = "red";
                        statusBudgetControlEl.style.color = "white"; // Supaya teks tetap terbaca

                        statusAPEl.style.backgroundColor = "red";
                        statusAPEl.style.color = "white"; // Supaya teks tetap terbaca
                    }
                }
            });
        });


        document.querySelectorAll('.truncate-text').forEach(td => {
            td.addEventListener('mouseover', function() {
                this.textContent = this.getAttribute('data-fulltext');
            });
            td.addEventListener('mouseout', function() {
                this.textContent = this.getAttribute('data-fulltext').substring(0, 50) + '...';
            });
        });

        function confirmReject(id) {
            Swal.fire({
                title: 'Reject Reimbursement',
                text: 'Please provide a reason for rejection:',
                input: 'text',
                inputPlaceholder: 'Enter reason here...',
                showCancelButton: true,
                confirmButtonText: 'Submit',
                confirmButtonColor: '#d33',
                cancelButtonText: 'Cancel',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Reason is required!';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Action if confirmed and send data
                    $('#rejectReason-' + id).val(result.value);
                    document.getElementById('reject-' + id).submit();
                } else if (result.isDismissed) {
                    // Action if dismissed
                    Swal.fire('You cancelled the input.', '', 'info');
                }
            });
        }
        
        function fillInput() {
            let input = document.getElementById("no_nota");
            if (!input.value) { // Jika input masih kosong
                input.value = input.placeholder;
            }
        }
    </script>
@endpush
