(function ($) {
    'use strict';

    $(document).ready(function () {
        // Inicializar color pickers
        $('.mlr-color-picker').wpColorPicker();

        // Media uploader para el logo
        $('.mlr-upload-logo').on('click', function (e) {
            e.preventDefault();

            var mediaUploader = wp.media({
                title: 'Seleccionar logo',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            });

            mediaUploader.on('select', function () {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#mlr_logo_url').val(attachment.url);

                // Actualizar preview
                var preview = $('.mlr-logo-preview');
                if (preview.length) {
                    preview.find('img').attr('src', attachment.url);
                } else {
                    $('#mlr_logo_url').after(
                        '<div class="mlr-logo-preview"><img src="' + attachment.url + '" alt="Logo preview" style="max-width:150px;margin-top:10px;"></div>'
                    );
                }
            });

            mediaUploader.open();
        });
    });
})(jQuery);
