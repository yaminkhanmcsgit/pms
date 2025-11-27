
@extends('layouts.app')

@section('title', 'صارفین')

@section('content')
<div class="container" dir="rtl">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('operators.create') }}" class="btn btn-success">
            <i class="fa fa-plus"></i> صارف شامل کریں
        </a>
    </div>
    <center><legend> <h3>صارفین کی فہرست</h3></legend></center>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="thead-dark text-center">
                <tr>
                    <th>نمبر شمار</th>
                    <th>صارف نام</th>
                    <th>مکمل نام</th>
                    <th>ضلع</th>
                    <th>تحصیل</th>
                    <th>رول</th>
                    <th>تاریخ شمولیت</th>
                    <th class="no-print">عمل</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @foreach($operators as $op)
                <tr>
                    <td>{{ $op->id }}</td>
                    <td>{{ $op->username }}</td>
                    <td>{{ $op->full_name }}</td>
                    <td>{{ $op->district_name }}</td>
                    <td>{{ $op->tehsil_name }}</td>
                    <td>{{ $op->role_title }}</td>
                    <td>{{ $op->created_at ? date('d-m-Y', strtotime($op->created_at)) : '' }}</td>
                    <td>
                        <a href="{{ route('operators.edit', $op->id) }}" class="btn btn-sm btn-warning">ترمیم</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
