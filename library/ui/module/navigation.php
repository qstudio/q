<?php

namespace q\ui;

use q\core;
use q\core\helper as h;
use q\ui;
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

        // handheld navigation ##
        #if ( q_ui::device() == 'handheld' ) {

?>
    <div id="the-navigation" class="navigation background-solid-<?php #echo self::get_header_colour()->name; ?>">
        <ul class="the-navigation inner-wrapper">
<!--                <li class="toggle"><a href="#"><?php #self::the_text( array( 'string' => 'Sections' ) ); ?></a></li>    -->
<?php

        #} // handheld markup ##

        // loop over returned WP_Query object ##
        foreach ( $array as $item ) {

?>
            <li <?php echo $item->data; ?> class="<?php echo $item->li_class; ?>">
                <a href="<?php echo $item->permalink; ?>" class="<?php echo $item->class; ?>"><?php echo $item->title; ?></a>
            </li>
<?php

        }

        // handheld navigation ##
        #if ( q_ui::device() == 'handheld' ) {

?>
        </ul>
    </div>
<?php

        #}

    }




	/**
    * Get Pagination links
    *
    * @since       1.0.2
	* @return      String      HTML
	* @link	https://gist.github.com/mtx-z/f95af6cc6fb562eb1a1540ca715ed928
    */
	public static function pagination( $args = array(), $return = 'echo' ) {

		// grab array ##
        if ( ! $array = get\navigation::pagination( $args ) ) { 

			// h::log( 'No pagination...' );
            
            return false; 
        
		}
		
		// test ##
		// h::log( $array );

		// get config ##
		$config = core\config::get('pagination');

		// format page items ##
		$items = '';
		foreach ( $array as $page ) {

			$markup = $config['item']; // '<li class="%active-class%">%item%</li>' ##
			$row = [];
			// $row['class_link_item'] = $config['class_link_item'];
			$row['li_class'] = $config['li_class'];
			$row['item'] = str_replace( 'page-numbers', $config['class_link_item'], $page );
			$row['active-class'] = (strpos($page, 'current') !== false ? ' active' : '');

			// h::log( $row );

			$items .= ui\method::markup( $markup, $row );

		}

		// get wrapping markup ##
		$string = str_replace( '%content%', $items, $config['markup'] ) ;

		// echo ##
		if ( 'return' == $return ){ 
			
			return $string ;

		} else {

			echo $string;

		}

		// kick back ##
		return true;
		
	}




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
