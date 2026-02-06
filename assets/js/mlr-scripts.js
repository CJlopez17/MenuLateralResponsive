(function () {
    'use strict';

    /**
     * Menu Lateral Responsive - Frontend JavaScript
     */
    var MLR = {
        sidebar: null,
        toggleBtn: null,
        closeBtn: null,
        overlay: null,
        submenuToggles: null,
        isOpen: false,

        /**
         * Inicializa el menú lateral.
         */
        init: function () {
            this.sidebar = document.getElementById('mlr-sidebar');
            if (!this.sidebar) {
                return;
            }

            this.toggleBtn = document.querySelector('.mlr-toggle-btn');
            this.closeBtn = this.sidebar.querySelector('.mlr-close-btn');
            this.overlay = document.querySelector('.mlr-overlay');
            this.submenuToggles = this.sidebar.querySelectorAll('.mlr-submenu-toggle');

            this.applyConfig();
            this.bindEvents();
            this.setupKeyboardNav();
        },

        /**
         * Aplica la configuración pasada desde PHP.
         */
        applyConfig: function () {
            var config = window.mlrConfig || {};

            if (config.menuPosition === 'right') {
                document.body.classList.add('mlr-menu-right');
            }

            if (config.overlayColor && this.overlay) {
                this.overlay.style.backgroundColor = config.overlayColor;
            }
        },

        /**
         * Bindea los eventos principales.
         */
        bindEvents: function () {
            var self = this;

            // Toggle button
            if (this.toggleBtn) {
                this.toggleBtn.addEventListener('click', function () {
                    self.toggle();
                });
            }

            // Close button
            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', function () {
                    self.close();
                });
            }

            // Overlay click
            if (this.overlay) {
                this.overlay.addEventListener('click', function () {
                    var config = window.mlrConfig || {};
                    if (config.closeOnOverlay !== false) {
                        self.close();
                    }
                });
            }

            // Submenu toggles
            if (this.submenuToggles) {
                this.submenuToggles.forEach(function (toggle) {
                    toggle.addEventListener('click', function (e) {
                        e.preventDefault();
                        self.toggleSubmenu(this);
                    });
                });
            }

            // Cerrar con resize si pasa a desktop (opcional)
            window.addEventListener('resize', this.debounce(function () {
                // Comportamiento opcional en resize
            }, 250));
        },

        /**
         * Configura la navegación por teclado.
         */
        setupKeyboardNav: function () {
            var self = this;

            document.addEventListener('keydown', function (e) {
                // Escape cierra el menú
                if (e.key === 'Escape' && self.isOpen) {
                    self.close();
                    if (self.toggleBtn) {
                        self.toggleBtn.focus();
                    }
                }
            });

            // Trap focus dentro del sidebar cuando está abierto
            if (this.sidebar) {
                this.sidebar.addEventListener('keydown', function (e) {
                    if (e.key === 'Tab' && self.isOpen) {
                        self.trapFocus(e);
                    }
                });
            }
        },

        /**
         * Abre o cierra el menú.
         */
        toggle: function () {
            if (this.isOpen) {
                this.close();
            } else {
                this.open();
            }
        },

        /**
         * Abre el menú lateral.
         */
        open: function () {
            this.isOpen = true;
            this.sidebar.classList.add('mlr-open');

            if (this.overlay) {
                this.overlay.classList.add('mlr-active');
                this.overlay.setAttribute('aria-hidden', 'false');
            }

            if (this.toggleBtn) {
                this.toggleBtn.setAttribute('aria-expanded', 'true');
            }

            document.body.classList.add('mlr-sidebar-open');
            document.body.style.overflow = 'hidden';

            // Focus al botón de cerrar
            if (this.closeBtn) {
                this.closeBtn.focus();
            }
        },

        /**
         * Cierra el menú lateral.
         */
        close: function () {
            this.isOpen = false;
            this.sidebar.classList.remove('mlr-open');

            if (this.overlay) {
                this.overlay.classList.remove('mlr-active');
                this.overlay.setAttribute('aria-hidden', 'true');
            }

            if (this.toggleBtn) {
                this.toggleBtn.setAttribute('aria-expanded', 'false');
            }

            document.body.classList.remove('mlr-sidebar-open');
            document.body.style.overflow = '';
        },

        /**
         * Expande o colapsa un submenú.
         *
         * @param {HTMLElement} toggle Botón de toggle del submenú.
         */
        toggleSubmenu: function (toggle) {
            var parentItem = toggle.closest('.mlr-has-children');
            if (!parentItem) {
                return;
            }

            var submenu = parentItem.querySelector('.mlr-sub-menu');
            if (!submenu) {
                return;
            }

            var isExpanded = toggle.getAttribute('aria-expanded') === 'true';

            if (isExpanded) {
                submenu.classList.remove('mlr-sub-open');
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                submenu.classList.add('mlr-sub-open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        },

        /**
         * Atrapa el foco dentro del sidebar.
         *
         * @param {KeyboardEvent} e Evento de teclado.
         */
        trapFocus: function (e) {
            var focusable = this.sidebar.querySelectorAll(
                'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'
            );

            if (focusable.length === 0) {
                return;
            }

            var firstFocusable = focusable[0];
            var lastFocusable = focusable[focusable.length - 1];

            if (e.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        },

        /**
         * Utilidad debounce.
         *
         * @param {Function} func     Función a ejecutar.
         * @param {number}   wait     Tiempo de espera en ms.
         * @return {Function} Función con debounce.
         */
        debounce: function (func, wait) {
            var timeout;
            return function () {
                var context = this;
                var args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function () {
                    func.apply(context, args);
                }, wait);
            };
        }
    };

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            MLR.init();
        });
    } else {
        MLR.init();
    }
})();
