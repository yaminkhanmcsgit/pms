@extends('layouts.app')

@section('title', 'Latest News')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('news.create') }}" class="btn btn-success pull-right">
            <i class="fa fa-plus"></i> Add News
        </a>
    </div>

    <center><legend> <h3>Latest News</h3></legend></center>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="news_table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Date</th>
                    <th>Title</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>

    {{ $records->links() }}
</div>

<style>
#news_table td:nth-child(3) {
    word-wrap: break-word;
    white-space: normal;
    max-width: 300px;
}
</style>

<script>
$(document).ready(function(){
    $('#news_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('news.datatable') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'sn', orderable: false },
            { data: 'created_at', orderable: true },
            { data: 'title', orderable: true },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection