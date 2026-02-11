(function () {
    'use strict';

    var MLR = {
        panel: null,
        toggleBtn: null,
        closeBtn: null,
        overlay: null,
        isOpen: false,
        activeCardIndex: null,
        lastFocusedElement: null,

        init: function () {
            this.panel = document.getElementById('mlr-panel');
            if (!this.panel) return;

            this.toggleBtn = document.querySelector('.mlr-toggle-btn');
            this.closeBtn = this.panel.querySelector('.mlr-close-btn');
            this.overlay = document.querySelector('.mlr-overlay');
            this.submenuPanel = this.panel.querySelector('.mlr-submenu-panel');

            // Move panel and overlay to be direct children of body so
            // the inert attribute on page wrappers does not block them.
            document.body.appendChild(this.panel);
            if (this.overlay) {
                document.body.appendChild(this.overlay);
            }

            this.bindEvents();
            this.setupKeyboard();
            this.addBackButtons();
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

            // Card clicks
            var cards = this.panel.querySelectorAll('.mlr-card[data-has-submenu]');
            for (var i = 0; i < cards.length; i++) {
                cards[i].addEventListener('click', function () {
                    var index = this.getAttribute('data-card-index');
                    self.toggleSubmenu(index, this);
                });
            }
        },

        setupKeyboard: function () {
            var self = this;

            document.addEventListener('keydown', function (e) {
                if (!self.isOpen) return;

                if (e.key === 'Escape') {
                    if (self.activeCardIndex !== null) {
                        self.closeSubmenu();
                    } else {
                        self.close();
                    }
                    return;
                }

                if (e.key === 'Tab') {
                    self.trapFocus(e);
                }
            });
        },

        addBackButtons: function () {
            if (!this.submenuPanel) return;
            var self = this;
            var contents = this.submenuPanel.querySelectorAll('.mlr-submenu-content');
            for (var i = 0; i < contents.length; i++) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'mlr-submenu-back';
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg> Volver';
                btn.addEventListener('click', function () {
                    self.closeSubmenu();
                });
                contents[i].insertBefore(btn, contents[i].firstChild);
            }
        },

        open: function () {
            if (this.isOpen) return;
            this.isOpen = true;

            this.lastFocusedElement = document.activeElement;

            this.panel.classList.add('mlr-open');

            if (this.overlay) {
                this.overlay.classList.add('mlr-active');
                this.overlay.setAttribute('aria-hidden', 'false');
            }

            if (this.toggleBtn) {
                this.toggleBtn.setAttribute('aria-expanded', 'true');
            }

            document.body.classList.add('mlr-body-locked');
            this.setInert(true);

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

            // Close submenu first if open
            if (this.activeCardIndex !== null) {
                this.closeSubmenu(true);
            }

            this.panel.classList.remove('mlr-open');

            if (this.overlay) {
                this.overlay.classList.remove('mlr-active');
                this.overlay.setAttribute('aria-hidden', 'true');
            }

            if (this.toggleBtn) {
                this.toggleBtn.setAttribute('aria-expanded', 'false');
            }

            document.body.classList.remove('mlr-body-locked');
            this.setInert(false);

            if (this.lastFocusedElement) {
                this.lastFocusedElement.focus();
                this.lastFocusedElement = null;
            }
        },

        toggleSubmenu: function (index, cardBtn) {
            if (this.activeCardIndex === index) {
                this.closeSubmenu();
                return;
            }

            this.openSubmenu(index, cardBtn);
        },

        openSubmenu: function (index, cardBtn) {
            // Deactivate previous
            if (this.activeCardIndex !== null) {
                this.deactivateCard(this.activeCardIndex);
            }

            this.activeCardIndex = index;

            // Activate card
            cardBtn.classList.add('mlr-card-selected');
            cardBtn.setAttribute('aria-expanded', 'true');

            var wrapper = cardBtn.closest('.mlr-card-wrapper');
            if (wrapper) {
                wrapper.classList.add('mlr-card-wrapper-active');
            }

            // Show submenu content
            var content = this.panel.querySelector('.mlr-submenu-content[data-card-index="' + index + '"]');
            if (content) {
                content.classList.add('mlr-submenu-visible');
                content.setAttribute('aria-hidden', 'false');
            }

            // Show submenu panel
            if (this.submenuPanel) {
                this.submenuPanel.setAttribute('aria-hidden', 'false');
            }

            // Expand panel
            this.panel.classList.add('mlr-submenu-active');

            // NUEVA LÍNEA: Actualizar border-radius según el índice
            this.updateSubmenuBorderRadius(index);
        },

        updateSubmenuBorderRadius: function (index) {
            var submenuContent = this.panel.querySelector(
                '.mlr-submenu-content[data-card-index="' + index + '"]'
            );

            if (submenuContent) {
                // Si es la primera tarjeta (índice 0), sin borde
                if (parseInt(index) === 0) {
                    submenuContent.style.borderTopLeftRadius = '0px';
                } else {
                    // Cualquier otra tarjeta: 10px de borde
                    submenuContent.style.borderTopLeftRadius = '10px';
                }
            }
        },

        closeSubmenu: function (skipAnimation) {
            if (this.activeCardIndex === null) return;

            this.deactivateCard(this.activeCardIndex);
            this.activeCardIndex = null;

            // Hide submenu panel
            if (this.submenuPanel) {
                this.submenuPanel.setAttribute('aria-hidden', 'true');
            }

            // Shrink panel - remove class from panel
            this.panel.classList.remove('mlr-submenu-active');
        },

        deactivateCard: function (index) {
            var cardBtn = this.panel.querySelector('.mlr-card[data-card-index="' + index + '"]');
            if (cardBtn) {
                cardBtn.classList.remove('mlr-card-selected');
                cardBtn.setAttribute('aria-expanded', 'false');

                // Deactivate wrapper
                var wrapper = cardBtn.closest('.mlr-card-wrapper');
                if (wrapper) {
                    wrapper.classList.remove('mlr-card-wrapper-active');
                }
            }

            var content = this.panel.querySelector('.mlr-submenu-content[data-card-index="' + index + '"]');
            if (content) {
                content.classList.remove('mlr-submenu-visible');
                content.setAttribute('aria-hidden', 'true');
            }
        },

        setInert: function (enable) {
            var children = document.body.children;
            for (var i = 0; i < children.length; i++) {
                var el = children[i];
                if (el === this.panel || el === this.overlay) continue;
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
