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

    public function __construct() {        
        add_filter( 'woocommerce_get_sections_shipping', [$this, 'market_add_section'] );
        add_filter( 'woocommerce_get_settings_shipping', [$this, 'market_all_settings'], 10, 2 );
    }
    
/**
 * Create the section beneath the products tab
 **/
public function market_add_section( $sections ) {
	
	$sections['market'] = __( 'Marchés', 'text-domain' );
	return $sections;
	
}

public function market_all_settings( $settings, $current_section ) {

    // get markets
    $markets = get_posts([
        'post_type' => 'market',
        'numberposts'=> 50
    ]);

    foreach($markets as $market) {
        $markets_options[$market->ID] = $market->post_title;
    }


    // section description
    $section_description = '<p>Séléctionnez les marchés correspondants à vos points de vente.</p>';
    $section_description .= '<br>Vous pouvez également ajouter des marchés de notre <a href="">Moteur de recherche</a>';


	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section == 'market' ) {
		$settings_slider = array();
		// Add Title to the Settings
		$settings_slider[] = array( 
            'name' => __( 'Gestion des Marchés', 'text-domain' ), 
            'type' => 'title', 
            'desc' => __( $section_description, 'text-domain' ), 
            'id' => 'market' 
        );
		// Add checkbox option
		$settings_slider[] = array(
			'name'     => __( 'Marchés', 'text-domain' ),
			'desc_tip' => __( 'Information text', 'text-domain' ),
			'id'       => 'market_auto_insert',
			'type'     => 'select',
			'css'      => 'min-width:300px;',
			'desc'     => __( 'Helper text', 'text-domain' ),
            'multiple' => true,
            'options'  => $markets_options,
		);

		
		$settings_slider[] = array( 
            'type' => 'sectionend', 
            'id' => 'market' 
        );
		return $settings_slider;
	
	/**
	 * If not, return the standard settings
	 **/
	} else {
		return $settings;
	}

}

}
