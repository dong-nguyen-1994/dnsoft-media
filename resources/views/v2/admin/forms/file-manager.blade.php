@php($gallery = object_get($item, $name))

@if ($gallery && count($gallery) > 0)
    <div id="holder">
        @php($arrGallery = array())
        @foreach($gallery as $image)
            <span class="form-group image-item-{{ $image['key'] }}">
                <img class="img-thumbs" src="{{ $image['url'] }}">
                <div data-key="{{ $image['key'] }}" data-name="{{ $image['name'] }}" class="remove-item">Remove file</div>
            </span>
            @php(array_push($arrGallery, $image['name']))
        @endforeach
    </div>
@endif

@if (!$gallery )
    <div id="holder"></div>
@endif

<div class="form-group">
    @if (isset($label))
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
   <div class="input-group-btn">
     <a data-input="files" data-preview="holder" style="cursor: pointer; color: #1abc9c" id="lfm">
       <i class="fa fa-picture-o"></i> Chọn File
     </a>
     <input id="files" class="form-control" value="{{ $gallery ? implode(',', $arrGallery) : '' }}" type="hidden" name="{{ $name }}">
   </div>
</div>

@push('scripts')
    <script>
        $(document).ready(function () {

            $('body').on('click', '.remove-item', function(e) {
                const key = $(this).data('key');
                let selector = '.image-item-' + key;
                $(selector).remove();
                const name = $(this).data('name');
                let images = $('#files').val();
                let arrImageName = images.split(',');
                const index = arrImageName.indexOf(name.toString());
                if (index > -1) {
                    arrImageName.splice(index, 1);
                    $('#files').val(arrImageName.join(','));
                }
            });

        });


        const lfm = function(id, type, options) {
            let button = document.getElementById(id);

            button.addEventListener('click', function () {
                const route_prefix = (options && options.prefix) ? options.prefix : '/laravel-filemanager';
                const files = button.getAttribute('data-input');
                const target_input = document.getElementById(files);
                const target_preview = document.getElementById(button.getAttribute('data-preview'));

                window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager', 'width=1390,height=650');
                window.SetUrl = function (items) {
                    const file_path = items.map(function (item) {
                        return item.name;
                    });
                    const arr_file_name = $('#files').val().split(',');
                    arr_file_name.forEach(function (itemFile) {
                        if (!file_path.includes(itemFile)) {
                            file_path.push(itemFile);
                        }
                    })
                    // set the value of the desired input to image url
                    target_input.value = file_path.join(',');

                    target_input.dispatchEvent(new Event('change'));

                    // clear previous preview
                    target_preview.innerHtml = '';

                    // set or change the preview image src
                    items.forEach(function (item) {
                        if (!arr_file_name.includes(item.name)) {
                            const key = randomKey(20);
                            const parent = document.createElement('span')
                            parent.setAttribute('class', `form-group image-item-${key}`)

                            let img = document.createElement('img')
                            img.setAttribute('class', 'img-thumbs')
                            img.setAttribute('src', item.thumb_url)
                            parent.appendChild(img);

                            const pElement = document.createElement('div')
                            pElement.setAttribute('data-key', key)
                            pElement.setAttribute('data-name', item.name)
                            pElement.setAttribute('class', `remove-item`)
                            pElement.innerHTML = 'Remove file'
                            parent.appendChild(pElement);

                            target_preview.appendChild(parent);
                        }
                    });

                    // trigger change event
                    target_preview.dispatchEvent(new Event('change'));
                };
            });
        };

        lfm('lfm', 'image', { prefix: '/admin/file-manager', type: 'file' });

        function randomKey(length) {
            let result = '';
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            const charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }
            return result;
        }
    </script>
@endpush

@push('styles')
    <style>
        #holder {
            display: flex;
            flex-flow: row wrap;
            margin-top: 25px
        }

        .img-thumbs {
            height: 5rem;
            margin-right: 0.7rem;
            border-radius: 10px;
        }

        .remove-item {
            font-size: 12px;
            margin-top: 3px;
            font-weight: 600;
            cursor: pointer;
            color: #1abc9c
        }
    </style>
@endpush
