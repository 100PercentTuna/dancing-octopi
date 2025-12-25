<?php
/**
 * 404 Template
 *
 * @package Kunaal_Theme
 */

get_header();
?>

<div class="container">
  <div class="notFound">
    <h1>404</h1>
    <p>Page not found</p>
    <a href="<?php echo esc_url(home_url('/')); ?>">Go home</a>
  </div>

<?php get_footer(); ?>
