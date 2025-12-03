@extends('layouts.app')

@section('title', 'ڈیش بورڈ')

@section('content')
<div class="container " dir="rtl" >
   
   <div class="row dashboard-panels">
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
           <div class="panel panel-success">
               <div class="panel-heading">صارفین</div>
               <div class="panel-body text-center">
                   <a href="{{ route('operators.index') }}" class="btn btn-success">صارفین دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
           <div class="panel panel-info">
               <div class="panel-heading">ملازمین</div>
               <div class="panel-body text-center">
                   <a href="{{ route('employees.index') }}" class="btn btn-primary">ملازمین دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
           <div class="panel panel-warning">
               <div class="panel-heading">تکمیلی عمل</div>
               <div class="panel-body text-center">
                   <a href="{{ route('completion_process.index') }}" class="btn btn-warning">تکمیلی عمل دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
           <div class="panel panel-danger">
               <div class="panel-heading">پڑتال</div>
               <div class="panel-body text-center">
                   <a href="{{ route('partal.index') }}" class="btn btn-danger">پڑتال دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
           <div class="panel panel-primary">
               <div class="panel-heading">شکایات</div>
               <div class="panel-body text-center">
                   <a href="{{ route('grievances.index') }}" class="btn btn-primary">شکایات دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
           <div class="panel panel-default">
               <div class="panel-heading">ترتیبات</div>
               <div class="panel-body text-center">
                   <a href="{{ route('settings.edit') }}" class="btn btn-default">ترتیبات دیکھیں</a>
               </div>
           </div>
       </div>
   </div>

   <!-- Charts Section -->
   <div class="row">
       <div class="col-md-12">
           <div class="dashboard-header">
               <h3>ڈیٹا چارٹس</h3>
           </div>
           <div class="row">
               <!-- Grievances Charts -->
               <div class="col-md-4">
                   <h4>شکایات - موجودہ سال</h4>
                   <canvas id="grievancesCurrentYearChart"></canvas>
               </div>
               <div class="col-md-4">
                   <h4>شکایات - آخری 30 دن</h4>
                   <canvas id="grievancesLast30DaysChart"></canvas>
               </div>
               <div class="col-md-4">
                   <h4>شکایات - آخری 7 دن</h4>
                   <canvas id="grievancesLast7DaysChart"></canvas>
               </div>
           </div>
           <Br>
           <div class="row">
               <!-- Completion Process Chart -->
               <div class="col-md-6">
                   <h4>تکمیلی کام</h4>
                   <canvas id="completionProcessChart" style="max-width: 300px; max-height: 300px;"></canvas>
               </div>
               <!-- Partal Chart -->
               <div class="col-md-6">
                   <h4>پڑتال</h4>
                   <canvas id="partalChart" style="max-width: 300px; max-height: 300px;"></canvas>
               </div>
           </div>
       </div>
   </div>

    <!-- گوشوارہ پڑتال رپورٹ section with table -->
    <div>
        <div class="">
            <style>
                .dashboard-header { text-align: center; margin: 30px 0 20px 0; padding: 15px; background: #f7f7f7; color: #333; border-radius: 5px; border: 1px solid #e0e0e0; }
                .report-container { padding: 10px 0 0 0; border-radius: 0; box-shadow: none; margin-bottom: 20px; background: none; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 14px; }
                table th, table td { border: 1px solid #ddd; padding: 10px; text-align: center; }
                table th { background-color: #e9ecef; color: #333; font-weight: bold; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                tr:nth-child(odd) { background-color: #fff; }
                tr:hover { background-color: #f1f1f1; }
                .filter-form { margin-bottom: 20px; }
                .value-cell { background-color: #d4edda !important; }
            </style>
            <div class="text-center" style="display:none;">
            <form method="GET" action="{{ route('dashboard') }}" class="filter-form form-inline" style="display: block; margin: 0 auto; width: fit-content;">
                <div class="form-group">
                    <label for="district_id">ضلع:</label>
                    @if($role_id == 1)
                    <select name="district_id" id="district_id" class="form-control" onchange="onDistrictChange(this.value, 'tehsil_id')">
                        <option value="">تمام</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->districtId }}" {{ (request('district_id') == $d->districtId) ? 'selected' : '' }}>{{ $d->districtNameUrdu }}</option>
                        @endforeach
                    </select>
                    @else
                    <input type="hidden" name="district_id" value="{{ session('zila_id') }}">
                    <input type="text" class="form-control" value="{{ $assigned_district_name }}" readonly>
                    @endif
                </div>
                <div class="form-group">
                    <label for="tehsil_id">تحصیل:</label>
                    @if($role_id == 1)
                    <select name="tehsil_id" id="tehsil_id" class="form-control">
                        <option value="">تمام</option>
                    </select>
                    @else
                    <input type="hidden" name="tehsil_id" value="{{ session('tehsil_id') }}">
                    <input type="text" class="form-control" value="{{ $assigned_tehsil_name }}" readonly>
                    @endif
                </div>
                <div class="form-group">
                    <label for="from_date">از تاریخ:</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ $from_date ?? '' }}">
                </div>
                <div class="form-group">
                    <label for="to_date">تا تاریخ:</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ $to_date ?? '' }}">
                </div>
                <button type="submit" class="btn btn-info" style="margin-right:5px;">فلٹر کریں</button>
            </form>
            </div>
            <div class="clearfix"></div>
 <div class="row">
    <div class="col-md-12">
            <div class="dashboard-header">
             <h3>گوشوارہ پڑتال خلاصہ ({{ $from_date ? date('d-m-Y', strtotime($from_date)) : '' }} تا {{ $to_date ? date('d-m-Y', strtotime($to_date)) : '' }})</h3>
            </div>
            <div class="report-container">
                <table id="reportTable">
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
                        @foreach($records as $index => $rec)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $rec->districtNameUrdu }}</td>
                            <td>{{ $rec->tehsilNameUrdu }}</td>
                            <td>{{ $rec->mozaNameUrdu }}</td>
                            <td>{{ $rec->patwari_nam }}</td>
                            <td>{{ $rec->ahalkar_nam }}</td>
                            <td >{{ $from_date ? date('d-m-Y', strtotime($from_date)) : '' }}</td>
                            <td >{{ $to_date ? date('d-m-Y', strtotime($to_date)) : '' }}</td>
                            <td class="{{ $rec->tasdeeq_milkiat_pemuda_khasra > 0 ? 'value-cell' : '' }}">{{ $rec->tasdeeq_milkiat_pemuda_khasra == 0 ? '-' : $rec->tasdeeq_milkiat_pemuda_khasra }}</td>
                            <td class="{{ $rec->tasdeeq_milkiat_pemuda_khasra_badrat > 0 ? 'value-cell' : '' }}">{{ $rec->tasdeeq_milkiat_pemuda_khasra_badrat == 0 ? '-' : $rec->tasdeeq_milkiat_pemuda_khasra_badrat }}</td>
                            <td class="{{ $rec->tasdeeq_milkiat_qabza_kasht_khasra > 0 ? 'value-cell' : '' }}">{{ $rec->tasdeeq_milkiat_qabza_kasht_khasra == 0 ? '-' : $rec->tasdeeq_milkiat_qabza_kasht_khasra }}</td>
                            <td class="{{ $rec->tasdeeq_milkiat_qabza_kasht_badrat > 0 ? 'value-cell' : '' }}">{{ $rec->tasdeeq_milkiat_qabza_kasht_badrat == 0 ? '-' : $rec->tasdeeq_milkiat_qabza_kasht_badrat }}</td>
                            <td class="{{ $rec->tasdeeq_shajra_nasab_guri > 0 ? 'value-cell' : '' }}">{{ $rec->tasdeeq_shajra_nasab_guri == 0 ? '-' : $rec->tasdeeq_shajra_nasab_guri }}</td>
                            <td class="{{ $rec->tasdeeq_shajra_nasab_badrat > 0 ? 'value-cell' : '' }}">{{ $rec->tasdeeq_shajra_nasab_badrat == 0 ? '-' : $rec->tasdeeq_shajra_nasab_badrat }}</td>
                            <td class="{{ $rec->muqabala_khatoni_chomanda > 0 ? 'value-cell' : '' }}">{{ $rec->muqabala_khatoni_chomanda == 0 ? '-' : $rec->muqabala_khatoni_chomanda }}</td>
                            <td class="{{ $rec->muqabala_khatoni_chomanda_badrat > 0 ? 'value-cell' : '' }}">{{ $rec->muqabala_khatoni_chomanda_badrat == 0 ? '-' : $rec->muqabala_khatoni_chomanda_badrat }}</td>
                            <td>{{ $rec->tabsara }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>
    <!-- تکمیلی عمل رپورٹ section with table -->
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-header">
            <h3>تکمیلی عمل خلاصہ ({{ $from_date ? date('d-m-Y', strtotime($from_date)) : '' }} تا {{ $to_date ? date('d-m-Y', strtotime($to_date)) : '' }})</h3>
            </div>
            <div class="report-container">
                <table id="completionProcessTable">
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
                        @foreach($completion_process as $index => $cp)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $cp->districtNameUrdu }}</td>
                            <td>{{ $cp->tehsilNameUrdu }}</td>
                            <td>{{ $cp->mozaNameUrdu }}</td>
                            <td>{{ $cp->employee_name }} @if(!empty($cp->employee_type_title))<small>({{ $cp->employee_type_title }})</small>@endif</td>
                            <td class="{{ $cp->mizan_khata_dar_khatoni > 0 ? 'value-cell' : '' }}">{{ $cp->mizan_khata_dar_khatoni == 0 ? '-' : $cp->mizan_khata_dar_khatoni }}</td>
                            <td class="{{ $cp->pukhta_khatoni_drandkas_khasra > 0 ? 'value-cell' : '' }}">{{ $cp->pukhta_khatoni_drandkas_khasra == 0 ? '-' : $cp->pukhta_khatoni_drandkas_khasra }}</td>
                            <td class="{{ $cp->durusti_badrat > 0 ? 'value-cell' : '' }}">{{ $cp->durusti_badrat == 0 ? '-' : $cp->durusti_badrat }}</td>
                            <td class="{{ $cp->tehreer_naqal_shajra_nasab > 0 ? 'value-cell' : '' }}">{{ $cp->tehreer_naqal_shajra_nasab == 0 ? '-' : $cp->tehreer_naqal_shajra_nasab }}</td>
                            <td class="{{ $cp->tehreer_shajra_nasab_malkan_qabza > 0 ? 'value-cell' : '' }}">{{ $cp->tehreer_shajra_nasab_malkan_qabza == 0 ? '-' : $cp->tehreer_shajra_nasab_malkan_qabza }}</td>
                            <td class="{{ $cp->pukhta_khatajat > 0 ? 'value-cell' : '' }}">{{ $cp->pukhta_khatajat == 0 ? '-' : $cp->pukhta_khatajat }}</td>
                            <td class="{{ $cp->kham_khatajat_dar_shajra_nasab > 0 ? 'value-cell' : '' }}">{{ $cp->kham_khatajat_dar_shajra_nasab == 0 ? '-' : $cp->kham_khatajat_dar_shajra_nasab }}</td>
                            <td class="{{ $cp->tehreer_mushtarka_khata > 0 ? 'value-cell' : '' }}">{{ $cp->tehreer_mushtarka_khata == 0 ? '-' : $cp->tehreer_mushtarka_khata }}</td>
                            <td class="{{ $cp->pukhta_numberwan_dar_khatoni > 0 ? 'value-cell' : '' }}">{{ $cp->pukhta_numberwan_dar_khatoni == 0 ? '-' : $cp->pukhta_numberwan_dar_khatoni }}</td>
                            <td class="{{ $cp->kham_numberwan_dar_khatoni > 0 ? 'value-cell' : '' }}">{{ $cp->kham_numberwan_dar_khatoni == 0 ? '-' : $cp->kham_numberwan_dar_khatoni }}</td>
                            <td class="{{ $cp->tasdeeq_akhir > 0 ? 'value-cell' : '' }}">{{ $cp->tasdeeq_akhir == 0 ? '-' : $cp->tasdeeq_akhir }}</td>
                            <td class="{{ $cp->mutafarriq_kaam > 0 ? 'value-cell' : '' }}">{{ $cp->mutafarriq_kaam == 0 ? '-' : $cp->mutafarriq_kaam }}</td>
                            <td>{{ $from_date ? date('d-m-Y', strtotime($from_date)) : '' }}</td>
                            <td>{{ $to_date ? date('d-m-Y', strtotime($to_date)) : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- شکایات رپورٹ section with table -->
    <div class="row">
        <div class="col-md-12">
            <div class="dashboard-header">
                <h3>شکایات خلاصہ ({{ $from_date ? date('d-m-Y', strtotime($from_date)) : '' }} تا {{ $to_date ? date('d-m-Y', strtotime($to_date)) : '' }})</h3>
            </div>
            <div class="report-container">
                <table id="grievancesTable">
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
                        @foreach($grievances as $index => $grievance)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $grievance->district_name }}</td>
                            <td>{{ $grievance->tehsil_name }}</td>
                            <td>{{ $grievance->moza_name }}</td>
                            <td>{{ $grievance->applicant_name }}</td>
                            <td>{{ $grievance->father_name }}</td>
                            <td>{{ $grievance->cnic }}</td>
                            <td>{{ $grievance->grievance_type_name }}</td>
                            <td><span class="label label-{{ $grievance->status_color }}">{{ $grievance->status_name }}</span></td>
                            <td>{{ $grievance->application_date ? date('d-m-Y', strtotime($grievance->application_date)) : '' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var districtSelect = document.getElementById('district_id');
            var selectedDistrict = districtSelect.value;
            if (selectedDistrict) {
                onDistrictChange(selectedDistrict, 'tehsil_id', '{{ request("tehsil_id") }}');
            }

            // Chart.js code
            const chartData = @json($chart_data);

            console.log(chartData);

            // Grievances Charts
            createBarChart('grievancesCurrentYearChart', 'شکایات موجودہ سال', chartData.grievances.current_year);
            createBarChart('grievancesLast30DaysChart', 'شکایات آخری 30 دن', chartData.grievances.last_30_days);
            createBarChart('grievancesLast7DaysChart', 'شکایات آخری 7 دن', chartData.grievances.last_7_days);

            // Completion Process Chart
            createDoughnutChart('completionProcessChart', 'تکمیلی کام', ['موجودہ سال', 'آخری 30 دن', 'آخری 7 دن'], chartData.completion_process);

            // Partal Chart
            createDoughnutChart('partalChart', 'پڑتال', ['موجودہ سال', 'آخری 30 دن', 'آخری 7 دن'], chartData.partal);

            function createBarChart(canvasId, title, data) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                const colors = [
                    'rgba(76, 175, 80, 0.6)',
                    'rgba(33, 150, 243, 0.6)',
                    'rgba(255, 193, 7, 0.6)',
                    'rgba(156, 39, 176, 0.6)',
                    'rgba(255, 87, 34, 0.6)',
                    'rgba(0, 188, 212, 0.6)'
                ];
                const borderColors = [
                    'rgba(76, 175, 80, 1)',
                    'rgba(33, 150, 243, 1)',
                    'rgba(255, 193, 7, 1)',
                    'rgba(156, 39, 176, 1)',
                    'rgba(255, 87, 34, 1)',
                    'rgba(0, 188, 212, 1)'
                ];
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(data),
                        datasets: [{
                            label: title,
                            data: Object.values(data),
                            backgroundColor: colors.slice(0, Object.keys(data).length),
                            borderColor: borderColors.slice(0, Object.keys(data).length),
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    callback: function(value) {
                                        return Number(value).toFixed(0);
                                    }
                                }
                            }
                        }
                    }
                });
            }

            function createLineChart(canvasId, title, labels, data) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: title,
                            data: data,
                            backgroundColor: 'rgba(45, 125, 45, 0.2)',
                            borderColor: 'rgba(45, 125, 45, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }

            function createDoughnutChart(canvasId, title, labels, data) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: title,
                            data: data,
                            backgroundColor: [
                                'rgba(255, 99, 132, 0.8)',
                                'rgba(54, 162, 235, 0.8)',
                                'rgba(255, 205, 86, 0.8)'
                            ],
                            borderColor: [
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(255, 205, 86, 1)'
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            }
                        }
                    }
                });
            }
        });
    </script>

</div>
@endsection
