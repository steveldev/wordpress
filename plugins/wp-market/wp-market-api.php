<?php
/**
 * Plugin Name: WP Market API
 * Description: Market API
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */

defined('ABSPATH') or die();

    /*        
    $location_origin = ['43.600000','1.433333']; // Toulouse
    $location_origin = ['47.1264192','1.4379064']; // marché : LUCAY LE MALE
    */


new WPMarketAPI();
class WPMarketAPI {

    const DISTANCE_DEFAULT  = 10;    // km
    const DISTANCE_MAX      = 200;       // km


    public function __construct() {
        // API endpoints
        add_action('rest_api_init', [$this, 'add_api_endpoint']);
    }
    

    // API Endpoints
    public function add_api_endpoint() {

        $routes = [  
            '/markets',               
            /*                                                 
            '/markets/(?P<zipcode>)',
            '/markets/(?P<lat>)/(?P<lng>)',
            '/markets/(?P<lat>)/(?P<lng>)/(?P<dist>)'
            */
        ];

        foreach($routes as $route) :
            register_rest_route('api', $route, [
                'methods'  => 'GET',
                'callback' => [$this, 'render'],
                'permission_callback'   => '__return_true',
            ]);
        endforeach;
    }

    // API Response
    public function render(WP_REST_Request $request) {
        // get data
        $data = $this->get_markets($request);
        
        // set code response
        $code = empty($data) ? 404 : 200;

        // response
        $response = new WP_REST_Response($data, $code);
        return $response;

    }
    

    public function get_markets(WP_REST_Request $request) {

        // filters
       
        $zipcode   = $request->get_param('zipcode');
        $latitude  = $request->get_param('lat');
        $longitude = $request->get_param('lng');
        $distance  = $request->get_param('dist');


        if(!empty($zipcode)) {
            if( !is_numeric($zipcode) || strlen($zipcode) < 3 ) {
                $zipcode = '';
            }
        }

        if(!empty($latitude) && !empty($longitude)) {
            if( !is_float($latitude) || !is_float($longitude) ) {
                $latitude  = '47.1264192';
                $longitude = '1.4379064';
            }
        }

        if(!empty($distance) ) {
            if( !is_numeric($distance) || $distance > self::DISTANCE_MAX ) {
                $distance  = self::DISTANCE_DEFAULT;
            }
        }


        // get data
        if(!empty( $zipcode ) ) :

            // get gps lat & lng for this zipcode

            $latitude  = '';
            $longitude = '';
            //$markets = $this->get_results($latitude, $longitude, $distance);

        else : 

            $markets = $this->get_results($latitude, $longitude, $distance);
        endif;

       
        return $markets;
    }


    // SQL Request
    function get_results( $lat, $lng, $distance = 10 ) {
        global $wpdb;
    
        // Radius of the earth 3959 miles or 6371 kilometers.
        $earth_radius = 3959;
    
        $sql = $wpdb->prepare( "
            SELECT DISTINCT
                p.ID,
                p.post_title,
                map_lat.meta_value as locLat,
                map_lng.meta_value as locLong,
                ( %d * acos(
                cos( radians( %s ) )
                * cos( radians( map_lat.meta_value ) )
                * cos( radians( map_lng.meta_value ) - radians( %s ) )
                + sin( radians( %s ) )
                * sin( radians( map_lat.meta_value ) )
                ) )
                AS distance
            FROM $wpdb->posts p
            INNER JOIN $wpdb->postmeta map_lat ON p.ID = map_lat.post_id
            INNER JOIN $wpdb->postmeta map_lng ON p.ID = map_lng.post_id
            WHERE 1 = 1
            AND p.post_type = 'market'
            AND p.post_status = 'publish'
            AND map_lat.meta_key = '_market_lat'
            AND map_lng.meta_key = '_market_long'
            HAVING distance < %s
            ORDER BY distance ASC",
            $earth_radius,
            (float) $lat,
            (float) $lng,
            (float) $lat,
            (int) $distance
        );
    
        // Uncomment and paste into phpMyAdmin to debug.
        // echo $sql;
    
        $nearbyLocations = $wpdb->get_results( $sql );
    
        if ( $nearbyLocations ) {
            return $nearbyLocations;
        }
    }

}
