<?php
/**
 * Plugin Name: WP MongoDB Connector
 * Author: Steve LERAT (contact@reseau-net.fr)
 * Author URI: https://reseau-net.fr
 */

defined( 'ABSPATH' ) || die();

new WPMongoDB();

class WPMongoDB {

    const CONFIG_FILE   = __DIR__ . '/mongo.config.php';
    const DRIVER_CLASS  = 'MongoDB\Driver\Manager';

    private $db_config  = [];
    private $db_string;


    public function __construct() {
      //  add_action('init', [$this, 'mongodb_connector'] );   
        add_action('rest_api_init', [$this, 'add_api_route'] ); // Fires when preparing to serve a REST API request.   
    }


    /**
    * Manage MongoDB   
    * @see https://www.mongodb.com/docs/php-library/current/reference/class/MongoDBClient/ 
    */

    /*

    // create new database
        $new_db = $client->test;
        $collection = $new_db->createCollection("users");

    // insert 
        $user = [
                    'name'  => 'user1',
                    'email' => 'user1@domain.com',
                    'tel'   => '0123456789',
                ];
        $db->$collection_name->insertOne($user);


    */


    /**
     * Testing autoloader 
     */
    public function test_autoloader() {
        $autoloader  = __DIR__.'/vendor/autoload.php';
        if(!file_exists( $autoloader )) {
            return 'Error : Autoloader not found. <br/>Run : composer install';
        } else {    
            require $autoloader;
        }
    }

    /**
     * Testing  MongoDB PHP Driver
     */
    public function test_mongodb_php_driver() {        
        if( !class_exists( self::DRIVER_CLASS ) ) {
            return 
                sprintf( 
                    'Error : MongoDB PHP Driver not found. Class %s not found. See %s',
                    self::DRIVER_CLASS,
                    'https://www.mongodb.com/docs/drivers/php/'
                );  
        }
    }

        
    /**
     * Add new API entry 
     * ./wp-json/api/demo
     */

    public function add_api_route(WP_REST_Server $wp_rest_server) {        

        //  if( current_user_can('editor') || current_user_can('administrator') ) :

        register_rest_route('api','/mongodb' , [
            'methods'  => 'GET',
            'callback' => function (WP_REST_Request  $request) {

                // get mongoDB data
                $data = $this->mongodb_connector();
                extract($data); // result : $code, $data
                
                // Create the response object
                return new WP_REST_Response( $data, $code );
            }
        ]);
        
        //  endif;
    }



    /** 
     * Get MongoDB configuration
     * @return void
     */
    public function load_mongodb_config() {

        // test mongoDB config file        
        if(!file_exists( self::CONFIG_FILE )) return 'Error : MongoDB config file not found';

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
     *      
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

        $data = [];

        // tests
            // test autoloader
            if(!empty($this->test_autoloader() )) { $data[] = $this->test_autoloader(); }

            // test mongoDB PHP Driver
            if(!empty($this->test_mongodb_php_driver()) ) {$data[] = $this->test_mongodb_php_driver();}

            // load mongoDB configuration // populate $this->db_config
            if(!empty($this->load_mongodb_config()) ) {$data[] = $this->load_mongodb_config();}

            // return errors
            if(!empty($data)) return ['code' => '404', 'data' => $data];

        // set data

        // connector
            $client = new MongoDB\Client($this->db_string);

            // get databases
            $databases = $client->listDatabases();

            // get databases content
            foreach($databases as $database) {
                $db_name = $database->getName();
                $db      = $client->$db_name;

                // get database collections
                foreach($db->listCollections() as $collection) {
                    $collection_name = $collection['name'];

                    $documents = $db->$collection_name->count();

                    // get collection documents 
                    foreach($db->$collection_name->find() as $document) {                        
                        $data['databases'][$db_name][$collection_name][] = $document; 
                    }
                }                
            }    

        return ['code' => '202', 'data' => $data];
        
    }

    
}
