@extends('layouts.app')

@section('title', 'ملازم میں ترمیم کریں')

@section('content')
<div class="container" dir="rtl">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">ملازم میں ترمیم کریں</h3>
        </div>
        <div class="panel-body">
            <form action="{{ route('employees.update', $employee->id) }}" method="POST" class="form-modern" accept-charset="UTF-8">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>ولدیت <span style="color: red;">*</span></label>
                        <input type="text" name="walid_ka_nam" class="form-control urdu-input" lang="ur" value="{{ $employee->walid_ka_nam }}" tabindex="2" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)" required>
                    </div>
                    <div class="col-md-6">
                        <label>نام <span style="color: red;">*</span></label>
                        <input type="text" name="nam" class="form-control urdu-input" lang="ur" value="{{ $employee->nam }}" required tabindex="1" autofocus style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>موضع <span style="color: red;">*</span></label>
                        @if($role_id == 1)
                        <select name="moza_id" id="moza_id" class="form-control" required tabindex="5" data-selected="{{ $employee->moza_id }}">
                            <option value="">منتخب کریں</option>
                        </select>
                        @else
                        <select name="moza_id" id="moza_id" class="form-control" required tabindex="5">
                            <option value="">منتخب کریں</option>
                            @foreach($mozas as $moza)
                                <option value="{{ $moza->mozaId }}" @if($employee->moza_id == $moza->mozaId) selected @endif>{{ $moza->mozaNameUrdu }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label>تحصیل <span style="color: red;">*</span></label>
                        @if($role_id == 1)
                        <select name="tehsil_id" id="tehsil_id" class="form-control" required tabindex="4" onchange="onTehsilChange(this.value, 'moza_id', '{{ $employee->moza_id }}')" data-selected="{{ $employee->tehsil_id }}">
                            <option value="">منتخب کریں</option>
                            @foreach($tehsils as $tehsil)
                                <option value="{{ $tehsil->tehsilId }}" @if($employee->tehsil_id == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                            @endforeach
                        </select>
                        @else
                        <select name="tehsil_id" class="form-control" required tabindex="4" onchange="onTehsilChange(this.value, 'moza_id', '{{ $employee->moza_id }}')">
                            <option value="">منتخب کریں</option>
                            @foreach($tehsils as $tehsil)
                                <option value="{{ $tehsil->tehsilId }}" @if($employee->tehsil_id == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label>ضلع <span style="color: red;">*</span></label>
                        @if($role_id == 1)
                        <select name="zila_id" id="zila_id" class="form-control" required tabindex="3" onchange="onDistrictChange(this.value, 'tehsil_id', '{{ $employee->tehsil_id }}')" data-selected="{{ $employee->zila_id }}">
                            <option value="">منتخب کریں</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->districtId }}" @if($employee->zila_id == $district->districtId) selected @endif>{{ $district->districtNameUrdu }}</option>
                            @endforeach
                        </select>
                        @else
                        <input type="hidden" name="zila_id" value="{{ $employee->zila_id }}">
                        <input type="text" class="form-control" value="{{ optional($districts->first())->districtNameUrdu ?? ($employee->district_name ?? '') }}" disabled>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>پتہ</label>
                        <input type="text" name="pata" class="form-control urdu-input" lang="ur" value="{{ $employee->pata }}" tabindex="8" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-3">
                        <label>فون</label>
                        <input type="number" name="phone" class="form-control" value="{{ $employee->phone }}" tabindex="7">
                    </div>
                    <div class="col-md-3">
                        <label>شناختی کارڈ</label>
                        <input type="number" name="cnic" class="form-control " lang="ur" value="{{ $employee->cnic }}" tabindex="6">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>تعلیم</label>
                        <input type="text" name="darja_taleem" class="form-control urdu-input" lang="ur" value="{{ $employee->darja_taleem }}" tabindex="11" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-4">
                        <label>اہلکار کی قسم <span style="color: red;">*</span></label>
                        <select name="ahalkar_type" class="form-control" required tabindex="10">
                            <option value="">منتخب کریں</option>
                            @foreach($employee_types as $type)
                                <option value="{{ $type->ahalkar_type_id }}" @if($employee->ahalkar_type == $type->ahalkar_type_id) selected @endif>{{ $type->ahalkar_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>تاریخ شمولیت <span style="color: red;">*</span></label>
                        <input type="date" name="tareekh_shamil" class="form-control" value="{{ $employee->tareekh_shamil }}" required tabindex="9">
                    </div>
                </div>

                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-success" tabindex="12">
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
