<?php

namespace q\extension\search;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\search\render::__run();

class render extends extension\search {

	public static function __run()
  	{

		// add assets on get_header --> priorirty 10 ##
		\add_action( 'get_header', [ '\\q\\extension\\search\\enqueue', 'wp_enqueue_scripts' ], 10 );
		
		// add q_search jS callback ##        
        \add_action( 'wp_footer', [ get_class(), 'q_search_callback' ], 10 );

	}




	
    /**
    * Q_Search Results Markup
    *
    * @since    2.0.0
    * @return   String
    */
    public static function q_search_callback()
    {

?>
<script>
    function q_search_callback( $args ) {
 
        // console.log( 'doing q_search_work_callback' );

        // do lazy loading ##
        if ( typeof q_do_lazy === "function" ) {
			// console.log( 'LAZY..' );
			q_do_lazy();
		}
        
    }
</script>
<?php

    }



	  
	/**
	 * Render the search engine
	 *
	 * @since       0.1
	 * @return      HTML
	 */
	public static function ui(){

	// h::log( 'rendering...' );
	// h::log( method::properties( 'args' ) );

	$array = [];

    // let's check if there are any posts to search, defined on very high, loose terms... ##
    if ( method::has_posts() ) {

		// add inline JS to instatiate AJAX call -- returns <script> block ##
		$array['scripts'] = self::scripts();

		/*
?>
		<div id="q-search-content" class="row row mt-3">
<?php
*/

			// build filter navigation ##
			$array['filters'] = self::filters();

			// add AJAX section -- this might be empty on load state ##
			$array['results'] = self::results();

/*
?>
		</div>
<?php
*/

    } else {

		// h::log( 'has_posts returned zero' );

		// nothing to search :( ##
		$array['no_posts'] = self::no_posts();

	}

	// kick back ##
	return $array;
	
}




	/**
	 * Message to show when no posts available to search
	 * Message shown is controllable via optional ACF storage in admin
	 *
	 * @since       0.0.5
	 * @return      string      HTML
	 */
	public static function no_posts()
	{

    	// grab global $post;
		global $post;
		#pr( $post );

		// check for post_meta field containing string for message ##
		if ( $post && $post->q_search_no_results ) {

			#pr( 'Found string..' );
			$message = $post->q_search_no_results;

		} else {

			// allow message to be passed ##
			$message = method::properties( 'no_results' ) ;

		}

		ob_start();

	?>
    	<div class="row no-results"><?php echo $message; ?></div>
<?php

		return ob_get_clean();

	}



	/**
	 * Results Markup
	 *
	 * @since    2.0.0
	 * @return   String
	 */
  	public static function result()
  	{

		// check we can get a post object ##
        if ( ! $object = \q\get\post::object() ) { 
        
            h::log( 'e:>error getting q_search row..' );
            
            return false; 
            
		}
		
		// get template ##
		// $config = \q\core\config::get([ 'context' => 'extension', 'task' => 'search' ]);
		$handle = method::properties( 'src_handle' ) ?: 'medium' ;
		// h::log( 'handle: '.$handle );

        // check what we got back ##
        // h::log( $object );
        #h::log( $args['markup'] );

        // pre-format values in preperation for generic template markup ##

        // date needs to be in 'days ago' format ##
        #h::log( 'date: '. \get_the_time('U') );
        $post_date = 
            \wp_doing_ajax() ? 
            human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' ago' : 
            \get_the_time('U');

        // add author name and permalink ##
        $author_permalink = \get_author_posts_url( \get_the_author_meta( 'ID' ), \get_the_author_meta( 'user_nicename' ) );;
        $author_name = \get_the_author();

        // grab first category ##
        #h::log( \get_category_link( $object->category[0]->term_id ) );
		#h::log( 'term_id: '.$object->category[0]->term_id );
		$object->category = \get_the_category();
        $category_permalink = 
            ( isset( $object->category ) && is_array( $object->category ) ) ? 
            \esc_url( \get_category_link( $object->category[0]->term_id ) ) : 
            '#'; // dead ##
        $category_name = 
            ( isset( $object->category ) && is_array( $object->category ) ) ? 
            $object->category[0]->name : 
            \__( "Uncategorized" );

        // class ##
        $class = \is_sticky() ? 'is_sticky' : 'not_sticky' ;

		// pass to willow render template method ##
		echo \q\willow\render\template::partial([
			'context' 	=> 'extension', 
			'task' 		=> 'search',
			'markup'	=> 'result', // markup->property ##
			'return'	=> 'echo', // also defined in config ## 
			// array of data to include in template ##
			'data'		=> [
				'class'					=> $class,
				'post_permalink' 		=> \get_the_permalink(),
				'src'					=> \get_the_post_thumbnail_url( \get_the_ID(), $handle ),
				'post_title'			=> \get_the_title(),
				'post_excerpt'			=> \get_the_excerpt(),
				'post_date_human'		=> $post_date,
				'category_permalink'	=> $category_permalink,
				'category_name'			=> $category_name,
			],
		]);

		// h::log( $return );

		return true;

		/*

?>
	<div class="col-12 col-md-6 col-lg-4 ajax-loaded q-search-default">
		<a href="<?php \the_permalink(); ?>">
			<img class="fit card-img-top" alt="Open" src="<?php echo \get_the_post_thumbnail_url( \get_the_ID(), 'square' ); ?>" />
		</a>
		<div class="card-body">
			<h5 class="card-title"><a href="%permalink%" title="Read More"><?php \the_title();?></a></h5>
			<p class="card-text"><?php \the_excerpt(); ?></p>
			<p class="card-text">
				<small class="text-muted"><?php \the_date(); ?></small>
				<small class="text-muted">in <a href="{{ category_permalink }}" title="{{ category_name }}">{{ category_name }}</a> </small>    
			</p>
		</div>
	</div>
<?php
		*/

  	}



	/**
	 * Create HTML area to hold AJAX loaded content
	 *
	 * @since    2.0.0
	 * @return   String
	 */
	public static function results()
	{

		ob_start();

?>
    <div id="ajax-content" class="col-12">
      	<div id="q-search-results" class="<?php echo method::properties( 'results_class' ); ?>">
<?php

		// h::log( method::properties( 'control', 'array' ) );

		// run load query ##
		method::query( method::properties( 'control', 'array' ) );

?>
      	</div>
    </div>
<?php

	return ob_get_clean();

  }




	/**
	 * build list of terms to filter by
	 *
	 * @since       1.7.0
	 * @return      string      HTML for filter nav
	 * @TODO missing keyword, tag, country filters. search by filtering doesn't work
	 */
	protected static function filters()
	{

		ob_start();

		// check for passed values or merge defaults ##
		$post_type = array( method::properties( 'post_type' ) );
		$taxonomies = explode( ",", method::properties( 'taxonomies' ) );
		#h::log( $taxonomies );
		$table = method::properties( 'table' );
		$application = method::properties( 'application' );
		$device = method::properties( 'device' );
		// $class = method::properties( 'class' );
		$filter_type = method::properties( 'filter_type' );
		$hide_titles = method::properties( 'hide_titles' );
		// $filter_position = method::properties( 'filter_position' );
		$show_count = method::properties( 'show_count' );
		$show_input_text = method::properties( 'show_input_text' );

		// position the filters correctly ##
		// $position = $filter_position == 'vertical' ? 'vertical' : 'horizontal' ;

?>
		<form id="q-search-form" class="ajax-filters col-12">
			<div class="row">
<?php

				// text input ##
				echo self::filter_input();

				// check for user_meta filters ##
				echo self::user_meta();

				// select grid ##
				$grid = method::properties( 'grid_select' );

				// h::log( $grid );

				$queried_object = \get_queried_object();
				// h::log($taxonomies);

				if ( 
					$taxonomies 
					&& isset( $taxonomies[0] ) 
					&& $taxonomies[0] > '' 
				) {

					foreach( $taxonomies as $taxonomy ) {

						// clean up ##
						$taxonomy = trim( $taxonomy );

						// get tax ##
						if ( false === $get_taxonomy = method::get_taxonomy( $taxonomy ) ) {

							// h::log( 'skipping: '.$taxonomy );

							continue;

						}

						if ( 
							$filter_type == 'list'
							&& $hide_titles == 0 
						){

							echo \apply_filters( 'q/search/filter/title', "<h4>{$the_tax_name}</h4>" );

						}

						echo "<div class='".$grid."'>"; 
						echo "<div class='selector'>";
						// echo $taxonomy !== 'category' ? "<label>".$get_taxonomy["label"]."</label>" : '';
						echo "<select name='".$taxonomy."' class=\"form-control q-search-select filter-$taxonomy\">";
						
						// check for preselect option ##
						echo "<option selected value=\"\" class=\"default\">".$get_taxonomy["name"]."</option>";

						#wp_die(pr($get_taxonomy["terms"]));
						
						foreach( $get_taxonomy["terms"] as $term ) {

							echo "<option value=\"{$term->term_id}\" data-tax=\"$taxonomy={$term->term_id}\" data-slug=\"{$term->slug}\" >";

							echo "{$term->name}";

							if( $show_count == 1 ) {
								
								echo " ({$term->count})";
					
							}

							echo "</option>";

						}

						echo "</select>";
						echo "</div></div>";

					} // loop ##

				} // taxs set ##

?>
			</div>
			<div id="q-search" class="<?php echo method::properties( 'button_class' ); ?>">
				<div class="buttons col-12 text-center mb-3 mt-2">
					<div class="input">
						<input type="reset" id="reset" class="qs-button qs-reset" value="Reset Options">
					</div>
				</div>
			</div>
		</form>
<?php

		return ob_get_clean();

  	}




	public static function filter_input()
	{

		// is this shown ? ##
		$show_input_text = method::properties( 'show_input_text' );

		if ( ! $show_input_text ) {

			return false;

		}

		// filter grid ##
		$grid = method::properties( 'grid_input' );

		$markup = 
		'<div class="input text input-searcher '.$grid.'">
			<input type="text" value="" name="searcher" id="searcher" placeholder="Keyword" class="searcher filter-selected" />	
		</div>';

		// filter ##
		return \apply_filters( 'q/search/filter/input', $markup );
	
	}



	public static function user_meta()
	{

		// is this shown ? ##
		$user_meta = \apply_filters( 'q/search/user_meta', false );

		if ( 
			! $user_meta 
			|| ! is_array( $user_meta ) // should be passed as an array ##
		) {

			return false;

		}

		// filter grid ##
		$grid = method::properties( 'input' == $user_meta['input'] ? 'grid_input' : 'grid_select' );

		// we need to get all the options values to loop over and show ##
		$options = $user_meta['options'];

		if (
			! $options
			|| ! is_array( $options )
		) {

			h::log( 'No valid options passed to display' );

			return false;

		}

		$markup = 
			"<div class='{$grid}'> 
				<div class='selector'>
					<select name='user_meta' class='form-control q-search-select filter-user-meta'>
						<option selected value='' class='default'>Filter by ".$user_meta["label"]."</option>
						".self::select_options( [
							'markup' 		=> '<option value="%key%" data-tax="%field%=%key%">%value%</option>',
							'options'		=> $options,
							'args'			=> $user_meta,
							'filter'		=> 'user_meta' // for filter ##
						] )."
					</select>
				</div>
			</div>";

		// filter ##
		return \apply_filters( 'q/search/filter/user_meta', $markup );
	
	}



	public static function filter_select()
	{
	
		// filter grid ##
		$grid = method::properties( 'grid_select' );

		$markup = 
		"<div class='{$grid}'> 
		   <div class='selector'>
			   <select name='user_meta' class='form-control q-search-select filter-user-meta'>
				   <option selected value='' class='default'>Filter by ".$user_meta["label"]."</option>
				   ".self::select_options( [
					   'markup' 	=> '<option value="%key%" data-tax="%field%=%key%" >',
					   'options'	=> $options,
					   'args'		=> $user_meta,
					   'filter'		=> 'user_meta' // for filter ##
				   ] )."
			   </select>
		   </div
	   	</div>";

		// filter ##
		return \apply_filters( 'q/search/filter/select', $markup );

  	}



	public static function select_options( Array $args = null )
	{

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
			|| ! isset( $args['markup'] )
			|| ! isset( $args['options'] )
			|| ! is_array( $args['options'] )
			|| ! isset( $args['args'] )
			|| ! isset( $args['args']['field'] )
		) {

			h::log( 'Malformed data passed to method' );

			return false;

		}

		// start empty ##
		$string = '';

		// loop over each option, add markup ##
		foreach( $args['options'] as $key => $value ) {

			$string .= str_replace( [ '%key%', '%field%', '%value%' ], [ $key, $args['args']['field'], $value ], $args['markup'] );

		}

		// return filtered string ##
		return \apply_filters( 'q/search/select_options/'.$args['filter'], $string );

	}  



	/**
	 * Buid pagination
	 *
	 * @since       1.4.0
	 * @return      String      HTML for pagination
	 */
	public static function pagination( $total_posts, $posts_per_page, $posted )
	{

		// switch ( $posted["device"] ) {

		// 	case ( 'handheld' ) :

		// 		self::pagination_handheld( $total_posts, $posts_per_page, $posted );

		// 	break ;

		// 	case ( 'desktop' ) :
		// 	default :

		self::pagination_desktop( $total_posts, $posts_per_page, $posted );

		// 	break ;

		// }

	}





	/**
	 * handheld pagination
	 *
	 * @since       1.4.0
	 * @return      String      HTML for pagination
	 */
	public static function pagination_handheld( $total_posts, $posts_per_page )
	{

    // h::log( 'Loading Handheld Pagination..' );

?>
    <nav class="q-search-pagination col-12 mt-3 mb-5">
      	<div class="pagination-inner">
<?php

		if( $_POST && isset($_POST['paged']) && $_POST['paged'] > 1 ) {

          	$page_number = $_POST['paged'];

?>
			<a href='#' class='page-numbers pagelink-1 pagelink' rel="1"><span>&laquo; First</span></a>
			<a class="paginationNav page-numbers prev" rel="prev" href="#"><span>&lsaquo; <?php _e( "Previous", 'q-search' ); ?></span></a>
<?php

        } else {

          	$page_number = 1;

?>
			<a href='#' class='disabled page-numbers' rel=""><span>&laquo; First</span></a>
			<a class="disabled prev" rel="" href="#">&lsaquo; <?php _e( "Previous", 'q-search' ); ?></a>
<?php

        }

        // work out total number of pages ##
        $total_pages = floor( $total_posts / $posts_per_page );
        // h::log( 'Total Pages: '.$total_pages );

        // check if we need to print pagination ##
        if (
			// ( $posts_per_page * $page_number ) < $total_posts
			// && $posts_per_page < $total_posts
			$page_number >= $total_pages
        ) {

?>
			<a class="disabled page-numbers" rel="" href="#"><span><?php _e( "Next", 'q-search' ); ?> &rsaquo;</span></a>
			<a href='#' class='disabled page-numbers' rel=""><span>Last &raquo;</span></a>
<?php

        } else {

?>
			<a class="paginationNav page-numbers next" rel="next" href="#"><span><?php _e( "Next", 'q-search' ); ?> &rsaquo;</span></a>
			<a href='#' class='page-numbers pagelink-<?php echo $total_pages; ?> pagelink' rel="<?php echo $total_pages; ?>"><span>Last &raquo;</span></a>
<?php

        }

?>
        	<div class="clear"></div>
      	</div>
	</nav>
<?php

  	}



	/**
	 * desktop pagination
	 *
	 * @since       1.4.0
	 * @return      String      HTML for pagination
	 */
	public static function pagination_desktop( $total_posts, $posts_per_page )
	{

    // h::log( 'Loading Desktop Pagination..' );

?>
<div class="col-12">
	<nav class="row row justify-content-center mt-5 mb-5">
		<ul class="pagination">
<?php

        if( $_POST && isset($_POST['paged']) && $_POST['paged'] > 1 ) {

			$page_number = $_POST['paged'];

?>
			<li class="page-item"><a class="page-link paginationNav page-numbers prev" rel="prev" href="#"><span>&lsaquo;</span></a></li>
<?php

        } else {

          	$page_number = 1;

        }

?>
        	<!-- <span class="qs-pages page-numbers-wrapper"> -->
<?php

	#h::log( $posts_per_page );

	// get paging info ##
	$pagination = method::get_pagination( $total_posts, $posts_per_page, $page_number );
	#h::log( $pagination );

	// limit number of items shown on screen ##
	$max = 3;

	// work out how many filler links to allow in between next and back arrows ##
	if( $pagination['page_number'] < $max ) {

		// current page is lower than max pages allowed to be shown ##
		$sp = 1;

	} elseif ( $pagination['page_number'] >= ( $pagination['pages'] - floor( $max / 2 ) ) ) {

		// the current page is greater or equal to half the max number allowed to be shown ##
		// current = 1, total pages = 6 - 2 = 4
		// current = 5, total pages = 6 - 2 = 4 - $sp = 4
		$sp = $pagination['pages'] - $max + 1;

	} elseif( $pagination['page_number'] >= $max ) {

		// current page is equal or greater than max ##
		// 1 >= 3 = false
		// 5 >= 3 = true - $sp = 3
		$sp = $pagination['page_number'] - floor( $max / 2 );

	}

	#h::log( '$sp: '.$sp );

	// If the current page >= $max then show link to 1st page
	if ( $pagination['page_number'] >= $max ) {

?>
  				<li class="page-item"><a href='#' class='page-link page-numbers pagelink-1 pagelink' rel="1">1</a><a href='#' class="page-numbers dots">&#8230;</a></li><?php

	}

	// Loop though max number of pages shown and show links either side equal to $max / 2 -->
	for( $i = $sp; $i <= ($sp + $max -1); $i++ ) {

		// skip ##
		if( $i > $pagination['pages']) {

			continue;

		}

		// current ##
		if ( $pagination['page_number'] == $i ) {

?>
				<li class="page-item active"><span aria-current="page" class="page-link current"><?php echo $i; ?></span></li>
				<!-- <span aria-current="page" class="page-link current">1</span>	 -->
<?php

    	// normal ##
	  	} else {

?>
    			<li class="page-item"><a href='#' class="page-link page-numbers pagelink-<?php echo $i; ?> pagelink" rel="<?php echo $i; ?>"><?php echo $i; ?></a></li>
<?php

  		}

	}

	// If the current page is less than the last page minus $max pages divided by 2 ##
	if ( $pagination['page_number'] < ( $pagination['pages'] - floor( $max / 2 ) ) ) {

?>
  				<span class="page-numbers dots">&#8230;</span>
				<li class="page-item"><a href='#' class="page-link page-numbers pagelink-<?php echo $pagination['pages']; ?> pagelink" rel="<?php echo $pagination['pages']; ?>"><?php echo $pagination['pages']; ?></a></li>
<?php

	}

?>
        	<!-- </span> -->
<?php

		// check if we need to print pagination ##
		if ( ( $posts_per_page * $page_number ) < $total_posts && $posts_per_page < $total_posts ) {

?>
			<li class="page-item"><a class="page-link paginationNav page-numbers next" rel="next" href="#"><span>Next &rsaquo;</span></a></li>
<?php

        } // pagination check ##

?>
      	</ul>
	</nav>
</div>
<?php

	}



  /**
   * Add inline JS to search page
   *
   * @since       1.7.0
   * @param       array   $post_type
   * @param       string  $class
   * @param       string  $order
   * @param       string  $order_by
   */
  public static function scripts()
  {

    // grab the queried object ##
    $queried_object = \get_queried_object();

	// h::log( $queried_object );

    // get the page's current taxonomy to filter
    if( isset( $queried_object->term_id ) ) {

      	$queried_object_string = $queried_object->taxonomy."##".$queried_object->term_id;

    } else {

      	$queried_object_string = "qs_null";

    }

    // create nonce ##
	$nonce = \esc_js( \wp_create_nonce( 'q-search-nonce' ) );
	
	// REMOVED ##
	// filter_position:    method::properties( 'filter_position'); ##
	// h::log( 'd:>'.method::properties( 'callback' ) );

	ob_start();

?>
    <script type="text/javascript">

        // configure QS_Filters ##
        var QS_CONFIG = {
            ajaxurl:            '<?php echo \home_url( 'wp-admin/admin-ajax.php' ) ?>',
            table:              '<?php echo method::properties( 'table' ) ; ?>',
            callback:           '<?php echo method::properties( 'js_callback' ) ; ?>',
            application:        '<?php echo method::properties( 'application' ) ; ?>',
            device:             '<?php echo method::properties( 'device' ) ; ?>',
            post_type:          '<?php echo method::properties( 'post_type' ); ?>',
            posts_per_page:     '<?php echo (int)method::properties( 'posts_per_page' ); ?>',
            taxonomies:         '<?php echo str_replace( " ", "", method::properties( 'taxonomies' ) ); ?>',
            order:              '<?php echo method::properties( 'order' ); ?>',
            order_by:           '<?php echo method::properties( 'order_by' ); ?>',
            filter_type:        '<?php echo method::properties( 'filter_type' ); ?>',
			category_name:      '<?php echo method::properties( 'category_name') ; ?>',
        	author_name:       	'<?php echo method::properties( 'author_name' ) ; ?>',
        	tag:    			'<?php echo method::properties( 'tag' ) ; ?>',
            queried_object:     '<?php echo $queried_object_string; ?>',
            page_number:        1,
            nonce:              '<?php echo $nonce; ?>'
        };

	</script>
<?php

	return ob_get_clean();

  	}



	/**
	 * Count total returned posts
	 *
	 * @since   0.4
	 * @param   integer     $count
	 * @return  string      HTML
	 */
	public static function count_results( $count = 0 )
	{	

		// h::log( method::properties( 'results', 'array' ) );

		printf (
			'<h5 class="mb-5 col-12 q-search-count-results text-center" data-count="%d">%d %s</h5>'
			,   intval( $count )
			,   intval( $count )
			,   intval( $count ) > 1 ? method::properties( 'results', 'array' )[1] : method::properties( 'results', 'array' )[0]
		);

  	}



	/**
	 * Buid No Results
	 *
	 * @since       1.4.0
	 * @return      String      HTML for sad face :(
	 */
	public static function no_results( $string = null )
	{

		// allow message to be passed ##
		$message = ! is_null( $string ) ? $string : method::properties( 'no_results' ) ;

?>
    <div class="no-results text-center col-12 mt-0 mb-0">
		<img class="push-20" src="<?php echo h::get( "asset/css/images/extension/search/search-no-results.svg", 'return' ); ?>" />
		<h5 class='push-20'><?php echo $message; ?></h5>
		<div>Sorry, that filter combination returned no results.</div>
		<div>Please try different criteria or <a href="#" class="qs-reset">Clear all Filters</a>.</div>
    </div>
<?php

		return;
    	// exit; // stop running now ##... @TODO, this is a killer.. ##

	  }
	  

	 /**
	 * Buid Empty Loads
	 *
	 * @since       1.4.0
	 * @return      String      HTML for sad face :(
	 */
	public static function load_empty( $array = null )
	{

		// allow message to be passed ##
		$message = ! is_null( $array ) ? $array : method::properties( 'load_message', 'array' ) ;

?>
    <div class="no-results text-center col-12 mt-0 mb-0">
		<img class="push-20" src="<?php echo h::get( "asset/css/images/extension/search/search-no-results.svg", 'return' ); ?>" />
		<h5 class='push-20'><?php echo $message['title']; ?></h5>
		<div><?php echo $message['body']; ?></div>
    </div>
<?php

		return;
    	// exit; // stop running now ##... @TODO, this is a killer.. ##

  	}


}
