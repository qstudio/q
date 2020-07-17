<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\willow\render;
use q\strings;

// Q Theme ##
use q\theme;

class post extends \q\get {

    /**
     * Method to clean up calling and checking for the global $post object
     * Allows $post to be passed
     *
     * @param       Mixed       $post       post ID or $post object
     *
     * @since       1.0.7
     * @return      Object      WP_Post object
     */
    public static function object( $args = null )
    {

        // h::log( $args );

        // let's try and get a $post from the passed $args ##
        if ( ! is_null ( $args ) && isset( $args ) ) {

            if ( is_array( $args ) && isset( $args["post"] ) ) {

				$post = $args["post"];
				// h::log( 'Post ID sent: '.$post );

            } else if ( is_object ( $args ) && isset ( $args->post ) ) {

                $post = $args->post;

            } else if ( is_integer( $args ) ) {

                $post = $args;

            }

        }

        // h::log( $post );

        // first let's see if anything was set ##
        if ( isset ( $post ) ) {

			// h::log( gettype( $post ) );

			// if ( ! is_object ( $post ) && is_int( $post ) ) {
            if ( is_string ( $post ) || is_int( $post ) ) {

                if ( $object = \get_post( $post ) ) {

                    // h::log( 'got post: '.$object->ID );

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
     * Get post object terms
     *
     * @since       4.0.0
     */
    public static function object_terms( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
			// || ! isset( $args['taxonomy'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// taxonomy -- defaults to category ##
		$taxonomy = isset( $args['taxonomy'] ) ? $args['taxonomy'] : 'category' ; 
		// h::log( 'd:>'.$taxonomy );

		// post ID ##
		$post_id = isset( $args['config']['post'] ) ? $args['config']['post']->ID : null ;
		// h::log( 'd:>post_id: '.$post_id );

		// $args ##
		$args = isset( $args['args'] ) ? $args['args'] : null ;
		// h::log( $args );

		// get field ##
		$array = \wp_get_post_terms( $post_id, $taxonomy, $args );
		// $array = \wp_the_terms( $post_id, $taxonomy );

		// we expect an array with 1 key [0] of WP_Term object - validate ##
		if (
			! is_array( $array )
			|| is_wp_error( $array )
		){

			h::log( 'e:>Error in returned terms data' );

			return false;

		}

		// h::log( $array );
		
		// return
		return get\method::prepare_return( $args, $array );

	}

	

	
    /**
     * Generic H1 title tag
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function title( $args = null ) {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

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
		return get\method::prepare_return( $args, $array );

	}


	

	/**
     * link to parent, works for single WP Post or page objects
     *
     * @since       1.0.1
     * @return      string   HTML
     */
    public static function parent( $args = null ) {

		// h::log( 'here..' );
		// h::log( $args );

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

        // set-up new array ##
		$array = [];
		
		// pages might have a parent
		if ( 
			'page' === $args['config']['post']->post_type
			&& $args['config']['post']->post_parent
		) {

			// h::log( 'Here..' );

            $array['permalink'] = \get_permalink( $args['config']['post']->ID );
            $array['slug'] = $args['config']['post']->post_name;
            $array['title'] = $args['config']['post']->post_title;

		// is singular post ##
		} elseif ( \is_single( $args['config']['post'] ) ) {

			// h::log( 'd:>Get category title..' );

			// $args->ID = $the_post->post_parent;
			if ( 
				! $terms = self::object_terms([ 
					'config' 		=> [ 
						'post'		=> $args['config']['post']
					],
					'taxonomy'		=> 'category',
					'args' 			=> [
						'number'	=> 1
					]
				])
					
			){

				h::log( 'e:>Returned terms empty' );

				return false;

			}

			// h::log( $terms );

			// we expect an array with 1 key [0] of WP_Term object - validate ##
			if (
			 	! is_array( $terms )
			 	|| ! isset( $terms[0] )
			 	|| ! $terms[0] instanceof \WP_Term
			){

			 	h::log( 'e:>Error in returned terms data' );

			 	return false;

			}

			$array['permalink'] = \get_category_link( $terms[0] );
			$array['slug'] = $terms[0]->slug;
			$array['title'] = $terms[0]->name;

		}

		// h::log( $array );

        // return ##
		return get\method::prepare_return( $args, $array );

	}



	
    /**
     * Get Post excerpt and return it in an HTML element with classes
     *
     * @since       1.0.7
     */
    public static function excerpt( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}


        // set-up new array ##
		$array = [];

        // get the post ##
        if ( \is_home() ) {

            // h::log('Loading home excerpt');

            $array['content'] = self::excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( isset( $args['limit'] ) ? $args['limit'] : 200 ) );

        } else if (
            \is_author()
        ) {

            // h::log('Loading author excerpt');

            $array['content'] =
                \get_the_author_meta( 'description' ) ?
                strings\method::chop( nl2br( \get_the_author_meta( 'description' ), intval( isset( $args['limit'] ) ? $args['limit'] : 200 ) ) ) :
                self::excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( isset( $args['limit'] ) ? $args['limit'] : 200 ) );

        } else if (
            \is_tax()
            || \is_category()
            || \is_archive()
        ) {

            // h::log('Loading category excerpt');
            // h::log( category_description() );

            $array['content'] =
                \category_description() ?
                strings\method::chop( nl2br( \category_description(), intval( isset( $args['limit'] ) ? $args['limit'] : 200 ) ) ) :
                self::excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( isset( $args['limit'] ) ? $args['limit'] : 200 ) );

        } else {

            // h::log('Loading other excerpt');

            $array['content'] = self::excerpt_from_id( get\post::object(), intval( isset( $args['limit'] ) ? $args['limit'] : 200 ) );

		}
		
		// return ##
		return get\method::prepare_return( $args, $array );

	}



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
    public static function excerpt_from_id( $post = null, $length = 155, $tags = null, $extra = '&hellip;' )
    {

		// null post ##
		if ( is_null( $post ) ) {

			$post = self::the_post();

		}

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

        return \apply_filters( 'q/get/wp/excerpt_from_id', $the_excerpt );

	}



    /**
    * Return the_content with basic filters applied
    *
    * @since       1.0.1
    * @return      string       HTML
    */
    public static function content( $args = null )
    {

		// h::log( 'e:>post->content hit..' );

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

        // set-up new array ##
		$array = [];

		// h::log( \get_post_field( 'post_content', $args['config']['post'] ) );

		// get the post_content with filters applied ##
		$array['content'] = \apply_filters( 'the_content', strings\method::clean( \get_post_field( 'post_content', $args['config']['post'] ) ) );

		// h::log( $array );

		// return ##
		return get\method::prepare_return( $args, $array );

	}



	/**
    * Return the_date with specified format
    *
    * @since       1.0.1
    * @return      string       HTML
    */
    public static function date( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

        // set-up new array ##
		$array = [];

		// get the post_content with filters applied ##
		$array['post_date'] = 
			\get_the_date( 
				isset( $args['config']['date_format'] ) ? 
				$args['date_format']['config'] : // take from value passed by caller ##
					core\config::get([ 'context' => 'global', 'task' => 'config', 'property' => 'date_format' ]) ?: // take from global config ##
					\apply_filters( 'q/format/date', 'F j, Y' ), // standard ##
				$args['config']['post']->ID
			);

		// h::log( $array );

		// return ##
		return get\method::prepare_return( $args, $array );

	}



	
    /**
    * The Post Data ( meta.. )
    *
    * @since       1.0.2
    */
    public static function data( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

        // get the_post ##
		$post = $args['config']['post'];

        // test ID ##
        #h::log( $post->ID );

		// starts with an empty array ##
		$array = [];

		// post time in @since format ##
		$array['post_date_human'] = \human_time_diff( \get_the_date( 'U', $post->ID ), \current_time('timestamp') );

		// post time ##
		$array['post_date'] = get\post::date( $args );

		// post author ##
		$array = render\method::extract( get\meta::author( $args ), 'author_', $array );

		// category will be an array, so create category_title, permalink and slug fields ##
		$array = render\method::extract( get\taxonomy::category( $args ), 'category_', $array );

		// tag will be an array, so create tag_title, permalink and slug fields ##
		$array = render\method::extract( get\taxonomy::tag( $args ), 'tag_', $array );

		// tags will be an array, we'll let the rendered deal with this via a section tag.. ##
		$array['tags'] = get\taxonomy::tags( $args );

		// comment will be an array, so create comment_count, link ##
		$array = render\method::extract( get\meta::comment( $args ), 'comment_', $array );

		// return
		return get\method::prepare_return( $args, $array );

    }


}
