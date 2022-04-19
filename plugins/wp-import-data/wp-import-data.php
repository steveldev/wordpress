<?php
/**
 * Plugin Name: Import Data
 * Author: Steve LERAT (contact@reseau-net.fr)
 * Author URI: https://reseau-net.fr
 */

 /**
  * HOW TO USE
  * Create Custom post type matching keys
  * Copy this plugin and activate it
  * Connect to front page with "?action=menu-import" parameter
  */



defined( 'ABSPATH' ) || die();
new ImportApiData();

class ImportApiData {


    const USER = '';
    const PASS = '';
    const API  = '';



    public function __construct()  {
        ini_set("allow_url_fopen", 1);

        if( !is_admin()) : 
            if(empty($_GET['action'])) :
                //add_action('init', [$this, 'render_buttons_action']);
            else :   
                switch($_GET['action']) {
                    case 'menu-import':
                        add_action('init', [$this, 'render_buttons_action']);
                        break;
                        
                    case 'import':
                        add_action('init', [$this, 'get_data']);
                        break;
                    case 'reset':
                        add_action('init', [$this, 'reset']);
                        break;
                    default:
                        add_action('init', [$this, 'render_buttons_action']);
                        break;
                }          
            endif;       
        endif;

    }


    public function set_cors() {
        
        // Allow from any origin
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
            // you want to allow, and if so:
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
        
        // Access-Control headers are received during OPTIONS requests
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                // may also be using PUT, PATCH, HEAD etc
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
            
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        
            exit(0);
        }

    }

    public function render_buttons_action() {
        
        if( is_admin() ) return;

        wp_head();
        $url = site_url() ;
        echo '
            <style type="text/css">
                body {height:100vh;display:flex;justify-content:center;align-items:center;background:#ddd;}
                h1 {text-align:center;}
                hr {margin-top:1.5rem;}
                .container-plugin{margin:auto;width:650px;padding:1.5rem;border:1px solid silver;background:#fff;}
                .buttons{margin:2rem;padding:1rem;display:flex;justify-content:center;}
                .button{padding:10px 15px!important;margin: 0 1rem;text-align:center;line-height:1.5;width:30%;}
            </style>
            <div class="container-plugin">
            <h1>WP IMPORT DATA</h1>
                <div class="buttons" >
                <a href="'.$url.'?action=import" class="button ">Import Markets</a>
                <a href="'.$url.'?action=reset" class="button button-warning ">Reset</a>
                </div>
        ';
        if(!empty($_GET['action']) && $_GET['action'] == 'menu-import') exit;
    }

    public function reset() {
        
        if( is_admin() ) return;

        $this->render_buttons_action();
        echo '<h1>Réinitialisation des données </h1>';
        echo '- Traitement en cours ...';

        $posts = get_posts([
            'post_type'     => 'market', 
            'numberposts'   => -1
        ]);

        foreach($posts as $post) {

            $post_id = $post->ID;
            
            // delete post meta
            $post_metas = get_post_meta( $post_id, '', true );
            foreach( $post_metas as $meta_key => $post_meta ) {
                delete_post_meta( $post_id, $meta_key);
            }           

            // delete post
            wp_delete_post($post_id, true);
        }
        
        echo '<br>- Traitement términé.';
        echo '<br>'.count($posts).' enregistrements supprimés';
        die();

    }


    public function get_data() {

        if( is_admin() ) return;

        require __DIR__ . '/vendor/autoload.php';

        $this->set_cors();

        $opts = array(
            'http'=>array(
                'method'=>"GET",
                'header'=>array(    
                            "Accept: application/json",
                            "Accept-language: en",
                            "Cookie: foo=bar",
                            "Custom-Header: value"
                            )
                        )
        );
       
        // filter url params
        $params = $this->api_url_filters();


        try {
            $datas = @file_get_contents(self::API . $params, false, stream_context_create($opts) );    
        } catch (Exception $e) {
            if(current_user_can('administrator')) : 
                wp_die('Exception reçue : ',  $e->getMessage(), "\n");
            else:
                wp_die('Error');
            endif;
        }

        if(empty( $datas )) {            
            echo '<br>Error : connexion failed <br>'.json_last_error();
            die();
        }

        $this->import( $datas, $params); 

        die();
    }
  
    public function api_url_filters() {
        $step   = 50;
        $page   = !empty($_GET['page'] )   ? (int) $_GET['page']   + 1 : 1 ;
        $limit  = !empty($_GET['limit'] )  ? (int) $_GET['limit']  + (int) $step : (int) $step ;
        $offset = !empty($_GET['offset'] ) ? (int) $_GET['offset'] + (int) $step -1 : 1 ;

        $params = sprintf('?page=%d&limit=%d&offset=%d', $page, $limit, $offset);

        // redirect fo first import if $_GET['page'] is empty
        if(empty($_GET['page'])) {
            echo '<meta http-equiv="refresh" content="1;URL='.site_url().'/'.$params.'&action=import"> ';
            exit();
        }

        // url structrure : ?page=3&limit=5&offset=50
        return $params ;
        
    }

    public function import( $datas, $params) { 
        /*
        debug
        dump($datas);
        */
        $datas = json_decode($datas);


        $this->render_buttons_action();

        echo '<h2>Import des données : <strong>Market</strong></h2>';
        echo '<hr/>';
        echo '- Connexion au serveur distant : <br><span style="padding-left:15px;">'.self::API.$params.'</span><br>';
        echo '- Import en cours ...';

        if(!empty( $datas)) :
            foreach( $datas as $data_key => $item) {            
                    
                $item = (array) $item;
                $item['location'] = (array) $item['location'];
                
                //dump($item);
                
                foreach( $item as $key => $value) {
                        
                    $key =  strtolower($key);

                    // post data
                    $post_title     = $item['name'];
                    $post_content   = '';  
                    $post_date      =  date('Y-m-d H:i:s', strtotime($item['created_at']));

                    // post meta
                    $metas = [
                        'market_lat'      => $item['location']['latitude'],
                        'market_long'     => $item['location']['longitude'],
                        'market_day'      => $item['day'],
                    ];

                    /**
                     * create post if not exists
                     * @see https://developer.wordpress.org/reference/functions/wp_insert_post/
                     */
                    
                    $post = get_page_by_title($post_title, OBJECT, 'market');
                    if(null != $post) {
                        $post_id = $post->ID;
                    } else {
                        $post_id = wp_insert_post( 
                            array(
                                'post_type'         => 'market',
                                'post_title'        => wp_strip_all_tags( $post_title ),
                                'post_status'       => 'publish',
                                'post_author'       => 1,
                                'post_content'      => $post_content,                                
                                'post_date'         => $post_date,
                            ) 
                        );
                    }

                    /**
                     * set post metas
                     * @see https://developer.wordpress.org/reference/functions/update_post_meta/
                     */
                    if(!empty($metas)) {
                        foreach($metas as $meta_key => $meta_value) {
                            update_post_meta($post_id, '_'.$meta_key, $meta_value );
                        }
                    }  

                }
            }
        else : 
            echo '<br>- Import des données términé.';
            echo '<br>Vous pouvez consulter vos données ici : <a href="'.admin_url().'/edit.php?post_type=market">Gestion des Markets</a>.';
            exit;

        endif;       


        echo '<hr/>';
        echo 'Merci de patienter pendant l\'import des données';

        // redirect to next import
        $params = $this->api_url_filters();
        echo '<meta http-equiv="refresh" content="10;URL='.site_url().'/'.$params.'&action=import"> ';

    }



}
