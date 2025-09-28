( function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { PanelBody, CheckboxControl, SelectControl, RangeControl, TextControl, __experimentalNumberControl: NumberControl } = wp.components;
    const { __ } = wp.i18n;
    const { useSelect } = wp.data;

    const ENABLED_OPTIONS = [
        { label: __('ページトップへ', 'andw-floating-tools'), value: 'top' },
        { label: __('お申し込み', 'andw-floating-tools'), value: 'apply' },
        { label: __('お問い合わせ', 'andw-floating-tools'), value: 'contact' },
        { label: __('目次', 'andw-floating-tools'), value: 'toc' }
    ];

    const LAYOUT_OPTIONS = [
        { label: __('右端・高さ中央・縦積み', 'andw-floating-tools'), value: 'stack-vertical-right-center' },
        { label: __('右下・横並び', 'andw-floating-tools'), value: 'bottom-right-inline' }
    ];

    const TOC_DEPTH_OPTIONS = [
        { label: 'H2', value: 2 },
        { label: 'H2-H3', value: 3 },
        { label: 'H2-H4', value: 4 }
    ];

    const TARGET_OPTIONS = [
        { label: __('同じウィンドウ', 'andw-floating-tools'), value: '_self' },
        { label: __('新しいウィンドウ', 'andw-floating-tools'), value: '_blank' }
    ];

    const DISPLAY_MODE_OPTIONS = [
        { label: __('アンカーシート（ボタン直上に展開）', 'andw-floating-tools'), value: 'anchor-sheet' },
        { label: __('右側ドロワー', 'andw-floating-tools'), value: 'drawer' },
        { label: __('アンカーパネル', 'andw-floating-tools'), value: 'anchor-panel' }
    ];

    const INITIAL_STATE_OPTIONS = [
        { label: __('閉じた状態', 'andw-floating-tools'), value: 'closed' },
        { label: __('ピーク状態（少し見える）', 'andw-floating-tools'), value: 'peek' }
    ];

    registerBlockType('andw-floating-tools/toc', {
        edit: function( props ) {
            const { attributes, setAttributes } = props;
            const {
                enabled,
                order,
                tocDepth,
                tocOffset,
                layoutDesktop,
                offsetDesktop,
                offsetMobile,
                offsetTablet,
                applyUrl,
                applyLabel,
                applyTarget,
                contactUrl,
                contactLabel,
                contactTarget,
                presetId,
                tocDisplayMode,
                sheetMaxWidth,
                maxHeightVh,
                gapRight,
                gapLeft,
                anchorOffsetY,
                initialState
            } = attributes;

            return wp.element.createElement(
                wp.element.Fragment,
                {},
                wp.element.createElement(
                    InspectorControls,
                    {},
                    wp.element.createElement(
                        PanelBody,
                        { title: __('基本設定', 'andw-floating-tools'), initialOpen: true },
                        wp.element.createElement(
                            'div',
                            { style: { marginBottom: '16px' } },
                            wp.element.createElement('strong', {}, __('有効なボタン', 'andw-floating-tools')),
                            wp.element.createElement(
                                'p',
                                { style: { fontSize: '12px', color: '#666', margin: '4px 0 8px' } },
                                __('空の場合はサイト既定の設定を使用', 'andw-floating-tools')
                            ),
                            ENABLED_OPTIONS.map( function( option ) {
                                return wp.element.createElement(
                                    CheckboxControl,
                                    {
                                        key: option.value,
                                        label: option.label,
                                        checked: enabled.includes(option.value),
                                        onChange: function( checked ) {
                                            if (checked) {
                                                setAttributes({ enabled: enabled.concat([option.value]) });
                                            } else {
                                                setAttributes({ enabled: enabled.filter(function(item) { return item !== option.value; }) });
                                            }
                                        }
                                    }
                                );
                            })
                        ),
                        wp.element.createElement(
                            SelectControl,
                            {
                                label: __('デスクトップレイアウト', 'andw-floating-tools'),
                                value: layoutDesktop,
                                options: LAYOUT_OPTIONS,
                                onChange: function( value ) { setAttributes({ layoutDesktop: value }); },
                                help: __('空の場合はサイト既定の設定を使用', 'andw-floating-tools')
                            }
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: __('位置設定', 'andw-floating-tools'), initialOpen: false },
                        wp.element.createElement(
                            'div',
                            { style: { marginBottom: '16px' } },
                            wp.element.createElement('strong', {}, __('デスクトップオフセット', 'andw-floating-tools')),
                            wp.element.createElement(
                                'div',
                                { style: { display: 'flex', gap: '8px', marginTop: '8px' } },
                                wp.element.createElement(
                                    'div',
                                    { style: { flex: '1' } },
                                    wp.element.createElement('label', {}, __('下', 'andw-floating-tools')),
                                    wp.element.createElement('input', {
                                        type: 'number',
                                        value: offsetDesktop.bottom,
                                        onChange: function( e ) {
                                            setAttributes({
                                                offsetDesktop: { ...offsetDesktop, bottom: parseInt(e.target.value) || 0 }
                                            });
                                        },
                                        min: 0,
                                        max: 999,
                                        style: { width: '100%' }
                                    })
                                ),
                                wp.element.createElement(
                                    'div',
                                    { style: { flex: '1' } },
                                    wp.element.createElement('label', {}, __('右', 'andw-floating-tools')),
                                    wp.element.createElement('input', {
                                        type: 'number',
                                        value: offsetDesktop.right,
                                        onChange: function( e ) {
                                            setAttributes({
                                                offsetDesktop: { ...offsetDesktop, right: parseInt(e.target.value) || 0 }
                                            });
                                        },
                                        min: 0,
                                        max: 999,
                                        style: { width: '100%' }
                                    })
                                )
                            )
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: __('目次設定', 'andw-floating-tools'), initialOpen: false },
                        wp.element.createElement(
                            SelectControl,
                            {
                                label: __('見出しの深さ', 'andw-floating-tools'),
                                value: tocDepth,
                                options: TOC_DEPTH_OPTIONS,
                                onChange: function( value ) { setAttributes({ tocDepth: parseInt(value) }); },
                                help: __('空の場合はサイト既定の設定を使用', 'andw-floating-tools')
                            }
                        ),
                        wp.element.createElement(
                            RangeControl,
                            {
                                label: __('スクロールオフセット (px)', 'andw-floating-tools'),
                                value: tocOffset,
                                onChange: function( value ) { setAttributes({ tocOffset: value }); },
                                min: 0,
                                max: 999,
                                help: __('固定ヘッダーの高さに応じて調整', 'andw-floating-tools')
                            }
                        ),
                        wp.element.createElement(
                            SelectControl,
                            {
                                label: __('表示モード', 'andw-floating-tools'),
                                value: tocDisplayMode,
                                options: [{ label: __('サイト既定', 'andw-floating-tools'), value: '' }].concat(DISPLAY_MODE_OPTIONS),
                                onChange: function( value ) { setAttributes({ tocDisplayMode: value }); },
                                help: __('空の場合はサイト既定の設定を使用', 'andw-floating-tools')
                            }
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: __('アンカーシート設定', 'andw-floating-tools'), initialOpen: false },
                        wp.element.createElement(
                            'div',
                            { style: { marginBottom: '16px' } },
                            wp.element.createElement('label', {}, __('最大幅 (px)', 'andw-floating-tools')),
                            wp.element.createElement('input', {
                                type: 'number',
                                value: sheetMaxWidth,
                                onChange: function( e ) { setAttributes({ sheetMaxWidth: parseInt(e.target.value) || 0 }); },
                                min: 0,
                                max: 800,
                                style: { width: '100%' },
                                placeholder: __('既定値を使用', 'andw-floating-tools')
                            })
                        ),
                        wp.element.createElement(
                            'div',
                            { style: { marginBottom: '16px' } },
                            wp.element.createElement('label', {}, __('最大高さ (vh)', 'andw-floating-tools')),
                            wp.element.createElement('input', {
                                type: 'number',
                                value: maxHeightVh,
                                onChange: function( e ) { setAttributes({ maxHeightVh: parseInt(e.target.value) || 0 }); },
                                min: 0,
                                max: 100,
                                style: { width: '100%' },
                                placeholder: __('既定値を使用', 'andw-floating-tools')
                            })
                        ),
                        wp.element.createElement(
                            'div',
                            { style: { display: 'flex', gap: '8px', marginBottom: '16px' } },
                            wp.element.createElement(
                                'div',
                                { style: { flex: '1' } },
                                wp.element.createElement('label', {}, __('左余白 (px)', 'andw-floating-tools')),
                                wp.element.createElement('input', {
                                    type: 'number',
                                    value: gapLeft,
                                    onChange: function( e ) { setAttributes({ gapLeft: parseInt(e.target.value) || 0 }); },
                                    min: 0,
                                    max: 100,
                                    style: { width: '100%' },
                                    placeholder: __('既定値', 'andw-floating-tools')
                                })
                            ),
                            wp.element.createElement(
                                'div',
                                { style: { flex: '1' } },
                                wp.element.createElement('label', {}, __('右余白 (px)', 'andw-floating-tools')),
                                wp.element.createElement('input', {
                                    type: 'number',
                                    value: gapRight,
                                    onChange: function( e ) { setAttributes({ gapRight: parseInt(e.target.value) || 0 }); },
                                    min: 0,
                                    max: 100,
                                    style: { width: '100%' },
                                    placeholder: __('既定値', 'andw-floating-tools')
                                })
                            )
                        ),
                        wp.element.createElement(
                            'div',
                            { style: { marginBottom: '16px' } },
                            wp.element.createElement('label', {}, __('ボタン間隔 (px)', 'andw-floating-tools')),
                            wp.element.createElement('input', {
                                type: 'number',
                                value: anchorOffsetY,
                                onChange: function( e ) { setAttributes({ anchorOffsetY: parseInt(e.target.value) || 0 }); },
                                min: 0,
                                max: 50,
                                style: { width: '100%' },
                                placeholder: __('既定値を使用', 'andw-floating-tools')
                            })
                        ),
                        wp.element.createElement(
                            SelectControl,
                            {
                                label: __('初期状態', 'andw-floating-tools'),
                                value: initialState,
                                options: [{ label: __('サイト既定', 'andw-floating-tools'), value: '' }].concat(INITIAL_STATE_OPTIONS),
                                onChange: function( value ) { setAttributes({ initialState: value }); },
                                help: __('アンカーシートモード時の初期表示状態', 'andw-floating-tools')
                            }
                        )
                    ),

                    wp.element.createElement(
                        PanelBody,
                        { title: __('CTAボタン設定', 'andw-floating-tools'), initialOpen: false },
                        wp.element.createElement(
                            'div',
                            { style: { marginBottom: '24px' } },
                            wp.element.createElement('strong', {}, __('お申し込みボタン', 'andw-floating-tools')),
                            wp.element.createElement(
                                TextControl,
                                {
                                    label: __('URL', 'andw-floating-tools'),
                                    value: applyUrl,
                                    onChange: function( value ) { setAttributes({ applyUrl: value }); },
                                    type: 'url',
                                    help: __('空の場合はサイト既定の設定を使用', 'andw-floating-tools')
                                }
                            ),
                            wp.element.createElement(
                                TextControl,
                                {
                                    label: __('ラベル', 'andw-floating-tools'),
                                    value: applyLabel,
                                    onChange: function( value ) { setAttributes({ applyLabel: value }); }
                                }
                            ),
                            wp.element.createElement(
                                SelectControl,
                                {
                                    label: __('ターゲット', 'andw-floating-tools'),
                                    value: applyTarget,
                                    options: TARGET_OPTIONS,
                                    onChange: function( value ) { setAttributes({ applyTarget: value }); }
                                }
                            )
                        ),
                        wp.element.createElement(
                            'div',
                            {},
                            wp.element.createElement('strong', {}, __('お問い合わせボタン', 'andw-floating-tools')),
                            wp.element.createElement(
                                TextControl,
                                {
                                    label: __('URL', 'andw-floating-tools'),
                                    value: contactUrl,
                                    onChange: function( value ) { setAttributes({ contactUrl: value }); },
                                    type: 'url',
                                    help: __('空の場合はサイト既定の設定を使用', 'andw-floating-tools')
                                }
                            ),
                            wp.element.createElement(
                                TextControl,
                                {
                                    label: __('ラベル', 'andw-floating-tools'),
                                    value: contactLabel,
                                    onChange: function( value ) { setAttributes({ contactLabel: value }); }
                                }
                            ),
                            wp.element.createElement(
                                SelectControl,
                                {
                                    label: __('ターゲット', 'andw-floating-tools'),
                                    value: contactTarget,
                                    options: TARGET_OPTIONS,
                                    onChange: function( value ) { setAttributes({ contactTarget: value }); }
                                }
                            )
                        )
                    )
                ),

                wp.element.createElement(
                    'div',
                    { className: 'wp-block-andw-floating-tools-toc' },
                    wp.element.createElement(
                        'div',
                        {
                            style: {
                                padding: '20px',
                                border: '2px dashed #ccc',
                                borderRadius: '4px',
                                textAlign: 'center',
                                backgroundColor: '#f9f9f9'
                            }
                        },
                        wp.element.createElement(
                            'h3',
                            { style: { margin: '0 0 10px', fontSize: '16px' } },
                            __('Floating Tools 設定', 'andw-floating-tools')
                        ),
                        wp.element.createElement(
                            'p',
                            { style: { margin: '0', fontSize: '14px', color: '#666' } },
                            __('このブロックは投稿単位での設定上書きに使用されます。フロントエンドでは表示されません。', 'andw-floating-tools')
                        ),
                        wp.element.createElement(
                            'div',
                            { style: { marginTop: '10px', fontSize: '12px', color: '#999' } },
                            enabled.length > 0 && wp.element.createElement(
                                'div',
                                {},
                                __('有効なボタン', 'andw-floating-tools') + ': ' + enabled.join(', ')
                            ),
                            (applyUrl || contactUrl) && wp.element.createElement(
                                'div',
                                {},
                                __('CTA設定あり', 'andw-floating-tools')
                            )
                        )
                    )
                )
            );
        },

        save: function() {
            return null;
        }
    });
} )();