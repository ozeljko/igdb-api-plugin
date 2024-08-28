<?php
add_action('init', 'define_client_credentials');

function define_client_credentials() {
    $current_user = wp_get_current_user();
    if (current_user_can('manage_options')) {
        define("CLIENT_ID", "Your Client ID"); // Type your Clien ID here
        define("CLIENT_SECRET", "Your Client Secret"); // Type your Client Secret here
        define("GRANT_TYPE", "client_credentials");
    } else {
        // Handle the case where the user does not have the required capability
        error_log('Unauthorized access attempt to define sensitive data.');
    }
}
