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
 * - Map
 *  - Affichage popup sur markers + liens marché 
 * 
 * https://codex.wordpress.org/Creating_a_Search_Page
 * https://wabeo.fr/requete-geolocalisee-wordpress/
 */

new WPCustomSearch();
class WPCustomSearch {

    const MAP_TYPE = 'openstreetmap'; // openstreetmap | googlemap 
    const GOOGLEMAP_APIKEY = '';

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

        wp_enqueue_script( 'script-name', plugin_dir_url( __FILE__ ) . '/src/components/geoloc.js', [], '1.0.0', true );

        if(self::MAP_TYPE == 'googlemap') {
            wp_enqueue_script( 'googlemap-js', 'https://maps.googleapis.com/maps/api/js?key='.self::GOOGLEMAP_APIKEY.'&callback=initMap&v=weekly', [], false, true );
        } else {
            wp_enqueue_style('openstreetmap-css', 'https://unpkg.com/leaflet@1.8.0/dist/leaflet.css', [], false);
            wp_enqueue_script( 'openstreetmap-js', 'https://unpkg.com/leaflet@1.8.0/dist/leaflet.js', [], false, true );
        }

    }
  
    
    public function add_shortcode( $atts = array(), $content = null , $tag = 'geoloc' ){
      ob_start();
      ?>
        <style type="text/css">
            .hidden {display:none;}
            input#zipcode {border:1px solid silver!important;width:100px;}
            .border-top {border-top:1px solid silver;}

            ul.markets, ul.markets li  {list-style: none!important;margin:0!important;padding:0!important;}
            ul.markets li {
                padding:.5rem .5rem!important;
                border:1px solid silver;
                margin:1rem 0!important;
            }
            ul.markets li:hover {border:1px solid silver;}
            ul.markets li a {text-decoration:none!important;color:#000!important;text-transform: capitalize; }
            .markets-count { font-size:1.3rem;font-weight: bold;}
            .markets-select {max-width:100%;}
            .searchbox {display:flex;padding:0 1.5rem;}
            .mapbox {width:100%;height:80vh;}
            #search-filters {position:relative;margin-bottom:1.5rem;}
            .search-filters-edit-link {position:absolute;top:.5rem;right:0;padding:.7rem;background:none!important;color:#000!important;border:1px #000 silver;opacity:.4;}
            .search-filters-edit-link:hover {opacity:.8;}
        </style>

        <div class="searchbox" >
            <div class="col" style="min-width:350px;">

                <div class="form-location hidden" style="margin:1.5rem 0;">
                    <p>Veuillez saisir votre code postal.</p>
                    <form action="" method="post" id="form-zipcode">
                        <label for="zipcode"></label>
                        <input id="zipcode" type="text" maxlength="5" name="zipcode" placeholder="Code Postal" value="">
                        <select id="distance" name="distance">
                            <option>Distance</option>
                            <option value="10">10 Km</option>
                            <option value="20">20 Km</option>
                            <option value="30">30 Km</option>
                            <option value="40">40 Km</option>
                            <option value="50" selected>50 Km</option>
                        </select>
                        <button type="submit"><span class="dashicons dashicons-search"></span></button>
                    </form>
                </div>
                

                <div id="search-filters">                    
                    <p class="search-filters-text" style="margin-bottom:0;"></p>
                    <button class="search-filters-edit-link hidden button button-secondary"><span class="dashicons dashicons-edit"></span></button>
                </div>

                <button id="find-me" style="width:100%;"><span class="dashicons dashicons-location"></span> Rechercher les marchés alentours</button><br/>
                
                <p id="status"></p>

                <div class="markets-container">
                    <div class="markets-count"></div>                    
                    <select class="markets-select hidden"></select>
                    <ul class="markets"></ul>
                </div>
            </div>
            <div class="col" style="width:100%;margin-left:2rem;">
                <div id="map" class="mapbox" ></div>
                <div id="googlemap" class="mapbox"></div>
            </div>
     </div>

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





