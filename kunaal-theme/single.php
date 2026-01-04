<?php
/**
 * Single Post Template (Fallback)
 *
 * @package Kunaal_Theme
 */

get_header();
?>

<main id="main" class="article container">
  <?php while (have_posts()) : the_post(); ?>
    <?php
    $subtitle = get_post_meta(get_the_ID(), 'kunaal_subtitle', true);
    $read_time = get_post_meta(get_the_ID(), 'kunaal_read_time', true);
    $topics = get_the_terms(get_the_ID(), 'topic');
    ?>
    <article>
      <header class="articleHeader reveal">
        <?php // Professional publication order: Title → Subtitle → Author → Meta ?>
        <h1 class="articleTitle"><?php the_title(); ?></h1>
        
        <?php if ($subtitle) { ?>
          <p class="articleDek"><?php echo esc_html($subtitle); ?></p>
        <?php } ?>
        
        <?php 
        // Author byline
        $author_first = kunaal_mod('kunaal_author_first_name', 'Kunaal');
        $author_last = kunaal_mod('kunaal_author_last_name', 'Wadhwa');
        $author_name = trim($author_first . ' ' . $author_last);
        if ($author_name) { ?>
          <div class="articleByline">By <?php echo esc_html($author_name); ?></div>
        <?php } ?>
        
        <div class="articleMeta">
          <?php // Primary meta: date and read time on one line ?>
          <div class="articleMeta__primary">
            <span><?php echo esc_html(get_the_date('j F Y')); ?></span>
            <?php if ($read_time) { ?>
              <span class="dot"></span>
              <span><?php echo esc_html($read_time); ?> min read</span>
            <?php } ?>
          </div>
          
          <?php // Tags on separate line ?>
          <?php if ($topics && !is_wp_error($topics)) { ?>
            <div class="articleMeta__tags">
              <?php foreach ($topics as $topic) { ?>
                <a href="<?php echo esc_url(get_term_link($topic)); ?>">#<?php echo esc_html($topic->name); ?></a>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      </header>

      <?php if (has_post_thumbnail()) : ?>
        <figure class="heroImage reveal">
          <?php the_post_thumbnail('essay-hero'); ?>
        </figure>
      <?php endif; ?>

      <div class="articleContent">
        <div class="prose">
          <?php the_content(); ?>
        </div>
        <aside class="rail"></aside>
      </div>
    </article>
  <?php endwhile; ?>
</main>

<?php get_footer(); ?>
