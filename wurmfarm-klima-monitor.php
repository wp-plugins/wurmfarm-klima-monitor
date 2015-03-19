<?php
/*
 * Plugin Name: Wurmfarm Klima Monitor
 * Plugin URI: www.2komma5.org
 * Description: Zeigt die Temperatur, Luftfeuchtigkeit, Luftdruck, Bodenfeuchtigkeit, welche mit RaspberryPi und GrovePi+ gemessen wurden.
 * Version: 1.3.0
 * Author: Stefan Mayer
 * Author URI: http://www.2komma5.org
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
 
// function library file
require_once 'functions/function.php';

// settings set in general area
 add_action( 'admin_init', 'ws_settings_api_init' ); 
 
// show button for shortcode
add_filter('mce_external_plugins', "ws_wormstation_register");
add_filter('mce_buttons', 'ws_wormstation_add_button', 0);


//Add hook for front-end <head></head>
wp_register_script('jsapi', 'http://www.google.com/jsapi');
wp_enqueue_script('jsapi');

//create DB table
register_activation_hook(__FILE__, 'ws_create_plugin_table');
//delete DB table and options
register_deactivation_hook(__FILE__, 'ws_delete_plugin_table');

add_action('wp_head', 'ws_visualization_load_js');

//Add the short codes for the charts
add_shortcode('ws_chart', 'ws_visualization_line_chart_shortcode');

//Add filter to edit the contents of the post
add_filter('the_content', 'ws_visualization_load_graphs_js', 1000);
?>
