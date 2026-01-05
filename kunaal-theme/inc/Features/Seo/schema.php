<?php
/**
 * Schema.org JSON-LD (theme-owned SEO).
 *
 * @package Kunaal_Theme
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function kunaal_seo_print_schema(): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    if (kunaal_seo_is_yoast_active()) {
        return;
    }
    if (kunaal_seo_should_noindex()) {
        return;
    }

    $home = home_url('/');
    $site_name = (string) get_bloginfo('name');
    $site_desc = (string) get_bloginfo('description');

    $author_first = (string) kunaal_mod('kunaal_author_first_name', 'Kunaal');
    $author_last = (string) kunaal_mod('kunaal_author_last_name', 'Wadhwa');
    $author_name = trim($author_first . ' ' . $author_last);

    $graph = array();

    $graph[] = array(
        '@type' => 'WebSite',
        '@id' => $home . '#website',
        'url' => $home,
        'name' => $site_name,
        'description' => $site_desc,
    );

    $graph[] = array(
        '@type' => 'Person',
        '@id' => $home . '#person',
        'name' => $author_name,
        'url' => $home,
    );

    if (is_singular(array('essay', 'jotting', 'post', 'page'))) {
        $post_id = get_queried_object_id();
        $url = (string) get_permalink($post_id);
        $title = kunaal_seo_get_title();
        $desc = kunaal_seo_get_description();
        $image = kunaal_seo_get_share_image_url();

        $type = is_singular('page') ? 'WebPage' : 'BlogPosting';

        $item = array(
            '@type' => $type,
            '@id' => $url . '#primary',
            'mainEntityOfPage' => array(
                '@type' => 'WebPage',
                '@id' => $url,
            ),
            'headline' => $title,
            'description' => $desc,
            'url' => $url,
            'author' => array(
                '@type' => 'Person',
                '@id' => $home . '#person',
                'name' => $author_name,
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => $site_name,
                'url' => $home,
            ),
            'datePublished' => get_the_date('c', $post_id),
            'dateModified' => get_the_modified_date('c', $post_id),
        );

        if ($image !== '') {
            $item['image'] = array($image);
        }

        $graph[] = $item;
    }

    $data = array(
        '@context' => 'https://schema.org',
        '@graph' => $graph,
    );

    echo "<script type=\"application/ld+json\">" . wp_json_encode($data) . "</script>\n";
}
add_action('wp_head', 'kunaal_seo_print_schema', 6);

