@extends('layouts.app')
@section('title', 'سیٹنگز')
@section('content')

<style>.form-group{float:left;width:100%;}</style>
<div class="container" dir="rtl">
    <div class="row">
        <div class="col-md-12">
            {{-- <!-- Page Header -->
            <div class="page-header" style="margin-top: 20px; margin-bottom: 30px;">
                <h1 style="text-align: right; color: #333;">
                    <i class="fa fa-cogs"></i> سیٹنگز
                    <small>لوگو اور فوٹر کی ترتیبات</small>
                </h1>
            </div> --}}

            <!-- Centered Panel -->
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-default" style="max-width: 400px; margin: 0 auto; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <div class="panel-heading" style="text-align: center; background: #f8f8f8; border-bottom: 1px solid #e0e0e0;">
                            <h3 class="panel-title" style="font-size: 18px; font-weight: bold; margin: 0;">
                                <i class="fa fa-cogs"></i> لوگو اور فوٹر سیٹنگز
                            </h3>
                        </div>
                        <div class="panel-body" style="padding: 25px;">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible" role="alert" style="margin-bottom: 20px;">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <i class="fa fa-check-circle"></i> {{ session('success') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                                @csrf

                                <!-- Logo Upload Section -->
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label class="control-label" style="text-align: right; display: block; font-weight: bold; font-size: 14px; margin-bottom: 10px;">
                                        <i class="fa fa-image"></i> لوگو اپلوڈ کریں
                                    </label>
                                    <div class="text-center">
                                        <div id="logoArea" style="margin-bottom: 10px;">
                                            @if($setting && $setting->logo_path)
                                                <img id="logoDisplay" src="{{ url('assets/logo/' . $setting->logo_path) }}" alt="Logo" style="max-height:90px; border:2px solid #ddd; background:#fff; padding:5px; border-radius:6px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                <div id="logoLabel" style="font-size:12px; color:#666; margin-top:5px; font-weight: bold;">پچھلا لوگو</div>
                                            @else
                                                <img id="logoDisplay" src="https://via.placeholder.com/120x100?text=Logo" alt="Logo" style="max-height:90px; border:2px dashed #bbb; background:#fff; padding:5px; border-radius:6px;">
                                                <div id="logoLabel" style="font-size:12px; color:#666; margin-top:5px; font-weight: bold;">نیا لوگو</div>
                                            @endif
                                        </div>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-primary btn-sm" id="changeLogoBtn" onclick="document.getElementById('logoInput').click();">
                                                <i class="fa fa-upload"></i> تبدیل کریں
                                            </button>
                                            <button type="button" class="btn btn-default btn-sm" id="cancelLogoBtn" style="display:none;" onclick="cancelLogoPreview();">
                                                <i class="fa fa-times"></i> منسوخ کریں
                                            </button>
                                        </div>
                                        <input type="file" name="logo" id="logoInput" class="form-control" style="display:none;" onchange="previewLogo(event)">
                                    </div>
                                </div>

                                <!-- Footer Text Section -->
                                <div class="form-group" style="margin-bottom: 25px;">
                                    <label for="footer_text" class="control-label" style="text-align: right; display: block; font-weight: bold; font-size: 14px; margin-bottom: 10px;">
                                        <i class="fa fa-file-text"></i> فوٹر معلومات
                                    </label>
                                    <textarea name="footer_text" id="footer_text" class="form-control urdu-input" rows="3" lang="ur" placeholder="فوٹر میں دکھانے کے لیے متن درج کریں..." style="resize:vertical; text-align:right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif; direction: rtl;" onfocus="ActivateUrdu(this)">{{ $setting->footer_text ?? '' }}</textarea>
                                    <span class="help-block" style="text-align: right; font-size: 11px; color: #666; margin-top: 5px;">یہ متن ویب سائٹ کے فوٹر میں ظاہر ہوگا۔</span>
                                </div>

                                <!-- Hidden inputs for JS -->
                                <input type="hidden" id="originalLogo" value="{{ $setting && $setting->logo_path ? url('assets/logo/' . $setting->logo_path) : 'https://via.placeholder.com/120x100?text=Logo' }}">
                                <input type="hidden" id="originalLabel" value="{{ $setting && $setting->logo_path ? 'پچھلا لوگو' : 'نیا لوگو' }}">

                                <!-- Submit Button -->
                                <div class="form-group text-center" style="margin-top: 25px;">
                                    <button type="submit" class="btn btn-success btn-lg" style="padding: 10px 25px; font-size: 14px;">
                                        <i class="fa fa-save"></i> ترتیبات محفوظ کریں
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewLogo(event) {
    var reader = new FileReader();
    reader.onload = function(){
        var output = document.getElementById('logoDisplay');
        output.src = reader.result;
        document.getElementById('logoLabel').innerText = 'نیا لوگو';
        document.getElementById('changeLogoBtn').style.display = 'none';
        document.getElementById('cancelLogoBtn').style.display = 'inline-block';
    };
    if(event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}
function cancelLogoPreview() {
    var output = document.getElementById('logoDisplay');
    output.src = document.getElementById('originalLogo').value;
    document.getElementById('logoLabel').innerText = document.getElementById('originalLabel').value;
    document.getElementById('logoInput').value = '';
    document.getElementById('changeLogoBtn').style.display = 'inline-block';
    document.getElementById('cancelLogoBtn').style.display = 'none';
}
</script>
@endsection
@push('scripts')

<script src="{{ url('public/js/urdutextbox.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var urduBox = document.querySelector('.urdu-input');
    if (urduBox) {
        ActivateUrdu(urduBox);
    }
});
</script>
@endpush
