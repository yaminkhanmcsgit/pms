@extends('layouts.app')

@section('title', 'ملازم میں ترمیم کریں')

@section('content')
<div class="container" dir="rtl">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">ملازم میں ترمیم کریں</h3>
        </div>
        <div class="panel-body">
            <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="form-modern">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>ولدیت</label>
                        <input type="text" name="walid_ka_nam" class="form-control urdu-input" lang="ur" value="{{ $employee->walid_ka_nam }}" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-6">
                        <label>نام</label>
                        <input type="text" name="nam" class="form-control urdu-input" lang="ur" value="{{ $employee->nam }}" required style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>موضع</label>
                        @if($role_id == 1)
                        <select name="moza_id" id="moza_id" class="form-control" required data-selected="{{ $employee->moza_id }}">
                            <option value="">منتخب کریں</option>
                        </select>
                        @else
                        <select name="moza_id" id="moza_id" class="form-control" required>
                            <option value="">منتخب کریں</option>
                            @foreach($mozas as $moza)
                                <option value="{{ $moza->mozaId }}" @if($employee->moza_id == $moza->mozaId) selected @endif>{{ $moza->mozaNameUrdu }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label>تحصیل</label>
                        @if($role_id == 1)
                        <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id', '{{ $employee->moza_id }}')" data-selected="{{ $employee->tehsil_id }}"></select>
                        @else
                        <input type="hidden" name="tehsil_id" value="{{ $employee->tehsil_id }}">
                        <input type="text" class="form-control" value="{{ $employee->tehsil_name }}" readonly>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label>ضلع</label>
                        @if($role_id == 1)
                        <select name="zila_id" id="zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id', '{{ $employee->tehsil_id }}')" data-selected="{{ $employee->zila_id }}"></select>
                        @else
                        <input type="hidden" name="zila_id" value="{{ $employee->zila_id }}">
                        <input type="text" class="form-control" value="{{ $employee->district_name }}" disabled>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>پتہ</label>
                        <input type="text" name="pata" class="form-control urdu-input" lang="ur" value="{{ $employee->pata }}" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-3">
                        <label>فون</label>
                        <input type="number" name="phone" class="form-control" lang="ur" value="{{ $employee->phone }}" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-3">
                        <label>شناختی کارڈ</label>
                        <input type="number" name="cnic" class="form-control " lang="ur" value="{{ $employee->cnic }}" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>تعلیم</label>
                        <input type="text" name="darja_taleem" class="form-control urdu-input" lang="ur" value="{{ $employee->darja_taleem }}" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-4">
                        <label>اہلکار کی قسم</label>
                        <select name="ahalkar_type" class="form-control" required>
                            <option value="">منتخب کریں</option>
                            @foreach($employee_types as $type)
                                <option value="{{ $type->ahalkar_type_id }}" @if($employee->ahalkar_type == $type->ahalkar_type_id) selected @endif>{{ $type->ahalkar_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>تاریخ شمولیت</label>
                        <input type="date" name="tareekh_shamil" class="form-control" value="{{ $employee->tareekh_shamil }}">
                    </div>
                </div>

                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> اپڈیٹ کریں
                    </button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                        <i class="fa fa-times"></i> منسوخ کریں
                    </a>
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
</div>
@endsection
