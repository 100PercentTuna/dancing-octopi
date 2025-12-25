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
<main class="contact-ledger">
    
    <div class="contact-ledgerWrap">
        
        <!-- Main Card -->
        <div class="ledgerCard">
            
            <header class="ledgerHeader">
                <div class="ledgerMeta">
                    <span class="ledgerMetaLabel">Note</span>
                    <span class="ledgerMetaRule"></span>
                    <time class="ledgerMetaTime" datetime="<?php echo esc_attr(gmdate('c')); ?>"><?php echo esc_html(wp_date('F Y')); ?></time>
                </div>

                <h1 class="ledgerHeadline"><?php echo esc_html($contact_headline); ?></h1>
                <?php if ($contact_intro) : ?>
                <p class="ledgerIntro"><?php echo esc_html($contact_intro); ?></p>
                <?php endif; ?>
            </header>
            
            <form id="contact-form" class="ledgerForm" method="post" novalidate>
                <?php wp_nonce_field('kunaal_contact_form', 'kunaal_contact_nonce'); ?>
                
                <!-- Honeypot: leave empty -->
                <div class="ledgerField" style="position:absolute; left:-9999px; width:1px; height:1px; overflow:hidden;" aria-hidden="true">
                    <label for="contact-company">Company</label>
                    <input type="text" id="contact-company" name="contact_company" tabindex="-1" autocomplete="off" placeholder=" ">
                </div>
                
                <div class="ledgerField">
                    <input type="text" id="contact-name" name="contact_name" required placeholder=" ">
                    <label for="contact-name">Your name</label>
                </div>
                
                <div class="ledgerField">
                    <input type="email" id="contact-email" name="contact_email" required placeholder=" ">
                    <label for="contact-email">Email address</label>
                </div>
                
                <div class="ledgerField ledgerTextarea">
                    <textarea id="contact-message" name="contact_message" rows="7" required placeholder=" "></textarea>
                    <label for="contact-message">Your note</label>
                    <div class="ledgerLines" aria-hidden="true"></div>
                </div>
                
                <button type="submit" class="ledgerSend">
                    <span class="btn-text">Send note</span>
                    <span class="btn-loading">Sending...</span>
                    <span class="btn-success">Sent</span>
                </button>
                
                <div class="form-status" aria-live="polite"></div>
                
            </form>
            
            <?php if ($contact_response) : ?>
            <p class="ledgerResponse"><?php echo esc_html($contact_response); ?></p>
            <?php endif; ?>
            
        </div>
        
        <!-- Alternative Contact -->
        <?php if ($email || $linkedin || $twitter) : ?>
        <div class="ledgerAlternatives">
            <span class="ledgerAltDivider">or elsewhere</span>
            <div class="ledgerAltIcons">
                <?php if ($email) : ?>
                <a href="mailto:<?php echo esc_attr($email); ?>" class="ledgerAltIcon" aria-label="Email">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" width="24" height="24">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="M22 6l-10 7L2 6"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($linkedin) : ?>
                <a href="<?php echo esc_url($linkedin); ?>" class="ledgerAltIcon" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                </a>
                <?php endif; ?>
                <?php if ($twitter) : ?>
                <a href="https://x.com/<?php echo esc_attr($twitter); ?>" class="ledgerAltIcon" target="_blank" rel="noopener" aria-label="X / Twitter">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php
        // Messenger QR tiles (privacy-friendly via /connect/<slug> redirects)
        $messengers = array(
            'telegram' => array(
                'label' => 'Telegram',
                'enabled' => (bool) get_theme_mod('kunaal_contact_messenger_telegram_enabled', false),
                'mode' => get_theme_mod('kunaal_contact_messenger_telegram_mode', 'redirect'),
                'target' => get_theme_mod('kunaal_contact_messenger_telegram_target', ''),
                'slug' => sanitize_title(get_theme_mod('kunaal_contact_messenger_telegram_redirect_slug', 'telegram')),
            ),
            'line' => array(
                'label' => 'LINE',
                'enabled' => (bool) get_theme_mod('kunaal_contact_messenger_line_enabled', false),
                'mode' => get_theme_mod('kunaal_contact_messenger_line_mode', 'redirect'),
                'target' => get_theme_mod('kunaal_contact_messenger_line_target', ''),
                'slug' => sanitize_title(get_theme_mod('kunaal_contact_messenger_line_redirect_slug', 'line')),
            ),
            'viber' => array(
                'label' => 'Viber',
                'enabled' => (bool) get_theme_mod('kunaal_contact_messenger_viber_enabled', false),
                'mode' => get_theme_mod('kunaal_contact_messenger_viber_mode', 'redirect'),
                'target' => get_theme_mod('kunaal_contact_messenger_viber_target', ''),
                'slug' => sanitize_title(get_theme_mod('kunaal_contact_messenger_viber_redirect_slug', 'viber')),
            ),
        );

        $enabled_any = false;
        foreach ($messengers as $m) {
            if (!empty($m['enabled'])) { $enabled_any = true; break; }
        }
        ?>

        <?php if ($enabled_any) : ?>
        <section class="ledgerMessengers" aria-label="Message me via messenger">
            <div class="ledgerMessengersHeader">
                <span class="ledgerAltDivider">message me</span>
                <p class="ledgerMessengersSub">Scan a code to open a private link—no phone number displayed.</p>
            </div>

            <div class="ledgerQrGrid">
                <?php foreach ($messengers as $key => $m) : ?>
                    <?php
                    if (empty($m['enabled'])) continue;
                    $connect_url = home_url('/connect/' . $m['slug'] . '/');
                    $direct_url = kunaal_build_messenger_target_url($key, $m['target']);
                    $open_url = ($m['mode'] === 'direct') ? $direct_url : $connect_url;
                    $qr_text = $open_url;
                    if (empty($qr_text)) continue;
                    ?>
                    <div class="ledgerQrTile">
                        <div class="ledgerQrLabel"><?php echo esc_html($m['label']); ?></div>
                        <a class="ledgerQrImageLink" href="<?php echo esc_url($open_url); ?>" target="_blank" rel="noopener">
                            <img class="ledgerQrImage" src="<?php echo esc_url(kunaal_qr_img_src($qr_text, 220)); ?>" alt="<?php echo esc_attr($m['label']); ?> QR code">
                        </a>
                        <a class="ledgerQrOpen" href="<?php echo esc_url($open_url); ?>" target="_blank" rel="noopener">Open →</a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
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
