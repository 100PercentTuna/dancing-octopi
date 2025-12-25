<div class="pdf-document">
  <!-- Cover/Title Page -->
  <div class="pdf-cover">
    <?php if ($avatar_url) : ?>
      <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($author_name); ?>" class="pdf-avatar" />
    <?php endif; ?>
    
    <h1 class="pdf-title"><?php echo esc_html($title); ?></h1>
    
    <?php if ($subtitle) : ?>
      <p class="pdf-subtitle"><?php echo esc_html($subtitle); ?></p>
    <?php endif; ?>
    
    <div class="pdf-byline">
      <strong>by <?php echo esc_html($author_name); ?></strong>
    </div>
    
    <div class="pdf-meta">
      <p><?php echo esc_html($date); ?></p>
      <?php if ($read_time) : ?>
        <p><?php echo esc_html($read_time); ?> minute read</p>
      <?php endif; ?>
      <?php if ($topics && !is_wp_error($topics)) : ?>
        <p class="pdf-topics">
          <?php 
          $topic_names = array_map(function($t) { return '#' . $t->name; }, $topics);
          echo esc_html(implode(' ', $topic_names));
          ?>
        </p>
      <?php endif; ?>
    </div>
  </div>
  
  <!-- Table of Contents -->
  <?php if ($toc) : ?>
    <div class="pdf-page-break"></div>
    <?php echo $toc; ?>
  <?php endif; ?>
  
  <!-- Hero Image -->
  <?php if ($hero_url) : ?>
    <div class="pdf-page-break"></div>
    <figure class="pdf-hero">
      <img src="<?php echo esc_url($hero_url); ?>" alt="<?php echo esc_attr($title); ?>" />
    </figure>
  <?php endif; ?>
  
  <!-- Main Content -->
  <div class="pdf-page-break"></div>
  <div class="pdf-content">
    <?php echo $pdf_content; ?>
  </div>
  
  <!-- Footer with author info -->
  <div class="pdf-footer-info">
    <hr />
    <div class="pdf-author-box">
      <h4>About the Author</h4>
      <p><strong><?php echo esc_html($author_name); ?></strong></p>
      <?php if ($author_email) : ?>
        <p>Email: <?php echo esc_html($author_email); ?></p>
      <?php endif; ?>
      <p>Website: <?php echo esc_html($site_url); ?></p>
    </div>
  </div>
</div>
