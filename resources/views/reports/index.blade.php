@extends('layouts.app')

@section('title', 'رپورٹس')

@push('styles')
<style>
@media print {
    .header-menu-area, .footer-copyright-area, .nav, .navbar, .mobile-menu-area { display: none !important; }
    body { margin: 0; padding: 0; }
    .container { width: 100%; max-width: none; padding: 0; margin: 0; }
    .page-header { display: none !important; }
    .tab-content > div { page-break-inside: auto; }
    .tab-content > div > h4 { display: block !important; text-align: center; margin-bottom: 10px; margin-top: 0; }
    #partal_table_container, #completion_table_container { display: block !important; margin: 0; }
    table { font-size: 10pt; page-break-inside: auto; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    .btn { display: none !important; }
    .clearfix { display: none !important; }
    .row.mb-3 { display: none !important; }
    .nav-tabs { display: none !important; }
}
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
$('#partal_pdf').click(function() {
    html2canvas(document.querySelector('#partal_content')).then(canvas => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', 10, 10, 180, 0);
        doc.save('partal_report.pdf');
    });
});
$('#completion_pdf').click(function() {
    html2canvas(document.querySelector('#completion_content')).then(canvas => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', 10, 10, 180, 0);
        doc.save('completion_report.pdf');
    });
});
$('#grievances_pdf').click(function() {
    html2canvas(document.querySelector('#grievances_content')).then(canvas => {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const imgData = canvas.toDataURL('image/png');
        doc.addImage(imgData, 'PNG', 10, 10, 180, 0);
        doc.save('grievances_report.pdf');
    });
});
</script>
@endpush

@section('content')
<style>
table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 14px; }
table th, table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
table th { background-color: #e9ecef; color: #333; font-weight: bold; }
tr:nth-child(even) { background-color: #f9f9f9; }
tr:nth-child(odd) { background-color: #fff; }
tr:hover { background-color: #f1f1f1; }
.value-cell { background-color: #d4edda !important; }

@media print {
    .header-menu-area, .footer-copyright-area, .mobile-menu-area { display: none !important; }
    body { margin: 0 !important; padding: 0 !important; -webkit-print-color-adjust: exact; }
    html, body { height: auto !important; }
    .container { width: 100% !important; max-width: none !important; padding: 0 !important; margin: 0 !important; }
    .nav-tabs, .row.mb-3, .btn, .form-control, .col-md-2 { display: none !important; }
    .tab-content > div > h4 { display: block !important; text-align: center; margin-bottom: 5px; margin-top: 0; font-size: 14pt !important; }
    #partal_content, #completion_content, #grievances_content { display: block !important; margin: 0; padding: 0; }
    #partal_table_container, #completion_table_container, #grievances_table_container { display: block !important; margin: 0; padding: 0; }
    table { font-size: 9pt; page-break-inside: auto; width: 100% !important; margin: 0 !important; }
    tr { page-break-inside: avoid; page-break-after: auto; }
    .content { padding: 0 !important; margin: 0 !important; }
}
.tab-content .col-md-2{float:right;}
</style>
<div class="container" dir="rtl">
    <center><legend><h3>رپورٹس</h3></legend></center>

    <ul class="nav nav-tabs">
        <li class="active"><a data-toggle="tab" href="#partal">پڑتال رپورٹ</a></li>
        <li><a data-toggle="tab" href="#completion">تکمیلی کام رپورٹ</a></li>
        <li><a data-toggle="tab" href="#grievances">شکایات رپورٹ</a></li>
    </ul>

    <div class="tab-content form-group" dir="rtl" style="width:100%;">
        <div id="partal" class="tab-pane fade in active" >

            <div id="" >
                <div class="col-md-2">
                    <label>تاریخ سے</label>
                    <input type="date" id="partal_from_date" class="form-control" value="{{ $from_date }}">
                </div>
                <div class="col-md-2">
                    <label>تاریخ تک</label>
                    <input type="date" id="partal_to_date" class="form-control" value="{{ $to_date }}">
                </div>
                <div class="col-md-2">
                    <label>ضلع</label>
                    @if($role_id == 1)
                    <select id="partal_district_id" class="form-control" onchange="onDistrictChange(this.value, 'partal_tehsil_id')">
                        <option value="">تمام</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->districtId }}">{{ $district->districtNameUrdu }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="hidden" id="partal_district_id" value="{{ session('zila_id') }}">
                    <input type="text" class="form-control" value="{{ $zila_name }}" disabled>
                   
                   
                    @endif
                </div>
                <div class="col-md-2">
                    <label>تحصیل</label>
                    @if($role_id == 1)
                    <select id="partal_tehsil_id" class="form-control" onchange="onTehsilChange(this.value, 'partal_moza_id')">
                        <option value="">تمام</option>
                    </select>
                    @else
                    <input type="hidden" id="partal_tehsil_id" value="{{ session('tehsil_id') }}">
                    <input type="text" class="form-control" value="{{ $tehsil_name }}" disabled>
                  
                    @endif
                </div>
                <div class="col-md-2">
                    <label>موضع</label>
                    <select id="partal_moza_id" class="form-control">
                        <option value="">تمام</option>
                    </select>
                </div>
                <div class="col-md-2" style="padding-left:0;padding-right:0">
                    <button id="partal_filter" class="btn btn-primary btn-sm">فلٹر</button>
                    <button id="partal_pdf" type="button" class="btn btn-danger btn-sm">PDF</button>
                    <button id="partal_excel" type="button" class="btn btn-success btn-sm">Excel</button>
                    <button id="partal_print" type="button" class="btn btn-info btn-sm">Print</button>
                </div>
</div>
            <div id="partal_content">
                <center><h4>پڑتال رپورٹ</h4></center>
                <div id="partal_table_container">
                <table>
                    <thead>
                        <tr>
                            <th rowspan="2">سیریل نمبر</th>
                            <th colspan="7">بنیادی معلومات</th>
                            <th colspan="2">پڑتال پیمائش موقع</th>
                            <th colspan="2">تصدیق آخیر ملکیت وغیرہ بر موقع</th>
                            <th colspan="2">تصدیق آخیر شجرہ نسب</th>
                            <th colspan="2">تصدیق ملکیت و قبضہ کاشت وغیرہ</th>
                            <th rowspan="2">تبصرہ</th>
                            <th rowspan="2">ٹوٹل</th>
                        </tr>
                        <tr>
                            <th>ضلع نام</th>
                            <th>تحصیل نام</th>
                            <th>موضع نام</th>
                            <th>پٹواری نام</th>
                            <th>اہلکار نام</th>
                            <th>از تاریخ</th>
                            <th>تا تاریخ</th>
                            <th>تصدیق ملکیت/پیمود شدہ نمبرات خسرہ</th>
                            <th>تعداد برامدہ بدرات</th>
                            <th>تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ</th>
                            <th>تعداد برامدہ بدرات</th>
                            <th>تعداد گھری</th>
                            <th>تعداد برامدہ بدرات</th>
                            <th>مقابلہ کھتونی ہمراہ کاپی چومنڈہ</th>
                            <th>تعداد برامدہ بدرات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($partal_data as $index => $item)
                        <tr>
                            <td>{{ (int) $index + 1 }}</td>
                            <td>{{ $item->districtNameUrdu }}</td>
                            <td>{{ $item->tehsilNameUrdu }}</td>
                            <td>{{ $item->mozaNameUrdu }}</td>
                            <td>{{ $item->patwari_nam }}@if(!empty($item->patwari_title)) <small>({{ $item->patwari_title }})</small>@endif</td>
                            <td>{{ $item->ahalkar_nam }}@if(!empty($item->ahalkar_title)) <small>({{ $item->ahalkar_title }})</small>@endif</td>
                            <td>{{ $from_date }}</td>
                            <td>{{ $to_date }}</td>
                            <td class="{{ $item->tasdeeq_milkiat_pemuda_khasra > 0 ? 'value-cell' : '' }}">{{ $item->tasdeeq_milkiat_pemuda_khasra == 0 ? '-' : $item->tasdeeq_milkiat_pemuda_khasra }}</td>
                            <td class="{{ $item->tasdeeq_milkiat_pemuda_khasra_badrat > 0 ? 'value-cell' : '' }}">{{ $item->tasdeeq_milkiat_pemuda_khasra_badrat == 0 ? '-' : $item->tasdeeq_milkiat_pemuda_khasra_badrat }}</td>
                            <td class="{{ $item->tasdeeq_milkiat_qabza_kasht_khasra > 0 ? 'value-cell' : '' }}">{{ $item->tasdeeq_milkiat_qabza_kasht_khasra == 0 ? '-' : $item->tasdeeq_milkiat_qabza_kasht_khasra }}</td>
                            <td class="{{ $item->tasdeeq_milkiat_qabza_kasht_badrat > 0 ? 'value-cell' : '' }}">{{ $item->tasdeeq_milkiat_qabza_kasht_badrat == 0 ? '-' : $item->tasdeeq_milkiat_qabza_kasht_badrat }}</td>
                            <td class="{{ $item->tasdeeq_shajra_nasab_guri > 0 ? 'value-cell' : '' }}">{{ $item->tasdeeq_shajra_nasab_guri == 0 ? '-' : $item->tasdeeq_shajra_nasab_guri }}</td>
                            <td class="{{ $item->tasdeeq_shajra_nasab_badrat > 0 ? 'value-cell' : '' }}">{{ $item->tasdeeq_shajra_nasab_badrat == 0 ? '-' : $item->tasdeeq_shajra_nasab_badrat }}</td>
                            <td class="{{ $item->muqabala_khatoni_chomanda > 0 ? 'value-cell' : '' }}">{{ $item->muqabala_khatoni_chomanda == 0 ? '-' : $item->muqabala_khatoni_chomanda }}</td>
                            <td class="{{ $item->muqabala_khatoni_chomanda_badrat > 0 ? 'value-cell' : '' }}">{{ $item->muqabala_khatoni_chomanda_badrat == 0 ? '-' : $item->muqabala_khatoni_chomanda_badrat }}</td>
                            <td>{{ $item->tabsara }}</td>
                            <td><strong>{{ $item->total_count > 0 ? $item->total_count : '-' }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <div id="completion" class="tab-pane fade">

            <div id="" style="overflow: hidden;">
                <div class="col-md-2">
                    <label>تاریخ سے</label>
                    <input type="date" id="completion_from_date" class="form-control" value="{{ $from_date }}">
                </div>
                <div class="col-md-2">
                    <label>تاریخ تک</label>
                    <input type="date" id="completion_to_date" class="form-control" value="{{ $to_date }}">
                </div>
                <div class="col-md-2">
                    <label>ضلع</label>
                    @if($role_id == 1)
                    <select id="completion_district_id" class="form-control" onchange="onDistrictChange(this.value, 'completion_tehsil_id')">
                        <option value="">تمام</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->districtId }}">{{ $district->districtNameUrdu }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="hidden" id="completion_district_id" value="{{ session('zila_id') }}">
                      <input type="text" class="form-control" value="{{ $zila_name }}" disabled>
                    
                    @endif
                </div>
                <div class="col-md-2">
                    <label>تحصیل</label>
                    @if($role_id == 1)
                    <select id="completion_tehsil_id" class="form-control" onchange="onTehsilChange(this.value, 'completion_moza_id')">
                        <option value="">تمام</option>
                    </select>
                    @else
                    <input type="hidden" id="completion_tehsil_id" value="{{ session('tehsil_id') }}">
                    <input type="text" class="form-control" value="{{ $tehsil_name }}" disabled>
                  
                    @endif
                </div>
                <div class="col-md-2">
                    <label>موضع</label>
                    <select id="completion_moza_id" class="form-control">
                        <option value="">تمام</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>ملازم</label>
                    <select id="completion_employee_id" class="form-control">
                        <option value="">تمام</option>
                    </select>
                </div>
<div class="col-md-12" style="padding-left:0;padding-right:0">
                   <center style="margin-top: 20px;"><button id="completion_filter" class="btn btn-primary btn-sm">فلٹر</button>
                    <button id="completion_pdf" type="button" class="btn btn-danger btn-sm">PDF</button>
                    <button id="completion_excel" type="button" class="btn btn-success btn-sm">Excel</button>
                    <button id="completion_print" type="button" class="btn btn-info btn-sm">Print</button></center>
                </div>
            </div>
            
            <div id="completion_content" style="margin-top: 20px;">
               <center><h4>تکمیلی کام رپورٹ</h4></center>
            <div id="completion_table_container" >
               
            </div>
        </div>
            </div>
            <div id="grievances" class="tab-pane fade">
    
                <div id="">
                    <div class="col-md-2">
                        <label>تاریخ سے</label>
                        <input type="date" id="grievances_from_date" class="form-control" value="{{ $from_date }}">
                    </div>
                    <div class="col-md-2">
                        <label>تاریخ تک</label>
                        <input type="date" id="grievances_to_date" class="form-control" value="{{ $to_date }}">
                    </div>
                    <div class="col-md-2">
                        <label>ضلع</label>
                        @if($role_id == 1)
                        <select id="grievances_district_id" class="form-control" onchange="onDistrictChange(this.value, 'grievances_tehsil_id')">
                            <option value="">تمام</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->districtId }}">{{ $district->districtNameUrdu }}</option>
                            @endforeach
                        </select>
                        @else
                        <input type="hidden" id="grievances_district_id" value="{{ session('zila_id') }}">
                          <input type="text" class="form-control" value="{{ $zila_name }}" disabled>
    
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label>تحصیل</label>
                        @if($role_id == 1)
                        <select id="grievances_tehsil_id" class="form-control" onchange="onTehsilChange(this.value, 'grievances_moza_id')">
                            <option value="">تمام</option>
                        </select>
                        @else
                        <input type="hidden" id="grievances_tehsil_id" value="{{ session('tehsil_id') }}">
                        <input type="text" class="form-control" value="{{ $tehsil_name }}" disabled>
    
                        @endif
                    </div>
                    <div class="col-md-2">
                        <label>موضع</label>
                        <select id="grievances_moza_id" class="form-control">
                            <option value="">تمام</option>
                        </select>
                    </div>
                    <div class="col-md-2" style="padding-left:0;padding-right:0">
                        <button id="grievances_filter" class="btn btn-primary btn-sm">فلٹر</button>
                        <button id="grievances_pdf" type="button" class="btn btn-danger btn-sm">PDF</button>
                        <button id="grievances_excel" type="button" class="btn btn-success btn-sm">Excel</button>
                        <button id="grievances_print" type="button" class="btn btn-info btn-sm">Print</button>
                    </div>
                </div>
    
<div id="grievances_content">
            <center><h4>شکایات رپورٹ</h4></center>
            <div id="grievances_table_container">
                   
                </div>
            </div>
        </div>
    
   
    </div>
</div>

<script>
$(document).ready(function() {
    loadPartalReports();
    loadCompletionReports();
    loadGrievancesReports();
    loadCompletionEmployees();

    // Load moza options for role_id 2
    if ($('#partal_tehsil_id').is('input:hidden')) {
        onTehsilChange($('#partal_tehsil_id').val(), 'partal_moza_id');
    }
    if ($('#completion_tehsil_id').is('input:hidden')) {
        onTehsilChange($('#completion_tehsil_id').val(), 'completion_moza_id');
    }
    if ($('#grievances_tehsil_id').is('input:hidden')) {
        onTehsilChange($('#grievances_tehsil_id').val(), 'grievances_moza_id');
    }

    $('#partal_filter').click(function() {
        loadPartalReports();
    });

    $('#completion_filter').click(function() {
        loadCompletionReports();
    });

    $('#grievances_filter').click(function() {
        loadGrievancesReports();
    });

    $('#partal_print').click(function() {
        window.print();
    });

    $('#completion_print').click(function() {
        window.print();
    });

    $('#grievances_print').click(function() {
        window.print();
    });


});

function loadCompletionEmployees() {
    $.get('{{ url("api/employees") }}?role_id={{ session("role_id") }}&zila_id={{ session("zila_id") }}&tehsil_id={{ session("tehsil_id") }}', function(data) {
        var select = $('#completion_employee_id');
        select.empty();
        select.append('<option value="">تمام</option>');
        data.forEach(function(item) {
            select.append(`<option value="${item.id}">${item.nam}</option>`);
        });
    });
}

function getPartalFilters() {
    return 'from_date=' + $('#partal_from_date').val() + '&to_date=' + $('#partal_to_date').val() + '&district_id=' + $('#partal_district_id').val() + '&tehsil_id=' + $('#partal_tehsil_id').val() + '&moza_id=' + $('#partal_moza_id').val();
}

function getCompletionFilters() {
    return 'from_date=' + $('#completion_from_date').val() + '&to_date=' + $('#completion_to_date').val() + '&district_id=' + $('#completion_district_id').val() + '&tehsil_id=' + $('#completion_tehsil_id').val() + '&moza_id=' + $('#completion_moza_id').val() + '&employee_id=' + $('#completion_employee_id').val();
}

function getGrievancesFilters() {
    return 'from_date=' + $('#grievances_from_date').val() + '&to_date=' + $('#grievances_to_date').val() + '&district_id=' + $('#grievances_district_id').val() + '&tehsil_id=' + $('#grievances_tehsil_id').val() + '&moza_id=' + $('#grievances_moza_id').val();
}

function loadPartalReports() {
    $.get('{{ route("reports.partal") }}', getPartalFilters(), function(data) {
        let html = '<table><thead><tr><th rowspan="2">سیریل نمبر</th><th colspan="7">بنیادی معلومات</th><th colspan="2">پڑتال پیمائش موقع</th><th colspan="2">تصدیق آخیر ملکیت وغیرہ بر موقع</th><th colspan="2">تصدیق آخیر شجرہ نسب</th><th colspan="2">تصدیق ملکیت و قبضہ کاشت وغیرہ</th><th rowspan="2">تبصرہ</th><th rowspan="2">ٹوٹل</th></tr><tr><th>ضلع نام</th><th>تحصیل نام</th><th>موضع نام</th><th>پٹواری نام</th><th>اہلکار نام</th><th>از تاریخ</th><th>تا تاریخ</th><th>تصدیق ملکیت/پیمود شدہ نمبرات خسرہ</th><th>تعداد برامدہ بدرات</th><th>تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ</th><th>تعداد برامدہ بدرات</th><th>تعداد گھری</th><th>تعداد برامدہ بدرات</th><th>مقابلہ کھتونی ہمراہ کاپی چومنڈہ</th><th>تعداد برامدہ بدرات</th></tr></thead><tbody>';
        function formatDateDMY(dateStr) {
            if (!dateStr) return '-';
            const parts = dateStr.split('-');
            if (parts.length !== 3) return dateStr;
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }

        data.forEach(function(item, index) {
            html += '<tr><td>' + (index + 1) + '</td>' +
                '<td>' + (item.districtNameUrdu || '-') + '</td>' +
                '<td>' + (item.tehsilNameUrdu || '-') + '</td>' +
                '<td>' + (item.mozaNameUrdu || '-') + '</td>' +
                '<td>' + (item.patwari_nam || '-') + (item.patwari_title ? ' <small>(' + item.patwari_title + ')</small>' : '') + '</td>' +
                '<td>' + (item.ahalkar_nam || '-') + (item.ahalkar_title ? ' <small>(' + item.ahalkar_title + ')</small>' : '') + '</td>' +
                '<td>' + formatDateDMY($('#partal_from_date').val()) + '</td>' +
                '<td>' + formatDateDMY($('#partal_to_date').val()) + '</td>' +
                '<td class="' + (item.tasdeeq_milkiat_pemuda_khasra > 0 ? 'value-cell' : '') + '">' + (!item.tasdeeq_milkiat_pemuda_khasra ? '-' : item.tasdeeq_milkiat_pemuda_khasra) + '</td>' +
                '<td class="' + (item.tasdeeq_milkiat_pemuda_khasra_badrat > 0 ? 'value-cell' : '') + '">' + (!item.tasdeeq_milkiat_pemuda_khasra_badrat ? '-' : item.tasdeeq_milkiat_pemuda_khasra_badrat) + '</td>' +
                '<td class="' + (item.tasdeeq_milkiat_qabza_kasht_khasra > 0 ? 'value-cell' : '') + '">' + (!item.tasdeeq_milkiat_qabza_kasht_khasra ? '-' : item.tasdeeq_milkiat_qabza_kasht_khasra) + '</td>' +
                '<td class="' + (item.tasdeeq_milkiat_qabza_kasht_badrat > 0 ? 'value-cell' : '') + '">' + (!item.tasdeeq_milkiat_qabza_kasht_badrat ? '-' : item.tasdeeq_milkiat_qabza_kasht_badrat) + '</td>' +
                '<td class="' + (item.tasdeeq_shajra_nasab_guri > 0 ? 'value-cell' : '') + '">' + (!item.tasdeeq_shajra_nasab_guri ? '-' : item.tasdeeq_shajra_nasab_guri) + '</td>' +
                '<td class="' + (item.tasdeeq_shajra_nasab_badrat > 0 ? 'value-cell' : '') + '">' + (!item.tasdeeq_shajra_nasab_badrat ? '-' : item.tasdeeq_shajra_nasab_badrat) + '</td>' +
                '<td class="' + (item.muqabala_khatoni_chomanda > 0 ? 'value-cell' : '') + '">' + (!item.muqabala_khatoni_chomanda ? '-' : item.muqabala_khatoni_chomanda) + '</td>' +
                '<td class="' + (item.muqabala_khatoni_chomanda_badrat > 0 ? 'value-cell' : '') + '">' + (!item.muqabala_khatoni_chomanda_badrat ? '-' : item.muqabala_khatoni_chomanda_badrat) + '</td>' +
                '<td>' + (item.tabsara || '-') + '</td>' +
                '<td><strong>' + (item.total_count > 0 ? item.total_count : '-') + '</strong></td></tr>';
        });
        html += '</tbody></table>';
        $('#partal_table_container').html(html);
    });
}

function loadCompletionReports() {
    $.get('{{ route("reports.completion_process") }}', getCompletionFilters(), function(data) {
        $.get('{{ url("api/completion-process-types") }}', function(types) {
            let headers = '<tr><th>نمبر شمار</th><th>نام ضلع</th><th>نام تحصیل</th><th>نام موضع</th><th>نام اہلکار</th>';
            let headerKeys = [];
            types.forEach(function(type) {
                if (type.field_name) {
                    headers += '<th>' + type.title_ur + '</th>';
                    headerKeys.push(type.field_name);
                }
            });
            headers += '<th>ٹوٹل</th><th>از تاریخ</th><th>تا تاریخ</th></tr>';
            
            let html = '<table><thead>' + headers + '</thead><tbody>';
            data.forEach(function(item, index) {
                function getCell(value, target) {
                    if (value == null || value == 0) return '<td>-</td>';
                    var targetVal = target || 0;
                    var text = targetVal + ' / ' + value;
                    var cls = '';
                    if (targetVal==0) {
                        cls += 'bg-warning';
                    } else  if (value >= targetVal) {
                        cls += 'bg-success';
                    } else {
                        cls += 'bg-danger';
                    }
                    return '<td class="' + cls + '">' + text + '</td>';
                }
                
                let row = '<tr><td>' + (index + 1) + '</td><td>' + item.districtNameUrdu + '</td><td>' + item.tehsilNameUrdu + '</td><td>' + item.mozaNameUrdu + '</td><td>' + item.employee_name + (item.employee_type_title ? ' <small>(' + item.employee_type_title + ')</small>' : '') + '</td>';
                headerKeys.forEach(function(key) {
                    let value = item[key] != null ? item[key] : 0;
                    let target = item['target_' + key] != null ? item['target_' + key] : 0;
                    row += getCell(value, target);
                });
                row += '<td><strong>' + (item.total_count > 0 ? item.total_count : '-') + '</strong></td>' + '<td>' + $('#completion_from_date').val() + '</td><td>' + $('#completion_to_date').val() + '</td></tr>';
                html += row;
            });
            html += '</tbody></table>';
            $('#completion_table_container').html(html);
        });
    });
}

function loadGrievancesReports() {
    $.get('{{ route("reports.grievances") }}', getGrievancesFilters(), function(data) {
        let html = '<table><thead><tr><th>نمبر شمار</th><th>نام ضلع</th><th>نام تحصیل</th><th>نام موضع</th><th>شکایت کنندہ کا نام</th><th>والد کا نام</th><th>شناختی کارڈ نمبر</th><th>شکایت کی قسم</th><th>حیثیت</th><th>تاریخ</th></tr></thead><tbody>';
        data.forEach(function(item, index) {
            html += '<tr><td>' + (index + 1) + '</td><td>' + item.district_name + '</td><td>' + item.tehsil_name + '</td><td>' + item.moza_name + '</td><td>' + item.applicant_name + '</td><td>' + item.father_name + '</td><td>' + item.cnic + '</td><td>' + item.grievance_type_name + '</td><td><span class="label label-' + item.status_color + '">' + item.status_name + '</span></td><td>' + (item.application_date ? new Date(item.application_date).toLocaleDateString('en-GB') : '') + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#grievances_table_container').html(html);
    });
}
</script>
@endsection