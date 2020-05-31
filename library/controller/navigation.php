<?php

namespace q\controller;

use q\core\config as config;
use q\core\core as core;
use q\core\helper as helper;
use q\controller\css as css;
use q\wordpress\core as wp_core;

// load it up ##
#\q\theme\theme\frontpage::run();

class navigation extends \Q {



    /**
    * Check if a page shoould show sub navigation
    *
    * @since    2.0.0
    * @return   Boolean
    */
    public static function has_sub_navigation()
    {

        // array of parent slugs to allow sub navigation
        $slugs = array(
            'about'
        );

        // check for the_post ## 
        if ( ! $the_post = wordpress::the_post() ) {

            #helper::log( 'No Post object found' );

            return false;

        }

        // check for post parent ##
        if ( ! $the_post->post_parent ) {

            #helper::log( 'Post has no parent' );

            return false;

        }

        if ( ! $parent = \get_post( $the_post->post_parent ) ) {

            #helper::log( 'Parent post missing..' );

            return false;

        }

        if ( in_array( $parent->post_name, $slugs ) ) {

            #helper::log( 'Slug matched: '.$parent->post_name );

            return true;

        }

        #helper::log( 'Not a page with sub navigation' );

        return false;

    }


    /**
    * Build Sub Navigation
    *
    * @since       1.0.5
    * @return      string   HTML
    */
    public static function the_navigation( $args = array() )
    {

        // try and data, or kick back false ##
       // if ( ! $array = wordpress::get_navigation( $args ) ) { return false; }

        // handheld navigation ##
        #if ( q_ui::get_device() == 'handheld' ) {

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
        #if ( q_ui::get_device() == 'handheld' ) {

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
    public static function the_multisite_nav_menu( $args = array(), $blog_id = 1 ) {

        #global $blog_id;
        $blog_id = \absint( $blog_id );

        #helper::log( 'nav_menu - $blog_id: '.$blog_id.' / $origin_id: '.$origin_id );

        if ( 
            ! \is_multisite() 
        ) {

            #helper::log( $args );
            \wp_nav_menu( $args );
            
            return;

        }

        \switch_to_blog( $blog_id );
        #helper::log( 'get_current_blog_id(): '.\get_current_blog_id()  );
        #helper::log( $args );
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
    public static function the_multisite_nav_menu_items( $args = array(), $origin_id = 1 ) {

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
    public static function the_nav_menu( $args = array(), $blog_id = 1 )
    {

        #helper::log( $args );

        // merge theme_location into passed args ##
        $args['theme_location'] = isset( $args['theme_location'] ) ? $args['theme_location'] : $args['menu'] ;

        // try and grab data, or kick back false ##
        // if ( ! $args = wordpress::get_nav_menu( $args ) ) { 

        //      helper::log( 'kicked here..' );

        //      return false; 
            
        // }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )wp_parse_args( 
            $args
            , config::get( 'the_nav_menu' ) 
        );
        
        //$args = \wp_parse_args( $args, self::$the_nav_menu );
		#helper::log( $args );
        
        if ( ! \has_nav_menu( $args->menu ) ) {
        
            helper::log( '! has nav menu: '.$args->theme_location );

            return false;

        }

        // pass to mulltisite handler ##
        self::the_multisite_nav_menu(
            $args,
            $blog_id
        );

?>

<?php

    }




	/**
	 * @param WP_Query|null $wp_query
	 * @param bool $echo
	 * @param array $params
	 *
	 * @return 	string|null
	 * @link	https://gist.github.com/mtx-z/f95af6cc6fb562eb1a1540ca715ed928
	 * 
	 * Accepts a WP_Query instance to build pagination (for custom wp_query()),
	 * or nothing to use the current global $wp_query (eg: taxonomy term page)
	 * - Tested on WP 5.4.1
	 * - Tested with Bootstrap 4.4
	 * - Tested on Sage 9.0.9
	 *
	 * INSTALLATION:
	 * add this file content to your theme function.php or equivalent
	 *
	 * USAGE:
	 *     <?php echo bootstrap_pagination(); ?> //uses global $wp_query
	 * or with custom WP_Query():
	 *     <?php
	 *      $query = new \WP_Query($args);
	 *       ... while(have_posts()), $query->posts stuff ... endwhile() ...
	 *       echo bootstrap_pagination($query);
	 *     ?>
	 * 
	 * Notes:
	* AJAX:
	* - When used with wp_ajax (generate pagination HTML from ajax) you'll need to provide base URL (or it'll be admin-ajax URL)
	* - Example for a term page: bootstrap_pagination( $query, false, ['base' => get_term_link($term) . '?paged=%#%'] )
	*
	* Images as next/prev:
	* - You can use image as next/prev buttons
	* - Example: 'prev_text' => '<img src="' . get_stylesheet_directory_uri() . '/assets/images/prev-arrow.svg">',
	*
	* Add query parameters to page URLs
	* - If you need custom URL parameters on your page URLS, use the "add_args" attribute
	* - Example (before paginate_links() call):
	* $arg = [];
	* if (isset($_GET[ 'sort' ])) {
	*  $args[ 'sort' ] = (string)$_GET[ 'sort' ];
	* }
	* ...
	* 'add_args'     => $args,
	 */
	public static function the_pagination( \WP_Query $wp_query = null, $echo = true, $params = [] ) {

		if ( null === $wp_query ) {
			global $wp_query;
		}

		$add_args = [];

		//add query (GET) parameters to generated page URLs
		/*if (isset($_GET[ 'sort' ])) {
			$add_args[ 'sort' ] = (string)$_GET[ 'sort' ];
		}*/

		$pages = \paginate_links( array_merge( [
				'base'         => str_replace( 999999999, '%#%', \esc_url( \get_pagenum_link( 999999999 ) ) ),
				'format'       => '?paged=%#%',
				'current'      => max( 1, \get_query_var( 'paged' ) ),
				'total'        => $wp_query->max_num_pages,
				'type'         => 'array',
				'show_all'     => false,
				'end_size'     => 3,
				'mid_size'     => 1,
				'prev_next'    => true,
				'prev_text'    => __( '« Prev' ),
				'next_text'    => __( 'Next »' ),
				'add_args'     => $add_args,
				'add_fragment' => ''
			], $params )
		);

		if ( is_array( $pages ) ) {
			//$current_page = ( get_query_var( 'paged' ) == 0 ) ? 1 : get_query_var( 'paged' );
			$pagination = '<div class="pagination"><ul class="pagination">';

			foreach ( $pages as $page ) {
				$pagination .= '<li class="page-item' . (strpos($page, 'current') !== false ? ' active' : '') . '"> ' . str_replace('page-numbers', 'page-link', $page) . '</li>';
			}

			$pagination .= '</ul></div>';

			if ( $echo ) {
				echo $pagination;
			} else {
				return $pagination;
			}
		}

		return null;
		
	}




    /**
    * Get Pagination links
    *
    * @since       1.0.2
    * @return      String      HTML
    */
    public static function the_pagination_OLD( $args = array() )
    {

        // grab array ##
        if ( ! $array = wp_core::get_pagination( $args ) ) { 

			helper::log( 'No pagination...' );
            
            return false; 
        
		}
		
		helper::log( $array );

?>
        <nav class="col-12">
            <div class="">
<?php

                // get pagination links, based on passed arguments ##
                #$paginate_links = \paginate_links( $array );
                echo $array['first'];
                echo \paginate_links( $array );
                echo $array['last'];
                #helper::log( $paginate_links );
                #self::log( reset( ( explode('?', $paginate_links) ) ) );
?>
            </div>
        </nav>
<?php

    }


    /**
    * Get Pagination links
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



    /**
    * Sidebar Template
    *
    * @since       1.3.0
    * @return      string      HTML code for sidebar
    */
    public static function the_sidebar( $args = array() )
    {

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )wp_parse_args( $args, config::$the_sidebar );

        // mobile wrapper ##
        if ( 'mobile' == self::get_device() ) {

            // close tag ##
            ui::get_tag( $args->tag, '', 'close' );

            // close tag ##
            ui::get_tag( $args->tag, '', 'close' );

            // open tag ##
            ui::get_tag( $args->tag, array ( $args->class, 'sidebar', 'wrapper-outer' ) );

            // open tag ##
            ui::get_tag( $args->tag, array ( 'wrapper-padding', 'wrapper-inner' ) );

        } else {

            // open tag ##
            ui::get_tag( $args->tag, array ( $args->class, 'sidebar' ) );

        }

        switch ( get_post_type() ) {

            case ( 'page' ) :

                // section navigation ##
                if ( 'mobile' != self::get_device() ) self::the_nav_menu();

                // secondary content ##
                #self::the_secondary_content();

                // page widgets ##
                dynamic_sidebar( 'q-page-sidebar' );

            break;

            case ( 'post' ) :
            default :

                // post widgets ##
                dynamic_sidebar( 'q-blog-sidebar' );

                // secondary content ##
                #self::the_secondary_content();

            break;

        }

        // mobile wrapper ##
        if ( 'handheld' == helper::get_device() ) {

            // close tag ##
            ui::get_tag( $args->tag, '', 'close' );

        }

        // close tag ##
        ui::get_tag( $args->tag, '', 'close' );

    }



    public static function css()
    {

?>
<style>

/* simple dropdown */

.navigation {
    padding: 20px 10px 10px;
    border-bottom: 1px solid #ddd;
}

ul.the-navigation {
	/*background:#005555;*/
	padding:0;
	margin:0;
	list-style-type:none;
	height:40px;
}
ul.the-navigation li { float:left; }
ul.the-navigation li a {
	padding: 9px 20px;
	display: block;
	/*color:#fff;*/
	text-decoration:none;
	/*font:12px arial, verdana, sans-serif;*/
}

/* Submenu */
ul.the-navigation ul.sub-menu {
	position:absolute;
	left:-9999px;
	top:-9999px;
	list-style-type:none;
	z-index: 100000;
}
ul.the-navigation li:hover { position:relative; background:#5FD367; }
ul.the-navigation li:hover ul.sub-menu {
	left:-26px;
	top:30px;
	background:#5FD367;
	padding:0px;
}

ul.the-navigation li:hover ul.sub-menu li a {
	padding:5px;
	display:block;
	width:250px;
	text-indent:15px;
	background-color:#5FD367;
}
ul.the-navigation li:hover ul li a:hover { background:#005555; }
</style>
<?php

    }


}