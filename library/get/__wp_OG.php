<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;

// Q Theme ##
use q\theme;

class wp extends \Q {


    /**
     * Method to clean up calling and checking for the global $post object
     * Allows $post to be passed
     *
     * @param       Mixed       $post       post ID or $post object
     *
     * @since       1.0.7
     * @return      Object      WP_Post object
     */
    public static function the_post( $args = null )
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
     * Generic H1 title tag
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function the_title( $args = null ) {

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
		return ui\method::prepare_return( $args, $array );

	}


	/**
     * link to parent, works for single WP Post or page objects
     *
     * @since       1.0.1
     * @return      string   HTML
     */
    public static function the_parent( $args = null ) {

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
		
		// pages might have a parent
		if ( 
			'page' === \get_post_type( $args['config']['post'] ) 
			&& $args['config']['post']->post_parent
		) {

			// $array['text'] = __( "View More", 'q-textdomain' );
            $array['permalink'] = \get_permalink( $object->ID );
            $array['slug'] = $object->post_name;
            $array['title'] = $object->post_title;

		// is singular post ##
		} elseif ( \is_single( $args['config']['post'] ) ) {

			// h::log( 'Get category title..' );

			// $args->ID = $the_post->post_parent;
			if ( 
				! $array = self::get_the_category([ 'post' => $args['config']['post'] ])
			){

				return false;

			}


		}

        // return ##
		return ui\method::prepare_return( $args, $array );

	}
	


	
    /**
     * Get Post excerpt and return it in an HTML element with classes
     *
     * @since       1.0.7
     */
    public static function the_excerpt( $args = null )
    {

        // global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ 
		
			// h::log( 'Bailing..' ); 
		
			return false; 
		
		}

        // set-up new array ##
		$array = [];

        // get the post ##
        if ( \is_home() ) {

            // h::log('Loading home excerpt');

            $array['content'] = self::the_excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( $args['limit'] ) );

        } else if (
            \is_author()
        ) {

            // h::log('Loading author excerpt');

            $array['content'] =
                \get_the_author_meta( 'description' ) ?
                ui\markup::chop( nl2br( \get_the_author_meta( 'description' ), intval( $args['limit'] ) ) ) :
                self::the_excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( $args['limit'] ) );

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
                self::the_excerpt_from_id( intval( \get_option( 'page_for_posts' ) ), intval( $args['limit'] ) );

        } else {

            // h::log('Loading other excerpt');

            $array['content'] = self::the_excerpt_from_id( self::the_post(), intval( $args['limit'] ) );

		}
		
		// return ##
		return ui\method::prepare_return( $args, $array );

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
    public static function the_excerpt_from_id( $post = null, $length = 155, $tags = null, $extra = '&hellip;' )
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
    public static function the_content( $args = null )
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
		$array['content'] = \apply_filters( 'the_content', ui\method::clean( \get_post_field( 'post_content', $args['config']['post'] ) ) );

		// h::log( $array );

		// return ##
		return ui\method::prepare_return( $args, $array );

	}


	

	/**
     * Get Main Posts Loop
     *
     * @since       1.0.2
     */
    public static function the_posts( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'Error in passed args' );

			return false;

		}

		// h::log( $args );

		// add hardcoded query args ##
		$wp_query_args['paged'] = \get_query_var( 'paged' ) ? \get_query_var( 'paged' ) : 1 ;
		
		// merge passed args ##
		if ( 
			isset( $args['wp_query_args'] )
			&& is_array( $args['wp_query_args'] )
		){

            // merge passed args ##
			$wp_query_args = array_merge( $args['wp_query_args'], $wp_query_args );
			
		}
		
        // merge in global $wp_query variables ( required for archive pages ) ##
        if ( 
			isset( $args['wp_query_args']['query_vars'] ) 
			// && true === $args['query_vars']	
		) {

            // grab all global wp_query args ##
            global $wp_query;

            // merge all args together ##
            $wp_query_args = array_merge( $wp_query->query_vars, $wp_query_args );

			// h::log('added query vars');

        }

		// h::log( $wp_query_args );

		// filter posts_args ##
		$wp_query_args = \apply_filters( 'q/get/wp/the_posts/wp_query_args', $wp_query_args );
		
		// set-up new array to hold returned post objects ##
		$array = [];

        // run query ##
		$q_query = new \WP_Query( $wp_query_args );
		
		// put in the array key 'query' ##
		$array['query'] = $q_query ;

		// h::log( $array );

		// filter and return array ##
		return ui\method::prepare_return( $args, $array );

    }



	
	/**
	 * Helper Method to get category
	 */
	public static function get_the_category( $args = null ){

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ 
	   
		   // h::log( 'Bailing..' ); 
	   
		   return false; 
	   
	   }

	   // try and get_post_categories ##
	   if ( 
		   ! $get_the_category = \get_the_category( $the_post->ID )
	   ){

		   h::log( 'No categories found for Post: '.$the_post->post_title );

		   return false;

	   }

	   // we only want the first array item ##
	   $category = $get_the_category[0];

	   // test ##
	   // h::log( $category );

	   // categories ##
	   if (
		   ! is_object( $category )
		   || ! $category instanceof \WP_Term
	   ) {

		   h::log( 'Error in returned category' );

		   return false;

	   }

	   $array['permalink'] = \get_category_link( $category );
	   $array['slug'] = $category->slug;
	   $array['title'] = $category->cat_name;

	   // return ##
	   return ui\method::prepare_return( $args, $array );

   }

	

    /**
     * Get Post Loop
     *
     * @since       1.0.2
     */
    public static function the_post_loop( $args = array() )
    {

        #pr( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, core\config::get( 'the_posts' ) );

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
            self::the_excerpt_from_id( $the_post->ID, $args->length ) ? 
            self::the_excerpt_from_id( $the_post->ID, $args->length ) : 
			\get_bloginfo( 'description' ) ;
			
		// if is_search - highlight ##
		if ( \is_search() ) {

			$object->excerpt = 
				ui\method::search_the_content([
					'string' 	=> \apply_filters( 'q/get/wp/get_post_loop', $the_post->post_content ),
					'limit'		=> $args->length
				]) ? 
				ui\method::search_the_content([
					'string' 	=> \apply_filters( 'q/get/wp/get_post_loop', $the_post->post_content ),
					'limit'		=> $args->length
				]) : 
				self::the_excerpt_from_id( $the_post->ID, $args->length ) ;

		}

        // content ##
        $object->content = \apply_filters( 'q/get/wp/get_post_loop', $the_post->post_content );

        // date ##
        $object->date = \get_the_date( $args->date_format, $the_post->ID ); 

        // kick back post loop object ##
        return $object;

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
     * Get Pagination links
     *
     * @since       1.0.2
     * @return      String      HTML
     */
    public static function the_pagination( $args = array() )
    {

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ 
		
			help::log( 'Bailing..' ); 
		
			return false; 
		
		}

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
		$config = core\config::get('the_pagination');
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
		$paginate_args = \apply_filters( 'q/get/wp/the_pagination/args', $paginate_args );

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

		// empty array ##
		$array = [];

		// prepare first item ##
		$link_first = '?paged=1'.$fragement;
		$array[] = '<li class="'.$config['li_class'].'"><a class="'.$config['class_link_first'].'" rel="1" href="'.\esc_url( $link_first ).'">'.$config['first_text'].'</a></li>';

		// merge pagination into links ##
		$array = array_merge( $array, $paginate_links ) ;

		// prepare last item ##
		$link_last = '?paged='.$total.$fragement;
		$array[] = '<li class="'.$config['li_class'].'"><a class="'.$config['class_link_last'].'" rel="'.$total.'" href="'.\esc_url( $link_last ).'">'.$config['last_text'].'</a></li>';

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
    public static function the_navigation( $args = array() )
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
     * Get Sibling pages and return them in a flexible "landing" format
     *
     * @since       1.3.0
     * @return      string       HTML Menu
     * @todo        Add exception to block certain pages from showing - "Hide_landing = true"
     */
    public static function the_landing( $args = array() )
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
     * get nav_menu based on parent page slug
     *
     * @since       1.3.3
     * @return      string       HTML Menu
     */
    public static function the_nav_menu( $args = array() )
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
    * Get post with title %like% search term
    *
    * @param       $title          Post title to search for
    * @param       $method         wpdb method to use to retrieve results
    * @param       $columns        Array of column rows to retrieve
    *
    * @since       0.3
    * @return      Mixed           Array || False
    */
    public static function posts_with_title_like( $title = null, $method = 'get_col', $columns = array ( 'ID' ) )
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
     * Get body classes
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function body_class( $args = array() ) 
    {

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

            // h::log( 'Adding passed class..' );

            if ( is_array( $args['class'] ) ) {
                
                $args['class'] = implode( array_filter( $args['class'] ), ' ' ) ;

            } 

            // add it in ##
            array_push( $array, $args['class'] );

        }

		// h::log( $array );
		
        // check if we've got an array - if so filter and implode it ##
        $string =
            is_array( $array ) ?
            implode( array_filter( $array ), ' ' ) :
            $array ;

        // kick it back ##
        return $string;

	}



}