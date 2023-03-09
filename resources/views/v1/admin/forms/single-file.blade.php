@php($image = object_get($item, $name))
@if ($image)
    <?php
    $imageName = explode('/', $image);
    $imageName = $imageName[count($imageName) - 1];
    ?>
    <div id="{{ $idHolder }}" style="display: flex; flex-flow: row wrap;">
        <span class="form-group image-item-{{ $item->id }}">
            <img class="img-thumbs" src="{{ $image }}">
            <div data-key="{{ $item->id }}" data-name="{{ $imageName }}" class="remove-item">Remove file</div>
        </span>
    </div>
@endif

@if (!$image )
    <div id="{{ $idHolder }}" style="display: flex; flex-flow: row wrap;"></div>
@endif

<div class="form-group">
    @if (isset($label))
        <label for="{{ $name }}">{{ $label }}</label>
    @endif
    <div class="input-group-btn">
        <a data-input="files_{{ $files }}" data-preview="{{ $idHolder }}" style="cursor: pointer; color: #1abc9c" id="{{ $name }}">
            <i class="fa fa-picture-o"></i> Chọn File
        </a>    
        <input id="files_{{ $files }}" class="form-control" value="{{ isset($imageName) ? $imageName : '' }}" type="hidden" name="{{ $name }}">
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
                let images = $('#files_{{ $files }}').val();
                let arrImageName = images.split(',');
                const index = arrImageName.indexOf(name.toString());
                if (index > -1) {
                    arrImageName.splice(index, 1);
                    $('#files_{{ $files }}').val(arrImageName.join(','));
                }
            });
        });

        (function(id, type, options) {
            let button = document.getElementById(id);
            button.addEventListener('click', function () {
                const route_prefix = (options && options.prefix) ? options.prefix : '/laravel-filemanager';
                const files = button.getAttribute('data-input');
                const target_input = document.getElementById(files);
                const target_preview = document.getElementById(button.getAttribute('data-preview'));

                window.open(route_prefix + '?type=' + options.type || 'file', 'FileManager', 'width=1390,height=650');
                window.SetUrl = function (items) {
                    items = [items[0]]
                    const file_path = items.map(function (item) {
                        return item.name;
                    });
                    const arr_file_name = $('#files_{{ $files }}').val().split(',');
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

                            // Remove previous image
                            const previouseElement = parent.previousElementSibling;
                            if (previouseElement) {
                                const divElement = previouseElement.querySelector('div');
                                const fileName = divElement.getAttribute('data-name');
                                previouseElement.remove();

                                // Remove data image
                                const files = $('#files_{{ $files }}').val();
                                let imagesName = files.split(',');
                                const index = imagesName.indexOf(fileName.toString());
                                if (index > -1) {
                                    imagesName.splice(index, 1);
                                    $('#files_{{ $files }}').val(imagesName.join(','));
                                }
                            }
                        }
                    });

                    // trigger change event
                    target_preview.dispatchEvent(new Event('change'));
                };
            });
        })('{{ $name }}', 'image', { prefix: '/admin/file-manager', type: 'file' });

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
