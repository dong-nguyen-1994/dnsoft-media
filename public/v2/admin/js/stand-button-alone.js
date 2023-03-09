(function ($) {
    $.fn.filemanager = function (type, options) {
        type = type || 'file';
        let _this = this;
        this.on('click', function (e) {
            const typeFile = _this.data('type');
            if (typeFile === 'single') {
                let route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
                let target_input = $('#' + $(this).data('single-input'));
                let target_preview = $('.' + $(this).data('single-preview'));

                window.open(route_prefix + '?type=' + type, 'FileManager', 'width=900,height=600');
                window.SetUrl = function (items) {
                    let item = items[0];
                    target_input.val(item.url).trigger('change');
                    // clear previous preview
                    target_preview.html('');
                    const html = '<div class="single-holder" style="margin-top: 15px; max-height: 100px; margin-bottom: 10px;">' +
                        '<span data-single-image="' + item.url + '" class="close">&times;</span>' +
                        '<img class="mr-2" style="height: 5rem;" src="' + item.thumb_url + '">' +
                        '</div>'
                    target_preview.append(html)

                    // trigger change event
                    target_preview.trigger('change');
                };
                return false;
            } else {
                let route_prefix = (options && options.prefix) ? options.prefix : '/filemanager';
                let target_input = $('#' + $(this).data('input'));
                let target_preview = $('.' + $(this).data('preview'));
                window.open(route_prefix + '?type=' + type, 'FileManager', 'width=900,height=600');
                window.SetUrl = function (items) {
                    let file_path = items.map(function (item) {
                        return item.url;
                    }).join(',');

                    // set the value of the desired input to image url
                    let comma = ',';
                    if (!$('#thumbnail').val()) {
                        comma = '';
                    }

                    target_input.val($('#thumbnail').val() + comma + file_path).trigger('change');

                    // clear previous preview
                    //target_preview.html('');

                    // set or change the preview image src
                    items.forEach(function (item) {
                        const html = '<div class="holder" style="margin-top: 15px; max-height: 100px; margin-bottom: 10px;">' +
                            '<span data-image="' + item.url + '" class="close">&times;</span>' +
                            '<img class="mr-2" style="height: 5rem;" src="' + item.thumb_url + '">' +
                            '</div>'
                        $(html).insertAfter(target_preview.last())
                    });
                    // trigger change event
                    target_preview.trigger('change');
                };
                return false;
            }

        });
    }

})(jQuery);
