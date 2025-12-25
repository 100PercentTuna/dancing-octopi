<?php
/**
 * Template Name: Contact Page
 * 
 * Elegant note card contact form - sophisticated and inviting
 *
 * @package Kunaal_Theme
 */

get_header();

// Get Customizer values
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$email = get_theme_mod('kunaal_contact_email', '');

// Contact-specific settings
$contact_headline = get_theme_mod('kunaal_contact_headline', 'Say hello');
$contact_intro = get_theme_mod('kunaal_contact_intro', '');
$contact_response = get_theme_mod('kunaal_contact_response_time', '');

// Social links
$linkedin = get_theme_mod('kunaal_linkedin_handle', '');
$twitter = get_theme_mod('kunaal_twitter_handle', '');
?>

<main class="contact-elegant">
    
    <div class="contact-wrapper">
        
        <!-- Main Card -->
        <div class="contact-card">
            
            <header class="contact-header">
                <h1><?php echo esc_html($contact_headline); ?></h1>
                <?php if ($contact_intro) : ?>
                <p class="contact-intro"><?php echo esc_html($contact_intro); ?></p>
                <?php endif; ?>
            </header>
            
            <form id="kunaal-contact-form" class="elegant-form" method="post">
                <?php wp_nonce_field('kunaal_contact_form', 'kunaal_contact_nonce'); ?>
                
                <div class="form-row">
                    <div class="form-field">
                        <input type="text" id="contact-name" name="contact_name" required placeholder=" ">
                        <label for="contact-name">Your name</label>
                        <span class="field-line"></span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-field">
                        <input type="email" id="contact-email" name="contact_email" required placeholder=" ">
                        <label for="contact-email">Email address</label>
                        <span class="field-line"></span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-field">
                        <input type="text" id="contact-subject" name="contact_subject" required placeholder=" ">
                        <label for="contact-subject">Subject</label>
                        <span class="field-line"></span>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-field textarea-field">
                        <textarea id="contact-message" name="contact_message" rows="5" required placeholder=" "></textarea>
                        <label for="contact-message">What's on your mind?</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="send-btn">
                        <span class="btn-text">Send message</span>
                        <span class="btn-sending">Sending...</span>
                        <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
                
                <div class="form-status"></div>
                
            </form>
            
            <?php if ($contact_response) : ?>
            <p class="response-note"><?php echo esc_html($contact_response); ?></p>
            <?php endif; ?>
            
        </div>
        
        <!-- Alternative Contact -->
        <?php if ($email || $linkedin || $twitter) : ?>
        <div class="contact-alternatives">
            <span class="alt-divider">or reach out directly</span>
            <div class="alt-links">
                <?php if ($email) : ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="alt-link" title="Email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="alt-link" target="_blank" rel="noopener" title="LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($twitter) : ?>
                <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="alt-link" target="_blank" rel="noopener" title="X / Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
    
</main>

<script>
(function() {
    var form = document.getElementById('kunaal-contact-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var btn = form.querySelector('.send-btn');
        var status = form.querySelector('.form-status');
        
        btn.classList.add('sending');
        btn.disabled = true;
        status.className = 'form-status';
        status.textContent = '';
        
        var formData = new FormData(form);
        formData.append('action', 'kunaal_contact_form');
        
        fetch(kunaalTheme.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                status.className = 'form-status success';
                status.textContent = data.data.message || 'Message sent! Thank you.';
                form.reset();
            } else {
                status.className = 'form-status error';
                status.textContent = data.data.message || 'Something went wrong. Please try again.';
            }
        })
        .catch(function() {
            status.className = 'form-status error';
            status.textContent = 'Network error. Please try again.';
        })
        .finally(function() {
            btn.classList.remove('sending');
            btn.disabled = false;
        });
    });
})();
</script>

<?php get_footer(); ?>
