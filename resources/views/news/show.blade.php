@extends('layouts.app')

@section('title', 'View News')

@section('content')
@php
function getImageUrl($imagePath) {
    if (!$imagePath) return '';
    if (file_exists(base_path('../assets'))) {
        return url(str_replace(base_path('../assets'), 'assets', $imagePath));
    } else {
        return url(str_replace(base_path('assets'), 'assets', $imagePath));
    }
}
@endphp
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <i class="fa fa-eye"></i> View News Details
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h2>{{ $record->title }}</h2>
                            <hr>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fa fa-calendar"></i> Date:</strong> {{ $record->created_at ? date('d-m-Y', strtotime($record->created_at)) : 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fa fa-columns"></i> Column Count:</strong> {{ $record->colorder }}
                        </div>
                    </div>

                    <div class="row" style="margin-top: 20px;">
                        <div class="col-md-12">
                            <strong><i class="fa fa-file-text"></i> Content:</strong>
                            <div style="margin-top: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
                                {!! $record->detail !!}
                            </div>
                        </div>
                    </div>

                    @if($record->image1 || $record->image2 || $record->image3 || $record->image4)
                    <div class="row" style="margin-top: 30px;">
                        <div class="col-md-12">
                            <strong><i class="fa fa-images"></i> Images:</strong>
                            <div class="row" style="margin-top: 15px;">
                                @if($record->image1)
                                <div class="col-md-3">
                                    <div class="thumbnail">
                                        <img src="{{ getImageUrl($record->image1) }}" alt="Image 1" style="width: 100%; height: 150px; object-fit: cover;">
                                        <div class="caption text-center">
                                            <small>Image 1</small>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($record->image2)
                                <div class="col-md-3">
                                    <div class="thumbnail">
                                        <img src="{{ getImageUrl($record->image2) }}" alt="Image 2" style="width: 100%; height: 150px; object-fit: cover;">
                                        <div class="caption text-center">
                                            <small>Image 2</small>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($record->image3)
                                <div class="col-md-3">
                                    <div class="thumbnail">
                                        <img src="{{ getImageUrl($record->image3) }}" alt="Image 3" style="width: 100%; height: 150px; object-fit: cover;">
                                        <div class="caption text-center">
                                            <small>Image 3</small>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                @if($record->image4)
                                <div class="col-md-3">
                                    <div class="thumbnail">
                                        <img src="{{ getImageUrl($record->image4) }}" alt="Image 4" style="width: 100%; height: 150px; object-fit: cover;">
                                        <div class="caption text-center">
                                            <small>Image 4</small>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                   
                </div>
                <div class="panel-footer">
                    <div class="text-center">
                        <a href="{{ route('news.edit', $record->id) }}" class="btn btn-warning">
                            <i class="fa fa-edit"></i> Edit News
                        </a>
                        <a href="{{ route('news.index') }}" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.thumbnail {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 4px;
}
.thumbnail img {
    border-radius: 3px;
}
.well {
    background-color: #f5f5f5;
    border: 1px solid #e3e3e3;
    border-radius: 4px;
    padding: 15px;
}
.panel-info > .panel-heading {
    background-color: #d9edf7;
    border-color: #bce8f1;
}
</style>
@endsection
