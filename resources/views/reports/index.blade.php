@extends('layouts.app')

@section('title', 'رپورٹس')

@push('styles')
<style>
@media print {
    .header-menu-area, .footer-copyright-area, .nav, .navbar, .mobile-menu-area { display: none !important; }
    body { margin: 0; padding: 0; }
    .container { width: 100%; max-width: none; padding: 0; margin: 0; }
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
@media print {
    .header-menu-area, .footer-copyright-area, .container { padding: 0; }
    .nav-tabs, .row.mb-3 { display: none !important; }
    .tab-content > div > h4 { display: block !important; text-align: center; margin-bottom: 20px; }
    #partal_table_container, #completion_table_container { display: block !important; margin: 0; }
    table { font-size: 12px; }
}
table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 14px; }
table th, table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
table th { background-color: #e9ecef; color: #333; font-weight: bold; }
tr:nth-child(even) { background-color: #f9f9f9; }
tr:nth-child(odd) { background-color: #fff; }
tr:hover { background-color: #f1f1f1; }
.value-cell { background-color: #d4edda !important; }

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
                    <br>
                    <button id="partal_filter" class="btn btn-primary btn-sm">فلٹر</button>
                    <button id="partal_pdf" type="button" class="btn btn-danger btn-sm">PDF</button>
                    <button id="partal_excel" type="button" class="btn btn-success btn-sm">Excel</button>
                    <button id="partal_print" type="button" class="btn btn-info btn-sm">Print</button>
                </div>
            </div>
            <div class="clearfix"></div>
            <div id="partal_content">
               <br>
                
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
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->districtNameUrdu }}</td>
                            <td>{{ $item->tehsilNameUrdu }}</td>
                            <td>{{ $item->mozaNameUrdu }}</td>
                            <td>{{ $item->patwari_nam }}</td>
                            <td>{{ $item->ahalkar_nam }}</td>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

        <div id="completion" class="tab-pane fade">

            <div id="">
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
                <div class="col-md-2" style="padding-left:0;padding-right:0">
                    <br>
                    <button id="completion_filter" class="btn btn-primary btn-sm">فلٹر</button>
                    <button id="completion_pdf" type="button" class="btn btn-danger btn-sm">PDF</button>
                    <button id="completion_excel" type="button" class="btn btn-success btn-sm">Excel</button>
                    <button id="completion_print" type="button" class="btn btn-info btn-sm">Print</button>
                </div>
            </div>
           
            <div id="completion_content">
              
                
            <div class="clearfix"></div>
             <br>
              <center><h4>تکمیلی کام رپورٹ</h4></center>
            <div id="completion_table_container">
                <table>
                    <thead>
                        <tr>
                            <th>نمبر شمار</th>
                            <th>نام ضلع</th>
                            <th>نام تحصیل</th>
                            <th>نام موضع</th>
                            <th>نام اہلکار</th>
                            <th>میزان کھاتہ دار/کھتونی</th>
                            <th>پختہ کھتونی درانڈکس خسرہ</th>
                            <th>درستی بدرات</th>
                            <th>تحریر نقل شجرہ نسب</th>
                            <th>تحریر شجرہ نسب مالکان قبضہ</th>
                            <th>پختہ کھاتاجات</th>
                            <th>خام کھاتہ جات در شجرہ نسب</th>
                            <th>تحریر مشترکہ کھاتہ</th>
                            <th>پختہ نمبرواں در کھتونی</th>
                            <th>خام نمبرواں در کھتونی</th>
                            <th>تصدیق آخیر</th>
                            <th>متفرق کام</th>
                            <th>از تاریخ</th>
                            <th>تا تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($completion_data as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->districtNameUrdu }}</td>
                            <td>{{ $item->tehsilNameUrdu }}</td>
                            <td>{{ $item->mozaNameUrdu }}</td>
                            <td>{{ $item->employee_name }} @if(!empty($item->employee_type_title))<small>({{ $item->employee_type_title }})</small>@endif</td>
                            <td class="{{ $item->mizan_khata_dar_khatoni > 0 ? 'value-cell' : '' }}">{{ $item->mizan_khata_dar_khatoni == 0 ? '-' : $item->mizan_khata_dar_khatoni }}</td>
                            <td class="{{ $item->pukhta_khatoni_drandkas_khasra > 0 ? 'value-cell' : '' }}">{{ $item->pukhta_khatoni_drandkas_khasra == 0 ? '-' : $item->pukhta_khatoni_drandkas_khasra }}</td>
                            <td class="{{ $item->durusti_badrat > 0 ? 'value-cell' : '' }}">{{ $item->durusti_badrat == 0 ? '-' : $item->durusti_badrat }}</td>
                            <td class="{{ $item->tehreer_naqal_shajra_nasab > 0 ? 'value-cell' : '' }}">{{ $item->tehreer_naqal_shajra_nasab == 0 ? '-' : $item->tehreer_naqal_shajra_nasab }}</td>
                            <td class="{{ $item->tehreer_shajra_nasab_malkan_qabza > 0 ? 'value-cell' : '' }}">{{ $item->tehreer_shajra_nasab_malkan_qabza == 0 ? '-' : $item->tehreer_shajra_nasab_malkan_qabza }}</td>
                            <td class="{{ $item->pukhta_khatajat > 0 ? 'value-cell' : '' }}">{{ $item->pukhta_khatajat == 0 ? '-' : $item->pukhta_khatajat }}</td>
                            <td class="{{ $item->kham_khatajat_dar_shajra_nasab > 0 ? 'value-cell' : '' }}">{{ $item->kham_khatajat_dar_shajra_nasab == 0 ? '-' : $item->kham_khatajat_dar_shajra_nasab }}</td>
                            <td class="{{ $item->tehreer_mushtarka_khata > 0 ? 'value-cell' : '' }}">{{ $item->tehreer_mushtarka_khata == 0 ? '-' : $item->tehreer_mushtarka_khata }}</td>
                            <td class="{{ $item->pukhta_numberwan_dar_khatoni > 0 ? 'value-cell' : '' }}">{{ $item->pukhta_numberwan_dar_khatoni == 0 ? '-' : $item->pukhta_numberwan_dar_khatoni }}</td>
                            <td class="{{ $item->kham_numberwan_dar_khatoni > 0 ? 'value-cell' : '' }}">{{ $item->kham_numberwan_dar_khatoni == 0 ? '-' : $item->kham_numberwan_dar_khatoni }}</td>
                            <td class="{{ $item->tasdeeq_akhir > 0 ? 'value-cell' : '' }}">{{ $item->tasdeeq_akhir == 0 ? '-' : $item->tasdeeq_akhir }}</td>
                            <td class="{{ $item->mutafarriq_kaam > 0 ? 'value-cell' : '' }}">{{ $item->mutafarriq_kaam == 0 ? '-' : $item->mutafarriq_kaam }}</td>
                            <td>{{ $from_date }}</td>
                            <td>{{ $to_date }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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
                        <br>
                        <button id="grievances_filter" class="btn btn-primary btn-sm">فلٹر</button>
                        <button id="grievances_pdf" type="button" class="btn btn-danger btn-sm">PDF</button>
                        <button id="grievances_excel" type="button" class="btn btn-success btn-sm">Excel</button>
                        <button id="grievances_print" type="button" class="btn btn-info btn-sm">Print</button>
                    </div>
                </div>
    
                <div id="grievances_content">
    
    
                <div class="clearfix"></div>
                 <br>
                  <center><h4>شکایات رپورٹ</h4></center>
                <div id="grievances_table_container">
                    <table>
                        <thead>
                            <tr>
                                <th>نمبر شمار</th>
                                <th>نام ضلع</th>
                                <th>نام تحصیل</th>
                                <th>نام موضع</th>
                                <th>شکایت کنندہ کا نام</th>
                                <th>والد کا نام</th>
                                <th>شناختی کارڈ نمبر</th>
                                <th>شکایت کی قسم</th>
                                <th>حیثیت</th>
                                <th>تاریخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grievances_data ?? [] as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->district_name }}</td>
                                <td>{{ $item->tehsil_name }}</td>
                                <td>{{ $item->moza_name }}</td>
                                <td>{{ $item->applicant_name }}</td>
                                <td>{{ $item->father_name }}</td>
                                <td>{{ $item->cnic }}</td>
                                <td>{{ $item->grievance_type_name }}</td>
                                <td><span class="label label-{{ $item->status_color }}">{{ $item->status_name }}</span></td>
                                <td>{{ $item->application_date ? date('d-m-Y', strtotime($item->application_date)) : '' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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
        let html = '<table><thead><tr><th rowspan="2">سیریل نمبر</th><th colspan="7">بنیادی معلومات</th><th colspan="2">پڑتال پیمائش موقع</th><th colspan="2">تصدیق آخیر ملکیت وغیرہ بر موقع</th><th colspan="2">تصدیق آخیر شجرہ نسب</th><th colspan="2">تصدیق ملکیت و قبضہ کاشت وغیرہ</th><th rowspan="2">تبصرہ</th></tr><tr><th>ضلع نام</th><th>تحصیل نام</th><th>موضع نام</th><th>پٹواری نام</th><th>اہلکار نام</th><th>از تاریخ</th><th>تا تاریخ</th><th>تصدیق ملکیت/پیمود شدہ نمبرات خسرہ</th><th>تعداد برامدہ بدرات</th><th>تصدیق ملکیت و قبضہ کاشت نمبرات خسرہ</th><th>تعداد برامدہ بدرات</th><th>تعداد گھری</th><th>تعداد برامدہ بدرات</th><th>مقابلہ کھتونی ہمراہ کاپی چومنڈہ</th><th>تعداد برامدہ بدرات</th></tr></thead><tbody>';
        data.forEach(function(item, index) {
            html += '<tr><td>' + (index + 1) + '</td><td>' + item.districtNameUrdu + '</td><td>' + item.tehsilNameUrdu + '</td><td>' + item.mozaNameUrdu + '</td><td>' + item.patwari_nam + '</td><td>' + item.ahalkar_nam + '</td><td>' + $('#partal_from_date').val() + '</td><td>' + $('#partal_to_date').val() + '</td><td class="' + (item.tasdeeq_milkiat_pemuda_khasra > 0 ? 'value-cell' : '') + '">' + (item.tasdeeq_milkiat_pemuda_khasra == 0 ? '-' : item.tasdeeq_milkiat_pemuda_khasra) + '</td><td class="' + (item.tasdeeq_milkiat_pemuda_khasra_badrat > 0 ? 'value-cell' : '') + '">' + (item.tasdeeq_milkiat_pemuda_khasra_badrat == 0 ? '-' : item.tasdeeq_milkiat_pemuda_khasra_badrat) + '</td><td class="' + (item.tasdeeq_milkiat_qabza_kasht_khasra > 0 ? 'value-cell' : '') + '">' + (item.tasdeeq_milkiat_qabza_kasht_khasra == 0 ? '-' : item.tasdeeq_milkiat_qabza_kasht_khasra) + '</td><td class="' + (item.tasdeeq_milkiat_qabza_kasht_badrat > 0 ? 'value-cell' : '') + '">' + (item.tasdeeq_milkiat_qabza_kasht_badrat == 0 ? '-' : item.tasdeeq_milkiat_qabza_kasht_badrat) + '</td><td class="' + (item.tasdeeq_shajra_nasab_guri > 0 ? 'value-cell' : '') + '">' + (item.tasdeeq_shajra_nasab_guri == 0 ? '-' : item.tasdeeq_shajra_nasab_guri) + '</td><td class="' + (item.tasdeeq_shajra_nasab_badrat > 0 ? 'value-cell' : '') + '">' + (item.tasdeeq_shajra_nasab_badrat == 0 ? '-' : item.tasdeeq_shajra_nasab_badrat) + '</td><td class="' + (item.muqabala_khatoni_chomanda > 0 ? 'value-cell' : '') + '">' + (item.muqabala_khatoni_chomanda == 0 ? '-' : item.muqabala_khatoni_chomanda) + '</td><td class="' + (item.muqabala_khatoni_chomanda_badrat > 0 ? 'value-cell' : '') + '">' + (item.muqabala_khatoni_chomanda_badrat == 0 ? '-' : item.muqabala_khatoni_chomanda_badrat) + '</td><td>' + item.tabsara + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#partal_table_container').html(html);
    });
}

function loadCompletionReports() {
    $.get('{{ route("reports.completion_process") }}', getCompletionFilters(), function(data) {
        let html = '<table><thead><tr><th>نمبر شمار</th><th>نام ضلع</th><th>نام تحصیل</th><th>نام موضع</th><th>نام اہلکار</th><th>میزان کھاتہ دار/کھتونی</th><th>پختہ کھتونی درانڈکس خسرہ</th><th>درستی بدرات</th><th>تحریر نقل شجرہ نسب</th><th>تحریر شجرہ نسب مالکان قبضہ</th><th>پختہ کھاتاجات</th><th>خام کھاتہ جات در شجرہ نسب</th><th>تحریر مشترکہ کھاتہ</th><th>پختہ نمبرواں در کھتونی</th><th>خام نمبرواں در کھتونی</th><th>تصدیق آخیر</th><th>متفرق کام</th><th>از تاریخ</th><th>تا تاریخ</th></tr></thead><tbody>';
        data.forEach(function(item, index) {
            function getCell(value, target) {
                if (value == 0) return '<td>-</td>';

                console.log('Value:', value, 'Target:', target);
                var targetVal = target || 0;
                var text = targetVal + ' / ' + value;
                var cls = '';
                if (targetVal==0) {
                    cls += 'bg-warning';
                }
                else  if (value >= targetVal) {
                    cls += 'bg-success';
                }
                
                 else {
                    cls += 'bg-danger';
                }
                return '<td class="' + cls + '">' + text + '</td>';
            }
            html += '<tr><td>' + (index + 1) + '</td><td>' + item.districtNameUrdu + '</td><td>' + item.tehsilNameUrdu + '</td><td>' + item.mozaNameUrdu + '</td><td>' + item.employee_name + (item.employee_type_title ? ' <small>(' + item.employee_type_title + ')</small>' : '') + '</td>' +
                getCell(item.mizan_khata_dar_khatoni, item.target_mizan_khata_dar_khatoni) +
                getCell(item.pukhta_khatoni_drandkas_khasra, item.target_pukhta_khatoni_drandkas_khasra) +
                getCell(item.durusti_badrat, item.target_durusti_badrat) +
                getCell(item.tehreer_naqal_shajra_nasab, item.target_tehreer_naqal_shajra_nasab) +
                getCell(item.tehreer_shajra_nasab_malkan_qabza, item.target_tehreer_shajra_nasab_malkan_qabza) +
                getCell(item.pukhta_khatajat, item.target_pukhta_khatajat) +
                getCell(item.kham_khatajat_dar_shajra_nasab, item.target_kham_khatajat_dar_shajra_nasab) +
                getCell(item.tehreer_mushtarka_khata, item.target_tehreer_mushtarka_khata) +
                getCell(item.pukhta_numberwan_dar_khatoni, item.target_pukhta_numberwan_dar_khatoni) +
                getCell(item.kham_numberwan_dar_khatoni, item.target_kham_numberwan_dar_khatoni) +
                getCell(item.tasdeeq_akhir, item.target_tasdeeq_akhir) +
                getCell(item.mutafarriq_kaam, item.target_mutafarriq_kaam) +
                '<td>' + $('#completion_from_date').val() + '</td><td>' + $('#completion_to_date').val() + '</td></tr>';
        });
        html += '</tbody></table>';
        $('#completion_table_container').html(html);
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