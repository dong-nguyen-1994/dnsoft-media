@if ($type == 'image')
    <div id="holder" style="margin-top:15px;max-height:100px; margin-bottom: 10px"></div>
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
    <input id="thumbnail" class="form-control" type="text" name="{{ $name }}">
</div>

@push('scripts')
    <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
    <script>
        $(document).ready(function () {
            $('#lfm').filemanager('{{ $type }}');
        })
    </script>
@endpush
