@extends('layouts.app')

@section('title', 'نیا ملازم شامل کریں')

@section('content')
<div class="container" dir="rtl">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">نیا ملازم شامل کریں</h3>
        </div>
        <div class="panel-body">
            <form action="{{ url('test_post.php') }}" method="POST" class="form-modern" accept-charset="UTF-8">
                @csrf

                <div class="row mb-3" >
                     <div class="col-md-6">
                        <label>ولدیت <span style="color: red;">*</span></label>
                        <input type="text" name="walid_ka_nam" class="form-control urdu-input" lang="ur" required tabindex="2" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-6">
                        <label>نام <span style="color: red;">*</span></label>
                        <input type="text" name="nam" class="form-control urdu-input" lang="ur" required tabindex="1" autofocus style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>

                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>نام ضلع <span style="color: red;">*</span></label>
                        @if($role_id == 1)
                        <select name="zila_id" id="zila_id" class="form-control" required tabindex="3" onchange="onDistrictChange(this.value, 'tehsil_id')">
                            <option value="">--- ضلع منتخب کریں ---</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->districtId }}">{{ $district->districtNameUrdu }}</option>
                            @endforeach
                        </select>
                        @else
                        <input type="hidden" name="zila_id" value="{{ optional($districts->first())->districtId }}">
                        <input type="text" class="form-control" value="{{ optional($districts->first())->districtNameUrdu }}" disabled>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label>نام تحصیل <span style="color: red;">*</span></label>
                        @if($role_id == 1)
                        <select name="tehsil_id" id="tehsil_id" class="form-control" required tabindex="4" onchange="onTehsilChange(this.value, 'moza_id')">
                            <option value="">--- تحصیل منتخب کریں ---</option>
                            @foreach($tehsils as $tehsil)
                                <option value="{{ $tehsil->tehsilId }}">{{ $tehsil->tehsilNameUrdu }}</option>
                            @endforeach
                        </select>
                        @else
                        <select name="tehsil_id" class="form-control" required tabindex="4" onchange="onTehsilChange(this.value, 'moza_id')">
                            <option value="">--- تحصیل منتخب کریں ---</option>
                            @foreach($tehsils as $tehsil)
                                <option value="{{ $tehsil->tehsilId }}">{{ $tehsil->tehsilNameUrdu }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label>نام موضع <span style="color: red;">*</span></label>
                        @if($role_id == 1)
                        <select name="moza_id" id="moza_id" class="form-control" required tabindex="5">
                            <option value="">منتخب کریں</option>
                        </select>
                        @else
                        <select name="moza_id" id="moza_id" class="form-control" required tabindex="5">
                            <option value="">منتخب کریں</option>
                            @foreach($mozas as $moza)
                                <option value="{{ $moza->mozaId }}">{{ $moza->mozaNameUrdu }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>



                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>پتہ</label>
                        <input type="text" name="pata" class="form-control urdu-input" lang="ur" tabindex="8" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-3">
                        <label>فون</label>
                        <input type="number" name="phone" class="form-control" tabindex="7">
                    </div>
                    <div class="col-md-3">
                        <label>شناختی کارڈ</label>
                        <input type="number" name="cnic" class="form-control " tabindex="6">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>تعلیم</label>
                        <input type="text" name="darja_taleem" class="form-control urdu-input" lang="ur" tabindex="11" style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">
                    </div>
                    <div class="col-md-4">
                        <label>اہلکار کی قسم <span style="color: red;">*</span></label>
                        <select name="ahalkar_type" class="form-control" required tabindex="10">
                            <option value="">منتخب کریں</option>
                            @foreach($employee_types as $type)
                                <option value="{{ $type->ahalkar_type_id }}">{{ $type->ahalkar_title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>تاریخ شمولیت <span style="color: red;">*</span></label>
                        <input type="date" name="tareekh_shamil" class="form-control" required tabindex="9">
                    </div>
                </div>

                <br>

                <div class="mt-3 text-right">
                    <button type="submit" class="btn btn-success" tabindex="12">
                        <i class="fa fa-save"></i> محفوظ کریں
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
