@extends('layouts.app')

@section('title', 'Edit News')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-edit"></i> Edit News
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

                    <form action="{{ route('news.update', $record->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="created_at">
                                        <i class="fa fa-calendar"></i> Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" name="created_at" id="created_at" class="form-control" value="{{ $record->created_at ? date('Y-m-d', strtotime($record->created_at)) : '' }}" required>
                                    <span class="help-block">Select the publication date</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="colorder">
                                        <i class="fa fa-columns"></i> Column Count <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="colorder" id="colorder" class="form-control" value="{{ $record->colorder }}" min="1" max="12" required>
                                    <span class="help-block">Number of columns for layout (1-12)</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="title">
                                        <i class="fa fa-header"></i> Title <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control" value="{{ $record->title }}" placeholder="Enter news title here" required>
                                    <span class="help-block">Enter a compelling title for the news article</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="detail">
                                        <i class="fa fa-file-text"></i> Detail <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="detail" id="detail" class="form-control" rows="10" placeholder="Enter news content here...">{{ $record->detail }}</textarea>
                                    <span class="help-block">Write the detailed news content</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image1">
                                        <i class="fa fa-image"></i> Image 1
                                    </label>
                                    <input type="file" name="image1" id="image1" class="form-control" accept="image/*">
                                    <span class="help-block">
                                        @if($record->image1)
                                            Current: <a href="{{ $record->image1 }}" target="_blank">View Image</a>
                                        @else
                                            No image uploaded
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image2">
                                        <i class="fa fa-image"></i> Image 2
                                    </label>
                                    <input type="file" name="image2" id="image2" class="form-control" accept="image/*">
                                    <span class="help-block">
                                        @if($record->image2)
                                            Current: <a href="{{ $record->image2 }}" target="_blank">View Image</a>
                                        @else
                                            No image uploaded
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image3">
                                        <i class="fa fa-image"></i> Image 3
                                    </label>
                                    <input type="file" name="image3" id="image3" class="form-control" accept="image/*">
                                    <span class="help-block">
                                        @if($record->image3)
                                            Current: <a href="{{ $record->image3 }}" target="_blank">View Image</a>
                                        @else
                                            No image uploaded
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image4">
                                        <i class="fa fa-image"></i> Image 4
                                    </label>
                                    <input type="file" name="image4" id="image4" class="form-control" accept="image/*">
                                    <span class="help-block">
                                        @if($record->image4)
                                            Current: <a href="{{ $record->image4 }}" target="_blank">View Image</a>
                                        @else
                                            No image uploaded
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-warning btn-lg">
                                        <i class="fa fa-save"></i> Update News
                                    </button>
                                    <a href="{{ route('news.index') }}" class="btn btn-default btn-lg">
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
                        Supported image formats: JPG, PNG, GIF. Max size: 2MB each.
                        <span class="pull-right">
                            <strong>ID:</strong> {{ $record->id }} |
                            <strong>Status:</strong> {{ $record->status == 1 ? 'Active' : 'Inactive' }}
                        </span>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

<script src="https://cdn.ckeditor.com/ckeditor5/35.4.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#detail'))
        .catch(error => {
            console.error(error);
        });
</script>
@endsection