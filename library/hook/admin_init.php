<?php

/**
 * WP_Head Function
 *
 * clean up things we don't want
 * add things we do want
 *
 * filters and actions ##
 *
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @since       0.1
 */

namespace q\hook;

use q\plugin as q;
use q\core;
use q\core\helper as h;

// load it up ##
\q\hook\admin_init::run();

class admin_init {

    public static function run(){

        // h::log( 'Admin...' );

        \add_action( 'admin_head', array ( get_class(), 'favicon' ), 9999999 ); // add to backend ##

    }


    /**
     * favicon function ##
     * reference favicon.png in header if found in top directory of child or parent theme ##
     * include favicon.ico on IE if found ##
     */
    public static function favicon(){

        // h::log( 'Adding favicon...' );

?>
        <link rel="icon" type="image/png" href="<?php echo \get_site_url( '1' ); ?>/favicon.png" /><!-- Major Browsers -->
        <!--[if IE]><link rel="SHORTCUT ICON" href="/favicon.ico" /><![endif]--><!-- Internet Explorer-->
<?php

           # }
    }

}
