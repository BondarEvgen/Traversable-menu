<?php

// Add scripts

// echo plugins_url();

function tm_add_scripts(){
// Add main CSS
    wp_register_style('ts-traversable-menu-style-css', plugins_url() . '/traversable-menu/css/traversable-menu.css');
    wp_enqueue_style('ts-traversable-menu-style', plugins_url() . '/traversable-menu/css/traversable-menu.css');

    wp_register_style('ts-traversable-menu-example-style-css', plugins_url() . '/traversable-menu/css/example.css');
    wp_enqueue_style('ts-traversable-menu-example-style', plugins_url() . '/traversable-menu/css/example.css');
// Add main JS
    wp_register_script('ts-traversable-menu-script', plugins_url() . '/traversable-menu/js/traversable-menu.js');
    wp_enqueue_script('ts-traversable-menu-script', plugins_url() . '/traversable-menu/js/traversable-menu.js');

// Add traversable menu initialize
    wp_register_script('ts-traversable-menu-initialize', plugins_url() . '/traversable-menu/js/traversable-menu-initialize.js');
    wp_enqueue_script('ts-traversable-menu-initialize', plugins_url() . '/traversable-menu/js/traversable-menu-initialize.js');
}

add_action('wp_enqueue_scripts', 'tm_add_scripts');