<?php
/**
 * Plugin Name: IGDB API custom Plugin
 * Description: A plugin to fetch and display data from IGDB API.
 * Version: 1.0.3
 * Author: ozeljko
 * Text Domain: igdb-api-plugin
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define constants
define('IGDB_API_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IGDB_API_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include necessary files
include_once plugin_dir_path(__FILE__) . 'config/config.php';
include_once plugin_dir_path(__FILE__) . 'config/styles_and_scripts.php';
include_once plugin_dir_path(__FILE__) . 'includes/settings.php';
include_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
include_once plugin_dir_path(__FILE__) . 'includes/api-calls.php';
include_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';

function igdb_plugin_load_textdomain() {
    load_plugin_textdomain('igdb-api-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'igdb_plugin_load_textdomain');

// Function to fetch IGDB access token
function get_igdb_access_token($client_id, $client_secret) {
    $ch = curl_init('https://id.twitch.tv/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array(
        'client_id' => CLIENT_ID,
        'client_secret' => CLIENT_SECRET,
        'grant_type' => GRANT_TYPE
    ));
    
    $r = curl_exec($ch);
    $i = curl_getinfo($ch);
    curl_close($ch);
    
    if ($i['http_code'] != 200) {
        return ['error' => true, 'message' => 'Failed to get Token: ' . $i['http_code'] . ' message: ' . $r];
    }

    $tokenData = json_decode($r);
    if (json_last_error() != JSON_ERROR_NONE) {
        return ['error' => true, 'message' => 'Failed to parse token JSON'];
    }

    $access_token = $tokenData->access_token;
}

// Function to refresh the access token if expired
if (!function_exists('refresh_token_if_expired')) {
    function refresh_token_if_expired($client_id, $client_secret, &$headers) {
        global $access_token, $token_expiry;
        if (time() >= $token_expiry) {
            error_log("Token expired. Refreshing token.");
            list($access_token, $expires_in) = get_access_token($client_id, $client_secret);
            if (!$access_token) {
                error_log("Failed to refresh access token.");
                return false;
            }
            $token_expiry = time() + $expires_in;
            $headers['Authorization'] = 'Bearer ' . $access_token;
        }
        return true;
    }
}

// Function to make an API request with rate limiting, token refresh, and caching
if (!function_exists('make_request')) {
    function make_request($url, &$headers, $client_id, $client_secret, $body = null, $method = 'POST') {
        global $request_times;

        // Check if the response is cached
        $cache_key = md5($url . json_encode($body));
        $cached_response = get_transient($cache_key);
        if ($cached_response !== false) {
            return $cached_response;
        }

        handle_rate_limit();
        if (!refresh_token_if_expired($client_id, $client_secret, $headers)) {
            return 'Error: Unable to refresh access token.';
        }

        $args = array(
            'headers' => $headers,
            'method' => $method,
            'body' => $body
        );

        $response = wp_remote_request($url, $args);
        if (is_wp_error($response)) {
            error_log('API request error: ' . $response->get_error_message());
            return 'Error: ' . $response->get_error_message();
        }

        $data = wp_remote_retrieve_body($response);
        if (empty($data)) {
            error_log('Empty response from API.');
            return 'Error: Empty response from API.';
        }

        set_transient($cache_key, $data, 12 * HOUR_IN_SECONDS); // Cache for 12 hours

        return $data;
    }
}

// Function to fetch data from IGDB API with caching
if (!function_exists('fetch_igdb_data')) {
    function fetch_igdb_data($client_id, $client_secret, $endpoint) {
        $endpoint = sanitize_text_field($endpoint);
        $transient_key = 'igdb_data_' . md5($endpoint);
        $cached_data = get_transient($transient_key);

        if ($cached_data !== false) {
            return $cached_data;
        }

        list($access_token, $expires_in) = get_access_token($client_id, $client_secret);
        if (!$access_token) {
            error_log('Failed to get access token.');
            return array('error' => true, 'message' => 'Failed to get access token.');
        }

        $headers = array(
            'Client-ID' => $client_id,
            'Authorization' => 'Bearer ' . $access_token
        );
        $url = 'https://api.igdb.com/v4/' . $endpoint;

        $response = make_request($url, $headers, $client_id, $client_secret);
        if (is_wp_error($response)) {
            return array('error' => true, 'message' => 'API request error: ' . $response->get_error_message());
        }

        $data = json_decode($response, true);
        if (json_last_error() != JSON_ERROR_NONE) {
            error_log('Failed to parse JSON response.');
            return array('error' => true, 'message' => 'Failed to parse JSON response.');
        }

        return is_array($data) ? $data : array('error' => true, 'message' => 'Unexpected response format.');
    }
}

// Function to get all registered webhooks
if (!function_exists('get_all_webhooks')) {
    function get_all_webhooks($client_id, $client_secret) {
        return fetch_igdb_data($client_id, $client_secret, 'webhooks/');
    }
}
//////////////////

////////////////////////////











