<?php

namespace q\wordpress;

// Q ##
use q\core;
use q\core\helper as h;
// use q\theme\template as template;
// use q\controller\navigation as navigation;
// use q\wordpress as wp;
use q\ui;

// Q Theme ##
use q\theme\ui\controller\fourzerofour as fourzerofour;

class post extends \Q {


	/**
     *alias to get() method ##
     */
    public static function the_post( $args = null )
    {

		return self::get( $args );

	}
	
    /**
     * Method to clean up calling and checking for the global $post object
     * Allows $post to be passed
     *
     * @param       Mixed       $post       post ID or $post object
     *
     * @since       1.0.7
     * @return      Object      WP_Post object
     */
    public static function get( $args = null )
    {

        // self::log( $args );

        // let's try and get a $post from the passed $args ##
        if ( ! is_null ( $args ) && isset( $args ) ) {

            if ( is_array( $args ) && isset( $args["post"] ) ) {

                $post = $args["post"];

            } else if ( is_object ( $args ) && isset ( $args->post ) ) {

                $post = $args->post;

            } else if ( is_integer( $args ) ) {

                $post = $args;

            }

        }

        // self::log( $post );

        // first let's see if anything was set ##
        if ( isset ( $post ) ) {

            if ( ! is_object ( $post ) && is_int( $post ) ) {

                if ( $object = \get_post( $post ) ) {

                    // self::log( 'got post: '.$post );

                    return (object) $object;

                }

            } else if ( is_object ( $post ) ) {

                return $post;

            }

        }

        // next, let's try the global scope ##
        global $post;

        // kick it back ##
        return $post;

    }



	
    /**
     * Generic H1 title tag
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function get_the_title( Array $args = null ) {

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

        // h::log( $args );

        // set-up new array ##
		$array = [];

        // type ##
        $type = 'page';

        // get the title ##
        if (
            \is_home() )
        {

            $the_post = \get_option( 'page_for_posts' );
            // h::log( 'Loading home title: '.$the_post );

            // type ##
            $type = 'is_home';

            // add the title ##
			$array['title'] = \get_the_title( $the_post );
			// $array['permalink'] = \get_permalink( $the_post );

        } else if (

            \is_404()

        ){

            // type ##
            $type = 'is_404';

            // h::log('Loading archive title');
			$array['title'] = \__('Oops! It looks like you\'re lost');
			// $array['permalink'] = \get_permalink( \get_site_option( 'page_on_front' ) );

        } else if (

            \is_search()

        ){

            // h::log( 'is_search' );

            // type ##
            $type = 'is_search';

            // h::log('Loading archive title');
			$array['title'] = \sprintf( 'Search results for "%s"', $_GET['s'] );
			// $array['permalink'] = \get_permalink( \get_site_option( 'page_on_front' ) );

        } else if (

                \is_author()
                || \is_tax()
                || \is_category()
                || \is_archive()

        ) {

            // type ##
            $type = 'is_archive';

            // h::log('Loading archive title');
			$array['title'] = \get_the_archive_title();
			// $array['permalink'] = \get_permalink( \get_site_option( 'page_on_front' ) );

        } else {

			$type = 'is_single';

            // h::log('Loading post title');

            // $the_post = $the_post->ID;

            // add the title ##
			$array['title'] = \get_the_title();
			// $array['permalink'] = \get_permalink( $the_post );

        }

		// return ##
		return ui\method::prepare_return( $args, $array );

	}
	


	
    /**
     * Get Post excerpt and return it in an HTML element with classes
     *
     * @since       1.0.7
     */
    public static function get_the_excerpt( $args = array() )
    {

        // global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ 
		
			help::log( 'Bailing..' ); 
		
			return false; 
		
		}

        // set-up new array ##
		$array = [];

        // get the post ##
        if ( \is_home() ) {

            // h::log('Loading home excerpt');

            $array['content'] = excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( $args['limit'] ) );

        } else if (
            \is_author()
        ) {

            // h::log('Loading author excerpt');

            $array['content'] =
                \get_the_author_meta( 'description' ) ?
                ui\markup::chop( nl2br( \get_the_author_meta( 'description' ), intval( $args['limit'] ) ) ) :
                self::excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( $args['limit'] ) );

        } else if (
            \is_tax()
            || \is_category()
            || \is_archive()
        ) {

            // h::log('Loading category excerpt');
            // h::log( category_description() );

            $array['content'] =
                \category_description() ?
                ui\markup::chop( nl2br( \category_description(), intval( $args['limit'] ) ) ) :
                self::excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( $args['limit'] ) );

        } else {

            // h::log('Loading other excerpt');

            $array['content'] = self::excerpt_from_id( self::get(), intval( $args['limit'] ) );

		}
		
		 // return ##
		 return ui\method::prepare_return( $args, $array );

	}

    
	

	/**
     * Get Main Posts Loop
     *
     * @since       1.0.2
     */
    public static function get_posts( $args = array() )
    {

        // h::log( $args );

		// global arg validator ##
		if ( ! $args = ( object ) ui\method::prepare_args( $args ) ){ return false; }

		// h::log( $args );
		
		// we need $args->controller and $args->method for this to work ##
		$args->controller = "\\q\\theme\\theme\\controller\\{$args->template}";
		$args->method = "the_{$args->template}_loop";

        // pagination ##
        $paged = \get_query_var( 'paged' ) ? \get_query_var( 'paged' ) : 1 ;

        // args ##
        $posts_args = array (
            'posts_per_page'    => $args->posts_per_page,
            'paged'             => $paged
        );

        // merge in global $wp_query variables ? ( required for archive pages ) ##
        if ( isset( $args->query_vars ) ) {

            // grab all global wp_query args ##
            global $wp_query;

            // merge all args together ##
            $posts_args = array_merge( $wp_query->query_vars, $posts_args );

            // h::log( array( 'added query vars' => $posts_args ) );

        }

        // merge in global $wp_query variables ? ( required for archive pages ) ##
        if ( isset( $args->search ) ) {

            // h::log( 'searching...' );

            $posts_args['post_type'] = isset( $args->post_type ) ? $args->post_type : 'any' ;
            #$posts_args['posts_per_page'] = 100; // get them all ##

        }

        // h::log( $posts_args );

        // set-up main query ##
        $q_query = new \WP_Query( $posts_args );

        // h::log( $q_query->request );
        // h::log( $q_query->found_posts );
        // h::log( $q_query->post_count );

        // weird WPE hack - to reduce the returned array to the size of $args->limit ##
        if ( -1 != $args->limit && $q_query->post_count > $args->limit ) {

            // h::log( "splicing.." );
            $get_posts = array_slice( $q_query->posts, 0, $args->limit, true );

        } else {

            $get_posts = $q_query->posts;

        }

        // self::log( count( $q_query->posts ) );
        // self::log( count( $get_posts ) );

        if ( 
            $q_query->posts 
            && count( $get_posts ) > 0
        ) {

            // open wrapping tag ##
            // ui::get_tag( $args->tag, array ( 'posts', $args->class ) );

            // total ##
            if ( isset( $args->total ) ) {

                echo str_replace( '%total%', $q_query->found_posts, $args->total );

            }

            // loop ##
            foreach ( $get_posts as $post ) {

                // iterate the post loop ##
                \setup_postdata( $post );

                // h::log( $post->ID );

                // add post ID to passed args ##
                $args_array['post'] = $post->ID;

                // check if method exists in 'q_theme' ##
                if (
                    method_exists( $args->controller, $args->method )
                    // && is_callable( array( "\q\theme\ui\view\{$args->template}\{$args->template}", "the_{$args->template}_loop" ) )
                ) {

					// h::log ( "Post loop from: {$args->controller}::{$args->method}" );

                    #pr( $args_array );

                    // call template method ##
                    call_user_func (
                            array( $args->controller, $args->method )
                        ,   (array)$args_array
                    );

                } else {

					h::log ( "Method Missing : {$args->controller}::{$args->method} -- using default" );
					
					// call template method ##
                    call_user_func (
							array( 'wp_ui', 'the_post_loop' )
						,   (array)$args_array
					);

                }

                // tidy up ##
                \wp_reset_postdata();

            }

            // close wrapping tag ##
            // ui::get_tag( $args->tag, '', 'close' );

            // // get sidebar ##
            // if ( $args->sidebar ) {
                
            //     \q\controller\navigation::the_sidebar();

            // }

            // pagination ##
            
            if ( isset( $args->pagination ) ) {

                // h::log( 'Adding pagination..' );
                navigation::the_pagination([ 'query' => $q_query ]);

            }

        } else {

            // nothing found ##
            fourzerofour::render();

        }

    }



    /**
     * Get object with loop variables
     *
     * @since       1.0.4
     * @return      Object      each property holds a loop variable
     */
	/*
    public static function get_loop( $args = array() )
    {

        // test incoming args ##
        #pr( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, q_theme::$the_loop );

        // set-up new object ##
        $object = new stdClass;

        // default image ##
        $object->src = $args->holder;

        // check for featured image ##
        if ( \has_post_thumbnail( $the_post->ID ) ) {

            $src = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), $args->image_handle );
            $object->src = $src[0];

        }

        // title - keep to 60 characters ##
        $object->title = markup::chop( \get_the_title( $the_post->ID ), $args->title_length );

        // get the excerpt ##
        $object->excerpt = self::excerpt_from_id( $the_post->ID, $args->excerpt_length );

        // kick it back ##
        return $object;

    }
	*/


    /**
     * Gets the excerpt of a specific post ID or object
     *
     * @param   $post       object/int  the ID or object of the post to get the excerpt of
     * @param   $length     int         the length of the excerpt in words
     * @param   $tags       string      the allowed HTML tags. These will not be stripped out
     * @param   $extra      string      text to append to the end of the excerpt
     *
     * @link    http://pippinsplugins.com/a-better-wordpress-excerpt-by-id-function/        Reference
     *
     * @since 0.1
     */
    public static function excerpt_from_id( $post, $length = 155, $tags = null, $extra = '&hellip;' )
    {

        if( is_int( $post) ) {
            $post = \get_post( $post );
        } elseif( ! is_object( $post ) ) {
            // var_dump( 'no $post' );
            return false;
        }

        if( \has_excerpt( $post->ID ) ) {
            $the_excerpt = $post->post_excerpt;
        } else {
            $the_excerpt = $post->post_content;
        }

        $the_excerpt = \strip_shortcodes( strip_tags( $the_excerpt, $tags ) );
        #pr( $length );

        if ( $length > 0 && strlen( $the_excerpt ) > $length ) { // length set and excerpt too long so chop ##
            $the_excerpt = substr( $the_excerpt, 0, $length ).$extra;
        }

        // var_dump( $the_excerpt );

        return \apply_filters( 'q/wordpress/excerpt_from_id', $the_excerpt );

    }




    /**
     * Get Post Loop
     *
     * @since       1.0.2
     */
    public static function get_post_loop( $args = array() )
    {

        #pr( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, config::get( 'the_posts' ) );

        #pr( $the_post->ID );

        // build a new object ##
        $object = new \stdClass;

        #return pr( $post->ID );

        // ID ##
        $object->ID = $the_post->ID;

        // sticky ?? ##
        $object->sticky = 
            in_array( $the_post->ID, \get_site_option( 'sticky_posts', [] ) ) ?
            true :
            false ;

        // title ##
        $object->title = \get_the_title( $the_post->ID );

        // permalink ##
        $object->permalink = \get_permalink( $the_post->ID );

        // category ##
        $object->category = \wp_get_post_terms( $the_post->ID, 'category' );
        #pr( $category );

        // tag ##
        $object->tag = \wp_get_post_terms( $the_post->ID, 'post_tag' );
        #pr( $category );

        // is this an "auto" tagged post ##
        $object->auto = \has_tag( "Auto", $the_post ) ? true : false ;

        // do we have a real image ##
        $has_src = true;
        $object->src = '';
		
		/*
        // image ##
        if ( \has_post_thumbnail( $the_post->ID ) ) {

            // handle might not be an array based on devices ##
            $handle = is_array( $args->handle ) ? $args->handle[h::device()] : $args->handle ;

            // show small image, linking to larger image ##
			$img_src = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), $args->handle ); 
			
			// h::log( $img_src );

            $object->src = $img_src[0]; // take first array item ##

            #if ( self::attachment_exists( get_post_thumbnail_id( $the_post->ID ) ) ) {

                // update flag ##
            #    $has_src = true;

            #}

        // check for taxonomy image ##
        } else if (

            function_exists ( 'get_field' ) &&
            isset( $object->tag ) && isset( $object->tag[0] ) &&
            isset( $object->category ) && isset( $object->category[0] ) &&
            $src = 
                \is_tag() ? 
                \get_field( 'taxonomy_image', 'post_tag_'.$object->tag[0]->term_id ) : 
                \get_field( 'taxonomy_image', 'category_'.$object->category[0]->term_id )

        ) {

            $src_array = \wp_get_attachment_image_src( $src, $args->handle[h::device()] );
            $object->src = $src_array[0];

		}
		*/

        // backup ##
        if ( ! $has_src ) {

            $object->src = $args->holder[h::device()];

        }

        // content ##
        $object->excerpt = 
            self::excerpt_from_id( $the_post->ID, $args->length ) ? 
            self::excerpt_from_id( $the_post->ID, $args->length ) : 
            \get_bloginfo( 'description' ) ;

        // content ##
        $object->content = \apply_filters( 'q/wordpress/get_post_loop', $the_post->post_content );

        // date ##
        $object->date = \get_the_date( $args->date_format, $the_post->ID ); 

        // kick back post loop object ##
        return $object;

    }



    /**
     * Check for a return post thumbnail images and exif-data baed on passed settings ##
     *
     */
    public static function get_post_thumbnail( $args = array() )
    {

        // test incoming args ##
        // self::log( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post() ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_post_thumbnail );

        #pr( $args );

        // set-up a new object ##
        $object = new \stdClass;

        if ( ! \has_post_thumbnail( $the_post->ID ) ) { return false; }

        // self::log( 'Handle: '.$args->handle[self::device()] );

        // show small image, linking to larger image ##
        $object->src = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), $args->handle[h::device()] );
        $img_alt = \get_post_meta( \get_post_thumbnail_id( $the_post->ID ), '_wp_attachment_image_alt', true);
        $object->alt = ( $img_alt ? $img_alt : \get_the_title() ) ;

        // image found ? ##
        if ( ! $object->src ) { return false; }

        // kick back object ##
        return $object;

    }


    /**
     * Get Sibling pages
     *
     * @since       1.0.1
     * @return      string       HTML Menu
     */
    public static function get_navigation( $args = array() )
    {

        // get $the_post - allows for post_forcing ##
        // move global post to a new variable, for later use ##
        if ( ! $global_post = self::$force_post ? self::$force_post : \Q::the_post() ) { return false; }

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
        $posts = \apply_filters( 'q/wordpress/get_navigation', $posts );

        // return object ##
        return $posts;

    }


}