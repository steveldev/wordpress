<?php
/**
 * Plugin Name: WP Error for Admin Only
 * Author: Steve LERAT (contact@reseau-net.fr)
 * Author URI: https://reseau-net.fr
 */

defined( 'ABSPATH' ) || die();

class WPErrorForAdminsOnly {

    public function __construct() {
        add_action( 'init', [$this, 'display_php_error_for_admin'] );          
    }    
    
    /**
     * Display Errors only if Administrators
     */
    public function display_php_error_for_admin()
    {
        $user_id    = get_current_user_id();
        $user_meta  = get_userdata($user_id);
        $roles      = $user_meta->roles;

        if(is_array($roles)){
            if (in_array("administrator", $roles)) {
                error_reporting(0);
                @ini_set('display_errors', 0);
            } 
        }
        elseif ($roles == "administrator"){
            error_reporting(0);
            @ini_set('display_errors', 0);
        }
    }
}

