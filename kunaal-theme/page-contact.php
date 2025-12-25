<?php
/**
 * Template Name: Contact Page
 * 
 * Premium contact form with floating labels
 *
 * @package Kunaal_Theme
 */

get_header();

// Get Customizer values
$email = get_theme_mod('kunaal_contact_email', '');
$linkedin = get_theme_mod('kunaal_linkedin_handle', '');
$twitter = get_theme_mod('kunaal_twitter_handle', '');

// Contact-specific settings
$contact_headline = get_theme_mod('kunaal_contact_headline', 'Say hello');
$contact_intro = get_theme_mod('kunaal_contact_intro', "I'd love to hear from you.");
$contact_response = get_theme_mod('kunaal_contact_response_time', '');
?>

<!-- Critical inline styles as fallback -->
<style>
.contact-premium { min-height: 100vh; background: linear-gradient(180deg, #FDFCFB 0%, #F9F7F4 100%); padding: 80px 24px; display: flex; align-items: center; justify-content: center; }
.contact-premium .contact-wrapper { width: 100%; max-width: 480px; }
.contact-premium .contact-card { background: #FFFFFF; border-radius: 12px; padding: 48px 40px; box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 12px 24px rgba(0,0,0,0.04); }
.contact-premium .contact-header { text-align: center; margin-bottom: 40px; }
.contact-premium .contact-headline { font-family: 'Cormorant Garamond', Georgia, serif; font-size: 36px; font-weight: 400; color: #0b1220; margin: 0 0 12px; }
.contact-premium .contact-intro { font-size: 16px; color: #6b7280; margin: 0; }
.contact-premium .contact-form { display: flex; flex-direction: column; gap: 28px; }
.contact-premium .form-field { position: relative; }
.contact-premium .form-field input, .contact-premium .form-field textarea { width: 100%; padding: 16px 0; border: none; border-bottom: 1px solid #E5E5E5; background: transparent; font-size: 16px; font-family: inherit; color: #0b1220; box-sizing: border-box; }
.contact-premium .form-field input:focus, .contact-premium .form-field textarea:focus { outline: none; border-bottom: 2px solid #1E5AFF; }
.contact-premium .form-field label { position: absolute; left: 0; top: 16px; font-size: 16px; color: #9CA3AF; pointer-events: none; transition: all 0.3s ease; }
.contact-premium .form-field input:focus ~ label, .contact-premium .form-field input:not(:placeholder-shown) ~ label { transform: translateY(-24px) scale(0.75); color: #1E5AFF; }
.contact-premium .textarea-field { margin-top: 8px; }
.contact-premium .textarea-field textarea { min-height: 140px; padding: 16px; border: 1px solid #E5E5E5; border-radius: 8px; resize: vertical; }
.contact-premium .textarea-field textarea:focus { border-color: #1E5AFF; }
.contact-premium .textarea-field label { left: 16px; top: 16px; background: white; padding: 0 4px; }
.contact-premium .textarea-field textarea:focus ~ label, .contact-premium .textarea-field textarea:not(:placeholder-shown) ~ label { transform: translateY(-24px) scale(0.75); color: #1E5AFF; }
.contact-premium .submit-btn { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px 32px; margin-top: 8px; background: #0b1220; color: white; border: none; border-radius: 8px; font-size: 15px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; }
.contact-premium .submit-btn:hover { background: #1E5AFF; transform: translateY(-2px); }
.contact-premium .submit-btn .btn-loading, .contact-premium .submit-btn .btn-success { display: none; }
.contact-premium .submit-btn.is-loading .btn-text, .contact-premium .submit-btn.is-loading .btn-arrow { display: none; }
.contact-premium .submit-btn.is-loading .btn-loading { display: inline; }
.contact-premium .submit-btn.is-success { background: #059669; }
.contact-premium .submit-btn.is-success .btn-text, .contact-premium .submit-btn.is-success .btn-arrow, .contact-premium .submit-btn.is-success .btn-loading { display: none; }
.contact-premium .submit-btn.is-success .btn-success { display: inline; }
.contact-premium .contact-alternatives { margin-top: 40px; text-align: center; }
.contact-premium .alt-divider { display: block; font-size: 13px; color: #6b7280; margin-bottom: 20px; }
.contact-premium .alt-icons { display: flex; justify-content: center; gap: 16px; }
.contact-premium .alt-icon-link { display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; border: 1px solid #E5E5E5; color: #6b7280; transition: all 0.3s ease; }
.contact-premium .alt-icon-link:hover { border-color: #1E5AFF; color: #1E5AFF; transform: scale(1.1); }
</style>

<main class="contact-premium">
    
    <div class="contact-wrapper">
        
        <!-- Main Card -->
        <div class="contact-card">
            
            <header class="contact-header">
                <h1 class="contact-headline"><?php echo esc_html($contact_headline); ?></h1>
                <?php if ($contact_intro) : ?>
                <p class="contact-intro"><?php echo esc_html($contact_intro); ?></p>
                <?php endif; ?>
            </header>
            
            <form id="contact-form" class="contact-form" method="post">
                <?php wp_nonce_field('kunaal_contact_form', 'kunaal_contact_nonce'); ?>
                
                <div class="form-field">
                    <input type="text" id="contact-name" name="contact_name" required placeholder=" ">
                    <label for="contact-name">Your name</label>
                </div>
                
                <div class="form-field">
                    <input type="email" id="contact-email" name="contact_email" required placeholder=" ">
                    <label for="contact-email">Email address</label>
                </div>
                
                <div class="form-field textarea-field">
                    <textarea id="contact-message" name="contact_message" rows="5" required placeholder=" "></textarea>
                    <label for="contact-message">What's on your mind?</label>
                </div>
                
                <button type="submit" class="submit-btn">
                    <span class="btn-text">Send message</span>
                    <span class="btn-loading">Sending...</span>
                    <span class="btn-success">Message sent!</span>
                    <svg class="btn-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </button>
                
                <div class="form-status"></div>
                
            </form>
            
            <?php if ($contact_response) : ?>
            <p class="response-note"><?php echo esc_html($contact_response); ?></p>
            <?php endif; ?>
            
        </div>
        
        <!-- Alternative Contact -->
        <?php if ($email || $linkedin || $twitter) : ?>
        <div class="contact-alternatives">
            <span class="alt-divider">or find me at</span>
            <div class="alt-icons">
                <?php if ($email) : ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="alt-icon-link" aria-label="Email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="alt-icon-link" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($twitter) : ?>
                <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="alt-icon-link" target="_blank" rel="noopener" aria-label="X / Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
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
    var form = document.getElementById('contact-form');
    if (!form) return;
    
    var btn = form.querySelector('.submit-btn');
    var status = form.querySelector('.form-status');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Set loading state
        btn.classList.add('is-loading');
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
            btn.classList.remove('is-loading');
            
            if (data.success) {
                btn.classList.add('is-success');
                form.reset();
                
                // Reset button after 3s
                setTimeout(function() {
                    btn.classList.remove('is-success');
                    btn.disabled = false;
                }, 3000);
            } else {
                status.className = 'form-status is-error';
                status.textContent = data.data.message || 'Something went wrong. Please try again.';
                btn.disabled = false;
            }
        })
        .catch(function() {
            btn.classList.remove('is-loading');
            status.className = 'form-status is-error';
            status.textContent = 'Network error. Please try again.';
            btn.disabled = false;
        });
    });
})();
</script>

<?php get_footer(); ?>
