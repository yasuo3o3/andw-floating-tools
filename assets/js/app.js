(function() {
    'use strict';

    const FloatingTools = {
        initialized: false,
        tocDrawer: null,
        tocAnchorSheet: null,
        tocToggleButton: null,
        focusableElements: [],
        lastFocusedElement: null,
        displayMode: 'anchor-sheet',
        sheetSettings: {},
        initialState: 'closed',

        init: function() {
            if (this.initialized) return;

            this.loadSettings();
            this.bindEvents();
            this.setupTOC();
            this.setupScrollBehavior();
            this.checkTOCVisibility();
            this.initialized = true;
        },

        loadSettings: function() {
            if (window.andwFloatingTools) {
                this.displayMode = window.andwFloatingTools.tocDisplayMode || 'anchor-sheet';
                this.sheetSettings = window.andwFloatingTools.sheetSettings || {};
                this.initialState = window.andwFloatingTools.initialState || 'closed';
            }
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

            if (target.classList.contains('andw-floating-button')) {
                this.handleButtonClick(target, event);
            } else if (target.hasAttribute('data-toc-close')) {
                this.closeTOC();
            } else if (target.classList.contains('andw-toc-link')) {
                this.handleTOCLinkClick(target, event);
            }
        },

        handleButtonClick: function(button, event) {
            event.preventDefault();

            if (button.hasAttribute('data-toc-toggle')) {
                this.toggleTOC();
            } else if (button.classList.contains('andw-button-top')) {
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
            this.tocToggleButton = document.querySelector('[data-toc-toggle]');

            if (this.displayMode === 'anchor-sheet') {
                this.tocAnchorSheet = document.getElementById('andw-toc-anchor-sheet');
                this.setupAnchorSheet();
            } else {
                this.tocDrawer = document.getElementById('andw-toc-drawer');
                this.setupDrawer();
            }
        },

        setupAnchorSheet: function() {
            if (!this.tocAnchorSheet) return;

            this.focusableElements = this.tocAnchorSheet.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            var self = this;
            var backdrop = this.tocAnchorSheet.querySelector('.andw-toc-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() { self.closeTOC(); });
            }

            // 初期状態の設定
            if (this.initialState === 'peek') {
                this.setPeekState();
            }

            // リサイズイベント
            window.addEventListener('resize', function() { self.updateAnchorPosition(); });
        },

        setupDrawer: function() {
            if (!this.tocDrawer) return;

            this.focusableElements = this.tocDrawer.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );

            var self = this;
            var backdrop = this.tocDrawer.querySelector('.andw-toc-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() { self.closeTOC(); });
            }
        },

        toggleTOC: function() {
            var container = this.displayMode === 'anchor-sheet' ? this.tocAnchorSheet : this.tocDrawer;
            if (!container) return;

            var isHidden = container.getAttribute('aria-hidden') === 'true';

            if (isHidden) {
                this.openTOC();
            } else {
                this.closeTOC();
            }
        },

        openTOC: function() {
            var container = this.displayMode === 'anchor-sheet' ? this.tocAnchorSheet : this.tocDrawer;
            if (!container) return;

            this.lastFocusedElement = document.activeElement;

            if (this.displayMode === 'anchor-sheet') {
                this.updateAnchorPosition();
                container.setAttribute('aria-hidden', 'false');
                container.classList.add('andw-toc-open');
                document.body.classList.add('andw-toc-sheet-open');
            } else {
                container.setAttribute('aria-hidden', 'false');
                container.classList.add('andw-toc-open');
                document.body.classList.add('andw-toc-drawer-open');
            }

            if (this.tocToggleButton) {
                this.tocToggleButton.setAttribute('aria-expanded', 'true');
            }

            var self = this;
            setTimeout(function() {
                var firstFocusable = container.querySelector('.andw-toc-close');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
            }, 100);
        },

        closeTOC: function() {
            var container = this.displayMode === 'anchor-sheet' ? this.tocAnchorSheet : this.tocDrawer;
            if (!container) return;

            container.setAttribute('aria-hidden', 'true');
            container.classList.remove('andw-toc-open');

            if (this.tocToggleButton) {
                this.tocToggleButton.setAttribute('aria-expanded', 'false');
            }

            if (this.displayMode === 'anchor-sheet') {
                document.body.classList.remove('andw-toc-sheet-open');
                if (this.initialState === 'peek') {
                    this.setPeekState();
                }
            } else {
                document.body.classList.remove('andw-toc-drawer-open');
            }

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
            return (window.andwFloatingTools && window.andwFloatingTools.tocOffset) || 72;
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
            var scrollTriggers = document.querySelectorAll('.andw-scroll-trigger');
            var viewportHeight = window.innerHeight;
            var triggerPoint = viewportHeight * 0.5;

            scrollTriggers.forEach(function(trigger) {
                var shouldShow = scrollY > triggerPoint || direction === 'up';

                if (shouldShow) {
                    trigger.classList.add('andw-visible');
                } else {
                    trigger.classList.remove('andw-visible');
                }
            });
        },

        handleResize: function() {
            if (this.tocDrawer && this.tocDrawer.getAttribute('aria-hidden') === 'false') {
                this.updateFocusableElements();
            }
        },

        updateFocusableElements: function() {
            var container = this.displayMode === 'anchor-sheet' ? this.tocAnchorSheet : this.tocDrawer;
            if (!container) return;

            this.focusableElements = container.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
        },

        setPeekState: function() {
            if (!this.tocAnchorSheet) return;

            this.tocAnchorSheet.setAttribute('aria-hidden', 'false');
            this.tocAnchorSheet.classList.add('andw-toc-peek');
            this.updateAnchorPosition();
        },

        updateAnchorPosition: function() {
            if (!this.tocAnchorSheet || !this.tocToggleButton) return;

            var buttonRect = this.tocToggleButton.getBoundingClientRect();
            var sheetContent = this.tocAnchorSheet.querySelector('.andw-toc-sheet-content');

            if (!sheetContent) return;

            var gapRight = this.sheetSettings.gapRight || 12;
            var gapLeft = this.sheetSettings.gapLeft || 16;
            var maxWidth = this.sheetSettings.sheetMaxWidth || 480;
            var anchorOffsetY = this.sheetSettings.anchorOffsetY || 8;

            var availableWidth = window.innerWidth - gapLeft - gapRight;
            var sheetWidth = Math.min(availableWidth, maxWidth);

            var rightPosition = window.innerWidth - buttonRect.right;
            var bottomPosition = window.innerHeight - buttonRect.top + anchorOffsetY;

            sheetContent.style.position = 'fixed';
            sheetContent.style.right = rightPosition + 'px';
            sheetContent.style.bottom = bottomPosition + 'px';
            sheetContent.style.width = sheetWidth + 'px';
            sheetContent.style.maxHeight = 'var(--andw-max-height-vh)';
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

        checkTOCVisibility: function() {
            var tocButton = document.querySelector('.andw-button-toc');
            if (!tocButton) return;

            // ページ内の見出し（H2-H6）をチェック
            var headings = document.querySelectorAll('h2, h3, h4, h5, h6');

            if (headings.length === 0) {
                // 見出しがない場合は目次ボタンを非表示
                tocButton.style.display = 'none';
                tocButton.setAttribute('aria-hidden', 'true');
            } else {
                // 見出しがある場合は目次ボタンを表示
                tocButton.style.display = '';
                tocButton.removeAttribute('aria-hidden');
            }

            // 動的コンテンツ変更の監視（初回のみ設定）
            if (!this.contentObserver) {
                this.setupContentObserver();
            }
        },

        setupContentObserver: function() {
            var self = this;

            // MutationObserverで見出しの追加/削除を監視
            this.contentObserver = new MutationObserver(function(mutations) {
                var shouldRecheck = false;

                mutations.forEach(function(mutation) {
                    if (mutation.type === 'childList') {
                        var addedNodes = Array.prototype.slice.call(mutation.addedNodes);
                        var removedNodes = Array.prototype.slice.call(mutation.removedNodes);

                        // 見出し要素が追加/削除されたかチェック
                        var hasHeadingChanges = addedNodes.concat(removedNodes).some(function(node) {
                            if (node.nodeType === 1) { // Element node
                                return node.matches && (
                                    node.matches('h2, h3, h4, h5, h6') ||
                                    node.querySelector && node.querySelector('h2, h3, h4, h5, h6')
                                );
                            }
                            return false;
                        });

                        if (hasHeadingChanges) {
                            shouldRecheck = true;
                        }
                    }
                });

                if (shouldRecheck) {
                    // デバウンスして再チェック
                    clearTimeout(self.visibilityCheckTimeout);
                    self.visibilityCheckTimeout = setTimeout(function() {
                        self.checkTOCVisibility();
                    }, 100);
                }
            });

            // document.bodyを監視対象にする
            if (document.body) {
                this.contentObserver.observe(document.body, {
                    childList: true,
                    subtree: true
                });
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