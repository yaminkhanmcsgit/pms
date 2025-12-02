
@extends('layouts.app')

@section('title', 'نیا صارف شامل کریں')

@section('content')
<div class="container" >
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">نیا صارف شامل کریں</h3>
        </div>
        <div class="panel-body">
            <form action="{{ route('operators.store') }}" method="POST" class="form-modern" >
                @csrf
        <div class="row">
           
           
            
            <div class="form-group col-md-4 col-12">
                <label>مکمل نام</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
             <div class="form-group col-md-4 col-12">
                <label>صارف نام</label>
                <input type="text" name="username" class="form-control" required>
            </div>
             <div class="form-group col-md-4 col-12">
                <label>پاس ورڈ</label>
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>
        <div class="row">
             <div class="form-group col-md-6 col-12">
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
             <div class="form-group col-md-6 col-12">
                 <label>تحصیل</label>
                 @if($role_id == 1)
                 <select name="tehsil_id" id="tehsil_id" class="form-control" required>
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
           
        </div>
        
        <br>
                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> محفوظ کریں</button>
                <a href="{{ route('operators.index') }}" class="btn btn-secondary">واپس</a>
            </form>
        </div>
    </div>
</div>
@endsection
