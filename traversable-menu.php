<?php
/*
Plugin Name: Traversable menu
Plugin URI: 
Description: Traversable menu
Author: Eugene Bondar
Author URI:
Version: 0.1
*/

if ( !defined('ABSPATH') ) {
    //If wordpress isn't loaded load it up.
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once $path . '/wp-load.php';
}


require_once plugin_dir_path( __FILE__ ) . "/includes/traversable-menu-scripts.php";

require_once plugin_dir_path( __FILE__ ) . "/includes/traversable-menu-class.php";

// Register widget

function register_traversable_menu () {
    register_widget('Traversable_Menu_Widget');
}

// Hook in function

add_action ('widgets_init', 'register_traversable_menu');

