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
               <!-- Grievances Chart -->
               <div class="col-md-6">
                   <div class="panel panel-default">
                       <div class="panel-heading">
                           <h4>شکایات</h4>
                       </div>
                       <div class="panel-body">
                           <select id="grievancesPeriod" class="form-control" style="width: auto; display: inline-block; margin-bottom: 10px;">
                               <option value="current_year">موجودہ سال</option>
                               <option value="last_year">گذشتہ سال</option>
                               <option value="current_month">موجودہ مہینہ</option>
                               <option value="last_month">گذشتہ مہینہ</option>
                               <option value="current_week">موجودہ ہفتہ</option>
                               <option value="last_week">گذشتہ ہفتہ</option>
                           </select>
                           <canvas id="grievancesChart" ></canvas>
                       </div>
                   </div>
               </div>

                <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>شکایات کی اقسام</h3>
                </div>
                <div class="panel-body">
                    <select id="grievancesByTypePeriod" class="form-control" style="width: auto; display: inline-block; margin-bottom: 10px;">
                        <option value="current_year">موجودہ سال</option>
                        <option value="last_year">گذشتہ سال</option>
                        <option value="current_month">موجودہ مہینہ</option>
                        <option value="last_month">گذشتہ مہینہ</option>
                        <option value="current_week">موجودہ ہفتہ</option>
                        <option value="last_week">گذشتہ ہفتہ</option>
                    </select>
                    <canvas id="grievancesByTypeChart"></canvas>
                </div>
            </div>
        </div>

               <!-- Completion Process Chart -->
               <div class="col-md-6">
                   <div class="panel panel-default">
                       <div class="panel-heading">
                           <h4>تکمیلی کام</h4>
                       </div>
                       <div class="panel-body">
                           <select id="completionProcessPeriod" class="form-control" style="width: auto; display: inline-block; margin-bottom: 10px;">
                               <option value="current_year">موجودہ سال</option>
                               <option value="last_year">گذشتہ سال</option>
                               <option value="current_month">موجودہ مہینہ</option>
                               <option value="last_month">گذشتہ مہینہ</option>
                               <option value="current_week">موجودہ ہفتہ</option>
                               <option value="last_week">گذشتہ ہفتہ</option>
                           </select>
                           <canvas id="completionProcessChart" style="max-width: 300px; max-height: 300px; display: block; margin: 0 auto;"></canvas>
                       </div>
                   </div>
               </div>
               <!-- Partal Chart -->
               <div class="col-md-6">
                   <div class="panel panel-default">
                       <div class="panel-heading">
                           <h4>پڑتال</h4>
                       </div>
                       <div class="panel-body">
                           <select id="partalPeriod" class="form-control" style="width: auto; display: inline-block; margin-bottom: 10px;">
                               <option value="current_year">موجودہ سال</option>
                               <option value="last_year">گذشتہ سال</option>
                               <option value="current_month">موجودہ مہینہ</option>
                               <option value="last_month">گذشتہ مہینہ</option>
                               <option value="current_week">موجودہ ہفتہ</option>
                               <option value="last_week">گذشتہ ہفتہ</option>
                           </select>
                           <canvas id="partalChart" style="max-width: 300px; max-height: 300px; display: block; margin: 0 auto;"></canvas>
                       </div>
                   </div>
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
            </div>
</div>
            <div class="clearfix"></div>
 <div class="row">
     <div class="col-md-6">
         <div class="panel panel-default">
             <div class="panel-heading">
                 <h3>گوشوارہ پڑتال خلاصہ</h3>
             </div>
             <div class="panel-body">
                 <select id="partalSummaryPeriod" class="form-control" style="width: auto; display: inline-block; margin-bottom: 10px;">
                     <option value="current_year">موجودہ سال</option>
                     <option value="last_year">گذشتہ سال</option>
                     <option value="current_month">موجودہ مہینہ</option>
                     <option value="last_month">گذشتہ مہینہ</option>
                     <option value="current_week">موجودہ ہفتہ</option>
                     <option value="last_week">گذشتہ ہفتہ</option>
                 </select>
                 <canvas id="partalSummaryChart"></canvas>
             </div>
         </div>
     </div>
     

  
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>تکمیلی عمل خلاصہ</h3>
                </div>
                <div class="panel-body">
                    <select id="completionProcessSummaryPeriod" class="form-control" style="width: auto; display: inline-block; margin-bottom: 10px;">
                        <option value="current_year">موجودہ سال</option>
                        <option value="last_year">گذشتہ سال</option>
                        <option value="current_month">موجودہ مہینہ</option>
                        <option value="last_month">گذشتہ مہینہ</option>
                        <option value="current_week">موجودہ ہفتہ</option>
                        <option value="last_week">گذشتہ ہفتہ</option>
                    </select>
                    <canvas id="completionProcessSummaryChart"></canvas>
                </div>
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

            // Grievances Chart
            let grievancesChart = createBarChart('grievancesChart', 'شکایات', chartData.grievances.current_year);

            // Handle period change
            document.getElementById('grievancesPeriod').addEventListener('change', function() {
                const selectedPeriod = this.value;
                const data = chartData.grievances[selectedPeriod];
                const labels = Object.keys(data);
                const values = Object.values(data);
                const colors = [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 205, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ];
                const borderColors = [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ];
                grievancesChart.data.labels = labels;
                grievancesChart.data.datasets[0].data = values;
                grievancesChart.data.datasets[0].backgroundColor = colors.slice(0, labels.length);
                grievancesChart.data.datasets[0].borderColor = borderColors.slice(0, labels.length);
                grievancesChart.update();
            });

            // Completion Process Chart
            let completionProcessChart = createDoughnutChart('completionProcessChart', 'تکمیلی کام', Object.keys(chartData.completion_process.current_year), Object.values(chartData.completion_process.current_year), [
                'rgba(45, 125, 45, 0.8)',    // Dark Green
                'rgba(54, 162, 235, 0.8)',   // Blue
                'rgba(255, 205, 86, 0.8)',   // Yellow
                'rgba(75, 192, 192, 0.8)',   // Teal
                'rgba(153, 102, 255, 0.8)',  // Purple
                'rgba(255, 159, 64, 0.8)',   // Orange
                'rgba(199, 199, 199, 0.8)',  // Grey
                'rgba(83, 102, 255, 0.8)',   // Indigo
                'rgba(255, 99, 255, 0.8)',   // Pink
                'rgba(99, 255, 132, 0.8)'    // Light Green
            ], [
                'rgba(45, 125, 45, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(199, 199, 199, 1)',
                'rgba(83, 102, 255, 1)',
                'rgba(255, 99, 255, 1)',
                'rgba(99, 255, 132, 1)'
            ]);

            // Handle completion process period change
            document.getElementById('completionProcessPeriod').addEventListener('change', function() {
                const selectedPeriod = this.value;
                const data = chartData.completion_process[selectedPeriod];
                const labels = Object.keys(data);
                const values = Object.values(data);
                completionProcessChart.data.labels = labels;
                completionProcessChart.data.datasets[0].data = values;
                completionProcessChart.update();
            });

            // Partal Chart
            let partalChart = createDoughnutChart('partalChart', 'پڑتال', Object.keys(chartData.partal.current_year), Object.values(chartData.partal.current_year), [
                'rgba(255, 87, 34, 0.8)',    // Deep Orange
                'rgba(156, 39, 176, 0.8)',   // Deep Purple
                'rgba(0, 150, 136, 0.8)',    // Teal
                'rgba(255, 193, 7, 0.8)',    // Amber
                'rgba(33, 150, 243, 0.8)',   // Blue
                'rgba(96, 125, 139, 0.8)',   // Blue Grey
                'rgba(63, 81, 181, 0.8)',    // Indigo
                'rgba(233, 30, 99, 0.8)',    // Pink
                'rgba(76, 175, 80, 0.8)',    // Green
                'rgba(121, 85, 72, 0.8)'     // Brown
            ], [
                'rgba(255, 87, 34, 1)',
                'rgba(156, 39, 176, 1)',
                'rgba(0, 150, 136, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(33, 150, 243, 1)',
                'rgba(96, 125, 139, 1)',
                'rgba(63, 81, 181, 1)',
                'rgba(233, 30, 99, 1)',
                'rgba(76, 175, 80, 1)',
                'rgba(121, 85, 72, 1)'
            ]);

            // Handle partal period change
            document.getElementById('partalPeriod').addEventListener('change', function() {
                const selectedPeriod = this.value;
                const data = chartData.partal[selectedPeriod];
                const labels = Object.keys(data);
                const values = Object.values(data);
                partalChart.data.labels = labels;
                partalChart.data.datasets[0].data = values;
                partalChart.update();
            });

            // Partal Summary Chart
            let partalSummaryChart = createBarChart('partalSummaryChart', 'گوشوارہ پڑتال خلاصہ', @json($partal_sums['current_year']));

            // Handle partal summary period change
            document.getElementById('partalSummaryPeriod').addEventListener('change', function() {
                const selectedPeriod = this.value;
                const data = @json($partal_sums)[selectedPeriod];
                const labels = Object.keys(data);
                const values = Object.values(data);
                partalSummaryChart.data.labels = labels;
                partalSummaryChart.data.datasets[0].data = values;
                partalSummaryChart.update();
            });

            // Completion Process Summary Chart
            let completionProcessSummaryChart = createBarChart('completionProcessSummaryChart', 'تکمیلی عمل خلاصہ', @json($completion_process_sums['current_year']));

            // Handle completion process summary period change
            document.getElementById('completionProcessSummaryPeriod').addEventListener('change', function() {
                const selectedPeriod = this.value;
                const data = @json($completion_process_sums)[selectedPeriod];
                const labels = Object.keys(data);
                const values = Object.values(data);
                completionProcessSummaryChart.data.labels = labels;
                completionProcessSummaryChart.data.datasets[0].data = values;
                completionProcessSummaryChart.update();
            });

            // Grievances by Type Chart
            let grievancesByTypeChart = createBarChart('grievancesByTypeChart', 'شکایات کی اقسام', @json($grievances_by_type['current_year']));

            // Handle grievances by type period change
            document.getElementById('grievancesByTypePeriod').addEventListener('change', function() {
                const selectedPeriod = this.value;
                const data = @json($grievances_by_type)[selectedPeriod];
                const labels = Object.keys(data);
                const values = Object.values(data);
                grievancesByTypeChart.data.labels = labels;
                grievancesByTypeChart.data.datasets[0].data = values;
                grievancesByTypeChart.update();
            });

            function createBarChart(canvasId, title, data) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                const colors = [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 205, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ];
                const borderColors = [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ];
                return new Chart(ctx, {
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

            function createDoughnutChart(canvasId, title, labels, data, colors, borderColors) {
                const ctx = document.getElementById(canvasId).getContext('2d');
                return new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: title,
                            data: data,
                            backgroundColor: colors.slice(0, labels.length),
                            borderColor: borderColors.slice(0, labels.length),
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
