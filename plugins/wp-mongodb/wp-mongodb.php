<?php
defined( 'ABSPATH' ) || die();
/**
 * Plugin Name: WP MongoDB Connector
 * Author: Steve LERAT (contact@reseau-net.fr)
 * Author URI: https://reseau-net.fr
 */

new WPMongoDB();

class WPMongoDB {

    const CONFIG_FILE   = __DIR__ . '/mongo.config.php';
    const DRIVER_CLASS  = 'MongoDB\Driver\Manager';

    private $db_config  = [];
    private $db_string;


    public function __construct() {
      add_action('init', [$this, 'mongodb_connector'] );   
       // add_action('rest_api_init', [$this, 'add_api_route'] ); // Fires when preparing to serve a REST API request.   
    }
/*

    public function create() {
        $bulk = new MongoDB\Driver\BulkWrite;
        $bulk->insert(['x' => 1]);
        $bulk->insert(['x' => 2]);
        $bulk->insert(['x' => 3]);
        $manager->executeBulkWrite('db.collection', $bulk);
    }
    public function read() {}

    public function update() {}

    public function delete() {}

*/

    /**
     * Testing autoloader 
     */
    public function test_autoloader() {
        $autoloader  = __DIR__.'/vendor/autoload.php';
        if(!file_exists( $autoloader )) {
            return false;
        } else {    
            require $autoloader;
        }
    }

    /**
     * Testing  MongoDB PHP Driver
     */
    public function test_mongodb_php_driver() {        
        if( !class_exists( self::DRIVER_CLASS ) ) {
            return false;              
        }
    }

        
    /**
     * Add new API entry 
     * ./wp-json/api/demo
     */

    public function add_api_route(WP_REST_Server $wp_rest_server) {        

        register_rest_route('api','/mongodb' , [
            'methods'  => 'GET',
            'callback' => function (WP_REST_Request  $request) {

                // get mongoDB data
                $data = $this->mongodb_connector();
                var_dump($data);die();
                // output
                return ['data' => $data, 'success' => 'Hey !!'];
            }
        ]);
    }



    /** 
     * Get MongoDB configuration
     * @return void
     */
    public function load_mongodb_config() {

        // test mongoDB config file        
        if(!file_exists( self::CONFIG_FILE )) wp_die('Error : MongoDB config file not found');

        // load mongoDB config
        $mongo_config = require( self::CONFIG_FILE );     

        // set mongoDB config
        foreach($mongo_config as $key => $value) {
            $this->db_config[ $key ] = $value;
        }

        // set db_string
        if($this->db_config['host'] == 'localhost') {
            $this->db_string = sprintf(
                'mongodb://%s:%s', 
                $this->db_config['host'], 
                $this->db_config['port'] 
            );   

        } else {
            $this->db_string = sprintf(
                'mongodb://%s:%s@%s:%s', 
                $this->db_config['user'], 
                $this->db_config['pass'],
                $this->db_config['host'], 
                $this->db_config['port'] 
            );   

        }     
    }

    /**
     * Mongo connector
     *
     * @see https://www.mongodb.com/docs/drivers/php/
     * 'mongodb+srv://<username>:<password>@<cluster-address>/test?retryWrites=true&w=majority'           
     */ 
                
    /*            
        Example 1 :
        $client      = new MongoDB\Client($mongo_cfg);
        $db          = $client->$mongo_db;
        $collections = $db->listCollections();

        Example 2 :      
        $manager     = new MongoDB\Driver\Manager($mongo_cfg);
        $collection  = new MongoDB\Collection($manager, "logs","capped_logs");
    */     
    public function mongodb_connector() {

       // if( is_admin() ) return;

      //  if( current_user_can('editor') || current_user_can('administrator') ) :
            
            // test autoloader
            if(false == $this->test_autoloader()) {
               // return 'Error : Autoloader not found. <br/>Run : composer install';
            };

            // test mongoDB PHP Driver
            if(false == $this->test_mongodb_php_driver()) {
               // return sprintf( 
                    'Error : MongoDB PHP Driver not found<br>Class %s not found
                    <br>See <a href="%s" target="_blank">%s</a><br>',
                    self::DRIVER_CLASS,
                    'https://www.mongodb.com/docs/drivers/php/',
                    'MongoDB PHP Driver '
                );  
            }

            // load mongoDB configuration / populate $this->db_config
            $this->load_mongodb_config();

            // set default database
            if( empty( $this->db_config['base'] )) {
                $this->db_config['base'] = 'test';
            } 

            // connector
            $client      = new MongoDB\Client($this->db_string);
            $db          = $this->db_config['base'];
            $db          = $client->$db;
            $collections = $db->listCollections();            

            //return $collections;
            // render
           $this->render( $collections );

      //  endif;

        
    }

    /**
     * Render MongoDBData collections
     * 
     */
    public function render($collections) {
            
        echo 'Connected to '.$this->db_string;
            
        // debug
        echo '<pre>';
        print_r($collections);
        echo '</pre>';   

        // loop on collections
        foreach ($collections as $col) {
            echo $col->getName();
        }

        // stop scripts
        exit;
    }
    
}
