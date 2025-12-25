/**
 * Inline Annotation Block - View Script
 * Handles mobile tap behavior for annotations
 */
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const annotations = document.querySelectorAll('.wp-block-kunaal-annotation');
        
        annotations.forEach(function(annotation) {
            const text = annotation.querySelector('.annotation-text');
            const note = annotation.querySelector('.annotation-note');
            
            if (!text || !note) return;
            
            // Mobile tap handling
            text.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    
                    // Close all other open annotations
                    annotations.forEach(function(a) {
                        if (a !== annotation) {
                            a.classList.remove('is-active');
                        }
                    });
                    
                    annotation.classList.toggle('is-active');
                }
            });
        });
        
        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.wp-block-kunaal-annotation')) {
                annotations.forEach(function(a) {
                    a.classList.remove('is-active');
                });
            }
        });
    });
})();

