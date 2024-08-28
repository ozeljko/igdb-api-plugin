<?php
// Enqueue admin styles and scripts
function igdb_api_plugin_admin_assets() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], null);
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', ['jquery'], null, true);
    wp_enqueue_style('igdb-admin-style', IGDB_API_PLUGIN_URL . 'css/admin-style.css', [], '1.0');
    wp_enqueue_script('igdb-admin-script', IGDB_API_PLUGIN_URL . 'js/admin-script.js', ['jquery'], '1.0', true);
}
add_action('admin_enqueue_scripts', 'igdb_api_plugin_admin_assets');
// Enqueue frontend styles and scripts
function igdb_api_plugin_frontend_assets() {
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', [], null);
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', ['jquery'], null, true);
    wp_enqueue_style('igdb-frontend-style', IGDB_API_PLUGIN_URL . 'css/frontend-style.css', [], '1.0');
}
add_action('wp_enqueue_scripts', 'igdb_api_plugin_frontend_assets');
// Enqueue slider styles and scripts
function igdb_enqueue_slick_slider() {
    wp_enqueue_style('slick-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css', [], '1.8.1');
    wp_enqueue_style('slick-theme-css', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css', [], '1.8.1');
    wp_enqueue_script('slick-js', 'https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js', ['jquery'], '1.8.1', true);
    wp_enqueue_script('igdb-slick-init', IGDB_API_PLUGIN_URL . 'js/slick-init.js', ['slick-js'], '1.0', true);
}
add_action('wp_enqueue_scripts', 'igdb_enqueue_slick_slider');

?>