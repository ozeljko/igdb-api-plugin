<div class="wrap">
    <h1><?php _e('IGDB API Plugin Settings', 'igdb-api-plugin'); ?></h1>
    <form action="options.php" method="post">
        <?php
        settings_fields('igdb_api_plugin_options');
        do_settings_sections('igdb_api_plugin');
        submit_button(__('Save Settings', 'igdb-api-plugin'));
        ?>
    </form>
</div>
