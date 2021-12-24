@if ($type == 'image')
@if ($item && $name == 'seometa[og_image]')
@php($image = $item->seometa ? $item->seometa['og_image'] : null)
@elseif($item && $name == 'seometa[twitter_image]')
@php($image = $item->seometa ? $item->seometa['twitter_image'] : null)
@else
@php($image = object_get($item, $name))
@endif
@if ($image || isset($value))
<div class="single-holder-{{ $id }}" @if(isset($isNotStyle)) style="margin-top:15px;max-height:100px; margin-bottom: 10px" @endif></div>
<div class="single-holder" @if(isset($isNotStyle)) style="margin-top:15px;max-height:100px; margin-bottom: 10px" @endif>
    <span class="close" data-single-image="{{ env('APP_URL'). '/'.$image ?? $value }}">&times;</span>
    <img src="{{ env('APP_URL'). '/'. $image ?? $value }}" style="height: 5rem;" class="mr-2">
</div>
@else
<div class="single-holder-{{ $id }}" @if(isset($isNotStyle)) style="margin-top:15px;max-height:100px; margin-bottom: 10px" @endif></div>
@endif
@endif

<div class="input-group">
    @if (isset($label))
    <label for="{{ $name }}" class="col-12 font-weight-600" style="margin-left: -12px">{{ $label }}</label>
    @endif
    <span class="input-group-btn">
        <a data-type="single" data-single-input="single-thumbnail-{{ $id }}" name="{{ $name }}" data-single-preview="single-holder-{{ $id }}" class="btn btn-primary single-lfm">
            <i class="fa fa-picture-o"></i> {{ __('media::media.choose')}}
        </a>
    </span>
    <input id="single-thumbnail-{{ $id }}" placeholder="{{ $placeholder ?? $label }}" class="form-control" type="text" value="{{ $image ? env('APP_URL'). '/'.$image : (isset($value) ? env('APP_URL'). '/'.$value : '') }}" name="{{ $name }}">
</div>

@push('scripts')
<script src="{{ asset('vendor/dnsoft/admin/js/scripts/stand-alone-button.js') }}"></script>
<script>
    $(document).ready(function() {
        $('.single-lfm').filemanager('image');

        $('body').on('click', '.single-holder .close', function(e) {
            let images = $('#' + 'single-thumbnail-{{ $id }}').val();
            let srcImage = $(this).data('single-image');
            if (srcImage == images) {
                $('#' + 'single-thumbnail-{{ $id }}').val('');
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