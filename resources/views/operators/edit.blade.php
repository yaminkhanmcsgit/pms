@extends('layouts.app')

@section('title', 'صارف ترمیم کریں')

@section('content')
<div class="container" dir="rtl">
    <center><legend><h3>صارف ترمیم کریں</h3></legend></center>
    <form action="{{ route('operators.update', $operator->id) }}" method="POST" class="form-modern">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="form-group col-md-4 col-12">
                <label>مکمل نام</label>
                <input type="text" name="full_name" class="form-control" value="{{ $operator->full_name }}" required>
            </div>
            <div class="form-group col-md-4 col-12">
                <label>صارف نام</label>
                <input type="text" name="username" class="form-control" value="{{ $operator->username }}" required>
            </div>
            <div class="form-group col-md-4 col-12">
                <label>پاس ورڈ (خالی چھوڑیں تو موجودہ رہے گا)</label>
                <input type="password" name="password" class="form-control">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6 col-12">
                <label>ضلع</label>
                @if($role_id == 1)
                <select name="zila_id" id="zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id', '{{ $operator->tehsil_id }}')" data-selected="{{ $operator->zila_id }}">
                    <option value="">منتخب کریں</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->districtId }}" @if($operator->zila_id == $district->districtId) selected @endif>{{ $district->districtNameUrdu }}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="zila_id" value="{{ $districts->first()->districtId }}">
                <input type="text" class="form-control" value="{{ $districts->first()->districtNameUrdu }}" disabled>
                @endif
            </div>
            <div class="form-group col-md-6 col-12">
                <label>تحصیل</label>
                @if($role_id == 1)
                <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, '', '')" data-selected="{{ $operator->tehsil_id }}">
                    <option value="">منتخب کریں</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}" @if($operator->tehsil_id == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="tehsil_id" value="{{ $tehsils->first()->tehsilId }}">
                <input type="text" class="form-control" value="{{ $tehsils->first()->tehsilNameUrdu }}" readonly>
                @endif
            </div>
        </div>
        <div class="row">
           
            <div class="form-group col-md-6 col-xs-12">
                <label class="control-label" style="float: right; text-align: right;">رول</label>
                <select name="role_id" class="form-control" style="direction: rtl; text-align: right;">
                    <option value="">منتخب کریں</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->role_id }}" @if($operator->role_id == $role->role_id) selected @endif>{{ $role->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <br>
        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> محفوظ کریں</button>
        <a href="{{ route('operators.index') }}" class="btn btn-secondary">واپس</a>
    </form>
</div>
@endsection
