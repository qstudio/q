<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;
use q\render;
use q\get;

// Q Theme ##
use q\theme;

class navigation extends \q\get {

	
    /**
     * Get Pagination links
     *
     * @since       1.0.2
     * @return      String      HTML
     */
    public static function pagination( $args = array() )
    {

		// global arg validator ##
		if ( ! $args = render\args::prepare( $args ) ){ 
		
			help::log( 'Bailing..' ); 
		
			return false; 
		
		}

		h::log( $args );

		if ( 
			isset( $args['query'] )
		) {

			$query = $args['query'];

		// grab some global variables ##
		} else {
			
			// h::log( 'Grabbing global query..' );
			global $wp_query;
			$query = $wp_query;

		}

		// no query, no pagination ##
		if ( ! $query ) {

			h::log( 'Nada to query...' );

			return false;

		}

		// get config setting ##
		$config = core\config::get('pagination');
		// h::log( $config );

        // work out total ##
		$total = $query->max_num_pages;
		// h::log( 'Total: '.$total );

		// append query to pagination links, if set ##
		$fragement = '';

		// args to query WP ##
		$paginate_args = [
			// 'base'         			=> str_replace( 999999999, '%#%', \esc_url( \get_pagenum_link( 999999999 ) ) ),
			'base'                  => @\add_query_arg('paged','%#%'),
			'format'       			=> '?paged=%#%',
			'total'        			=> $total,
			'current'      			=> max( 1, \get_query_var( 'paged' ) ),
			'type'         			=> 'array',
            'show_all'              => false,
            'end_size'		        => $config['end_size'], 
            'mid_size'		        => $config['mid_size'],
            'prev_text'             => $config['prev_text'],
            'next_text'             => $config['next_text'],                   
		];

		// optionally add search query var ##
		if( ! empty( $query->query_vars['s'] ) ) {

			$paginate_args['add_args'] = array( 's' => \get_query_var( 's' ) );
			// $query_args['s'] = \get_query_var( 's' );
			$fragement .= '&s='.\get_query_var( 's' );
			
		}

		// h::log( $query_args );

		// filter args ##
		$paginate_args = \apply_filters( 'q/get/navigation/pagination/args', $paginate_args );

		// get links from WP ##
		$paginate_links = \paginate_links( $paginate_args );

		// check if we got any links ##
		if ( 
			! $paginate_links
			|| 0 == count( $paginate_links )
		) {

			h::log( '$paginate_links empty.. bailing' );

			return false;

		}

		// test ##
		// h::log( $pages );
		// h::log( 'd:>paged: '.\get_query_var( 'paged' ) );

		// empty array ##
		$array = [];

		// prepare first item -- unless on first page ##
		if ( 0 != \get_query_var( 'paged' ) ) {
			$link_first = '?paged=1'.$fragement;
			$array[] = '<li class="'.$config['li_class'].'"><a class="'.$config['class_link_first'].'" rel="1" href="'.\esc_url( $link_first ).'">'.$config['first_text'].'</a></li>';
		}

		// merge pagination into links ##
		$array = array_merge( $array, $paginate_links ) ;

		// prepare last item ##
		if ( $total != \get_query_var( 'paged' ) ) {
			$link_last = '?paged='.$total.$fragement;
			$array[] = '<li class="'.$config['li_class'].'"><a class="'.$config['class_link_last'].'" rel="'.$total.'" href="'.\esc_url( $link_last ).'">'.$config['last_text'].'</a></li>';
		}

		// test ##
        // h::log( $array );

        // kick back array ##
        return $array;

	}
	


	
    /**
     * Get Sibling pages
     *
     * @since       1.0.1
     * @return      string       HTML Menu
     */
    public static function siblings( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = self::the_post() ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_navigation );

        #// find out "depth" of current page ##
        #$depth = count( get_post_ancestors( $global_post->ID ) );
        #pr( $depth );

        // work out who to list pages from ##
        #$post_parent = $depth == 0 ? $global_post : get_post( $global_post->post_parent );
        #pr( $post_parent->ID );

        $post_parent = array_reverse( \get_post_ancestors( $global_post->ID ) );
        #$first_parent = get_page($parent[0]);
        #pr( $post_parent[0] );

        // meta_query to exclude certain sub pages from desktop on screen sub navigation ##
        $meta_query = array(); // nada ##
        if ( 'desktop' == h::device() ) {

            $meta_query =
                array(
                    array(
                        'key'       => 'the_navigation_exclude',
                        'compare'   => 'NOT EXISTS',
                    )
                );

        }

        // query for child or sibling's post ##
        $wp_args = array(
            'post_type'         => $args->post_type,
            'post_parent'       => $post_parent[0],
            'orderby'           => 'menu_order',
            'order'             => 'ASC',
            'posts_per_page'    => $args->posts_per_page,
            'meta_query'        => $meta_query
        );

        #pr( $wp_args );

        $object = new \WP_Query( $wp_args );

        // test returned array ##
        #self::log( $object->posts );

        // prepend parent post, useful when creating navigation menu. Remove if not needed ##
        if ( $args->add_parent && $object->posts ) { array_unshift( $object->posts, $post_parent ); }

        // nothing cooking ##
        if ( ! $object->have_posts() ) { return false; }

        // $posts array ##
        $posts = array();

        // loop over all posts ##
        foreach ( $object->posts as $post ) {

            $item = new \stdClass();

            // make WP functions available ##
            #setup_postdata( $post );

            // title ##
            $item->title = 
                \get_post_meta( $post->ID, 'template_navigation_title' ) ? 
                \get_post_meta( $post->ID, 'template_navigation_title', true ) : 
                $post->post_title ;

            // permalink ##
            $item->permalink = \get_permalink( $post->ID );

            // class & highlight ##
            $item->class = 'post';

            // class & highlight ##
            $item->li_class = 
                $post->ID === $global_post->ID ? 
                'current_page_item page-'.\sanitize_key( $post->post_name ).' post-'.$post->ID : 
                'post-'.$post->ID.' page-'.\sanitize_key( $post->post_name ) ;

            // class ##
            $item->data = false;

            // slim post object to fields we need ##
            #$item = q_trim_post( $item );

            // sort out global $post after WP_Query loop ##
            \wp_reset_postdata();

            #self::log( $item );

            // add to array ##
            $posts[] = $item;

        }

        // test posts #
        #pr( $object->posts );

        // allow posts to be filtered ##
        $posts = \apply_filters( 'q/get/wp/the_navigation', $posts );

        // return object ##
        return $posts;

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
        
            // h::log( 'd:>! has nav menu: '.$args->theme_location );

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


}
