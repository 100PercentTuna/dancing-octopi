/**
 * Chart Presets System
 * Allows saving and loading common chart configurations
 */
(function() {
  'use strict';

  const PRESET_STORAGE_KEY = 'kunaal_chart_presets';
  const MAX_PRESETS = 20;

  class PresetManager {
    constructor() {
      this.presets = this.loadPresets();
    }

    loadPresets() {
      try {
        const stored = localStorage.getItem(PRESET_STORAGE_KEY);
        return stored ? JSON.parse(stored) : [];
      } catch (error) {
        if (typeof kunaalTheme !== 'undefined' && kunaalTheme.debug) {
          console.error('Failed to load presets:', error);
        }
        return [];
      }
    }

    savePresets() {
      try {
        localStorage.setItem(PRESET_STORAGE_KEY, JSON.stringify(this.presets));
        return true;
      } catch (error) {
        if (typeof kunaalTheme !== 'undefined' && kunaalTheme.debug) {
          console.error('Failed to save presets:', error);
        }
        return false;
      }
    }

    savePreset(name, blockType, attributes) {
      if (this.presets.length >= MAX_PRESETS) {
        this.presets.shift(); // Remove oldest
      }

      const preset = {
        id: 'preset-' + Date.now(),
        name: name,
        blockType: blockType,
        attributes: JSON.parse(JSON.stringify(attributes)), // Deep clone
        createdAt: new Date().toISOString(),
      };

      this.presets.push(preset);
      return this.savePresets();
    }

    getPresets(blockType = null) {
      if (blockType) {
        return this.presets.filter(p => p.blockType === blockType);
      }
      return this.presets;
    }

    loadPreset(presetId) {
      const preset = this.presets.find(p => p.id === presetId);
      return preset ? preset.attributes : null;
    }

    deletePreset(presetId) {
      this.presets = this.presets.filter(p => p.id !== presetId);
      return this.savePresets();
    }

    exportPresets() {
      return JSON.stringify(this.presets, null, 2);
    }

    importPresets(jsonString) {
      try {
        const imported = JSON.parse(jsonString);
        if (Array.isArray(imported)) {
          this.presets = imported.slice(0, MAX_PRESETS);
          return this.savePresets();
        }
        return false;
      } catch (error) {
        if (typeof kunaalTheme !== 'undefined' && kunaalTheme.debug) {
          console.error('Failed to import presets:', error);
        }
        return false;
      }
    }
  }

  // Make available globally
  window.kunaalPresets = new PresetManager();

  // Gutenberg integration
  if (window.wp && window.wp.hooks) {
    window.wp.hooks.addFilter(
      'kunaal.block.presets',
      'kunaal/presets',
      function(presets, blockType) {
        return window.kunaalPresets.getPresets(blockType);
      }
    );
  }
})();

