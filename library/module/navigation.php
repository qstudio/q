<?php

namespace q\module;

use q\core;
use q\core\helper as h;
use q\ui;
use q\render;
use q\get;

class navigation extends \Q {

    /**
    * Build Sub Navigation
    *
    * @since       1.0.5
    * @return      string   HTML
    */
    public static function siblings( $args = array() )
    {

        // try and data, or kick back false ##
       	if ( ! $array = get\navigation::siblings( $args ) ) { return false; }

?>
    <div id="the-navigation" class="navigation background-solid-<?php #echo self::get_header_colour()->name; ?>">
        <ul class="the-navigation inner-wrapper">
<!--                <li class="toggle"><a href="#"><?php #self::the_text( array( 'string' => 'Sections' ) ); ?></a></li>    -->
<?php

        // loop over returned WP_Query object ##
        foreach ( $array as $item ) {

?>
            <li <?php echo $item->data; ?> class="<?php echo $item->li_class; ?>">
                <a href="<?php echo $item->permalink; ?>" class="<?php echo $item->class; ?>"><?php echo $item->title; ?></a>
            </li>
<?php

        }

?>
        </ul>
    </div>
<?php

    }




	// /**
    // * Build Sub Navigation from a specified taxonomy terms
    // *
    // * @since       4.0.0
    // * @return      boolean|string   HTML
    // */
    // public static function terms( $args = null )
    // {

	// 	// global arg validator ##
	// 	if ( ! $args = render\args::prepare( $args ) ){ return false; }

    //     // try and get data, or kick back false ##
	// 	if ( ! $array = get\taxonomy::terms( $args ) ) { 
		
	// 		// h::log('e:>kicked here..');

	// 		return false; 
		
	// 	}
		
	// 	// h::log( $array );

	// 	// return ##
	// 	return render\method::prepare( $args, $array );

    // }




	// /**
    // * Get Pagination links
    // *
    // * @since       1.0.2
	// * @return      String      HTML
	// * @link	https://gist.github.com/mtx-z/f95af6cc6fb562eb1a1540ca715ed928
    // */
	// public static function pagination( $args = [], $return = 'echo' ) {

	// 	// grab array ##
	// 	// h::log( $args );
	// 	// $args['context'] = 'ui'; // hack for now ##
	// 	// $args['task'] = 'pagination'; // hack for now ##
	// 	h::log( 't:>this needs to move to Q modules.. and all the rest.. we should note hope to call modules in parent from Q, only the other way' );
    //     if ( ! $array = get\navigation::pagination( $args ) ) { 

	// 		// h::log( 'No pagination...' );
            
    //         return false; 
        
	// 	}
		
	// 	// test ##
	// 	// h::log( $array );

	// 	// get config ##
	// 	$config = core\config::get([ 'context' => 'navigation', 'task' => 'pagination' ]);

	// 	// format page items ##
	// 	$items = [];
	// 	// $markup = $config['markup']['template']; // '<li class="%active-class%">%item%</li>' ##
	// 	$i = 0;

	// 	foreach ( $array as $page ) {

	// 		// $row['class_link_item'] = $config['class_link_item'];
	// 		$items[$i]['li_class'] = $config['li_class'];
	// 		$items[$i]['item'] = str_replace( 'page-numbers', $config['class_link_item'], $page );
	// 		$items[$i]['active-class'] = (strpos($page, 'current') !== false ? ' active' : '');

	// 		// iterate ##
	// 		$i ++;

	// 	}

	// 	// markup array ##
	// 	$string = render\method::markup( $config['markup']['template'], $items, $config['markup'] );

	// 	// echo ##
	// 	if ( 'return' == $return ){ 
			
	// 		return $string ;

	// 	} else {

	// 		echo $string;

	// 	}

	// 	// kick back ##
	// 	return true;
		
	// }




    /**
    * Get Next Back links
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


}
