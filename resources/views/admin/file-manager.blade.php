@extends('core::admin.master')

@section('meta_title', __('media::media.index.page_title'))

@section('content-header')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">Home</a></li>
                        <li class="breadcrumb-item active">{{ __('media::media.index.page_title') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('media::media.index.page_title') }}</h4>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="main-content">
        <iframe src="/admin/file-manager?type=image" style="width: 100%; height: 996px; overflow: hidden; border: none;"></iframe>
    </div>
@stop
