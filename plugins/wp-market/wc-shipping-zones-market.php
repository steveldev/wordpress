<?php
 
/**
 * Plugin Name: TutsPlus Shipping
 * Plugin URI: http://code.tutsplus.com/tutorials/create-a-custom-shipping-method-for-woocommerce--cms-26098
 * Description: Custom Shipping Method for WooCommerce
 * Version: 1.0.0
 * Author: Igor Benić
 * Author URI: http://www.ibenic.com
 * License: GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Domain Path: /lang
 * Text Domain: tutsplus
 */
/**
 * Create the section beneath the products tab
 **/
add_filter( 'woocommerce_get_sections_shipping', 'market_add_section' );
function market_add_section( $sections ) {
	
	$sections['market'] = __( 'Marchés', 'text-domain' );
	return $sections;
	
}

add_filter( 'woocommerce_get_settings_shipping', 'market_all_settings', 10, 2 );
function market_all_settings( $settings, $current_section ) {

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

$val = get_post_meta($post->ID,'_shop_market',true);
        
if(empty($val)) : 

    // get markets by user account address latitude longitude
    $markets = [];
    $markets = get_posts([
        'post_type' => 'market',
        'numberposts'=> 50
    ]);

endif;

echo '<label for="field_market">Marchés : </label>';
echo '<select id="field_market" name="field_market"  />';
if( !empty( $markets ) ) : 
    foreach($markets as $market) :
        printf('<option value="%s">%s</option>', $market->ID, $market->post_title);
    endforeach;
endif;
echo '</select>';