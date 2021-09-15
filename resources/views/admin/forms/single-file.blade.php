@if ($type == 'image')
    @php($image = object_get($item, $name))

    @if ($image)
        <div class="single-holder-{{ $id }}" style="margin-top:15px;max-height:100px; margin-bottom: 10px">
            <span class="close" data-single-image="{{ $image }}">&times;</span>
            <img src="{{ $image }}" style="height: 5rem;" class="mr-2">
        </div>
    @else
        <div class="single-holder-{{ $id }}" style="margin-top:15px;max-height:100px; margin-bottom: 10px"></div>
    @endif
@endif
<div class="input-group">
    @if (isset($label))
        <label for="{{ $name }}" class="col-12 font-weight-600 text-left">{{ $label }}</label>
    @endif
    <span class="input-group-btn">
     <a data-type="single" data-single-input="single-thumbnail-{{$id}}" name="{{ $name }}" data-single-preview="single-holder-{{ $id }}" class="btn btn-primary single-lfm">
       <i class="fa fa-picture-o"></i> Choose
     </a>
   </span>
    <input id="single-thumbnail-{{$id}}" class="form-control" type="text" value="{{ $image }}" name="{{ $name }}">
</div>

@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
{{--    <script src="{{ asset('assets/js/file-manager.js') }}"></script>--}}
    <script>

        $(document).ready(function () {
            $('.single-lfm').filemanager('image');

            $('body').on('click', '.single-holder .close', function(e) {
                let images = $('#' + 'single-thumbnail-{{$id}}').val();
                let srcImage = $(this).data('single-image');
                if (srcImage == images) {
                    $('#' + 'single-thumbnail-{{$id}}').val('');
                }

                let imgWrap = this.parentElement;
                if (imgWrap.parentElement) {
                    imgWrap.parentElement.removeChild(imgWrap);
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        .single-holder {
            position: relative;
            display: inline-block;
            font-size: 0;
        }
        .single-holder .close {
            position: absolute;
            top: -10px;
            right: 2px;
            z-index: 100;
            background-color: #FFF;
            padding: 5px 2px 2px;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            opacity: .2;
            text-align: center;
            font-size: 22px;
            line-height: 10px;
            border-radius: 50%;
        }
        .single-holder .close {
            opacity: 1;
        }
    </style>
@endpush
