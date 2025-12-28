/**
 * Custom Color Picker Component for Gutenberg
 * Provides theme colors, gradients, and custom color selection
 */
(function(wp) {
  const { Component } = wp.element;
  const { Button, Popover, TabPanel } = wp.components;
  const { __ } = wp.i18n;
  const el = wp.element.createElement;

  const themeColors = [
    { name: 'Brown', value: '#7D6B5D', label: __('Brown', 'kunaal-theme') },
    { name: 'Warm Light', value: '#B8A99A', label: __('Warm Light', 'kunaal-theme') },
    { name: 'Terracotta', value: '#C9553D', label: __('Terracotta', 'kunaal-theme') },
    { name: 'Sienna', value: '#8B7355', label: __('Sienna', 'kunaal-theme') },
    { name: 'Champagne', value: '#D4C4B5', label: __('Champagne', 'kunaal-theme') },
    { name: 'Dark Brown', value: '#6B5B4F', label: __('Dark Brown', 'kunaal-theme') },
    { name: 'Taupe', value: '#A08B7A', label: __('Taupe', 'kunaal-theme') },
    { name: 'Blue', value: '#4A90A4', label: __('Blue', 'kunaal-theme') },
  ];

  const gradients = [
    { name: 'Brown Gradient', value: 'linear-gradient(135deg, #7D6B5D 0%, #B8A99A 100%)', label: __('Brown Gradient', 'kunaal-theme') },
    { name: 'Warm Gradient', value: 'linear-gradient(135deg, #B8A99A 0%, #D4C4B5 100%)', label: __('Warm Gradient', 'kunaal-theme') },
    { name: 'Terracotta Gradient', value: 'linear-gradient(135deg, #C9553D 0%, #E07A62 100%)', label: __('Terracotta Gradient', 'kunaal-theme') },
    { name: 'Neutral Gradient', value: 'linear-gradient(135deg, #F5F0EB 0%, #7D6B5D 100%)', label: __('Neutral Gradient', 'kunaal-theme') },
  ];

  wp.kunaalColorPicker = class extends Component {
    constructor(props) {
      super(props);
      this.state = {
        isOpen: false,
      };
    }

    render() {
      const { value, onChange, label, showGradients = true, showCustom = true } = this.props;
      const { isOpen } = this.state;

      return el('div', { className: 'kunaal-color-picker' },
        label && el('label', { className: 'components-base-control__label' }, label),
        el('div', { className: 'kunaal-color-picker-controls' },
          el(Button, {
            onClick: () => this.setState({ isOpen: !isOpen }),
            className: 'kunaal-color-picker-button',
            'aria-label': __('Select color', 'kunaal-theme')
          },
            el('span', {
              className: 'kunaal-color-preview',
              style: {
                background: value || 'transparent',
                border: value ? 'none' : '2px dashed var(--hair)',
              }
            }),
            el('span', { className: 'kunaal-color-value' }, value || __('No color', 'kunaal-theme'))
          ),
          isOpen && el(Popover, {
            position: 'bottom left',
            onClose: () => this.setState({ isOpen: false }),
            className: 'kunaal-color-picker-popover'
          },
            el(TabPanel, {
              className: 'kunaal-color-picker-tabs',
              tabs: [
                { name: 'theme', title: __('Theme', 'kunaal-theme') },
                ...(showGradients ? [{ name: 'gradients', title: __('Gradients', 'kunaal-theme') }] : []),
                ...(showCustom ? [{ name: 'custom', title: __('Custom', 'kunaal-theme') }] : []),
              ]
            }, (tab) => {
              if (tab.name === 'theme') {
                return el('div', { className: 'kunaal-color-grid' },
                  themeColors.map((color) =>
                    el('button', {
                      key: color.value,
                      className: 'kunaal-color-swatch',
                      style: { backgroundColor: color.value },
                      onClick: () => {
                        onChange(color.value);
                        this.setState({ isOpen: false });
                      },
                      'aria-label': color.label,
                      title: color.label
                    })
                  )
                );
              }
              if (tab.name === 'gradients' && showGradients) {
                return el('div', { className: 'kunaal-gradient-grid' },
                  gradients.map((gradient) =>
                    el('button', {
                      key: gradient.value,
                      className: 'kunaal-gradient-swatch',
                      style: { background: gradient.value },
                      onClick: () => {
                        onChange(gradient.value);
                        this.setState({ isOpen: false });
                      },
                      'aria-label': gradient.label,
                      title: gradient.label
                    })
                  )
                );
              }
              if (tab.name === 'custom' && showCustom) {
                return el('div', { className: 'kunaal-custom-color' },
                  el('input', {
                    type: 'color',
                    value: value && value.startsWith('#') ? value : '#000000',
                    onChange: (e) => onChange(e.target.value),
                    className: 'kunaal-color-input'
                  }),
                  el('input', {
                    type: 'text',
                    value: value || '',
                    onChange: (e) => onChange(e.target.value),
                    placeholder: '#000000 or gradient',
                    className: 'kunaal-color-text-input'
                  })
                );
              }
              return null;
            })
          )
        )
      );
    }
  };
})(window.wp);
