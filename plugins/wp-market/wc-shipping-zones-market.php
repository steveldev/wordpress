<?php
 
/**
 * Plugin Name: WC Shipping Zones Market
 * Description: Custom Shipping Zones for WooCommerce
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */



defined('ABSPATH') or die();


new WCShippingZonesMarket();

/**
 * https://woocommerce.com/document/adding-a-section-to-a-settings-tab/
 */
class WCShippingZonesMarket {

    public $store_zipcode;

    public function __construct() { 

        $this->store_zipcode = get_option( 'woocommerce_store_postcode' );

        add_action( 'admin_menu', [$this, 'add_menu_page'] );
        add_filter( 'woocommerce_get_sections_shipping', [$this, 'market_add_section'] );
        add_filter( 'woocommerce_get_settings_shipping', [$this, 'market_all_settings'], 10, 2 );
    }
    
    public function add_menu_page(  ) {
	


        add_menu_page(
            __( 'Points de vente', 'textdomain' ),
            'Points de vente',
            'manage_options',
            'admin.php?page=wc-settings&tab=shipping&section',
            '',
            'dashicons-location-alt',
            2
        );
        /*
        add_submenu_page(
            'admin.php?page=wc-settings&tab=shipping&section',
            __( 'Ajouter un oints de vente', 'textdomain' ),
            'Ajouter',
            'manage_options',
            'admin.php?page=wc-settings&tab=shipping&zone_id=new',
            '',
        );
        */

        add_submenu_page(
            'admin.php?page=wc-settings&tab=shipping&section',
            __( 'Marchés', 'textdomain' ),
            ' Marchés',
            'manage_options',
            'admin.php?page=wc-settings&tab=shipping&section=market',
            '',
        );


    }
        
    /**
     * Create the section beneath the products tab
     **/
    public function market_add_section( $sections ) {
        
        $sections['market'] = __( 'Marchés', 'text-domain' );
        return $sections;
        
    }

    public function market_all_settings( $settings, $current_section ) {

        // section description
        $section_description = '<p>Séléctionnez les marchés correspondants à vos points de vente.</p>';
        $section_description .= '<br>Vous pouvez également ajouter des marchés de notre <a href="">Moteur de recherche</a>';

        
        if(empty( $this->store_zipcode ) ) {
            $section_description = '<p>Vous devez saisir l\'adresse de votre boutique sur <a href="'.admin_url().'admin.php?page=wc-settings">cette page</a>.</p>'; 
        }

        /**
         * Check the current section is what we want
         **/
        if ( $current_section == 'market' ) {

            $settings = array();
            

            // Add Title to the Settings
            $settings[] = array( 
                'name' => __( 'Gestion des Marchés', 'text-domain' ), 
                'type' => 'title', 
                'desc' => __( $section_description, 'text-domain' ), 
                'id' => 'market' 
            );


            // Add checkbox option
            $settings[] = array(
                'name'     => __( 'Marchés', 'text-domain' ),
                'desc_tip' => __( 'Information text', 'text-domain' ),
                'id'       => 'market_auto_insert',
                'type'     => empty( $this->store_zipcode ) ? false : 'select',
                'css'      => 'min-width:300px;',
                'desc'     => __( 'Helper text', 'text-domain' ),
                'multiple' => true,
                'options'  => $this->get_markets(),
            );

            
            $settings[] = array( 
                'type' => 'sectionend', 
                'id' => 'market' 
            );
 
            
            if(empty( $this->store_zipcode ) ) return $settings;

            return $settings;
        
        /**
         * If not, return the standard settings
         **/
        } else {
            return $settings;
        }

    }

    public function get_markets() {
        
        // check woocommerce general setings zipcode
        $zipcode = get_option( 'woocommerce_store_city' );

        if(empty($zipcode)) return [];

        // get GPS location for this zipcode

        // get api markets for this area


        // get markets
        $markets = get_posts([
            'post_type' => 'market',
            'numberposts'=> 50
        ]);

        // filter output
        foreach($markets as $market) {
            $data[$market->ID] = $market->post_title;
        }

        return $data;
    }
}
