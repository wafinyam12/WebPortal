@extends('layouts.admin', [
    'title' => 'Tools Management',
])

@push('css')
    <!-- Custom styles for this page -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable();
        });
    </script>
@endpush

@section('main-content')
    <h1 class="h3 mb-2 text-gray-800">Petty Cash</h1>
    <p class="mb-4">
        This page is used Input record pettycash.
    </p>

    <div class="card shadow mb-4">
        <div class="card-header ">
            <label for="tanggal">Tanggal awal</label>
            <input type="date" class="form-control" id="tanggal_start" name="tanggal_start" value="{{ $start }}"
                required>
            <label for="tanggal">Tanggal akhir</label>
            <input type="date" class="form-control" id="tanggal_end" name="tanggal_end" value="{{ $end }}"
                required><br>
            <input type="hidden" name="name1" id="name1" value="{{ $id }}">
            @can('export petty cash')
                <button type="button" onclick="exportExcel()"class="btn btn-primary btn-md" style="margin-right: 10px;">
                    <i class="fas fa-file-export"></i> Download Report
                </button>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="15%">Nama</th>
                            <th width="15%">Tanggal</th>
                            <th width="15%">Tanggal Nota</th>
                            <th width="15%">SKU</th>
                            <th width="15%">COA</th>
                            <th width="15%">Description</th>
                            <th width="15%">Debet</th>
                            <th width="15%">Kredit</th>
                            <th width="15%">Nama File</th>
                            <th width="15%">Status Budget Control</th>
                            <th width="15%">Status AP</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            @if ($item->debet > 0)
                                <tr id="list_{{ $item->id }}" style="background-color: lightgreen">
                                    <td class="center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->owner->fullName }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->tanggal_nota }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->coa }}</td>
                                    <td class="truncate-text" data-fulltext="{{ $item->keterangan }}">
                                        {{ \Illuminate\Support\Str::limit($item->keterangan, 50, '...') }}
                                    </td>
                                    <td>{{ $item->debet ?? 0 }}</td>
                                    <td>{{ $item->kredit ?? 0 }}</td>
                                    <td>
                                        <a href="{{ asset('storage/pettycash/' . $item->file_name) }}"
                                            data-lightbox="pettycash">

                                            <img src="{{ asset('storage/pettycash/' . $item->file_name) }}" width="100"
                                                height="100">
                                        </a>
                                    </td>
                                    <td id="status_budget_control_{{ $item->id }}">{{ $item->status_budget_control }}
                                        &nbsp;
                                        {{ $item->approved_date }} &nbsp; {{ $item->approved_by }}
                                    </td>
                                    <td id="status_ap_{{ $item->id }}">{{ $item->status_ap }} &nbsp;
                                        {{ $item->approved_ap_date }} &nbsp; {{ $item->approved_ap_by }}
                                    </td>
                                </tr>
                            @else
                                <tr id="list_{{ $item->id }}">
                                    <td class="center">{{ $loop->iteration }}</td>
                                    <td>{{ $item->owner->fullName }}</td>
                                    <td>{{ $item->tanggal }}</td>
                                    <td>{{ $item->tanggal_nota }}</td>
                                    <td>{{ $item->sku }}</td>
                                    <td>{{ $item->coa }}</td>
                                    <td class="truncate-text" data-fulltext="{{ $item->keterangan }}">
                                        {{ \Illuminate\Support\Str::limit($item->keterangan, 50, '...') }}
                                    </td>
                                    <td>{{ $item->debet ?? 0 }}</td>
                                    <td>{{ $item->kredit ?? 0 }}</td>
                                    <td>
                                        <a href="{{ asset('storage/pettycash/' . $item->file_name) }}"
                                            data-lightbox="pettycash">

                                            <img src="{{ asset('storage/pettycash/' . $item->file_name) }}" width="100"
                                                height="100">
                                        </a>
                                    </td>
                                    <td id="status_budget_control_{{ $item->id }}">{{ $item->status_budget_control }}
                                        &nbsp;
                                        {{ $item->approved_date }} &nbsp; {{ $item->approved_by }}
                                    </td>
                                    <td id="status_ap_{{ $item->id }}">{{ $item->status_ap }} &nbsp;
                                        {{ $item->approved_ap_date }} &nbsp; {{ $item->approved_ap_by }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    // Export Excel
    <script>
        function exportExcel() {
            var startDate = document.getElementById('tanggal_start').value;
            var endDate = document.getElementById('tanggal_end').value;
            var user = document.getElementById('name1').value;

            if (!startDate || !endDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Start date and end date are required.',
                    confirmButtonText: 'OK'
                });
                return;
            }

            window.location.href = `/petty/petty-cash-print?tanggal_start=${startDate}&tanggal_end=${endDate}&name1=${user}`;
        }
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

                    if (statusBudgetControl.includes("rejected") || statusAP.includes("rejected")) {
                        row.style.backgroundColor = "red";
                        row.style.color = "white"; // Supaya teks tetap terbaca
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
    </script>
@endpush
