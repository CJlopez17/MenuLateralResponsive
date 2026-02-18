(function ($) {
    'use strict';

    /**
     * Media uploader para el campo de icono en cada item del menú.
     * Funciona tanto con items existentes como con items nuevos agregados dinámicamente.
     */
    $(document).on('click', '.mlr-icon-upload-btn', function (e) {
        e.preventDefault();

        var btn = $(this);
        var targetId = btn.data('target');
        var input = $('#' + targetId);
        var removeBtn = btn.siblings('.mlr-icon-remove-btn');
        var previewId = 'mlr-icon-preview-' + targetId.replace('mlr-icon-', '');
        var preview = $('#' + previewId);

        var mediaUploader = wp.media({
            title: mlrMenuIcon.title,
            button: { text: mlrMenuIcon.button },
            multiple: false,
            library: { type: ['image/svg+xml'] }
        });

        mediaUploader.on('select', function () {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            var url = attachment.url || '';
            // Validar que sea un archivo SVG
            if (url && !url.toLowerCase().match(/\.svg(\?.*)?$/)) {
                alert(mlrMenuIcon.svgOnly || 'Solo se permiten archivos SVG.');
                return;
            }
            input.val(url);
            removeBtn.show();

            // Actualizar preview
            if (preview.length) {
                preview.html(
                    '<img src="' + url + '" alt="" style="max-width:40px;max-height:40px;margin-top:6px;border:1px solid #ddd;border-radius:4px;padding:3px;background:#f9f9f9;">'
                );
            }
        });

        mediaUploader.open();
    });

    // Botón de quitar icono
    $(document).on('click', '.mlr-icon-remove-btn', function (e) {
        e.preventDefault();

        var btn = $(this);
        var targetId = btn.data('target');
        var input = $('#' + targetId);
        var previewId = 'mlr-icon-preview-' + targetId.replace('mlr-icon-', '');
        var preview = $('#' + previewId);

        input.val('');
        btn.hide();
        preview.html('');
    });

})(jQuery);
