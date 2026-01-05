<?php
/**
 * Per-post SEO meta box (theme-owned).
 *
 * @package Kunaal_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function kunaal_seo_add_meta_boxes(): void {
    foreach (array('essay', 'jotting', 'post', 'page') as $pt) {
        add_meta_box(
            'kunaal_seo_meta',
            'SEO (Theme)',
            'kunaal_seo_render_meta_box',
            $pt,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'kunaal_seo_add_meta_boxes');

function kunaal_seo_render_meta_box(WP_Post $post): void {
    wp_enqueue_media();
    wp_nonce_field('kunaal_seo_meta_box', 'kunaal_seo_meta_box_nonce');

    $title = (string) get_post_meta($post->ID, 'kunaal_seo_title', true);
    $desc = (string) get_post_meta($post->ID, 'kunaal_seo_description', true);
    $noindex = (bool) get_post_meta($post->ID, 'kunaal_seo_noindex', true);
    $img_id = absint(get_post_meta($post->ID, 'kunaal_seo_og_image_id', true));
    ?>
    <p>
        <label for="kunaal_seo_title"><strong>SEO title (optional)</strong></label><br/>
        <input type="text" class="widefat" id="kunaal_seo_title" name="kunaal_seo_title" value="<?php echo esc_attr($title); ?>" />
        <span class="description">If empty, the normal post title is used.</span>
    </p>
    <p>
        <label for="kunaal_seo_description"><strong>SEO description (optional)</strong></label><br/>
        <textarea class="widefat" rows="3" id="kunaal_seo_description" name="kunaal_seo_description"><?php echo esc_textarea($desc); ?></textarea>
        <span class="description">If empty, the subtitle → excerpt → content → default fallback is used.</span>
    </p>
    <p>
        <label>
            <input type="checkbox" name="kunaal_seo_noindex" value="1" <?php checked($noindex, true); ?> />
            Noindex this page/post (theme only)
        </label>
    </p>
    <p>
        <strong>Share image override (optional)</strong><br/>
        <input type="hidden" name="kunaal_seo_og_image_id" value="<?php echo esc_attr((string) $img_id); ?>" data-kunaal-seo-meta-img-id />
        <span data-kunaal-seo-meta-img-preview>
            <?php echo $img_id ? wp_get_attachment_image($img_id, array(160, 160)) : '<em>No image selected</em>'; ?>
        </span><br/>
        <button type="button" class="button" data-kunaal-seo-meta-img-pick>Select image</button>
        <button type="button" class="button" data-kunaal-seo-meta-img-clear <?php echo $img_id ? '' : 'hidden'; ?>">Clear</button>
    </p>
    <script>
      (function(){
        const box = document.currentScript && document.currentScript.parentElement;
        if (!box) return;
        const idInput = box.querySelector('[data-kunaal-seo-meta-img-id]');
        const preview = box.querySelector('[data-kunaal-seo-meta-img-preview]');
        const pick = box.querySelector('[data-kunaal-seo-meta-img-pick]');
        const clear = box.querySelector('[data-kunaal-seo-meta-img-clear]');
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

function kunaal_seo_save_meta_box(int $post_id): void {
    if (!isset($_POST['kunaal_seo_meta_box_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['kunaal_seo_meta_box_nonce'])), 'kunaal_seo_meta_box')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $title = isset($_POST['kunaal_seo_title']) ? sanitize_text_field(wp_unslash($_POST['kunaal_seo_title'])) : '';
    $desc = isset($_POST['kunaal_seo_description']) ? sanitize_textarea_field(wp_unslash($_POST['kunaal_seo_description'])) : '';
    $noindex = !empty($_POST['kunaal_seo_noindex']) ? 1 : 0;
    $img_id = isset($_POST['kunaal_seo_og_image_id']) ? absint(wp_unslash($_POST['kunaal_seo_og_image_id'])) : 0;

    update_post_meta($post_id, 'kunaal_seo_title', $title);
    update_post_meta($post_id, 'kunaal_seo_description', $desc);
    update_post_meta($post_id, 'kunaal_seo_noindex', $noindex);
    update_post_meta($post_id, 'kunaal_seo_og_image_id', $img_id);
}
add_action('save_post', 'kunaal_seo_save_meta_box');

