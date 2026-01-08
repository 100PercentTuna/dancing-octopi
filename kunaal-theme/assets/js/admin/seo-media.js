/* global wp */

(function () {
  'use strict';

  /**
   * Initialize a media picker root (handles both settings page and meta box pickers).
   *
   * @param {HTMLElement} root - Container element with media picker controls
   */
  function initRoot(root) {
    if (!root || root.dataset.kunaalSeoMediaInit === '1') {
      return;
    }
    root.dataset.kunaalSeoMediaInit = '1';

    // Support both data attribute patterns:
    // - Settings page: data-kunaal-seo-media-*
    // - Meta box: data-kunaal-seo-meta-img-*
    const idInput =
      root.querySelector('[data-kunaal-seo-media-id]') ||
      root.querySelector('[data-kunaal-seo-meta-img-id]');
    const preview =
      root.querySelector('[data-kunaal-seo-media-preview]') ||
      root.querySelector('[data-kunaal-seo-meta-img-preview]');
    const pick =
      root.querySelector('[data-kunaal-seo-media-pick]') ||
      root.querySelector('[data-kunaal-seo-meta-img-pick]');
    const clear =
      root.querySelector('[data-kunaal-seo-media-clear]') ||
      root.querySelector('[data-kunaal-seo-meta-img-clear]');

    if (!idInput || !preview || !pick || !clear) {
      return;
    }
    if (!window.wp || !wp.media) {
      return;
    }

    let frame = null;

    /**
     * Update preview and input from attachment data.
     *
     * @param {Object} data - Attachment JSON data
     */
    function setPreviewFromAttachment(data) {
      const id = data && data.id ? String(data.id) : '';
      idInput.value = id;

      const sizes = data && data.sizes ? data.sizes : null;
      const thumb =
        sizes && (sizes.thumbnail || sizes.medium)
          ? (sizes.thumbnail || sizes.medium).url
          : data && data.url
            ? data.url
            : '';

      if (thumb) {
        const img = document.createElement('img');
        img.src = thumb;
        img.style.maxWidth = '160px';
        img.style.height = 'auto';
        preview.innerHTML = '';
        preview.appendChild(img);
      } else {
        preview.innerHTML = '<em>Selected</em>';
      }

      if (clear) {
        clear.classList.remove('hidden');
      }
    }

    pick.addEventListener('click', function () {
      if (frame) {
        frame.open();
        return;
      }

      frame = wp.media({
        title: 'Select image',
        button: { text: 'Use image' },
        multiple: false
      });

      frame.on('select', function () {
        const attachment = frame.state().get('selection').first();
        if (!attachment) {
          return;
        }
        setPreviewFromAttachment(attachment.toJSON());
      });

      frame.open();
    });

    if (clear) {
      clear.addEventListener('click', function () {
        idInput.value = '';
        preview.innerHTML = '<em>No image selected</em>';
        clear.classList.add('hidden');
      });
    }
  }

  /**
   * Initialize all media pickers on the page.
   */
  function initAll() {
    // Settings page pickers (wrapped in [data-kunaal-seo-media]).
    document.querySelectorAll('[data-kunaal-seo-media]').forEach(initRoot);

    // Meta box pickers (find by the hidden input's parent container).
    // The meta box doesn't have a wrapper, so we find the closest parent <p> or meta box container.
    document
      .querySelectorAll('[data-kunaal-seo-meta-img-id]')
      .forEach(function (input) {
        // Find the closest container (usually a <p> tag in the meta box).
        const container = input.closest('p') || input.parentElement;
        if (container) {
          initRoot(container);
        }
      });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();
