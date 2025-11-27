@extends('layouts.app')

@section('title', 'نیا ملازم شامل کریں')

@section('content')
<div class="container" dir="rtl">
    <h3 class="mb-4">نیا ملازم شامل کریں</h3>

    <form action="{{ route('employees.store') }}" method="POST" class="form-modern">
        @csrf

        <div class="row mb-3" >
             <div class="col-md-6">
                <label>ولدیت</label>
                <input type="text" name="walid_ka_nam" class="form-control urdu-input" lang="ur" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
            </div>
            <div class="col-md-6">
                <label>نام</label>
                <input type="text" name="nam" class="form-control urdu-input" lang="ur" required style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
            </div>
           
        </div>

        <div class="row mb-3">
           
        <div class="col-md-4">
                <label>نام موضع</label>
               
               <select name="moza_id" id="moza_id" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($mozas as $moza)
                        <option value="{{ $moza->mozaId }}">{{ $moza->mozaNameUrdu }}</option>
                    @endforeach
                </select>
               
               
            </div>
     <div class="col-md-4">
        <label>نام تحصیل</label>
        @if($role_id == 1)
        <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id')">
            <option value="">--- تحصیل منتخب کریں ---</option>
        </select>
        @else
        <input type="hidden" name="tehsil_id" value="{{ $tehsils->first()->tehsilId }}">
        <input type="text" class="form-control" value="{{ $tehsils->first()->tehsilNameUrdu }}" disabled>
        @endif
    </div>
    <div class="col-md-4">
        <label>نام ضلع</label>
        @if($role_id == 1)
        <select name="zila_id" id="zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id')">
            <option value="">--- ضلع منتخب کریں ---</option>
        </select>
        @else
        <input type="hidden" name="zila_id" value="{{ $districts->first()->districtId }}">
        <input type="text" class="form-control" value="{{ $districts->first()->districtNameUrdu }}" disabled>
        @endif
    </div>

   

   
</div>

        

        <div class="row mb-3">
            <div class="col-md-6">
                <label>پتہ</label>
                <input type="text" name="pata" class="form-control urdu-input" lang="ur" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
            </div>
            <div class="col-md-3">
                <label>فون</label>
                <input type="number" name="phone" class="form-control" lang="ur" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
            </div>
            <div class="col-md-3">
                <label>شناختی کارڈ</label>
                <input type="number" name="cnic" class="form-control " lang="ur" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label>تعلیم</label>
                <input type="text" name="darja_taleem" class="form-control urdu-input" lang="ur" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
            </div>
            <div class="col-md-4">
                <label>اہلکار کی قسم</label>
                <select name="ahalkar_type" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($employee_types as $type)
                        <option value="{{ $type->ahalkar_type_id }}">{{ $type->ahalkar_title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>تاریخ شمولیت</label>
                <input type="date" name="tareekh_shamil" class="form-control">
            </div>
        </div>

        <br>

        <div class="mt-3 text-right">
            <button type="submit" class="btn btn-success">
                <i class="fa fa-save"></i> محفوظ کریں
            </button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                <i class="fa fa-times"></i> منسوخ کریں
            </a>
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
</div>
@endsection
