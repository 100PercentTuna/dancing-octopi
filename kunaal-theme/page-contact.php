<?php
/**
 * Template Name: Contact Page
 * 
 * Contact page with form and social links
 *
 * @package Kunaal_Theme
 */

get_header();

// Get Customizer values
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = $first_name . ' ' . $last_name;
$email = get_theme_mod('kunaal_contact_email', '');

// Contact-specific settings
$contact_headline = get_theme_mod('kunaal_contact_headline', 'Get in Touch');
$contact_intro = get_theme_mod('kunaal_contact_intro', 'Have a question, idea, or just want to say hello? I\'d love to hear from you.');
$contact_response_time = get_theme_mod('kunaal_contact_response_time', 'I typically respond within 2-3 business days.');

// Social links
$linkedin = get_theme_mod('kunaal_linkedin_handle', '');
$twitter = get_theme_mod('kunaal_twitter_handle', '');
?>

<main class="contact-page">
    <!-- Hero Section -->
    <section class="contact-hero reveal">
        <h1 class="contact-headline"><?php echo esc_html($contact_headline); ?></h1>
        <p class="contact-intro"><?php echo esc_html($contact_intro); ?></p>
    </section>
    
    <!-- Contact Form -->
    <section class="contact-form-section reveal">
        <form id="kunaal-contact-form" class="contact-form" method="post">
            <?php wp_nonce_field('kunaal_contact_form', 'kunaal_contact_nonce'); ?>
            
            <div class="form-group">
                <label for="contact-name">Your Name</label>
                <input type="text" id="contact-name" name="contact_name" required placeholder="Jane Doe">
            </div>
            
            <div class="form-group">
                <label for="contact-email">Your Email</label>
                <input type="email" id="contact-email" name="contact_email" required placeholder="jane@example.com">
            </div>
            
            <div class="form-group">
                <label for="contact-subject">Subject</label>
                <input type="text" id="contact-subject" name="contact_subject" required placeholder="What's this about?">
            </div>
            
            <div class="form-group">
                <label for="contact-message">Message</label>
                <textarea id="contact-message" name="contact_message" rows="6" required placeholder="Your message..."></textarea>
            </div>
            
            <button type="submit" class="submit-btn">
                <span class="btn-text">Send Message</span>
                <span class="btn-sending" style="display:none;">Sending...</span>
            </button>
            
            <div class="form-status" style="display:none;"></div>
        </form>
        
        <?php if ($contact_response_time) : ?>
            <p class="response-note"><?php echo esc_html($contact_response_time); ?></p>
        <?php endif; ?>
    </section>
    
    <!-- Alternative Contact Methods -->
    <section class="contact-alternatives reveal">
        <h2 class="section-label">Other Ways to Connect</h2>
        
        <div class="contact-methods">
            <?php if ($email) : ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-method">
                    <div class="method-icon">✉️</div>
                    <div class="method-info">
                        <span class="method-label">Email directly</span>
                        <span class="method-value"><?php echo esc_html($email); ?></span>
                    </div>
                </a>
            <?php endif; ?>
            
            <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="contact-method" target="_blank" rel="noopener">
                    <div class="method-icon">💼</div>
                    <div class="method-info">
                        <span class="method-label">LinkedIn</span>
                        <span class="method-value">Connect professionally</span>
                    </div>
                </a>
            <?php endif; ?>
            
            <?php if ($twitter) : ?>
                <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="contact-method" target="_blank" rel="noopener">
                    <div class="method-icon">𝕏</div>
                    <div class="method-info">
                        <span class="method-label">X / Twitter</span>
                        <span class="method-value">@<?php echo esc_html($twitter); ?></span>
                    </div>
                </a>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Page content if any -->
    <?php while (have_posts()) : the_post(); 
        $content = get_the_content();
        if (!empty($content)) :
    ?>
    <section class="contact-additional reveal">
        <?php the_content(); ?>
    </section>
    <?php endif; endwhile; ?>
</main>

<script>
(function() {
    var form = document.getElementById('kunaal-contact-form');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        var btn = form.querySelector('.submit-btn');
        var btnText = btn.querySelector('.btn-text');
        var btnSending = btn.querySelector('.btn-sending');
        var status = form.querySelector('.form-status');
        
        btnText.style.display = 'none';
        btnSending.style.display = 'inline';
        btn.disabled = true;
        
        var formData = new FormData(form);
        formData.append('action', 'kunaal_contact_form');
        
        fetch(kunaalTheme.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            status.style.display = 'block';
            if (data.success) {
                status.className = 'form-status success';
                status.textContent = data.data.message || 'Message sent successfully!';
                form.reset();
            } else {
                status.className = 'form-status error';
                status.textContent = data.data.message || 'Something went wrong. Please try again.';
            }
        })
        .catch(function(error) {
            status.style.display = 'block';
            status.className = 'form-status error';
            status.textContent = 'Network error. Please try again.';
        })
        .finally(function() {
            btnText.style.display = 'inline';
            btnSending.style.display = 'none';
            btn.disabled = false;
        });
    });
})();
</script>

<?php get_footer(); ?>

