<?php
/**
 * Plugin Name: WP Search Market
 * Description: Search in custom post type market
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

defined('ABSPATH') or die();


/** 
 * HOW TO USE
 * - Add shortcode [search_market] in your page to display button (My location)
 * 
 */





/**
 * - Rplace default page query
 * - Get user location
 * - Get search results (market in location area : 10Km )
 * - Paginate results (infinite scroll)
 * - Search template page
 *      - user can filter by : 
 *          - zipcode (display if location is empty / autocomplete by user location)
 *          - distance (force max distance (km)
 *          ? categories  
 *          ? other 
 * 
 * 
 * https://codex.wordpress.org/Creating_a_Search_Page
 * https://wabeo.fr/requete-geolocalisee-wordpress/
 */

new WPCustomSearch();
class WPCustomSearch {

    public function __construct() {

        // Button : search around me
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_shortcode( 'search_market', [$this, 'add_shortcode'] );

        // search page / results
        if(!empty( $_GET['s'])) {            
           // add_action('init', [$this, 'render']);
        }       

    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'script-name', plugin_dir_url( __FILE__ ) . '/src/components/geoloc.js', array(), '1.0.0', true );
    }
  
    
    public function add_shortcode( $atts = array(), $content = null , $tag = 'geoloc' ){
      ob_start();
      ?>
          <button id = "find-me"><span class="dashicons dashicons-location"></span> Rechercher les marchés alentours</button><br/>
          <p id = "status"></p>
          <a id = "map-link" target="_blank"></a>
          <div id = "markets"></div>
          <div id = "map" style="height:450px;"></div>
          <div id = "markets-map" style="height:250px;"></div>
  
          <!-- Openstreetmap -->
          <link rel="stylesheet" href="https://unpkg.com/leaflet@1.8.0/dist/leaflet.css"
   integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ=="
   crossorigin=""/>
    <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.8.0/dist/leaflet.js"
   integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ=="
   crossorigin=""></script>

        <!-- google maps
          <script defer src="https://maps.googleapis.com/maps/api/js?key=__API_KEY__&callback=initMap&v=weekly"></script>
        -->
      <?php 
      return ob_get_clean();
    }
    

    // search pafe / results

    public function get_markets() {

        $params = '';

        if(!empty($_GET['lat'] && !empty($_GET['lng']))) {
            $params .= '?lat='.$_GET['lat'];
            $params .= '&lng='.$_GET['lng'];
        }

        if(!empty($_GET['dist'])) {
            $params .= '&dist='.$_GET['dist'];
        }

        $params = !empty($params) ? $params : '?lat=47.1264192&lng=1.4379064&dist=10';

        $api_url  = 'http://localhost/lesconnectes/wp-json/api/markets' . $params;
        $api_data = file_get_contents($api_url);

        return json_decode($api_data);

    }


    public function render() {
        
        echo '<div style="margin:5rem 2.5rem;">';
        // search form
        //get_search_form();
       echo do_shortcode('[search_market]');
       
        $posts = $this->get_markets();
        

        if(empty($posts)) return;

        echo '<ul>';
        foreach($posts as $post) :

            echo '<li>';
            printf('<a href="%s">%s<a> (Distance : %d Km, xx shops)', 
                get_the_permalink($post->ID), 
                $post->post_title, 
                number_format( (float) $post->distance, 2, '.', ' ') 
            );
            echo '</li>';

        endforeach;
        echo '</ul>';

        echo '</div>';

    }



} 





