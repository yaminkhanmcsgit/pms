@extends('layouts.app')

@section('title', 'نیا پڑتال ریکارڈ شامل کریں')

@section('content')
<div class="container" dir="rtl">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">نیا پڑتال ریکارڈ شامل کریں</h3>
        </div>
        <div class="panel-body">
            <form action="{{ route('partal.store') }}" method="POST" class="form-modern" accept-charset="UTF-8">
                @csrf
        <div class="row">
            <div class="form-group col-md-4 col-xs-12">
                <label>نام ضلع <span style="color: red;">*</span></label>
                @if($role_id == 1)
                <select name="zila_id" id="zila_id" class="form-control" required onchange="onDistrictChange(this.value, 'tehsil_id')">
                    <option value="">منتخب کریں</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->districtId }}">{{ $district->districtNameUrdu }}</option>
                    @endforeach
                </select>
                @else
                <input type="hidden" name="zila_id" value="{{ optional($districts->first())->districtId }}">
                <input type="text" class="form-control" value="{{ optional($districts->first())->districtNameUrdu }}" disabled>
                @endif
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نام تحصیل <span style="color: red;">*</span></label>
                @if($role_id == 1)
                <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id'); loadPartalEmployees(this.value);">
                    <option value="">منتخب کریں</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}">{{ $tehsil->tehsilNameUrdu }}</option>
                    @endforeach
                </select>
                @else
                <select name="tehsil_id" id="tehsil_id" class="form-control" required onchange="onTehsilChange(this.value, 'moza_id'); loadPartalEmployees(this.value);">
                    <option value="">منتخب کریں</option>
                    @foreach($tehsils as $tehsil)
                        <option value="{{ $tehsil->tehsilId }}">{{ $tehsil->tehsilNameUrdu }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نام موضع <span style="color: red;">*</span></label>
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
                <label>نام پٹواری <span style="color: red;">*</span></label>
                <select name="patwari_nam" id="patwari_nam" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @if($role_id == 1)
                        <!-- Admin: Load via AJAX when tehsil selected -->
                    @else
                        <!-- Limited user: Show pre-loaded patwaris -->
                        @foreach($patwaris as $emp)
                            <option value="{{ $emp->nam }}">{{ $emp->nam }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group col-md-4 col-xs-12">
                <label>نام اہلکار <span style="color: red;">*</span></label>
                <select name="ahalkar_nam" id="ahalkar_nam" class="form-control" required>
                    <option value="">منتخب کریں</option>
                    @if($role_id == 1)
                        <!-- Admin: Load via AJAX when tehsil selected -->
                    @else
                        <!-- Limited user: Show pre-loaded employees -->
                        @foreach($ahalkars as $emp)
                            <option value="{{ $emp->nam }}">{{ $emp->nam }}</option>
                        @endforeach
                    @endif
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
    </div>
</div>

<script>
// Function to load employees when tehsil changes
function loadPartalEmployees(tehsilId) {
    console.log('Loading employees for tehsil:', tehsilId);

    var apiUrl = '{{ url("api/partal-employees") }}';
    console.log('API URL:', apiUrl);

    // Fallback: construct URL manually if Laravel helper fails
    if (!apiUrl || apiUrl.indexOf('api/partal-employees') === -1) {
        var baseUrl = window.location.origin;
        var pathParts = window.location.pathname.split('/');
        var appPath = pathParts[1]; // Get the app path (e.g., 'pms' or 'admin')
        apiUrl = baseUrl + '/' + appPath + '/api/partal-employees';
        console.log('Fallback API URL constructed:', apiUrl);
    }

    if (tehsilId) {
        // Load patwaris (ahalkar_type = 1)
        var patwariUrl = apiUrl + '?tehsil_id=' + tehsilId + '&type=patwari';
        console.log('Loading patwaris from:', patwariUrl);

        fetch(patwariUrl)
            .then(response => response.json())
            .then(data => {
                console.log('Patwaris data received:', data);
                var options = '<option value="">منتخب کریں</option>';
                data.forEach(function(employee) {
                    options += '<option value="' + employee.nam + '">' + employee.nam + '</option>';
                });
                document.getElementById('patwari_nam').innerHTML = options;
                console.log('Patwaris dropdown updated');
            })
            .catch(error => {
                console.log('Error loading patwaris:', error);
            });

        // Load all employees for ahalkar
        var employeeUrl = apiUrl + '?tehsil_id=' + tehsilId + '&type=all';
        console.log('Loading employees from:', employeeUrl);

        fetch(employeeUrl)
            .then(response => response.json())
            .then(data => {
                console.log('Employees data received:', data);
                var options = '<option value="">منتخب کریں</option>';
                data.forEach(function(employee) {
                    options += '<option value="' + employee.nam + '">' + employee.nam + '</option>';
                });
                document.getElementById('ahalkar_nam').innerHTML = options;
                console.log('Employees dropdown updated');
            })
            .catch(error => {
                console.log('Error loading employees:', error);
            });
    } else {
        // Clear selects if no tehsil selected
        document.getElementById('patwari_nam').innerHTML = '<option value="">منتخب کریں</option>';
        document.getElementById('ahalkar_nam').innerHTML = '<option value="">منتخب کریں</option>';
        console.log('Dropdowns cleared');
    }
}

console.log('Partal employee loading functions loaded');
</script>
@endsection
