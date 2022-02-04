<?php
add_action( 'wp_enqueue_scripts', 'load_assets' );
function load_assets() {
    wp_enqueue_style(  'style-css',  get_template_directory_uri() . '/assets/dist/css/style.css' );
    wp_enqueue_script( 'scripts-js', get_template_directory_uri() . '/assets/dist/js/scripts.js', [], false, true );
}
