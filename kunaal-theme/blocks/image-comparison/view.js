/**
 * Image Comparison Block - Frontend JavaScript
 * Handles drag slider for before/after comparison
 */
(function() {
    'use strict';

    function initImageComparison(block) {
        const container = block.querySelector('.imgcmp__container');
        const beforeEl = block.querySelector('.imgcmp__before');
        const handle = block.querySelector('.imgcmp__handle');
        const orientation = block.dataset.orientation || 'horizontal';
        const isHorizontal = orientation === 'horizontal';
        
        if (!container || !beforeEl || !handle) return;

        let isDragging = false;

        function getPosition(e) {
            const rect = container.getBoundingClientRect();
            let pos;
            
            if (e.touches && e.touches.length > 0) {
                pos = isHorizontal 
                    ? e.touches[0].clientX - rect.left
                    : e.touches[0].clientY - rect.top;
            } else {
                pos = isHorizontal 
                    ? e.clientX - rect.left
                    : e.clientY - rect.top;
            }
            
            const size = isHorizontal ? rect.width : rect.height;
            let percentage = (pos / size) * 100;
            
            // Clamp between 5% and 95%
            percentage = Math.max(5, Math.min(95, percentage));
            
            return percentage;
        }

        function updatePosition(percentage) {
            if (isHorizontal) {
                beforeEl.style.width = percentage + '%';
                handle.style.left = percentage + '%';
            } else {
                beforeEl.style.height = percentage + '%';
                handle.style.top = percentage + '%';
            }
            
            handle.setAttribute('aria-valuenow', Math.round(percentage));
        }

        function startDrag(e) {
            e.preventDefault();
            isDragging = true;
            container.classList.add('is-dragging');
            
            const percentage = getPosition(e);
            updatePosition(percentage);
        }

        function doDrag(e) {
            if (!isDragging) return;
            e.preventDefault();
            
            const percentage = getPosition(e);
            updatePosition(percentage);
        }

        function endDrag() {
            if (!isDragging) return;
            isDragging = false;
            container.classList.remove('is-dragging');
        }

        // Mouse events
        handle.addEventListener('mousedown', startDrag);
        container.addEventListener('mousedown', startDrag);
        document.addEventListener('mousemove', doDrag);
        document.addEventListener('mouseup', endDrag);

        // Touch events
        handle.addEventListener('touchstart', startDrag, { passive: false });
        container.addEventListener('touchstart', startDrag, { passive: false });
        document.addEventListener('touchmove', doDrag, { passive: false });
        document.addEventListener('touchend', endDrag);
        document.addEventListener('touchcancel', endDrag);

        // Keyboard support
        handle.addEventListener('keydown', function(e) {
            const currentValue = parseFloat(handle.getAttribute('aria-valuenow')) || 50;
            let newValue = currentValue;
            
            switch (e.key) {
                case 'ArrowLeft':
                case 'ArrowUp':
                    newValue = Math.max(5, currentValue - 5);
                    e.preventDefault();
                    break;
                case 'ArrowRight':
                case 'ArrowDown':
                    newValue = Math.min(95, currentValue + 5);
                    e.preventDefault();
                    break;
                case 'Home':
                    newValue = 5;
                    e.preventDefault();
                    break;
                case 'End':
                    newValue = 95;
                    e.preventDefault();
                    break;
                default:
                    return;
            }
            
            updatePosition(newValue);
        });

        // Prevent image drag
        container.querySelectorAll('img').forEach(img => {
            img.addEventListener('dragstart', e => e.preventDefault());
        });
    }

    // Initialize all image comparison blocks
    function initAll() {
        document.querySelectorAll('.wp-block-kunaal-image-comparison').forEach(block => {
            if (block.classList.contains('is-initialized')) return;
            block.classList.add('is-initialized');
            initImageComparison(block);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }
})();

