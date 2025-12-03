@extends('layouts.app')

@section('title', 'ڈیش بورڈ')

@section('content')
<style>
.dashboard-card {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    color: #ffffff;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 140px;
    display: flex;
    flex-direction: column;
    position: relative;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

.dashboard-card-header {
    padding: 15px 20px 10px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.dashboard-card-title {
    margin: 0;
    font-size: 1.1em;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.dashboard-card-body {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 10px 20px;
}

.dashboard-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: #ffffff;
    padding: 8px 16px;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 500;
    font-size: 0.9em;
    transition: all 0.3s ease;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    display: inline-block;
}

.dashboard-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: #ffffff;
    text-decoration: none;
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .dashboard-card {
        height: 120px;
    }

    .dashboard-card-title {
        font-size: 1em;
    }

    .dashboard-btn {
        padding: 6px 12px;
        font-size: 0.8em;
    }
}
</style>
<div class="container " dir="rtl" >
   
   <div class="row dashboard-panels">
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
           <div class="dashboard-card" style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);">
               <div class="dashboard-card-header">
                   <h4 class="dashboard-card-title">({{ $operators_count }}) صارفین</h4>
               </div>
               <div class="dashboard-card-body">
                   <a href="{{ route('operators.index') }}" class="dashboard-btn">صارفین دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
           <div class="dashboard-card" style="background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);">
               <div class="dashboard-card-header">
                   <h4 class="dashboard-card-title">({{ $employees_count }}) ملازمین</h4>
               </div>
               <div class="dashboard-card-body">
                   <a href="{{ route('employees.index') }}" class="dashboard-btn">ملازمین دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
           <div class="dashboard-card" style="background: linear-gradient(135deg, #FF9800 0%, #F57C00 100%);">
               <div class="dashboard-card-header">
                   <h4 class="dashboard-card-title">({{ $completion_process_count }}) تکمیلی عمل</h4>
               </div>
               <div class="dashboard-card-body">
                   <a href="{{ route('completion_process.index') }}" class="dashboard-btn">تکمیلی عمل دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
           <div class="dashboard-card" style="background: linear-gradient(135deg, #F44336 0%, #D32F2F 100%);">
               <div class="dashboard-card-header">
                   <h4 class="dashboard-card-title">({{ $partal_count }}) پڑتال</h4>
               </div>
               <div class="dashboard-card-body">
                   <a href="{{ route('partal.index') }}" class="dashboard-btn">پڑتال دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
           <div class="dashboard-card" style="background: linear-gradient(135deg, #9C27B0 0%, #7B1FA2 100%);">
               <div class="dashboard-card-header">
                   <h4 class="dashboard-card-title">({{ $grievances_count }}) شکایات</h4>
               </div>
               <div class="dashboard-card-body">
                   <a href="{{ route('grievances.index') }}" class="dashboard-btn">شکایات دیکھیں</a>
               </div>
           </div>
       </div>
       <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" style="margin-bottom: 20px;">
           <div class="dashboard-card" style="background: linear-gradient(135deg, #607D8B 0%, #455A64 100%);">
               <div class="dashboard-card-header">
                   <h4 class="dashboard-card-title">ترتیبات</h4>
               </div>
               <div class="dashboard-card-body">
                   <a href="{{ route('settings.edit') }}" class="dashboard-btn">ترتیبات دیکھیں</a>
               </div>
           </div>
       </div>
   </div>

   <!-- Charts Section -->
   <div class="row">
       <div class="col-md-12">
          
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
