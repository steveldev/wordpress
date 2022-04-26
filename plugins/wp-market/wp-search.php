<?php
/**
 * Plugin Name: WP Search Market
 * Description: Search in custom post type market
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

defined('ABSPATH') or die();

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

        if(empty( $_GET['s'])) return;

        add_action('init', [$this, 'render']);

    }


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
        
        // search form
        //get_search_form();

        $posts = $this->get_markets();
        

        if(empty($posts)) return;

        echo '<ul>';
        foreach($posts as $post) :

            echo '<li>';
            printf('<a href="%s">%s<a> (%d Km)', get_the_permalink($post->ID), $post->post_title, number_format( (float) $post->distance, 2, '.', ' ') );
            echo '</li>';

        endforeach;
        echo '</ul>';


    }





} 





