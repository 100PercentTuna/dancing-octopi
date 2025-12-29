/**
 * Kunaal Theme - Complete JavaScript
 * All interactions: scroll effects, filters, sharing, reveals, parallax
 */
(function() {
  'use strict';

  // Only enable "hide until revealed" animations once this script is actually running.
  // If this JS fails to load/parse on a client, we want server-rendered content visible.
  document.documentElement.classList.add('js-ready');

  // ========================================
  // DYNAMIC HEADER HEIGHT (--mastH)
  // ========================================
  function updateMastHeight() {
    const mast = document.querySelector('.mast');
    if (mast) {
      const rect = mast.getBoundingClientRect();
      const height = rect.bottom;
      document.documentElement.style.setProperty('--mastH', height + 'px');
    }
  }
  
  // Update on load and resize
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', updateMastHeight);
  } else {
    updateMastHeight();
  }
  window.addEventListener('resize', updateMastHeight);
  // Also update after fonts load (header height may change)
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(updateMastHeight);
  }

  // ========================================
  // DOM REFERENCES
  // ========================================
  // Unique page elements (acceptable to use IDs)
  const progressFill = document.getElementById('progressFill');
  const avatar = document.getElementById('avatar');
  const avatarImg = document.getElementById('avatarImg');
  const downloadButton = document.getElementById('downloadButton');
  const tocList = document.getElementById('tocList');
  const infiniteLoader = document.getElementById('infiniteLoader');
  
  // Reusable components (using data-* hooks instead of IDs)
  const navToggle = document.querySelector('[data-ui="nav-toggle"]');
  const nav = document.querySelector('[data-ui="nav"]');
  const actionDock = document.querySelector('[data-ui="action-dock"]');
  const shareToggle = document.querySelector('[data-ui="share-toggle"]');
  const subscribeToggle = document.querySelector('[data-ui="subscribe-toggle"]');
  const sharePanel = document.querySelector('[data-ui="share-panel"]');
  const subscribePanel = document.querySelector('[data-ui="subscribe-panel"]');
  const essayGrid = document.querySelector('[data-ui="essay-grid"]');
  const jotList = document.querySelector('[data-ui="jot-list"]');
  
  // Early exit if critical DOM elements are missing
  if (!navToggle || !nav) {
    // Navigation not available - continue with other features
  }

  // State
  let lastY = 0;
  let ticking = false;
  let cachedDocH = 1;
  let selectedTopics = new Set();
  let currentPage = 1;
  let isLoading = false;
  let hasMore = true;
  let ajaxDisabled = false;

  // ========================================
  // SUBSCRIBE (built-in mode)
  // ========================================
  function initSubscribeForms() {
    const forms = Array.from(document.querySelectorAll('form[data-subscribe-form]'));
    if (!forms.length) return;

    forms.forEach((form) => {
      const mode = form.dataset.subscribeMode || 'builtin';
      if (mode === 'external') return; // allow normal provider POST

      const statusEl = form.parentElement?.querySelector('.subscribe-status') || form.querySelector('.subscribe-status');
      const input = form.querySelector('input[type="email"]');
      const btn = form.querySelector('button[type="submit"]');
      if (!input || !btn) return;

      form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const ajaxUrl = window.kunaalTheme?.ajaxUrl;
        const nonce = window.kunaalTheme?.nonce;
        if (!ajaxUrl || !nonce) {
          if (statusEl) statusEl.textContent = 'Subscribe is not configured correctly.';
          return;
        }

        const email = (input.value || '').trim();
        if (!email) {
          if (statusEl) statusEl.textContent = 'Please enter an email address.';
          return;
        }

        btn.disabled = true;
        const originalText = btn.textContent;
        btn.textContent = 'Working…';
        if (statusEl) statusEl.textContent = '';

        try {
          const body = new URLSearchParams();
          body.set('action', 'kunaal_subscribe');
          body.set('nonce', nonce);
          body.set('email', email);
          body.set('source', form.dataset.subscribeForm || 'unknown');

          const res = await fetch(ajaxUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            credentials: 'same-origin',
            body: body.toString()
          });

          const data = await res.json();
          if (data && data.success) {
            if (statusEl) statusEl.textContent = (data.data && data.data.message) ? data.data.message : 'Check your inbox to confirm your subscription.';
            form.reset();
          } else {
            if (statusEl) statusEl.textContent = (data && data.data && data.data.message) ? data.data.message : 'Unable to subscribe right now.';
          }
        } catch (err) {
          if (statusEl) statusEl.textContent = 'Network error. Please try again.';
        } finally {
          btn.disabled = false;
          btn.textContent = originalText;
        }
      });
    });
  }

  // Preserve initial server-rendered items for client-side filtering fallback
  const initialEssayItems = essayGrid ? Array.from(essayGrid.children) : null;
  const initialJotItems = jotList ? Array.from(jotList.children) : null;

  // ========================================
  // SCROLL ENGINE - Header compaction & progress
  // ========================================
  function cacheViewport() {
    const doc = document.documentElement;
    cachedDocH = (doc.scrollHeight - doc.clientHeight) || 1;
  }

  function updateScrollEffects(y) {
    // Header compaction (0 to 1 over 120px scroll)
    const p = Math.min(y / 120, 1);
    if (document.body) {
      document.body.style.setProperty('--p', p.toFixed(4));
    }

    // Progress bar
    if (progressFill) {
      const targetFrac = Math.min(y / cachedDocH, 1);
      progressFill.style.width = (targetFrac * 100) + '%';
    }
  }

  function onTick() {
    ticking = false;
    updateScrollEffects(lastY);
    onParallaxTick();
  }

  function requestTick() {
    if (!ticking) {
      ticking = true;
      requestAnimationFrame(onTick);
    }
  }

  // ========================================
  // PARALLAX for card images
  // ========================================
  let parallaxItems = [];
  let parallaxObserver = null;
  let visibleParallaxItems = new Set();

  function initParallax() {
    if (parallaxObserver) parallaxObserver.disconnect();
    visibleParallaxItems.clear();

    parallaxItems = Array.from(document.querySelectorAll('[data-parallax="true"] img'));
    if (!parallaxItems.length) return;

    parallaxObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          visibleParallaxItems.add(entry.target);
        } else {
          visibleParallaxItems.delete(entry.target);
        }
      });
    }, { rootMargin: '50px 0px', threshold: 0 });

    parallaxItems.forEach(img => parallaxObserver.observe(img));
  }

  function onParallaxTick() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const vh = window.innerHeight || 1;

    for (const img of visibleParallaxItems) {
      if (!img) continue;
      const r = img.getBoundingClientRect();
      const mid = (r.top + r.bottom) / 2;
      // Normalized position from -1 (top of screen) to 1 (bottom of screen)
      const t = (mid / vh) * 2 - 1;
      // Full bidirectional parallax: -20px to +20px
      const y = t * -20;
      img.style.setProperty('--py', y + 'px');
    }
  }

  // ========================================
  // SCROLL REVEAL - Bidirectional
  // ========================================
  let revealObserver = null;

  function initScrollReveal() {
    if (revealObserver) revealObserver.disconnect();
    
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    
    if (prefersReducedMotion) {
      // Just show everything
      document.querySelectorAll('.card, .jRow, .reveal, .reveal-left, .reveal-right, .sectionHead').forEach(el => {
        el.classList.add('revealed');
      });
      return;
    }

    let didRevealAny = false;

    revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          didRevealAny = true;
        } else {
          // Never "un-reveal" content. If reveal logic fails for any reason,
          // keeping content visible is always better than hiding it.
        }
      });
    }, {
      rootMargin: '0px 0px -60px 0px',
      threshold: 0.1
    });
    
    document.querySelectorAll('.card, .jRow, .reveal, .reveal-left, .reveal-right, .sectionHead').forEach(el => {
      revealObserver.observe(el);
    });

    // Failsafe: if IntersectionObserver never marks anything as revealed,
    // show server-rendered content after a short delay.
    setTimeout(() => {
      if (didRevealAny) return;
      document.querySelectorAll('.card, .jRow, .reveal, .reveal-left, .reveal-right, .sectionHead').forEach(el => {
        el.classList.add('revealed');
      });
    }, 400);
  }

  // ========================================
  // AVATAR ERROR HANDLER
  // ========================================
  function initAvatar() {
    if (!avatar || !avatarImg) return;
    avatarImg.addEventListener('error', () => {
      avatarImg.remove();
      avatar.classList.add('noImg');
    }, { once: true });
  }

  // ========================================
  // MOBILE NAV
  // ========================================
  function initNav() {
    if (!navToggle || !nav) return;

    navToggle.addEventListener('click', () => {
      const isOpen = nav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    document.addEventListener('click', (e) => {
      if (!nav.contains(e.target) && !navToggle.contains(e.target)) {
        nav.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        nav.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ========================================
  // FILTERS - Event delegation via data-* hooks
  // ========================================
  function initFilters() {
    // Find filter container using data-ui="filter"
    const filterContainer = document.querySelector('[data-ui="filter"]');
    if (!filterContainer) return;

    // Event delegation: handle all filter actions on the container
    filterContainer.addEventListener('click', (e) => {
      const action = e.target.closest('[data-action]')?.dataset.action;
      if (!action) return;

      if (action === 'panel-toggle') {
        // Toggle filter panel (mobile only)
        const panel = filterContainer.querySelector('[data-role="panel"]');
        const clickedBtn = e.target.closest('[data-action="panel-toggle"]');
        if (panel && clickedBtn) {
          const isOpen = panel.classList.toggle('open');
          clickedBtn.classList.toggle('active', isOpen);
          clickedBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        }
      } else if (action === 'topics-toggle') {
        // Toggle topic dropdown
        const clickedBtn = e.target.closest('[data-action="topics-toggle"]');
        const topicMenu = clickedBtn?.closest('[data-role="topic-menu"]');
        if (clickedBtn && topicMenu) {
          e.stopPropagation();
          const dropdown = topicMenu.querySelector('.topicDropdown');
          if (dropdown) {
            const isOpen = dropdown.classList.toggle('open');
            clickedBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
          }
        }
      } else if (action === 'reset') {
        // Reset all filters
        selectedTopics.clear();
        const sortSelect = filterContainer.querySelector('[data-role="sort"]');
        const searchInput = filterContainer.querySelector('[data-role="search"]');
        const topicMenu = filterContainer.querySelector('[data-role="topic-menu"]');
        
        if (sortSelect) sortSelect.value = 'new';
        if (searchInput) searchInput.value = '';
        if (topicMenu) {
          topicMenu.querySelectorAll('input[type="checkbox"]').forEach((cb, i) => {
            cb.checked = (i === 0); // Only "all" checked
          });
        }
        updateTopicSummary(filterContainer);
        triggerFilter();
      }
    });

    // Handle topic item selection
    filterContainer.addEventListener('click', (e) => {
      const topicItem = e.target.closest('[data-role="topic-item"]');
      if (!topicItem) return;
      
      e.stopPropagation();
      const tag = topicItem.dataset.tag;
      const checkbox = topicItem.querySelector('input[type="checkbox"]');
      const topicMenu = filterContainer.querySelector('[data-role="topic-menu"]');
      
      if (!topicMenu) return;
      
      if (tag === '__ALL__') {
        selectedTopics.clear();
        topicMenu.querySelectorAll('input[type="checkbox"]').forEach(cb => {
          cb.checked = (cb.closest('[data-role="topic-item"]')?.dataset.tag === '__ALL__');
        });
      } else {
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
          selectedTopics.add(tag);
        } else {
          selectedTopics.delete(tag);
        }
        // Uncheck "all" if specific topics selected
        const allCheckbox = topicMenu.querySelector('[data-tag="__ALL__"] input');
        if (allCheckbox) allCheckbox.checked = selectedTopics.size === 0;
      }
      
      updateTopicSummary(filterContainer);
      triggerFilter();
    });

    // Sort change
    const sortSelect = filterContainer.querySelector('[data-role="sort"]');
    if (sortSelect) {
      sortSelect.addEventListener('change', triggerFilter);
    }

    // Search with debounce
    const searchInput = filterContainer.querySelector('[data-role="search"]');
    if (searchInput) {
      let searchTimeout;
      searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(triggerFilter, 300);
      });
    }

    // Close topic dropdown on outside click
    document.addEventListener('click', (e) => {
      const topicMenu = filterContainer.querySelector('[data-role="topic-menu"]');
      if (!topicMenu) return;
      
      const dropdown = topicMenu.querySelector('.topicDropdown');
      const topicBtn = topicMenu.querySelector('[data-action="topics-toggle"]');
      
      if (dropdown && topicBtn && !dropdown.contains(e.target) && !topicBtn.contains(e.target)) {
        dropdown.classList.remove('open');
        topicBtn.setAttribute('aria-expanded', 'false');
      }
    });
  }

  function updateTopicSummary(filterContainer) {
    // Find topic summary within the filter container (or document if container not provided)
    const container = filterContainer || document;
    const topicSummary = container.querySelector('[data-role="count"]');
    if (!topicSummary) return;
    
    if (selectedTopics.size === 0) {
      topicSummary.textContent = 'all topics';
    } else if (selectedTopics.size === 1) {
      topicSummary.textContent = '#' + Array.from(selectedTopics)[0];
    } else {
      topicSummary.textContent = '#' + Array.from(selectedTopics)[0] + ' +' + (selectedTopics.size - 1);
    }
  }

  function triggerFilter() {
    currentPage = 1;
    hasMore = true;
    
    // Determine post type from page
    let postType = 'essay';
    if (essayGrid) postType = essayGrid.dataset.postType || 'essay';
    if (jotList) postType = jotList.dataset.postType || 'jotting';
    
    // For homepage (or any page that renders BOTH sections), filter both sections
    const hasBoth = !!(essayGrid && jotList);
    if (hasBoth || document.body.classList.contains('home') || document.body.classList.contains('front-page')) {
      applyFilter('essay', true);
      applyFilter('jotting', true);
    } else {
      applyFilter(postType, true);
    }
  }

  function applyFilter(postType, replace = false) {
    // If AJAX is blocked (WAF/maintenance/non-JSON), fall back to filtering what we already have.
    if (ajaxDisabled) {
      filterDom(postType, replace);
      return;
    }
    filterContent(postType, replace);
  }

  function filterDom(postType, replace = false) {
    const container = postType === 'essay' ? essayGrid : jotList;
    if (!container) return;

    // Only support "replace" mode for the fallback (no infinite scroll).
    if (!replace) return;

    const source = postType === 'essay' ? (initialEssayItems || []) : (initialJotItems || []);
    const topics = Array.from(selectedTopics);
    // Find filter container to get search and sort elements
    const filterContainer = document.querySelector('[data-ui="filter"]');
    const searchInput = filterContainer?.querySelector('[data-role="search"]');
    const sortSelect = filterContainer?.querySelector('[data-role="sort"]');
    const search = (searchInput?.value || '').trim().toLowerCase();
    const sort = sortSelect?.value || 'new';

    const filtered = source.filter((el) => {
      if (!(el instanceof Element)) return false;

      // Topic filter
      if (topics.length) {
        const tags = (el.getAttribute('data-tags') || '').split(',').map(s => s.trim()).filter(Boolean);
        const hasAny = topics.some(t => tags.includes(t));
        if (!hasAny) return false;
      }

      // Search filter (title + subtitle/dek/text)
      if (search) {
        const title = (el.getAttribute('data-title') || '').toLowerCase();
        const dek = (el.getAttribute('data-dek') || el.getAttribute('data-text') || '').toLowerCase();
        if (!title.includes(search) && !dek.includes(search)) return false;
      }

      return true;
    });

    // Sort (works on loaded items only)
    const byDate = (a, b) => {
      const da = (a.getAttribute('data-date') || '');
      const db = (b.getAttribute('data-date') || '');
      // YYYY-MM-DD string compares correctly
      if (da === db) return 0;
      return da < db ? -1 : 1;
    };
    const byTitle = (a, b) => {
      const ta = (a.getAttribute('data-title') || '').toLowerCase();
      const tb = (b.getAttribute('data-title') || '').toLowerCase();
      return ta.localeCompare(tb);
    };

    if (sort === 'old') filtered.sort(byDate);
    else if (sort === 'title') filtered.sort(byTitle);
    else filtered.sort((a, b) => -byDate(a, b)); // newest

    // Render
    container.innerHTML = '';
    if (!filtered.length) {
      container.innerHTML = `<p class="no-posts">No ${postType === 'essay' ? 'essays' : 'jottings'} match.</p>`;
    } else {
      const frag = document.createDocumentFragment();
      filtered.forEach(el => frag.appendChild(el));
      container.appendChild(frag);
    }

    // Update counts on homepage
    if (postType === 'essay') {
      const countEl = document.getElementById('essayCountShown');
      const labelEl = document.getElementById('essayLabel');
      if (countEl) countEl.textContent = String(filtered.length);
      if (labelEl) labelEl.textContent = filtered.length === 1 ? 'essay' : 'essays';
    }
    if (postType === 'jotting') {
      const countEl = document.getElementById('jotCountShown');
      const labelEl = document.getElementById('jotLabel');
      if (countEl) countEl.textContent = String(filtered.length);
      if (labelEl) labelEl.textContent = filtered.length === 1 ? 'jotting' : 'jottings';
    }

    // Ensure visibility
    initScrollReveal();
    initParallax();
  }

  function filterContent(postType, replace = false) {
    if (isLoading) return;
    isLoading = true;
    
    if (infiniteLoader) infiniteLoader.classList.remove('hidden');

    const formData = new FormData();
    formData.append('action', 'kunaal_filter');
    // Only send nonce if present; backend allows missing nonce for backwards-compat.
    const nonce = window.kunaalTheme?.nonce;
    if (nonce) formData.append('nonce', nonce);
    // Find filter container to get search and sort elements
    const filterContainer = document.querySelector('[data-ui="filter"]');
    const searchInput = filterContainer?.querySelector('[data-role="search"]');
    const sortSelect = filterContainer?.querySelector('[data-role="sort"]');
    
    formData.append('post_type', postType);
    formData.append('topics', Array.from(selectedTopics));
    formData.append('sort', sortSelect?.value || 'new');
    formData.append('search', searchInput?.value || '');
    formData.append('page', currentPage);
    formData.append('per_page', 12);

    fetch(window.kunaalTheme?.ajaxUrl || '/wp-admin/admin-ajax.php', {
      method: 'POST',
      body: formData
    })
    .then(async (res) => {
      const contentType = res.headers.get('content-type') || '';
      const text = await res.text();
      if (!contentType.includes('application/json')) {
        // Managed hosts/plugins sometimes return HTML (maintenance/WAF) or "0".
        throw new Error('Non-JSON response from admin-ajax (' + contentType + '): ' + text.slice(0, 200));
      }
      return JSON.parse(text);
    })
    .then(data => {
      if (data.success) {
        renderPosts(data.data.posts, postType, replace);
        hasMore = currentPage < data.data.pages;
        currentPage++;
        
        // Update counts on homepage
        if (postType === 'essay') {
          const countEl = document.getElementById('essayCountShown');
          const labelEl = document.getElementById('essayLabel');
          if (countEl) countEl.textContent = data.data.total;
          if (labelEl) labelEl.textContent = data.data.total === 1 ? 'essay' : 'essays';
        }
        if (postType === 'jotting') {
          const countEl = document.getElementById('jotCountShown');
          const labelEl = document.getElementById('jotLabel');
          if (countEl) countEl.textContent = data.data.total;
          if (labelEl) labelEl.textContent = data.data.total === 1 ? 'jotting' : 'jottings';
        }
      }
      // If request fails, keep existing content and surface a useful error.
      if (!data.success) {
        if (window.kunaalTheme?.debug) {
          console.error('Filter request failed:', data?.data || data);
        }
        ajaxDisabled = true;
        filterDom(postType, replace);
      }
    })
    .catch(err => {
      if (window.kunaalTheme?.debug) {
        console.error('Filter error:', err);
      }
      ajaxDisabled = true;
      filterDom(postType, replace);
      // Announce to screen readers + keep UI responsive
      const announcer = document.getElementById('announcer');
      if (announcer) announcer.textContent = 'Filtering failed. Please refresh the page.';
    })
    .finally(() => {
      isLoading = false;
    if (infiniteLoader) {
      // If AJAX is disabled (fallback mode), we don't support infinite scroll.
      infiniteLoader.classList.add('hidden');
    }
    });
  }

  function renderPosts(posts, postType, replace) {
    const container = postType === 'essay' ? essayGrid : jotList;
    if (!container) return;

    if (replace) {
      container.innerHTML = '';
    }

    if (posts.length === 0 && replace) {
      container.innerHTML = `<p class="no-posts">No ${postType === 'essay' ? 'essays' : 'jottings'} yet.</p>`;
      return;
    }

    posts.forEach((post, index) => {
      const el = postType === 'essay' ? createEssayCard(post) : createJottingRow(post);
      container.insertAdjacentHTML('beforeend', el);
      
      // Set transition delay via CSS custom property (replaces nth-child())
      const lastChild = container.lastElementChild;
      if (lastChild) {
        const delays = postType === 'essay' 
          ? [0, 60, 120, 180, 50, 110, 170, 230]
          : [0, 50, 100, 150];
        const delay = delays[index % delays.length] || 0;
        lastChild.style.setProperty('--card-delay', `${delay}ms`);
        if (postType === 'jotting') {
          lastChild.style.setProperty('--jotting-delay', `${delay}ms`);
        }
      }
    });

    // Re-init reveals for new content
    initScrollReveal();
    initParallax();
  }

  // Set transition delays for server-rendered cards (replaces nth-child())
  function setCardDelays() {
    const essayCards = document.querySelectorAll('#essayGrid .card, #essayGridFallback .card');
    const essayDelays = [0, 60, 120, 180, 50, 110, 170, 230];
    essayCards.forEach((card, index) => {
      const delay = essayDelays[index % essayDelays.length] || 0;
      card.style.setProperty('--card-delay', `${delay}ms`);
    });

    const jottingRows = document.querySelectorAll('#jotList .jRow, #jotListFallback .jRow');
    const jottingDelays = [0, 50, 100, 150];
    jottingRows.forEach((row, index) => {
      const delay = jottingDelays[index % jottingDelays.length] || 0;
      row.style.setProperty('--jotting-delay', `${delay}ms`);
    });
  }

  function createEssayCard(post) {
    // Tags go on separate line
    const tagsHtml = post.tags.slice(0, 2).map((t, i) => 
      `${i > 0 ? '<span class="dot"></span>' : ''}<span class="tag">#${escapeHtml(t)}</span>`
    ).join('');
    
    const imageHtml = post.image 
      ? `<img src="${escapeHtml(post.image)}" alt="${escapeHtml(post.title)}" loading="lazy" />`
      : `<svg viewBox="0 0 400 500" fill="none"><rect width="400" height="500" fill="url(#g${post.id})"/><defs><linearGradient id="g${post.id}" x1="0%" y1="0%" x2="100%" y2="100%"><stop offset="0%" style="stop-color:rgba(30,90,255,0.08)"/><stop offset="100%" style="stop-color:rgba(11,18,32,0.02)"/></linearGradient></defs></svg>`;

    return `
      <li><a href="${escapeHtml(post.url)}" class="card">
        <div class="media" data-parallax="true">
          ${imageHtml}
          <div class="scrim"></div>
        </div>
        <div class="overlay">
          <h3 class="tTitle">${escapeHtml(post.title)}</h3>
          <div class="details">
            <p class="meta">
              <span>${escapeHtml(post.date)}</span>
              ${post.readTime ? `<span class="dot"></span><span>${post.readTime} min</span>` : ''}
            </p>
            ${tagsHtml ? `<p class="metaTags">${tagsHtml}</p>` : ''}
            ${post.subtitle ? `<p class="dek">${escapeHtml(post.subtitle)}</p>` : ''}
          </div>
        </div>
      </a></li>
    `;
  }

  function createJottingRow(post) {
    const tagsHtml = post.tags.map(t => `<span>#${escapeHtml(t)}</span>`).join('');
    
    return `
      <li><a href="${escapeHtml(post.url)}" class="jRow">
        <span class="jDate">${escapeHtml(post.dateShort)}</span>
        <div class="jContent">
          <h3 class="jTitle">${escapeHtml(post.title)}</h3>
          ${post.subtitle ? `<p class="jText">${escapeHtml(post.subtitle)}</p>` : ''}
          ${tagsHtml ? `<div class="jTags">${tagsHtml}</div>` : ''}
        </div>
      </a></li>
    `;
  }

  function escapeHtml(str) {
    if (!str) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
  }

  // ========================================
  // INFINITE SCROLL
  // ========================================
  function initInfiniteScroll() {
    if (!infiniteLoader) return;
    
    const observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting && hasMore && !isLoading) {
        const postType = essayGrid?.dataset.postType || jotList?.dataset.postType || 'essay';
        filterContent(postType, false);
      }
    }, { rootMargin: '200px' });
    
    observer.observe(infiniteLoader);
  }

  // ========================================
  // UNIFIED ACTION DOCK - Share, Subscribe, Download
  // ========================================
  function initDocks() {
    if (!actionDock) return;

    // Track active panel
    let activePanel = null;
    
    // Update button positions when share panel opens
    function updateButtonPositions() {
      if (!sharePanel) return;
      
      const shareOpen = sharePanel.classList.contains('open');
      
      if (shareOpen) {
        // Calculate share panel height
        const sharePanelHeight = sharePanel.offsetHeight || 250;
        const shiftAmount = sharePanelHeight + 12; // Panel height + gap
        
        // BOTH buttons shift by the SAME amount
        // The gap between them is maintained by the actionDock's gap: 12px
        if (subscribeToggle) {
          subscribeToggle.style.transform = `translateY(${shiftAmount}px)`;
          subscribeToggle.style.transition = 'transform 300ms cubic-bezier(0.34,1.56,0.64,1)';
        }
        
        if (downloadButton) {
          downloadButton.style.transform = `translateY(${shiftAmount}px)`;
          downloadButton.style.transition = 'transform 300ms cubic-bezier(0.34,1.56,0.64,1)';
        }
      } else {
        // Reset positions
        if (subscribeToggle) subscribeToggle.style.transform = '';
        if (downloadButton) downloadButton.style.transform = '';
      }
    }

    // Helper to close all panels
    function closeAllPanels() {
      if (sharePanel) sharePanel.classList.remove('open');
      if (subscribePanel) subscribePanel.classList.remove('open');
      if (shareToggle) shareToggle.classList.remove('active');
      if (subscribeToggle) subscribeToggle.classList.remove('active');
      activePanel = null;
      updateButtonPositions();
    }

    // Share Toggle
    if (shareToggle && sharePanel) {
      shareToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        
        if (activePanel === 'share') {
          // Close if already open
          closeAllPanels();
        } else {
          // Close others, open share
          if (subscribePanel) subscribePanel.classList.remove('open');
          if (subscribeToggle) subscribeToggle.classList.remove('active');
          
          sharePanel.classList.add('open');
          shareToggle.classList.add('active');
          activePanel = 'share';
          
          // Wait for panel to render then shift buttons
          setTimeout(updateButtonPositions, 50);
        }
      });
    }

    // Subscribe Toggle
    if (subscribeToggle && subscribePanel) {
      subscribeToggle.addEventListener('click', (e) => {
        e.stopPropagation();
        
        if (activePanel === 'subscribe') {
          // Close if already open
          closeAllPanels();
        } else {
          // Close others, open subscribe
          closeAllPanels();
          subscribePanel.classList.add('open');
          subscribeToggle.classList.add('active');
          activePanel = 'subscribe';
        }
      });
    }

    // Download Button
    if (downloadButton) {
      downloadButton.addEventListener('click', (e) => {
        e.stopPropagation();
        // Close any open panels first
        closeAllPanels();
        // Trigger PDF generation (will be handled by custom PDF system)
        generatePDF();
      });
    }

    // Close panels when clicking outside
    document.addEventListener('click', (e) => {
      if (activePanel && !actionDock.contains(e.target) && 
          !sharePanel?.contains(e.target) && 
          !subscribePanel?.contains(e.target)) {
        closeAllPanels();
      }
    });

    // Share buttons functionality
    document.querySelectorAll('[data-share]').forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        
        const platform = btn.dataset.share;
        const pageUrl = window.location.href;
        const encodedUrl = encodeURIComponent(pageUrl);
        const rawTitle = document.title;
        
        // Use custom share text if available
        const twitterHandle = window.kunaalTheme?.twitterHandle || '';
        
        let shareUrl = null;

        switch (platform) {
          case 'linkedin':
            // LinkedIn share URL - relies on Open Graph meta tags
            shareUrl = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodedUrl;
            break;
          case 'x':
            // X/Twitter with proper text formatting
            const tweetText = rawTitle + (twitterHandle ? ' via @' + twitterHandle : '');
            shareUrl = 'https://twitter.com/intent/tweet?url=' + encodedUrl + '&text=' + encodeURIComponent(tweetText);
            break;
          case 'facebook':
            shareUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + encodedUrl;
            break;
          case 'reddit':
            shareUrl = 'https://www.reddit.com/submit?url=' + encodedUrl + '&title=' + encodeURIComponent(rawTitle);
            break;
          case 'viber':
            // Viber share (mobile app deep-link)
            shareUrl = 'viber://forward?text=' + encodeURIComponent(rawTitle + ' ' + pageUrl);
            break;
          case 'whatsapp':
            shareUrl = 'https://wa.me/?text=' + encodeURIComponent(rawTitle + ' ' + pageUrl);
            break;
          case 'email':
            window.location.href = 'mailto:?subject=' + encodeURIComponent(rawTitle) + '&body=' + encodeURIComponent(rawTitle + '\n\n' + pageUrl);
            return;
          case 'copy':
            navigator.clipboard.writeText(pageUrl).then(() => {
              const tip = btn.querySelector('.tip');
              if (tip) {
                const originalText = tip.textContent;
                tip.textContent = 'Copied!';
                setTimeout(() => { tip.textContent = originalText; }, 1500);
              }
            });
            return;
          default:
            // Unknown platform - do nothing
            return;
        }

        if (shareUrl) {
          // Open in new window - must be synchronous with user action to avoid popup blockers
          const win = window.open(shareUrl, '_blank', 'width=600,height=500,menubar=no,toolbar=no,resizable=yes,scrollbars=yes');
          if (win) {
            win.focus();
          } else {
            // Fallback: navigate to the URL directly
            window.location.href = shareUrl;
          }
        }
      });
    });
  }

  // ========================================
  // PDF GENERATION (Custom eBook-style system)
  // ========================================
  function generatePDF() {
    // Get current post ID from body class or data attribute
    const bodyClasses = document.body.className;
    const postIdMatch = bodyClasses.match(/postid-(\d+)/);
    
    if (!postIdMatch) {
      if (window.kunaalTheme?.debug) {
        console.error('Could not determine post ID for PDF generation');
      }
      return;
    }
    
    const postId = postIdMatch[1];
    // Generate nonce for PDF download (use existing nonce from kunaalTheme or create new one)
    const nonce = window.kunaalTheme?.pdfNonce || '';
    const pdfUrl = window.location.origin + '/?kunaal_pdf=1&post_id=' + postId + '&nonce=' + encodeURIComponent(nonce);
    
    // Open PDF in new window
    window.open(pdfUrl, '_blank', 'noopener');
  }

  // ========================================
  // OLD SHARE/SUBSCRIBE FUNCTIONS (Deprecated - keeping for reference)
  // ========================================
  function initShare() {
    // Deprecated - now handled by initDocks()
    return;
  }

  function initSubscribe() {
    // Deprecated - now handled by initDocks()
    return;
  }

  // ========================================
  // TABLE OF CONTENTS (On This Page)
  // ========================================
  function initTOC() {
    if (!tocList) return;
    
    const prose = document.getElementById('articleProse');
    if (!prose) return;

    const headings = prose.querySelectorAll('h2[id], .sectionHead h2[id]');
    if (headings.length === 0) {
      // Hide rail if no headings
      const rail = document.getElementById('articleRail');
      if (rail) rail.style.display = 'none';
      return;
    }

    let counter = 1;
    headings.forEach(h => {
      const li = document.createElement('li');
      const a = document.createElement('a');
      a.href = '#' + h.id;
      
      const spanText = document.createElement('span');
      spanText.textContent = h.textContent;
      
      const spanNum = document.createElement('span');
      spanNum.className = 'num';
      spanNum.textContent = String(counter).padStart(2, '0');
      
      a.appendChild(spanText);
      a.appendChild(spanNum);
      li.appendChild(a);
      tocList.appendChild(li);
      
      counter++;
    });
  }

  // ========================================
  // SCROLLY (Interactive steps)
  // ========================================
  function initScrolly() {
    const steps = document.querySelectorAll('.step');
    const scrollyTitle = document.getElementById('scrollyTitle');
    const scrollyNote = document.getElementById('scrollyNote');
    
    if (!steps.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          steps.forEach(s => s.classList.remove('active'));
          entry.target.classList.add('active');
          
          if (scrollyTitle) scrollyTitle.textContent = entry.target.dataset.title || '';
          if (scrollyNote) scrollyNote.textContent = entry.target.dataset.note || '';
        }
      });
    }, {
      threshold: 0.6,
      rootMargin: '-20% 0px -40% 0px'
    });

    steps.forEach(step => observer.observe(step));
  }

  // ========================================
  // CODE BLOCK COPY
  // ========================================
  function initCodeBlocks() {
    document.querySelectorAll('.codeBlock .codeHead button, .wp-block-kunaal-code .codeHead button').forEach(btn => {
      btn.addEventListener('click', () => {
        const pre = btn.closest('.codeBlock, .wp-block-kunaal-code').querySelector('pre');
        if (pre) {
          navigator.clipboard.writeText(pre.textContent).then(() => {
            btn.textContent = 'Copied!';
            setTimeout(() => { btn.textContent = 'Copy'; }, 1500);
          });
        }
      });
    });
  }

  // ========================================
  // ACCORDIONS
  // ========================================
  function initAccordions() {
    document.querySelectorAll('.accordion, .wp-block-kunaal-accordion').forEach(acc => {
      const summary = acc.querySelector('summary');
      if (summary) {
        summary.addEventListener('click', (e) => {
          // Default behavior handles open/close
        });
      }
    });
  }

  // ========================================
  // INLINE FORMAT TOUCH SUPPORT
  // ========================================
  function initInlineFormatTouch() {
    // Touch support for sidenotes, definitions, data-refs
    const touchElements = document.querySelectorAll('.kunaal-sidenote, .kunaal-definition, .kunaal-data-ref[data-source]');
    
    touchElements.forEach(function(el) {
      // Find tooltip content element
      const tooltip = el.querySelector('.tooltip-content, .definition-content, .data-ref-content');
      
      el.addEventListener('click', function(e) {
        // If already active, close it
        if (this.classList.contains('active')) {
          this.classList.remove('active');
          if (tooltip) {
            tooltip.setAttribute('aria-hidden', 'true');
          }
          return;
        }
        
        // Close any other active tooltips
        touchElements.forEach(function(other) {
          other.classList.remove('active');
          const otherTooltip = other.querySelector('.tooltip-content, .definition-content, .data-ref-content');
          if (otherTooltip) {
            otherTooltip.setAttribute('aria-hidden', 'true');
          }
        });
        
        // Open this one
        this.classList.add('active');
        if (tooltip) {
          tooltip.setAttribute('aria-hidden', 'false');
        }
        e.stopPropagation();
      });
    });
    
    // Close tooltips when clicking elsewhere
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.kunaal-sidenote, .kunaal-definition, .kunaal-data-ref')) {
        touchElements.forEach(function(el) {
          el.classList.remove('active');
          const tooltip = el.querySelector('.tooltip-content, .definition-content, .data-ref-content');
          if (tooltip) {
            tooltip.setAttribute('aria-hidden', 'true');
          }
        });
      }
    });
  }

  // ========================================
  // INITIALIZE
  // ========================================
  function init() {
    try {
      cacheViewport();
      
      window.addEventListener('scroll', () => {
        lastY = window.scrollY || 0;
        requestTick();
      }, { passive: true });

      window.addEventListener('resize', () => {
        cacheViewport();
      });

      initNav();
      initAvatar();
      initFilters();
      initParallax();
      initScrollReveal();
      initInfiniteScroll();
      initDocks();
      initTOC();
      initScrolly();
      initCodeBlocks();
      initAccordions();
      initInlineFormatTouch();
      initSubscribeForms(); // Initialize built-in subscribe flow if subscribe forms are present
      // About page functionality is handled by about-page.js

      // Initial scroll effect
      lastY = window.scrollY || 0;
      requestTick();
    } catch (e) {
      if (window.kunaalTheme?.debug) {
        console.error('Theme init failed; disabling js-only reveals', e);
      }
      document.documentElement.classList.remove('js-ready');
    }
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
