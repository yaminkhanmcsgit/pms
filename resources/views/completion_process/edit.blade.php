@extends('layouts.app')

@section('title', 'تکمیلی عمل ترمیم کریں')

@section('content')
<div class="container" dir="rtl">
    <center><legend><h3>تکمیلی عمل ترمیم کریں</h3></legend></center>
    <form action="{{ route('completion_process.update', $completion_process->id) }}" method="POST" class="form-modern">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>ضلع</label>
                @if($role_id == 1)
                <select name="zila_id" id="zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id', '{{ $completion_process->tehsil_id }}')" data-selected="{{ $completion_process->zila_id }}">
                    <option value="">منتخب کریں</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->districtId }}" @if($completion_process->zila_id == $district->districtId) selected @endif>{{ $district->districtNameUrdu }}</option>
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
                <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id', '{{ $completion_process->moza_id }}')" data-selected="{{ $completion_process->tehsil_id }}">
                    <option value="">منتخب کریں</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}" @if($completion_process->tehsil_id == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
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
                <select name="moza_id" id="moza_id" class="form-control" required data-selected="{{ $completion_process->moza_id }}">
                    <option value="">منتخب کریں</option>
                </select>
                @else
                <select name="moza_id" id="moza_id" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($mozas as $moza)
                        <option value="{{ $moza->mozaId }}" @if($completion_process->moza_id == $moza->mozaId) selected @endif>{{ $moza->mozaNameUrdu }}</option>
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
                        <option value="{{ $emp->id }}" @if($completion_process->employee_id == $emp->id) selected @endif>{{ $emp->nam }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>ٹاسک کی قسم</label>
                <select name="completion_process_type_id" id="completion_process_type_id" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @foreach($completion_process_types as $type)
                        <option value="{{ $type->id }}" @if(($completion_process->completion_process_type_id ?? null) == $type->id) selected @endif>{{ $type->title_ur }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نمبر</label>
                <input type="number" name="type_value" class="form-control" value="{{ $completion_process->type_value ?? '' }}" placeholder="نمبر درج کریں">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>تاریخ</label>
                <input type="date" name="tareekh" class="form-control" value="{{ $completion_process->tareekh }}">
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> محفوظ کریں</button>
        <a href="{{ route('completion_process.index') }}" class="btn btn-secondary">واپس</a>
    </form>
</div>
@endsection

