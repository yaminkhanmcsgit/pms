@extends('layouts.app')

@section('title', 'Edit Contact Message')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-edit"></i> Edit Contact 
                    </h3>
                </div>
                <div class="panel-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fa fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <i class="fa fa-exclamation-triangle"></i>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('contactus.update', $record->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        <i class="fa fa-user"></i> Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" class="form-control" value="{{ $record->name }}" placeholder="Enter full name" required>
                                    <span class="help-block">Please enter the contact person's full name</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fa fa-envelope"></i> Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" class="form-control" value="{{ $record->email }}" placeholder="Enter email address" required>
                                    <span class="help-block">Please enter a valid email address</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject">
                                        <i class="fa fa-tag"></i> Subject <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="subject" id="subject" class="form-control" value="{{ $record->subject }}" placeholder="Enter message subject" required>
                                    <span class="help-block">Please enter a brief subject for the message</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Empty column for balance -->
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">
                                        <i class="fa fa-comment"></i> Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="message" id="message" class="form-control" rows="6" placeholder="Enter your message here..." required>{{ $record->message }}</textarea>
                                    <span class="help-block">Please enter the detailed message content</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="fa fa-save"></i> Update Message
                                    </button>
                                    <a href="{{ route('contactus.index') }}" class="btn btn-default btn-lg">
                                        <i class="fa fa-times"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="panel-footer">
                    <small class="text-muted">
                        <i class="fa fa-info-circle"></i> Fields marked with <span class="text-danger">*</span> are required.
                        <span class="pull-right">
                            <strong>ID:</strong> {{ $record->id }} |
                            <strong>Created:</strong> {{ $record->created_at ? date('d-m-Y H:i', strtotime($record->created_at)) : 'N/A' }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.panel-warning > .panel-heading {
    background-color: #f0ad4e;
    border-color: #f0ad4e;
}
.form-group {
    float: left;
    width: 100%;
}
.help-block {
    font-size: 12px;
    color: #666;
}
.btn-lg {
    padding: 10px 20px;
    font-size: 16px;
}
.text-center .btn {
    margin: 0 10px;
}
.pull-right {
    float: right;
}
</style>
@endsection