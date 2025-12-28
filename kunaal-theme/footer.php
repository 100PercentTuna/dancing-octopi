  <!-- Footer -->
  <footer>
    <div class="container footerInner">
      <div class="footerLeft">
        <?php echo esc_html(kunaal_mod('kunaal_footer_disclaimer', __('Personal writing. Independent of my day job.', 'kunaal-theme'))); ?>
      </div>
      
      <?php
      $contact_email = kunaal_mod('kunaal_contact_email', '');
      if (!empty($contact_email)) {
          ?>
        <a class="mailWrap" href="mailto:<?php echo esc_attr($contact_email); ?>" aria-label="Email <?php echo esc_attr(kunaal_mod('kunaal_author_first_name', 'Kunaal') . ' ' . kunaal_mod('kunaal_author_last_name', 'Wadhwa')); ?>">
          <svg class="env" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M4 6h16v12H4z"/>
            <path d="M4 7l8 6 8-6"/>
          </svg>
          <span><?php echo esc_html($contact_email); ?></span>
        </a>
          <?php
      } else {
          ?>
        <span></span>
          <?php
      }
      ?>
      
      <div class="footerRight">
        &copy; <span id="footerYear"><?php echo esc_html(date('Y')); ?></span>
        <?php
        $first_name = kunaal_mod('kunaal_author_first_name', 'Kunaal');
        $last_name = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
        echo esc_html($first_name . ' ' . $last_name);
        ?>
      </div>
    </div>
  </footer>

  <div class="sr-only" id="announcer" aria-live="polite" aria-atomic="true"></div>

  <?php wp_footer(); ?>
</body>
</html>
