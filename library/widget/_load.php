<?php

namespace q;

use q\core;
use q\core\helper as h;
// use q\core\config as config;

// load it up ##
\q\widget::run();

class widget extends \Q {

    // private property ##
    public static $add_default = [];
    public static $add;
    public static $remove = [];

    /**
    * Class Constructor
    */
    public static function run()
    {

		// h::log( 'HERE..' );

        // stack up the default widgets ##
        \add_action( 'widgets_init', array ( get_class(), 'add_default' ), 10 );

        // define widgets to add ##
        \add_action( 'widgets_init', array ( get_class(), 'add' ), 11 );

        // add defined widgets ##
        \add_action( 'widgets_init', array ( get_class(), 'do_add' ), 12 );

        // define widgets to remove ##
        \add_action( 'widgets_init', array ( get_class(), 'remove' ), 10 );

        // remove defined widgets ##
        \add_action( 'widgets_init', array ( get_class(), 'do_remove' ), 11 );

    }



    public static function render( $array = null ){

        if ( array_filter( $array ) ) { // widget ID passed ##

           // get all widgets ##
           #$widgets = \wp_get_sidebars_widgets();

           if ( \is_active_sidebar( $array['widget'] ) ) {

?>
                <ul class="widget <?php echo $array['class']; ?>">
<?php

                    \dynamic_sidebar( $array['widget'] );

?>
                </ul>
<?php

            } else {

                h::log( 'e:>Widget Error: '.$array['widget'] );

            }

        }

    }




    /**
    * Default list of widgets to activate
    *
    * @since       1.2.0
    * @return      void
    */
    public static function add_default()
    {

        $array = [

             	// 'instagram'     => 'instagram' // Instagram ##
            // ,   'gooverseaes'   => 'gooverseas' // Instagram ##
            // ,   'sharelines'    => 'sharelines' // Facebook / Twitter Share ##

		];

		// filterable list ##
		self::$add_default = \apply_filters( 'q/widget/default', $array );

    }



    /**
    * Add Widgets
    *
    * See list to define which to add
    *
    * @since       1.0
    * @return      void
    */
    public static function add( $widgets )
    {

        // build list of widgets to add ##
        self::$add = array();

        // add default widgets ##
        self::$add = array_merge( self::$add_default, self::$add );

        // add each seleted widget to the load list ##
        if ( $widgets || is_array( $widgets ) ) {

            // merge extra widgets ##
            self::$add = array_merge( self::$add, $widgets );

        }

        // let's remove duplicate keys - taking the used added ones over the default ##


    }


    /**
    * Do Add Widgets
    *
    * @since       1.0
    * @return      void
    */
    public static function do_add()
    {

        // sanity check ##
        if ( ! self::$add || ! is_array( self::$add ) ) { return false; }

        // test ##
        // h::log( self::$add );

        // add each seleted widget to the load list ##
        foreach ( self::$add as $key => $value ) {

			$file = h::get( "widget/{$value}.php", 'require', 'path' );
			
			// h::log( "e:>widget file: $file" );

        }

    }


    /**
    * Remove Widgets
    *
    * See list to define which to remove
    *
    * @since       1.0
    * @return      void
    * @todo        Handle extra removals
    */
    public static function remove( $widgets ) {

        // build our list of default widgets to remove ##
        $array = array(

            'WP_Widget_Pages' // Pages ##
            /*
            ,'WP_Widget_Search' // Search ##
            ,'WP_Widget_Calendar'
            ,'WP_Widget_Archives'
            ,'WP_Widget_Links'
            ,'WP_Widget_Meta'
            ,'WP_Widget_Text'
            ,'WP_Widget_Categories'
            ,'WP_Widget_Recent_Posts'
            ,'WP_Widget_Recent_Comments'
            ,'WP_Widget_RSS'
            ,'WP_Widget_Tag_Cloud'
            */

		);

		// filterable list ##
		self::$remove = \apply_filters( 'q/widget/remove', $array );

        // handle extra removals ##
        if ( $widgets && is_array( $widgets ) ) {

            self::$remove = array_merge( $widgets, self::$remove );

        }

    }


    /**
    * Do Remove Widgets
    *
    * @since       1.0
    * @return      void
    */
    public static function do_remove()
    {

        // h::log (self::$remove );
        if (
            ! self::$remove
            || ! is_array( self::$remove )
        ) {

            return false;

        }

        foreach ( self::$remove as $remove ) {

            \unregister_widget( $remove );

        }

    }


}
