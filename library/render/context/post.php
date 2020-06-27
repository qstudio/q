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

	/** MAGIC */
	public static function __callStatic( $function, $args ) {

        return self::run( $args ); 
	
	}

	public static function run( $args = null ){

        // validate passed args ##
        if ( ! render\args::validate( $args ) ) {

			render\log::set( $args );
			
			// h::log( 'd:>Bunked here..' );

            return false;

		}

		// h::log( $args );

		// run method to populate field data ##
		$method = $args['task'];
		if (
			! \method_exists( get_class(), $method ) // && exists ##
		) {

			h::log( 'e:>Cannot locate method: '.__CLASS__.'::'.$method );

		}

		// call render method ##
		self::{ $method }( self::$args );
		// h::log( 'method: '.$method );
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

		// get title - returns array with key 'title' ##
		render\fields::define(
			get\post::title( $args )
		);

    }


	
    /**
    * Render WP_Query
    *
    * @since       1.0.2
    */
    public static function query( $args = [] )
    {

		// h::log( $args );

		// build fields array with default values ##
		render\fields::define([
			'total' 		=> '0', // set to zero string value ##
			'pagination' 	=> null, // empty field.. ##
			'posts' 		=> $args['no_results'] // replace posts with no_results markup ##
		]);

        // pass to get_posts -- and validate that we get an array back ##
		if ( ! $array = get\query::posts( $args ) ) {

			// return false;

			// log ##
			h::log( self::$args['task'].'~>n:query::posts did not return any data');

		}

		// validate what came back - it should include the WP Query, posts and totals ##
		if ( 
			! isset( $array['query'] ) 
			|| ! isset( $array['query']->posts ) 
			// || ! isset( $array['query']->posts ) 
		){

			// h::log( 'Error in data returned from query::posts' );

			// log ##
			h::log( self::$args['task'].'~>n:Error in data returned from query::posts');

		}
		
		// no posts.. so empty, set count to 0 and no pagination ##
		if ( 
			empty( $array['query']->posts )
			|| 0 == count( $array['query']->posts )
		){

			// h::log( 'No results returned from the_posts' );
			h::log( self::$args['task'].'~>n:No results returned from query::posts');

		// we have posts, so let's add some charm ##
		} else {

			// merge array into args ##
			$args = core\method::parse_args( $array, $args );

			// define all required fields for markup ##
			self::$fields = [
				'total' 		=> $array['query']->found_posts, // total posts ##
				'pagination'	=> theme\module\navigation::pagination( $args, 'return' ), // get pagination ##
				'posts'			=> $array['query']->posts // array of WP_Posts ##
			];

		}

		// ok ##
		return true;

    }

	




    /**
	 * Helper Method to get parent
	 */
	public static function parent( $args = null ){

		// get parent - returns false OR array with key 'title, slug, permalink' ##
		render\fields::define( 
			get\post::parent( $args ) 
		);

	}



	/**
	 * Helper Method to get the_excerpt
	 */
	public static function excerpt( $args = null ){

		render\fields::define( 
			get\post::excerpt( $args ) 
		);

	}




	/**
	 * Helper Method to get the_content
	 */
	public static function content( Array $args = null ){

		// get content - returns array with key 'content' ##
		render\fields::define( 
			get\post::content( $args ) 
		);

	}



	

	public static function the_category( Array $args = null ) {

		// global arg validator ##
		if ( ! $args = render\args::prepare( $args ) ){ return false; }

		// get title - returns array with key 'title' ##
		$array = get\wp::the_category( $args );

        // return ##
		return render\method::prepare( $args, $array );

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

		$string = render\method::markup( $args['markup'], $array );

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
