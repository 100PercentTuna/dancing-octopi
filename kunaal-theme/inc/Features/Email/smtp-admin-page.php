<?php
/**
 * SMTP Diagnostics Admin Page
 *
 * Avoids front-end nonce failures caused by full-page caching.
 * Provides a wp-admin Tools page to run:
 * - TCP reachability test
 * - Send test (SMTP vs PHP fallback)
 *
 * @package Kunaal_Theme
 * @since 4.99.16
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function kunaal_register_smtp_diagnostics_admin_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    add_management_page(
        'Kunaal SMTP Diagnostics',
        'SMTP Diagnostics',
        'manage_options',
        'kunaal-smtp-diagnostics',
        'kunaal_render_smtp_diagnostics_admin_page'
    );
}
add_action('admin_menu', 'kunaal_register_smtp_diagnostics_admin_page');

function kunaal_render_smtp_diagnostics_admin_page(): void {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized.');
    }

    $nonce = wp_create_nonce('kunaal_theme_nonce');
    ?>
    <div class="wrap">
        <h1>Kunaal SMTP Diagnostics</h1>
        <p>This page runs diagnostics using fresh wp-admin nonces (not affected by front-end caching).</p>

        <h2>1) TCP Reachability</h2>
        <p><button class="button button-primary" id="kunaal-smtp-tcp">Run TCP test</button></p>

        <h2>2) Send Test</h2>
        <p>
            <label>Recipient email:
                <input type="email" id="kunaal-smtp-to" value="<?php echo esc_attr(wp_get_current_user()->user_email ?? ''); ?>" style="min-width: 320px;">
            </label>
        </p>
        <p>
            <label>Type:
                <select id="kunaal-smtp-type">
                    <option value="contact">contact</option>
                    <option value="subscribe">subscribe</option>
                </select>
            </label>
        </p>
        <p>
            <button class="button button-primary" id="kunaal-smtp-send">Send test (normal)</button>
            <button class="button" id="kunaal-smtp-send-php">Send test (force PHP mail)</button>
        </p>

        <h2>Output</h2>
        <pre id="kunaal-smtp-out" style="padding:12px;background:#fff;border:1px solid #ccd0d4;max-width:900px;white-space:pre-wrap;"></pre>
    </div>

    <script>
    (function(){
      const ajaxUrl = <?php echo wp_json_encode(admin_url('admin-ajax.php')); ?>;
      const nonce = <?php echo wp_json_encode($nonce); ?>;
      const out = document.getElementById('kunaal-smtp-out');
      const toEl = document.getElementById('kunaal-smtp-to');
      const typeEl = document.getElementById('kunaal-smtp-type');

      function print(obj) {
        out.textContent = JSON.stringify(obj, null, 2);
      }

      function post(params) {
        return fetch(ajaxUrl, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
          credentials: 'same-origin',
          body: new URLSearchParams(params).toString()
        }).then(r => r.json());
      }

      document.getElementById('kunaal-smtp-tcp').addEventListener('click', async function(){
        print({loading:true});
        const res = await post({ action: 'kunaal_smtp_diagnostics', nonce });
        print(res);
      });

      document.getElementById('kunaal-smtp-send').addEventListener('click', async function(){
        print({loading:true});
        const res = await post({
          action: 'kunaal_smtp_send_test',
          nonce,
          type: typeEl.value,
          to: toEl.value || ''
        });
        print(res);
      });

      document.getElementById('kunaal-smtp-send-php').addEventListener('click', async function(){
        print({loading:true});
        const res = await post({
          action: 'kunaal_smtp_send_test',
          nonce,
          type: typeEl.value,
          to: toEl.value || '',
          forcePhp: '1'
        });
        print(res);
      });
    })();
    </script>
    <?php
}


