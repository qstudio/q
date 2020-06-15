<?php

namespace q\render;

use q\core; // core functions, options files ##
use q\core\helper as h; // helper shortcut ##
use q\plugin; // plugins ## 
// use q\ui; // template, ui, markup... ##
use q\get; // wp, db, data lookups ##
use q\render; // self ##

// Q Theme ##
use q\theme;

class post extends \q\render {


	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = null, $method = null ){

		// return h::log( 'hello here..' );

        // validate passed args ##
        if ( ! render\args::validate( $args ) ) {

			render\log::set( $args );
			
			// h::log( 'd:>Bunked here..' );

            return false;

		}

		// h::log( $args );

		// run method to populate field data ##
		// $method = $args['config']['method'];
		if (
			! \method_exists( get_class(), $method ) // && exists ##
		) {

			h::log( 'd:>Cannot locate method: '.$method );

		}

		// call render method ##
		self::{ $method }( self::$args );
		// h::log( self::$fields );

		// Now we can loop over each field ---
		// running callbacks ##
		// formatting none string types to strings ##
		// removing placeholders in markup, if no field data found etc ##
		render\fields::prepare();
		
		// h::log( self::$fields );

        // Prepare template markup ##
        render\markup::prepare();

        // optional logging to show removals and stats ##
        render\log::set( $args );

        // return or echo ##
        return render\output::return();

	}
	


	// ---------- methods ##




	/**
     * Generic H1 title tag
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function title( $args = null ) {

		// global arg validator ##
		// if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		// $array = get\post::title( $args );
		render\fields::define(
			get\post::title( $args )
		);

        // return ##
		// return ui\method::prepare_render( $args, $array );

    }


	
    /**
    * Render WP_Query
    *
    * @since       1.0.2
    */
    public static function query( $args = [] )
    {

		// validate passed args ##
        // if ( ! $args = render\args::validate( $args ) ) {

        //     // render\log::render( $args );

        //     return false;

		// }

		// h::log( $args );

		// build $args['fields'] -- @todo -- this can be removed due to args...##
		// self::$args['fields'] = [];

		// build fields array with default values ##
		render\fields::define([
			'total' 		=> '0', // set to zero string value ##
			'pagination' 	=> null, // empty field.. ##
			'posts' 		=> $args['no_results'] // replace posts with no_results markup ##
		]);

        // pass to get_posts -- and validate that we get an array back ##
        // if ( ! $array = get\wp::the_posts( $args ) ) {
		if ( ! $array = get\query::posts( $args ) ) {

			// return false;

			// log ##
			h::log( self::$args['group'].'~>n:query::posts did not return any data');

		}

		// validate what came back - it should include the WP Query, posts and totals ##
		if ( 
			! isset( $array['query'] ) 
			|| ! isset( $array['query']->posts ) 
			// || ! isset( $array['query']->posts ) 
		){

			// h::log( 'Error in data returned from query::posts' );

			// log ##
			h::log( self::$args['group'].'~>n:Error in data returned from query::posts');

		}
		
		// no posts.. so empty, set count to 0 and no pagination ##
		if ( 
			empty( $array['query']->posts )
			|| 0 == count( $array['query']->posts )
		){

			// h::log( 'No results returned from the_posts' );
			h::log( self::$args['group'].'~>n:No results returned from query::posts');

		// we have posts, so let's add some charm ##
		} else {

			// define all required fields for markup ##
			self::$fields = [
				'total' 		=> $array['query']->found_posts, // total posts ##
				'pagination'	=> theme\ui\module\navigation::pagination( $array, 'return' ), // get pagination ##
				'posts'			=> $array['query']->posts // array of WP_Posts ##
			];

		}

		// // filter fields by template ##
		// self::$fields = \apply_filters( 'q/render/posts/'.ui\template::get(), self::$fields, self::$args );

		// ok ##
		return true;

		// h::log( self::$fields );

		// check each field data and apply numerous filters ##
		// render\fields::prepare();

		// // Prepare template markup ##
        // render\markup::prepare();

        // // optional logging to show removals and stats ##
        // // render\log::set( $args );

        // // return or echo ##
        // return render\output::return();

    }

	

    /**
    * The Post Meta
    *
    * @since       1.0.2
    */
    public static function the_meta( $args = null )
    {

        // get the_post ##
        if ( ! $the_post = get\post::object( $args ) ) { return false; }

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
	public static function parent( $args = null ){

		// global arg validator ##
		// if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get parent - returns false OR array with key 'title, slug, permalink' ##
		// $array = get\post::parent( $args );
		// h::log( $array );
		render\fields::define( get\post::parent( $args ) ) ;

        // return ##
		// return ui\method::prepare_render( $args, $array );

	}



	/**
	 * Helper Method to get the_excerpt
	 */
	public static function excerpt( $args = null ){

		// global arg validator ##
		// if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		// $array = get\wp::the_excerpt( $args );
		// $array = get\post::excerpt( $args );
		render\fields::define( get\post::excerpt( $args ) ) ;

        // return ##
		// return ui\method::prepare_render( $args, $array );

	}




	/**
	 * Helper Method to get the_content
	 */
	public static function content( Array $args = null ){

		// global arg validator ##
		// if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get content - returns array with key 'content' ##
		// $array = get\post::content( $args );
		render\fields::define( get\post::content( $args ) ) ;

        // return ##
		// return ui\method::prepare_render( $args, $array );

	}



	// /**
	//  * Helper Method to render::group - ui\field\render() ##
	//  */
	// public static function group( Array $args = null ){

	// 	// bounce on, and return array ##
	// 	return group::render( $args );

	// }




	// /**
	//  * Helper Method to render:field - ui\field\render() ##
	//  */
	// public static function field( Array $args = null ){

	// 	// global arg validator ##
	// 	if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

	// 	// get content - returns array with key 'content' ##
	// 	$array = get\post::field( $args );

    //     // return ##
	// 	return ui\method::prepare_render( $args, $array );

	// }


	

	public static function the_category( Array $args = null ) {

		// global arg validator ##
		if ( ! $args = ui\method::prepare_args( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		$array = get\wp::the_category( $args );

        // return ##
		return ui\method::prepare_render( $args, $array );

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
