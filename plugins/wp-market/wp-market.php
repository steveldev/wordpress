<?php

/**
 * Plugin Name: WP Market
 * Description: 
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

defined('ABSPATH') or die();

new WPMarket();

class WPMarket {

    public function __construct()
    {        
        // Custom Post Type
        add_action( 'init', [$this, 'register_post_type'], 0 );

        // Custom Metaboxes
        add_action('add_meta_boxes', [$this, 'register_metaboxes']);
        add_action('save_post',[$this, 'save_metaboxes']);
    }

    /**
     * Register post type : market
     */
    public function register_post_type() {

        /* Property */
        $labels = array(
            'name'                => _x('Market', 'Post Type General Name', 'textdomain'),
            'singular_name'       => _x('Market', 'Post Type Singular Name', 'textdomain'),
            'menu_name'           => __('Markets', 'textdomain'),
            'name_admin_bar'      => __('Markets', 'textdomain'),
            'parent_item_colon'   => __('Parent Item:', 'textdomain'),
            'all_items'           => __('All Items', 'textdomain'),
            'add_new_item'        => __('Add New Item', 'textdomain'),
            'add_new'             => __('Add New', 'textdomain'),
            'new_item'            => __('New Item', 'textdomain' ),
            'edit_item'           => __('Edit Item', 'textdomain'),
            'update_item'         => __('Update Item', 'textdomain'),
            'view_item'           => __('View Item', 'textdomain'),
            'search_items'        => __('Search Item', 'textdomain'),
            'not_found'           => __('Not found', 'textdomain'),
            'not_found_in_trash'  => __('Not found in Trash', 'textdomain'),
        );
        $rewrite = array(
            'slug'                => _x('property', 'property', 'textdomain'),
            'with_front'          => true,
            'pages'               => true,
            'feeds'               => false,
        );
        $args = array(
            'label'               => __('property', 'textdomain'),
            'description'         => __('Properties', 'textdomain'),
            'labels'              => $labels,
            'supports'            => array('title', 'editor', 'thumbnail'),
            'taxonomies'          => [],
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-admin-home',
            'show_in_admin_bar'   => true,
            'show_in_nav_menus'   => true,
            'can_export'          => true,
            'has_archive'         => false,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'query_var'           => 'property',
            'rewrite'             => $rewrite,
            'capability_type'     => 'page',
        );
        register_post_type('market', $args);	
    }


    /**
     * Register Metaboxes for post type market
     * @see https://developer.wordpress.org/reference/functions/add_meta_box/
     * add_meta_box( string $id, string $title, callable $callback, string|array|WP_Screen $screen = null, string $context = 'advanced', string $priority = 'default', array $callback_args = null )
     */
    
    public function register_metaboxes(){
        add_meta_box('market_lat', 'Latitude',  [$this, 'field_lat'], 'market', 'side', 'high');
        add_meta_box('market_long', 'Longitude', [$this, 'field_long'], 'market', 'side', 'high');
        add_meta_box('market_day', 'Jours', [$this, 'field_day'], 'market', 'side', 'high');
    }

    /**
     * Register metabox
     */

    public function field_lat($post){
        // on récupère la valeur actuelle pour la mettre dans le champ
        $val = get_post_meta($post->ID,'_market_lat',true);
        echo '<label for="field_lat">Latitude : </label>';
        echo '<input id="field_lat" type="text" name="field_lat" value="'.$val.'" class="regular-text" />';
    }

    public function field_long($post){
        // on récupère la valeur actuelle pour la mettre dans le champ
        $val = get_post_meta($post->ID,'_market_long',true);
        echo '<label for="field_long">Longitude : </label>';
        echo '<input id="field_long" type="text" name="field_long" value="'.$val.'" class="regular-text" />';
    }

    public function field_day($post){
        // on récupère la valeur actuelle pour la mettre dans le champ
        $val = get_post_meta($post->ID,'_market_day',true);
        echo '<label for="field_day">Dates : </label>';
        echo '<input id="field_day" type="text" name="field_day" value="'.$val.'" class="regular-text" />';
    }


      /**
       * Save metaboxes
       * 
       */
      function save_metaboxes($post_ID){

        if(isset($_POST['field_lat'])){
            update_post_meta($post_ID,'_market_lat', esc_html($_POST['field_lat']));
        }
        
        if(isset($_POST['field_long'])){
            update_post_meta($post_ID,'_market_long', esc_html($_POST['field_long']));
        }
        
        if(isset($_POST['field_day'])){
            update_post_meta($post_ID,'_market_day', esc_html($_POST['field_day']));
        }

    }


}
