(function () {
    'use strict';

    /**
     * Menu Lateral Responsive v2
     *
     * Comportamiento:
     * - El panel cubre todo el lateral izquierdo de arriba a abajo
     * - El overlay oscurece el resto de la pantalla
     * - Mientras el panel está abierto, es IMPOSIBLE interactuar con elementos fuera del panel
     * - Click en el overlay cierra el panel con animación suave
     * - Escape cierra el panel
     * - Focus trap mantiene la navegación dentro del panel
     */
    var MLR = {
        panel: null,
        toggleBtn: null,
        closeBtn: null,
        overlay: null,
        isOpen: false,
        lastFocusedElement: null,

        init: function () {
            this.panel = document.getElementById('mlr-panel');
            if (!this.panel) return;

            this.toggleBtn = document.querySelector('.mlr-toggle-btn');
            this.closeBtn = this.panel.querySelector('.mlr-close-btn');
            this.overlay = document.querySelector('.mlr-overlay');

            this.bindEvents();
            this.setupKeyboard();
        },

        bindEvents: function () {
            var self = this;

            if (this.toggleBtn) {
                this.toggleBtn.addEventListener('click', function () {
                    self.open();
                });
            }

            if (this.closeBtn) {
                this.closeBtn.addEventListener('click', function () {
                    self.close();
                });
            }

            if (this.overlay) {
                this.overlay.addEventListener('click', function () {
                    self.close();
                });
            }
        },

        setupKeyboard: function () {
            var self = this;

            document.addEventListener('keydown', function (e) {
                if (!self.isOpen) return;

                // Escape cierra el panel
                if (e.key === 'Escape') {
                    self.close();
                    return;
                }

                // Tab trap: mantener foco dentro del panel
                if (e.key === 'Tab') {
                    self.trapFocus(e);
                }
            });
        },

        open: function () {
            if (this.isOpen) return;
            this.isOpen = true;

            // Guardar el elemento que tenía foco
            this.lastFocusedElement = document.activeElement;

            // Abrir panel con animación
            this.panel.classList.add('mlr-open');

            // Activar overlay
            if (this.overlay) {
                this.overlay.classList.add('mlr-active');
                this.overlay.setAttribute('aria-hidden', 'false');
            }

            // Actualizar botón toggle
            if (this.toggleBtn) {
                this.toggleBtn.setAttribute('aria-expanded', 'true');
            }

            // Bloquear scroll del body y toda interacción fuera del panel
            document.body.classList.add('mlr-body-locked');

            // Marcar todos los elementos fuera del panel como inert (no interactuables)
            this.setInert(true);

            // Mover foco al botón de cerrar
            var self = this;
            requestAnimationFrame(function () {
                if (self.closeBtn) {
                    self.closeBtn.focus();
                }
            });
        },

        close: function () {
            if (!this.isOpen) return;
            this.isOpen = false;

            // Cerrar panel con animación
            this.panel.classList.remove('mlr-open');

            // Desactivar overlay
            if (this.overlay) {
                this.overlay.classList.remove('mlr-active');
                this.overlay.setAttribute('aria-hidden', 'true');
            }

            // Actualizar botón toggle
            if (this.toggleBtn) {
                this.toggleBtn.setAttribute('aria-expanded', 'false');
            }

            // Desbloquear scroll y la interacción
            document.body.classList.remove('mlr-body-locked');
            this.setInert(false);

            // Devolver foco al elemento original
            if (this.lastFocusedElement) {
                this.lastFocusedElement.focus();
                this.lastFocusedElement = null;
            }
        },

        /**
         * Marca todos los hijos directos del body como inert (no interactuables)
         * excepto el panel y el overlay. Esto impide hacer click, tab o cualquier
         * interacción con elementos fuera del menú.
         */
        setInert: function (enable) {
            var children = document.body.children;
            for (var i = 0; i < children.length; i++) {
                var el = children[i];
                // No tocar el panel ni el overlay
                if (el === this.panel || el === this.overlay) continue;
                // No tocar scripts ni estilos
                if (el.tagName === 'SCRIPT' || el.tagName === 'STYLE' || el.tagName === 'LINK') continue;

                if (enable) {
                    el.setAttribute('inert', '');
                    el.setAttribute('aria-hidden', 'true');
                } else {
                    el.removeAttribute('inert');
                    el.removeAttribute('aria-hidden');
                }
            }
        },

        trapFocus: function (e) {
            var focusable = this.panel.querySelectorAll(
                'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])'
            );
            if (focusable.length === 0) return;

            var first = focusable[0];
            var last = focusable[focusable.length - 1];

            if (e.shiftKey) {
                if (document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                if (document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            MLR.init();
        });
    } else {
        MLR.init();
    }
})();
