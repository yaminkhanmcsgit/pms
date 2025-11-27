@extends('layouts.app')

@section('title', 'نیا پڑتال ریکارڈ شامل کریں')

@section('content')
<div class="container" dir="rtl">
    <center><legend><h3>نیا پڑتال ریکارڈ شامل کریں</h3></legend></center>
    <form action="{{ route('partal.store') }}" method="POST" class="form-modern">
        @csrf
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>نام ضلع</label>
                @if($role_id == 1)
                <select name="zila_id" id="zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id')">
                    <option value="">منتخب کریں</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->districtId }}">{{ $district->districtNameUrdu }}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="zila_id" value="{{ $districts->first()->districtId }}">
                <input type="text" class="form-control" value="{{ $districts->first()->districtNameUrdu }}" disabled>
                @endif
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نام تحصیل</label>
                @if($role_id == 1)
                <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id')">
                    <option value="">منتخب کریں</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}">{{ $tehsil->tehsilNameUrdu }}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="tehsil_id" value="{{ $tehsils->first()->tehsilId }}">
                <input type="text" class="form-control" value="{{ $tehsils->first()->tehsilNameUrdu }}" readonly>
                @endif
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نام موضع</label>
                @if($role_id == 1)
                <select name="moza_id" id="moza_id" class="form-control" required></select>
                @else
                <select name="moza_id" id="moza_id" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($mozas as $moza)
                        <option value="{{ $moza->mozaId }}">{{ $moza->mozaNameUrdu }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>نام پٹواری</label>
                <select name="patwari_nam" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->nam }}">{{ $emp->nam }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نام اہلکار</label>
                <select name="ahalkar_nam" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->nam }}">{{ $emp->nam }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>تاریخ پڑتال</label>
                <input type="date" name="tareekh_partal" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>تصدیق ملکیت پیمودہ/خسراہ</label>
                <input type="number" name="tasdeeq_milkiat_pemuda_khasra" class="form-control">
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>تصدیق ملکیت پیمودہ/بدرات</label>
                <input type="number" name="tasdeeq_milkiat_pemuda_khasra_badrat" class="form-control">
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>تصدیق ملکیت قبضہ/کاشت/خسراہ</label>
                <input type="number" name="tasdeeq_milkiat_qabza_kasht_khasra" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>تصدیق ملکیت قبضہ/کاشت/بدرات</label>
                <input type="number" name="tasdeeq_milkiat_qabza_kasht_badrat" class="form-control">
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>تصدیق شجرہ نسب گوری</label>
                <input type="number" name="tasdeeq_shajra_nasab_guri" class="form-control">
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>تصدیق شجرہ نسب بدرات</label>
                <input type="number" name="tasdeeq_shajra_nasab_badrat" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>مقابلہ کھاتونی چومندہ</label>
                <input type="number" name="muqabala_khatoni_chomanda" class="form-control">
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>مقابلہ کھاتونی چومندہ بدرات</label>
                <input type="number" name="muqabala_khatoni_chomanda_badrat" class="form-control">
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>تبصرہ</label>
                <input type="text" name="tabsara" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> محفوظ کریں</button>
        <a href="{{ route('partal.index') }}" class="btn btn-secondary">واپس</a>
    </form>
</div>
@endsection
