<?php
/**
 * Template Name: Contact Page
 * 
 * Elegant note-taking interface with optional name/email
 * Polished design matching site aesthetic
 *
 * @package Kunaal_Theme
 */

get_header();

// Get Customizer values
$email = kunaal_mod('kunaal_contact_email', '');
$linkedin = kunaal_mod('kunaal_linkedin_handle', '');
$twitter = kunaal_mod('kunaal_twitter_handle', '');
$instagram = kunaal_mod('kunaal_contact_instagram', '');
$whatsapp = kunaal_mod('kunaal_contact_whatsapp', '');
$viber = kunaal_mod('kunaal_contact_viber', '');
$line = kunaal_mod('kunaal_contact_line', '');

// Contact-specific settings
$contact_headline = kunaal_mod('kunaal_contact_headline', __('Say hello', 'kunaal-theme'));
$contact_placeholder = kunaal_mod('kunaal_contact_placeholder', __('Leave a note...', 'kunaal-theme'));
?>
<main id="main" class="contact-page">
    <div class="contact-container">
        <div class="contact-card">
            <header class="contact-header">
                <div class="contact-label" data-reveal="up">Note</div>
                <h1 class="contact-title" data-reveal="up"><?php echo esc_html($contact_headline); ?></h1>
            </header>
            
            <form id="contact-form" class="contact-form" method="post" novalidate>
                <?php wp_nonce_field('kunaal_contact_form', 'kunaal_contact_nonce'); ?>
                
                <!-- Honeypot -->
                <div class="contact-honeypot" aria-hidden="true">
                    <input type="text" name="contact_company" tabindex="-1" autocomplete="off">
                </div>
                
                <!-- Message textarea (always visible) -->
                <div class="contact-field contact-message-field">
                    <textarea 
                        id="contact-message" 
                        name="contact_message" 
                        rows="5" 
                        required 
                        placeholder="<?php echo esc_attr($contact_placeholder); ?>"
                        data-reveal="up"
                    ></textarea>
                </div>
                
                <!-- Optional name/email checkbox -->
                <div class="contact-optional" data-reveal="up">
                    <label class="contact-checkbox-label">
                        <input type="checkbox" id="contact-include-info" class="contact-checkbox">
                        <span class="contact-checkbox-custom"></span>
                        <span class="contact-checkbox-text"><?php esc_html_e('Include my name and email', 'kunaal-theme'); ?></span>
                    </label>
                </div>
                
                <!-- Name and email fields (hidden by default, animated reveal) -->
                <div class="contact-optional-fields" id="contact-optional-fields" style="display: none;">
                    <div class="contact-field" data-reveal="up">
                        <input 
                            type="text" 
                            id="contact-name" 
                            name="contact_name" 
                            placeholder="<?php esc_attr_e('Your name', 'kunaal-theme'); ?>"
                        >
                    </div>
                    <div class="contact-field" data-reveal="up">
                        <input 
                            type="email" 
                            id="contact-email" 
                            name="contact_email" 
                            placeholder="<?php esc_attr_e('Your email', 'kunaal-theme'); ?>"
                        >
                    </div>
                </div>
                
                <!-- Submit button with paper airplane icon -->
                <button type="submit" class="contact-submit" id="contact-submit" data-reveal="up">
                    <svg class="contact-submit-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                    <span class="contact-submit-text"><?php esc_html_e('Send', 'kunaal-theme'); ?></span>
                    <span class="contact-submit-loading" style="display: none;"><?php esc_html_e('Sending...', 'kunaal-theme'); ?></span>
                    <span class="contact-submit-success" style="display: none;"><?php esc_html_e('Sent', 'kunaal-theme'); ?></span>
                </button>
                
                <div class="contact-status" id="contact-status" aria-live="polite"></div>
            </form>
            
            <!-- Social Links -->
            <?php if ($email || $linkedin || $twitter || $instagram || $whatsapp || $viber || $line) : ?>
            <div class="contact-social" data-reveal="up">
                <p class="contact-social-label"><?php esc_html_e('Or reach out via', 'kunaal-theme'); ?></p>
                <div class="contact-social-links">
                    <?php if ($email) : ?>
                    <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Email', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="2" y="4" width="20" height="16" rx="2"/>
                            <path d="M22 6l-10 7L2 6"/>
                        </svg>
                        <span>Email</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($linkedin) : ?>
                    <a href="<?php echo esc_url($linkedin); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('LinkedIn', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                        <span>LinkedIn</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($instagram) : ?>
                    <a href="<?php echo esc_url($instagram); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Instagram', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        <span>Instagram</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($whatsapp) : ?>
                    <a href="<?php echo esc_url($whatsapp); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('WhatsApp', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .925 4.57.925 10.127c0 1.79.413 3.47 1.155 4.965L0 24l9.115-2.064a11.86 11.86 0 005.05 1.115h.005c5.554 0 10.124-4.57 10.124-10.127 0-3.033-1.34-5.75-3.47-7.625"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($viber) : ?>
                    <a href="<?php echo esc_url($viber); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('Viber', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12.5 0C7.253 0 3 4.253 3 9.5c0 1.5.4 2.9 1.1 4.1L0 24l10.4-4.1c1.2.7 2.6 1.1 4.1 1.1 5.247 0 9.5-4.253 9.5-9.5S17.747 0 12.5 0zm0 16.5c-1.1 0-2.1-.3-3-.8l-.7-.3-2.7.7.7-2.6-.3-.7c-.5-.9-.8-1.9-.8-3 0-3.86 3.14-7 7-7s7 3.14 7 7-3.14 7-7 7z"/>
                        </svg>
                        <span>Viber</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($line) : ?>
                    <a href="<?php echo esc_url($line); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('LINE', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.77.058 1.093l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                        </svg>
                        <span>LINE</span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($twitter) : 
                        $twitter_url = 'https://x.com/' . ltrim($twitter, '@');
                    ?>
                    <a href="<?php echo esc_url($twitter_url); ?>" class="contact-social-link" target="_blank" rel="noopener" aria-label="<?php esc_attr_e('X / Twitter', 'kunaal-theme'); ?>">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                        <span>X / Twitter</span>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<style>
/* Contact Page Styles - Polished Design */
.contact-page {
    min-height: 100vh;
    padding: var(--space-6) var(--space-4);
    padding-top: calc(var(--space-6) + var(--mastH, 100px)); /* Account for fixed header */
    margin-top: calc(-1 * var(--mastH, 100px)); /* Pull up into header space */
    background: 
        radial-gradient(2000px 1500px at 20% 10%, rgba(30,90,255,0.04), transparent 70%),
        radial-gradient(1800px 1400px at 80% 90%, rgba(125,107,93,0.04), transparent 70%),
        var(--bg);
    position: relative;
    /* Removed background-attachment: fixed - causes issues on mobile and gaps */
}

.contact-container {
    max-width: 500px;
    margin: 0 auto;
}

.contact-card {
    background: var(--bg);
    border: 1px solid var(--hair);
    border-radius: 12px;
    padding: var(--space-5);
    box-shadow: 
        0 2px 6px rgba(0,0,0,0.04),
        0 18px 50px rgba(0,0,0,0.06);
    position: relative;
    overflow: hidden;
}

.contact-card::before {
    content: '';
    position: absolute;
    inset: 0;
    background:
        radial-gradient(circle at 20% 30%, rgba(0,0,0,0.015) 0, transparent 35%),
        radial-gradient(circle at 80% 70%, rgba(0,0,0,0.012) 0, transparent 40%);
    pointer-events: none;
}

.contact-header {
    margin-bottom: var(--space-5);
    text-align: center;
    position: relative;
}

.contact-label {
    font-family: var(--mono);
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--muted2);
    margin-bottom: var(--space-2);
}

.contact-title {
    font-family: var(--serif);
    font-size: clamp(1.5rem, 3.5vw, 2rem);
    font-weight: 400;
    color: var(--ink);
    margin-bottom: var(--space-2);
    line-height: 1.2;
}

.contact-intro {
    font-family: var(--serif);
    font-size: 0.95rem;
    color: var(--muted);
    line-height: 1.6;
}

.contact-form {
    margin-bottom: var(--space-5);
    position: relative;
}

.contact-honeypot {
    position: absolute;
    left: -9999px;
    width: 1px;
    height: 1px;
    overflow: hidden;
}

.contact-field {
    margin-bottom: var(--space-3);
}

.contact-message-field textarea {
    width: 100%;
    min-height: 120px;
    padding: var(--space-3);
    font-family: var(--serif);
    font-size: 0.9375rem;
    line-height: 1.6;
    color: var(--ink);
    background: var(--bg);
    border: 1px solid var(--hair);
    border-radius: 6px;
    resize: vertical;
    transition: all 0.2s ease;
}

.contact-message-field textarea:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px var(--blueTint);
}

.contact-optional {
    margin-bottom: var(--space-3);
}

.contact-checkbox-label {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    font-family: var(--sans);
    font-size: 0.8125rem;
    color: var(--muted);
    cursor: pointer;
    position: relative;
}

.contact-checkbox {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.contact-checkbox-custom {
    width: 20px;
    height: 20px;
    border: 1.5px solid var(--hair);
    border-radius: 4px;
    background: var(--bg);
    position: relative;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.contact-checkbox:checked + .contact-checkbox-custom {
    background: var(--blue);
    border-color: var(--blue);
}

.contact-checkbox:checked + .contact-checkbox-custom::after {
    content: '';
    position: absolute;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid var(--bg);
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.contact-checkbox-label:hover .contact-checkbox-custom {
    border-color: var(--blue);
}

.contact-checkbox-text {
    flex: 1;
}

.contact-optional-fields {
    margin-bottom: var(--space-3);
    overflow: hidden;
}

.contact-optional-fields[style*="display: block"] {
    animation: slideDownFade 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideDownFade {
    from {
        opacity: 0;
        transform: translateY(-12px);
        max-height: 0;
    }
    to {
        opacity: 1;
        transform: translateY(0);
        max-height: 200px;
    }
}

.contact-optional-fields input {
    width: 100%;
    padding: var(--space-2) var(--space-3);
    font-family: var(--sans);
    font-size: 0.875rem;
    color: var(--ink);
    background: var(--bg);
    border: 1px solid var(--hair);
    border-radius: 6px;
    margin-bottom: var(--space-2);
    transition: all 0.2s ease;
}

.contact-optional-fields input:focus {
    outline: none;
    border-color: var(--blue);
    box-shadow: 0 0 0 3px var(--blueTint);
}

.contact-submit {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    width: 100%;
    padding: var(--space-2) var(--space-4);
    font-family: var(--sans);
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--bg);
    background: var(--blue);
    border: none;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 8px rgba(30,90,255,0.2);
}

.contact-submit:hover {
    background: #1a4fe6;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30,90,255,0.3);
}

.contact-submit:active {
    transform: translateY(0);
}

.contact-submit:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.contact-submit-icon {
    width: 16px;
    height: 16px;
    stroke: currentColor;
}

.contact-submit-text,
.contact-submit-loading,
.contact-submit-success {
    display: inline-block;
}

.contact-submit.is-loading .contact-submit-text,
.contact-submit.is-loading .contact-submit-icon {
    display: none;
}

.contact-submit.is-loading .contact-submit-loading {
    display: inline-block;
}

.contact-submit.is-success {
    background: #0E7A3A;
}

.contact-submit.is-success .contact-submit-text,
.contact-submit.is-success .contact-submit-loading {
    display: none;
}

.contact-submit.is-success .contact-submit-success {
    display: inline-block;
}

.contact-status {
    margin-top: var(--space-2);
    font-family: var(--sans);
    font-size: 0.8125rem;
    text-align: center;
    min-height: 18px;
}

.contact-status.is-error {
    color: #d32f2f;
}

.contact-social {
    margin-top: var(--space-6);
    padding-top: var(--space-5);
    border-top: 1px solid var(--hair);
    text-align: center;
    position: relative;
}

.contact-social-label {
    font-family: var(--mono);
    font-size: 8px;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    color: var(--muted2);
    margin-bottom: var(--space-3);
}

.contact-social-links {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: var(--space-2);
}

.contact-social-link {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: var(--space-1);
    padding: var(--space-2) var(--space-3);
    font-family: var(--sans);
    font-size: 0.75rem;
    color: var(--muted);
    text-decoration: none;
    border: 1px solid var(--hair);
    border-radius: 6px;
    transition: all 0.2s ease;
    width: 72px;
    height: 72px;
    background: var(--bg);
}

.contact-social-link:hover {
    color: var(--blue);
    border-color: var(--blue);
    background: var(--blueTint);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30,90,255,0.15);
}

.contact-social-link svg {
    width: 18px;
    height: 18px;
    max-width: 18px;
    max-height: 18px;
    flex-shrink: 0;
}

.contact-social-link span {
    font-size: 0.6875rem;
    line-height: 1.2;
    white-space: nowrap; /* Prevent text wrapping - fixes X/Twitter splitting */
}

/* Dark mode */
:root[data-theme="dark"] .contact-page {
    background: 
        radial-gradient(2000px 1500px at 20% 10%, rgba(224,122,98,0.06), transparent 70%),
        radial-gradient(1800px 1400px at 80% 90%, rgba(201,184,168,0.06), transparent 70%),
        var(--bg);
    /* No background-attachment: fixed */
}

:root[data-theme="dark"] .contact-card {
    background: var(--bg);
    border-color: var(--hair);
}

:root[data-theme="dark"] .contact-message-field textarea,
:root[data-theme="dark"] .contact-optional-fields input {
    background: var(--bg);
    border-color: var(--hair);
    color: var(--ink);
}

:root[data-theme="dark"] .contact-checkbox-custom {
    background: var(--bg);
    border-color: var(--hair);
}

:root[data-theme="dark"] .contact-checkbox:checked + .contact-checkbox-custom {
    background: var(--blue);
    border-color: var(--blue);
}

:root[data-theme="dark"] .contact-social-link {
    border-color: var(--hair);
    background: var(--bg);
    color: var(--muted);
}

:root[data-theme="dark"] .contact-social-link:hover {
    border-color: var(--blue);
    background: var(--blueTint);
}

/* Scroll reveal animations */
[data-reveal] {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

[data-reveal].is-visible {
    opacity: 1;
    transform: translateY(0);
}

/* Responsive */
@media (max-width: 640px) {
    .contact-page {
        padding: var(--space-4) var(--space-2);
    }
    
    .contact-card {
        padding: var(--space-4);
    }
    
    .contact-social-links {
        gap: var(--space-2);
    }
    
    .contact-social-link {
        width: 68px;
        height: 68px;
        padding: var(--space-2);
    }
    
    .contact-social-link svg {
        width: 20px;
        height: 20px;
    }
}
</style>

<script>
(function() {
    // Scroll reveal animations
    function initScrollReveals() {
        var elements = document.querySelectorAll('[data-reveal]');
        if (!elements.length) return;
        
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        elements.forEach(function(el) {
            observer.observe(el);
        });
    }
    
    function initContactForm() {
        var form = document.getElementById('contact-form');
        if (!form) {
            setTimeout(initContactForm, 100);
            return;
        }
        
        var checkbox = document.getElementById('contact-include-info');
        var optionalFields = document.getElementById('contact-optional-fields');
        var nameField = document.getElementById('contact-name');
        var emailField = document.getElementById('contact-email');
        var submitBtn = document.getElementById('contact-submit');
        var status = document.getElementById('contact-status');
        
        // Toggle optional fields with animation
        if (checkbox && optionalFields) {
            checkbox.addEventListener('change', function() {
                if (this.checked) {
                    optionalFields.style.display = 'block';
                    // Trigger reflow for animation
                    optionalFields.offsetHeight;
                    // Name and email are OPTIONAL - do not set required
                    
                    // Trigger scroll reveals for new fields
                    setTimeout(function() {
                        var revealEls = optionalFields.querySelectorAll('[data-reveal]');
                        revealEls.forEach(function(el) {
                            el.classList.add('is-visible');
                        });
                    }, 50);
                } else {
                    optionalFields.style.display = 'none';
                    // Name and email are optional - no required attribute
                    nameField.value = '';
                    emailField.value = '';
                }
            });
        }
        
        // Form submission
        if (!submitBtn) return;
        
        var ajaxUrl = (typeof kunaalTheme !== 'undefined' && kunaalTheme.ajaxUrl) 
            ? kunaalTheme.ajaxUrl 
            : <?php echo json_encode(admin_url('admin-ajax.php')); ?>;
        
        if (!ajaxUrl) {
            if (status) {
                status.className = 'contact-status is-error';
                status.textContent = 'Form configuration error. Please refresh the page.';
            }
            return;
        }
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            submitBtn.classList.add('is-loading');
            submitBtn.disabled = true;
            if (status) {
                status.className = 'contact-status';
                status.textContent = '';
            }
            
            var formData = new FormData(form);
            formData.append('action', 'kunaal_contact_form');
            
            fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function(data) {
                submitBtn.classList.remove('is-loading');
                
                if (data.success) {
                    submitBtn.classList.add('is-success');
                    form.reset();
                    if (optionalFields) {
                        optionalFields.style.display = 'none';
                        checkbox.checked = false;
                    }
                    
                    setTimeout(function() {
                        submitBtn.classList.remove('is-success');
                        submitBtn.disabled = false;
                    }, 3000);
                } else {
                    if (status) {
                        status.className = 'contact-status is-error';
                        status.textContent = data.data && data.data.message ? data.data.message : 'Something went wrong. Please try again.';
                    }
                    submitBtn.disabled = false;
                }
            })
            .catch(function(error) {
                console.error('Contact form error:', error);
                submitBtn.classList.remove('is-loading');
                if (status) {
                    status.className = 'contact-status is-error';
                    status.textContent = 'Network error. Please try again.';
                }
                submitBtn.disabled = false;
            });
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initScrollReveals();
            initContactForm();
        });
    } else {
        initScrollReveals();
        initContactForm();
    }
})();

// #region agent log - Contact page background check
(function() {
  setTimeout(function() {
    var contactPage = document.querySelector('.contact-page');
    if (contactPage) {
      var pageStyles = window.getComputedStyle(contactPage);
      var pageRect = contactPage.getBoundingClientRect();
      var bodyRect = document.body.getBoundingClientRect();
      fetch('http://127.0.0.1:7242/ingest/e3e8dd77-ee5b-448b-9061-0c1518ee7491',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'page-contact.php:inline',message:'Contact page background CSS computed styles',data:{minHeight:pageStyles.minHeight,height:pageRect.height,viewportHeight:window.innerHeight,paddingTop:pageStyles.paddingTop,marginTop:pageStyles.marginTop,background:pageStyles.background,backgroundAttachment:pageStyles.backgroundAttachment,backgroundSize:pageStyles.backgroundSize,pageTop:pageRect.top,bodyTop:bodyRect.top,gapAbove:pageRect.top-bodyRect.top},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H6.1,H6.2,H6.3'})}).catch(()=>{});
    }
    
    // Check X/Twitter text wrapping
    var twitterLink = document.querySelector('.contact-social-link span');
    if (twitterLink) {
      var linkStyles = window.getComputedStyle(twitterLink);
      var linkRect = twitterLink.getBoundingClientRect();
      fetch('http://127.0.0.1:7242/ingest/e3e8dd77-ee5b-448b-9061-0c1518ee7491',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({location:'page-contact.php:inline',message:'X/Twitter text wrapping check',data:{whiteSpace:linkStyles.whiteSpace,width:linkRect.width,height:linkRect.height,textContent:twitterLink.textContent.trim(),lineCount:Math.ceil(linkRect.height/parseFloat(linkStyles.lineHeight)||16)},timestamp:Date.now(),sessionId:'debug-session',runId:'run1',hypothesisId:'H7.1,H7.2'})}).catch(()=>{});
    }
  }, 1000);
})();
// #endregion
</script>

<?php get_footer(); ?>
