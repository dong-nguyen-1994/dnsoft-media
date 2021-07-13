@if ($type == 'image')
    @php($gallery = object_get($item, $name))
        @if ($gallery && is_string($gallery))
            <div id="holder" style="margin-top:15px;max-height:100px; margin-bottom: 10px">
                <span class="close" data-image="{{ $gallery }}">&times;</span>
                <img src="{{ $gallery }}" style="height: 5rem;" class="mr-2">
            </div>
        @else
            @if ($gallery && count($gallery) > 0)
                @foreach($gallery as $image)
                    <div id="holder" style="margin-top:15px;max-height:100px; margin-bottom: 10px">
                        <span class="close" data-image="{{ $image }}">&times;</span>
                        <img src="{{ $image }}" style="height: 5rem;" class="mr-2">
                    </div>
                @endforeach
            @else
                <div id="holder" style="margin-top:15px;max-height:100px; margin-bottom: 10px"></div>
            @endif
        @endif
@endif
<div class="input-group">
    @if (isset($label))
        <label for="{{ $name }}" class="col-12 font-weight-600 text-left">{{ $label }}</label>
    @endif
   <span class="input-group-btn">
     <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
       <i class="fa fa-picture-o"></i> Choose
     </a>
   </span>
    <input id="thumbnail" class="form-control" type="text" value="{{ is_string($gallery) ? $gallery : ($gallery ? implode(',', $gallery) : '') }}" name="{{ $name }}">
</div>

@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script>
        $(document).ready(function () {
            $('#lfm').filemanager('{{ $type }}');

            let closeBtns = document.querySelectorAll('#holder .close');
            let images = $('#thumbnail').val();
            let arrImage = images.split(',');

            for (let i = 0, l = closeBtns.length; i < l; i++) {
                closeBtns[i].addEventListener('click', function() {
                    let imgWrap = this.parentElement;
                    imgWrap.parentElement.removeChild(imgWrap);
                    let srcImage = $(this).data('image');
                    const index = arrImage.indexOf(srcImage);
                    if (index > -1) {
                        arrImage.splice(index, 1);
                        $('#thumbnail').val(arrImage.join(','));
                        // $('#hiddenValueGallery').val(arrImage.join(','));
                    }
                });
            }
        });
    </script>
@endpush

@push('styles')
    <style>
        #holder {
            position: relative;
            display: inline-block;
            font-size: 0;
        }
        #holder .close {
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
        #holder .close {
            opacity: 1;
        }
    </style>
@endpush
