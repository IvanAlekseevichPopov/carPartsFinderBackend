$(document).ready(function () {
    //draw images previews below "upload" button
    $("input[type=file].show-images").siblings('div.help-block').each(function (_, div) {
        var images = $(div).text().split('|');
        $(div).html('');

        for (let i = 0; i < images.length; i++) {
            const $img = $("<img class='admin-preview-image'>");
            $img.attr("src", images[i]);
            $(div).append($img);
        }
    });

    //showing files names for upload
    $('input[type=file]').on('change', function () {
        Array.from(this.files).forEach(file => {
            console.log(file);
            if(file != null) {
                $("label[for='"+$(this).attr('id')+"']").after('<br/><span>'+file.name+'</span>');
            } else {
                $("label[for='"+$(this).attr('id')+"']").next('span').remove();
            }
        });

    });

    //Add download button to fancybox
    $.fancybox.defaults.btnTpl.download = '<button id="download-image-to-storage" data-fancybox-dowload class="fancybox-button" title="Download image to internal storage and apply to part"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256c-35.2 0-64 28.8-64 64c0 35.2 28.8 64 64 64c35.2 0 64-28.8 64-64C288 284.8 259.2 256 224 256zM433.1 129.1l-83.9-83.9C341.1 37.06 328.8 32 316.1 32H64C28.65 32 0 60.65 0 96v320c0 35.35 28.65 64 64 64h320c35.35 0 64-28.65 64-64V163.9C448 151.2 442.9 138.9 433.1 129.1zM128 80h144V160H128V80zM400 416c0 8.836-7.164 16-16 16H64c-8.836 0-16-7.164-16-16V96c0-8.838 7.164-16 16-16h16v104c0 13.25 10.75 24 24 24h192C309.3 208 320 197.3 320 184V83.88l78.25 78.25C399.4 163.2 400 164.8 400 166.3V416z"/></svg></button>';
    $.fancybox.defaults.buttons = [
        'slideShow',
        'fullScreen',
        'thumbs',
        'download',
        'close'
    ];

    $('body').on('click', '#download-image-to-storage', function(e) {
        let imageAddress = $('.fancybox-slide--current .fancybox-image').attr('src');
        // TODO window.selectedPart is global state - it's dangerous, refactor it! second part in list_field_images_to_parts.html.twig
        if(window.selectedPart == null) {
            Swal.fire({'title': 'Part not selected', 'type': 'error', "text": "Part not selected. Error in JS code."});
            return;
        }
        $.post({
            url: '/admin/api/parts/' + window.selectedPart + '/images',
            data: {imageAddress: imageAddress},
            success: function (data) {
                Loader.close();
                $('img[src="'+imageAddress+'"]').remove();
                $.fancybox.close();
            },
            error: function (data) {
                Loader.close()
                if(data.status === 500) {
                    Swal.fire({'title': 'Internal server error', 'type': 'error', "text": "Something went wrong. Please try again later."});
                } else {
                    Swal.fire({'title': 'Unknown error', 'type': 'error', "text": "Something went wrong. Error code: " +  data.status});
                    alert('Unknown error, code: ' + data.status);
                }
            }
        });
        Loader.open();
    });

    window.selectedPart = null;
});