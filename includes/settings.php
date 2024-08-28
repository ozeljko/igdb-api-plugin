<?php
// Register settings
function igdb_api_plugin_register_settings() {
    register_setting('igdb_api_plugin_options', 'igdb_api_plugin_options');
    add_settings_section('igdb_api_plugin_main', __('Main Settings', 'igdb-api-plugin'), 'igdb_api_plugin_section_text', 'igdb_api_plugin');
    add_settings_field('igdb_num_posts', __('Number of Posts to Fetch', 'igdb-api-plugin'), 'igdb_num_posts_callback', 'igdb_api_plugin', 'igdb_api_plugin_main');
}
add_action('admin_init', 'igdb_api_plugin_register_settings');

function igdb_api_plugin_section_text() {
    echo '<p>' . __('Enter your settings below:', 'igdb-api-plugin') . '</p>';
}

function igdb_num_posts_callback() {
    $options = get_option('igdb_api_plugin_options');
    $num_posts = isset($options['igdb_num_posts']) ? absint($options['igdb_num_posts']) : '';
    echo '<input type="number" id="igdb_num_posts" name="igdb_api_plugin_options[igdb_num_posts]" value="' . esc_attr($num_posts) . '" class="form-control" />';
}
