<?php

namespace q\render;

use q\core; // core functions, options files ##
use q\core\helper as h; // helper shortcut ##
use q\plugin; // plugins ## 
use q\ui; // template, ui, markup... ##
use q\get; // wp, db, data lookups ##
// use q\render;

class method extends \q\render {


	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function the_content_open( $args = null )
    {

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

        // grab classes ##
		$array['classes'] = get\wp::body_class( $args );

        // return ##
		return ui\method::prepare_render( $args, $array );

	}

	

	/**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function the_content_close( $args = null )
    {

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

        // set-up new array -- nothing really to do ##
		$array[] = []; // hack.. nothing to pass here ##

        // return ##
		return ui\method::prepare_render( $args, $array );

	}



	/**
     * Generic H1 title tag
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function the_title( $args = null ) {

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		$array = get\wp::the_title( $args );

        // return ##
		return ui\method::prepare_render( $args, $array );

    }


	
    /**
    * Render WP_Query
    *
    * @since       1.0.2
    */
    public static function the_posts( $args = [] )
    {

		// validate passed args ##
        if ( ! $args = args::validate( $args ) ) {

            log::render( $args );

            return false;

		}

		// build $args['fields'] -- @todo -- this can be moved to a pre-function call ##
		self::$args['fields'] = [];

		// build fields array with default values ##
		self::$fields = [
			'total' 		=> '0', // set to zero string value ##
			'pagination' 	=> null, // empty field.. ##
			// 'pagination' = false, // don't load pagination ##
			'posts' 		=> $args['markup']['no_results'] // replace posts with no_results markup ## is this right ?? ##
		];

        // pass to get_posts -- and validate that we get an array back ##
        if ( ! $array = get\wp::the_posts( $args ) ) {

			// return false;

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' =>  'the_posts did not return any data'
			]);

		}

		// validate what came back - it should include the WP Query, posts and totals ##
		if ( 
			! isset( $array['query'] ) 
			|| ! isset( $array['query']->posts ) 
			// || ! isset( $array['query']->posts ) 
		){

			h::log( 'Error in data returned from the_posts' );

			// log ##
			log::add([
				'key' => 'notice', 
				'field'	=> __FUNCTION__,
				'value' =>  'Error in data returned from the_posts'
			]);

		}
		
		// no posts.. so empty, set count to 0 and no pagination ##
		if ( 
			empty( $array['query']->posts )
			|| 0 == count( $array['query']->posts )
		){

			h::log( 'No results returned from the_posts' );

		// we have posts, so let's add some charm ##
		} else {

			// define all required fields for markup ##
			self::$fields = [
				'total' 		=> count( $array['query']->posts ), // total posts ##
				'pagination'	=> ui\navigation::the_pagination( $array, 'return' ), #'PAGINATION', // @todo ## -- perhaps we have to call the_pagination here ??? ##
				'posts'			=> $array['query']->posts // array of WP_Posts ##
			];

		}

		// filter fields by template ##
		self::$fields = \apply_filters( 'q/render/the_posts/'.ui\template::get(), self::$fields, self::$args );

		// h::log( self::$fields );

		// check each field data and apply numerous filters ##
		fields::prepare();

		// Prepare template markup ##
        markup::prepare();

        // optional logging to show removals and stats ##
        log::render( $args );

        // return or echo ##
        return output::return();

    }



	
    /**
    * Get Post Loop
    *
    * @since       1.0.2
	*/
	/*
    public static function the_post_loop( $args = null ){

        // h::log( $args );

        // grab object with post_loop data ##
        if ( ! $object = get\wp::the_post_loop( $args ) ) { 
		
			h::log( 'Error in $object returned from get\the_post_loop' );

			return false; 
		
		}

        // auto ##
        $class = ( isset( $object->auto ) && $object->auto ) ? ' auto' : '' ;

        // class ##
        $class .= true === $object->sticky ? ' is_sticky' : ' not_sticky' ;

?>
        <li class="the-post-loop">

            <div class="blog-wrapper equal-item use_wrap posts<?php echo $class; if ( 'handheld' != h::device() ) echo ' whole'; ?>">
<?php

                // auto ##
                if ( isset( $object->auto ) && $object->auto ) {

?>                  <span class="auto"></span><?php

                }

?>
                <a class="blog-image lazy rounded" href="<?php echo $object->permalink; ?>" data-src="<?php echo $object->src; ?>">
                    <span></span>
                </a>
                <a href="<?php echo $object->permalink; ?>" class="use"><h3><?php echo $object->title; ?></h3></a>
                <p><?php echo $object->excerpt; ?></p>
<?php
                // test ID ##
                #pr( $object->ID );

                // post meta ##
                self::the_meta( array( 'post' => $object->ID ) );

?>
            </div>
        </li>
<?php

    }
	*/


	

    /**
    * The Post Meta
    *
    * @since       1.0.2
    */
    public static function the_meta( $args = null )
    {

        // get the_post ##
        if ( ! $the_post = get\wp::the_post( $args ) ) { return false; }

        // test ID ##
        #h::log( $the_post->ID );

        // load config from Q.. meged via filter ##
        $args = ( object ) core\config::get( 'the_post_meta' );
        #h::log( $args );

?>
        <div class="post-meta">
<?php

			// post time ##
            printf(
                __( '<span class="date">Posted %s ago </span>', self::text_domain )
                ,   \human_time_diff( \get_the_date( 'U', $the_post->ID ), \current_time('timestamp') )
            );

			// post author ##
            self::the_author( [
				'markup' => '<span class="author mr-1">Posted by <a href="%permalink%">%title%</a></span>', 
				'post'		=> $the_post // needed for loops ##
			]);
			
            // post category ##
            self::the_category( [
				'markup' 	=> '<span class="category ml-1 mr-1">in <a href="%permalink%">%title%</a></span>', 
				'post'		=> $the_post // needed for loops ##
			]);

            // if on single page and post has tags, show them ##
            // h::log( \has_tag( '', $the_post->ID ) );
            if ( 
                \is_single() 
				&& 
				\has_tag( '', $the_post->ID ) 
            ) {

                // get the tags ##
                $the_tags = \get_the_tags();
                $tags = ''; // empty ##
                if ( $the_tags ) {
                    foreach( $the_tags as $tag ) {
                        $tags .= '<span class="tag"><a href="'.\get_tag_link( $tag->term_id ).'">#'.$tag->name.'</a></span> ';
                    }
                }

                \printf(
                    \__( '<span class="tags">, Tagged: %s</span>', self::text_domain )
                     ,   $tags
                );

            }

            // comments ##
            if ( $args->allow_comments && 'open' == $the_post->comment_status ) {

                // get number of comments ##
                $comments_number = \get_comments_number( $the_post->ID );

                if ( $comments_number == 0 ) {
                    $comments = __( 'Comment', self::text_domain );
                } elseif ( $comments_number > 1 ) {
                    $comments = $comments_number.' '.__( ' Comments', self::text_domain );
                } else {
                    $comments = '1'.__( 'Comment', self::text_domain );
                }

                if ( \is_single() ) {

                    printf(
                        __( ', <span class="comment"><a href="%s" class="anchor-trigger" data-scroll="#comments">%s</a></span>', self::text_domain )
                        ,   '#comment' // variable link ##
                        ,   $comments
                    );

                } else {

                    printf(
                        __( ', <span class="comment"><a href="%s">%s</a></span>', self::text_domain )
                        ,   \get_the_permalink( $the_post->ID ).'#comment' // variable link ##
                        ,   $comments
                    );

                }

            }


?>
        </div>
<?php

    }




    /**
	 * Helper Method to get parent
	 */
	public static function the_parent( $args = null ){

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		$array = get\wp::the_parent( $args );

        // return ##
		return ui\method::prepare_render( $args, $array );

	}



	/**
	 * Helper Method to get the_excerpt
	 */
	public static function the_excerpt( $args = null ){

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		$array = get\wp::the_excerpt( $args );

        // return ##
		return ui\method::prepare_render( $args, $array );

	}




	/**
	 * Helper Method to get the_content
	 */
	public static function the_content( Array $args = null ){

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		$array = get\wp::the_content( $args );

        // return ##
		return ui\method::prepare_render( $args, $array );

	}



	/**
	 * Helper Method to get the_fields - ui\field\render() ##
	 */
	public static function the_group( Array $args = null ){

		// bounce on, and return array ##
		return group::render( $args );

	}


	

	public static function the_category( Array $args = null ) {

		if ( 
			is_null( $args )
		) {

			h::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['config']['post'] ) 
			&& $args['config']['post'] instanceof \WP_Post
		) {

			$the_post = $args['config']['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			h::log( 'cats: Error with post object, validate.' );

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

		// h::log( $array );

		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			h::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = ui\method::markup( $args['markup'], $array );

		// h::log( $string );

		// echo ##
		echo $string ;

		// stop ##
		return true;

	}



	/**
	 * Helper Method to get category
	 */
	public static function get_the_category( Array $args = null ){

		// we want to return ##
		$args['return'] = 'return';

		// bounce on, and return array ##
		return \apply_filters( 'q/wordpress/get_the_category', self::the_category( $args ) );

	}




	public static function the_author( Array $args = null ) {

		if ( 
			is_null( $args )
		) {

			h::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['config']['post'] ) 
			&& $args['config']['post'] instanceof \WP_Post
		) {

			$the_post = $args['config']['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			h::log( 'auth: Error with post object, validate.' );

			return false;

		}

		// get author ##
		$author = $the_post->post_author;
		$authordata = \get_userdata( $author );

		// validate ##
		if (
			! $authordata
		) {

			h::log( 'Error in returned author data' );

			return false;

		}

		// get author name ##
		$author_name = $authordata && isset( $authordata->display_name ) ? $authordata->display_name : 'Author' ;

		// assign values ##
		$array['permalink'] = \esc_url( \get_author_posts_url( $author ) );
		$array['slug'] = $authordata->user_login;
		$array['title'] = $author_name;

		// h::log( $array );

		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			h::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = ui\method::markup( $args['markup'], $array );

		// h::log( $string );

		// echo ##
		echo $string ;

		// stop ##
		return true;

	}



	/**
	 * Helper Method to get the author
	 */
	public static function get_the_author( Array $args = null ){

		// we want to return ##
		$args['return'] = 'return';

		// bounce on, and return array ##
		return \apply_filters( 'q/wordpress/get_the_author', self::the_author( $args ) );

	}




	public static function the_date( Array $args = null ) {

		if ( 
			is_null( $args )
		) {

			h::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['config']['post'] ) 
			&& $args['config']['post'] instanceof \WP_Post
		) {

			$the_post = $args['config']['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			h::log( 'date: Error with post object, validate.' );

			return false;

		}

		// get author ##
		$author = $the_post->post_author;
		$authordata = \get_userdata( $author );

		// validate ##
		if (
			! $authordata
		) {

			h::log( 'Error in returned author data' );

			return false;

		}

		// get author name ##
		$author_name = $authordata && isset( $authordata->display_name ) ? $authordata->display_name : 'Author' ;

		// assign values ##
		$array['permalink'] = \esc_url( \get_author_posts_url( $author ) );
		$array['slug'] = $authordata->user_login;
		$array['title'] = $author_name;

		// h::log( $array );

		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			h::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = theme\markup::apply( $args['markup'], $array );

		// h::log( $string );

		// echo ##
		echo $string ;

		// stop ##
		return true;

	}



	/**
	 * Helper Method to get the author
	 */
	public static function get_the_date( Array $args = null ){

		// we want to return ##
		$args['return'] = 'return';

		// bounce on, and return array ##
		return \apply_filters( 'q/wordpress/get_the_date', self::the_date( $args ) );

	}



	

    /**
    * Check for a return post thumbnail images and exif-data baed on passed settings ##
    *
    */
    public static function the_post_thumbnail( $args = array() )
    {

        // pass to functions
        if ( ! $object = wp\media::get_post_thumbnail( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )\wp_parse_args( $args, config::$the_post_thumbnail );

?>
        <img src="<?php echo $object->src[0]; ?>" alt="<?php echo $object->alt; ?>" class="<?php echo $args->class; ?>" />
<?php

    }



    public static function the_password_form()
    {

?>
        <div class="password" style="text-align: center; margin: 20px;">
            <?php echo \get_the_password_form(); ?>
        </div>
<?php

        return true;

    }

}