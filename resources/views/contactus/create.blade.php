@extends('layouts.app')

@section('title', 'Add Contact Message')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-envelope"></i> Add New Contact 
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

                    <form action="{{ route('contactus.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">
                                        <i class="fa fa-user"></i> Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter full name" required>
                                    <span class="help-block">Please enter the contact person's full name</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">
                                        <i class="fa fa-envelope"></i> Email <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter email address" required>
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
                                    <input type="text" name="subject" id="subject" class="form-control" placeholder="Enter message subject" required>
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
                                    <textarea name="message" id="message" class="form-control" rows="6" placeholder="Enter your message here..." required></textarea>
                                    <span class="help-block">Please enter the detailed message content</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="fa fa-save"></i> Save Message
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
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.panel-primary > .panel-heading {
    background-color: #337ab7;
    border-color: #337ab7;
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
</style>
@endsection