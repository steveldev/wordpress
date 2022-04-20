<?php
/**
 * Plugin Name: WP User Services Manager
 * Description: Allow Users to manage their own Services
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

defined('ABSPATH') or die();
/*
    NOTE : Refreash Permalinks after editing this file

    TODO : 
    - Get Woocommerce products
    - Get Current user Orders
    - Display services & expirations (REAL)
    - user account list service : group by categories and hide/slide
    - enqueue script
*/

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
        add_action( 'woocommerce_account_assistance_endpoint', [$this, 'user_assistance_endpoint_render'] );
        add_action( 'woocommerce_account_parrainage_endpoint', [$this, 'user_parrainage_endpoint_render'] );
        
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
        unset( $menu_links['downloads'] );        // Disable Downloads
        //unset( $menu_links['edit-account'] );     // Remove Account details tab
        unset( $menu_links['customer-logout'] );  // Remove Logout link

        // add new menu item
        $menu_links['services-manager'] = "Services";
        $menu_links['assistance']       = "Assistance";
        $menu_links['parrainage']       = "Parrainage";

        $menu_links['customer-logout'] = "Déconnexion";

        return $menu_links;        
    }


    /** 
     * Woocommerce User Services Endpoint Permalink
    */
    function user_service_manager_endpoint_permalink() {    
        add_rewrite_endpoint( 'services-manager', EP_PAGES ); 
        add_rewrite_endpoint( 'assistance', EP_PAGES ); 
        add_rewrite_endpoint( 'parrainage', EP_PAGES );      
    }

    /**
     * Woocommerce User Services Endpoint Link (menu item)
     */
    public function user_service_manager_endpoint_link( $url, $endpoint, $value, $permalink ){
        if( $endpoint === 'services-manager' ) {
            // Here is the place for your custom URL, it could be external
            $url = site_url().'/mon-compte/services-manager';
        }
        if( $endpoint === 'assistance' ) {
            // Here is the place for your custom URL, it could be external
            $url = site_url().'/mon-compte/assistance';
        }
        if( $endpoint === 'parrainage' ) {
            // Here is the place for your custom URL, it could be external
            $url = site_url().'/mon-compte/parrainage';
        }

        return $url;
    }

    
    /**
     * Woocommerce User Services Endpoint Link (menu item)
     */
    function user_assistance_endpoint_render() {
        echo '<h2>Assistance</h2>';
        echo '
            <ul>
                    <li><a href="">Mise en route : configuration du compte, site, boutique, paiements</a></li>
                    <li><a href="">Accompagnement : prise en main de wp + woo</a></li>
                    <li><a href="">Redirection de nom de domaine</a></li>
            </ul>
        ';
    }
    
    /**
     * Woocommerce User Services Endpoint Link (menu item)
     */
    function user_parrainage_endpoint_render() {
        echo '<h2>Parrainage</h2>';
      
    }
    /**
     * Woocommerce User Services Endpoint Link (menu item)
     */
    function user_service_manager_endpoint_render() {

        echo '
        <style type="text/css">
            body {box-sizing: border-box;}
            table {border-collapse:collapse;}
                tr:hover {background:#ddd!important;}
                tr:hover td {background:none!important;}
                    th {padding:.5rem 0!important;}
                    th:first-child {padding-left:10px!important;}
                    td {vertical-align:middle!important;padding:.5rem 0!important;}
                    td:first-child {vertical-align:middle!important;width:30px;padding:0!important;text-align:center;}
                    td:last-child {
                        text-align:right;
                        width:200px;                       
                    }
                    td:last-child button {margin-right:.5rem;}
            .button-icon    {border-radius:50%; background:none!important; border:none solid silver;color:silver;padding:.2rem;}
            .button-config  { background-color:#fff;border-color:1px solid #7f54b3;}
            .button-config, .button-renew {border-radius:10px;}        
            .button-renew, .button-processing, .button-on-hold  { background-color:#28a745!important;color:#fff!important;}

        </style>';
        echo '<h2>Gestion de vos services</h2>';
        

        $products = (array) $this->get_products();

        $user_services = $this->get_user_services();

/*
        echo '<pre>';
        print_r($user_services);
        echo '</pre>';
    */

        // user current services list  
        $current_user_services_count = !empty($user_services['current']) ? '('.count($user_services['current']).')' : '';
        echo '<h3>Services actifs '.$current_user_services_count.'</h3>'; 
        
        if(!empty($user_services['current'])) : 
        echo '<table>';
        foreach($user_services['current'] as $user_service) {

            echo '<pre>';
            print_r($user_service);
            echo '</pre>';

            switch($user_service['status']) {
                case 'processing':
                    $bouton_action = '<strong>En cours de validation</strong>';
                    break;
                default:
                    $bouton_action = '<button>Configurer</button>';
                    break;
            }

            echo '<tr>';           
                echo '
                <td>
                    <button class="button-icon">
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </button>
                </td>';  
                echo '<td>'.$user_service['product_name'].'<br><small>Expiration : '.$user_service['date_expire'].'</small></td>';  
                echo '<td>'.$bouton_action.'</td>';   
            echo '</tr>';
        }
        echo '</table>';
        else: 
            echo '<p>Vous n\'avez aucun service actif actuellement.';
            echo '<br><a href="">Signaler un problème</a></p>';
        endif;

        // render user history services list  
        if(!empty($user_services['history'])) :            
            echo '<h3>Historique </h3>'; 
            echo '<table>';
            foreach($user_services['history'] as $user_service) {                           
                echo '<tr>';           
                    echo '
                    <td>
                        <button class="button-icon">
                            <span class="dashicons dashicons-arrow-right-alt2"></span>
                        </button>
                    </td>';  
                    echo '<td>'.$user_service['product_name'].'<br><small>Expiration : '.$user_service['date_expire'].'</small></td>';   
                    echo '<td><button>Activer</button></td>';   
                echo '</tr>';
            }
            echo '</table>';
        endif;

        // render products list (without current user service)
        echo '<h3>Services disponibles</h3>'; 
        echo '<table>';   
        foreach($products as $product) {   
            
            $user_order_status = '';

            // skip curent user services
            if( in_array( $product->ID, array_keys($user_services['current']) ) ) continue;

            // check waiting payment for matching service
            if( in_array( $product->ID, array_keys($user_services['wait']) ) ) :

                $user_service = $user_services['wait'][$product->ID]; 

                switch($user_service['status']) {
                    case 'pending':
                        $service_informations = 'En attente de paiement';
                        break;
                    case 'on-hold':
                        $service_informations = 'En attente de paiement';
                        break;
                    case 'cancelled':
                        $service_informations = 'Paiement Annulé';
                        break;
                    case 'refunded':
                        $service_informations = 'Remboursé';
                        break;
                    default:
                        $service_informations = '';
                        break;
                }

                $service_informations = !empty($service_informations) ? '<br>Informations : '.$service_informations : '';               

            endif;

            // render
            echo '<tr>';           
                echo '
                <td>
                    <button class="button-icon" style="color:silver;">
                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                    </button>
                </td>';  
                echo '<td>'.$product->post_title.' '.$service_informations.'</td>'; 
                echo '<td><button>Activer</button></td>';   
            echo '</tr>';
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
     * Get User Services ( products in orders)
     * https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
     * https://stackoverflow.com/questions/39401393/how-to-get-woocommerce-order-details
     */
    public function get_user_services() {
        $user_services = [];
        // TODO : 
        // filter : older orders 
        // filter : pagination

        // user orders      
        $user_orders = wc_get_orders([
            'customer_id' => get_current_user_id(),
            'orderby'     => 'date',
            'order'       => 'DESC',
           // 'status'      => ['completed'],
        ]);

        foreach($user_orders as $user_order) {

            $user_order_data = $user_order->get_data(); // returns array

            // user order items 
            $user_order_items = $user_order->get_items();

            foreach($user_order_items as $user_order_item) {

                // https://woocommerce.github.io/code-reference/files/woocommerce-includes-wc-order-functions.html

                $product_id = $user_order_item->get_product_id();

                if( $user_order->get_status() == 'completed') : 
                    // compare expiration date
                    $current_date    = strtotime(date('Y-m-d H:i'));
                    $expiration_date = strtotime($user_order->get_date_modified()->date('Y-m-d H:i') . "+1 year");

                    $key = $current_date > $expiration_date ? 'history' : 'current';
                    
                elseif( $user_order->get_status() == 'processing') :
                    $key = 'current';
                else : 
                    $key = 'wait';
                    $expiration_date = '';
                endif;

                $expiration_date = !empty($expiration_date) ? date('d-m-Y H:i', $expiration_date) : '';

                $user_services[$key][$product_id] = [
                    'product_id'        => $product_id,
                    'quantity '         => $user_order_item->get_quantity(),
                    'product_name'      => $user_order_item->get_name(),
                    'status'            => $user_order->get_status(),
                    'date_completed'    => $user_order->get_date_modified()->date('Y-m-d H:i'),
                    'date_expire'       => $expiration_date,
                ];
            }
        }

        return $user_services;
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
     * Get Woocommerce Products
     */
    public function get_products() {
        return get_posts([
            'post_type'      => 'product',  
            'posts_per_page' => -1
        ]); 
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
