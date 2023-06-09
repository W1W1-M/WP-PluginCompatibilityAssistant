<?php
/**
    * WP Plugin Compatibility Assistant
    *
    * @package           PluginPackage
    * @author            William Mead
    * @copyright         2023 William Mead
    * @license           https://www.gnu.org/licenses/gpl-3.0.html GNU GPLv3
    * @version           0.1.0
    *
    * @wordpress-plugin
    * Plugin Name:       WP Plugin Compatibility Assistant
    * Plugin URI:        https://github.com/W1W1-M/WP-PluginCompatibilityAssistant
    * Description:       A WordPress plugin to assist website administrators in managing plugin compatibility
    * Version:           1.0.0
    * Requires at least: 5.9.5
    * Requires PHP:      8.0.28
    * Author:            William Mead
    * Author URI:        https://m34d.com
    * License:           GNU GPLv3
    * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
    * Text Domain:       wp-plugin-compatibility-assistant
    * Domain Path:       /languages
*/
/*
WP Plugin Compatibility Assistant is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

WP Plugin Compatibility Assistant is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WP Plugin Compatibility Assistant. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

require_once plugin_dir_path( __FILE__ ) . 'admin/class-wp-pca-options.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-pca-logic.php';

add_action( 'init', 'wp_pca_load_textdomain' );
add_action('plugins_loaded', 'wp_pca_init', 99,0);

wp_enqueue_style('style-wp-pca', plugins_url('admin//css/style-wp-pca.css', __FILE__));
/**
 * Initializes WP Plugin Compatibility Assistant
 * 
 * Setups plugin logic instance and passes it to options instance to generate view
 * 
 * @see WP_PCA_Logic
 * @see WP_PCA_Options
 * @return void
 */
function wp_pca_init() {
    $wp_pca_logic = new WP_PCA_Logic();
    $wp_pca_logic->run();
    $wp_pca_options = new WP_PCA_Options();
    $wp_pca_options->run($wp_pca_logic);
}

/** 
 * Load WP PCA plugin textdomain
*/
function wp_pca_load_textdomain() {
    load_plugin_textdomain( 'wp-plugin-compatibility-assistant', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

?>