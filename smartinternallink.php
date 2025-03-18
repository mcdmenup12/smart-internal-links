<?php
/**
 * Plugin Name: Smart Internal Link Generator
 * Plugin URI: https://example.com
 * Description: Automatically suggests and inserts internal links based on predefined keywords.
 * Version: 1.0
 * Author: Your Name
 * Author URI: https://example.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Create Admin Menu
function silg_create_menu() {
    add_menu_page('Internal Link Settings', 'Smart Links', 'manage_options', 'silg-settings', 'silg_settings_page');
}
add_action('admin_menu', 'silg_create_menu');

// Plugin Settings Page
function silg_settings_page() {
    ?>
    <div class="wrap">
        <h2>Smart Internal Link Generator Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('silg_settings_group');
            do_settings_sections('silg-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

// Register Plugin Settings
function silg_register_settings() {
    register_setting('silg_settings_group', 'silg_keywords');
    add_settings_section('silg_main_section', 'Keyword & URL Mapping', null, 'silg-settings');
    add_settings_field('silg_keywords', 'Keywords & Links', 'silg_keywords_callback', 'silg-settings', 'silg_main_section');
}
add_action('admin_init', 'silg_register_settings');

function silg_keywords_callback() {
    $keywords = get_option('silg_keywords', '{}');
    echo '<textarea name="silg_keywords" rows="10" cols="50">' . esc_textarea($keywords) . '</textarea>';
    echo '<p>Enter keywords and URLs in JSON format: {"keyword": "https://example.com"}</p>';
}

// Auto-Link Content
function silg_auto_link_content($content) {
    if (is_singular() && in_the_loop() && is_main_query()) {
        $keywords = json_decode(get_option('silg_keywords', '{}'), true);
        if ($keywords) {
            foreach ($keywords as $keyword => $url) {
                $content = preg_replace('/\b' . preg_quote($keyword, '/') . '\b/i', '<a href="' . esc_url($url) . '" target="_blank" rel="noopener">' . $keyword . '</a>', $content, 1);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'silg_auto_link_content');
