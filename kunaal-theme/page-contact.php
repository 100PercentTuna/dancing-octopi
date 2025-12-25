<?php
/**
 * Template Name: Contact Page
 * A casual, inviting contact page with message box and socials
 * 
 * @package Kunaal_Theme
 */

get_header();

// Get customizer values
$first_name = get_theme_mod('kunaal_author_first_name', 'Kunaal');
$last_name = get_theme_mod('kunaal_author_last_name', 'Wadhwa');
$full_name = $first_name . ' ' . $last_name;

// Contact settings
$contact_intro = get_theme_mod('kunaal_contact_intro', 'I\'d love to hear from you. Drop a message, share a thought, or just say hi.');
$contact_email = get_theme_mod('kunaal_contact_email', get_theme_mod('kunaal_email', ''));

// Socials
$email = get_theme_mod('kunaal_email', '');
$linkedin_url = get_theme_mod('kunaal_linkedin_handle', '');
$twitter_handle = get_theme_mod('kunaal_twitter_handle', '');
$instagram = get_theme_mod('kunaal_instagram', '');
$whatsapp_qr = get_theme_mod('kunaal_whatsapp_qr', '');
?>

<main class="contact-page">
  
  <!-- Hero Section -->
  <section class="contact-hero">
    <div class="contact-hero-content">
      <h1 class="contact-title reveal motion-fade-up">Say Hi</h1>
      <p class="contact-intro reveal motion-fade-up stagger-1"><?php echo wp_kses_post($contact_intro); ?></p>
    </div>
  </section>
  
  <!-- Message Box Section -->
  <section class="contact-message-section">
    <div class="contact-message-inner">
      
      <!-- Success Message (hidden by default) -->
      <div class="contact-success" id="contactSuccess">
        <div class="contact-success-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
        <h3>Message Sent!</h3>
        <p>Thanks for reaching out. I'll get back to you soon.</p>
      </div>
      
      <!-- Contact Form -->
      <form class="contact-form" id="contactForm" method="POST" action="">
        <?php wp_nonce_field('kunaal_contact_form', 'kunaal_contact_nonce'); ?>
        
        <div class="contact-field reveal motion-fade-up stagger-1">
          <textarea name="message" id="contactMessage" placeholder=" " required></textarea>
          <label for="contactMessage">Your message...</label>
        </div>
        
        <div class="contact-field reveal motion-fade-up stagger-2">
          <input type="text" name="name" id="contactName" placeholder=" " />
          <label for="contactName">Your name (optional)</label>
        </div>
        
        <div class="contact-field reveal motion-fade-up stagger-3">
          <input type="email" name="email" id="contactEmail" placeholder=" " />
          <label for="contactEmail">Your email (optional, if you'd like a reply)</label>
        </div>
        
        <p class="contact-message-hint reveal motion-fade-up stagger-4">
          No pressure—just drop your thoughts. I read everything.
        </p>
        
        <div class="contact-submit-wrapper reveal motion-fade-up stagger-5">
          <button type="submit" class="contact-submit">
            <span>Send Message</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="22" y1="2" x2="11" y2="13"/>
              <polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
          </button>
        </div>
      </form>
      
    </div>
  </section>
  
  <!-- Socials Section -->
  <?php if ($email || $linkedin_url || $twitter_handle || $instagram || $whatsapp_qr) : ?>
  <section class="contact-socials">
    <div class="contact-socials-inner">
      <h2 class="contact-socials-title reveal motion-fade-up">Or find me elsewhere</h2>
      
      <div class="contact-socials-grid">
        <?php if ($email) : ?>
        <a href="mailto:<?php echo esc_attr($email); ?>" class="contact-social-card reveal motion-scale-in stagger-1">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="4" width="20" height="16" rx="2"/>
            <path d="M22 7l-10 6L2 7"/>
          </svg>
          <span>Email</span>
        </a>
        <?php endif; ?>
        
        <?php if ($linkedin_url) : ?>
        <a href="<?php echo esc_url($linkedin_url); ?>" target="_blank" rel="noopener" class="contact-social-card reveal motion-scale-in stagger-2">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/>
          </svg>
          <span>LinkedIn</span>
        </a>
        <?php endif; ?>
        
        <?php if ($twitter_handle) : ?>
        <a href="https://twitter.com/<?php echo esc_attr($twitter_handle); ?>" target="_blank" rel="noopener" class="contact-social-card reveal motion-scale-in stagger-3">
          <svg viewBox="0 0 24 24" fill="currentColor">
            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
          </svg>
          <span>X / Twitter</span>
        </a>
        <?php endif; ?>
        
        <?php if ($instagram) : ?>
        <a href="https://instagram.com/<?php echo esc_attr($instagram); ?>" target="_blank" rel="noopener" class="contact-social-card reveal motion-scale-in stagger-4">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="2" y="2" width="20" height="20" rx="5"/>
            <circle cx="12" cy="12" r="4"/>
            <circle cx="18" cy="6" r="1.5" fill="currentColor"/>
          </svg>
          <span>Instagram</span>
        </a>
        <?php endif; ?>
        
        <?php if ($whatsapp_qr) : ?>
        <div class="contact-whatsapp-qr reveal motion-scale-in stagger-5">
          <img src="<?php echo esc_url($whatsapp_qr); ?>" alt="WhatsApp QR Code" />
          <span>WhatsApp</span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>
  
  <!-- Page Content (from WordPress editor) -->
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <?php if (trim(get_the_content())) : ?>
    <section class="contact-custom-content container">
      <div class="prose">
        <?php the_content(); ?>
      </div>
    </section>
    <?php endif; ?>
  <?php endwhile; endif; ?>
  
</main>

<script>
(function() {
  const form = document.getElementById('contactForm');
  const successBox = document.getElementById('contactSuccess');
  
  if (!form) return;
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(form);
    formData.append('action', 'kunaal_contact_form');
    
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        form.style.display = 'none';
        successBox.classList.add('show');
      } else {
        alert('Something went wrong. Please try again or email directly.');
      }
    })
    .catch(err => {
      console.error(err);
      alert('Something went wrong. Please try again or email directly.');
    });
  });
})();
</script>

<?php get_footer(); ?>

