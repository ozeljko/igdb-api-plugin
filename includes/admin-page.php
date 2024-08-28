<?php
// Add settings page to the admin menu
function igdb_api_plugin_menu() {
    add_options_page(
        __('IGDB API Plugin Settings', 'igdb-api-plugin'),
        __('IGDB API Plugin', 'igdb-api-plugin'),
        'manage_options',
        'igdb_api_plugin',
        'igdb_api_plugin_options_page'
    );
}
add_action('admin_menu', 'igdb_api_plugin_menu');

function twitch_api_settings_page() {
    add_options_page(
        'Twitch API Settings',
        'Twitch API',
        'manage_options',
        'twitch-api-settings',
        'twitch_api_settings_page_html'
    );
}
add_action('admin_menu', 'twitch_api_settings_page');

function twitch_api_settings_page_html() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['submit'])) {
        update_option('twitch_client_id', sanitize_text_field($_POST['twitch_client_id']));
        update_option('twitch_access_token', sanitize_text_field($_POST['twitch_access_token']));
    }

    $client_id = get_option('twitch_client_id');
    $access_token = get_option('twitch_access_token');

    ?>
    <div class="wrap">
        <h1>Twitch API Settings</h1>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Client ID</th>
                    <td><input type="text" name="twitch_client_id" value="<?php echo esc_attr($client_id); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Access Token</th>
                    <td><input type="text" name="twitch_access_token" value="<?php echo esc_attr($access_token); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Retrieve credentials securely
$client_id = get_option('twitch_client_id');
$access_token = get_option('twitch_access_token');

// Display the settings page
function igdb_api_plugin_options_page() {
    include IGDB_API_PLUGIN_DIR . 'templates/admin-page-template.php';
}
