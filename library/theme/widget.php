<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;

// load it up ##
\q\theme\widget::run();

class widget extends \Q {

    // private property ##
    public static $widgets_add_default = array();
    public static $widgets_add;
    public static $widgets_remove = array();
    
    /**
    * Class Constructor
    */
    public static function run()
    {
        
        // stack up the default widgets ##
        \add_action( 'widgets_init', array ( get_class(), 'widgets_add_default' ), 1 );
        
        // define widgets to add ##
        \add_action( 'widgets_init', array ( get_class(), 'widgets_add' ), 2 );
        
        // add defined widgets ##
        \add_action( 'widgets_init', array ( get_class(), 'do_widgets_add' ), 3 );
        
        // define widgets to remove ##
        \add_action( 'widgets_init', array ( get_class(), 'widgets_remove' ), 1 );
        
        // remove defined widgets ##
        \add_action( 'widgets_init', array ( get_class(), 'do_widgets_remove' ), 2 );
        
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

                helper::log( 'Widget Error: '.$array['widget'] );
               
            }

        }

    }



        
    /**
    * Default list of widgets to activate
    * 
    * @since       1.2.0
    * @return      void
    */
    public static function widgets_add_default()
    {
        
        self::$widgets_add_default = array(
        
                'instagram'     => 'instagram' // Instagram ##
            ,   'gooverseaes'   => 'gooverseas' // Instagram ##
            ,   'sharelines'    => 'sharelines' // Facebook / Twitter Share ##
                        
        );
        
    }
    
    
    
    /**
    * Add Widgets
    * 
    * See list to define which to add
    * 
    * @since       1.0
    * @return      void
    */
    public static function widgets_add( $widgets )
    {
        
        // build list of widgets to add ##
        self::$widgets_add = array();
        
        // add default widgets ##
        self::$widgets_add = array_merge( self::$widgets_add_default, self::$widgets_add );
        
        // add each seleted widget to the load list ##
        if ( $widgets || is_array( $widgets ) ) { 
        
            // merge extra widgets ##
            self::$widgets_add = array_merge( self::$widgets_add, $widgets );
            
        }
            
        // let's remove duplicate keys - taking the used added ones over the default ##
        
        
    }
    
    
    /**
    * Do Add Widgets
    * 
    * @since       1.0
    * @return      void
    */
    public static function do_widgets_add()
    {
        
        // sanity check ##
        if ( ! self::$widgets_add || ! is_array( self::$widgets_add ) ) { return false; }
        
        // test ##
        #wp_die( pr( self::$widgets_add ) );
        
        // add each seleted widget to the load list ##
        foreach ( self::$widgets_add as $key => $value ) {
            
            helper::get( "theme/widget/{$value}.php", 'require', 'path' );
                
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
    public static function widgets_remove( $widgets ) {
        
        // build our list of default widgets to remove ##
        self::$widgets_remove = array(
            
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
        
        // handle extra removals ##
        if ( $widgets && is_array( $widgets ) ) {
            
            self::$widgets_remove = array_merge( $widgets, self::$widgets_remove );
            
        }

    }
    
    
    /**
    * Do Remove Widgets
    * 
    * @since       1.0
    * @return      void
    */
    public static function do_widgets_remove()
    {
        
        #wp_die( pr(self::$widgets_remove) );
        if ( 
            ! self::$widgets_remove 
            || ! is_array( self::$widgets_remove ) 
        ) { 
            
            return false; 
            
        }
        
        foreach ( self::$widgets_remove as $remove ) {
            
            \unregister_widget( $remove );
        
        }
        
    }
    
    
}
    