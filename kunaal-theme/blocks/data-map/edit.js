/**
 * Data Map Block - Editor Component
 */
(function(blocks, element, blockEditor, components, i18n) {
  const { registerBlockType } = blocks;
  const { InspectorControls, useBlockProps } = blockEditor;
  const { PanelBody, TextControl, ToggleControl, SelectControl, RangeControl, Button } = components;
  const { __ } = i18n;
  const el = element.createElement;

  registerBlockType('kunaal/data-map', {
    edit: function(props) {
      const { attributes, setAttributes } = props;
      const { title, subtitle, mapType, baseMap, centerLat, centerLng, initialZoom, enableZoom, enablePan,
        showLabels, colorScale, colorLow, colorHigh, colorMid, colorNegative, dotSizeMin, dotSizeMax,
        dotOpacity, valueLabel, valueFormat, currencySymbol, valueSuffix, showLegend, legendPosition,
        legendTitle, height, sourceNote, regionData, pointData } = attributes;

      const blockProps = useBlockProps({ className: 'wp-block-kunaal-data-map' });

      function updateRegion(index, field, value) {
        const newRegions = [...regionData];
        if (!newRegions[index]) newRegions[index] = { code: '', value: 0, label: '' };
        newRegions[index][field] = field === 'value' ? parseFloat(value) || 0 : value;
        setAttributes({ regionData: newRegions });
      }

      function addRegion() {
        setAttributes({ regionData: [...regionData, { code: '', value: 0, label: '' }] });
      }

      function removeRegion(index) {
        setAttributes({ regionData: regionData.filter((_, i) => i !== index) });
      }

      function updatePoint(index, field, value) {
        const newPoints = [...pointData];
        if (!newPoints[index]) newPoints[index] = { lat: 0, lng: 0, value: 0, label: '', category: '' };
        newPoints[index][field] = field === 'lat' || field === 'lng' || field === 'value' ? parseFloat(value) || 0 : value;
        setAttributes({ pointData: newPoints });
      }

      function addPoint() {
        setAttributes({ pointData: [...pointData, { lat: 0, lng: 0, value: 0, label: '', category: '' }] });
      }

      function removePoint(index) {
        setAttributes({ pointData: pointData.filter((_, i) => i !== index) });
      }

      return el('div', blockProps,
        el(InspectorControls, {},
          el(PanelBody, { title: __('General'), initialOpen: true },
            el(TextControl, { label: __('Title'), value: title, onChange: v => setAttributes({ title: v }) }),
            el(TextControl, { label: __('Subtitle'), value: subtitle, onChange: v => setAttributes({ subtitle: v }) }),
            el(TextControl, { label: __('Source Note'), value: sourceNote, onChange: v => setAttributes({ sourceNote: v }) }),
            el(RangeControl, { label: __('Height'), value: height, min: 300, max: 800, onChange: v => setAttributes({ height: v }) })
          ),
          el(PanelBody, { title: __('Map Type') },
            el(SelectControl, { label: __('Visualization'), value: mapType, options: [
              { label: 'Choropleth', value: 'choropleth' },
              { label: 'Proportional Dots', value: 'dots' },
              { label: 'Gradient Dots', value: 'gradient-dots' },
              { label: 'Combined', value: 'combined' }
            ], onChange: v => setAttributes({ mapType: v }) }),
            el(SelectControl, { label: __('Base Map'), value: baseMap, options: [
              { label: 'World', value: 'world' },
              { label: 'USA', value: 'usa' },
              { label: 'Europe', value: 'europe' },
              { label: 'Custom GeoJSON', value: 'custom-geojson' }
            ], onChange: v => setAttributes({ baseMap: v }) }),
            baseMap === 'custom-geojson' && el(TextControl, {
              label: __('GeoJSON URL'),
              value: attributes.customGeoJSON || '',
              onChange: v => setAttributes({ customGeoJSON: v })
            }),
            el('div', { style: { display: 'flex', gap: '8px' } },
              el(RangeControl, { label: __('Center Lat'), value: centerLat, min: -90, max: 90, onChange: v => setAttributes({ centerLat: v }) }),
              el(RangeControl, { label: __('Center Lng'), value: centerLng, min: -180, max: 180, onChange: v => setAttributes({ centerLng: v }) })
            ),
            el(RangeControl, { label: __('Initial Zoom'), value: initialZoom, min: 1, max: 10, onChange: v => setAttributes({ initialZoom: v }) }),
            el(ToggleControl, { label: __('Enable Zoom'), checked: enableZoom, onChange: v => setAttributes({ enableZoom: v }) }),
            el(ToggleControl, { label: __('Enable Pan'), checked: enablePan, onChange: v => setAttributes({ enablePan: v }) })
          ),
          (mapType === 'choropleth' || mapType === 'combined') && el(PanelBody, { title: __('Region Data') },
            el('table', { className: 'map-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('Code')),
                  el('th', {}, __('Value')),
                  el('th', {}, __('Label')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                regionData.map((region, i) =>
                  el('tr', { key: i },
                    el('td', {}, el(TextControl, { value: region.code || '', onChange: v => updateRegion(i, 'code', v), placeholder: 'US' })),
                    el('td', {}, el('input', { type: 'number', value: region.value || 0, onChange: e => updateRegion(i, 'value', e.target.value), step: 'any' })),
                    el('td', {}, el(TextControl, { value: region.label || '', onChange: v => updateRegion(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removeRegion(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 4 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addRegion }, __('+ Add Region'))
                  )
                )
              )
            )
          ),
          (mapType === 'dots' || mapType === 'gradient-dots' || mapType === 'combined') && el(PanelBody, { title: __('Point Data') },
            el('table', { className: 'map-editor-table' },
              el('thead', {},
                el('tr', {},
                  el('th', {}, __('Lat')),
                  el('th', {}, __('Lng')),
                  el('th', {}, __('Value')),
                  el('th', {}, __('Label')),
                  el('th', {}, '')
                )
              ),
              el('tbody', {},
                pointData.map((point, i) =>
                  el('tr', { key: i },
                    el('td', {}, el('input', { type: 'number', value: point.lat || 0, onChange: e => updatePoint(i, 'lat', e.target.value), step: 'any', placeholder: '40.7128' })),
                    el('td', {}, el('input', { type: 'number', value: point.lng || 0, onChange: e => updatePoint(i, 'lng', e.target.value), step: 'any', placeholder: '-74.0060' })),
                    el('td', {}, el('input', { type: 'number', value: point.value || 0, onChange: e => updatePoint(i, 'value', e.target.value), step: 'any' })),
                    el('td', {}, el(TextControl, { value: point.label || '', onChange: v => updatePoint(i, 'label', v), placeholder: __('Label') })),
                    el('td', {}, el(Button, { isDestructive: true, isSmall: true, onClick: () => removePoint(i) }, '×'))
                  )
                ),
                el('tr', {},
                  el('td', { colSpan: 5 },
                    el(Button, { isPrimary: true, isSmall: true, onClick: addPoint }, __('+ Add Point'))
                  )
                )
              )
            )
          ),
          el(PanelBody, { title: __('Colors') },
            el(SelectControl, { label: __('Color Scale'), value: colorScale, options: [
              { label: 'Sequential', value: 'sequential' },
              { label: 'Diverging', value: 'diverging' },
              { label: 'Categorical', value: 'categorical' }
            ], onChange: v => setAttributes({ colorScale: v }) }),
            colorScale === 'sequential' && el('div', {},
              el('p', {}, __('Low Color')),
              el('input', { type: 'color', value: colorLow, onChange: e => setAttributes({ colorLow: e.target.value }) }),
              el('p', {}, __('High Color')),
              el('input', { type: 'color', value: colorHigh, onChange: e => setAttributes({ colorHigh: e.target.value }) })
            ),
            colorScale === 'diverging' && el('div', {},
              el('p', {}, __('Negative Color')),
              el('input', { type: 'color', value: colorNegative, onChange: e => setAttributes({ colorNegative: e.target.value }) }),
              el('p', {}, __('Neutral Color')),
              el('input', { type: 'color', value: colorMid, onChange: e => setAttributes({ colorMid: e.target.value }) }),
              el('p', {}, __('Positive Color')),
              el('input', { type: 'color', value: colorHigh, onChange: e => setAttributes({ colorHigh: e.target.value }) })
            )
          ),
          el(PanelBody, { title: __('Display') },
            el(TextControl, { label: __('Value Label'), value: valueLabel, onChange: v => setAttributes({ valueLabel: v }) }),
            el(SelectControl, { label: __('Value Format'), value: valueFormat, options: [
              { label: 'Number', value: 'number' },
              { label: 'Percent', value: 'percent' },
              { label: 'Currency', value: 'currency' },
              { label: 'Compact', value: 'compact' },
              { label: '1 Decimal', value: 'decimal1' }
            ], onChange: v => setAttributes({ valueFormat: v }) }),
            valueFormat === 'currency' && el(TextControl, { label: __('Currency Symbol'), value: currencySymbol, onChange: v => setAttributes({ currencySymbol: v }) }),
            el(TextControl, { label: __('Unit Suffix'), value: valueSuffix, onChange: v => setAttributes({ valueSuffix: v }), placeholder: 'people, °C, etc.' }),
            el(ToggleControl, { label: __('Show Legend'), checked: showLegend, onChange: v => setAttributes({ showLegend: v }) }),
            showLegend && el(SelectControl, { label: __('Legend Position'), value: legendPosition, options: [
              { label: 'Bottom Left', value: 'bottom-left' },
              { label: 'Bottom Right', value: 'bottom-right' },
              { label: 'Top Left', value: 'top-left' },
              { label: 'Top Right', value: 'top-right' }
            ], onChange: v => setAttributes({ legendPosition: v }) }),
            showLegend && el(TextControl, { label: __('Legend Title'), value: legendTitle, onChange: v => setAttributes({ legendTitle: v }) })
          ),
          (mapType === 'dots' || mapType === 'gradient-dots' || mapType === 'combined') && el(PanelBody, { title: __('Dot Settings') },
            el(RangeControl, { label: __('Min Size'), value: dotSizeMin, min: 2, max: 20, onChange: v => setAttributes({ dotSizeMin: v }) }),
            el(RangeControl, { label: __('Max Size'), value: dotSizeMax, min: 20, max: 60, onChange: v => setAttributes({ dotSizeMax: v }) }),
            el(RangeControl, { label: __('Opacity'), value: dotOpacity, min: 0.3, max: 1, step: 0.1, onChange: v => setAttributes({ dotOpacity: v }) })
          )
        ),
        el('div', { className: 'map-preview' },
          title && el('h3', {}, title),
          subtitle && el('p', {}, subtitle),
          el('div', { className: 'map-chart-preview', style: { height: height + 'px', background: 'var(--bg-alt)', border: '1px solid var(--hair)', borderRadius: '8px', display: 'flex', alignItems: 'center', justifyContent: 'center' } },
            el('p', { style: { color: 'var(--muted)' } }, __('Map preview will appear on frontend'))
          )
        )
      );
    }
  });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);

