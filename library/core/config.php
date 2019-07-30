<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;
// use q\core\options as options;

// load it up ##
\q\core\config::run();

class config extends \Q {

    public static function run()
    {

        // filter intermediate image sizes ##
        \add_filter( 'intermediate_image_sizes_advanced', array ( get_class(), 'intermediate_image_sizes_advanced' ) );

        // add_image_sizes for all themes ##
        \add_action( 'init', array( get_class(), 'add_image_sizes' ), 1 );

        if ( \is_admin() ) {
            

        } else {


        }

    }




    /**
     * Remove standard image sizes so that these sizes are not
     * created during the Media Upload process
     *
     * Tested with WP 3.2.1
     *
     * Hooked to intermediate_image_sizes_advanced filter
     * See wp_generate_attachment_metadata( $attachment_id, $file ) in wp-admin/includes/image.php
     *
     * @param $sizes, array of default and added image sizes
     * @return $sizes, modified array of image sizes
     * @author http://www.wpmayor.com/code/remove-image-sizes-in-wordpress/
     */
    public static function intermediate_image_sizes_advanced( $sizes)
    {

        unset( $sizes['slides']);
        unset( $sizes['slides-small']);
        unset( $sizes['home']);
        unset( $sizes['new-photos']);
        unset( $sizes['hero']);

        return $sizes;

    }



    /**
     * Add image sizes for all devices - so that all device images sizes are prepared when files are uploaded
     * Note: Tablet uses desktop sized images
     *
     * @since       0.1
     * @return      void
     */
    public static function add_image_sizes()
    {

        // generic ##
        \add_image_size( 'icon', 80, 80, false ); // icon ##
        \add_image_size( 'thumb', 270, 9999, false ); // small thumb ##

        // generic ##
        \add_image_size( 'thumb', 194, 97, true ); // small thumb ##

    }



}