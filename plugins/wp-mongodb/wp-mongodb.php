<?php
defined( 'ABSPATH' ) || die();
/**
 * Plugin Name: WP MongoDB Connector
 * Author: Steve LERAT (contact@reseau-net.fr)
 * Author URI: https://reseau-net.fr
 */

new WPMongoDB();

class WPMongoDB {

    public function __construct() {
        add_action('init', [$this, 'verify_autoloader'] );
        add_action('init', [$this, 'mongodb_connector'] );
    }


    /**
     * Checking autoloader 
     */
    public function verify_autoloader() {
        $autoloader  = __DIR__.'/vendor/autoload.php';
        if(!file_exists( $autoloader )) {
            wp_die('Error : Autoloader not found. <br/>Run : composer install');
        } else {    
            require $autoloader;
        }
    }

    /**
     * Mongo connector
     *
     * @see https://www.mongodb.com/docs/drivers/php/
     * 'mongodb+srv://<username>:<password>@<cluster-address>/test?retryWrites=true&w=majority'
     */ 
    //'mongodb+srv://<username>:<password>@<cluster-address>/test?retryWrites=true&w=majority'
    public function mongodb_connector() {

        if( is_admin() ) return ;

        if( current_user_can('editor') || current_user_can('administrator') ) :

            // check mongoDB config file
            $config_file = __DIR__ . '/mongo.config.php';

            if(!file_exists( $config_file )) wp_die('Error : MongoDB config file not found');

            try {

                // load mongoDB config
                $mongo = require($config_file);            
                extract($mongo);   
                $mongo_cfg   = "mongodb://".$mongo_host.":".$mongo_port;         

                // connect to mongoDB
                /*
                    Example 1 :
                    $client      = new MongoDB\Client($mongo_cfg);
                    $db          = $client->$mongo_db;
                    $collections = $db->listCollections();

                    Example 2 :      
                    $manager     = new MongoDB\Driver\Manager($mongo_cfg);
                    $collection  = new MongoDB\Collection($manager, "logs","capped_logs");

                */
                
                $client      = new MongoDB\Client($mongo_cfg);
                $db          = $client->$mongo_db;
                $collections = $db->listCollections();            

            } catch (Exception $e) {
                echo 'Exception reçue : ',  $e->getMessage(), "\n";
            }

            // render             
            echo 'Connected to '.$mongo_cfg;
                
            echo '<pre>';
            print_r($collection);
            echo '</pre>';   

            foreach ($collections as $col) {
                echo $col->getName();
            }

        endif;

        exit;

    }
    
}
