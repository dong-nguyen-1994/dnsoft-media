@php($value = $item && method_exists($item, 'getGalleryData') ? $item->getGalleryData() : null)
<div
  id="{{ $id }}"
  data-show="{{ isset($show_button) && $show_button }}"
  data-gallery="{{ $value ? json_encode($value['medias']) : null }}"
>
</div>
<input type="hidden" name="{{ $name }}" id="gallery-{{ $id }}" value="{{ $value ? json_encode($value['ids']) : null }}">
@push('styles')
  <link rel="stylesheet" type="text/css" href="{{ asset('vendor/build/assets/app.css') }}">
@endpush

@push('scripts')
  <script src="{{ asset('vendor/build/assets/app.js') }}"></script>
@endpush
