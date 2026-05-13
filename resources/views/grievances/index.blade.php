@extends('layouts.app')

@section('title', 'Grievances')

@section('content')
<div class="container" dir="ltr">
    <div class="mb-3">

        <a href="{{ route('grievances.create') }}" class="btn btn-success pull-right">
            <i class="fa fa-plus"></i> Add New Grievance
        </a>
        @if(config('app.debug'))
        <button onclick="deleteGrievance(1)" class="btn btn-warning pull-right" style="margin-right: 10px;">
            <i class="fa fa-trash"></i> Test Delete
        </button>
        @endif
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
                    <th>Attachments</th>
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
                <input type="hidden" id="viewed_grievance_id" value="">
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

    <!-- Attachment Upload/View Modal -->
    <div class="modal fade" id="attachmentModal" tabindex="-1" role="dialog" aria-labelledby="attachmentModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form id="attachmentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title" id="attachmentModalLabel">Grievance Attachments</h4>
                    </div>
                    <div class="modal-body">
                        <div id="attachmentMsg"></div>
                        <input type="hidden" name="grievance_id" id="attachment_grievance_id">
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="attachment_file">Upload New Attachment (Image Only):</label>
                                    <input type="file" name="attachment_file" id="attachment_file" class="form-control" accept="image/*">
                                    <small class="text-muted">Accepted formats: JPG, PNG, GIF, WEBP. Max size: 5MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <label>Current Attachments:</label>
                                <div id="currentAttachments" class="row">
                                    <!-- Attachments will be loaded here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
            { data: 'attachments', orderable: false },
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
        const userTehsilIds = '{{ session('tehsil_id') }}'.split(',').map(id => id.trim());

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

                // Load only their assigned tehsils
                fetch(`{{ url('api/tehsils') }}?district_id=${userDistrictId}`)
                    .then(res => res.json())
                    .then(tehsils => {
                        // Filter to only assigned tehsils
                        const assignedTehsils = tehsils.filter(tehsil => userTehsilIds.includes(String(tehsil.tehsil_id)));
                        const tehsilDropdown = document.getElementById('modal_tehsil');
                        tehsilDropdown.innerHTML = '<option value="">Select Tehsil</option>';
                        assignedTehsils.forEach(tehsil => {
                            tehsilDropdown.innerHTML += `<option value="${tehsil.tehsil_id}">${tehsil.tehsilNameUrdu}</option>`;
                        });
                        // Disable tehsil dropdown for limited users
                        tehsilDropdown.disabled = true;

                        // Load mozas for the first assigned tehsil or the grievance's tehsil
                        const grievanceTehsilId = grievance.tehsil;
                        if (userTehsilIds.includes(String(grievanceTehsilId))) {
                            onTehsilChange(grievanceTehsilId, 'modal_moza', grievance.village_name);
                        } else if (assignedTehsils.length > 0) {
                            onTehsilChange(assignedTehsils[0].tehsil_id, 'modal_moza', grievance.village_name);
                        }
                    });
            });
    }
}

function viewGrievance(id) {
    // Show loading indicator
    document.getElementById('grievanceDetails').innerHTML = '<p class="text-center">Loading...</p>';
    $('#grievanceModal').modal('show');
    
    // Fetch grievance details and types/statuses via AJAX
    fetch(`{{ url('grievances') }}/${id}`)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error('Server error: ' + response.status);
                });
            }
            return response.json();
        })
        .then(grievanceData => {
            if (grievanceData.success) {
                // Fetch types and statuses
                return Promise.all([
                    Promise.resolve(grievanceData),
                    fetch(`{{ url('grievance-types') }}`).then(r => r.json()),
                    fetch(`{{ url('grievance-statuses') }}`).then(r => r.json())
                ]);
            } else {
                throw new Error(grievanceData.message || 'Grievance not found');
            }
        })
        .then(([grievanceData, typesData, statusesData]) => {
            displayGrievanceDetails(grievanceData.grievance, typesData.types || [], statusesData.statuses || []);
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            document.getElementById('grievanceDetails').innerHTML = '<p class="text-danger text-center">Error: ' + error.message + '</p>';
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
        'assistant_remarks': 'ASO Remarks',
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
        currentSignatureDiv.innerHTML = `<img src="{{ url('assets/img') }}/${currentSignature}" alt="Current Signature" style="max-width: 200px; max-height: 100px; border: 1px solid #ddd;">`;
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
    console.log('=== DELETE GRIEVANCE FUNCTION CALLED ===');
    console.log('Grievance ID:', grievanceId);

    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this grievance?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d9534f',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        console.log('User confirmed deletion:', result.value);
        if (result.value) {
            // Use AJAX DELETE request instead of form spoofing
            const baseUrl = window.location.origin;
            const pathParts = window.location.pathname.split('/');
            const appPath = pathParts[1]; // Get the app path (e.g., 'pms' or 'admin')
            const deleteUrl = baseUrl + '/' + appPath + '/grievances/' + grievanceId;

            console.log('Sending DELETE request to:', deleteUrl);
            console.log('Grievance ID:', grievanceId);
            console.log('Window location:', window.location);
            console.log('Pathname:', window.location.pathname);

            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token meta tag not found');
                Swal.fire({
                    title: 'Error!',
                    text: 'CSRF token not found. Please refresh the page.',
                    icon: 'error'
                });
                return;
            }

            console.log('CSRF token found:', csrfToken.getAttribute('content').substring(0, 10) + '...');

            // Use jQuery AJAX instead of fetch for better compatibility
            $.ajax({
                url: deleteUrl,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(data, textStatus, jqXHR) {
                    console.log('Delete success:', data);

                    Swal.fire({
                        title: 'Deleted!',
                        text: data.message || 'Grievance has been deleted.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Delete error:', jqXHR.responseText);
                    console.error('Status:', jqXHR.status);
                    console.error('Error thrown:', errorThrown);

                    let errorMessage = 'Failed to delete grievance';
                    try {
                        const errorData = JSON.parse(jqXHR.responseText);
                        errorMessage = errorData.message || errorMessage;
                    } catch (e) {
                        errorMessage = jqXHR.responseText || errorMessage;
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error'
                    });
                }
            });
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
                <strong>Tehsildar Signature:</strong> ${grievance.tehsildar_signature ? '<img src="' + '{{ url("assets/img") }}' + '/' + grievance.tehsildar_signature + '" alt="Signature" style="max-width: 60px; max-height: 100px; border: 1px solid #ddd;">' : 'No signature'}
            </div>
        </div>

        <div class="row" style="margin-top:20px;" id="grievanceAttachmentsSection">
            <div class="col-md-12">
                <strong>Attachments:</strong>
                <div id="grievanceAttachmentsList" style="margin-top:10px; page-break-before: always;">
                    <p class="text-muted">Loading attachments...</p>
                </div>
            </div>
        </div>
    `;
    document.getElementById('grievanceDetails').innerHTML = detailsHtml;

    // Store grievance ID for PDF generation
    document.getElementById('viewed_grievance_id').value = grievance.id;

    // Fetch and display attachments
    fetch(`{{ url('grievances') }}/${grievance.id}/attachments`)
        .then(response => response.json())
        .then(data => {
            const attachmentsDiv = document.getElementById('grievanceAttachmentsList');
            if (data.success && data.attachments && data.attachments.length > 0) {
                let html = '';
                data.attachments.forEach((att, index) => {
                    // Use public assets path
                    let imageUrl = '{{ asset('assets/grievance_attachments/') }}/' + att.image_path.split('/').pop();
                    html += `
                        <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; page-break-after: always; page-break-inside: avoid;">
                            <p style="margin: 0 0 10px 0;"><strong>Attachment ${index + 1}:</strong> ${att.uploaded_datetime}</p>
                            <img src="${imageUrl}" alt="Attachment ${index + 1}" style="width: 100%; height: auto; display: block;">
                            <p style="margin: 10px 0 0 0;">
                                <a href="${imageUrl}" target="_blank" class="btn btn-xs btn-primary attachment-action-btn"><i class="fa fa-eye"></i> View</a>
                                <button onclick="deleteAttachment(${grievance.id}, ${att.attachment_id})" class="btn btn-xs btn-danger attachment-action-btn"><i class="fa fa-trash"></i> Delete</button>
                            </p>
                        </div>
                    `;
                });
                attachmentsDiv.innerHTML = html;
            } else {
                attachmentsDiv.innerHTML = '<p class="text-muted">No attachments uploaded.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading attachments:', error);
            document.getElementById('grievanceAttachmentsList').innerHTML = '<p class="text-muted">No attachments uploaded.</p>';
        });
}

function printGrievance() {
    const element = document.getElementById('grievanceDetails');
    
    // Clone the element to modify for printing
    const clonedContent = element.cloneNode(true);
    
    // Convert all images to base64 for printing
    const images = clonedContent.querySelectorAll('img');
    const originalImages = element.querySelectorAll('img');
    
    const convertPromises = Array.from(images).map((img, index) => {
        return new Promise((resolve) => {
            if (img.src.startsWith('data:')) {
                resolve();
                return;
            }
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');
            const originalImg = originalImages[index];
            
            img.onload = () => {
                canvas.width = img.width;
                canvas.height = img.height;
                ctx.drawImage(img, 0, 0);
                img.src = canvas.toDataURL('image/png');
                resolve();
            };
            img.onerror = () => {
                // Try loading with fetch if canvas fails
                fetch(img.src)
                    .then(resp => resp.blob())
                    .then(blob => {
                        const reader = new FileReader();
                        reader.onload = () => {
                            img.src = reader.result;
                            resolve();
                        };
                        reader.onerror = resolve;
                        reader.readAsDataURL(blob);
                    })
                    .catch(() => resolve());
            };
        });
    });
    
    Promise.all(convertPromises).then(() => {
        // Hide action buttons for printing
        const actionBtns = clonedContent.querySelectorAll('.attachment-action-btn');
        actionBtns.forEach(btn => btn.style.display = 'none');
        
        const printContent = clonedContent.innerHTML;
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Grievance Details</title>
                <style>
                    body { font-family: 'Noto Nastaliq Urdu', 'Jameel Nori Nastaleeq', 'Alvi Nastaleeq', 'Urdu Typesetting', Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    table, th, td { border: 1px solid #ddd; }
                    th, td { padding: 8px; text-align: left; }
                    .text-center { text-align: center; }
                    h4, h5 { margin: 10px 0; }
                    .row { margin-bottom: 10px; }
                    .col-md-6, .col-md-4, .col-md-12 { padding: 0 10px; }
                    .col-md-6 { width: 50%; }
                    .col-md-4 { width: 33.33%; }
                    .col-md-12 { width: 100%; }
                    img { width: 100%; height: auto; max-height: none; display: block; margin: 10px 0; }
                    .attachment-item { page-break-after: always; page-break-inside: avoid; margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; }
                </style>
            </head>
            <body>
                ${printContent}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });
}

function pdfGrievance() {
    // Get the grievance details element
    const element = document.getElementById('grievanceDetails');
    
    // Hide only attachment View and Delete buttons for PDF
    const attachmentButtons = element.querySelectorAll('#grievanceAttachmentsList .attachment-action-btn');
    
    attachmentButtons.forEach(btn => {
        btn.style.display = 'none';
    });
    
    // Get the attachments section
    const attachmentsSection = element.querySelector('#grievanceAttachmentsSection');
    const attachmentsList = element.querySelector('#grievanceAttachmentsList');
    
    // Store original display states
    const attachmentsOriginalDisplay = attachmentsSection ? attachmentsSection.style.display : 'none';
    
    // Hide attachments temporarily to capture main details first
    if (attachmentsSection) {
        attachmentsSection.style.display = 'none';
    }
    
    // Use html2canvas to capture the main content (without attachments)
    html2canvas(element, {
        scale: 2,
        useCORS: true,
        logging: false,
        backgroundColor: '#ffffff'
    }).then(canvas => {
        // Create PDF with jsPDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('p', 'mm', 'a4');
        const imgWidth = 190; // A4 width minus margins
        const pageHeight = 295;
        const imgHeight = canvas.height * imgWidth / canvas.width;
        let heightLeft = imgHeight;
        let position = 10;
        
        // Add main details to first page(s)
        const imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
        heightLeft -= pageHeight;
        
        // Add more pages if main details is longer than one page
        while (heightLeft >= 0) {
            position = heightLeft - imgHeight + 10;
            doc.addPage();
            doc.addImage(imgData, 'PNG', 10, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
        }
        
        // Now process attachments - show attachments section
        if (attachmentsSection) {
            attachmentsSection.style.display = '';
        }
        
        const attachmentDivs = element.querySelectorAll('#grievanceAttachmentsList > div');
        
        if (attachmentDivs.length > 0) {
            // Capture each attachment on a separate page
            const captureAttachment = (index) => {
                if (index >= attachmentDivs.length) {
                    // All attachments captured, restore and save
                    attachmentButtons.forEach(btn => {
                        btn.style.display = '';
                    });
                    if (attachmentsSection) {
                        attachmentsSection.style.display = attachmentsOriginalDisplay;
                    }
                    doc.save('grievance-' + document.getElementById('viewed_grievance_id').value + '.pdf');
                    return;
                }
                
                // Hide all attachments except current one
                attachmentDivs.forEach((div, i) => {
                    div.style.display = (i === index) ? '' : 'none';
                });
                
                html2canvas(element, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                }).then(attachmentCanvas => {
                    const attImgData = attachmentCanvas.toDataURL('image/png');
                    const attImgHeight = attachmentCanvas.height * imgWidth / attachmentCanvas.width;
                    let attHeightLeft = attImgHeight;
                    let attPosition = 10;
                    
                    // Add new page for this attachment
                    doc.addPage();
                    
                    doc.addImage(attImgData, 'PNG', 10, attPosition, imgWidth, attImgHeight);
                    attHeightLeft -= pageHeight;
                    
                    // Add more pages if needed for this attachment
                    while (attHeightLeft >= 0) {
                        attPosition = attHeightLeft - attImgHeight + 10;
                        doc.addPage();
                        doc.addImage(attImgData, 'PNG', 10, attPosition, imgWidth, attImgHeight);
                        attHeightLeft -= pageHeight;
                    }
                    
                    // Capture next attachment
                    captureAttachment(index + 1);
                });
            };
            
            // Start capturing attachments
            captureAttachment(0);
        } else {
            // No attachments, restore and save
            attachmentButtons.forEach(btn => {
                btn.style.display = '';
            });
            if (attachmentsSection) {
                attachmentsSection.style.display = attachmentsOriginalDisplay;
            }
            doc.save('grievance-' + document.getElementById('viewed_grievance_id').value + '.pdf');
        }
    }).catch(error => {
        // Restore buttons on error too
        attachmentButtons.forEach(btn => {
            btn.style.display = '';
        });
        if (attachmentsSection) {
            attachmentsSection.style.display = attachmentsOriginalDisplay;
        }
        
        console.error('Error generating PDF:', error);
        Swal.fire('Error', 'Failed to generate PDF. Please try again.', 'error');
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

// View Attachments Modal
function viewAttachments(grievanceId) {
    document.getElementById('attachment_grievance_id').value = grievanceId;
    document.getElementById('attachmentMsg').innerHTML = '';
    
    // Load attachments
    fetch(`{{ url('grievances') }}/${grievanceId}/attachments`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const attachmentsDiv = document.getElementById('currentAttachments');
                if (data.attachments && data.attachments.length > 0) {
                    let html = '';
                    data.attachments.forEach(att => {
                        // Use public assets path
                        let imageUrl = '{{ asset('assets/grievance_attachments/') }}/' + att.image_path.split('/').pop();
                        html += `
                            <div class="col-md-4 col-sm-6" style="margin-bottom: 15px;">
                                <div class="thumbnail">
                                    <img src="${imageUrl}" alt="Attachment" style="width: 100%; height: 150px; object-fit: cover;">
                                    <div class="caption text-center">
                                        <p><small>${att.uploaded_datetime}</small></p>
                                        <p>
                                            <a href="${imageUrl}" target="_blank" class="btn btn-xs btn-primary"><i class="fa fa-eye"></i> View</a>
                                            <button onclick="deleteAttachment(${grievanceId}, ${att.attachment_id})" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    attachmentsDiv.innerHTML = html;
                } else {
                    attachmentsDiv.innerHTML = '<p class="text-muted">No attachments uploaded yet.</p>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading attachments:', error);
        });

    $('#attachmentModal').modal('show');
}

// Delete Attachment
function deleteAttachment(grievanceId, attachmentId) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this attachment?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d9534f',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.value) {
            fetch(`{{ url('grievances') }}/${grievanceId}/attachment/${attachmentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success', 'Attachment deleted successfully!', 'success');
                    viewAttachments(grievanceId); // Reload attachments
                    $('#grievances_table').DataTable().ajax.reload(); // Reload table
                } else {
                    Swal.fire('Error', data.message || 'Error deleting attachment', 'error');
                }
            })
            .catch(error => {
                console.error('Error deleting attachment:', error);
                Swal.fire('Error', 'Error deleting attachment', 'error');
            });
        }
    });
}

// Attachment Upload Form Handler
document.getElementById('attachmentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const grievanceId = formData.get('grievance_id');

    fetch(`{{ url('grievances') }}/${grievanceId}/upload-attachment`, {
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
            Swal.fire('Success', 'Attachment uploaded successfully!', 'success');
            document.getElementById('attachment_file').value = ''; // Clear file input
            viewAttachments(grievanceId); // Reload attachments
            $('#grievances_table').DataTable().ajax.reload(); // Reload table
        } else {
            document.getElementById('attachmentMsg').innerHTML = '<div class="alert alert-danger">' + (data.message || 'Error uploading attachment') + '</div>';
        }
    })
    .catch(error => {
        console.error('Error uploading attachment:', error);
        document.getElementById('attachmentMsg').innerHTML = '<div class="alert alert-danger">Error uploading attachment</div>';
    });
});
</script>
@endsection