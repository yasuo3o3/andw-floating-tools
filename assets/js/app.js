(function() {
    'use strict';

    const FloatingTools = {
        initialized: false,
        tocDrawer: null,
        tocToggleButton: null,
        focusableElements: [],
        lastFocusedElement: null,

        init: function() {
            if (this.initialized) return;

            this.bindEvents();
            this.setupTOC();
            this.setupScrollBehavior();
            this.initialized = true;
        },

        bindEvents: function() {
            var self = this;
            document.addEventListener('click', function(event) { self.handleClick(event); });
            document.addEventListener('keydown', function(event) { self.handleKeydown(event); });
            window.addEventListener('scroll', function() { self.handleScroll(); });
            window.addEventListener('resize', self.debounce(function() { self.handleResize(); }, 250));
        },

        handleClick: function(event) {
            var target = event.target.closest('button');
            if (!target) return;

            if (target.classList.contains('of-floating-button')) {
                this.handleButtonClick(target, event);
            } else if (target.hasAttribute('data-toc-close')) {
                this.closeTOC();
            } else if (target.classList.contains('of-toc-link')) {
                this.handleTOCLinkClick(target, event);
            }
        },

        handleButtonClick: function(button, event) {
            event.preventDefault();

            if (button.hasAttribute('data-toc-toggle')) {
                this.toggleTOC();
            } else if (button.classList.contains('of-button-top')) {
                this.scrollToTop();
            } else if (button.hasAttribute('data-url')) {
                this.handleCTAClick(button);
            }
        },

        handleCTAClick: function(button) {
            var url = button.getAttribute('data-url');
            var target = button.getAttribute('data-target') || '_blank';

            if (url) {
                if (target === '_blank') {
                    window.open(url, '_blank', 'noopener,noreferrer');
                } else {
                    window.location.href = url;
                }
            }
        },

        handleKeydown: function(event) {
            if (event.key === 'Escape') {
                if (this.tocDrawer && this.tocDrawer.getAttribute('aria-hidden') === 'false') {
                    this.closeTOC();
                }
            }

            if (this.tocDrawer && this.tocDrawer.getAttribute('aria-hidden') === 'false') {
                this.handleTOCKeydown(event);
            }
        },

        handleTOCKeydown: function(event) {
            if (event.key === 'Tab') {
                this.trapFocus(event);
            }
        },

        trapFocus: function(event) {
            if (this.focusableElements.length === 0) return;

            var firstFocusable = this.focusableElements[0];
            var lastFocusable = this.focusableElements[this.focusableElements.length - 1];

            if (event.shiftKey) {
                if (document.activeElement === firstFocusable) {
                    event.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                if (document.activeElement === lastFocusable) {
                    event.preventDefault();
                    firstFocusable.focus();
                }
            }
        },

        setupTOC: function() {
            this.tocDrawer = document.getElementById('of-toc-drawer');
            this.tocToggleButton = document.querySelector('[data-toc-toggle]');

            if (!this.tocDrawer) return;

            this.focusableElements = this.tocDrawer.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            var self = this;
            var backdrop = this.tocDrawer.querySelector('.of-toc-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() { self.closeTOC(); });
            }
        },

        toggleTOC: function() {
            if (!this.tocDrawer) return;

            var isHidden = this.tocDrawer.getAttribute('aria-hidden') === 'true';

            if (isHidden) {
                this.openTOC();
            } else {
                this.closeTOC();
            }
        },

        openTOC: function() {
            if (!this.tocDrawer) return;

            this.lastFocusedElement = document.activeElement;

            this.tocDrawer.setAttribute('aria-hidden', 'false');
            this.tocDrawer.classList.add('of-toc-open');

            if (this.tocToggleButton) {
                this.tocToggleButton.setAttribute('aria-expanded', 'true');
            }

            document.body.classList.add('of-toc-drawer-open');

            var self = this;
            setTimeout(function() {
                var firstFocusable = self.tocDrawer.querySelector('.of-toc-close');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            }, 100);
        },

        closeTOC: function() {
            if (!this.tocDrawer) return;

            this.tocDrawer.setAttribute('aria-hidden', 'true');
            this.tocDrawer.classList.remove('of-toc-open');

            if (this.tocToggleButton) {
                this.tocToggleButton.setAttribute('aria-expanded', 'false');
            }

            document.body.classList.remove('of-toc-drawer-open');

            if (this.lastFocusedElement && typeof this.lastFocusedElement.focus === 'function') {
                this.lastFocusedElement.focus();
                this.lastFocusedElement = null;
            }
        },

        handleTOCLinkClick: function(link, event) {
            var href = link.getAttribute('href');
            if (!href || !href.startsWith('#')) return;

            event.preventDefault();

            var targetId = href.substring(1);
            var targetElement = document.getElementById(targetId);

            if (targetElement) {
                this.closeTOC();
                this.scrollToElement(targetElement);
            }
        },

        scrollToElement: function(element) {
            var offset = this.getTOCOffset();
            var elementTop = element.getBoundingClientRect().top + window.pageYOffset;
            var scrollPosition = Math.max(0, elementTop - offset);

            if (this.prefersReducedMotion()) {
                window.scrollTo(0, scrollPosition);
            } else {
                window.scrollTo({
                    top: scrollPosition,
                    behavior: 'smooth'
                });
            }
        },

        getTOCOffset: function() {
            return (window.ofFloatingTools && window.ofFloatingTools.tocOffset) || 72;
        },

        scrollToTop: function() {
            if (this.prefersReducedMotion()) {
                window.scrollTo(0, 0);
            } else {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        },

        setupScrollBehavior: function() {
            var self = this;
            var lastScrollY = window.scrollY;
            var scrollDirection = 'down';

            this.handleScroll = this.throttle(function() {
                var currentScrollY = window.scrollY;
                var scrollDifference = Math.abs(currentScrollY - lastScrollY);

                if (scrollDifference > 5) {
                    scrollDirection = currentScrollY > lastScrollY ? 'down' : 'up';
                    lastScrollY = currentScrollY;
                }

                self.updateScrollTriggers(currentScrollY, scrollDirection);
            }, 16);

            this.handleScroll();
        },

        updateScrollTriggers: function(scrollY, direction) {
            var scrollTriggers = document.querySelectorAll('.of-scroll-trigger');
            var viewportHeight = window.innerHeight;
            var triggerPoint = viewportHeight * 0.5;

            scrollTriggers.forEach(function(trigger) {
                var shouldShow = scrollY > triggerPoint || direction === 'up';

                if (shouldShow) {
                    trigger.classList.add('of-visible');
                } else {
                    trigger.classList.remove('of-visible');
                }
            });
        },

        handleResize: function() {
            if (this.tocDrawer && this.tocDrawer.getAttribute('aria-hidden') === 'false') {
                this.updateFocusableElements();
            }
        },

        updateFocusableElements: function() {
            if (!this.tocDrawer) return;

            this.focusableElements = this.tocDrawer.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
        },

        prefersReducedMotion: function() {
            return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        },

        throttle: function(func, limit) {
            var inThrottle;
            return function() {
                var args = arguments;
                var context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(function() { inThrottle = false; }, limit);
                }
            }
        },

        debounce: function(func, wait) {
            var timeout;
            return function() {
                var context = this;
                var args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(function() {
                    func.apply(context, args);
                }, wait);
            };
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            FloatingTools.init();
        });
    } else {
        FloatingTools.init();
    }

    window.FloatingTools = FloatingTools;

})();