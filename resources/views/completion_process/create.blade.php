@extends('layouts.app')

@section('title', 'نیا تکمیلی عمل شامل کریں')

@section('content')
<div class="container" dir="rtl">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">نیا تکمیلی عمل شامل کریں</h3>
        </div>
        <div class="panel-body">
            <form action="{{ route('completion_process.store') }}" method="POST" class="form-modern">
                @csrf
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>ضلع</label>
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
                <label>تحصیل</label>
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
                <label>موضع</label>
                @if($role_id == 1)
                <select name="moza_id" id="moza_id" class="form-control" required>
                    <option value="">منتخب کریں</option>
                </select>
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
                <label>اہلکار</label>
                <select name="employee_id" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->nam }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>کام کی قسم</label>
                <select name="completion_process_type_id" id="completion_process_type_id" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($completion_process_types as $type)
                        <option value="{{ $type->id }}">{{ $type->title_ur }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نمبر</label>
                <input type="number" name="type_value" class="form-control" placeholder="نمبر درج کریں">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>تاریخ</label>
                <input type="date" name="tareekh" class="form-control">
            </div>
        </div>
            <!-- Add other fields as needed, following the same form-group/col format -->
            <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> محفوظ کریں</button>
            <a href="{{ route('completion_process.index') }}" class="btn btn-secondary">واپس</a>
        </form>
        </div>
    </div>
</div>
@endsection

