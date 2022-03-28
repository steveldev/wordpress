<?php 
/**
 * Plugin Name: WP React Plugin
 */

defined( 'ABSPATH' ) || die();

/**
 * Register shortcode 
 */
add_shortcode( 'react_app', 'react_app' );

function react_app( $atts = array(), $content = null , $tag = 'react_app' ){
    ob_start();
    ?>
        <div id="app">Loading ...</div>
        <?php wp_enqueue_script( 'example-app', plugins_url( 'build/index.js', __FILE__ ), array( 'wp-element' ), time(), true ); ?>
    <?php 
    return ob_get_clean();
}
