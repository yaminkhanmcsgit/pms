@extends('layouts.app')

@section('title', 'Grievances')

@section('content')
<div class="container" dir="ltr">
    <div class="mb-3">

        <a href="{{ route('grievances.create') }}" class="btn btn-success pull-right">
            <i class="fa fa-plus"></i> Add New Grievance
        </a>
    </div>

    <center><legend> <h3>Grievances List</h3></legend></center>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="grievances_table" class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Applicant Name</th>
                    <th>Father Name</th>
                    <th>CNIC</th>
                    <th>District</th>
                    <th>Tehsil</th>
                    <th>Mouza</th>
                    <th>Grievance Type</th>
                    <th>Status</th>
                    <th>Application Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Grievance Details Modal -->
    <div class="modal fade" id="grievanceModal" tabindex="-1" role="dialog" aria-labelledby="grievanceModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="grievanceModalLabel">Grievance Details</h4>
                </div>
                <div class="modal-body" id="grievanceDetails">
                    <!-- Grievance details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="printGrievance()"><i class="fa fa-print"></i> Print</button>
                    <button type="button" class="btn btn-success" onclick="pdfGrievance()"><i class="fa fa-file-pdf-o"></i> PDF</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Field Update Modal -->
    <div class="modal fade" id="fieldUpdateModal" tabindex="-1" role="dialog" aria-labelledby="fieldUpdateModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="fieldUpdateForm">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="fieldUpdateModalLabel">Update Field</h4>
                    </div>
                    <div class="modal-body">
                        <div id="fieldUpdateMsg"></div>
                        <input type="hidden" name="grievance_id" id="update_grievance_id">
                        <input type="hidden" name="field_name" id="update_field_name">
                        <div class="form-group">
                            <label id="fieldLabel"></label>
                            <div id="fieldInputContainer">
                                <!-- Input field will be dynamically added here -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Current Status:</label>
                            <p id="currentStatus" class="text-info"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Signature Upload Modal -->
    <div class="modal fade" id="signatureModal" tabindex="-1" role="dialog" aria-labelledby="signatureModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="signatureForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="signatureModalLabel">Upload Tehsildar Signature</h4>
                    </div>
                    <div class="modal-body">
                        <div id="signatureMsg"></div>
                        <input type="hidden" name="grievance_id" id="signature_grievance_id">
                        <div class="form-group">
                            <label>Current Signature:</label>
                            <div id="currentSignature">
                                <!-- Current signature image will be shown here -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="signature_file">Upload New Signature (Image):</label>
                            <input type="file" name="signature_file" id="signature_file" class="form-control" accept="image/*" required>
                            <small class="text-muted">Accepted formats: JPG, PNG, GIF. Max size: 2MB</small>
                        </div>
                        <div class="form-group">
                            <label>Current Status:</label>
                            <p id="signatureStatus" class="text-info"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    .form-group {
        float: left;width:100%;
    }

    /* Fixed center dropdown for actions */
    .actions-dropdown-menu {
        position: fixed !important;
        top: 50% !important;
        left: 70% !important;
        transform: translate(-50%, -50%) !important;
        z-index: 9999 !important;
        min-width: 300px !important;
        max-height: 80vh !important;
        overflow-y: auto !important;
        box-shadow: 0 6px 12px rgba(0,0,0,.175) !important;
        border: 1px solid rgba(0,0,0,.15) !important;
    }

    .actions-dropdown-menu:before,
    .actions-dropdown-menu:after {
        display: none !important;
    }
    #view-grievance td {text-align: left !important}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
$(document).ready(function(){
    $('#grievances_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('grievances.datatable') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id', orderable: true },
            { data: 'applicant_name', orderable: true },
            { data: 'father_name', orderable: true },
            { data: 'cnic', orderable: true },
            { data: 'district_name', orderable: true },
            { data: 'tehsil_name', orderable: true },
            { data: 'moza_name', orderable: true },
            { data: 'grievance_type_name', orderable: true },
            { data: 'status_name', orderable: false },
            { data: 'application_date', orderable: true },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
<script>
function populateModalDropdowns(grievance) {
    // Apply role-based filtering like in completion process forms
    const isAdmin = {{ session('role_id') == 1 ? 'true' : 'false' }};

    if (isAdmin) {
        // Admin: load all districts
        fetch(`{{ url('api/districts') }}`)
            .then(res => res.json())
            .then(districts => {
                const districtDropdown = document.getElementById('modal_district');
                districtDropdown.innerHTML = '<option value="">Select District</option>';
                districts.forEach(district => {
                    const selected = grievance.district == district.zila_id ? 'selected' : '';
                    districtDropdown.innerHTML += `<option value="${district.zila_id}" ${selected}>${district.zilaNameUrdu}</option>`;
                });

                // Then populate dependent dropdowns using existing global functions
                if (grievance.district) {
                    onDistrictChange(grievance.district, 'modal_tehsil', grievance.tehsil);
                    if (grievance.tehsil) {
                        // Small delay to ensure tehsils are populated first
                        setTimeout(() => {
                            onTehsilChange(grievance.tehsil, 'modal_moza', grievance.village_name);
                        }, 100);
                    }
                }
            });
    } else {
        // Limited user: load only their district and set it as selected/readonly
        const userDistrictId = {{ session('zila_id') }};
        const userTehsilId = {{ session('tehsil_id') }};

        fetch(`{{ url('api/districts') }}`)
            .then(res => res.json())
            .then(districts => {
                // Filter to only their district
                const filteredDistricts = districts.filter(district => district.zila_id == userDistrictId);
                const districtDropdown = document.getElementById('modal_district');
                districtDropdown.innerHTML = '<option value="">Select District</option>';
                filteredDistricts.forEach(district => {
                    districtDropdown.innerHTML += `<option value="${district.zila_id}" selected>${district.zilaNameUrdu}</option>`;
                });
                // Disable district dropdown for limited users
                districtDropdown.disabled = true;

                // Load their tehsils and disable the dropdown
                onDistrictChange(userDistrictId, 'modal_tehsil', userTehsilId);
                // Disable tehsil dropdown for limited users
                setTimeout(() => {
                    document.getElementById('modal_tehsil').disabled = true;
                    onTehsilChange(userTehsilId, 'modal_moza', grievance.village_name);
                }, 100);
            });
    }
}

function viewGrievance(id) {
    // Fetch grievance details and types/statuses via AJAX
    Promise.all([
        fetch(`{{ url('grievances') }}/${id}`).then(response => response.json()),
        fetch(`{{ url('grievance-types') }}`).then(response => response.json()),
        fetch(`{{ url('grievance-statuses') }}`).then(response => response.json())
    ])
    .then(([grievanceData, typesData, statusesData]) => {
        if (grievanceData.success) {
            displayGrievanceDetails(grievanceData.grievance, typesData.types || [], statusesData.statuses || []);
        } else {
            Swal.fire('Error', grievanceData.message || 'Unable to load grievance details', 'error');
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        Swal.fire('Error', 'Error loading grievance details', 'error');
    });
}

function updateField(grievanceId, fieldName, currentValue) {
    // Set modal title and form data
    const fieldLabels = {
        'forwarded_by': 'Forwarded By',
        'received_by_tehsildar_date': 'Date of Receipt By Tehsildar',
        'field_verification_date': 'Field Verification Date',
        'disposal_date': 'Date of Disposal',
        'preliminary_remarks': 'Preliminary Remarks',
        'action_proposed': 'Action Proposed',
        'decision': 'Decision / Redressal',
        'assistant_remarks': 'Assistant Officer Remarks',
        'status_id': 'Status'
    };

    document.getElementById('fieldUpdateModalLabel').textContent = 'Update ' + fieldLabels[fieldName];
    document.getElementById('update_grievance_id').value = grievanceId;
    document.getElementById('update_field_name').value = fieldName;
    document.getElementById('fieldLabel').textContent = fieldLabels[fieldName] + ':';

    // Create appropriate input field
    const inputContainer = document.getElementById('fieldInputContainer');
    inputContainer.innerHTML = '';

    if (fieldName.includes('date')) {
        const input = document.createElement('input');
        input.type = 'date';
        input.className = 'form-control';
        input.name = fieldName;
        input.value = currentValue ? new Date(currentValue).toISOString().split('T')[0] : '';
        inputContainer.appendChild(input);
    } else if (fieldName.includes('remarks') || fieldName.includes('decision') || fieldName.includes('proposed')) {
        const textarea = document.createElement('textarea');
        textarea.className = 'form-control';
        textarea.name = fieldName;
        textarea.rows = 3;
        textarea.textContent = currentValue || '';
        inputContainer.appendChild(textarea);
    } else if (fieldName === 'status_id') {
        // Create status dropdown
        const select = document.createElement('select');
        select.className = 'form-control';
        select.name = fieldName;
        select.innerHTML = '<option value="">Select Status</option>';

        // Fetch statuses and populate dropdown
        fetch(`{{ url('grievance-statuses') }}`)
            .then(response => response.json())
            .then(data => {
                data.statuses.forEach(status => {
                    const option = document.createElement('option');
                    option.value = status.id;
                    option.textContent = status.name;
                    if (currentValue == status.id) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching statuses:', error);
            });

        inputContainer.appendChild(select);
    } else {
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'form-control';
        input.name = fieldName;
        input.value = currentValue || '';
        inputContainer.appendChild(input);
    }

    // Get current status
    fetch(`{{ url('grievances') }}/${grievanceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusName = data.grievance.status_name || 'Unknown';
                document.getElementById('currentStatus').textContent = statusName;
            }
        })
        .catch(error => {
            console.error('Error fetching grievance status:', error);
        });

    $('#fieldUpdateModal').modal('show');
}

function updateSignature(grievanceId, currentSignature) {
    document.getElementById('signature_grievance_id').value = grievanceId;

    // Show current signature if exists
    const currentSignatureDiv = document.getElementById('currentSignature');
    if (currentSignature) {
        currentSignatureDiv.innerHTML = `<img src="{{ url('public/storage/signatures') }}/${currentSignature}" alt="Current Signature" style="max-width: 200px; max-height: 100px; border: 1px solid #ddd;">`;
    } else {
        currentSignatureDiv.innerHTML = '<p class="text-muted">No signature uploaded yet.</p>';
    }

    // Get current status
    fetch(`{{ url('grievances') }}/${grievanceId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const statusName = data.grievance.status_name || 'Unknown';
                document.getElementById('signatureStatus').textContent = statusName;
            }
        });

    $('#signatureModal').modal('show');
}

function editGrievance(grievanceId) {
    window.location.href = `{{ url('grievances') }}/${grievanceId}/edit`;
}

function confirmDelete(event) {
    event.preventDefault();
    const form = event.target.closest('form');

    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this grievance?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d9534f',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            form.submit();
        }
    });

    return false;
}

function deleteGrievance(grievanceId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this grievance?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d9534f',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('grievances') }}/${grievanceId}`;
            form.style.display = 'none';

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';

            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            form.appendChild(methodInput);
            form.appendChild(tokenInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

function displayGrievanceDetails(grievance, types, statuses) {
    // Set the HTML first
    const detailsHtml = `
        <div class="text-center" style="margin-bottom:20px;">
            <h4><strong>GOVERNMENT OF KHYBER PAKHTUNKHWA</strong></h4>
            <h5>BOARD OF REVENUE KHYBER PAKHTUNKHWA</h5>
            <h5>SETTLEMENT OF LAND RECORDS DIR/KALAM PROJECT</h5>
        </div>
        <div class="row" style="margin-bottom:20px;">
            <div class="col-md-6">
                <strong>District:</strong> ${grievance.district_name || ''}
            </div>
            <div class="col-md-6">
                <strong>Tehsil:</strong> ${grievance.tehsil_name || ''}
            </div>
        </div>

        <h4 style="margin-bottom:20px;">
            <strong>PROFORMA FOR REDRESSAL OF APPLICATION / GRIEVANCE DURING LAND SETTLEMENT OPERATIONS</strong>
        </h4>

        <table class="table table-bordered text-left" id="view-grievance">
            <tr>
                <td style="width:30%;">1. Name of Applicant:</td>
                <td>${grievance.applicant_name || ''}</td>
            </tr>
            <tr>
                <td>2. Father's Name:</td>
                <td>${grievance.father_name || ''}</td>
            </tr>
            <tr>
                <td>3. CNIC No.:</td>
                <td>${grievance.cnic || ''}</td>
            </tr>
            <tr>
                <td>4. Address / Contact No.:</td>
                <td>${grievance.address || ''}</td>
            </tr>
            <tr>
                <td>5. Mouza / Village Name:</td>
                <td>${grievance.moza_name || ''}</td>
            </tr>
            <tr>
                <td>6. Nature of Grievance / Application:</td>
                <td>${grievance.nature_of_grievance || ''}</td>
            </tr>
            <tr>
                <td>7. Status:</td>
                <td><span class="label label-${grievance.status_color}">${grievance.status_name || ''}</span></td>
            </tr>
        </table>

        <div style="margin-left:10px; margin-bottom:20px;">
            <strong>Grievance Type:</strong> ${grievance.grievance_type_name || ''}
        </div>
        <hr>

        <div class="row">
            <div class="col-md-12">
                <strong>7. Brief Description of Grievance:</strong><br>
                ${grievance.grievance_description || ''}
            </div>
        </div>
        <div class="row" style="margin-top:10px;">
            <div class="col-md-12">
                <strong>8. Date of Receipt:</strong> ${grievance.application_date ? new Date(grievance.application_date).toLocaleDateString() : ''}
            </div>
            <div class="col-md-12">
                <strong>9. Forwarded by:</strong> ${grievance.forwarded_by || ''}
            </div>
        </div>

        <h4 style="margin-top:20px;"><strong>Action by Tehsildar (Settlement)</strong></h4>

        <div class="row">
            <div class="col-md-12">
                <strong>10. Date of Receipt By Tehsildar:</strong> ${grievance.received_by_tehsildar_date ? new Date(grievance.received_by_tehsildar_date).toLocaleDateString() : ''}
            </div>
            <div class="col-md-12">
                <strong>13. Field Verification Date:</strong> ${grievance.field_verification_date ? new Date(grievance.field_verification_date).toLocaleDateString() : ''}
            </div>
            <div class="col-md-12">
                <strong>15. Date of Disposal:</strong> ${grievance.disposal_date ? new Date(grievance.disposal_date).toLocaleDateString() : ''}
            </div>
        </div>

        <div class="row" style="margin-top:10px;">
            <div class="col-md-12">
                <strong>11. Preliminary Remarks:</strong>
                ${grievance.preliminary_remarks || ''}
            </div>
            <div class="col-md-12">
                <strong>12. Action Proposed:</strong>
                ${grievance.action_proposed || ''}
            </div>
        </div>

        <div class="row" style="margin-top:10px;">
            <div class="col-md-12">
                <strong>14. Decision / Redressal:</strong>
                ${grievance.decision || ''}
            </div>
            <div class="col-md-12">
                <strong>17. Assistant Officer Remarks:</strong>
                ${grievance.assistant_remarks || ''}
            </div>
        </div>

        <div class="row" style="margin-top:10px;">
            <div class="col-md-12">
                <strong>Tehsildar Signature:</strong> ${grievance.tehsildar_signature ? '<img src="' + '{{ url("public/storage/signatures") }}' + '/' + grievance.tehsildar_signature + '" alt="Signature" style="max-width: 60px; max-height: 100px; border: 1px solid #ddd;">' : 'No signature'}
            </div>
        </div>
    `;
    document.getElementById('grievanceDetails').innerHTML = detailsHtml;

    // Show modal
    $('#grievanceModal').modal('show');
}

function printGrievance() {
    const printContent = document.getElementById('grievanceDetails').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
        <head>
            <title>Grievance Details</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                table, th, td { border: 1px solid #ddd; }
                th, td { padding: 8px; text-align: left; }
                .text-center { text-align: center; }
                h4, h5 { margin: 10px 0; }
                .row {  margin-bottom: 10px; }
                .col-md-6, .col-md-4, .col-md-12 { padding: 0 10px; }
                .col-md-6 { width: 50%; }
                .col-md-4 { width: 33.33%; }
                .col-md-12 { width: 100%; }
                img { max-width: 200px; max-height: 100px; }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function pdfGrievance() {
    const modalContent = document.getElementById('grievanceDetails');

    // Use html2canvas to capture the modal content
    html2canvas(modalContent, {
        scale: 2, // Higher quality
        useCORS: true,
        allowTaint: true,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');

        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 295; // A4 height in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        let heightLeft = imgHeight;
        let position = 0;

        // Add first page
        doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;

        // Add additional pages if content is longer than one page
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }

        // Save the PDF
        doc.save('grievance-details.pdf');
    }).catch(error => {
        console.error('Error generating PDF:', error);
        swal('Error', 'Failed to generate PDF. Please try again.', 'error');
    });
}

// Field Update Form Handler
document.getElementById('fieldUpdateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const grievanceId = formData.get('grievance_id');
    const fieldName = formData.get('field_name');
    const fieldValue = formData.get(fieldName);

    fetch(`{{ url('grievances') }}/${grievanceId}/update-field`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            field_name: fieldName,
            field_value: fieldValue
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response ok:', response.ok);
        if (!response.ok) {
            return response.text().then(text => {
                throw new Error('HTTP error! status: ' + response.status + ', response: ' + text);
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire('Success', 'Field updated successfully!', 'success');
            $('#fieldUpdateModal').modal('hide');

            if(fieldName=='status_id'){
               location.reload();
            }
            
        } else {
            console.log('Update failed:', data.message);
            document.getElementById('fieldUpdateMsg').innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error updating field') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error updating field:', error);
        document.getElementById('fieldUpdateMsg').innerHTML = '<div class="alert alert-danger">Error updating field: ' + error.message + '</div>';
    });
});

// Signature Upload Form Handler
document.getElementById('signatureForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const grievanceId = formData.get('grievance_id');

    fetch(`{{ url('grievances') }}/${grievanceId}/upload-signature`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', 'Signature uploaded successfully!', 'success');
            $('#signatureModal').modal('hide');
            location.reload();
        } else {
            document.getElementById('signatureMsg').innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error uploading signature') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error uploading signature:', error);
        document.getElementById('signatureMsg').innerHTML = '<div class="alert alert-danger">Error uploading signature</div>';
    });
});
</script>
@endsection