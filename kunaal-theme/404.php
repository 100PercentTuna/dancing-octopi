<?php
/**
 * 404 Template
 *
 * @package Kunaal_Theme
 */

get_header();
?>

<main id="main" class="container">
  <div class="notFound">
    <h1>404</h1>
    <p><?php esc_html_e('Page not found', 'kunaal-theme'); ?></p>
    <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Go home', 'kunaal-theme'); ?></a>
  </div>
</main>

<?php get_footer(); ?>
