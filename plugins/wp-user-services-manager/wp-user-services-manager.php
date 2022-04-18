<?php
/**
 * Plugin Name: WP User Services Manager
 * Description: Allow Users to manage their own Services
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

/*
    TODO : 
    - Get Woocommerce products
    - Get Current user Orders
    - Display services & expirations (REAL)

*/

defined('ABSPATH') or die();
new WPUserServicesManager();
class WPUserServicesManager {


    public function __construct() {

        if (! class_exists( 'WooCommerce' ) ) {
            return;
        } 

        // Woocommerce User Account Menu Items
        add_filter('woocommerce_account_menu_items', [$this, 'user_service_manager_account_menu_item'] );

        // Woocommerce User Services Endpoint
        add_filter( 'woocommerce_get_endpoint_url', [$this, 'user_service_manager_endpoint_link'] , 10, 4 );
        add_action( 'init', [$this, 'user_service_manager_endpoint_permalink']  );


        add_action( 'woocommerce_account_services-manager_endpoint', [$this, 'user_service_manager_endpoint_render'] );
        
        //add_action('wpmu_new_blog', [$this, 'after_insert_site']); //Fires actions after insert new site
    }


    /**
     * Woocommerce User Account Menu Items
     */
    public function user_service_manager_account_menu_item( $menu_links ){

        //unset( $menu_links['edit-address'] );     // Remove Addresses   
        //unset( $menu_links['dashboard'] );        // Remove Dashboard
        //unset( $menu_links['payment-methods'] );  // Remove Payment Methods
        //unset( $menu_links['orders'] );           // Remove Orders
        //unset( $menu_links['downloads'] );        // Disable Downloads
        //unset( $menu_links['edit-account'] );     // Remove Account details tab
        //unset( $menu_links['customer-logout'] );  // Remove Logout link

        // add new menu item
        $menu_links['services-manager'] = "Services";

        return $menu_links;        
    }


    /** 
     * Woocommerce User Services Endpoint Permalink
    */
    function user_service_manager_endpoint_permalink() {    
        add_rewrite_endpoint( 'services-manager', EP_PAGES );    
    }

    /**
     * Woocommerce User Services Endpoint Link (menu item)
     */
    public function user_service_manager_endpoint_link( $url, $endpoint, $value, $permalink ){
        if( $endpoint === 'services-manager' ) {
            // Here is the place for your custom URL, it could be external
            $url = site_url().'/mon-compte/services-manager';
        }
        return $url;
    }

    /**
     * Woocommerce User Services Endpoint Link (menu item)
     */
    function user_service_manager_endpoint_render() {

        echo '
        <style type="text/css">
            th, td {
                border-bottom:1px solid silver;
                text-align:right;
                width:200px;
            }
            th:first-child, 
            td:first-child {
                text-align:left;
                width:inherit;
            }
            .button-config { background-color:#fff;border-color:1px solid #7f54b3;}
            .button-renew { background-color:#28a745!important;color:#fff!important;}
            .button-config, .button-renew {border-radius:10px;}
            
            
        </style>';
        echo '<h2>Gestion de vos services</h2>';
        
        $services = $this->get_user_services();

        echo '<table>';

            echo '<tr>';
            foreach(array_keys($services[0]) as $col_title) {
                echo '<th>'.ucfirst($col_title).'</th>';                
            }
            echo '<th></th>';
            echo '<tr>';

        foreach($services as $service) {
            // check expiration date
            $current_date    = strtotime(date('Y-m-d'));
            $exipration_date = strtotime($service['expiration']);


            if($current_date > $exipration_date) :
                // expired service
                $expiration = sprintf('<span style="color:red;">%s</span>', $service['expiration'] );
                $btn_action = sprintf('<button class="button-renew">Renouveler</button>');
            else :
                // active service
                $expiration = sprintf('<span style="color:green;">%s</span>', $service['expiration'] );
                $btn_action = sprintf('<button class="button-config">Configurer</button>');
            endif;

            echo '<tr>';
            
                echo '<td>'.$service['name'].'</td>'; 
                echo '<td>'.$expiration.'</td>';   
                echo '<td>'.$btn_action.'</td>';              
            echo '<tr>';
        }
        echo '</table>';
     
    }


    /**
     * Fires actions after insert new site
     */
    public function after_insert_site($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
        
        if( ! current_user_can('administrator') || ! is_admin() ) return;

        if( ! is_multisite() ) wp_die('<h1>Error : </h1>Plugin : '.basename(plugin_dir_path(__FILE__)).'<br>This site is not Multisite.'); 

        $this->switch_theme( $blog_id );
    }


    /**
     * Get User Services 
     */
    public function get_user_services() {

        // TODO :

        $user_id = get_current_user_id(); 

        return [
            [
                'name'          => 'Site internet',
                'expiration'    => date("Y-m-d", strtotime("+1 year")),
            ],
            [
                'name'          => 'E-commerce',
                'expiration'    => date("Y-m-d", strtotime("+9 month")),
            ],
            [
                'name'          => 'Service 3',
                'expiration'    => date("Y-m-d", strtotime("-3 day")),
            ],

        ];
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
