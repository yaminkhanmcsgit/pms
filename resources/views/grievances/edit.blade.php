@extends('layouts.app')

@section('title', 'Edit Grievance')

@section('content')
<div class="container" dir="ltr" style="max-width: 800px;margin: 0 auto;">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <style>
    .form-group {
        float: left;width:100%;
    }
</style>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Edit Grievance</h3>
        </div>
        <div class="panel-body">
            <div class="text-center" style="margin-bottom:20px;">
                <h4><strong>GOVERNMENT OF KHYBER PAKHTUNKHWA</strong></h4>
                <h5>BOARD OF REVENUE KHYBER PAKHTUNKHWA</h5>
                <h5>SETTLEMENT OF LAND RECORDS DIR/KALAM PROJECT</h5>
            </div>

            <form action="{{ route('grievances.update', $grievance->id) }}" method="POST" class="form-modern" accept-charset="UTF-8">
                @csrf
                @method('PUT')

        <div class="row" style="margin-bottom:20px;">
            @if($role_id == 1)
            <!-- Admin: Show all dropdowns -->
            <div class="col-xs-6">
                <strong>District <span style="color: red;">*</span>:</strong>
                <select name="district_id" id="zila_id" class="form-control" required tabindex="1" onchange="onDistrictChange(this.value, 'tehsil_id', '{{ $grievance->tehsil }}')" data-selected="{{ $grievance->district }}" style="display: inline-block; width: 60%;">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->districtId }}" @if($grievance->district == $district->districtId) selected @endif>{{ $district->districtNameUrdu }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-6">
                <strong>Tehsil <span style="color: red;">*</span>:</strong>
                <select name="tehsil_id" id="tehsil_id" class="form-control" required tabindex="2" onchange="onTehsilChange(this.value, 'moza_id', '{{ $grievance->village_name }}')" data-selected="{{ $grievance->tehsil }}" style="display: inline-block; width: 60%;">
                    <option value="">Select Tehsil</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}" @if($grievance->tehsil == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <!-- Limited user: Show district readonly and tehsil dropdown -->
            <input type="hidden" name="district_id" value="{{ optional($districts->first())->districtId }}">
            <div class="col-xs-6">
                <strong>District:</strong>
                <input type="text" class="form-control" value="{{ optional($districts->first())->districtNameUrdu }}" readonly style="display: inline-block; width: 60%;">
            </div>
            <div class="col-xs-6">
                <strong>Tehsil <span style="color: red;">*</span>:</strong>
                <select name="tehsil_id" class="form-control" required tabindex="2" onchange="onTehsilChange(this.value, 'moza_id', '{{ $grievance->village_name }}')" data-selected="{{ $grievance->tehsil }}" style="display: inline-block; width: 60%;">
                    <option value="">Select Tehsil</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}" @if($grievance->tehsil == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>

        <h4 style="margin-bottom:20px;">
            <strong>PROFORMA FOR REDRESSAL OF APPLICATION / GRIEVANCE DURING LAND SETTLEMENT OPERATIONS</strong>
        </h4>

        <table class="table table-bordered">
            <tr>
                <td style="width:30%;">1. Name of Applicant <span style="color: red;">*</span>:</td>
                <td><input type="text" name="applicant_name" class="form-control urdu-input" value="{{ $grievance->applicant_name }}" required tabindex="3" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)"></td>
            </tr>
            <tr>
                <td>2. Father's Name <span style="color: red;">*</span>:</td>
                <td><input type="text" name="father_name" class="form-control urdu-input" value="{{ $grievance->father_name }}" required tabindex="4" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)"></td>
            </tr>
            <tr>
                <td>3. CNIC No. <span style="color: red;">*</span>:</td>
                <td><input type="text" name="cnic" class="form-control" value="{{ $grievance->cnic }}" required tabindex="5"></td>
            </tr>
            <tr>
                <td>4. Address / Contact No. <span style="color: red;">*</span>:</td>
                <td><input type="text" name="address" class="form-control " value="{{ $grievance->address }}" required tabindex="6"></td>
            </tr>
            <tr>
                <td>5. Mouza / Village Name <span style="color: red;">*</span>:</td>
                <td>
                    @if($role_id == 1)
                    <select name="moza_id" id="moza_id" class="form-control" required tabindex="7" data-selected="{{ $grievance->village_name }}">
                        <option value="">Select Mouza</option>
                    </select>
                    @else
                    <select name="moza_id" id="moza_id" class="form-control" required tabindex="7">
                        <option value="">Select Mouza</option>
                        @foreach($mozas as $moza)
                            <option value="{{ $moza->mozaId }}" @if($grievance->village_name == $moza->mozaId) selected @endif>{{ $moza->mozaNameUrdu }}</option>
                        @endforeach
                    </select>
                    @endif
                </td>
            </tr>
            <tr>
                <td>6. Nature of Grievance / Application:</td>
                <td><input type="text" name="nature_of_grievance" class="form-control urdu-input" value="{{ $grievance->nature_of_grievance }}" tabindex="8" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)"></td>
            </tr>
        </table>

        <div style="margin-left:10px; margin-bottom:20px;">
            <label>Grievance Type <span style="color: red;">*</span>:</label>
            <select name="grievance_type_id" class="form-control" required tabindex="9">
                <option value="">Select Type</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @if($grievance->grievance_type_id == $type->id) selected @endif>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>7. Brief Description of Grievance:</label>
            <textarea name="grievance_description" class="form-control urdu-input" rows="4" tabindex="10" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">{{ $grievance->grievance_description }}</textarea>
           
        </div>

        <div class="form-group">
            <label>8. Date of Receipt of Application <span style="color: red;">*</span>:</label>
            <input type="date" name="application_date" class="form-control" value="{{ $grievance->application_date ? date('Y-m-d', strtotime($grievance->application_date)) : '' }}" required tabindex="11">
        </div>

        <div class="form-group">
            <label>Status <span style="color: red;">*</span>:</label>
            <select name="main_status_id" id="main_status_id" class="form-control" required tabindex="12">
                <option value="">Select Status</option>
                @php
                    $parentStatusesEdit = $statuses->where('parent_id', null);
                    $childStatusesEdit = $statuses->where('parent_id', '!=', null);
                @endphp
                @foreach($parentStatusesEdit as $parent)
                    <option value="{{ $parent->id }}" @if($grievance->status_id == $parent->id || (isset($grievance->parent_status_name) && $grievance->parent_status_name == $parent->name)) selected @endif>{{ $parent->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group" id="sub_status_group" style="display: none;">
            <label>Priority:</label>
            <select name="sub_status_id" id="sub_status_id" class="form-control">
                <option value="">Select Priority</option>
                @foreach($childStatusesEdit as $child)
                    <option value="{{ $child->id }}" @if($grievance->status_id == $child->id) selected @endif>{{ $child->name }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="status_id" id="status_id" value="{{ $grievance->status_id }}">

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var mainStatusSelect = document.getElementById('main_status_id');
            var subStatusGroup = document.getElementById('sub_status_group');
            var subStatusSelect = document.getElementById('sub_status_id');
            var hiddenStatusId = document.getElementById('status_id');
            
            var statuses = @json($statuses);
            
            function updateStatusId() {
                if (subStatusSelect.value) {
                    hiddenStatusId.value = subStatusSelect.value;
                } else {
                    hiddenStatusId.value = mainStatusSelect.value;
                }
            }
            
            mainStatusSelect.addEventListener('change', function() {
                var selectedId = this.value;
                subStatusSelect.innerHTML = '<option value="">Select Priority</option>';
                
                if (selectedId) {
                    var subStatuses = statuses.filter(function(s) { return s.parent_id == selectedId; });
                    if (subStatuses.length > 0) {
                        subStatuses.forEach(function(s) {
                            var option = document.createElement('option');
                            option.value = s.id;
                            option.textContent = s.name;
                            subStatusSelect.appendChild(option);
                        });
                        subStatusGroup.style.display = 'block';
                        subStatusSelect.required = true;
                    } else {
                        subStatusGroup.style.display = 'none';
                        subStatusSelect.required = false;
                        subStatusSelect.value = '';
                    }
                } else {
                    subStatusGroup.style.display = 'none';
                    subStatusSelect.required = false;
                    subStatusSelect.value = '';
                }
                
                updateStatusId();
            });
            
            subStatusSelect.addEventListener('change', function() {
                updateStatusId();
            });
            
            @if($grievance->parent_status_name)
                mainStatusSelect.value = {{ $statuses->where('name', $grievance->parent_status_name)->first()->id ?? 'null' }};
                mainStatusSelect.dispatchEvent(new Event('change'));
                subStatusSelect.value = {{ $grievance->status_id }};
                updateStatusId();
            @endif
        });
        </script>

            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg" tabindex="13">Update Grievance</button>
            </div>
        </form>
        </div>
    </div>
</div>
@push('scripts')
<script src="{{ url('public/js/urdutextbox.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.urdu-input').forEach(function(input) {
        ActivateUrdu(input);
    });
});
</script>
@endpush
@endsection