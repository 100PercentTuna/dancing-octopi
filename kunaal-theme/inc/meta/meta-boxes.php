<?php
/**
 * Meta Boxes
 * 
 * Registers meta boxes for classic editor fallback (Gutenberg uses JS sidebar).
 *
 * @package Kunaal_Theme
 * @since 4.30.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register Meta Boxes for Classic Editor fallback only
 * Gutenberg uses the JavaScript sidebar plugin instead
 */
function kunaal_add_meta_boxes(): void {
    $is_block_editor = false;
    $post_type = '';
    if (function_exists('use_block_editor_for_post_type')) {
        $screen = get_current_screen();
        $post_type = ($screen && isset($screen->post_type)) ? (string) $screen->post_type : '';
        if ($post_type && use_block_editor_for_post_type($post_type)) {
            $is_block_editor = true;
        }
    }

    // Always add the subscriber email meta box (works in both classic + block editor).
    if ($post_type === 'essay' || $post_type === 'jotting') {
        add_meta_box(
            'kunaal_subscriber_email',
            'Subscriber Email',
            'kunaal_subscriber_email_meta_box_callback',
            $post_type,
            'side',
            'high'
        );
    }

    // Only add the classic “required fields” meta boxes when NOT using Gutenberg.
    // Gutenberg editing uses REST meta + editor UI patterns; these classic boxes are fallback.
    if ($is_block_editor) {
        return;
    }
    
    add_meta_box(
        'kunaal_essay_details',
        'Essay Details (Required)',
        'kunaal_essay_meta_box_callback',
        'essay',
        'side',
        'high'
    );

    add_meta_box(
        'kunaal_jotting_details',
        'Jotting Details',
        'kunaal_jotting_meta_box_callback',
        'jotting',
        'side',
        'high'
    );
    
    add_meta_box(
        'kunaal_card_image',
        'Card Image (Required for Essays)',
        'kunaal_card_image_meta_box_callback',
        'essay',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'kunaal_add_meta_boxes');

/**
 * Subscriber Email Meta Box (essay + jotting)
 *
 * Controls whether and when a post triggers an email to subscribers upon publish.
 */
function kunaal_subscriber_email_meta_box_callback(WP_Post $post): void {
    wp_nonce_field('kunaal_save_meta', 'kunaal_meta_nonce');

    $enabled = (bool) get_post_meta($post->ID, 'kunaal_notify_subscribers', true);
    $mode = (string) get_post_meta($post->ID, 'kunaal_notify_mode', true);
    $mode = $mode !== '' ? $mode : 'delay';
    $delay_minutes = (int) get_post_meta($post->ID, 'kunaal_notify_delay_minutes', true);
    $scheduled_gmt = (string) get_post_meta($post->ID, 'kunaal_notify_scheduled_gmt', true);

    // Display scheduled time as local (site timezone) in the input.
    $scheduled_local = '';
    if ($scheduled_gmt !== '') {
        $scheduled_local = get_date_from_gmt($scheduled_gmt, 'Y-m-d\\TH:i');
    }

    ?>
    <p>
        <label>
            <input type="checkbox" name="kunaal_notify_subscribers" value="1" <?php checked($enabled); ?> />
            <strong>Email subscribers when this is published</strong>
        </label>
    </p>

    <p style="margin-bottom:6px;">
        <label for="kunaal_notify_mode"><strong>Send timing</strong></label><br>
        <select id="kunaal_notify_mode" name="kunaal_notify_mode" style="width:100%;">
            <option value="delay" <?php selected($mode, 'delay'); ?>>Delay (minutes)</option>
            <option value="time" <?php selected($mode, 'time'); ?>>Specific time</option>
        </select>
    </p>

    <div id="kunaal-notify-delay" style="<?php echo $mode === 'delay' ? '' : 'display:none;'; ?>">
        <p style="margin-top:0;">
            <label for="kunaal_notify_delay_minutes">Delay (minutes)</label><br>
            <input type="number" id="kunaal_notify_delay_minutes" name="kunaal_notify_delay_minutes" value="<?php echo esc_attr((string) $delay_minutes); ?>" min="0" step="1" style="width:100%;" />
        </p>
    </div>

    <div id="kunaal-notify-time" style="<?php echo $mode === 'time' ? '' : 'display:none;'; ?>">
        <p style="margin-top:0;">
            <label for="kunaal_notify_scheduled_local">Scheduled time</label><br>
            <input type="datetime-local" id="kunaal_notify_scheduled_local" name="kunaal_notify_scheduled_local" value="<?php echo esc_attr($scheduled_local); ?>" style="width:100%;" />
        </p>
    </div>

    <p style="font-size:11px;color:#666;margin-top:6px;">
        Note: sending is queued and may be delayed by your configured global minimum delay.
    </p>

    <script>
    (function() {
        var sel = document.getElementById('kunaal_notify_mode');
        if (!sel) return;
        var delayEl = document.getElementById('kunaal-notify-delay');
        var timeEl = document.getElementById('kunaal-notify-time');
        sel.addEventListener('change', function() {
            var v = sel.value;
            if (delayEl) delayEl.style.display = (v === 'delay') ? '' : 'none';
            if (timeEl) timeEl.style.display = (v === 'time') ? '' : 'none';
        });
    })();
    </script>
    <?php
}

/**
 * Essay Meta Box
 */
function kunaal_essay_meta_box_callback(WP_Post $post): void {
    wp_nonce_field('kunaal_save_meta', 'kunaal_meta_nonce');
    $subtitle = get_post_meta($post->ID, 'kunaal_subtitle', true);
    $read_time = get_post_meta($post->ID, 'kunaal_read_time', true);
    ?>
    <p>
        <label for="kunaal_subtitle"><strong>Subtitle/Dek</strong></label><br>
        <input type="text" id="kunaal_subtitle" name="kunaal_subtitle" value="<?php echo esc_attr($subtitle); ?>" style="width:100%;" />
        <span style="font-size:11px;color:#666;">Appears below the title on cards and article header</span>
    </p>
    <?php if ($read_time) : ?>
    <p style="background:#e7f5ff;padding:8px;border-left:3px solid #1E5AFF;font-size:12px;">
        <strong>Reading Time:</strong> <?php echo esc_html($read_time); ?> minutes (auto-calculated)
    </p>
    <?php endif; ?>
    <p style="background:#fff3cd;padding:8px;border-left:3px solid #ffc107;font-size:11px;">
        <strong>Note:</strong> Essays require at least one Topic tag and a Card Image to be published. Reading time is calculated automatically based on word count.
    </p>
    <?php
}

/**
 * Jotting Meta Box
 */
function kunaal_jotting_meta_box_callback(WP_Post $post): void {
    wp_nonce_field('kunaal_save_meta', 'kunaal_meta_nonce');
    $subtitle = get_post_meta($post->ID, 'kunaal_subtitle', true);
    ?>
    <p>
        <label for="kunaal_jotting_subtitle"><strong>Subtitle/Description</strong></label><br>
        <input type="text" id="kunaal_jotting_subtitle" name="kunaal_subtitle" value="<?php echo esc_attr($subtitle); ?>" style="width:100%;" />
        <span style="font-size:11px;color:#666;">Short description shown in the jottings list</span>
    </p>
    <?php
}

/**
 * Card Image Meta Box
 */
function kunaal_card_image_meta_box_callback(WP_Post $post): void {
    $card_image = get_post_meta($post->ID, 'kunaal_card_image', true);
    $card_image_url = $card_image ? wp_get_attachment_image_url($card_image, 'medium') : '';
    ?>
    <div id="kunaal-card-image-container">
        <?php if ($card_image_url) : ?>
            <img src="<?php echo esc_url($card_image_url); ?>" alt="<?php echo esc_attr__('Card preview', 'kunaal-theme'); ?>" style="max-width:100%;margin-bottom:10px;" />
        <?php endif; ?>
    </div>
    <input type="hidden" id="kunaal_card_image" name="kunaal_card_image" value="<?php echo esc_attr($card_image); ?>" />
    <button type="button" class="button" id="kunaal-upload-card-image">Select Card Image</button>
    <?php if ($card_image) : ?>
        <button type="button" class="button" id="kunaal-remove-card-image">Remove</button>
    <?php endif; ?>
    <p style="font-size:11px;color:#666;margin-top:8px;">
        This image appears on the card grid. Recommended: 4:5 aspect ratio (e.g., 800x1000px).
        Can be different from the hero image in the post.
    </p>
    <script>
    jQuery(document).ready(function($) {
        var frame;
        $('#kunaal-upload-card-image').on('click', function(e) {
            e.preventDefault();
            if (frame) { frame.open(); return; }
            frame = wp.media({
                title: 'Select Card Image',
                button: { text: 'Use this image' },
                multiple: false
            });
            frame.on('select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                $('#kunaal_card_image').val(attachment.id);
                $('#kunaal-card-image-container').html('<img src="' + attachment.url + '" style="max-width:100%;margin-bottom:10px;" />');
                if ($('#kunaal-remove-card-image').length === 0) {
                    $('#kunaal-upload-card-image').after('<button type="button" class="button" id="kunaal-remove-card-image">Remove</button>');
                }
            });
            frame.open();
        });
        $(document).on('click', '#kunaal-remove-card-image', function(e) {
            e.preventDefault();
            $('#kunaal_card_image').val('');
            $('#kunaal-card-image-container').html('');
            $(this).remove();
        });
    });
    </script>
    <?php
}

/**
 * Calculate Reading Time Automatically
 * Based on word count at configurable words per minute
 *
 * @param int $post_id Post ID
 * @return int Reading time in minutes
 */
function kunaal_calculate_reading_time(int $post_id): int {
    $content = get_post_field('post_content', $post_id);
    
    // Strip shortcodes and HTML tags
    $content = strip_shortcodes($content);
    $content = wp_strip_all_tags($content);
    
    // Count words
    $word_count = str_word_count($content);
    
    // Calculate reading time using constant
    $wpm = defined('KUNAAL_READING_SPEED_WPM') ? KUNAAL_READING_SPEED_WPM : 200;
    return (int) max(1, ceil($word_count / $wpm));
}

/**
 * Save Meta Box Data
 */
function kunaal_save_meta_box_data(int $post_id): void {
    // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nonce is verified below
    if (!isset($_POST['kunaal_meta_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['kunaal_meta_nonce'])), 'kunaal_save_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['kunaal_subtitle'])) {
        update_post_meta($post_id, 'kunaal_subtitle', sanitize_text_field(wp_unslash($_POST['kunaal_subtitle'])));
    }

    // Subscriber email settings (essay + jotting)
    $notify_enabled = isset($_POST['kunaal_notify_subscribers']) && wp_validate_boolean(wp_unslash($_POST['kunaal_notify_subscribers']));
    update_post_meta($post_id, 'kunaal_notify_subscribers', $notify_enabled ? '1' : '0');

    $mode = isset($_POST['kunaal_notify_mode']) ? sanitize_text_field(wp_unslash($_POST['kunaal_notify_mode'])) : 'delay';
    $mode = in_array($mode, array('delay', 'time'), true) ? $mode : 'delay';
    update_post_meta($post_id, 'kunaal_notify_mode', $mode);

    $delay_minutes = isset($_POST['kunaal_notify_delay_minutes']) ? absint(wp_unslash($_POST['kunaal_notify_delay_minutes'])) : 0;
    update_post_meta($post_id, 'kunaal_notify_delay_minutes', $delay_minutes);

    $scheduled_local = isset($_POST['kunaal_notify_scheduled_local']) ? sanitize_text_field(wp_unslash($_POST['kunaal_notify_scheduled_local'])) : '';
    if ($scheduled_local !== '') {
        // Convert local datetime string to GMT for storage.
        $scheduled_gmt = get_gmt_from_date($scheduled_local, 'Y-m-d H:i:s');
        update_post_meta($post_id, 'kunaal_notify_scheduled_gmt', $scheduled_gmt);
    } else {
        delete_post_meta($post_id, 'kunaal_notify_scheduled_gmt');
    }
    
    // Auto-calculate reading time for essays
    $post_type = get_post_type($post_id);
    if ($post_type === 'essay') {
        $reading_time = kunaal_calculate_reading_time($post_id);
        update_post_meta($post_id, 'kunaal_read_time', $reading_time);
    }
    
    if (isset($_POST['kunaal_card_image'])) {
        update_post_meta($post_id, 'kunaal_card_image', absint(wp_unslash($_POST['kunaal_card_image'])));
    }
}
add_action('save_post', 'kunaal_save_meta_box_data');

/**
 * Register meta fields for REST API access (needed for Gutenberg)
 */
function kunaal_register_meta_fields(): void {
    register_post_meta('essay', 'kunaal_read_time', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));

    register_post_meta('essay', 'kunaal_subtitle', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));

    register_post_meta('essay', 'kunaal_card_image', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'integer',
        'sanitize_callback' => 'absint',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));

    register_post_meta('jotting', 'kunaal_subtitle', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));

    register_post_meta('essay', 'kunaal_summary', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));

    register_post_meta('jotting', 'kunaal_summary', array(
        'show_in_rest' => true,
        'single' => true,
        'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback' => function() {
            return current_user_can('edit_posts');
        },
    ));

    // Subscriber email settings (essay + jotting)
    foreach (array('essay', 'jotting') as $pt) {
        register_post_meta($pt, 'kunaal_notify_subscribers', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
            'sanitize_callback' => 'wp_validate_boolean',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));

        register_post_meta($pt, 'kunaal_notify_mode', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));

        register_post_meta($pt, 'kunaal_notify_delay_minutes', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'integer',
            'sanitize_callback' => 'absint',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));

        register_post_meta($pt, 'kunaal_notify_scheduled_gmt', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));
    }
}
add_action('init', 'kunaal_register_meta_fields');

/**
 * Enqueue media uploader in admin
 */
function kunaal_admin_scripts(string $hook): void {
    if ('post.php' === $hook || 'post-new.php' === $hook) {
        wp_enqueue_media();
    }
}
add_action('admin_enqueue_scripts', 'kunaal_admin_scripts');

