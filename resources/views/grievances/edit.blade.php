@extends('layouts.app')

@section('title', 'Edit Grievance')

@section('content')
<div class="container" dir="ltr" style="max-width: 800px;margin: 0 auto;background: #fff;">
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
    <div class="text-center" style="margin-bottom:20px;">
        <h4><strong>GOVERNMENT OF KHYBER PAKHTUNKHWA</strong></h4>
        <h5>BOARD OF REVENUE KHYBER PAKHTUNKHWA</h5>
        <h5>SETTLEMENT OF LAND RECORDS DIR/KALAM PROJECT</h5>
    </div>

    <form action="{{ route('grievances.update', $grievance->id) }}" method="POST" class="form-modern">
        @csrf
        @method('PUT')

        <div class="row" style="margin-bottom:20px;">
            @if($role_id == 1)
            <!-- Admin: Show all dropdowns -->
            <div class="col-xs-6">
                <strong>District:</strong>
                <select name="district_id" id="zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id', '{{ $grievance->tehsil }}')" data-selected="{{ $grievance->district }}" style="display: inline-block; width: 60%;">
                    <option value="">Select District</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->districtId }}" @if($grievance->district == $district->districtId) selected @endif>{{ $district->districtNameUrdu }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-xs-6">
                <strong>Tehsil:</strong>
                <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id', '{{ $grievance->village_name }}')" data-selected="{{ $grievance->tehsil }}" style="display: inline-block; width: 60%;">
                    <option value="">Select Tehsil</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}" @if($grievance->tehsil == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <!-- Limited user: Show hidden inputs and readonly text -->
            <input type="hidden" name="district_id" value="{{ $districts->first()->districtId }}">
            <input type="hidden" name="tehsil_id" value="{{ $tehsils->first()->tehsilId }}">
            <div class="col-xs-6">
                <strong>District:</strong>
                <input type="text" class="form-control" value="{{ $districts->first()->districtNameUrdu }}" readonly style="display: inline-block; width: 60%;">
            </div>
            <div class="col-xs-6">
                <strong>Tehsil:</strong>
                <input type="text" class="form-control" value="{{ $tehsils->first()->tehsilNameUrdu }}" readonly style="display: inline-block; width: 60%;">
            </div>
            @endif
        </div>

        <h4 style="margin-bottom:20px;">
            <strong>PROFORMA FOR REDRESSAL OF APPLICATION / GRIEVANCE DURING LAND SETTLEMENT OPERATIONS</strong>
        </h4>

        <table class="table table-bordered">
            <tr>
                <td style="width:30%;">1. Name of Applicant:</td>
                <td><input type="text" name="applicant_name" class="form-control urdu-input" value="{{ $grievance->applicant_name }}" required style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)"></td>
            </tr>
            <tr>
                <td>2. Father's Name:</td>
                <td><input type="text" name="father_name" class="form-control urdu-input" value="{{ $grievance->father_name }}" required style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)"></td>
            </tr>
            <tr>
                <td>3. CNIC No.:</td>
                <td><input type="text" name="cnic" class="form-control" value="{{ $grievance->cnic }}" required></td>
            </tr>
            <tr>
                <td>4. Address / Contact No.:</td>
                <td><input type="text" name="address" class="form-control urdu-input" value="{{ $grievance->address }}" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)"></td>
            </tr>
            <tr>
                <td>5. Mouza / Village Name:</td>
                <td>
                    @if($role_id == 1)
                    <select name="moza_id" id="moza_id" class="form-control" required data-selected="{{ $grievance->village_name }}">
                        <option value="">Select Mouza</option>
                    </select>
                    @else
                    <select name="moza_id" id="moza_id" class="form-control" required>
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
                <td><input type="text" name="nature_of_grievance" class="form-control urdu-input" value="{{ $grievance->nature_of_grievance }}" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)"></td>
            </tr>
        </table>

        <div style="margin-left:10px; margin-bottom:20px;">
            <label>Grievance Type:</label>
            <select name="grievance_type_id" class="form-control" required>
                <option value="">Select Type</option>
                @foreach($types as $type)
                    <option value="{{ $type->id }}" @if($grievance->grievance_type_id == $type->id) selected @endif>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>7. Brief Description of Grievance:</label>
            <textarea name="grievance_description" class="form-control urdu-input" rows="4" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">{{ $grievance->grievance_description }}</textarea>
           
        </div>

        <div class="form-group">
            <label>8. Date of Receipt of Application:</label>
            <input type="date" name="application_date" class="form-control" value="{{ $grievance->application_date ? date('Y-m-d', strtotime($grievance->application_date)) : '' }}" required>
        </div>

        <div class="form-group">
            <label>Status:</label>
            <select name="status_id" class="form-control" required>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @if($grievance->status_id == $status->id) selected @endif>{{ $status->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg">Update Grievance</button>
        </div>
    </form>
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