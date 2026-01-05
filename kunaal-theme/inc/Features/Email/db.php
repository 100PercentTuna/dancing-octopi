<?php
/**
 * Subscriber DB
 *
 * Stores subscribers and outbound email queue in dedicated tables for performance.
 *
 * @package Kunaal_Theme
 * @since 5.0.0
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Schema version for subscriber/email tables.
 * Bump when schema changes and implement an upgrade path.
 */
const KUNAAL_SUBSCRIBERS_SCHEMA_VERSION = 1;

/**
 * @return string
 */
function kunaal_subscribers_table(): string {
    global $wpdb;
    return $wpdb->prefix . 'kunaal_subscribers';
}

/**
 * @return string
 */
function kunaal_email_queue_table(): string {
    global $wpdb;
    return $wpdb->prefix . 'kunaal_email_queue';
}

/**
 * @return string
 */
function kunaal_email_events_table(): string {
    global $wpdb;
    return $wpdb->prefix . 'kunaal_email_events';
}

/**
 * Install/upgrade subscriber tables.
 *
 * Safe to run multiple times.
 */
function kunaal_subscribers_install_schema(): void {
    if (!function_exists('dbDelta')) {
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    }

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $subscribers = kunaal_subscribers_table();
    $queue = kunaal_email_queue_table();
    $events = kunaal_email_events_table();

    // Subscribers table.
    $sql_subscribers = "CREATE TABLE {$subscribers} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        email VARCHAR(190) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        token_hash CHAR(64) DEFAULT NULL,
        created_gmt DATETIME NOT NULL,
        confirmed_gmt DATETIME DEFAULT NULL,
        unsubscribed_gmt DATETIME DEFAULT NULL,
        last_email_sent_gmt DATETIME DEFAULT NULL,
        last_confirm_sent_gmt DATETIME DEFAULT NULL,
        source VARCHAR(64) DEFAULT NULL,
        meta_json LONGTEXT DEFAULT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY email_unique (email),
        KEY status_idx (status),
        KEY created_idx (created_gmt)
    ) {$charset_collate};";

    // Email queue table (one row per recipient/send).
    $sql_queue = "CREATE TABLE {$queue} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        type VARCHAR(24) NOT NULL,
        subscriber_id BIGINT(20) UNSIGNED NOT NULL,
        post_id BIGINT(20) UNSIGNED DEFAULT NULL,
        subject TEXT NOT NULL,
        body LONGTEXT NOT NULL,
        headers_json LONGTEXT DEFAULT NULL,
        scheduled_gmt DATETIME NOT NULL,
        attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
        last_error TEXT DEFAULT NULL,
        status VARCHAR(16) NOT NULL DEFAULT 'queued',
        created_gmt DATETIME NOT NULL,
        sent_gmt DATETIME DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY status_sched_idx (status, scheduled_gmt),
        KEY subscriber_idx (subscriber_id),
        KEY post_idx (post_id)
    ) {$charset_collate};";

    // Optional events table (opens/clicks/unsubscribes).
    $sql_events = "CREATE TABLE {$events} (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        subscriber_id BIGINT(20) UNSIGNED NOT NULL,
        queue_id BIGINT(20) UNSIGNED DEFAULT NULL,
        event VARCHAR(24) NOT NULL,
        url TEXT DEFAULT NULL,
        ua_hash CHAR(64) DEFAULT NULL,
        created_gmt DATETIME NOT NULL,
        PRIMARY KEY  (id),
        KEY subscriber_event_idx (subscriber_id, event),
        KEY created_idx (created_gmt)
    ) {$charset_collate};";

    dbDelta($sql_subscribers);
    dbDelta($sql_queue);
    dbDelta($sql_events);

    update_option('kunaal_subscribers_schema_version', KUNAAL_SUBSCRIBERS_SCHEMA_VERSION, true);
}

/**
 * Upgrade hook (runs on admin requests).
 */
function kunaal_subscribers_maybe_upgrade(): void {
    $current = (int) get_option('kunaal_subscribers_schema_version', 0);
    if ($current < KUNAAL_SUBSCRIBERS_SCHEMA_VERSION) {
        kunaal_subscribers_install_schema();
    }
}
add_action('admin_init', 'kunaal_subscribers_maybe_upgrade');

/**
 * Hash a token for storage.
 *
 * @param string $token
 * @return string
 */
function kunaal_subscribers_hash_token(string $token): string {
    return hash_hmac('sha256', $token, wp_salt('auth'));
}

/**
 * Get subscriber row by email.
 *
 * @param string $email
 * @return array<string,mixed>|null
 */
function kunaal_subscriber_get_by_email(string $email): array|null {
    global $wpdb;
    $email = strtolower(trim($email));
    if (!is_email($email)) {
        return null;
    }
    $table = kunaal_subscribers_table();
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared below
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE email = %s LIMIT 1", $email), ARRAY_A);
    return is_array($row) ? $row : null;
}

/**
 * Get subscriber row by id.
 *
 * @param int $id
 * @return array<string,mixed>|null
 */
function kunaal_subscriber_get_by_id(int $id): array|null {
    global $wpdb;
    $table = kunaal_subscribers_table();
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared below
    $row = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE id = %d LIMIT 1", $id), ARRAY_A);
    return is_array($row) ? $row : null;
}

/**
 * Insert subscriber (pending by default).
 *
 * @param string $email
 * @param string $status
 * @param string $source
 * @param string|null $token_hash
 * @return int|WP_Error
 */
function kunaal_subscriber_insert(string $email, string $status = 'pending', string $source = '', string|null $token_hash = null): int|WP_Error {
    global $wpdb;
    $email = strtolower(trim($email));
    if (!is_email($email)) {
        return new WP_Error('invalid_email', 'Invalid email.');
    }
    $table = kunaal_subscribers_table();

    $data = array(
        'email' => $email,
        'status' => $status,
        'token_hash' => $token_hash,
        'created_gmt' => gmdate('Y-m-d H:i:s'),
        'source' => $source !== '' ? $source : null,
    );
    $formats = array('%s', '%s', '%s', '%s', '%s');

    $ok = $wpdb->insert($table, $data, $formats);
    if ($ok === false) {
        return new WP_Error('insert_failed', 'Failed to create subscriber.');
    }
    return (int) $wpdb->insert_id;
}

/**
 * Update subscriber status timestamps.
 *
 * @param int $id
 * @param string $status
 * @return bool
 */
function kunaal_subscriber_update_status(int $id, string $status): bool {
    global $wpdb;
    $table = kunaal_subscribers_table();
    $updates = array('status' => $status);
    $formats = array('%s');

    if ($status === 'confirmed') {
        $updates['confirmed_gmt'] = gmdate('Y-m-d H:i:s');
        $formats[] = '%s';
        $updates['token_hash'] = null;
        $formats[] = '%s';
    }
    if ($status === 'unsubscribed') {
        $updates['unsubscribed_gmt'] = gmdate('Y-m-d H:i:s');
        $formats[] = '%s';
    }

    $updated = $wpdb->update($table, $updates, array('id' => $id), $formats, array('%d'));
    return $updated !== false;
}

/**
 * Set/replace confirmation token hash and mark last_confirm_sent_gmt.
 *
 * @param int $id
 * @param string $token_hash
 * @return bool
 */
function kunaal_subscriber_set_token_hash(int $id, string $token_hash): bool {
    global $wpdb;
    $table = kunaal_subscribers_table();
    $updated = $wpdb->update(
        $table,
        array(
            'token_hash' => $token_hash,
            'last_confirm_sent_gmt' => gmdate('Y-m-d H:i:s'),
        ),
        array('id' => $id),
        array('%s', '%s'),
        array('%d')
    );
    return $updated !== false;
}

/**
 * One-time migration from legacy CPT `kunaal_subscriber` into DB table.
 *
 * @return array{migrated:int,skipped:int}
 */
function kunaal_subscribers_migrate_from_cpt(): array {
    $done = (bool) get_option('kunaal_subscribers_migrated_v1', false);
    if ($done) {
        return array('migrated' => 0, 'skipped' => 0);
    }

    if (!post_type_exists('kunaal_subscriber')) {
        update_option('kunaal_subscribers_migrated_v1', true, true);
        return array('migrated' => 0, 'skipped' => 0);
    }

    kunaal_subscribers_install_schema();

    $q = new WP_Query(array(
        'post_type' => 'kunaal_subscriber',
        'post_status' => 'private',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'no_found_rows' => true,
    ));

    $migrated = 0;
    $skipped = 0;
    foreach ($q->posts as $post_id) {
        $email = (string) get_post_meta($post_id, 'kunaal_email', true);
        $status = (string) get_post_meta($post_id, 'kunaal_status', true);
        $status = $status !== '' ? $status : 'pending';

        $existing = kunaal_subscriber_get_by_email($email);
        if ($existing) {
            $skipped++;
            continue;
        }

        $token = (string) get_post_meta($post_id, 'kunaal_token', true);
        $token_hash = $token !== '' ? kunaal_subscribers_hash_token($token) : null;
        $created = (string) get_post_meta($post_id, 'kunaal_created_gmt', true);

        $id = kunaal_subscriber_insert($email, $status, 'legacy_cpt', $token_hash);
        if (is_wp_error($id)) {
            $skipped++;
            continue;
        }

        // Preserve created timestamp if present.
        if ($created !== '' && strtotime($created) !== false) {
            global $wpdb;
            $table = kunaal_subscribers_table();
            // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- prepared below
            $wpdb->query($wpdb->prepare("UPDATE {$table} SET created_gmt = %s WHERE id = %d", gmdate('Y-m-d H:i:s', strtotime($created)), (int) $id));
        }

        $migrated++;
    }

    update_option('kunaal_subscribers_migrated_v1', true, true);
    return array('migrated' => $migrated, 'skipped' => $skipped);
}


