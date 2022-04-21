<?php
/**
 * Plugin Name: WP Extensions Manager
 * Description: Manage custom extensions
 * Author: Steve LERAT
 * Author URI: https://reseau-net.fr
 */
defined('ABSPATH') or die();

/**
 *  TODO 
 *  - limit view on specific screens
 *  - a lactivation / désactivaton : declencher le install / uninistall ( initialisation / nettoyage des données)
 */

new WPExtensionsManager();

class WPExtensionsManager {

    public $plugin_name;
    public $plugin_path;
    public $plugin_slug;
    public $plugin_notices = []; 


    public function __construct() {

        $this->plugin_path = plugin_dir_path(__FILE__) ;
        $this->plugin_name = basename( $this->plugin_path );
        $this->plugin_slug = strtolower( str_replace(' ', '-', $this->plugin_name));

        //$this->plugin_notices[] = ['class' => 'notice notice-success', 'message' => 'Chargement du plugin OK ! '];

        add_action('init', [$this, 'test_required_files'], 1);
        add_action('init', [$this, 'test_capabilities'], 2);
        add_action('admin_init', [$this, 'plugin_page_settings'] );
        add_action('admin_notices', [$this, 'plugin_notices']);
        add_action('admin_menu', [$this, 'plugin_menu'] );
        add_action('update_option', [$this, 'plugin_save_data']);
        add_action('plugin_loaded', [$this, 'load_extensions']);


    }

    /**
     * Tests
     */    
    public function test_capabilities() { }

    public function test_required_files() {

        $required_files = [
            'autoload' => 'vendor/autoload.php',
            'helpers'    => 'includes/class-helpers.php',
        ];

        foreach($required_files as $filename => $path) :
            $filepath = $this->plugin_path . $path;
            if( !file_exists( $filepath ) ) :   
                $this->set_plugin_notice( 'notice notice-error', 'File "'.$filename.'" not found : '. $filepath );
            endif;
        endforeach;
     }


    /**
     * Plugin Page 
     */

     // https://developer.wordpress.org/reference/functions/add_menu_page/
    public function plugin_menu() { 

        $menu_item = add_menu_page(
            'WP Extensions Manager',              // Page title
            'WP Manager',              // Menu title
            'manage_options',                     // Capabilities
            'wp-extensions-manager',              // Slug
            [$this, 'plugin_page'],       // Display callback
            'dashicons-admin-generic',                   // Icon
            2                                    // Priority/position. Just after 'Plugins'
        );
    }

    public function plugin_page() {
        ?>
         <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
  
            <form method="POST" action="options.php">
            <?php
                    settings_fields( 'extensions_settings' );
                    do_settings_sections( $this->plugin_slug  );
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    
    public function plugin_page_settings() {

        /**
         * Register settings
         * @see https://developer.wordpress.org/reference/functions/register_setting/
         */
        register_setting( 
            'extensions_settings',          // Settings group.
            'extensions_settings',          // Setting name
            [$this, 'sanitize_text_field']  // Sanitize callback.
        );

        /**
         * Add settings section
         * @see https://developer.wordpress.org/reference/functions/add_settings_section/
         */                
        add_settings_section( 
            'section_extensions',                       // Section ID
            'Gestion des extensions',         // Title
            function () { // Callback or empty string

                // section content here                

            },   
            $this->plugin_slug                          // Page to display the section in.
        );

        /**
         * Add settings field
         * default fields from ./data/$class/example.php
         * @see https://developer.wordpress.org/reference/functions/add_settings_field/
         */ 
        $option = (array) get_option( $this->plugin_slug );          

        $extensions = $this->get_extensions();
        if( !empty( $extensions) ) :
            foreach($extensions as $extension) {

                    add_settings_field( 
                        $extension,          // Field ID
                        ucwords( str_replace('-',' ',$extension) ),          // Title
                        function() use ($extension, $option) {  // Callback to display the field
                            $checked = in_array($extension,$option) ? 'checked' : '';
                            echo '<input type="checkbox" name="_ext_'.$extension.'" '.$checked.'>';
                        },
                        $this->plugin_slug,   // Page
                        'section_extensions', // Section
                        [ 
                            'type'         => 'checkbox',
                            'option_group' => $this->plugin_slug, 
                            'name'         => $extension,
                            'label_for'    => $extension,
                            'value'        => '',
                            'checked'      => in_array($extension, $option) ? 'checked' : 0,
                            // Used 0 in this case but will still return Boolean not[see notes below]                         
                        ]
                    );
                }                        
            endif;
    }

    public function sanitize_text_field() {
    }


    // return allowed plugin screens
    public function test_plugin_screens() {

        $screens = [
            $this->plugin_slug,
        ];

        $current_page = get_current_screen();
        $current_page = $current_page->parent_base;

        return in_array($current_page, $screens) ? true : false;
    }


    public function plugin_save_data() {   

        if(empty($_POST))  return;

        $prefix = '_ext_';

        $data = [];

        foreach($_POST as $key => $value) {
            // filter _ext_ fields
            if(substr($key, 0, 5) != $prefix) continue;

            if( $value != 'on') continue;

            $data[] = str_replace($prefix, '', $key);
        }

        // register
        if( !get_option($this->plugin_slug) && !empty($data)) :       
            add_option($this->plugin_slug, $data );             
        else :
            if(empty($data)) :
                delete_option($this->plugin_slug);
            else : 
                delete_option($this->plugin_slug);      
                add_option($this->plugin_slug, $data );         
                //update_option($this->plugin_slug, $data ); // <- bug , memory
            endif;
        endif;            
        
     }

    // Plugin notice
    // https://developer.wordpress.org/reference/hooks/admin_notices/

    public function plugin_notices() {

        if(empty($this->plugin_notices)) return;

        if( !$this->test_plugin_screens() ) return;

        foreach($this->plugin_notices as $notice ) :
            extract($notice);
            printf( '                
                <div class="is-dismissible %1$s">
                    <p><strong>Plugin : '.$this->plugin_name.'</strong></p>
                    <p>%2$s</p>
                </div>', 
                esc_attr( $class ), 
                esc_html(  __( $message, 'text-domain' ) ) 
            );  
        endforeach;
    }

    // set plugin notices
    public function set_plugin_notice(string $class, string $message) {
        $this->plugin_notices[] = ['class' => $class, 'message' => $message];
    }

    // get_all_extensions
    public function get_extensions() {

        $extensions_path = plugin_dir_path(__FILE__).'extensions';
        $extensions      = [];

        foreach(scandir( $extensions_path ) as $extension ) {

            $excluded_dir = ['.', '..', 'includes'];

            if(! is_dir( $extensions_path .'/'. $extension ) ) continue;

            if( in_array( $extension, $excluded_dir ) ) continue;

            $extensions[] = $extension; 
        }
        return $extensions;
    }

    // load acctivated extensions
    public function load_extensions() {

        $extensions      = get_option($this->plugin_slug);
        $extensions_path = plugin_dir_path(__FILE__).'extensions';

        if(empty($extensions)) return;

        foreach($extensions as $extension) {
            $files = ['index.php', $extension.'.php'];
            foreach( $files as $file) {
                $filepath = $extensions_path .'/'.$extension.'/'.$file;
                
                if(file_exists($filepath)) require_once($filepath);   
            }
        }
    }
}
