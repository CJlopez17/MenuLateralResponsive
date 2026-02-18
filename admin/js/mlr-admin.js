(function ($) {
    'use strict';

    var data = window.mlrAdminData || {};
    var menuData = data.menuData || { top_links: [], cards: [] };
    var icons = data.icons || [];
    var i18n = data.i18n || {};

    var MLRAdmin = {

        init: function () {
            this.renderTopLinks();
            this.renderCards();
            this.bindGlobalEvents();
            // Init color pickers on appearance tab
            $('.mlr-color-picker').wpColorPicker();
        },

        bindGlobalEvents: function () {
            var self = this;

            // Top links
            $(document).on('click', '#mlr-add-top-link', function () {
                menuData.top_links.push({ title: '', url: '' });
                self.renderTopLinks();
            });

            $(document).on('click', '#mlr-save-top-links', function () {
                self.collectTopLinks();
                self.saveData('top_links', '#mlr-save-message');
            });

            $(document).on('click', '.mlr-remove-top-link', function () {
                var idx = $(this).data('index');
                menuData.top_links.splice(idx, 1);
                self.renderTopLinks();
            });

            $(document).on('click', '.mlr-move-top-link-up', function () {
                var idx = $(this).data('index');
                if (idx > 0) {
                    self.collectTopLinks();
                    var item = menuData.top_links.splice(idx, 1)[0];
                    menuData.top_links.splice(idx - 1, 0, item);
                    self.renderTopLinks();
                }
            });

            $(document).on('click', '.mlr-move-top-link-down', function () {
                var idx = $(this).data('index');
                if (idx < menuData.top_links.length - 1) {
                    self.collectTopLinks();
                    var item = menuData.top_links.splice(idx, 1)[0];
                    menuData.top_links.splice(idx + 1, 0, item);
                    self.renderTopLinks();
                }
            });

            // Cards
            $(document).on('click', '#mlr-add-card', function () {
                menuData.cards.push({
                    title: '',
                    url: '',
                    icon_type: 'builtin',
                    icon_name: 'grid',
                    icon_url: '',
                    categories: []
                });
                self.renderCards();
            });

            $(document).on('click', '#mlr-save-cards', function () {
                self.collectCards();
                self.saveData('cards', '#mlr-save-message-cards');
            });

            $(document).on('click', '.mlr-remove-card', function () {
                var idx = $(this).data('card');
                menuData.cards.splice(idx, 1);
                self.renderCards();
            });

            $(document).on('click', '.mlr-move-card-up', function () {
                var idx = $(this).data('card');
                if (idx > 0) {
                    self.collectCards();
                    var item = menuData.cards.splice(idx, 1)[0];
                    menuData.cards.splice(idx - 1, 0, item);
                    self.renderCards();
                }
            });

            $(document).on('click', '.mlr-move-card-down', function () {
                var idx = $(this).data('card');
                if (idx < menuData.cards.length - 1) {
                    self.collectCards();
                    var item = menuData.cards.splice(idx, 1)[0];
                    menuData.cards.splice(idx + 1, 0, item);
                    self.renderCards();
                }
            });

            // Toggle card expansion
            $(document).on('click', '.mlr-card-header-toggle', function () {
                var $card = $(this).closest('.mlr-admin-card');
                $card.toggleClass('mlr-card-collapsed');
            });

            // Add category
            $(document).on('click', '.mlr-add-category', function () {
                var ci = $(this).data('card');
                self.collectCards();
                menuData.cards[ci].categories.push({
                    title: '',
                    color: '#7B2D8E',
                    links: []
                });
                self.renderCards();
            });

            $(document).on('click', '.mlr-remove-category', function () {
                var ci = $(this).data('card');
                var cati = $(this).data('cat');
                self.collectCards();
                menuData.cards[ci].categories.splice(cati, 1);
                self.renderCards();
            });

            $(document).on('click', '.mlr-move-cat-up', function () {
                var ci = $(this).data('card');
                var cati = $(this).data('cat');
                if (cati > 0) {
                    self.collectCards();
                    var item = menuData.cards[ci].categories.splice(cati, 1)[0];
                    menuData.cards[ci].categories.splice(cati - 1, 0, item);
                    self.renderCards();
                }
            });

            $(document).on('click', '.mlr-move-cat-down', function () {
                var ci = $(this).data('card');
                var cati = $(this).data('cat');
                if (cati < menuData.cards[ci].categories.length - 1) {
                    self.collectCards();
                    var item = menuData.cards[ci].categories.splice(cati, 1)[0];
                    menuData.cards[ci].categories.splice(cati + 1, 0, item);
                    self.renderCards();
                }
            });

            // Add link
            $(document).on('click', '.mlr-add-link', function () {
                var ci = $(this).data('card');
                var cati = $(this).data('cat');
                self.collectCards();
                menuData.cards[ci].categories[cati].links.push({
                    title: '',
                    url: ''
                });
                self.renderCards();
            });

            $(document).on('click', '.mlr-remove-link', function () {
                var ci = $(this).data('card');
                var cati = $(this).data('cat');
                var li = $(this).data('link');
                self.collectCards();
                menuData.cards[ci].categories[cati].links.splice(li, 1);
                self.renderCards();
            });

            // Upload icon (solo SVG)
            $(document).on('click', '.mlr-upload-icon', function () {
                var ci = $(this).data('card');
                var uploader = wp.media({
                    title: i18n.selectSvg || 'Seleccionar archivo SVG',
                    button: { text: i18n.useSvg || 'Usar este SVG' },
                    multiple: false,
                    library: { type: ['image/svg+xml'] }
                });

                uploader.on('select', function () {
                    var attachment = uploader.state().get('selection').first().toJSON();
                    var url = attachment.url || '';
                    // Validar que sea un archivo SVG
                    if (url && !url.toLowerCase().match(/\.svg(\?.*)?$/)) {
                        alert(i18n.svgOnly || 'Solo se permiten archivos SVG.');
                        return;
                    }
                    self.collectCards();
                    menuData.cards[ci].icon_type = 'custom';
                    menuData.cards[ci].icon_url = url;
                    self.renderCards();
                });

                uploader.open();
            });

            $(document).on('click', '.mlr-remove-icon', function () {
                var ci = $(this).data('card');
                self.collectCards();
                menuData.cards[ci].icon_type = 'builtin';
                menuData.cards[ci].icon_url = '';
                self.renderCards();
            });

            $(document).on('change', '.mlr-icon-select', function () {
                var ci = $(this).data('card');
                self.collectCards();
                menuData.cards[ci].icon_name = $(this).val();
                menuData.cards[ci].icon_type = 'builtin';
                self.renderCards();
            });
        },

        // --- Top Links ---

        renderTopLinks: function () {
            var $list = $('#mlr-top-links-list');
            if (!$list.length) return;

            $list.empty();

            if (menuData.top_links.length === 0) {
                $list.append('<div class="mlr-empty-state">' + (i18n.noTopLinks || 'No hay links.') + '</div>');
                return;
            }

            for (var i = 0; i < menuData.top_links.length; i++) {
                var link = menuData.top_links[i];
                var html = '<div class="mlr-top-link-row" data-index="' + i + '">';
                html += '<span class="mlr-row-number">' + (i + 1) + '</span>';
                html += '<div class="mlr-row-fields">';
                html += '<input type="text" class="mlr-tl-title regular-text" value="' + this.esc(link.title) + '" placeholder="' + (i18n.title || 'Titulo') + '">';
                html += '<input type="url" class="mlr-tl-url regular-text" value="' + this.esc(link.url) + '" placeholder="' + (i18n.url || 'URL') + '">';
                html += '</div>';
                html += '<div class="mlr-row-actions">';
                html += '<button type="button" class="button mlr-move-top-link-up" data-index="' + i + '" title="' + (i18n.moveUp || 'Subir') + '"><span class="dashicons dashicons-arrow-up-alt2"></span></button>';
                html += '<button type="button" class="button mlr-move-top-link-down" data-index="' + i + '" title="' + (i18n.moveDown || 'Bajar') + '"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
                html += '<button type="button" class="button mlr-remove-top-link mlr-btn-danger" data-index="' + i + '" title="' + (i18n.remove || 'Eliminar') + '"><span class="dashicons dashicons-trash"></span></button>';
                html += '</div>';
                html += '</div>';
                $list.append(html);
            }
        },

        collectTopLinks: function () {
            var links = [];
            $('.mlr-top-link-row').each(function () {
                links.push({
                    title: $(this).find('.mlr-tl-title').val() || '',
                    url: $(this).find('.mlr-tl-url').val() || ''
                });
            });
            menuData.top_links = links;
        },

        // --- Cards ---

        renderCards: function () {
            var $list = $('#mlr-cards-list');
            if (!$list.length) return;

            $list.empty();

            if (menuData.cards.length === 0) {
                $list.append('<div class="mlr-empty-state">' + (i18n.noCards || 'No hay tarjetas.') + '</div>');
                return;
            }

            for (var ci = 0; ci < menuData.cards.length; ci++) {
                var card = menuData.cards[ci];
                var html = '<div class="mlr-admin-card" data-card="' + ci + '">';

                // Card header
                html += '<div class="mlr-admin-card-header">';
                html += '<button type="button" class="mlr-card-header-toggle">';
                html += '<span class="dashicons dashicons-arrow-down-alt2 mlr-toggle-icon"></span>';
                html += '<strong class="mlr-card-title-preview">' + (card.title || (i18n.addCard || 'Tarjeta') + ' ' + (ci + 1)) + '</strong>';
                html += '</button>';
                html += '<div class="mlr-card-header-actions">';
                html += '<button type="button" class="button mlr-move-card-up" data-card="' + ci + '" title="' + (i18n.moveUp || 'Subir') + '"><span class="dashicons dashicons-arrow-up-alt2"></span></button>';
                html += '<button type="button" class="button mlr-move-card-down" data-card="' + ci + '" title="' + (i18n.moveDown || 'Bajar') + '"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
                html += '<button type="button" class="button mlr-remove-card mlr-btn-danger" data-card="' + ci + '" title="' + (i18n.remove || 'Eliminar') + '"><span class="dashicons dashicons-trash"></span></button>';
                html += '</div>';
                html += '</div>';

                // Card body
                html += '<div class="mlr-admin-card-body">';

                // Title
                html += '<div class="mlr-field-group">';
                html += '<label>' + (i18n.title || 'Titulo') + '</label>';
                html += '<input type="text" class="mlr-card-title regular-text" value="' + this.esc(card.title) + '" placeholder="' + (i18n.title || 'Titulo') + '">';
                html += '</div>';

                // URL
                html += '<div class="mlr-field-group">';
                html += '<label>' + (i18n.url || 'URL') + ' <small style="color:#888;">(para tarjetas sin submenu)</small></label>';
                html += '<input type="url" class="mlr-card-url regular-text" value="' + this.esc(card.url || '') + '" placeholder="https://...">';
                html += '</div>';

                // Icon
                html += '<div class="mlr-field-group">';
                html += '<label>' + (i18n.icon || 'Icono') + '</label>';
                html += '<div class="mlr-icon-config">';

                // Builtin icon selector
                html += '<div class="mlr-icon-builtin">';
                html += '<select class="mlr-icon-select" data-card="' + ci + '">';
                for (var ii = 0; ii < icons.length; ii++) {
                    var sel = (card.icon_name === icons[ii]) ? ' selected' : '';
                    html += '<option value="' + icons[ii] + '"' + sel + '>' + icons[ii] + '</option>';
                }
                html += '</select>';
                html += '</div>';

                // Custom icon upload
                html += '<div class="mlr-icon-custom">';
                html += '<button type="button" class="button mlr-upload-icon" data-card="' + ci + '">';
                html += '<span class="dashicons dashicons-upload"></span> ' + (i18n.uploadSvg || 'Subir SVG');
                html += '</button>';

                if (card.icon_type === 'custom' && card.icon_url) {
                    html += '<span class="mlr-icon-preview">';
                    html += '<img src="' + this.esc(card.icon_url) + '" alt="icon">';
                    html += '<button type="button" class="mlr-remove-icon mlr-link-btn" data-card="' + ci + '">' + (i18n.removeIcon || 'Quitar') + '</button>';
                    html += '</span>';
                }

                html += '</div>';
                html += '</div>'; // .mlr-icon-config
                html += '</div>'; // .mlr-field-group

                // Categories
                html += '<div class="mlr-categories-section">';
                html += '<h4>' + (i18n.categories || 'Categorias') + ' <small>(submenu)</small></h4>';

                if (!card.categories || card.categories.length === 0) {
                    html += '<div class="mlr-empty-state mlr-empty-small">' + (i18n.noCategories || 'No hay categorias.') + '</div>';
                } else {
                    for (var cati = 0; cati < card.categories.length; cati++) {
                        html += this.renderCategory(ci, cati, card.categories[cati]);
                    }
                }

                html += '<button type="button" class="button button-secondary mlr-add-category" data-card="' + ci + '">';
                html += '<span class="dashicons dashicons-plus-alt2"></span> ' + (i18n.addCategory || 'Agregar categoria');
                html += '</button>';
                html += '</div>'; // .mlr-categories-section

                html += '</div>'; // .mlr-admin-card-body
                html += '</div>'; // .mlr-admin-card

                $list.append(html);
            }
        },

        renderCategory: function (ci, cati, cat) {
            var html = '<div class="mlr-admin-category" data-card="' + ci + '" data-cat="' + cati + '">';

            // Category header
            html += '<div class="mlr-category-header">';
            html += '<div class="mlr-category-fields">';
            html += '<input type="text" class="mlr-cat-title" value="' + this.esc(cat.title) + '" placeholder="' + (i18n.title || 'Titulo') + ' de categoria">';
            html += '<input type="text" class="mlr-cat-color mlr-mini-color" value="' + this.esc(cat.color || '#7B2D8E') + '" placeholder="' + (i18n.color || 'Color') + '">';
            html += '</div>';
            html += '<div class="mlr-category-actions">';
            html += '<button type="button" class="button mlr-move-cat-up" data-card="' + ci + '" data-cat="' + cati + '" title="' + (i18n.moveUp || 'Subir') + '"><span class="dashicons dashicons-arrow-up-alt2"></span></button>';
            html += '<button type="button" class="button mlr-move-cat-down" data-card="' + ci + '" data-cat="' + cati + '" title="' + (i18n.moveDown || 'Bajar') + '"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
            html += '<button type="button" class="button mlr-remove-category mlr-btn-danger" data-card="' + ci + '" data-cat="' + cati + '" title="' + (i18n.remove || 'Eliminar') + '"><span class="dashicons dashicons-trash"></span></button>';
            html += '</div>';
            html += '</div>';

            // Links
            html += '<div class="mlr-category-links">';
            html += '<label class="mlr-links-label">' + (i18n.links || 'Links') + ':</label>';

            if (!cat.links || cat.links.length === 0) {
                html += '<div class="mlr-empty-state mlr-empty-tiny">' + (i18n.noLinks || 'No hay links.') + '</div>';
            } else {
                for (var li = 0; li < cat.links.length; li++) {
                    var lnk = cat.links[li];
                    html += '<div class="mlr-link-row" data-card="' + ci + '" data-cat="' + cati + '" data-link="' + li + '">';
                    html += '<input type="text" class="mlr-link-title" value="' + this.esc(lnk.title) + '" placeholder="' + (i18n.title || 'Titulo') + '">';
                    html += '<input type="url" class="mlr-link-url" value="' + this.esc(lnk.url) + '" placeholder="' + (i18n.url || 'URL') + '">';
                    html += '<button type="button" class="button mlr-remove-link mlr-btn-danger-sm" data-card="' + ci + '" data-cat="' + cati + '" data-link="' + li + '" title="' + (i18n.remove || 'Eliminar') + '"><span class="dashicons dashicons-no-alt"></span></button>';
                    html += '</div>';
                }
            }

            html += '<button type="button" class="button button-small mlr-add-link" data-card="' + ci + '" data-cat="' + cati + '">';
            html += '<span class="dashicons dashicons-plus-alt2"></span> ' + (i18n.addLink || 'Agregar link');
            html += '</button>';
            html += '</div>'; // .mlr-category-links

            html += '</div>'; // .mlr-admin-category
            return html;
        },

        collectCards: function () {
            var cards = [];
            $('.mlr-admin-card').each(function () {
                var $card = $(this);
                var ci = $card.data('card');
                var existingCard = menuData.cards[ci] || {};

                var card = {
                    title: $card.find('.mlr-card-title').val() || '',
                    url: $card.find('.mlr-card-url').val() || '',
                    icon_type: existingCard.icon_type || 'builtin',
                    icon_name: $card.find('.mlr-icon-select').val() || 'grid',
                    icon_url: existingCard.icon_url || '',
                    categories: []
                };

                $card.find('.mlr-admin-category').each(function () {
                    var $cat = $(this);
                    var cat = {
                        title: $cat.find('.mlr-cat-title').val() || '',
                        color: $cat.find('.mlr-cat-color').val() || '#7B2D8E',
                        links: []
                    };

                    $cat.find('.mlr-link-row').each(function () {
                        var $link = $(this);
                        cat.links.push({
                            title: $link.find('.mlr-link-title').val() || '',
                            url: $link.find('.mlr-link-url').val() || ''
                        });
                    });

                    card.categories.push(cat);
                });

                cards.push(card);
            });
            menuData.cards = cards;
        },

        // --- Save ---

        saveData: function (type, msgSelector) {
            var $msg = $(msgSelector);
            var $btn;

            if (type === 'top_links') {
                this.collectTopLinks();
                $btn = $('#mlr-save-top-links');
            } else {
                this.collectCards();
                $btn = $('#mlr-save-cards');
            }

            $btn.prop('disabled', true).text(i18n.saving || 'Guardando...');
            $msg.removeClass('mlr-msg-success mlr-msg-error').text('');

            $.ajax({
                url: data.ajaxUrl,
                method: 'POST',
                data: {
                    action: 'mlr_save_menu_data',
                    nonce: data.nonce,
                    menu_data: JSON.stringify(menuData)
                },
                success: function (response) {
                    if (response.success) {
                        $msg.addClass('mlr-msg-success').text(i18n.saveSuccess || 'Guardado.');
                    } else {
                        $msg.addClass('mlr-msg-error').text(i18n.saveError || 'Error.');
                    }
                },
                error: function () {
                    $msg.addClass('mlr-msg-error').text(i18n.saveError || 'Error.');
                },
                complete: function () {
                    $btn.prop('disabled', false).text(i18n.save || 'Guardar');
                    setTimeout(function () {
                        $msg.fadeOut(300, function () {
                            $(this).text('').show().removeClass('mlr-msg-success mlr-msg-error');
                        });
                    }, 3000);
                }
            });
        },

        // --- Utils ---

        esc: function (str) {
            if (!str) return '';
            var div = document.createElement('div');
            div.appendChild(document.createTextNode(str));
            return div.innerHTML.replace(/"/g, '&quot;');
        }
    };

    $(document).ready(function () {
        MLRAdmin.init();
    });

})(jQuery);
