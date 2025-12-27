<?php
/**
 * About Page Helper Functions
 * 
 * Reusable functions for About page template
 *
 * @package Kunaal_Theme
 * @since 4.28.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render panorama section
 * 
 * @param array $panoramas Array of panorama items
 * @param string $extra_class Additional CSS class(es) to add
 * @return void Outputs HTML
 */
function kunaal_render_panoramas($panoramas, $extra_class = '') {
    if (empty($panoramas)) {
        return;
    }
    
    foreach ($panoramas as $panorama) {
        $height_class = 'h-' . esc_attr($panorama['height']);
        $cut_class = $panorama['cut'] !== 'none' ? PANORAMA_CUT_PREFIX . esc_attr($panorama['cut']) : '';
        $bg_class = $panorama['bg'] === 'warm' ? PANORAMA_BG_WARM : '';
        
        // Determine loading strategy
        $loading = 'lazy';
        $fetchpriority = '';
        // First panorama after rabbit holes uses eager loading (high priority)
        if (!empty($extra_class) && strpos($extra_class, 'after-rabbit-holes') !== false) {
            $loading = 'eager';
            $fetchpriority = ' fetchpriority="high"';
        }
        
        $classes = trim($height_class . $cut_class . $bg_class . (!empty($extra_class) ? ' ' . $extra_class : ''));
        ?>
        <!-- PANORAMA -->
        <div class="panorama <?php echo esc_attr($classes); ?>" data-speed="<?php echo esc_attr($panorama['speed']); ?>">
            <div class="panorama-inner">
                <img alt="" class="panorama-img" decoding="async" loading="<?php echo esc_attr($loading); ?>"<?php echo $fetchpriority; ?> src="<?php echo esc_url($panorama['image']); ?>"/>
            </div>
        </div>
        <?php
    }
}

