<?php
add_action( 'wp_enqueue_scripts', 'load_assets' );
function load_assets() {
    wp_enqueue_style( 'style-name-css', get_stylesheet_uri() );
    wp_enqueue_script( 'script-name-js', get_template_directory_uri() . '/js/scripts.js', [], false, true );
}
