<?php

namespace q\wordpress;

use q\core\config as config;
use q\core\helper as helper;
// use q\theme\template as template;
// use q\theme\ui as ui;
use q\theme\markup as markup;
use q\wordpress\post as wp_post;
use q\theme\core as ui_core;

class core extends \Q {


    /**
     * Check if a plugin is active
     * 
     * @since       2.0.0
     * @return      Boolean
     */
    public static function plugin_is_active( $plugin ) 
    {
        
        return in_array( $plugin, (array) \get_site_option( 'active_plugins', [] ) );
    
    }


    /**
     * Save a value to the options table, either updating or creating a new key
     * 
     * @since       2.0.0
     * @return      Void
     */
    public static function add_update_option( $option_name, $new_value, $deprecated = ' ', $autoload = 'no' ) 
    {
    
        if ( \get_site_option( $option_name ) != $new_value ) {

            \update_site_option( $option_name, $new_value );

        } else {

            \add_site_option( $option_name, $new_value, $deprecated, $autoload );

        }
    
    }


    /**
    * Get Q Plugin data
    *
    * @return   Object
    * @since    0.3
    */
    public static function plugin_data( $refresh = false ){

        if ( $refresh ) {

            #echo 'refrshing stored framework data<br />'; ##
            \delete_site_option( 'q_plugin_data' ); // delete option ##

        }

        if ( ! $array = \get_site_option( 'q_plugin_data' ) ) {

            $array = array (
                'version'       => \Q::version
            );

            if ( $array ) {

                self::add_update_option( 'q_plugin_data', $array, '', 'yes' );

            }

        }

        return core::array_to_object( $array );

    }



    /**
    * Get installed theme data
    *
    * @return  Object
    * @since   0.3
    */
    public static function theme_data( $refresh = false )
    {

       if ( $refresh ) {

           #echo 'refrshing stored theme data<br />'; ##
           \delete_site_option( 'q_theme_data' ); // delete option ##

       }

       // declare global variable ##
       global $q_theme_data;

       $array = \get_site_option( 'q_theme_data' );

       if ( ! \get_site_option( 'q_theme_data' ) ) {

           #echo 'stored theme option empty<br />';
           #$array = @file_get_contents( q_get_option("uri_parent")."library/version/");

           if( function_exists( 'wp_get_theme' ) ) {
               $array = \wp_get_theme( \get_site_option( 'template' ));
               #$theme_version = $theme_data->Version;
           } else {
               $array = \get_theme_data( \get_template_directory() . '/style.css');
               #$theme_version = $theme_data['Version'];
           }
           #$theme_base = get_option('template');

           if ( $array ) {

               self::add_update_option( 'q_theme_data', $array, '', 'yes' );
               #echo 'stored fresh theme data<br />';

           }

       }

       return core::array_to_object( $array );

    }



	/**
     * Get body classes
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function get_body_class( $args = array() ) 
    {

        // grab global post - or kick back ##
        // if ( ! $the_post = wp_post::get( $args ) ) { 
            
            // helper::log( 'Cannot find post...' );

            // return false; 
        
        // }

        // // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        // $args = \wp_parse_args( $args, \q_theme::$the_content_open );
        // $args = ( object )$args_array;

        // helper::log( $args );

        // set-up new array ##
        $array = [];

        // get page template ##
        $template =
            \get_body_class() ?
            (array) \get_body_class() :
            array( "home" ) ;

        // add to array ##
        array_push( $array, $template[0] );

        // add page-$post_name ##
        if ( $post_name = ( isset( $the_post ) && \get_post_type() == "page" ) ? "page-{$the_post->post_name}" : false ) {

            // add to array ##
            array_push( $array, $post_name );

        }

        // added passed element, if ! is_null ##
        if ( isset ( $args['class'] ) ) {

            // helper::log( 'Adding passed class..' );

            if ( is_array( $args['class'] ) ) {
                
                $args['class'] = implode( array_filter( $args['class'] ), ' ' ) ;

            } 

            // add it in ##
            array_push( $array, $args['class'] );

        }

		// helper::log( $array );
		
        // check if we've got an array - if so filter and implode it ##
        $string =
            is_array( $array ) ?
            implode( array_filter( $array ), ' ' ) :
            $array ;

        // kick it back ##
        return $string;

	}



    /**
     * Force Post ID based on pased arguments, or return false to keep property null
     *
     * @since       1.0.7
     * @return      Mixed       Int Post ID or void
     */
    public static function set_force_post( $args = array() )
    {

        // grab global post - or kick back ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$set_force_post );

        // if we're requesting the parent - grab that ##
        if ( $args->post_parent === true && $the_post->post_parent ) {

            return self::$force_post = get_post( $the_post->post_parent );

        }

        // nothing cooking ##
        return false;

    }



    /**
    * Get post with title %like% search term
    *
    * @param       $title          Post title to search for
    * @param       $method         wpdb method to use to retrieve results
    * @param       $columns        Array of column rows to retrieve
    *
    * @since       0.3
    * @return      Mixed           Array || False
    */
    public static function get_post_with_title_like( $title = null, $method = 'get_col', $columns = array ( 'ID' ) )
    {

        // sanity check ##
        if ( ! $title ) { return false; }

        // global $wpdb ##
        global $wpdb;

        // First escape the $columns, since we don't use it with $wpdb->prepare() ##
        $columns = \esc_sql( $columns );

        // now implode the values, if it's an array ##
        if( is_array( $columns ) ){
            $columns = implode( ', ', $columns ); // e.g. "ID, post_title" ##
        }

        // run query ##
        $results = $wpdb->$method (
                $wpdb->prepare (
                "
                    SELECT $columns
                    FROM $wpdb->posts
                    WHERE {$wpdb->posts}.post_title LIKE %s
                "
                #,   esc_sql( '%'.like_escape( trim( $title ) ).'%' )
                ,   \esc_sql( '%'.$wpdb->esc_like( trim( $title )  ).'%' )
                )
            );

        #var_dump( $results );

        // return results or false ##
        return $results ? $results : false ;

    }


	



    /**
     * get nav_menu based on parent page slug
     *
     * @since       1.3.3
     * @return      string       HTML Menu
     */
    public static function get_nav_menu( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = self::the_post() ) { return false; }

        #self::log( $args );

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )\wp_parse_args( 
            $args
            , \q_theme::$the_nav_menu 
        );

        #self::log( $args );

        if ( \has_nav_menu( $args->menu ) ) {
        
            #self::log( 'has nav menu..' );

            return $args;

        }

        return false;

    }



    /**
     * Get Sibling pages and return them in a flexible "landing" format
     *
     * @since       1.3.0
     * @return      string       HTML Menu
     * @todo        Add exception to block certain pages from showing - "Hide_landing = true"
     */
    public static function get_landing( $args = array() )
    {

        // get $the_post - allows for post_forcing ##
        // move global post to a new variable, for later use ##
        if ( ! $the_post = self::the_post() ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_landing );

        // find out "depth" of current page ##
        $depth = count( \get_post_ancestors( $the_post->ID ) );

        // work out who to list pages from ##
        $post_parent = $depth == 0 ? $the_post : \get_post( $the_post->post_parent );

        $args = array (
            'child_of'          => $post_parent->ID,
            'sort_column'       => 'menu_order',
            'sort_order'        => 'ASC',
        );
        $pages = \get_pages($args);

        if ( ! $pages ) { return false; }

        #pr( $pages );

        // remove pages with children ##
        foreach ( $pages as $key => $value ) {

            if ( self::has_children( $value->ID ) ) {

                // not needed ##
                unset( $pages[$key] );

            }

        }

        // kick 'em back ##
        return $pages;

    }



    /**
     * Check if a page has children
     *
     * @since       1.3.0
     * @param       integer         $post_id
     * @return      boolean
     */
    public static function has_children( $post_id = null )
    {

        // nothing to do here ##
        if ( is_null ( $post_id ) ) { return false; }

        // meta query to allow for inclusion and exclusion of certain posts / pages ##
        $meta_query =
                array(
                    array(
                        'key'       => 'program_sub_group',
                        'value'     => '',
                        'compare'   => '='
                    )
                );

        // query for child or sibling's post ##
        $wp_args = array(
            'post_type'         => 'page',
            'orderby'           => 'menu_order',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
            'meta_query'        => $meta_query,
        );

        #pr( $wp_args );

        $object = new \WP_Query( $wp_args );

        // nothing found - why? ##
        if ( 0 === $object->post_count ) { return false; }

        // get children ##
        $children = \get_pages(
            array(
                'child_of'      => $post_id,
                'meta_key'      => '',
                'meta_value'    => '',
            )
        );

        // count 'em ##
        if( count( $children ) == 0 ) {

            // No children ##
            return false;

        } else {

            // Has Children ##
            return true;

        }

    }



    /**
     * Get Post object by post_meta query
     *
     * @since       1.0.4
     * @return      Object      $args
     */
    public static function get_post_by_meta( $args = array() )
    {

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$get_post_by_meta );

        // grab page - polylang will take care of language selection ##
        $post_args = array(
            'meta_query'        => array(
                array(
                    'key'       => $args->meta_key,
                    'value'     => $args->meta_value
                )
            ),
            'post_type'         => $args->post_type,
            'posts_per_page'    => $args->posts_per_page,
            'order'				=> $args->order,
            'orderby'			=> $args->orderby
        );

        #pr( $args );

        // run query ##
        $posts = \get_posts( $post_args );

        // check results ##
        if ( ! $posts || \is_wp_error( $posts ) ) return false;

        // test it ##
        #pr( $posts[0] );
        #pr( $args->posts_per_page );

        // if we've only got a single item - shuffle up the array ##
        if ( 1 === $args->posts_per_page && $posts[0] ) { return $posts[0]; }

        // kick back results ##
        return $posts;

	}
	


	

    /**
     * Get Pagination links
     *
     * @since       1.0.2
     * @return      String      HTML
     */
    public static function get_the_pagination( $args = array() )
    {

		// global arg validator ##
		if ( ! $args = ui_core::prepare_args( $args ) ){ 
		
			help::log( 'Bailing..' ); 
		
			return false; 
		
		}

		if ( 
			isset( $args['query'] )
		) {

			$query = $args['query'];

		// grab some global variables ##
		} else {
			
			global $wp_query;
			$query = $wp_query;

		}

		// no query, no pagination ##
		if ( ! $query ) {

			helper::log( 'Nada to query...' );

			return false;

		}

		// get config ##
		$config = config::get('the_pagination');
		// helper::log( $config );

        // work out total ##
		$total = $query->max_num_pages;

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
			
		}

		// filter args ##
		$paginate_args = \apply_filters( 'q/wordpress/get_pagination/args', $paginate_args );

		// get links from WP ##
		$paginate_links = \paginate_links( $paginate_args );

		// test ##
        // helper::log( $pages );

		// empty array ##
		$array = [];

		// prepare first item ##
		$array[] = '<li class="'.$config['li_class'].'"><a class="'.$config['class_link_first'].'" rel="1" href="?paged=1">'.$config['first_text'].'</a></li>';

		// merge pagination into links ##
		$array = array_merge( $array, $paginate_links ) ;

		// prepare last item ##
		$array[] = '<li class="'.$config['li_class'].'"><a class="'.$config['class_link_last'].'" rel="'.$total.'" href="?paged='.$total.'">'.$config['last_text'].'</a></li>';

		// test ##
        // helper::log( $array );

        // kick back array ##
        return $array;

	}
	
	


    public static function list_image_sizes()
    {

        global $_wp_additional_image_sizes; 
        if( self::$debug ) helper::log( $_wp_additional_image_sizes ); 

    }



}