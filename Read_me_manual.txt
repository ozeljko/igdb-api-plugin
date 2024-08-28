igdb-api-plugin/
├── config/
│   └── config.php
    └── styles_and_scripts.php
├── css/
│   └── admin-style.css
    └── frontend-style.css
├── js/
│   └── admin-script.js
    └── slick-init.js
├── includes/
│   ├── admin-page.php
│   ├── api-calls.php
│   └── settings.php
    └── shortcodes.php
├── templates/
│   └── admin-page-template.php
├── igdb-api-plugin.php
└── Read_me_manual.txt

=== IGDB API Plugin ===
Contributors: ozeljko
Tags: IGDB, API, games
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A plugin to fetch and display data from IGDB API.

== Description ==
This plugin allows you to fetch and display game data from the IGDB API.

== Installation ==
Get Client Id and Client Secret on IGDB input credencials in /cofig/config.php
1. Upload the plugin files to the `/wp-content/plugins/igdb-api-plugin` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Use the Settings->IGDB API Plugin screen to configure the plugin (yet work to be done :)

== Changelog ==
= 1.0.3 =
* Initial release.
Use the Shortcode in Posts or Pages
You can now use the shortcode [igdb_game game_name="Your Game Name"] in any post or page to display the game data. 
For example: [igdb_game game_name="The Legend of Zelda"]
Trailers[igdb_game_trailers game_name="Spiderman"]
Search any game [igdb_game_search]
Upcoming games [igdb_upcoming_games]
