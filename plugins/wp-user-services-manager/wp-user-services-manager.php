<?php
/**
 * Plugin Name: WP User Services Manager
 * Description: Allow Users to manage their own Services
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

defined('ABSPATH') or die();

class WPUserServicesManager {

    public function __construct() {

        add_action('wpmu_new_blog', [$this, 'after_insert_site']); //Fires actions after insert new site
    }

    
    /**
     * Fires actions after insert new site
     */
    public function after_insert_site($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        
        if( ! current_user_can('administrator') ) return;

        if( ! is_multisite() ) wp_die('<h1>Error : </h1>Plugin : '.basename(plugin_dir_path(__FILE__)).'<br>This site is not Multisite.'); 

        $this->switch_theme( $blog_id );
    }

    /**
     * Get User Service Configuration
     */
    public function get_user_service_config() {
        // TODO :
        return [
            'theme' => '',
        ];
    }

    
    /**
     * Switch theme for $blog_id
     */
    public function switch_theme(int $blog_id) {

        // set default theme
        $default_theme = 'twentytwelve';

        // get user service config
        $user_service_config = $this->get_user_service_config();

        $theme = !empty($user_service_config['theme']) ? $user_service_config['theme'] : $default_theme;

        // Switch the newly created blog
        switch_to_blog( $blog_id );

        // Change to a different theme
        switch_theme( $theme );
        
        // Restore to the current blog
        restore_current_blog();

    }
 }
