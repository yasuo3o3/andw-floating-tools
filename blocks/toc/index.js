import { registerBlockType } from '@wordpress/blocks';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, CheckboxControl, SelectControl, RangeControl, TextControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';

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

registerBlockType('andw-floating-tools/toc', {
    edit: function({ attributes, setAttributes }) {
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
            presetId
        } = attributes;

        const options = useSelect((select) => {
            return select('core').getOption('of_floating_tools_options') || {};
        });

        const defaultEnabled = options.enabled_buttons || ['top', 'toc', 'apply', 'contact'];
        const defaultOrder = options.button_order || ['top', 'apply', 'contact', 'toc'];

        return (
            <>
                <InspectorControls>
                    <PanelBody title={__('基本設定', 'andw-floating-tools')} initialOpen={true}>
                        <div style={{ marginBottom: '16px' }}>
                            <strong>{__('有効なボタン', 'andw-floating-tools')}</strong>
                            <p style={{ fontSize: '12px', color: '#666', margin: '4px 0 8px' }}>
                                {__('空の場合はサイト既定の設定を使用', 'andw-floating-tools')}
                            </p>
                            {ENABLED_OPTIONS.map((option) => (
                                <CheckboxControl
                                    key={option.value}
                                    label={option.label}
                                    checked={enabled.includes(option.value)}
                                    onChange={(checked) => {
                                        if (checked) {
                                            setAttributes({ enabled: [...enabled, option.value] });
                                        } else {
                                            setAttributes({ enabled: enabled.filter(item => item !== option.value) });
                                        }
                                    }}
                                />
                            ))}
                        </div>

                        <SelectControl
                            label={__('デスクトップレイアウト', 'andw-floating-tools')}
                            value={layoutDesktop}
                            options={LAYOUT_OPTIONS}
                            onChange={(value) => setAttributes({ layoutDesktop: value })}
                            help={__('空の場合はサイト既定の設定を使用', 'andw-floating-tools')}
                        />
                    </PanelBody>

                    <PanelBody title={__('位置設定', 'andw-floating-tools')} initialOpen={false}>
                        <div style={{ marginBottom: '16px' }}>
                            <strong>{__('デスクトップオフセット', 'andw-floating-tools')}</strong>
                            <div style={{ display: 'flex', gap: '8px', marginTop: '8px' }}>
                                <NumberControl
                                    label={__('下', 'andw-floating-tools')}
                                    value={offsetDesktop.bottom}
                                    onChange={(value) => setAttributes({
                                        offsetDesktop: { ...offsetDesktop, bottom: parseInt(value) || 0 }
                                    })}
                                    min={0}
                                    max={999}
                                />
                                <NumberControl
                                    label={__('右', 'andw-floating-tools')}
                                    value={offsetDesktop.right}
                                    onChange={(value) => setAttributes({
                                        offsetDesktop: { ...offsetDesktop, right: parseInt(value) || 0 }
                                    })}
                                    min={0}
                                    max={999}
                                />
                            </div>
                        </div>

                        <div style={{ marginBottom: '16px' }}>
                            <strong>{__('タブレットオフセット', 'andw-floating-tools')}</strong>
                            <div style={{ display: 'flex', gap: '8px', marginTop: '8px' }}>
                                <NumberControl
                                    label={__('下', 'andw-floating-tools')}
                                    value={offsetTablet.bottom}
                                    onChange={(value) => setAttributes({
                                        offsetTablet: { ...offsetTablet, bottom: parseInt(value) || 0 }
                                    })}
                                    min={0}
                                    max={999}
                                />
                                <NumberControl
                                    label={__('右', 'andw-floating-tools')}
                                    value={offsetTablet.right}
                                    onChange={(value) => setAttributes({
                                        offsetTablet: { ...offsetTablet, right: parseInt(value) || 0 }
                                    })}
                                    min={0}
                                    max={999}
                                />
                            </div>
                        </div>

                        <div style={{ marginBottom: '16px' }}>
                            <strong>{__('モバイルオフセット', 'andw-floating-tools')}</strong>
                            <div style={{ display: 'flex', gap: '8px', marginTop: '8px' }}>
                                <NumberControl
                                    label={__('下', 'andw-floating-tools')}
                                    value={offsetMobile.bottom}
                                    onChange={(value) => setAttributes({
                                        offsetMobile: { ...offsetMobile, bottom: parseInt(value) || 0 }
                                    })}
                                    min={0}
                                    max={999}
                                />
                                <NumberControl
                                    label={__('右', 'andw-floating-tools')}
                                    value={offsetMobile.right}
                                    onChange={(value) => setAttributes({
                                        offsetMobile: { ...offsetMobile, right: parseInt(value) || 0 }
                                    })}
                                    min={0}
                                    max={999}
                                />
                            </div>
                        </div>
                    </PanelBody>

                    <PanelBody title={__('目次設定', 'andw-floating-tools')} initialOpen={false}>
                        <SelectControl
                            label={__('見出しの深さ', 'andw-floating-tools')}
                            value={tocDepth}
                            options={TOC_DEPTH_OPTIONS}
                            onChange={(value) => setAttributes({ tocDepth: parseInt(value) })}
                            help={__('空の場合はサイト既定の設定を使用', 'andw-floating-tools')}
                        />

                        <RangeControl
                            label={__('スクロールオフセット (px)', 'andw-floating-tools')}
                            value={tocOffset}
                            onChange={(value) => setAttributes({ tocOffset: value })}
                            min={0}
                            max={999}
                            help={__('固定ヘッダーの高さに応じて調整', 'andw-floating-tools')}
                        />
                    </PanelBody>

                    <PanelBody title={__('CTAボタン設定', 'andw-floating-tools')} initialOpen={false}>
                        <div style={{ marginBottom: '24px' }}>
                            <strong>{__('お申し込みボタン', 'andw-floating-tools')}</strong>
                            <TextControl
                                label={__('URL', 'andw-floating-tools')}
                                value={applyUrl}
                                onChange={(value) => setAttributes({ applyUrl: value })}
                                type="url"
                                help={__('空の場合はサイト既定の設定を使用', 'andw-floating-tools')}
                            />
                            <TextControl
                                label={__('ラベル', 'andw-floating-tools')}
                                value={applyLabel}
                                onChange={(value) => setAttributes({ applyLabel: value })}
                            />
                            <SelectControl
                                label={__('ターゲット', 'andw-floating-tools')}
                                value={applyTarget}
                                options={TARGET_OPTIONS}
                                onChange={(value) => setAttributes({ applyTarget: value })}
                            />
                        </div>

                        <div>
                            <strong>{__('お問い合わせボタン', 'andw-floating-tools')}</strong>
                            <TextControl
                                label={__('URL', 'andw-floating-tools')}
                                value={contactUrl}
                                onChange={(value) => setAttributes({ contactUrl: value })}
                                type="url"
                                help={__('空の場合はサイト既定の設定を使用', 'andw-floating-tools')}
                            />
                            <TextControl
                                label={__('ラベル', 'andw-floating-tools')}
                                value={contactLabel}
                                onChange={(value) => setAttributes({ contactLabel: value })}
                            />
                            <SelectControl
                                label={__('ターゲット', 'andw-floating-tools')}
                                value={contactTarget}
                                options={TARGET_OPTIONS}
                                onChange={(value) => setAttributes({ contactTarget: value })}
                            />
                        </div>
                    </PanelBody>
                </InspectorControls>

                <div className="wp-block-andw-floating-tools-toc">
                    <div style={{
                        padding: '20px',
                        border: '2px dashed #ccc',
                        borderRadius: '4px',
                        textAlign: 'center',
                        backgroundColor: '#f9f9f9'
                    }}>
                        <h3 style={{ margin: '0 0 10px', fontSize: '16px' }}>
                            {__('Floating Tools 設定', 'andw-floating-tools')}
                        </h3>
                        <p style={{ margin: '0', fontSize: '14px', color: '#666' }}>
                            {__('このブロックは投稿単位での設定上書きに使用されます。フロントエンドでは表示されません。', 'andw-floating-tools')}
                        </p>
                        <div style={{ marginTop: '10px', fontSize: '12px', color: '#999' }}>
                            {enabled.length > 0 && (
                                <div>{__('有効なボタン', 'andw-floating-tools')}: {enabled.join(', ')}</div>
                            )}
                            {(applyUrl || contactUrl) && (
                                <div>{__('CTA設定あり', 'andw-floating-tools')}</div>
                            )}
                        </div>
                    </div>
                </div>
            </>
        );
    },

    save: function() {
        return null;
    }
});