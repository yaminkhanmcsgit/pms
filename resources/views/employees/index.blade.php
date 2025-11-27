@extends('layouts.app')

@section('title', 'ملازمین')

@section('content')
<div class="container" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-3">
       
        <a href="{{ route('employees.create') }}" class="btn btn-success">
            <i class="fa fa-plus"></i> ملازم شامل کریں
        </a>
    </div>
    
    <center><legend> <h3>ملازمین کی فہرست</h3></legend></center>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark text-center">
                <tr>
                    <th>نمبر شمار</th>
                    <th>نام</th>
                    <th>والد کا نام</th>
                    <th>نام ضلع</th>
                    <th>نام تحصیل</th>
                    <th>نام موضع</th>
                    <th>فون</th>
                    <th>شناختی کارڈ</th>
                    <th>تعلیم</th>
                    <th>اہلکار کی قسم</th>
                    <th>تاریخ شمولیت</th>
                    <th class="no-print">عمل</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($employees as $emp)
                <tr>
                    <td>{{ $emp->id }}</td>
                    <td>{{ $emp->nam }}</td>
                    <td>{{ $emp->walid_ka_nam }}</td>
                    <td>{{ $emp->district_name }}</td>
                    <td>{{ $emp->tehsil_name }}</td>
                    <td>{{ $emp->moza_name }}</td>
                    <td>{{ $emp->phone }}</td>
                    <td>{{ $emp->cnic }}</td>
                    <td>{{ $emp->darja_taleem }}</td>
                    <td>{{ $emp->employee_type_title }}</td>
                    <td>{{ $emp->tareekh_shamil ? date('d-m-Y', strtotime($emp->tareekh_shamil)) : '' }}</td>
                    <td>
                        <a href="{{ route('employees.edit', $emp->id) }}" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> ترمیم
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
