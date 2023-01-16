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
});