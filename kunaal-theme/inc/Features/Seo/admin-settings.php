<?php
/**
 * SEO Settings (WP Admin -> Settings -> SEO)
 *
 * Theme-owned SEO configuration should not bloat the Customizer.
 *
 * @package Kunaal_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue admin assets for Settings → SEO.
 *
 * Fixes the media picker not opening by ensuring wp.media is loaded and by using
 * a stable, idempotent JS initializer (instead of brittle inline scripts).
 */
function kunaal_seo_admin_enqueue_assets(string $hook_suffix): void {
    if ($hook_suffix !== 'settings_page_kunaal-seo') {
        return;
    }

    wp_enqueue_media();

    wp_enqueue_script(
        'kunaal-seo-admin-media',
        KUNAAL_THEME_URI . '/assets/js/admin/seo-media.js',
        array('media-editor'),
        (string) KUNAAL_THEME_VERSION,
        true
    );
}
add_action('admin_enqueue_scripts', 'kunaal_seo_admin_enqueue_assets');

function kunaal_seo_register_settings(): void {
    register_setting(
        'kunaal_seo',
        'kunaal_seo_settings',
        array(
            'type' => 'array',
            'sanitize_callback' => 'kunaal_seo_sanitize_settings',
            'default' => array(),
        )
    );

    add_settings_section('kunaal_seo_section_defaults', 'Defaults', '__return_false', 'kunaal_seo');
    add_settings_field(
        'default_description',
        'Default meta description',
        'kunaal_seo_field_textarea',
        'kunaal_seo',
        'kunaal_seo_section_defaults',
        array(
            'key' => 'default_description',
            'help' => 'Used when a page/post does not have an excerpt/subtitle/SEO description.',
        )
    );
    add_settings_field(
        'person_job_title',
        'Person job title (Schema)',
        'kunaal_seo_field_text',
        'kunaal_seo',
        'kunaal_seo_section_defaults',
        array(
            'key' => 'person_job_title',
            'help' => 'Schema.org Person.jobTitle (e.g., "Writer").',
        )
    );
    add_settings_field(
        'person_description',
        'Person description (Schema)',
        'kunaal_seo_field_textarea',
        'kunaal_seo',
        'kunaal_seo_section_defaults',
        array(
            'key' => 'person_description',
            'help' => 'Schema.org Person.description (keep it short and specific).',
        )
    );
    add_settings_field(
        'person_alternate_names',
        'Person alternate names (Schema)',
        'kunaal_seo_field_text',
        'kunaal_seo',
        'kunaal_seo_section_defaults',
        array(
            'key' => 'person_alternate_names',
            'help' => 'Comma-separated (e.g., "Kunaal, Kunaal W").',
        )
    );
    add_settings_field(
        'person_same_as',
        'Person sameAs URLs (Schema)',
        'kunaal_seo_field_textarea',
        'kunaal_seo',
        'kunaal_seo_section_defaults',
        array(
            'key' => 'person_same_as',
            'help' => 'One URL per line. These are merged with enabled social links.',
        )
    );
    add_settings_field(
        'default_share_image_id',
        'Default share image',
        'kunaal_seo_field_media',
        'kunaal_seo',
        'kunaal_seo_section_defaults',
        array(
            'key' => 'default_share_image_id',
            'help' => 'Used for Open Graph / Twitter when a post does not have an image.',
        )
    );

    add_settings_section('kunaal_seo_section_archives', 'Archive descriptions', '__return_false', 'kunaal_seo');
    add_settings_field('archive_essay_description', 'Essays archive description', 'kunaal_seo_field_textarea', 'kunaal_seo', 'kunaal_seo_section_archives', array('key' => 'archive_essay_description'));
    add_settings_field('archive_jotting_description', 'Jottings archive description', 'kunaal_seo_field_textarea', 'kunaal_seo', 'kunaal_seo_section_archives', array('key' => 'archive_jotting_description'));
    add_settings_field('archive_topic_description', 'Topics archive description', 'kunaal_seo_field_textarea', 'kunaal_seo', 'kunaal_seo_section_archives', array('key' => 'archive_topic_description'));

    add_settings_section('kunaal_seo_section_indexing', 'Indexing controls', '__return_false', 'kunaal_seo');
    add_settings_field(
        'noindex_search',
        'Noindex search results',
        'kunaal_seo_field_checkbox',
        'kunaal_seo',
        'kunaal_seo_section_indexing',
        array('key' => 'noindex_search', 'label' => 'Prevent indexing of search results pages.')
    );
    add_settings_field(
        'noindex_paged_archives',
        'Noindex paginated archives (page 2+)',
        'kunaal_seo_field_checkbox',
        'kunaal_seo',
        'kunaal_seo_section_indexing',
        array('key' => 'noindex_paged_archives', 'label' => 'Prevent indexing of /page/2+ on archives/taxonomies.')
    );
}
add_action('admin_init', 'kunaal_seo_register_settings');

/**
 * @param mixed $input
 * @return array<string, mixed>
 */
function kunaal_seo_sanitize_settings($input): array {
    $in = is_array($input) ? $input : array();
    $out = array();

    $out['default_description'] = isset($in['default_description']) ? sanitize_textarea_field((string) $in['default_description']) : '';
    $out['archive_essay_description'] = isset($in['archive_essay_description']) ? sanitize_textarea_field((string) $in['archive_essay_description']) : '';
    $out['archive_jotting_description'] = isset($in['archive_jotting_description']) ? sanitize_textarea_field((string) $in['archive_jotting_description']) : '';
    $out['archive_topic_description'] = isset($in['archive_topic_description']) ? sanitize_textarea_field((string) $in['archive_topic_description']) : '';
    $out['person_job_title'] = isset($in['person_job_title']) ? sanitize_text_field((string) $in['person_job_title']) : '';
    $out['person_description'] = isset($in['person_description']) ? sanitize_textarea_field((string) $in['person_description']) : '';
    $out['person_alternate_names'] = isset($in['person_alternate_names']) ? sanitize_text_field((string) $in['person_alternate_names']) : '';
    $out['person_same_as'] = isset($in['person_same_as']) ? sanitize_textarea_field((string) $in['person_same_as']) : '';
    $out['default_share_image_id'] = isset($in['default_share_image_id']) ? absint($in['default_share_image_id']) : 0;
    $out['noindex_search'] = !empty($in['noindex_search']) ? 1 : 0;
    $out['noindex_paged_archives'] = !empty($in['noindex_paged_archives']) ? 1 : 0;

    return $out;
}

function kunaal_seo_add_settings_page(): void {
    add_options_page('SEO', 'SEO', 'manage_options', 'kunaal-seo', 'kunaal_seo_render_settings_page');
}
add_action('admin_menu', 'kunaal_seo_add_settings_page');

function kunaal_seo_render_settings_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>SEO</h1>
        <?php if (kunaal_seo_is_yoast_active()) : ?>
            <div class="notice notice-warning">
                <p><strong>Yoast is active.</strong> Theme SEO will not output tags to avoid duplication. Disable Yoast to use theme-owned SEO.</p>
            </div>
        <?php endif; ?>
        <form action="options.php" method="post">
            <?php
            settings_fields('kunaal_seo');
            do_settings_sections('kunaal_seo');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function kunaal_seo_field_textarea(array $args): void {
    $key = (string) ($args['key'] ?? '');
    $settings = kunaal_seo_get_settings();
    $value = isset($settings[$key]) ? (string) $settings[$key] : '';
    $help = isset($args['help']) ? (string) $args['help'] : '';

    printf(
        '<textarea name="%1$s[%2$s]" rows="3" class="large-text">%3$s</textarea>',
        esc_attr('kunaal_seo_settings'),
        esc_attr($key),
        esc_textarea($value)
    );
    if ($help !== '') {
        printf('<p class="description">%s</p>', esc_html($help));
    }
}

function kunaal_seo_field_text(array $args): void {
    $key = (string) ($args['key'] ?? '');
    $settings = kunaal_seo_get_settings();
    $value = isset($settings[$key]) ? (string) $settings[$key] : '';
    $help = isset($args['help']) ? (string) $args['help'] : '';

    printf(
        '<input type="text" name="%1$s[%2$s]" value="%3$s" class="regular-text" />',
        esc_attr('kunaal_seo_settings'),
        esc_attr($key),
        esc_attr($value)
    );
    if ($help !== '') {
        printf('<p class="description">%s</p>', esc_html($help));
    }
}

function kunaal_seo_field_checkbox(array $args): void {
    $key = (string) ($args['key'] ?? '');
    $label = (string) ($args['label'] ?? '');
    $settings = kunaal_seo_get_settings();
    $checked = !empty($settings[$key]);

    printf(
        '<label><input type="checkbox" name="%1$s[%2$s]" value="1" %3$s /> %4$s</label>',
        esc_attr('kunaal_seo_settings'),
        esc_attr($key),
        checked($checked, true, false),
        esc_html($label)
    );
}

function kunaal_seo_field_media(array $args): void {
    $key = (string) ($args['key'] ?? '');
    $settings = kunaal_seo_get_settings();
    $id = isset($settings[$key]) ? absint($settings[$key]) : 0;
    $help = isset($args['help']) ? (string) $args['help'] : '';
    $preview = $id ? wp_get_attachment_image($id, array(160, 160)) : '';

    $field_name = 'kunaal_seo_settings[' . $key . ']';
    ?>
    <div data-kunaal-seo-media>
        <input type="hidden" name="<?php echo esc_attr($field_name); ?>" value="<?php echo esc_attr((string) $id); ?>" data-kunaal-seo-media-id />
        <div data-kunaal-seo-media-preview style="margin: 6px 0;">
            <?php echo $preview ? $preview : '<em>No image selected</em>'; ?>
        </div>
        <button type="button" class="button" data-kunaal-seo-media-pick>Select image</button>
        <button type="button" class="button" data-kunaal-seo-media-clear <?php echo $id ? '' : 'hidden'; ?>">Clear</button>
        <?php if ($help !== '') : ?>
            <p class="description"><?php echo esc_html($help); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

