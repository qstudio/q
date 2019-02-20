<?php

namespace q\theme\widget;

use q\core\helper as helper;
use q\core\core as core;
use q\core\config as config;
use q\controller\generic as generic;

/**
 * Instagram Quasi Widget
 *
 * @package WordPress
 * @since 2.0.0
 *
 */

// load it up ##
#\q\theme\widget\instagram::run();

class instagram extends \Q {

    // plugin properties ##
    public static $properties = false;
    public static $args = array();

    public static function run( array $args = null )
    {

        // merge any passed args ##
        self::$args = $args;

        // hook assets ##
        self::assets();

	}



    /**
    * Load required Assets 
    *
    * @since    2.0.0
    */
    public static function assets()
    {

        // add required script ##
        \wp_register_script( 'q-instagram', helper::get( "theme/javascript/jquery.instagram.js", 'return' ), array('jquery'), '2.0.0', false );
        \wp_enqueue_script( 'q-instagram' );

	}




    public static function config()
    {

        // new array ##
        $config = array();

        // values ##
        $config['title'] = \apply_filters( 'q/widget/instagram/title', \__( 'Instagram', 'q-textdomain' ) );

        // http://instafeedjs.com/#standard ##
        $config['get'] = \apply_filters( 'q/widget/instagram/get', 'user' );
        $config['target'] = \apply_filters( 'q/widget/instagram/target', '#instafeed' );
        $config['selector'] = str_replace( array( ".", "#" ), "", $config['target'] ) ;

        $config['user_id'] = \apply_filters( 'q/widget/instagram/user_id', false );

        // fix added for update to Instagram API June 2016 ##
        $config['client_id'] = \apply_filters( 'q/widget/instagram/client_id', false );

        // https://instagram.com/accounts/manage_access ##
        $config['access_token'] = \apply_filters( 'q/widget/instagram/access_token', false );

        // http://instafeedjs.com/#advanced ##
        $config['filter'] = \apply_filters( 'q/widget/instagram/filter', false );
        $config['wrapper'] = \apply_filters( 'q/widget/instagram/wrapper', '<ul id="%selector%"></ul>' ) ;

        // http://instafeedjs.com/#templating ##
        $config['markup'] = \apply_filters( 'q/widget/instagram/markup', '<li><a href="{{link}}" target="_blank"><img src="{{image}}" /></a></li>' ) ;
        $config['resolution'] = \apply_filters( 'q/widget/instagram/resolution', 'thumbnail' );
        $config['limit'] = \apply_filters( 'q/widget/instagram/limit', 6 );
        $config['links'] = \apply_filters( 'q/widget/instagram/links', 'true' );

        // merge ##
        if ( ! empty( self::$args ) ) {

            $config = array_merge( $config, self::$args );

        }

        // check ##
        #helper::log( $config );

        // populate static property ##
        return self::$properties = $config;

    }


    /**
    * Load plugin properties
    *
    * @since    2.0.0
    * @return   Array
    */
    public static function properties( $key = null, $return = 'string' )
    {

        #helper::log( 'called for key: '.$key );

        // properties not defined yet ##
        if ( ! self::$properties ) {

            #helper::log( 'properties empty, so loading fresh...' );
            #helper::log( self::$passed_args );

            self::config();

        }

        #helper::log( self::$properties );

        // kick back specified key or whole array ##
        return 
            ( ! is_null( $key ) && isset( self::$properties[$key] ) && array_key_exists( $key, self::$properties ) ) ? 

            // single array item ##
            ( is_array ( self::$properties[$key] ) && 'string' == $return ) ? 
            implode( ",", self::$properties[$key] ) : // flat csv ##
            self::$properties[$key] : // as array ##
            
            // whole thing ##
            self::$properties ;

    }



    /**
    * Validate that we have all the required data to make an API call to Instagram
    *
    * @since    2.0.0
    **/
    public static function validate()
    {

        // get stored properties ##
        $array = self::properties();

        #helper::log( $array );

        // reject if missing required data ##
        if ( 
            empty( $array )
            || false == $array
            || ! isset( $array['user_id'] )
            || false == $array['user_id']
            || ! isset( $array['client_id'] )
            || false == $array['client_id']
            || ! isset( $array['access_token'] ) 
            || false == $array['access_token']
        ) {

            helper::log( 'Missing required config.' );

            return false;

        }

        // ok ##
        return true;

    }



    public static function render( array $args = null )
    {

        // we should stop if we're missing key settings ##
        if ( ! self::validate() ) {

            helper::log( 'Config Error...' );

            return false;

        }

        // get properties ##
        $array = self::properties( $args );
        
        #helper::log( $array );

        // reject if missing required data ##
        if ( 
            empty( $array )
            || false == $array
        ) {

            helper::log( 'Missing required config.' );

            return false;

        }

        // title ##
        if ( $array['title'] ) {
            
?>
            <h3><?php echo $array['title']; ?></h3>
<?php

        }

        // get wrapper ##
        $markup = $array['wrapper'];

        // markup ##
        $markup = str_replace( '%selector%', $array['selector'], $markup );

        // add trigger element ##
        echo $markup;

        // add javascript ##
        self::javascript();

    }



    
    public static function javascript()
    {

        // get properties ##
        $array = self::properties();
        
        #helper::log( $array );

        // reject if missing required data ##
        if ( 
            empty( $array )
            || false == $array
        ) {

            helper::log( 'Missing required config.' );

            return false;

        }

?>
<script>
if ( typeof jQuery !== 'undefined' ) {
    jQuery(document).ready(function() {
        if ( typeof Instafeed !== 'undefined' ) {
            var userFeed = new Instafeed({
                get:            '<?php echo $array['get']; ?>',
                clientId:       '<?php echo $array['client_id']; ?>', // fix added for update to Instagram API June 2016 ##
                target:         '<?php echo $array['selector'] ?>',
                userId:         <?php echo $array['user_id'];  ?>, // integer ##
                accessToken:    '<?php echo $array['access_token']; ?>',
                filter:         '<?php echo $array['filter']; ?>',
                template:       '<?php echo $array['markup']; ?>',
                resolution:     '<?php echo $array['resolution']; ?>',
                limit:          <?php echo $array['limit']; ?>,
                links:          <?php echo $array['links']; ?>
            });
            userFeed.run();
        } else {
            console.log( "Instagram plugin undefined..." );
        }
    });
}
</script>
<?php

    }

}