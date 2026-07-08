@extends('layouts.app')

@section('title', 'MIS Changes')

@section('content')
<div class="container" dir="rtl">
    <div class="mb-3">
        <a href="{{ route('mis_changes.create') }}" class="btn btn-success pull-right">
            <i class="fa fa-plus"></i> نیا ریکارڈ شامل کریں
        </a>
    </div>

    <center><legend><h3>MIS تبدیلیاں</h3></legend></center>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
        <table id="mis_changes_table" class="table table-bordered table-striped" style="width: 100%;">
            <thead>
                <tr>
                    <th>سیریل نمبر</th>
                    <th>وقت تبدیلی</th>
                    <th>ضلع</th>
                    <th>تحصیل</th>
                    <th>موضع</th>
                    <th>فیملی آئی ڈی</th>
                    <th>تفصیل</th>
                    <th>صارف</th>
                    <th>سکرین شاٹ پہلے</th>
                    <th>سکرین شاٹ بعد</th>
                    <th>کارروائیاں</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
function deleteMisChange(id) {
    Swal.fire({
        title: 'کیا آپ واقعی حذف کرنا چاہتے ہیں؟',
        text: "یہ عمل واپس نہیں ہو سکتا!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ہاں، حذف کریں!',
        cancelButtonText: 'بند کریں'
    }).then((result) => {
        if (result.isConfirmed) {
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ url("mis-changes") }}/' + id;
            var csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '{{ csrf_token() }}';
            form.appendChild(csrf);
            var method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

$(document).ready(function(){
    $('#mis_changes_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('mis_changes.datatable') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'sno', orderable: false },
            { data: 'change_time', orderable: true },
            { data: 'district_name', orderable: true },
            { data: 'tehsil_name', orderable: true },
            { data: 'moza_name', orderable: true },
            { data: 'family_id', orderable: false },
            { data: 'description', orderable: false },
            { data: 'user_name', orderable: true },
            {
                data: 'screenshot_before',
                orderable: false,
                render: function(data, type, row) {
                    if (data) {
                        return '<a href="' + data + '" target="_blank"><img src="' + data + '" style="max-height:60px; max-width:80px;" class="img-thumbnail"></a>';
                    }
                    return '<span class="text-muted">No image</span>';
                }
            },
            {
                data: 'screenshot_after',
                orderable: false,
                render: function(data, type, row) {
                    if (data) {
                        return '<a href="' + data + '" target="_blank"><img src="' + data + '" style="max-height:60px; max-width:80px;" class="img-thumbnail"></a>';
                    }
                    return '<span class="text-muted">No image</span>';
                }
            },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection
