/**
 * Kunaal Theme - Complete JavaScript
 * All interactions: scroll effects, filters, sharing, reveals, parallax
 */
(function() {
  'use strict';

  // ========================================
  // DOM REFERENCES
  // ========================================
  const progressFill = document.getElementById('progressFill');
  const navToggle = document.getElementById('navToggle');
  const nav = document.getElementById('nav');
  const avatar = document.getElementById('avatar');
  const avatarImg = document.getElementById('avatarImg');
  const filterBtn = document.getElementById('filterBtn');
  const filterPanel = document.getElementById('filterPanel');
  const topicBtn = document.getElementById('topicBtn');
  const topicMenu = document.getElementById('topicMenu');
  const topicSummary = document.getElementById('topicSummary');
  const sortSelect = document.getElementById('sortSelect');
  const searchInput = document.getElementById('searchInput');
  const resetBtn = document.getElementById('resetBtn');
  const essayGrid = document.getElementById('essayGrid');
  const jotList = document.getElementById('jotList');
  
  // New unified dock system
  const actionDock = document.getElementById('actionDock');
  const shareToggle = document.getElementById('shareToggle');
  const subscribeToggle = document.getElementById('subscribeToggle');
  const downloadButton = document.getElementById('downloadButton');
  const sharePanel = document.getElementById('sharePanel');
  const subscribePanel = document.getElementById('subscribePanel');
  
  const tocList = document.getElementById('tocList');
  const infiniteLoader = document.getElementById('infiniteLoader');

  // State
  let lastY = 0;
  let ticking = false;
  let cachedDocH = 1;
  let selectedTopics = new Set();
  let currentPage = 1;
  let isLoading = false;
  let hasMore = true;

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
    document.body.style.setProperty('--p', p.toFixed(4));

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

    revealObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
        } else {
          // Bidirectional - remove when scrolling back up
          if (entry.boundingClientRect.top > 0) {
            entry.target.classList.remove('revealed');
          }
        }
      });
    }, {
      rootMargin: '0px 0px -60px 0px',
      threshold: 0.1
    });
    
    document.querySelectorAll('.card, .jRow, .reveal, .reveal-left, .reveal-right, .sectionHead').forEach(el => {
      revealObserver.observe(el);
    });
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
      navToggle.setAttribute('aria-expanded', isOpen);
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
  // FILTERS
  // ========================================
  function initFilters() {
    // Filter toggle button (right side)
    if (filterBtn && filterPanel) {
      filterBtn.addEventListener('click', () => {
        const isOpen = filterPanel.classList.toggle('open');
        filterBtn.classList.toggle('active', isOpen);
        filterBtn.setAttribute('aria-expanded', isOpen);
      });
    }

    // Topics dropdown
    if (topicBtn && topicMenu) {
      topicBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        const isOpen = topicMenu.classList.toggle('open');
        topicBtn.setAttribute('aria-expanded', isOpen);
      });

      // Topic options
      topicMenu.querySelectorAll('.topicOpt').forEach(opt => {
        opt.addEventListener('click', (e) => {
          e.stopPropagation();
          const tag = opt.dataset.tag;
          const checkbox = opt.querySelector('input[type="checkbox"]');
          
          if (tag === '__ALL__') {
            selectedTopics.clear();
            topicMenu.querySelectorAll('input[type="checkbox"]').forEach(cb => {
              cb.checked = (cb.closest('.topicOpt').dataset.tag === '__ALL__');
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
          
          updateTopicSummary();
          triggerFilter();
        });
      });

      // Close dropdown on outside click
      document.addEventListener('click', (e) => {
        if (!topicMenu.contains(e.target) && !topicBtn.contains(e.target)) {
          topicMenu.classList.remove('open');
          topicBtn.setAttribute('aria-expanded', 'false');
        }
      });
    }

    // Sort
    if (sortSelect) {
      sortSelect.addEventListener('change', triggerFilter);
    }

    // Search with debounce
    if (searchInput) {
      let searchTimeout;
      searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(triggerFilter, 300);
      });
    }

    // Reset
    if (resetBtn) {
      resetBtn.addEventListener('click', () => {
        selectedTopics.clear();
        if (sortSelect) sortSelect.value = 'new';
        if (searchInput) searchInput.value = '';
        topicMenu?.querySelectorAll('input[type="checkbox"]').forEach((cb, i) => {
          cb.checked = (i === 0); // Only "all" checked
        });
        updateTopicSummary();
        triggerFilter();
      });
    }
  }

  function updateTopicSummary() {
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
    
    // For homepage, filter both sections
    if (document.body.classList.contains('home')) {
      filterContent('essay', true);
      filterContent('jotting', true);
    } else {
      filterContent(postType, true);
    }
  }

  function filterContent(postType, replace = false) {
    if (isLoading) return;
    isLoading = true;
    
    if (infiniteLoader) infiniteLoader.classList.remove('hidden');

    const formData = new FormData();
    formData.append('action', 'kunaal_filter');
    formData.append('nonce', window.kunaalTheme?.nonce || '');
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
    .then(res => res.json())
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
    })
    .catch(err => console.error('Filter error:', err))
    .finally(() => {
      isLoading = false;
      if (infiniteLoader) {
        infiniteLoader.classList.toggle('hidden', !hasMore);
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

    posts.forEach(post => {
      const el = postType === 'essay' ? createEssayCard(post) : createJottingRow(post);
      container.insertAdjacentHTML('beforeend', el);
    });

    // Re-init reveals for new content
    initScrollReveal();
    initParallax();
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
      <a href="${escapeHtml(post.url)}" class="card" role="listitem">
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
      </a>
    `;
  }

  function createJottingRow(post) {
    const tagsHtml = post.tags.map(t => `<span>#${escapeHtml(t)}</span>`).join('');
    
    return `
      <a href="${escapeHtml(post.url)}" class="jRow" role="listitem">
        <span class="jDate">${escapeHtml(post.dateShort)}</span>
        <div class="jContent">
          <h3 class="jTitle">${escapeHtml(post.title)}</h3>
          ${post.subtitle ? `<p class="jText">${escapeHtml(post.subtitle)}</p>` : ''}
          ${tagsHtml ? `<div class="jTags">${tagsHtml}</div>` : ''}
        </div>
      </a>
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
      btn.addEventListener('click', () => {
        const platform = btn.dataset.share;
        const url = encodeURIComponent(window.location.href);
        const rawTitle = document.title;
        
        // Use custom share text if available
        const shareText = window.kunaalTheme?.shareText || '';
        const twitterHandle = window.kunaalTheme?.twitterHandle || '';
        const authorName = window.kunaalTheme?.authorName || '';
        
        // Build share text with "by Author" attribution
        let titleWithAttribution = rawTitle;
        if (authorName) {
          titleWithAttribution = rawTitle + ' by ' + authorName;
        }
        const fullShareText = shareText ? shareText + ' ' + titleWithAttribution : titleWithAttribution;
        const title = encodeURIComponent(fullShareText);
        
        let shareUrl;

        switch (platform) {
          case 'linkedin':
            const linkedInText = titleWithAttribution;
            const linkedinUrl = window.kunaalTheme?.linkedinUrl || '';
            let finalLinkedInUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            finalLinkedInUrl += `&summary=${encodeURIComponent(linkedInText)}`;
            shareUrl = finalLinkedInUrl;
            break;
          case 'x':
            const handleText = twitterHandle ? ` @${twitterHandle}` : '';
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}${encodeURIComponent(handleText)}`;
            break;
          case 'whatsapp':
            shareUrl = `https://wa.me/?text=${title}%20${url}`;
            break;
          case 'email':
            shareUrl = `mailto:?subject=${encodeURIComponent(rawTitle)}&body=${title}%20${url}`;
            break;
          case 'copy':
            navigator.clipboard.writeText(window.location.href).then(() => {
              const label = btn.querySelector('.shareLabel');
              if (label) {
                const originalText = label.textContent;
                label.textContent = 'Copied!';
                setTimeout(() => { label.textContent = originalText; }, 1500);
              }
            });
            return;
        }

        if (shareUrl) {
          window.open(shareUrl, '_blank', 'noopener,width=600,height=400');
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
      console.error('Could not determine post ID for PDF generation');
      return;
    }
    
    const postId = postIdMatch[1];
    const pdfUrl = window.location.origin + '/?kunaal_pdf=1&post_id=' + postId;
    
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
  // INITIALIZE
  // ========================================
  function init() {
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

    // Initial scroll effect
    lastY = window.scrollY || 0;
    requestTick();
  }

  // Run on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

})();
