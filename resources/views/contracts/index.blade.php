@extends('layouts.admin', [
    'title' => 'Contracts Management'
])

@push('css')
<!-- Custom styles for this page -->
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@section('main-content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">Contracts Management</h1>
    <p class="mb-4">
        This page is used to manage contracts.
    </p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap">
                <h4 class="m-0 font-weight-bold text-primary">List Contracts</h4>
                <div class="d-flex align-items-center flex-wrap">
                    @can('print contracts')
                    <input type="date" id="startDate" name="start_date" class="form-control mr-2 mb-2 w-auto" required>
                    <span class="mx-2">to</span>
                    <input type="date" id="endDate" name="end_date" class="form-control mx-2 mb-2 w-auto" required>   
                    <button type="button" onclick="exportExcel()" class="btn btn-success btn-md ml-2 mb-2">
                        <i class="fas fa-file-excel fa-md white-50"></i> Print Excel
                    </button>
                    @endcan
                    @can('create contracts')
                    <!-- Tombol Import Data Contracts -->
                    <button type="button" class="btn btn-warning btn-md ml-2 mb-2" data-toggle="modal" data-target="#importContractsModal">
                        <i class="fas fa-file-import fa-md white-50"></i> Import Contracts
                    </button>
                    <!-- Tombol Add Contracts -->
                    <button type="button" class="btn btn-primary btn-md ml-2 mb-2" data-toggle="modal" data-target="#addContractsModal">
                        <i class="fas fa-file-contract fa-md white-50"></i> Add Contracts
                    </button>
                    @endcan
                </div>
            </div> 
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Name Perusahaan</th>
                                <th>Status Kontrak</th>
                                <th>Status Proyek</th>
                                <th>Tanggal Kontrak</th>
                                <th>Masa Berlaku</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($contracts as $contract)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td>{{ $contract->code }}</td>
                                    <td>{{ $contract->name }}</td>
                                    <td>{{ $contract->nama_perusahaan }}</td>
                                    <td>{{ $contract->status_kontrak }}</td>
                                    <td>{{ $contract->status_proyek }}</td>
                                    <td>{{ $contract->tanggal_kontrak }}</td>
                                    <td>{{ $contract->masa_berlaku }}</td>
                                    <td class="text-center">
                                        <div class="d-inline-flex">
                                            @can('view contracts')
                                            <a href="{{ route('contract.show', $contract->id) }}" class="btn btn-info mr-2 btn-circle">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            @can('print contracts')
                                            <a href="{{ route('contract.export', $contract->id) }}" class="btn btn-success btn-circle mr-2">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @endcan
                                            @can('update contracts')
                                            <button type="button" class="btn btn-warning btn-circle mr-2" data-toggle="modal"
                                                data-id="{{ $contract->id }}"
                                                data-code="{{ $contract->code }}"
                                                data-name="{{ $contract->name }}"
                                                data-nama_perusahaan="{{ $contract->nama_perusahaan }}"
                                                data-nama_pekerjaan="{{ $contract->nama_pekerjaan }}"
                                                data-status_kontrak="{{ $contract->status_kontrak }}"
                                                data-jenis_pekerjaan="{{ $contract->jenis_pekerjaan }}"
                                                data-nominal_kontrak="{{ $contract->nominal_kontrak }}"
                                                data-tanggal_kontrak="{{ $contract->tanggal_kontrak }}"
                                                data-masa_berlaku="{{ $contract->masa_berlaku }}"
                                                data-status_proyek="{{ $contract->status_proyek }}"
                                                data-retensi="{{ $contract->retensi }}"
                                                data-masa_retensi="{{ $contract->masa_retensi }}"
                                                data-status_retensi="{{ $contract->status_retensi }}"
                                                data-pic_sales="{{ $contract->pic_sales }}"
                                                data-pic_pc="{{ $contract->pic_pc  }}"
                                                data-pic_customer="{{ $contract->pic_customer }}"
                                                data-mata_uang="{{ $contract->mata_uang }}"
                                                data-bast_1="{{ $contract->bast_1 }}"
                                                data-bast_1_nomor="{{ $contract->bast_1_nomor }}"
                                                data-bast_2="{{ $contract->bast_2 }}"
                                                data-bast_2_nomor="{{ $contract->bast_2_nomor }}"
                                                data-overall_status="{{ $contract->overall_status }}"
                                                data-kontrak_milik="{{ $contract->kontrak_milik }}"
                                                data-keterangan="{{ $contract->keterangan }}"
                                                data-memo="{{ $contract->memo }}"
                                                data-target="#editContractsModal">
                                                <i class="fas fa-pencil"></i>
                                            </button>
                                            @endcan
                                            @can('delete contracts')
                                            <form action="{{ route('contract.destroy', $contract->id) }}" method="post" id="deleteContractsForm-{{ $contract->id }}" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-circle" onclick="confirmDeleteContract({{ $contract->id }})">
                                                    <i class="fas fa-trash"></i>
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

    <!--- Modal Add Contracts --->
    <div class="modal fade" id="addContractsModal" tabindex="-1" role="dialog" aria-labelledby="addContractsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center">
                    <h4 class="modal-title font-weight-bold text-white" id="addContractsModalLabel">Create Contracts</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 10px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>                
                <div class="modal-body">
                    <form action="{{ route('contract.store') }}" method="post" id="addContractsForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required placeholder="Enter Code">
                                </div>
                                <div class="form-group">
                                    <label for="nama_perusahaan">Nama Perusahaan</label>
                                    <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" id="nama_perusahaan" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}" required placeholder="Enter Nama Perusahaan">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required placeholder="Enter Name">
                                </div>
                                <div class="form-group">
                                    <label for="nama_pekerjaan">Nama Pekerjaan / Proyek</label>
                                    <input type="text" class="form-control @error('nama_pekerjaan') is-invalid @enderror" id="nama_pekerjaan" name="nama_pekerjaan" value="{{ old('nama_pekerjaan') }}" required placeholder="Enter Nama Pekerjaan / Proyek">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status_kontrak">Status Kontrak</label>
                                    <input type="text" class="form-control @error('status_kontrak') is-invalid @enderror" id="status_kontrak" name="status_kontrak" value="{{ old('status_kontrak') }}" required placeholder="Enter Status Kontrak">
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_kontrak">Tanngal Kontrak</label>
                                    <input type="date" class="form-control @error('tanggal_kontrak') is-invalid @enderror" id="tanggal_kontrak" name="tanggal_kontrak" value="{{ old('tanggal_kontrak') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nominal_kontrak">Nominal Kontrak</label>
                                    <input type="number" class="form-control @error('nominal_kontrak') is-invalid @enderror" id="nominal_kontrak" name="nominal_kontrak" value="{{ old('nominal_kontrak') }}" placeholder="Enter Nominal Kontrak">
                                </div>
                                <div class="form-group">
                                    <label for="pic_sales">PIC Sales</label>
                                    <input type="text" class="form-control @error('pic_sales') is-invalid @enderror" id="pic_sales" name="pic_sales" value="{{ old('pic_sales') }}" placeholder="Enter PIC Sales">
                                </div>
                                <div class="form-group">
                                    <label for="bast_1">BAST-1 Tgl</label>
                                    <input type="date" class="form-control @error('bast_1') is-invalid @enderror" id="bast_1" name="bast_1" value="{{ old('bast_1') }}">
                                </div>
                                <div class="form-group">
                                    <label for="bast_1_nomor">BAST-1 Nomor</label>
                                    <input type="text" class="form-control @error('bast_1_nomor') is-invalid @enderror" id="bast_1_nomor" name="bast_1_nomor" value="{{ old('bast_1_nomor') }}" placeholder="Enter BAST-1 Nomor">
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keterangan" name="keterangan" placeholder="Enter Keterangan">{{ old('keterangan') }}</textarea>
                                </div>             
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jenis_pekerjaan">Jenis Pekerjaan</label>
                                    <select name="jenis_pekerjaan" id="jenis_pekerjaan" class="form-control @error('jenis_pekerjaan') is-invalid @enderror">
                                        <option value="" disabled selected>Select Jenis Pekerjaan</option>
                                        <option value="-">-</option>
                                        <option value="CO LICENSING">CO LICENSING</option>
                                        <option value="DISTRIBUTOR">DISTRIBUTOR</option>
                                        <option value="IZIN">IZIN</option>
                                        <option value="JASA">JASA</option> 
                                        <option value="SEWA">SEWA</option>
                                        <option value="KEMITRAAN">KEMITRAAN</option>
                                        <option value="KERJASAMA">KERJASAMA</option>
                                        <option value="KONSULTASI">KONSULTASI</option>
                                        <option value="PENGADAAN">PENGADAAN</option>
                                        <option value="PEMASANGAN">PEMASANGAN</option>
                                        <option value="LAYANAN JASA">LAYANAN JASA</option>
                                        <option value="PERJANJIAN">PERJANJIAN</option>
                                        <option value="SERTIFIKASI">SERTIFIKASI</option>
                                        <option value="PENGAMANAN">PENGAMANAN</option>
                                        <option value="PERBAIKAN">PERBAIKAN</option>
                                        <option value="LAINNYA">LAINNYA</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="masa_berlaku">Masa Berlaku</label>
                                    <input type="date" class="form-control @error('masa_berlaku') is-invalid @enderror" id="masa_berlaku" name="masa_berlaku" value="{{ old('masa_berlaku') }}">
                                </div>
                                <div class="form-group">
                                    <label for="mata_uang">Mata Uang</label>
                                    <select name="mata_uang" id="mata_uang" class="form-control @error('mata_uang') is-invalid @enderror">
                                        <option value="" disabled selected>Select Mata Uang</option>
                                        <option value="IDR">IDR</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="pic_pc">PIC PC</label>
                                    <input type="text" class="form-control @error('pic_pc') is-invalid @enderror" id="pic_pc" name="pic_pc" value="{{ old('pic_pc') }}" placeholder="Enter PIC PC">
                                </div>
                                <div class="form-group">
                                    <label for="bast_2">BAST-2 Tgl</label>
                                    <input type="date" class="form-control @error('bast_2') is-invalid @enderror" id="bast_2" name="bast_2" value="{{ old('bast_2') }}">
                                </div>
                                <div class="form-group">
                                    <label for="bast_2_nomor">B AST-2 Nomor</label>
                                    <input type="text" class="form-control @error('bast_2_nomor') is-invalid @enderror" id="bast_2_nomor" name="bast_2_nomor" value="{{ old('bast_2_nomor') }}" placeholder="Enter BAST-2 Nomor">
                                </div>
                                <div class="form-group">
                                    <label for="overall_status">Overall Status</label>
                                    <input type="text" class="form-control @error('overall_status') is-invalid @enderror" id="overall_status" name="overall_status" value="{{ old('overall_status') }}" placeholder="Enter Overall Status">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kontrak_milik">Kontrak Milik</label>
                                    <input type="text" class="form-control @error('kontrak_milik') is-invalid @enderror" id="kontrak_milik" name="kontrak_milik" value="{{ old('kontrak_milik') }}" placeholder="Enter Kontrak Milik">
                                </div>
                                <div class="form-group">
                                    <label for="status_proyek">Status Proyek</label>
                                    <select name="status_proyek" id="status_proyek" class="form-control @error('status_proyek') is-invalid @enderror">
                                        <option value="" disabled selected>Select Status Proyek</option>
                                        <option value="-">-</option>
                                        <option value="OPEN">OPEN</option>
                                        <option value="CLOSED">CLOSSED</option>
                                        <option value="PENDING">PENDING</option>
                                        <option value="CANCELLED">CANCELLED</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="pic_customer">PIC Customer</label>
                                    <input type="text" class="form-control @error('pic_customer') is-invalid @enderror" id="pic_customer" name="pic_customer" value="{{ old('pic_customer') }}" placeholder="Enter PIC Customer">
                                </div>
                                <div class="form-group">
                                    <label for="retensi">Retensi</label>
                                    <input type="text" class="form-control @error('retensi') is-invalid @enderror" id="retensi" name="retensi" value="{{ old('retensi') }}" placeholder="Enter Retensi">
                                </div>
                                <div class="form-group">
                                    <label for="masa_retensi">Masa Retensi</label>
                                    <input type="date" class="form-control @error('masa_retensi') is-invalid @enderror" id="masa_retensi" name="masa_retensi" value="{{ old('masa_retensi') }}">
                                </div>
                                <div class="form-group">
                                    <label for="status_retensi">Status Retensi</label>
                                    <select name="status_retensi" id="status_retensi" class="form-control @error('status_retensi') is-invalid @enderror">
                                        <option value="" disabled selected>Select Status Retensi</option>
                                        <option value="-">-</option>
                                        <option value="OPEN">OPEN</option>
                                        <option value="CLOSED">CLOSSED</option>
                                        <option value="PENDING">PENDING</option>
                                        <option value="CANCELLED">CANCELLED</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="memo">Memo</label>
                                    <textarea class="form-control @error('memo') is-invalid @enderror" id="memo" name="memo" placeholder="Enter Memo">{{ old('memo') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmAddContracts()"><i class="fas fa-check"></i> Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Contracts -->
    <div class="modal fade" id="editContractsModal" tabindex="-1" role="dialog" aria-labelledby="editContractsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex justify-content-center position-relative">
                    <h4 class="modal-title font-weight-bold text-white" id="editContractsModalLabel">Update Contracts</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 10px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>                
                <div class="modal-body">
                    <form action="{{ route('contract.update', ':id') }}" method="post" id="updateContractsForm">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" id="codeEdit" name="code" value="{{ old('code') }}" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="nama_perusahaan">Nama Perusahaan</label>
                                    <input type="text" class="form-control @error('nama_perusahaan') is-invalid @enderror" id="nama_perusahaanEdit" name="nama_perusahaan" value="{{ old('nama_perusahaan') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="nameEdit" name="name" value="{{ old('name') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nama_pekerjaan">Nama Pekerjaan / Proyek</label>
                                    <input type="text" class="form-control @error('nama_pekerjaan') is-invalid @enderror" id="nama_pekerjaanEdit" name="nama_pekerjaan" value="{{ old('nama_pekerjaan') }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status_kontrak">Status Kontrak</label>
                                    <input type="text" class="form-control @error('status_kontrak') is-invalid @enderror" id="status_kontrakEdit" name="status_kontrak" value="{{ old('status_kontrak') }}">
                                </div>
                                <div class="form-group">
                                    <label for="tanggal_kontrak">Tanngal Kontrak</label>
                                    <input type="date" class="form-control @error('tanggal_kontrak') is-invalid @enderror" id="tanggal_kontrakEdit" name="tanggal_kontrak" value="{{ old('tanggal_kontrak') }}">
                                </div>
                                <div class="form-group">
                                    <label for="nominal_kontrak">Nominal Kontrak</label>
                                    <input type="number" class="form-control @error('nominal_kontrak') is-invalid @enderror" id="nominal_kontrakEdit" name="nominal_kontrak" value="{{ old('nominal_kontrak') }}">
                                </div>
                                <div class="form-group">
                                    <label for="pic_sales">PIC Sales</label>
                                    <input type="text" class="form-control @error('pic_sales') is-invalid @enderror" id="pic_salesEdit" name="pic_sales" value="{{ old('pic_sales') }}">
                                </div>
                                <div class="form-group">
                                    <label for="bast_1">BAST-1 Tgl</label>
                                    <input type="date" class="form-control @error('bast_1') is-invalid @enderror" id="bast_1Edit" name="bast_1" value="{{ old('bast_1') }}">
                                </div>
                                <div class="form-group">
                                    <label for="bast_1_nomor">BAST-1 Nomor</label>
                                    <input type="text" class="form-control @error('bast_1_nomor') is-invalid @enderror" id="bast_1_nomorEdit" name="bast_1_nomor" value="{{ old('bast_1_nomor') }}">
                                </div>
                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea class="form-control @error('keterangan') is-invalid @enderror" id="keteranganEdit" name="keterangan">{{ old('keterangan') }}</textarea>
                                </div>           
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="jenis_pekerjaan">Jenis Pekerjaan</label>
                                    <select name="jenis_pekerjaan" id="jenis_pekerjaanEdit" class="form-control @error('jenis_pekerjaan') is-invalid @enderror">
                                        <option value="" disabled selected>Select Jenis Pekerjaan</option>
                                        <option value="-">-</option>
                                        <option value="CO LICENSING">CO LICENSING</option>
                                        <option value="DISTRIBUTOR">DISTRIBUTOR</option>
                                        <option value="IZIN">IZIN</option>
                                        <option value="JASA">JASA</option> 
                                        <option value="SEWA">SEWA</option>
                                        <option value="KEMITRAAN">KEMITRAAN</option>
                                        <option value="KERJASAMA">KERJASAMA</option>
                                        <option value="KONSULTASI">KONSULTASI</option>
                                        <option value="PENGADAAN">PENGADAAN</option>
                                        <option value="PEMASANGAN">PEMASANGAN</option>
                                        <option value="LAYANAN JASA">LAYANAN JASA</option>
                                        <option value="PERJANJIAN">PERJANJIAN</option>
                                        <option value="SERTIFIKASI">SERTIFIKASI</option>
                                        <option value="PENGAMANAN">PENGAMANAN</option>
                                        <option value="PERBAIKAN">PERBAIKAN</option>
                                        <option value="LAINNYA">LAINNYA</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="masa_berlaku">Masa Berlaku</label>
                                    <input type="date" class="form-control @error('masa_berlaku') is-invalid @enderror" id="masa_berlakuEdit" name="masa_berlaku" value="{{ old('masa_berlaku') }}">
                                </div>
                                <div class="form-group">
                                    <label for="mata_uang">Mata Uang</label>
                                    <select name="mata_uang" id="mata_uangEdit" class="form-control @error('mata_uang') is-invalid @enderror">
                                        <option value="" disabled selected>Select Mata Uang</option>
                                        <option value="-">-</option>
                                        <option value="IDR">IDR</option>
                                        <option value="USD">USD</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="pic_pc">PIC PC</label>
                                    <input type="text" class="form-control @error('pic_pc') is-invalid @enderror" id="pic_pcEdit" name="pic_pc" value="{{ old('pic_pc') }}">
                                </div>
                                <div class="form-group">
                                    <label for="bast_2">BAST-2 Tgl</label>
                                    <input type="date" class="form-control @error('bast_2') is-invalid @enderror" id="bast_2Edit" name="bast_2" value="{{ old('bast_2') }}">
                                </div>
                                <div class="form-group">
                                    <label for="bast_2_nomor">B AST-2 Nomor</label>
                                    <input type="text" class="form-control @error('bast_2_nomor') is-invalid @enderror" id="bast_2_nomorEdit" name="bast_2_nomor" value="{{ old('bast_2_nomor') }}">
                                </div>
                                <div class="form-group">
                                    <label for="overall_status">Overall Status</label>
                                    <input type="text" class="form-control @error('overall_status') is-invalid @enderror" id="overall_statusEdit" name="overall_status" value="{{ old('overall_status') }}">
                                </div>                       
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kontrak_milik">Kontrak Milik</label>
                                    <input type="text" class="form-control @error('kontrak_milik') is-invalid @enderror" id="kontrak_milikEdit" name="kontrak_milik" value="{{ old('kontrak_milik') }}">
                                </div>
                                <div class="form-group">
                                    <label for="status_proyek">Status Proyek</label>
                                    <select name="status_proyek" id="status_proyekEdit" class="form-control @error('status_proyek') is-invalid @enderror">
                                        <option value="" disabled selected>Select Status Proyek</option>
                                        <option value="-">-</option>
                                        <option value="OPEN">OPEN</option>
                                        <option value="CLOSED">CLOSSED</option>
                                        <option value="PENDING">PENDING</option>
                                        <option value="CANCELLED">CANCELLED</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="pic_customer">PIC Customer</label>
                                    <input type="text" class="form-control @error('pic_customer') is-invalid @enderror" id="pic_customerEdit" name="pic_customer" value="{{ old('pic_customer') }}">
                                </div>
                                <div class="form-group">
                                    <label for="retensi">Retensi</label>
                                    <input type="text" class="form-control @error('retensi') is-invalid @enderror" id="retensiEdit" name="retensi" value="{{ old('retensi') }}">
                                </div>
                                <div class="form-group">
                                    <label for="masa_retensi">Masa Retensi</label>
                                    <input type="date" class="form-control @error('masa_retensi') is-invalid @enderror" id="masa_retensiEdit" name="masa_retensi" value="{{ old('masa_retensi') }}">
                                </div>
                                <div class="form-group">
                                    <label for="status_retensi">Status Retensi</label>
                                    <select name="status_retensi" id="status_retensiEdit" class="form-control @error('status_retensi') is-invalid @enderror">
                                        <option value="" disabled selected>Select Status Retensi</option>
                                        <option value="-">-</option>
                                        <option value="OPEN">OPEN</option>
                                        <option value="CLOSED">CLOSSED</option>
                                        <option value="PENDING">PENDING</option>
                                        <option value="CANCELLED">CANCELLED</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="memo">Memo</label>
                                    <textarea class="form-control @error('memo') is-invalid @enderror" id="memoEdit" name="memo">{{ old('memo') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmUpdateContracts()"><i class="fas fa-check"></i> Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import Contracts -->
    <div class="modal fade" id="importContractsModal" tabindex="-1" role="dialog" aria-labelledby="importContractsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary d-flex align-items-center position-relative">
                    <h4 class="modal-title font-weight-bold text-white mx-auto" id="importContractsModalLabel">Import Contracts</h4>
                    <button type="button" class="close position-absolute" data-dismiss="modal" aria-label="Close" style="right: 15px; top: 15px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>                
                <div class="modal-body">
                    <form action="{{ route('contract.importContract') }}" method="post" enctype="multipart/form-data" id="importContractsForm">
                        @csrf
                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" class="form-control" id="file" name="file">
                            <p class="text-danger">*Format file .xlsx .xls .csv</p>
                            <p class="text-info">
                                <a href="{{ asset('template/file/contract-template.xlsx') }}" target="_blank"> Download Template</a>
                            </p>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary" onclick="confirmImportContracts()"><i class="fas fa-upload"></i> Upload</button>
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
    });

    // Konfirmasi pengguna sebelum menyimpan data
    function confirmAddContracts() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to save this contract data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Save it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('addContractsForm').submit();
            } else {
                return false;
            }
        })
    }   

    $('#editContractsModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var contractId = button.data('id')
        var contractCode = button.data('code');
        var contractName = button.data('name');
        var contractNamaPerusahaan = button.data('nama_perusahaan');
        var contractNamaPekerjaan = button.data('nama_pekerjaan');
        var contractStatusKontrak = button.data('status_kontrak');
        var contractJenisPekerjaan = button.data('jenis_pekerjaan');
        var contractNominalKontrak = button.data('nominal_kontrak');
        var contractTanggalKontrak = button.data('tanggal_kontrak');
        var contractMasaBerlaku = button.data('masa_berlaku');
        var contractStatusProyek = button.data('status_proyek');
        var contractRetensi = button.data('retensi');
        var contractMasaRetensi = button.data('masa_retensi');
        var contractStatusRetensi = button.data('status_retensi');
        var contractPicSales = button.data('pic_sales');
        var contractPicPc = button.data('pic_pc');
        var contractPicCustomer = button.data('pic_customer');
        var contractMataUang = button.data('mata_uang');
        var contractBast1 = button.data('bast_1');
        var contractBast1No = button.data('bast_1_nomor');
        var contractBast2 = button.data('bast_2');
        var contractBast2No = button.data('bast_2_nomor');
        var contractOverallStatus = button.data('overall_status');
        var contractKontrakMilik = button.data('kontrak_milik');
        var contractKeterangan = button.data('keterangan');
        var contractMemo = button.data('memo');
        
        var modal = $(this);
        document.getElementById('codeEdit').value = contractCode
        document.getElementById('nameEdit').value = contractName
        document.getElementById('nama_perusahaanEdit').value = contractNamaPerusahaan
        document.getElementById('nama_pekerjaanEdit').value = contractNamaPekerjaan
        document.getElementById('status_kontrakEdit').value = contractStatusKontrak

        var select = document.getElementById('jenis_pekerjaanEdit');
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].value == contractJenisPekerjaan) {
                    select.options[i].selected = true;
                }
            }

        document.getElementById('kontrak_milikEdit').value = contractKontrakMilik
        document.getElementById('tanggal_kontrakEdit').value = contractTanggalKontrak
        document.getElementById('masa_berlakuEdit').value = contractMasaBerlaku

        var select1 = document.getElementById('status_proyekEdit');
            for (var i = 0; i < select1.options.length; i++) {
                if (select1.options[i].value == contractStatusProyek) {
                    select1.options[i].selected = true;
                }
            }

        document.getElementById('nominal_kontrakEdit').value = contractNominalKontrak

        var select2 = document.getElementById('mata_uangEdit');
            for (var i = 0; i < select2.options.length; i++) {
                if (select2.options[i].value == contractMataUang) {
                    select2.options[i].selected = true;
                }
            }

        document.getElementById('pic_customerEdit').value = contractPicCustomer
        document.getElementById('pic_salesEdit').value = contractPicSales
        document.getElementById('pic_pcEdit').value = contractPicPc
        document.getElementById('retensiEdit').value = contractRetensi
        document.getElementById('bast_1Edit').value = contractBast1
        document.getElementById('bast_2Edit').value = contractBast2
        document.getElementById('bast_1_nomorEdit').value = contractBast1No
        document.getElementById('bast_2_nomorEdit').value = contractBast2No
        document.getElementById('masa_retensiEdit').value = contractMasaRetensi
            
        var select3 = document.getElementById('status_retensiEdit');
            for (var i = 0; i < select3.options.length; i++) {
                if (select3.options[i].value == contractStatusRetensi) {
                    select3.options[i].selected = true;
                }
            }

        document.getElementById('overall_statusEdit').value = contractOverallStatus
        document.getElementById('keteranganEdit').value = contractKeterangan
        document.getElementById('memoEdit').value = contractMemo
        
        // Ubah action form agar sesuai dengan id yang akan diupdate
        var formAction = '{{ route("contract.update", ":id") }}';
        formAction = formAction.replace(':id', contractId);
        $('#updateContractsForm').attr('action', formAction);
    })

    // Konfirmasi pengguna sebelum menyimpan data
    function confirmUpdateContracts() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to update this contract data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Update it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('updateContractsForm').submit();
            }
        })
    }

    // Konfirmasi pengguna sebelum menghapus data
    function confirmDeleteContract(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete this contract data!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, Delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`deleteContractsForm-${id}`).submit(); // Perbaikan tanda backtick
            }
        });
    }

    // Konfirmasi pengguna sebelum mengimport data
    function confirmImportContracts() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You want to import this contract data!",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Import it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('importContractsForm').submit();
            } else {
                return false;
            }
        })
    }

    // Export Excel
    function exportExcel() {
        var startDate = document.getElementById('startDate').value;
        var endDate = document.getElementById('endDate').value;

        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Start date and end date are required.',
                confirmButtonText: 'OK'
            });
            return;
        }
    
        window.location.href = `/contracts/export-excel?start_date=${startDate}&end_date=${endDate}`;
    }
</script>
@endpush