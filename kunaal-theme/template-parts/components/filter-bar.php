<?php
/**
 * Component: Filter Bar
 *
 * Canonical filter UI markup with topics dropdown, sort, search, and reset.
 * Use this component for ALL filter bars site-wide.
 *
 * @package Kunaal_Theme
 * @since 4.31.0
 *
 * @param array $args {
 *     @type array  $topics       Array of topic data (slug, name, count)
 *     @type bool   $show_search  Whether to show search input (default: true)
 *     @type bool   $show_reset   Whether to show reset button (default: true)
 *     @type string $class        Optional additional CSS classes
 * }
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get args with defaults
$topics      = $args['topics'] ?? [];
$show_search = $args['show_search'] ?? true;
$show_reset  = $args['show_reset'] ?? true;
$class       = $args['class'] ?? '';

$toolbar_classes = 'toolbar';
if (!empty($class)) {
    $toolbar_classes .= ' ' . esc_attr($class);
}
?>
<div class="<?php echo esc_attr($toolbar_classes); ?>" data-ui="filter">
  <div class="filterControls">
    <div class="filterPanel" data-role="panel">
      <?php if (!empty($topics)) : ?>
      <!-- Topics dropdown -->
      <div class="topicSelect" data-role="topic-menu" aria-label="<?php esc_attr_e('Filter by topic', 'kunaal-theme'); ?>">
        <button class="topicDropdownBtn" data-action="topics-toggle" type="button" aria-haspopup="listbox" aria-expanded="false">
          <span class="topicSummary" data-role="count"><?php esc_html_e('all topics', 'kunaal-theme'); ?></span>
          <svg class="caret" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6 9l6 6 6-6" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </button>
        <ul class="topicDropdown" role="listbox" aria-multiselectable="true">
          <li class="topicOpt" data-tag="__ALL__" data-role="topic-item" role="option" aria-selected="true">
            <input type="checkbox" checked tabindex="-1" />
            <span class="tName"><?php esc_html_e('all topics', 'kunaal-theme'); ?></span>
          </li>
          <li class="topicDivider"><hr></li>
          <?php foreach ($topics as $topic) : ?>
          <li class="topicOpt" data-tag="<?php echo esc_attr($topic['slug']); ?>" data-role="topic-item" role="option" aria-selected="false">
            <input type="checkbox" tabindex="-1" />
            <span class="tName">#<?php echo esc_html($topic['name']); ?></span>
            <span class="tCount"><?php echo esc_html($topic['count']); ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>
      
      <!-- Sort -->
      <select class="modernSelect" data-role="sort" aria-label="<?php esc_attr_e('Sort by', 'kunaal-theme'); ?>">
        <option value="new"><?php esc_html_e('newest first', 'kunaal-theme'); ?></option>
        <option value="old"><?php esc_html_e('oldest first', 'kunaal-theme'); ?></option>
        <option value="popular"><?php esc_html_e('most popular', 'kunaal-theme'); ?></option>
        <option value="title"><?php esc_html_e('alphabetical', 'kunaal-theme'); ?></option>
      </select>
      
      <?php if ($show_search) : ?>
      <!-- Search -->
      <div class="searchWrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
          <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
        </svg>
        <input type="search" class="searchInput" data-role="search" placeholder="<?php esc_attr_e('search…', 'kunaal-theme'); ?>" autocomplete="off" />
      </div>
      <?php endif; ?>
      
      <?php if ($show_reset) : ?>
      <!-- Reset -->
      <button class="resetBtn" data-action="reset" type="button"><?php esc_html_e('reset', 'kunaal-theme'); ?></button>
      <?php endif; ?>
    </div>
    
    <!-- Filter toggle button (mobile only) -->
    <button class="filterToggle" data-action="panel-toggle" type="button" aria-label="<?php esc_attr_e('Toggle filter panel', 'kunaal-theme'); ?>">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
        <path d="M3 6h18M7 12h10M10 18h4"/>
      </svg>
      <span><?php esc_html_e('filter', 'kunaal-theme'); ?></span>
    </button>
  </div>
</div>

