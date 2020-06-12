<?php

namespace q\ui;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;

class navigation extends \Q {



    /**
    * Build Sub Navigation
    *
    * @since       1.0.5
    * @return      string   HTML
    */
    public static function siblings( $args = array() )
    {

        // try and data, or kick back false ##
       	if ( ! $array = get\navigation::siblings( $args ) ) { return false; }

        // handheld navigation ##
        #if ( q_ui::device() == 'handheld' ) {

?>
    <div id="the-navigation" class="navigation background-solid-<?php #echo self::get_header_colour()->name; ?>">
        <ul class="the-navigation inner-wrapper">
<!--                <li class="toggle"><a href="#"><?php #self::the_text( array( 'string' => 'Sections' ) ); ?></a></li>    -->
<?php

        #} // handheld markup ##

        // loop over returned WP_Query object ##
        foreach ( $array as $item ) {

?>
            <li <?php echo $item->data; ?> class="<?php echo $item->li_class; ?>">
                <a href="<?php echo $item->permalink; ?>" class="<?php echo $item->class; ?>"><?php echo $item->title; ?></a>
            </li>
<?php

        }

        // handheld navigation ##
        #if ( q_ui::device() == 'handheld' ) {

?>
        </ul>
    </div>
<?php

        #}

    }


    /**
    * Multisite network nav menus
    *
    * @link        http://wordpress.stackexchange.com/questions/26367/use-wp-nav-menu-to-display-a-menu-from-another-site-in-a-network-install
    * @global      Integer     $blog_id
    * @param       Array       $args
    * @param       Integer     $origin_id
    * @return type
    */
    public static function multisite_nav_menu( $args = array(), $blog_id = 1 ) {

        #global $blog_id;
        $blog_id = \absint( $blog_id );

        #h::log( 'nav_menu - $blog_id: '.$blog_id.' / $origin_id: '.$origin_id );

        if ( 
            ! \is_multisite() 
        ) {

            #h::log( $args );
            \wp_nav_menu( $args );
            
            return;

        }

        \switch_to_blog( $blog_id );
        #h::log( 'get_current_blog_id(): '.\get_current_blog_id()  );
        #h::log( $args );
	    \wp_nav_menu( $args );
        \restore_current_blog();

        return;

    }



    /**
    * Get Multisite network nav menus items
    *
    * @link        http://wordpress.stackexchange.com/questions/26367/use-wp-nav-menu-to-display-a-menu-from-another-site-in-a-network-install
    * @global      Integer     $blog_id
    * @param       Array       $args
    * @param       Integer     $origin_id
    * @return      Array
    */
    public static function multisite_nav_menu_items( $args = array(), $origin_id = 1 ) {

        global $blog_id;
        $origin_id = \absint( $origin_id );

        #pr( $args );

        // not WP Multisite OR on correct site ##
        if ( ! \is_multisite() || $origin_id == $blog_id ) {

            $wp_get_nav_menu_items = \wp_get_nav_menu_items( $args );

        } else {

            // switch to the correct blog ##
            \switch_to_blog( $origin_id );

            // grab the nav menu items ##
            \wp_get_nav_menu_items( $args );

            // restore the main blog ##
            \restore_current_blog();

        }

        // nothing found ##
        if ( ! $wp_get_nav_menu_items ) { return false; }

        #pr( $wp_get_nav_menu_items );

        // drop the top item - as we don't need this ##
        unset( $wp_get_nav_menu_items[0] );

        // remove custom links and not viewable items ##
        foreach ( $wp_get_nav_menu_items as $key => $value ) {

            #pr( $value->classes[0] );

            // remove items ##
            if (
                    'custom' == $value->object // custom links ##
                || 'landing-hide' == $value->classes[0] // landing page hiders ##
            ) {

                #pr( $value->object );

                // out ##
                unset( $wp_get_nav_menu_items[$key] );

            }

        }

        // return the nav menu items ##
        return $wp_get_nav_menu_items;

    }


    /**
    * Render nav menu
    *
    * @since       1.3.3
    * @return      string   HTML
    */
    public static function nav_menu( $args = array(), $blog_id = 1 )
    {

        #h::log( $args );

        // merge theme_location into passed args ##
        $args['theme_location'] = isset( $args['theme_location'] ) ? $args['theme_location'] : $args['menu'] ;

        // try and grab data, or kick back false ##
        // if ( ! $args = wordpress::get_nav_menu( $args ) ) { 

        //      h::log( 'kicked here..' );

        //      return false; 
            
        // }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )wp_parse_args( 
            $args
            , core\config::get( 'nav_menu' ) 
        );
        
        //$args = \wp_parse_args( $args, self::$the_nav_menu );
		#h::log( $args );
        
        if ( ! \has_nav_menu( $args->menu ) ) {
        
            h::log( '! has nav menu: '.$args->theme_location );

            return false;

        }

        // pass to mulltisite handler ##
        self::multisite_nav_menu(
            $args,
            $blog_id
        );

?>

<?php

    }




	/**
    * Get Pagination links
    *
    * @since       1.0.2
	* @return      String      HTML
	* @link	https://gist.github.com/mtx-z/f95af6cc6fb562eb1a1540ca715ed928
    */
	public static function pagination( $args = array(), $return = 'echo' ) {

		// grab array ##
        if ( ! $array = get\navigation::pagination( $args ) ) { 

			// h::log( 'No pagination...' );
            
            return false; 
        
		}
		
		// test ##
		// h::log( $array );

		// get config ##
		$config = core\config::get('pagination');

		// format page items ##
		$items = '';
		foreach ( $array as $page ) {

			$markup = $config['item']; // '<li class="%active-class%">%item%</li>' ##
			$row = [];
			// $row['class_link_item'] = $config['class_link_item'];
			$row['li_class'] = $config['li_class'];
			$row['item'] = str_replace( 'page-numbers', $config['class_link_item'], $page );
			$row['active-class'] = (strpos($page, 'current') !== false ? ' active' : '');

			// h::log( $row );

			$items .= ui\method::markup( $markup, $row );

		}

		// get wrapping markup ##
		$string = str_replace( '%content%', $items, $config['markup'] ) ;

		// echo ##
		if ( 'return' == $return ){ 
			
			return $string ;

		} else {

			echo $string;

		}

		// kick back ##
		return true;
		
	}




    /**
    * Get Next Back links
    *
    * @since       1.4.7
    * @return      String      HTML
    */
    public static function the_next_back( $args = array() )
    {

        // get blog link ##
        $blog_url = \get_permalink( intval( \get_option( 'page_for_posts' ) ) ) ;

?>
        <nav class="nav-single pagination next-back wrapper-padding">
            <h3 class="assistive-text"><?php _e( 'Post navigation', self::text_domain ); ?></h3>
            <div class="alignleft next-back-col">
                <?php \previous_post_link( '%link', __( '&laquo; Back', self::text_domain ), true ); ?>
            </div>
            <div class="aligncenter next-back-col">
                <a href="<?php echo $blog_url; ?>"><?php _e( 'Home', self::text_domain ); ?></a>
            </div>
            <div class="alignright next-back-col">
                <?php \next_post_link( '%link', __( 'Next &raquo;', self::text_domain ), true ); ?>
            </div>
        </nav>
<?php

    }


}