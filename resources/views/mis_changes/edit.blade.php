@extends('layouts.app')

@section('title', 'MIS تبدیلی ریکارڈ ترمیم کریں')

@section('content')
<div class="container" dir="rtl">
    <div class="panel panel-warning">
        <div class="panel-heading">
            <h3 class="panel-title">
                <i class="fa fa-edit"></i> MIS تبدیلی ریکارڈ ترمیم کریں
            </h3>
        </div>
        <div class="panel-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>براہ کرم درج ذیل غلطیوں کو درست کریں:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('mis_changes.update', $record->change_id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="form-group col-md-4 col-xs-12">
                        <label>ضلع</label>
                        @if($role_id == 1)
                        <select name="district_id" id="district_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id', '{{ $record->tehsil_id }}')" data-selected="{{ $record->district_id }}">
                            <option value="">منتخب کریں</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->districtId }}" @if($record->district_id == $district->districtId) selected @endif>{{ $district->districtNameUrdu }}</option>
                            @endforeach
                        </select>
                        @else
                        <input type="hidden" name="district_id" value="{{ optional($districts->first())->districtId }}">
                        <input type="text" class="form-control" value="{{ optional($districts->first())->districtNameUrdu }}" disabled>
                        @endif
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                        <label>تحصیل</label>
                        @if($role_id == 1)
                        <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id')" data-selected="{{ $record->tehsil_id }}">
                            <option value="">منتخب کریں</option>
                            @foreach($tehsils as $tehsil)
                                <option value="{{ $tehsil->tehsilId }}" @if($record->tehsil_id == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                            @endforeach
                        </select>
                        @else
                        <select name="tehsil_id" class="form-control" required>
                            <option value="">منتخب کریں</option>
                            @foreach($tehsils as $tehsil)
                                <option value="{{ $tehsil->tehsilId }}" @if($record->tehsil_id == $tehsil->tehsilId) selected @endif>{{ $tehsil->tehsilNameUrdu }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    <div class="form-group col-md-4 col-xs-12">
                        <label>موضع</label>
                        @if($role_id == 1)
                        <select name="moza_id" id="moza_id" class="form-control" required data-selected="{{ $record->moza_id }}"></select>
                        @else
                        <select name="moza_id" id="moza_id" class="form-control" required>
                            <option value="">منتخب کریں</option>
                            @foreach($mozas as $moza)
                                <option value="{{ $moza->mozaId }}" @if($record->moza_id == $moza->mozaId) selected @endif>{{ $moza->mozaNameUrdu }}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 col-xs-12">
                        <label>فیملی آئی ڈی</label>
                        <input type="number" name="family_id" id="family_id" class="form-control" value="{{ old('family_id', $record->family_id) }}" placeholder="فیملی آئی ڈی درج کریں">
                    </div>
                    <div class="form-group col-md-6 col-xs-12">
                        <label>تفصیل <span style="color: red;">*</span></label>
                        <textarea name="description" id="description" class="form-control urdu-input" rows="3" placeholder="تبدیلی کی تفصیل درج کریں" required style="direction: rtl; text-align: right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif;" onfocus="ActivateUrdu(this)">{{ old('description', $record->description) }}</textarea>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-6 col-xs-12">
                        <label>سکرین شاٹ پہلے از تبدیلی</label>
                        <input type="file" name="screenshot_before_change" id="screenshot_before_change" class="form-control" accept="image/*">
                        @if($record->screenshot_before_change)
                            <div class="mt-2">
                                <img src="{{ url('assets/mis_changes/' . $record->screenshot_before_change) }}" style="max-height:150px;" class="img-thumbnail">
                                <p class="text-muted">موجودہ تصویر</p>
                            </div>
                        @endif
                        <span class="help-block">تبدیلی سے پہلے کی تصویر اپ لوڈ کریں (اگر ترمیم کرنا ہے تو نیا انتخاب کریں)</span>
                    </div>
                    <div class="form-group col-md-6 col-xs-12">
                        <label>سکرین شاٹ بعد از تبدیلی</label>
                        <input type="file" name="screenshot_after_change" id="screenshot_after_change" class="form-control" accept="image/*">
                        @if($record->screenshot_after_change)
                            <div class="mt-2">
                                <img src="{{ url('assets/mis_changes/' . $record->screenshot_after_change) }}" style="max-height:150px;" class="img-thumbnail">
                                <p class="text-muted">موجودہ تصویر</p>
                            </div>
                        @endif
                        <span class="help-block">تبدیلی کے بعد کی تصویر اپ لوڈ کریں (اگر ترمیم کرنا ہے تو نیا انتخاب کریں)</span>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group col-md-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> ترمیم محفوظ کریں
                        </button>
                        <a href="{{ route('mis_changes.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> واپس
                        </a>
                    </div>
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
@endsection
