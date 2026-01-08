/* global wp */

(function () {
  function initRoot(root) {
    if (!root || root.dataset.kunaalSeoMediaInit === '1') return;
    root.dataset.kunaalSeoMediaInit = '1';

    const idInput = root.querySelector('[data-kunaal-seo-media-id]');
    const preview = root.querySelector('[data-kunaal-seo-media-preview]');
    const pick = root.querySelector('[data-kunaal-seo-media-pick]');
    const clear = root.querySelector('[data-kunaal-seo-media-clear]');

    if (!idInput || !preview || !pick || !clear) return;
    if (!window.wp || !wp.media) return;

    let frame = null;

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

      clear.classList.remove('hidden');
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
        if (!attachment) return;
        setPreviewFromAttachment(attachment.toJSON());
      });

      frame.open();
    });

    clear.addEventListener('click', function () {
      idInput.value = '';
      preview.innerHTML = '<em>No image selected</em>';
      clear.classList.add('hidden');
    });
  }

  function initAll() {
    document.querySelectorAll('[data-kunaal-seo-media]').forEach(initRoot);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();

/* global wp */

(function () {
  function initRoot(root) {
    if (!root || root.dataset.kunaalSeoMediaInit === '1') return;
    root.dataset.kunaalSeoMediaInit = '1';

    const idInput = root.querySelector('[data-kunaal-seo-media-id]');
    const preview = root.querySelector('[data-kunaal-seo-media-preview]');
    const pick = root.querySelector('[data-kunaal-seo-media-pick]');
    const clear = root.querySelector('[data-kunaal-seo-media-clear]');

    if (!idInput || !preview || !pick || !clear) return;
    if (!window.wp || !wp.media) return;

    let frame = null;

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

      clear.classList.remove('hidden');
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
        if (!attachment) return;
        setPreviewFromAttachment(attachment.toJSON());
      });

      frame.open();
    });

    clear.addEventListener('click', function () {
      idInput.value = '';
      preview.innerHTML = '<em>No image selected</em>';
      clear.classList.add('hidden');
    });
  }

  function initAll() {
    document.querySelectorAll('[data-kunaal-seo-media]').forEach(initRoot);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();

/* global wp */

(function () {
  function initRoot(root) {
    if (!root || root.dataset.kunaalSeoMediaInit === '1') return;
    root.dataset.kunaalSeoMediaInit = '1';

    const idInput = root.querySelector('[data-kunaal-seo-media-id]');
    const preview = root.querySelector('[data-kunaal-seo-media-preview]');
    const pick = root.querySelector('[data-kunaal-seo-media-pick]');
    const clear = root.querySelector('[data-kunaal-seo-media-clear]');

    if (!idInput || !preview || !pick || !clear) return;
    if (!window.wp || !wp.media) return;

    let frame = null;

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

      clear.classList.remove('hidden');
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
        if (!attachment) return;
        setPreviewFromAttachment(attachment.toJSON());
      });

      frame.open();
    });

    clear.addEventListener('click', function () {
      idInput.value = '';
      preview.innerHTML = '<em>No image selected</em>';
      clear.classList.add('hidden');
    });
  }

  function initAll() {
    document
      .querySelectorAll('[data-kunaal-seo-media]')
      .forEach(initRoot);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }
})();


