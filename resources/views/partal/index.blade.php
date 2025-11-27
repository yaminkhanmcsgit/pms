@extends('layouts.app')

@section('title', 'پڑتال ریکارڈ')

@section('content')
<div class="container" dir="rtl">
    <style>
        thead th {
            border:1px solid #ddd8d8 !important;
        }
        @media print { .no-print { display: none; } }
    </style>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('partal.create') }}" class="btn btn-success">
            <i class="fa fa-plus"></i> نیا ریکارڈ شامل کریں
        </a>
        <center><h3 class="mb-0">گوشوارہ پڑتال رپورٹ</h3></center>
    </div>

    <div class="table-responsive">
        <table id="partal_table" class="table table-striped table-hover table-bordered text-center" style="width: 100%;">
            <thead class="thead-dark">
                <tr>
                    <th rowspan="2">سیریل نمبر</th>
                    <th colspan="6">بنیادی معلومات</th>
                    <th colspan="2">پڑتال پیمائش موقع</th>
                    <th colspan="2">تصدیق آخیر ملکیت وغیرہ بر موقع</th>
                    <th colspan="2">تصدیق آخیر شجرہ نسب</th>
                    <th colspan="2">تصدیق ملکیت و قبضہ کاشت وغیرہ</th>
                    <th rowspan="2">تبصرہ</th>
                    <th rowspan="2">عمل</th>
                </tr>
                <tr>
                    <th>ضلع نام</th>
                    <th>تحصیل نام</th>
                    <th>موضع نام</th>
                    <th>پٹواری نام</th>
                    <th>اہلکار نام</th>
                    <th>تاریخ پڑتال</th>
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
        </table>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#partal_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('partal.datatable') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id', orderable: false },
            { data: 'districtNameUrdu', orderable: false },
            { data: 'tehsilNameUrdu', orderable: false },
            { data: 'mozaNameUrdu', orderable: false },
            { data: 'patwari_nam', orderable: false },
            { data: 'ahalkar_nam', orderable: false },
            { data: 'tareekh_partal', orderable: false },
            { data: 'tasdeeq_milkiat_pemuda_khasra', orderable: false },
            { data: 'tasdeeq_milkiat_pemuda_khasra_badrat', orderable: false },
            { data: 'tasdeeq_milkiat_qabza_kasht_khasra', orderable: false },
            { data: 'tasdeeq_milkiat_qabza_kasht_badrat', orderable: false },
            { data: 'tasdeeq_shajra_nasab_guri', orderable: false },
            { data: 'tasdeeq_shajra_nasab_badrat', orderable: false },
            { data: 'muqabala_khatoni_chomanda', orderable: false },
            { data: 'muqabala_khatoni_chomanda_badrat', orderable: false },
            { data: 'tabsara', orderable: false },
            { data: 'actions', orderable: false }
        ],
        columnDefs: [
            { targets: [7,8,9,10,11,12,13,14], className: 'text-center' },
            { targets: [15], className: 'text-right' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        
        dom: '<"row"<"col-sm-4"l><"col-sm-4"f><"col-sm-4"B>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
        buttons: [
            { extend: 'csv', title: 'Partal Records', text: 'CSV', className: 'btn btn-sm btn-success' },
            { extend: 'excel', title: 'Partal Records', text: 'Excel', className: 'btn btn-sm btn-success' },
            { extend: 'pdf', title: 'Partal Records', text: 'PDF', className: 'btn btn-sm btn-success' },
            { extend: 'print', text: 'پرنٹ', className: 'btn btn-sm btn-success' }
        ]
       
    });
});
</script>
@endsection
