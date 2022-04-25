<?php
/**
 * Plugin Name: WP Search Market
 * Description: Search in custom post type market
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

defined('ABSPATH') or die();

/**
 * - Get user location
 * - Get search results (market in location area : 10Km )
 * - Paginate results (infinite scroll)
 * - Search template page
 * 
 * https://codex.wordpress.org/Creating_a_Search_Page
 * 
 */


class WPCustomSearch {

    const DISTANCE    = 10;// Km;
    const NUMBERPOSTS = 50; // results per page

    public function __construct() {
        if( !empty($_POST['s']) && current_user_can('administrator') ) {            
            add_action('init', [$this, 'render']);
        }
    }
    
    public function get_results() { 

        // settings 
        $location_origin = ['43.600000','1.433333']; // Toulouse

        // convert Km to m (GPS unit : m )
        $distance = self::DISTANCE * 1000 / 2; // circle radius

        // Set location area
        $location = [    
            'long_origin' => $location_origin[1], 
            'lat_origin'  => $location_origin[0],   
            'lat_min'     => $location_origin[0] - $distance, 
            'long_min'    => $location_origin[1] - $distance, 
            'long_max'    => $location_origin[1] + $distance, 
            'lat_max'     => $location_origin[0] + $distance, 
        ];


        return new WP_Query([
            'post_type'     => 'market',
            'numberposts'   => 50, 
            'meta_query'    => [
                [
                'key'       => 'market_lat',
                'value'     => [$location['lat_min'], $location['lat_max']],            
                'type'      => 'numeric',
                'compare'   => 'BETWEEN'
                ],
                [
                'key'       => 'market_long',
                'value'     => [$location['long_min'], $location['long_max']],            
                'type'      => 'numeric',
                'compare'   => 'BETWEEN'
                ],

            ]
        ]);

    }

    public function get_distance($start, $end) {

    }

    public function render() {
        
        $results = $this->get_results();

        // debug
        echo '<pre>';
        var_dump($results);
        echo '</pre>';

        if ( have_posts() ) :
            while ( have_posts() ) : the_post(); 
                
                // get post meta
                $post_meta = get_post_meta( $post->ID );
                
                // debug
                echo '<pre>';
                var_dump($post_meta);
                echo '</pre>';
                
            endwhile;
        else : 
            return 'Aucun market trouvé pour cette zone';
        endif; 



        die();
    }




} 





