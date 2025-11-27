@extends('layouts.app')

@section('title', 'تکمیلی عمل')

@section('content')
<div class="container" dir="rtl">
    <style>
        thead th {
            border:1px solid #ddd8d8 !important;
        }
        .value-cell {
            background-color: #d4edda !important;
        }
    </style>
    <center><h3 class="mb-3">تکمیلی عمل</h3></center>
<style>
    .form-group {
        float:left;width:100%;
    }
    .readonly-select {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
    }
</style>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#completion-process" aria-controls="completion-process" role="tab" data-toggle="tab">تکمیلی عمل کی فہرست</a>
        </li>
        <li role="presentation">
            <a href="#target-values" aria-controls="target-values" role="tab" data-toggle="tab">ہدف کی اقدار</a>
        </li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="completion-process">
            <br>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('completion_process.create') }}" class="btn btn-success">
                    <i class="fa fa-plus"></i> نیا اندراج
                </a>
            </div>

            <div class="table-responsive">
                <table id="completion_process_table" class="table table-striped table-hover table-bordered text-center" style="width: 100%;">
                    <thead class="thead-dark">
                        <tr>
                            <th>نمبر شمار</th>
                            <th>نام ضلع</th>
                            <th>نام تحصیل</th>
                            <th>نام موضع</th>
                            <th>نام اہلکار</th>
                            <th>اہلکار کی قسم</th>
                            <th>میزان کھاتہ دار/کھتونی</th>
                            <th>پختہ کھتونی درانڈکس خسرہ</th>
                            <th>درستی بدرات</th>
                            <th>تحریر نقل شجرہ نسب</th>
                            <th>تحریر شجرہ نسب مالکان قبضہ</th>
                            <th>پختہ کھاتاجات</th>
                            <th>خام کھاتہ جات در شجرہ نسب</th>
                            <th>تحریر مشترکہ کھاتہ</th>
                            <th>پختہ نمبرواں در کھتونی</th>
                            <th>خام نمبرواں در کھتونی</th>
                            <th>تصدیق آخیر</th>
                            <th>متفرق کام</th>
                            <th>تاریخ</th>
                            <th class="no-print">عمل</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div role="tabpanel" class="tab-pane" id="target-values">
            <br>
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-primary" onclick="openTargetModal()">
                    <i class="fa fa-plus"></i> ہدف کی قیمت شامل کریں
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered text-center" id="targetValuesTable">
                    <thead class="thead-dark">
                        <tr>
                            <th>نمبر شمار</th>
                            <th>نام موضع</th>
                            <th>قسم کام</th>
                            <th> ہدف </th>
                            <th>عمل</th>
                        </tr>
                    </thead>
                    <tbody id="targetValuesBody">
                        <!-- Data will be loaded via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Target Value Modal -->
<div class="modal fade" id="targetModal" tabindex="-1" role="dialog" aria-labelledby="targetModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="targetForm">
                @csrf
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="targetModalLabel">ہدف کی قیمت شامل/ترمیم کریں</h4>
                </div>
                <div class="modal-body">
                    <div id="targetMsg"></div>
                    @if(session('role_id') == 1)
                    <!-- Admin: Show all dropdowns -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_zila_id">ضلع</label>
                                <select name="zila_id" id="modal_zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'modal_tehsil_id')">
                                    <option value="">منتخب کریں</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_tehsil_id">تحصیل</label>
                                <select id="modal_zila_id" name="tehsil_id" id="modal_tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'modal_moza_id')">
                                    <option value="">منتخب کریں</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @else
                    <!-- Limited user: Show hidden inputs and readonly text -->
                    <input type="hidden" name="zila_id" value="{{ session('zila_id') }}">
                    <input type="hidden" id="modal_tehsil_id" name="tehsil_id" value="{{ session('tehsil_id') }}">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_zila_display">ضلع</label>
                                <input type="text" id="modal_zila_display" class="form-control" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal_tehsil_display">تحصیل</label>
                                <input type="text" id="modal_tehsil_display" class="form-control" readonly>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        
                     <div class="col-md-6">
                    <div class="form-group">
                        <label for="modal_completion_process_type_id">قسم کام</label>
                        <select name="completion_process_type_id" id="modal_completion_process_type_id" class="form-control" required>
                            <option value="">منتخب کریں</option>
                        </select>
                    </div>
                </div>
                 <div class="col-md-6">
                    <div class="form-group">
                        <label for="modal_moza_id">موضع</label>
                        <select name="moza_id" id="modal_moza_id" class="form-control" required>
                            <option value="">منتخب کریں</option>
                        </select>
                    </div>
                </div>
                </div>
                <div class="row">
                     <div class="col-md-6 pull-right">
                    <div class="form-group">
                        <label for="target_value">ہدف کی قیمت</label>
                        <input type="number" step="0.01" name="target_value" id="target_value" class="form-control" required>
                        <input type="hidden" name="target_value_id" id="target_value_id">
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">بند کریں</button>
                    <button type="submit" class="btn btn-primary">محفوظ کریں</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    loadTargetValues();
    loadModalDropdowns();

    $('#completion_process_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('completion_process.datatable') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id', orderable: false },
            { data: 'districtNameUrdu', orderable: false },
            { data: 'tehsilNameUrdu', orderable: false },
            { data: 'mozaNameUrdu', orderable: false },
            { data: 'employee_name', orderable: false },
            { data: 'employee_type_title', orderable: false },
            { data: 'mizan_khata_dar_khatoni', orderable: false, className: 'text-center' },
            { data: 'pukhta_khatoni_drandkas_khasra', orderable: false, className: 'text-center' },
            { data: 'durusti_badrat', orderable: false, className: 'text-center' },
            { data: 'tehreer_naqal_shajra_nasab', orderable: false, className: 'text-center' },
            { data: 'tehreer_shajra_nasab_malkan_qabza', orderable: false, className: 'text-center' },
            { data: 'pukhta_khatajat', orderable: false, className: 'text-center' },
            { data: 'kham_khatajat_dar_shajra_nasab', orderable: false, className: 'text-center' },
            { data: 'tehreer_mushtarka_khata', orderable: false, className: 'text-center' },
            { data: 'pukhta_numberwan_dar_khatoni', orderable: false, className: 'text-center' },
            { data: 'kham_numberwan_dar_khatoni', orderable: false, className: 'text-center' },
            { data: 'tasdeeq_akhir', orderable: false, className: 'text-center' },
            { data: 'mutafarriq_kaam', orderable: false, className: 'text-center' },
            { data: 'tareekh', orderable: true },
            { data: 'actions', orderable: false }
        ],
        columnDefs: [
            {
                targets: [6,7,8,9,10,11,12,13,14,15,16,17],
                createdCell: function (td, cellData, rowData, row, col) {
                    if (cellData > 0) {
                        $(td).addClass('value-cell');
                    }
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25
        
    });
});

function loadTargetValues() {
    $.get('{{ route("completion_process.target_values") }}', function(data) {
        var tbody = $('#targetValuesBody');
        tbody.empty();
        data.forEach(function(item, index) {
            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.moza_name}</td>
                    <td>${item.type_title}</td>
                    <td>${item.target_value}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" onclick="editTarget(${item.moza_id}, ${item.completion_process_type_id}, ${item.target_value_id})">
                            <i class="fa fa-edit"></i> ترمیم
                        </button>
                    </td>
                </tr>
            `);
        });
    });
}

function loadModalDropdowns() {
    // Load districts for modal with role-based filtering
    @if(session('role_id') == 1)
    // Admin: load all districts
    fetch("{{ url('api/districts') }}")
        .then(res => res.json())
        .then(data => {
            populateDropdown(document.getElementById('modal_zila_id'), data, 'zila_id', 'zilaNameUrdu');
        });
    @else
    // Limited user: just set display values for readonly fields
    fetch("{{ url('api/districts') }}")
        .then(res => res.json())
        .then(data => {
            // Filter to only their district
            var filteredData = data.filter(item => item.zila_id == {{ session('zila_id') }});
            // Set display values for readonly fields
            if (filteredData.length > 0) {
                document.getElementById('modal_zila_display').value = filteredData[0].zilaNameUrdu;
            }
            // Set tehsil display value
            fetch("{{ url('api/tehsils') }}?district_id={{ session('zila_id') }}")
                .then(res => res.json())
                .then(tehsilData => {
                    var filteredTehsil = tehsilData.filter(item => item.tehsil_id == {{ session('tehsil_id') }});
                    if (filteredTehsil.length > 0) {
                        document.getElementById('modal_tehsil_display').value = filteredTehsil[0].tehsilNameUrdu;
                    }
                });
        });
    @endif

    // Load completion process types
    $.get('{{ url("api/completion-process-types") }}', function(data) {
        var select = $('#modal_completion_process_type_id');
        select.empty();
        select.append('<option value="">منتخب کریں</option>');
        data.forEach(function(item) {
            select.append(`<option value="${item.id}">${item.title_ur}</option>`);
        });
    });
}

function openTargetModal(mozaId = null, typeId = null, targetValueId = null) {
    $('#targetForm')[0].reset();
    $('#targetMsg').empty();
    $('#target_value_id').val('');

    @if(session('role_id') == 1)
    // Admin: Reset disabled states
    $('#modal_zila_id').prop('disabled', false);
    $('#modal_tehsil_id').prop('disabled', false);
    $('#modal_moza_id').prop('disabled', false);
    @else
    // Limited user: Populate readonly display fields and moza dropdown
    fetch("{{ url('api/districts') }}")
        .then(res => res.json())
        .then(data => {
            var filteredData = data.filter(item => item.zila_id == {{ session('zila_id') }});
            if (filteredData.length > 0) {
                document.getElementById('modal_zila_display').value = filteredData[0].zilaNameUrdu;
            }
            return fetch("{{ url('api/tehsils') }}?district_id={{ session('zila_id') }}");
        })
        .then(res => res.json())
        .then(tehsilData => {
            var filteredTehsil = tehsilData.filter(item => item.tehsil_id == {{ session('tehsil_id') }});
            if (filteredTehsil.length > 0) {
                document.getElementById('modal_tehsil_display').value = filteredTehsil[0].tehsilNameUrdu;
            }
            // Populate moza dropdown for limited user
            return fetch("{{ url('api/mozas') }}?tehsil_id={{ session('tehsil_id') }}");
        })
        .then(res => res.json())
        .then(mozaData => {
            populateDropdown(document.getElementById('modal_moza_id'), mozaData, 'moza_id', 'mozaNameUrdu');
        });
    @endif

    if (targetValueId) {
        @if(session('role_id') == 1)
        // Admin: Editing existing target value - make location fields disabled
        $('#modal_zila_id').prop('disabled', true);
        $('#modal_tehsil_id').prop('disabled', true);
        $('#modal_moza_id').prop('disabled', true);
        @else
        // Limited user: Disable moza dropdown when editing
        $('#modal_moza_id').prop('disabled', true);
        @endif
        $('#target_value_id').val(targetValueId);

        $.get('{{ route("completion_process.get_target_value") }}', {moza_id: mozaId, completion_process_type_id: typeId}, function(data) {
            if (data) {
                $('#target_value').val(data.target_value);
                $('#modal_completion_process_type_id').val(typeId);
                @if(session('role_id') == 1)
                // Admin: Set selected values for cascading dropdowns
                $('#modal_zila_id').val(data.district_id);
                onDistrictChange(data.district_id, 'modal_tehsil_id', data.tehsil_id);
                setTimeout(function() {
                    onTehsilChange(data.tehsil_id, 'modal_moza_id', data.moza_id);
                }, 300);
                @else
                // Limited user: Set moza value
                $('#modal_moza_id').val(mozaId);
                @endif
            }
        });
    } else {
        @if(session('role_id') == 1)
        // Admin: Reset dropdowns for new entry
        $('#modal_zila_id').val('');
        populateDropdown(document.getElementById('modal_tehsil_id'), [], 'tehsil_id', 'tehsilNameUrdu');
        populateDropdown(document.getElementById('modal_moza_id'), [], 'moza_id', 'mozaNameUrdu');
        @endif
    }
    $('#targetModal').modal('show');
}

function editTarget(mozaId, typeId, targetValueId) {
    openTargetModal(mozaId, typeId, targetValueId);
}

$('#targetForm').on('submit', function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $.post('{{ route("completion_process.store_target_value") }}', formData, function(response) {
        if (response.success) {
            $('#targetMsg').html('<div class="alert alert-success">' + response.message + '</div>');
            loadTargetValues();
            setTimeout(function() {
                $('#targetModal').modal('hide');
            }, 1000);
        } else {
            $('#targetMsg').html('<div class="alert alert-danger">خرابی</div>');
        }
    }).fail(function() {
        $('#targetMsg').html('<div class="alert alert-danger">سرور کی خرابی</div>');
    });
});
</script>
@endsection
