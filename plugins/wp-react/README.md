# Install wp-scripts

```
npm init
npm install @wordpress/scripts --save-dev
npm start
```

# Create plugin

## Plugin structure
    plugin-name/
        plugin-name.php
        src/
            index.js 
        build/
            index.js

## plugin-name.php            
```
<?php 
/**
 * Plugin Name: React Example
 * Description : use shortcode in page
 */
defined( 'ABSPATH' ) || die();

/**
 * Registers a shortcode that simply displays a placeholder for our React App.
 */
add_shortcode( 'example_react_app', 'example_react_app' );
function example_react_app( $atts = array(), $content = null , $tag = 'example_react_app' ){
    ob_start();
    ?>
        <div id="app">App goes here</div>
        <?php wp_enqueue_script( 'example-app', plugins_url( 'build/index.js', __FILE__ ), array( 'wp-element' ), time(), true ); ?>
    <?php 
    return ob_get_clean();
}
```

# Activate plugin


# Create page
Create page with the shortcode "example_react_app"

Connect to the page


# Edit app
edit index.js

# Build app
run npm build

