@extends('layouts.admin', [
    'title' => 'Asuransi Project',
])

@push('css')
    <!-- Custom styles for this page -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <!-- CSS Date Range Picker -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.css">
@endpush




@section('main-content')
    <h1 class="h3 mb-4 text-gray-800">Asuransi Project</h1>
    <p class="mb-4">
        This page is used to manage asuransi project.
    </p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary d-flex justify-content-center ">
            <h4 class="m-0 font-weight-bold text-white">List Asuransi Project</h4>
        </div>
        <div class="card-header py-3">
            <form action="{{ route('asuransi-project.store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="project1" class="form-label">Project</label>
                            <input type="text" class="form-control" id="project1" name="project1" data-toggle="modal"
                                data-target="#projectModal" placeholder="Project Yang ada" required>
                            <input type="hidden" class="form-control" id="project_id" name="project_id">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Name" class="form-label">Nama</label>
                            <input type="text
                            " class="form-control" id="name"
                                name="name" placeholder="Nama Asuransi" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Tanggal Mulai" class="form-label">Tanggal Mulai</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
                                placeholder="Tanggal Mulai" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="masa_berlaku" class="form-label">Masa Berlaku</label>
                            <input type="text" class="form-control" id="masa_berlaku" name="masa_berlaku"
                                placeholder="dd-mm-yyyy - dd-mm-yyyy" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tempo" class="form-label">Tanggal jatuh tempo</label>
                            <input type="date" class="form-control" id="tanggal_jatuh_tempo" name="tanggal_jatuh_tempo"
                                placeholder="Tanggal jatuh tempo" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Status" class="form-label">Status</label>
                            <input type="text" class="form-control" id="status" name="status" placeholder="Status"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="Catatan" class="form-label">Catatan</label>
                            <input type="text" class="form-control" id="catatan" name="catatan" placeholder="catatan"
                                required>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th>Name</th>
                            <th>Project</th>
                            <th>Tanggal Mulai</th>
                            <th>Masa Berlaku</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($asuransi as $asuransis)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $asuransis->name }}</td>
                                <td>{{ $asuransis->project->name }}</td>
                                <td>{{ $asuransis->tanggal_mulai }}</td>
                                <td>{{ $asuransis->masa_berlaku }}</td>
                                <td>{{ $asuransis->tanggal_jatuh_tempo }}</td>
                                <td>{{ $asuransis->status }}</td>
                                <td>{{ $asuransis->catatan }}</td>
                                <td class="text-center">
                                    <a href=""
                                        class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>
                                    <form action="" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No data available</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectModalLabel">List Project</h5>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close"
                        style="right: 10px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Name</th>
                                <th>Action</th> <!-- Fix Typo -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($project as $projects)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="project-name">{{ $projects->name }}</td>
                                    <td class="project-id" hidden>{{ $projects->id }}</td>
                                    <td>
                                        <button type="button" class="btn btn-primary pilih-project"
                                            data-project="{{ $projects->name }}" data-id="{{ $projects->id }}"
                                            data-dismiss="modal">
                                            Pilih
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Moment.js (Dependency for Date Range Picker) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>

    <!-- Date Range Picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>


    <!-- DataTables -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {

            // Inisialisasi DataTables
            $('#dataTable').DataTable();

            $('#masa_berlaku').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    format: 'DD-MM-YYYY',
                    applyLabel: "Pilih",
                    cancelLabel: "Batal"
                }
            });

            // Saat tanggal dipilih, isi input
            $('#masa_berlaku').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format(
                    'DD-MM-YYYY'));
            });

            // Saat tombol batal diklik, kosongkan input
            $('#masa_berlaku').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });
            // Event klik pada tombol "Pilih"
            $(document).on('click', '.pilih-project', function() {
                var projectName = $(this).data('project');
                var projectId = $(this).data('id');
                console.log(projectName);
                $('#project1').val(projectName);
                $('#project_id').val(projectId);
            });

        });
    </script>
@endpush
