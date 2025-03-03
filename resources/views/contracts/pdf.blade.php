<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contract Agreement</title>
    <style>
        @page {
            margin: 20mm;
        }
        
        body {
            font-family: 'Times New Roman', Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #333;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 14pt;
            color: #007bff;
            margin: 0;
            text-transform: uppercase;
        }

        .contract-number {
            color: #6c757d;
            font-size: 9pt;
            margin-top: 5px;
        }

        .section-title {
            font-size: 9pt;
            color: #007bff;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
            margin: 15px 0 10px;
            font-weight: bold;
        }

        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .info-table td {
            padding: 5px;
            border-bottom: 1px solid #e0e0e0;
        }

        .info-table .label {
            width: 40%;
            font-weight: bold;
            color: #495057;
        }

        .signatures {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .signature-box {
            display: table-cell;
            text-align: center;
            width: 33%;
        }

        .footer {
            text-align: center;
            color: #6c757d;
            font-size: 9pt;
            margin-top: 20px;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Contract Agreement</h1>
        <div class="contract-number">Contract No: {{ $contract->code }}</div>
    </div>

    <div class="content">
        <p>
            This contract is entered into by and between <strong>{{ $contract->perusahaan }}</strong> 
            (hereinafter referred to as "Company"), and the client for the project titled 
            <strong>"{{ $contract->nama_pekerjaan }}"</strong>.
        </p>
        <p>
            Effective as of <strong>{{ date('d F Y', strtotime($contract->tanggal_kontrak)) }}</strong>, 
            this agreement outlines the terms and conditions governing the engagement of the project.
        </p>

        <div class="section-title">Project Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Project Type</td>
                <td>{{ $contract->jenis_pekerjaan }}</td>
            </tr>
            <tr>
                <td class="label">Contract Status</td>
                <td>{{ $contract->status_kontrak }}</td>
            </tr>
            <tr>
                <td class="label">Contract Value</td>
                <td>{{ number_format($contract->nominal_kontrak, 2) }} {{ $contract->mata_uang }}</td>
            </tr>
            <tr>
                <td class="label">Validity Period</td>
                <td>{{ date('d F Y', strtotime($contract->masa_berlaku)) }}</td>
            </tr>
            <tr>
                <td class="label">Project Status</td>
                <td>{{ $contract->status_proyek }}</td>
            </tr>
        </table>

        <div class="section-title">Retention Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Retention Percentage</td>
                <td>{{ $contract->retensi }}%</td>
            </tr>
            <tr>
                <td class="label">Retention Period</td>
                <td>{{ date('d F Y', strtotime($contract->masa_retensi)) }}</td>
            </tr>
            <tr>
                <td class="label">Retention Status</td>
                <td>{{ $contract->status_retensi }}</td>
            </tr>
        </table>

        <div class="section-title">Additional Information</div>
        <p>
            The contract is owned by <strong>{{ $contract->kontrak_milik }}</strong>. 
            Additional notes: <em>"{{ $contract->memo }}"</em>.
        </p>

        <div class="section-title">Termination and Closing</div>
        <p>
            This contract will remain <strong>{{ $contract->overall_status }}</strong>, 
            unless further amendments are agreed upon by both parties. 
            Contract remarks: <strong>{{ $contract->keterangan }}</strong>.
        </p>
    </div>

    <div class="signatures">
        <div class="signature-box">
            <p><strong>PIC Sales</strong></p>
            <br>
            <p>{{ $contract->pic_sales }}</p>
        </div>

        <div class="signature-box">
            <p><strong>PIC PC</strong></p>
            <br>
            <p>{{ $contract->pic_pc }}</p>
        </div>

        <div class="signature-box">
            <p><strong>PIC Customer</strong></p>
            <br>
            <p>{{ $contract->pic_customer }}</p>
        </div>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}, PT. All rights reserved.</p>
    </div>
</body>
</html>