@extends('layouts.app')
@section('title', 'سیٹنگز')
@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-12" style="float:right;">
            <div class="panel panel-primary" style="margin-top:30px;">
                <div class="panel-heading" style="font-size:18px;">
                    <i class="fa fa-cogs"></i> لوگو اور فوٹر سیٹنگز
                </div>
                <div class="panel-body" style="background:#f9f9f9;">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group col-md-12 mb-4 text-right">
                            <label class="d-block mb-2" style="font-weight:bold;">لوگو اپلوڈ کریں</label>
                            <div class="mb-2 text-right" id="logoArea">
                                @if($setting && $setting->logo_path)
                                    <img id="logoDisplay" src="{{ url('public/storage/logo/' . basename($setting->logo_path)) }}" alt="Logo" style="max-height:100px; border:2px solid #bbb; background:#fff; padding:6px; border-radius:8px;">
                                    <div id="logoLabel" style="font-size:13px; color:#888; margin-top:4px;">پچھلا لوگو</div>
                                @else
                                    <img id="logoDisplay" src="https://via.placeholder.com/120x100?text=Logo" alt="Logo" style="max-height:100px; border:2px dashed #bbb; background:#fff; padding:6px; border-radius:8px;">
                                    <div id="logoLabel" style="font-size:13px; color:#888; margin-top:4px;">نیا لوگو</div>
                                @endif
                                <div class="mt-2">
                                    <button type="button" class="btn btn-info btn-sm" id="changeLogoBtn" onclick="document.getElementById('logoInput').click();">تبدیل کریں</button>
                                    <button type="button" class="btn btn-danger btn-sm" id="cancelLogoBtn" style="display:none;" onclick="cancelLogoPreview();">منسوخ کریں</button>
                                </div>
                            </div>
                            <input type="file" name="logo" id="logoInput" class="form-control mt-2" style="max-width:300px; text-align:right; display:none;" onchange="previewLogo(event)">
                        </div>
                        <!-- Hidden inputs to store original logo and label for JS -->
                        <input type="hidden" id="originalLogo" value="{{ $setting && $setting->logo_path ? url('public/storage/logo/' . basename($setting->logo_path)) : 'https://via.placeholder.com/120x100?text=Logo' }}">
                        <input type="hidden" id="originalLabel" value="{{ $setting && $setting->logo_path ? 'پچھلا لوگو' : 'نیا لوگو' }}">
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
                        <div class="form-group col-md-12 mb-4 text-right">
                            <label class="d-block mb-2" style="font-weight:bold;">فوٹر معلومات</label>
                            <textarea name="footer_text" class="form-control urdu-input" rows="3" lang="ur" style="resize:vertical; text-align:right; font-family: 'Noto Nastaleeq Urdu', 'Jameel Noori Nastaleeq', 'Nafees', sans-serif; direction: rtl;" onfocus="ActivateUrdu(this)">{{ $setting->footer_text ?? '' }}</textarea>
                        </div>
                        <div class="form-group col-md-12 text-right mt-3">
                            <button type="submit" class="btn btn-success px-4 py-2" style="font-size:16px;">
                                <i class="fa fa-save"></i> محفوظ کریں
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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
