@extends('core::admin.master')

@section('meta_title', __('media::media.index.page_title'))

@section('content-header')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard.index') }}">{{ trans('dashboard::message.index.breadcrumb') }}</a></li>
                        <li class="breadcrumb-item active">{{ trans('media::media.index.page_title') }}</li>
                    </ol>
                </div>
                <h4 class="page-title">{{ __('media::media.index.page_title') }}</h4>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card-box">
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <form id="upload-form" enctype="multipart/form-data" action="{{route('media.admin.media.store')}}" method="POST">
                            @csrf
                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ol>
                                        @foreach ($errors['image'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ol>
                                </div>
                            @endif
                            <div class="card-body">

                                @include('media::admin.layouts.upload-image')

                                <div class="row">

                                    @include('media::admin.layouts.view-system')

                                    @include('media::admin.layouts.search-model')

                                    @include('media::admin.layouts.search-type')

                                    @include('media::admin.layouts.sort')

                                    @include('media::admin.layouts.search-day')

                                    @include('media::admin.layouts.search-name')

                                </div>
                                <input type="hidden" value="{{request()->mode}}" name="mode" id="mode">
                                <div class="row viewImage">
                                    @if (request()->mode == 'list')
                                        @include('media::admin.layouts.listview')
                                    @else
                                        @include('media::admin.layouts.gridview')
                                    @endif
                                </div>

                                <br>
                                {{ $medias->appends(Request::all())->render() }}
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
    @include('media::admin.modals.edit-modal')

    @include('media::admin.modals.delete-modal')
@stop

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/media/admin/css/custom.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('vendor/media/admin/js/custom.js') }}"></script>
@endpush

{{--@extends('core::admin.master')--}}

{{--@assetadd('my-script', 'vendor/media/js/admin/custom.js', ['jquery'])--}}

{{--@assetadd('my-script', 'vendor/media/css/admin/custom.css')--}}



{{--@section('page_title', __('media::media.index.page_title'))--}}

{{--@section('page_subtitle', __('media::media.index.page_subtitle'))--}}

{{--@section('content')--}}
{{--    <div class="card mb-4">--}}
{{--        <div class="card-header">--}}
{{--            <div class="d-flex justify-content-between align-items-center">--}}
{{--                <div>--}}
{{--                    <h4 class="fs-17 font-weight-600 mb-2">--}}
{{--                        {{ __('media::media.index.page_title') }}--}}
{{--                        <a href="#" id="addFile" style="color: blue">Add file</a>--}}
{{--                    </h4>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <form id="upload-form" enctype="multipart/form-data" action="{{route('media.admin.media.store')}}" method="POST">--}}
{{--            @csrf--}}
{{--            @if (count($errors) > 0)--}}
{{--                <div class="alert alert-danger">--}}
{{--                    <ol>--}}
{{--                        @foreach ($errors['image'] as $error)--}}
{{--                            <li>{{ $error }}</li>--}}
{{--                        @endforeach--}}
{{--                    </ol>--}}
{{--                </div>--}}
{{--            @endif--}}
{{--            <div class="card-body">--}}

{{--                @include('media::admin.layouts.upload-image')--}}

{{--                <div class="row">--}}

{{--                    @include('media::admin.layouts.view-system')--}}

{{--                    @include('media::admin.layouts.search-model')--}}

{{--                    @include('media::admin.layouts.search-type')--}}

{{--                    @include('media::admin.layouts.sort')--}}

{{--                    @include('media::admin.layouts.search-day')--}}

{{--                    @include('media::admin.layouts.search-name')--}}

{{--                </div>--}}
{{--                <input type="hidden" value="{{request()->mode}}" name="mode" id="mode">--}}
{{--                <div class="row viewImage">--}}
{{--                    @if (request()->mode == 'list')--}}
{{--                        @include('media::admin.layouts.listview')--}}
{{--                    @else--}}
{{--                        @include('media::admin.layouts.gridview')--}}
{{--                    @endif--}}
{{--                </div>--}}

{{--                <br>--}}
{{--                {{ $medias->appends(Request::all())->render() }}--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </div>--}}

{{--    @include('media::admin.modals.edit-modal')--}}

{{--    @include('media::admin.modals.delete-modal')--}}
{{--@stop--}}
