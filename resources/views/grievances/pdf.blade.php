<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Grievance #{{ $grievance->id }}</title>
    <style>
        body {
            font-family: 'arabic', 'Arabic Typesetting', 'Traditional Arabic', sans-serif;
            margin: 20px;
            font-size: 12px;
            direction: ltr;
            unicode-bidi: plaintext;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h3 {
            margin: 5px 0;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table th, .info-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .info-table th {
            background-color: #f5f5f5;
            width: 30%;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
        }
        .status-danger { background-color: #d9534f; }
        .status-warning { background-color: #f0ad4e; }
        .status-success { background-color: #5cb85c; }
        .status-info { background-color: #5bc0de; }
        
        /* Attachment page styles */
        .attachment-page {
            page-break-after: always;
            margin: 0;
            padding: 20px;
        }
        .attachment-page:last-child {
            page-break-after: auto;
        }
        .attachment-container {
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .attachment-image {
            max-width: 100%;
            max-height: 90%;
            object-fit: contain;
        }
        .attachment-info {
            margin-top: 10px;
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>

<!-- First Page - Grievance Details -->
<div class="header">
    <h3>GOVERNMENT OF KHYBER PAKHTUNKHWA</h3>
    <h4>BOARD OF REVENUE KHYBER PAKHTUNKHWA</h4>
    <h5>SETTLEMENT OF LAND RECORDS DIR/KALAM PROJECT</h5>
    <h4 style="margin-top: 15px;">PROFORMA FOR REDRESSAL OF APPLICATION / GRIEVANCE</h4>
</div>

<table class="info-table">
    <tr>
        <th>Grievance ID:</th>
        <td>{{ $grievance->id }}</td>
    </tr>
    <tr>
        <th>District:</th>
        <td>{{ $grievance->district_name }}</td>
    </tr>
    <tr>
        <th>Tehsil:</th>
        <td>{{ $grievance->tehsil_name }}</td>
    </tr>
    <tr>
        <th>Mouza/Village:</th>
        <td>{{ $grievance->moza_name }}</td>
    </tr>
    <tr>
        <th>Applicant Name:</th>
        <td>{{ $grievance->applicant_name }}</td>
    </tr>
    <tr>
        <th>Father Name:</th>
        <td>{{ $grievance->father_name }}</td>
    </tr>
    <tr>
        <th>CNIC:</th>
        <td>{{ $grievance->cnic }}</td>
    </tr>
    <tr>
        <th>Address:</th>
        <td>{{ $grievance->address }}</td>
    </tr>
    <tr>
        <th>Grievance Type:</th>
        <td>{{ $grievance->grievance_type_name }}</td>
    </tr>
    <tr>
        <th>Nature of Grievance:</th>
        <td>{{ $grievance->nature_of_grievance }}</td>
    </tr>
    <tr>
        <th>Status:</th>
        <td>
            @if($grievance->status_color == 'danger')
                <span class="status-badge status-danger">{{ $grievance->status_name }}</span>
            @elseif($grievance->status_color == 'warning')
                <span class="status-badge status-warning">{{ $grievance->status_name }}</span>
            @elseif($grievance->status_color == 'success')
                <span class="status-badge status-success">{{ $grievance->status_name }}</span>
            @else
                <span class="status-badge status-info">{{ $grievance->status_name }}</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Application Date:</th>
        <td>{{ \Carbon\Carbon::parse($grievance->application_date)->format('d-m-Y') }}</td>
    </tr>
    <tr>
        <th>Forwarded By:</th>
        <td>{{ $grievance->forwarded_by }}</td>
    </tr>
    <tr>
        <th>Date of Receipt By Tehsildar:</th>
        <td>{{ $grievance->received_by_tehsildar_date ? \Carbon\Carbon::parse($grievance->received_by_tehsildar_date)->format('d-m-Y') : 'N/A' }}</td>
    </tr>
    <tr>
        <th>Field Verification Date:</th>
        <td>{{ $grievance->field_verification_date ? \Carbon\Carbon::parse($grievance->field_verification_date)->format('d-m-Y') : 'N/A' }}</td>
    </tr>
    <tr>
        <th>Date of Disposal:</th>
        <td>{{ $grievance->disposal_date ? \Carbon\Carbon::parse($grievance->disposal_date)->format('d-m-Y') : 'N/A' }}</td>
    </tr>
    <tr>
        <th>Preliminary Remarks:</th>
        <td>{{ $grievance->preliminary_remarks }}</td>
    </tr>
    <tr>
        <th>Action Proposed:</th>
        <td>{{ $grievance->action_proposed }}</td>
    </tr>
    <tr>
        <th>Decision / Redressal:</th>
        <td>{{ $grievance->decision }}</td>
    </tr>
    <tr>
        <th>Assistant Officer Remarks:</th>
        <td>{{ $grievance->assistant_remarks }}</td>
    </tr>
    <tr>
        <th>Description:</th>
        <td>{{ $grievance->grievance_description }}</td>
    </tr>
</table>

@if($grievance->tehsildar_signature)
<div style="margin-top: 20px;">
    <strong>Tehsildar Signature:</strong><br>
    @php 
    $sigPath = public_path('assets/img/' . $grievance->tehsildar_signature);
    @endphp
    @if(file_exists($sigPath))
    <img src="{{ $sigPath }}" alt="Signature" style="max-width: 150px; max-height: 80px;">
    @else
    <p>Signature file not found at: {{ $sigPath }}</p>
    @endif
</div>
@endif

<div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
    Page 1 of {{ $attachments->count() + 1 }}
</div>

<!-- Attachment Pages - One per page -->
@foreach($attachments as $index => $attachment)
<div class="attachment-page">
    <div class="attachment-container">
        <div style="margin-bottom: 20px; font-size: 16px;">
            <strong>Attachment {{ $index + 1 }} of {{ $attachments->count() }}</strong>
        </div>
        @php
        // Use absolute file path for MPDF
        $attachmentPath = public_path($attachment->image_path);
        @endphp
        @if(file_exists($attachmentPath))
        <img src="{{ $attachmentPath }}" class="attachment-image" alt="Attachment {{ $index + 1 }}">
        @else
        <p style="color: red;">Attachment file not found at: {{ $attachmentPath }}</p>
        @endif
        <div class="attachment-info">
            <strong>Uploaded:</strong> {{ \Carbon\Carbon::parse($attachment->uploaded_datetime)->format('d-m-Y H:i:s') }}
        </div>
    </div>
</div>
@endforeach

</body>
</html>