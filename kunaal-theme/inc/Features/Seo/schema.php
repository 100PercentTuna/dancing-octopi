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

    $person = array(
        '@type' => 'Person',
        '@id' => $home . '#person',
        'name' => $author_name,
        'url' => $home,
    );

    // Enrich Person schema for name discovery queries (Kunaal / Kunaal W / etc).
    $alt_raw = trim((string) kunaal_seo_setting('person_alternate_names', ''));
    if ($alt_raw !== '') {
        $alts = array_map('trim', explode(',', $alt_raw));
        $alts = array_values(array_filter($alts, static fn($v) => $v !== ''));
    } else {
        $alts = array();
        if ($author_first !== '') {
            $alts[] = $author_first;
        }
        if ($author_last !== '') {
            $alts[] = $author_first . ' ' . strtoupper(substr($author_last, 0, 1));
        }
        // Requested common variants (safe defaults).
        $alts[] = 'Kunaal';
        $alts[] = 'Kunaal W';
        $alts = array_values(array_unique(array_filter(array_map('trim', $alts), static fn($v) => $v !== '')));
    }
    if (!empty($alts)) {
        $person['alternateName'] = $alts;
    }

    $job_title = trim((string) kunaal_seo_setting('person_job_title', 'Writer'));
    if ($job_title !== '') {
        $person['jobTitle'] = $job_title;
    }

    $desc = trim((string) kunaal_seo_setting('person_description', 'Essays on how human collectives work'));
    if ($desc !== '') {
        $person['description'] = $desc;
    }

    $same_as = array();

    $same_raw = trim((string) kunaal_seo_setting('person_same_as', ''));
    if ($same_raw !== '') {
        $lines = preg_split("/\r\n|\r|\n/", $same_raw) ?: array();
        foreach ($lines as $line) {
            $url = trim((string) $line);
            if ($url === '') {
                continue;
            }
            $same_as[] = esc_url_raw($url);
        }
    }

    // Merge enabled social links (Customizer) if available.
    if (function_exists('kunaal_get_social_link')) {
        foreach (array('linkedin', 'twitter', 'instagram') as $platform) {
            $data = kunaal_get_social_link($platform);
            if (is_array($data) && !empty($data['url'])) {
                $same_as[] = esc_url_raw((string) $data['url']);
            }
        }
    }

    // Ensure stable defaults for Kunaal's profiles (fallback if none configured).
    if (empty($same_as)) {
        $same_as = array(
            'https://www.linkedin.com/in/kunaalw/',
            'https://x.com/100PercentTuna',
            'https://www.instagram.com/hundredpercenttuna/',
        );
    }

    $same_as = array_values(array_unique(array_filter(array_map('trim', $same_as), static fn($v) => $v !== '')));
    if (!empty($same_as)) {
        $person['sameAs'] = $same_as;
    }

    $graph[] = $person;

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

