<?php
function fetch_igdb_data($game_name) {
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

    // Call IGDB API
    $ch = curl_init('https://api.igdb.com/v4/games/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Client-ID: " . CLIENT_ID,
        "Authorization: Bearer " . $access_token
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'fields name, summary, cover.*, screenshots.*; search "' . sanitize_text_field($game_name) . '";');
    
    $r = curl_exec($ch);
    $i = curl_getinfo($ch);
    curl_close($ch);

    if ($i['http_code'] != 200) {
        return ['error' => true, 'message' => 'Failed to call IGDB: ' . $i['http_code'] . ' message: ' . $r];
    }

    $IGDBData = json_decode($r);
    if (json_last_error() != JSON_ERROR_NONE) {
        return ['error' => true, 'message' => 'Failed to parse IGDB JSON'];
    }

    return ['error' => false, 'games' => $IGDBData];
}

function fetch_upcoming_games() {
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

    // Call IGDB API for upcoming games
    $ch = curl_init('https://api.igdb.com/v4/games/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Client-ID: " . CLIENT_ID,
        "Authorization: Bearer " . $access_token
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'fields name, release_dates.date, cover.url; where release_dates.date > ' . time() . '; sort release_dates.date asc; limit 10;');
    
    $r = curl_exec($ch);
    $i = curl_getinfo($ch);
    curl_close($ch);

    if ($i['http_code'] != 200) {
        return ['error' => true, 'message' => 'Failed to call IGDB: ' . $i['http_code'] . ' message: ' . $r];
    }

    $IGDBData = json_decode($r);
    if (json_last_error() != JSON_ERROR_NONE) {
        return ['error' => true, 'message' => 'Failed to parse IGDB JSON'];
    }

    return ['error' => false, 'games' => $IGDBData];
}

function fetch_game_trailers($game_name) {
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

    // Call IGDB API for game trailers
    $ch = curl_init('https://api.igdb.com/v4/games/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Client-ID: " . CLIENT_ID,
        "Authorization: Bearer " . $access_token
    ));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'fields name, videos.*; search "' . sanitize_text_field($game_name) . '";');
    
    $r = curl_exec($ch);
    $i = curl_getinfo($ch);
    curl_close($ch);

    if ($i['http_code'] != 200) {
        return ['error' => true, 'message' => 'Failed to call IGDB: ' . $i['http_code'] . ' message: ' . $r];
    }

    $IGDBData = json_decode($r);
    if (json_last_error() != JSON_ERROR_NONE) {
        return ['error' => true, 'message' => 'Failed to parse IGDB JSON'];
    }

    return ['error' => false, 'games' => $IGDBData];
}