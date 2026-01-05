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
    if (!current_user_can('manage_options')) return;
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

    wp_enqueue_media();

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
    <script>
      (function(){
        const root = document.currentScript && document.currentScript.previousElementSibling;
        if (!root) return;
        const idInput = root.querySelector('[data-kunaal-seo-media-id]');
        const preview = root.querySelector('[data-kunaal-seo-media-preview]');
        const pick = root.querySelector('[data-kunaal-seo-media-pick]');
        const clear = root.querySelector('[data-kunaal-seo-media-clear]');
        if (!idInput || !preview || !pick || !clear || !window.wp || !wp.media) return;

        let frame;
        pick.addEventListener('click', function(){
          if (frame) { frame.open(); return; }
          frame = wp.media({ title: 'Select image', button: { text: 'Use image' }, multiple: false });
          frame.on('select', function(){
            const attachment = frame.state().get('selection').first();
            if (!attachment) return;
            const data = attachment.toJSON();
            idInput.value = String(data.id || '');
            const thumb = (data.sizes && (data.sizes.thumbnail || data.sizes.medium)) ? (data.sizes.thumbnail || data.sizes.medium).url : data.url;
            preview.innerHTML = thumb ? '<img src=\"' + thumb + '\" style=\"max-width:160px;height:auto;\" />' : '<em>Selected</em>';
            clear.classList.remove('hidden');
          });
          frame.open();
        });
        clear.addEventListener('click', function(){
          idInput.value = '';
          preview.innerHTML = '<em>No image selected</em>';
          clear.classList.add('hidden');
        });
      })();
    </script>
    <?php
}

