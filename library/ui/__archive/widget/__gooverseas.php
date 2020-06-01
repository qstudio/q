<?php

namespace q\theme\widget;

use q\core\helper as helper;
use q\core\core as core;
use q\core\config as config;
use q\controller\generic as generic;

/**
 * Gooverseas quasi widget
 *
 * @package WordPress
 * @since 2.0.0
 *
 */

// load it up ##
#\q\theme\widget\instagram::run();

class gooverseas extends \Q {

    // plugin properties ##
    public static $properties = false;
    public static $args = array();

    public static function run( array $args = null )
    {

        // merge any passed args ##
        self::$args = $args;

        // hook assets ##
        // self::assets();

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
        $config['title'] = \apply_filters( 'q/widget/instagram/title', \__( 'Go Overseas', 'q-textdomain' ) );

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
        // $array = self::properties();

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
        // if ( ! self::validate() ) {

        //     helper::log( 'Config Error...' );

        //     return false;

        // }

        // get properties ##
        $array = self::properties( $args );
        
        // #helper::log( $array );

        // // reject if missing required data ##
        // if ( 
        //     empty( $array )
        //     || false == $array
        // ) {

        //     helper::log( 'Missing required config.' );

        //     return false;

        // }

        // title ##
        // if ( $array['title'] ) {
            
?>
        <div class="go-overseas-review-widget-component"
            data-gooverseas-widget-id="16340"
            data-gooverseas-widget-name="b3"
            data-gooverseas-widget-theme="primary"
            data-gooverseas-widget-link="yes">
        </div>
<?php

        // markup ##
        // $markup = str_replace( '%selector%', $array['selector'], $markup );

        // add trigger element ##
        // echo $markup;

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
        <script type="text/javascript">
            if ( typeof jQuery !== 'undefined' ) {
                jQuery(document).ready(function() {

                    // console.log( 'Doing Go Overseas...' );
                
                    (function (w, d, t) {
                        w.GoOverseas = w.GoOverseas || function () {
                            (w.GoOverseas.q = w.GoOverseas.q || []).push(arguments);
                        };
                        w.GoOverseas.l = 1 * new Date();
                        
                        var a = d.createElement(t), m = d.getElementsByTagName(t)[0];
                        a.async = 1;
                        a.src = '//www.gooverseas.com/static/0.1.1/main.min.js';
                        m.parentNode.insertBefore(a, m);
                    })(window, document, 'script');
                    
                    GoOverseas('review_widget_embed');

                });
            }
        </script>
<?php

    }

}