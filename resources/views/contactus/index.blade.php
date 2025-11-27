@extends('layouts.app')

@section('title', 'Contact Messages')

@section('content')
<div class="container">
    <div class="mb-3">
        <a href="{{ route('contactus.create') }}" class="btn btn-success pull-right">
            <i class="fa fa-plus"></i> Add New Contact
        </a>
    </div>

    <center><legend> <h3>Contacts </h3></legend></center>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="contactus_table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    {{ $records->links() }}
</div>

<script>
$(document).ready(function(){
    $('#contactus_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('contactus.datatable') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        },
        columns: [
            { data: 'id', orderable: true },
            { data: 'created_at', orderable: true },
            { data: 'name', orderable: true },
            { data: 'email', orderable: true },
            { data: 'subject', orderable: true },
            { data: 'message', orderable: false },
            { data: 'actions', orderable: false }
        ],
        order: [[0, 'desc']],
        pageLength: 25
    });
});
</script>
@endsection