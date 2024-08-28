<?php
//display
function igdb_display_game_data($atts) {
    $atts = shortcode_atts(array(
        'game_name' => ''
    ), $atts, 'igdb_game');

    if (empty($atts['game_name'])) {
        return '<p>Please provide a game name.</p>';
    }

    $data = fetch_igdb_data($atts['game_name']);

    if ($data['error']) {
        return '<p>' . esc_html($data['message']) . '</p>';
    }

    $output = '<div class="igdb-game-data">';
    foreach ($data['games'] as $game) {
        $output .= '<h2>' . esc_html($game->name) . '</h2>';
    
        if (isset($game->summary)) {
            $output .= '<p>' . esc_html($game->summary) . '</p>';
        } else {
            $output .= '<p>No summary available.</p>';
        }
    
        if (!empty($game->cover) && isset($game->cover->url)) {
            $output .= '<img src="' . esc_url($game->cover->url) . '" alt="' . esc_attr($game->name) . ' cover">';
        }
    
        if (!empty($game->screenshots)) {
            foreach ($game->screenshots as $screenshot) {
                if (isset($screenshot->url)) {
                    $output .= '<img src="' . esc_url($screenshot->url) . '" alt="' . esc_attr($game->name) . ' screenshot">';
                }
            }
        }
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('igdb_game', 'igdb_display_game_data');



//Display Game Search Form
function igdb_game_search_form() {
    ob_start();
    ?>
    <form method="post" action="">
        <input type="text" name="igdb_game_name" placeholder="Enter game name">
        <input type="submit" value="Search">
    </form>
    <?php
    if (isset($_POST['igdb_game_name'])) {
        echo do_shortcode('[igdb_game game_name="' . sanitize_text_field($_POST['igdb_game_name']) . '"]');
    }
    return ob_get_clean();
}
add_shortcode('igdb_game_search', 'igdb_game_search_form');

//upcoming games
function igdb_display_upcoming_games() {
    $data = fetch_upcoming_games();

    if ($data['error']) {
        return '<p>' . esc_html($data['message']) . '</p>';
    }

    $output = '<div class="igdb-upcoming-games">';
    foreach ($data['games'] as $game) {
        $release_date = date('F j, Y', $game->release_dates[0]->date);
        $output .= '<div class="igdb-game">';
        $output .= '<h2>' . esc_html($game->name) . '</h2>';
        $output .= '<p>Release Date: ' . esc_html($release_date) . '</p>';
        if (!empty($game->cover)) {
            $output .= '<img src="' . esc_url($game->cover->url) . '" alt="' . esc_attr($game->name) . ' cover">';
        }
        $output .= '</div>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('igdb_upcoming_games', 'igdb_display_upcoming_games');

// game trailers
function igdb_display_game_trailers($atts) {
    $atts = shortcode_atts(array(
        'game_name' => ''
    ), $atts, 'igdb_game_trailers');

    if (empty($atts['game_name'])) {
        return '<p>Please provide a game name.</p>';
    }

    $data = fetch_game_trailers($atts['game_name']);

    if ($data['error']) {
        return '<p>' . esc_html($data['message']) . '</p>';
    }

    $output = '<div class="igdb-game-trailers">';
    foreach ($data['games'] as $game) {
        $output .= '<h2>' . esc_html($game->name) . '</h2>';
        if (!empty($game->videos)) {
            foreach ($game->videos as $video) {
                $output .= '<div class="igdb-trailer">';
                $output .= '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . esc_attr($video->video_id) . '" frameborder="0" allowfullscreen></iframe>';
                $output .= '</div>';
            }
        } else {
            $output .= '<p>No trailers available.</p>';
        }
    }
    $output .= '</div>';

    return $output;
}
add_shortcode('igdb_game_trailers', 'igdb_display_game_trailers');

// Function to fetch top games from Twitch API
function fetch_with_retry($url, $headers, $max_retries = 5) {
    $retry_count = 0;
    $wait_time = 1; // Initial wait time in seconds

    while ($retry_count < $max_retries) {
        $response = wp_remote_get($url, array('headers' => $headers));

        if (!is_wp_error($response)) {
            return $response;
        }

        $retry_count++;
        sleep($wait_time);
        $wait_time *= 2; // Exponential backoff
    }

    return new WP_Error('request_failed', 'Failed to fetch data after multiple retries.');
}

function fetch_twitch_top_games() {
    $client_id = get_option('twitch_client_id'); // Securely retrieve client ID
    $access_token = get_option('twitch_access_token'); // Securely retrieve access token

    if (!$client_id || !$access_token) {
        return 'Twitch API credentials are missing.';
    }

    $cache_key = 'twitch_top_games';
    $cached_data = get_transient($cache_key);

    if ($cached_data) {
        return $cached_data;
    }
    
    $url = 'https://api.twitch.tv/helix/videos';    
    //$url = 'https://api.twitch.tv/helix/games/top';
    $headers = array(
        'Client-ID' => $client_id,
        'Authorization' => 'Bearer ' . $access_token
    );

    $response = fetch_with_retry($url, $headers);

    if (is_wp_error($response)) {
        return 'Failed to fetch data from Twitch API: ' . $response->get_error_message();
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['data'])) {
        return 'No data found.';
    }

    $output = '<div class="twitch-top-games">';
    foreach ($data['data'] as $game) {
        $output .= '<div class="game">';
        $output .= '<h3>' . esc_html($game['name']) . '</h3>';
        if (!empty($game['box_art_url'])) {
            $output .= '<img src="' . esc_url(str_replace('{width}x{height}', '285x380', $game['box_art_url'])) . '" alt="' . esc_attr($game['name']) . '">';
        }
        $output .= '</div>';
    }
    $output .= '</div>';

    set_transient($cache_key, $output, HOUR_IN_SECONDS);

    return $output;
}
add_shortcode('twitch_top_games', 'fetch_twitch_top_games');

function fetch_twitch_videos($user_id) {
    $client_id = get_option('twitch_client_id'); // Securely retrieve client ID
    $access_token = get_option('twitch_access_token'); // Securely retrieve access token

    if (!$client_id || !$access_token) {
        return 'Twitch API credentials are missing.';
    }

    $cache_key = 'twitch_videos_' . $user_id;
    $cached_data = get_transient($cache_key);

    if ($cached_data) {
        return $cached_data;
    }

    $url = 'https://api.twitch.tv/helix/videos?user_name=' . $user_id;
    $headers = array(
        'Client-ID' => $client_id,
        'Authorization' => 'Bearer ' . $access_token
    );

    $response = fetch_with_retry($url, $headers);

    if (is_wp_error($response)) {
        return 'Failed to fetch data from Twitch API: ' . $response->get_error_message();
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (empty($data['data'])) {
        return 'No videos found.';
    }

    $output = '<div class="twitch-videos">';
    foreach ($data['data'] as $video) {
        $output .= '<div class="video">';
        $output .= '<h3>' . esc_html($video['title']) . '</h3>';
        if (!empty($video['thumbnail_url'])) {
            $output .= '<img src="' . esc_url($video['thumbnail_url']) . '" alt="' . esc_attr($video['title']) . '">';
        }
        $output .= '<p><a href="' . esc_url($video['url']) . '" target="_blank">Watch Video</a></p>';
        $output .= '</div>';
    }
    $output .= '</div>';

    set_transient($cache_key, $output, HOUR_IN_SECONDS);

    return $output;
}
function twitch_videos_shortcode($atts) {
    $atts = shortcode_atts(array(
        'user_name' => 'ninja'
    ), $atts, 'twitch_videos');

    if (empty($atts['user_name'])) {
        return 'User name is required.';
    }

    return fetch_twitch_videos($atts['user_name']);
}
add_shortcode('twitch_videos', 'twitch_videos_shortcode');
?>