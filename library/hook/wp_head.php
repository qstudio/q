<?php

/**
 * WP_Head Function
 *
 * clean up things we don't want
 * add things we do want
 *
 * filters and actions ##
 *
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @since       0.1
 */

namespace q\hook;

use q\core;
use q\core\helper as h;
use q\strings;
use q\get;

// load it up ##
\q\hook\wp_head::run();

class wp_head extends \Q {

    public static function run(){

        // pre-fetch data ##
        #add_action( 'wp_head', 'q_prefetch', 1 );

        // remove WP version from RSS
        \add_filter( 'the_generator', array ( get_class(), 'rss_version' ) );

        // remove pesky injected css for recent comments widget
        \add_filter( 'wp_head', array ( get_class(), 'remove_wp_widget_recent_comments_style' ), 1 );

        // clean up comment styles in the head
        \add_action( 'wp_head', array ( get_class(), 'remove_recent_comments_style' ), 0 );

        // simple SEO stuff #
        \add_action( 'wp_head', array ( get_class(), 'simple_seo' ), 1 );

        // simple SEO stuff #
        // \add_action( 'wp_head', array ( get_class(), 'webmasters' ), 3 );

        // remove category feeds
        \remove_action( 'wp_head', 'feed_links_extra', 3 );

        // remove post and comment feeds
        \remove_action( 'wp_head', 'feed_links', 2 );

        // remove EditURI link
        \remove_action( 'wp_head', 'rsd_link' );

        // remove windows live writer
        \remove_action( 'wp_head', 'wlwmanifest_link' );

        // remove index link
        \remove_action( 'wp_head', 'index_rel_link' );

        // remove previous link
        \remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );

        // remove start link
        \remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );

        // remove links for adjacent posts
        \remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

        // remove WP version
        \remove_action( 'wp_head', 'wp_generator' );

        // remove WP version from css
        #add_filter( 'style_loader_src', array ( get_class(), 'remove_wp_ver_css_js' ), 9999 );

        // remove Wp version from scripts
        #add_filter( 'script_loader_src', array ( get_class(), 'remove_wp_ver_css_js' ), 9999 );

        // add favicon ##
        \add_action( 'wp_head', array ( get_class(), 'favicon' ), 9999999 ); // add to theme ##
        \add_action( 'admin_head', array ( get_class(), 'favicon' ), 9999999 ); // add to backend ##

        // filter meta title ##
		// \add_filter( 'wp_title', array ( get_class(), 'wp_title' ), 10, 2 );
		\add_filter( 'pre_get_document_title', array ( get_class(), 'pre_get_document_title' ), 10, 11 );

        // add body classes ##
		\add_filter( 'body_class', array ( get_class(), 'body_class' ), 15, 1 );
		
    }


    /**
     * remove WP version from RSS
     */
    public static function rss_version() { return ''; }



    /**
     * remove recent comments filter ##
     */
    public static function remove_wp_widget_recent_comments_style() {
        if ( \has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
            \remove_filter('wp_head', 'wp_widget_recent_comments_style' );
        }
    }


    /**
     * remove injected CSS from recent comments widget
     */
    public static function remove_recent_comments_style() {
        global $wp_widget_factory;
        if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
            \remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
        }
    }



    /* helper function to get parent term name */
    public static function parent_cat_names( $sep = '|' ) {
        if ( ! is_single() or array() === $categories = get_the_category() )
            return '';

        $parents = array ();

        foreach ( $categories as $category )
        {
            $parent = end( get_ancestors( $category->term_id, 'category' ) );

            if ( ! empty ( $parent ) )
                $top = get_category( $parent );
            else
                $top = $category;

            $parents[ $top->term_id ] = $top;
        }

        return esc_html( join( $sep, wp_list_pluck( $parents, 'name' ) ) );
    }


    /*
        * prefetch data ##
        * @TODO check if serving from cloudflare ##
        * @TODO get server CDN ##
        */
    public static function q_prefetch() {

        $home = get_option('home');
        $home = str_replace( 'http://', '', $home );

        echo '<link rel=dns-prefetch href="//ajax.cloudflare.com">';
        echo '<link rel=dns-prefetch href="//'.$home.'">';
        echo '<link rel=dns-prefetch href="//dns.'.$home.'">';

    }


    /**
     * Simple SEO function - add meta desc &
     * Filter the page meta description - based on follow options ##
     * special tag rules - home, category, search etc.
     * check for template_meta_description
     * check for excerpt
     * build description from page content ##
     *
     * @package WordPress
     * @subpackage Q
     * @since 0.1
     */
    public static function simple_seo( $length = 155, $echo = true ){

        $length = 155;
        global $post;

        #pr( $length, 'length' );

        // post found ##
        if ( ! isset ( $post ) ) { return false; }

        // assign ID ##
        $id = $post->ID;

        $meta_robots = ''; // nada ##

        if( \is_category() || \is_archive() ) {

            $meta_desc = ( \category_description() )? \category_description() : \single_cat_title( '', false ).' '.__("category archive.",self::text_domain) ;

        } elseif( \is_tag() ) {

            $meta_desc = \single_tag_title( '', false).' '.__("tag archive.", self::text_domain);

        } elseif( \is_month() ) {

            $meta_desc = \get_the_time('F, Y').' '.__("archive page.", self::text_domain);

        } elseif ( \is_search() ) {

            $meta_desc = \esc_html(stripslashes($_GET['s'])).' '.__("search results.", self::text_domain);

        } elseif ( \is_404() ) {

            $meta_desc = __("404 - page not found.", self::text_domain);
            $meta_robots = 'noindex,nofollow';

        } else { // normal page or post ##

                // custom field not set - use excerpt ##
            if ( $meta_desc = \get_post_meta( $id, "metadescription", true ) ) {

                #pr( '1' );
                $meta_desc =  \Q\willow\get\post::excerpt_from_id( \get_the_ID(), $length, '', '' );

            } else if ( $meta_desc = \get_post_meta ( $id, 'template_meta_description', true ) ) {

                #pr( '2' );
                $meta_desc = \get_post_meta ( $id, 'template_meta_description', true );

            } else {

                #pr( '3' );
                $meta_desc =  \Q\willow\get\post::excerpt_from_id( $id, $length );

            }
        }

        #wp_die( $meta_desc );

        // fall-back ##
        if ( ! $meta_desc ) { $meta_desc =  \Q\willow\get\post::excerpt_from_id( $id, $length ); }

        // extra fall-back ##
        if ( ! $meta_desc ) { $meta_desc = \get_the_title( $id ); }

        // clean up ##
        $meta_desc = \Q\willow\strings\method::rip_tags($meta_desc);

        // replacements ##
        $meta_desc = str_replace( "\"", "'", $meta_desc );

        // keep it all to size ##
        $meta_desc = \Q\willow\strings\method::chop( $meta_desc, $length );

        // apply filters ##
        $meta_desc = \apply_filters( 'q/simple_seo/meta_description', $meta_desc );

        // add required tag ##
        $meta_desc = '<meta name="description" content="'.$meta_desc.'">' . "\n"; // this clears a line to make it neat in the html :) ##

		// apply filters ##
        $meta_robots = \apply_filters( 'q/simple_seo/meta_robots', $meta_robots );

        // robots meta ##
        if ( '1' == \get_site_option('blog_public') ) { // site public ##
            if ( $meta_robots ) {
                $meta_robots = '    <meta name="robots" content="'.$meta_robots.'">' . "\n";
            }
        } else { // site private, so meta robots added already by WP ( general-template.php ) ##
            $meta_robots = '';
		}
		
        // compile ##
        $simple_seo = $meta_desc.$meta_robots;

        // echo or return string ##
        if ( $echo === true ) {

            echo $simple_seo;

        } else {

            return $simple_seo;

        }

    }


    /**
     * add webmaster verification meta to head ##
     */
    public static function webmasters() {

        #global $option; // load framework options ##

        // instatiate Q_Options Class
        #$option_class = new Q_Options();

        // grab the options ##
        #$option = $option_class->options_get();

        #Q_Control::log( $option );

        $option = core\option::get();

        $webmasters = $option["google_webmasters"];

        if ( $webmasters && strlen( $webmasters  ) > 1 ) {

?>
        <meta name="google-site-verification" content="<?php echo $webmasters; ?>" />
<?php

        }
    }


    /**
     * remove WP version from scripts
     *
     * @deprecated
     */
    public static function remove_wp_ver_css_js( $src )
    {

        if ( strpos( $src, 'ver=' ) ) {

            $src = \remove_query_arg( 'ver', $src );

        }

        return $src;

    }


    /**
     * favicon function ##
     * reference favicon.png in header if found in top directory of child or parent theme ##
     * include favicon.ico on IE if found ##
     */
    public static function favicon(){

        #if ( $file = h::get( 'favicon.png' ) ) { // load from parent ##

        // h::log( 'Adding favicon...' );

?>
        <link rel="icon" type="image/png" href="<?php echo \get_site_url( '1' ); ?>/favicon.png" /><!-- Major Browsers -->
        <!--[if IE]><link rel="SHORTCUT ICON" href="/favicon.ico" /><![endif]--><!-- Internet Explorer-->
<?php

           # }
    }



    /**
     * Google Analytics tracking code ##
	 *
	 * __deprecated
     */
	/*
    public static function google_analytics() {

        #global $option; // load framework options ##

        // instatiate Q_Options Class
        #$option_class = new Q_Options();

        // grab the options ##
        #$option = $option_class->options_get();

        $option = core\option::get();

        #Q_Control::log( $option );

        $analytics = $option->google_analytics;

        if ( ! $analytics ) { return false; }

        if ( class_exists( 'q_theme' ) && method_exists( 'q_theme', 'the_analytics' ) ) {

            // print markup ##
            echo Q_Template::the_analytics();

        // which template file to use ( plugin or theme ) -- TODO ##
        } else {

            q_get_template_part( "templates/analytics.php" );

        }

    }
	*/



    /**
     * Filters the page title appropriately depending on the current page
     * This function is attached to the 'pre_get_document_title' filter hook.
     *
     * @uses	get_bloginfo()
     * @uses	is_home()
     * @uses	is_front_page()
     *
     * @since       0.1
     */
    public static function pre_get_document_title() {

		// define seperator ##
		$sep = ' ~ ';

		// pull in some global goodies.. ## 
		global $page, $paged, $post;
		
		// start empty ##
		$page_title = '';

		if ( \is_front_page() ) {

			// $title = '';

			// add site description if not empty and on front page ##
			$title = \get_bloginfo( 'description' ).$sep ;

		} else {

			$get_post = new \Q\willow\get\post( willow() );
		
			$get_title = $get_post->title([ 'filter' => true ]);

			// h::log( $get_title );
			$title = $get_title['title'];

			// add sep ##
			$title = $title.$sep;

		}

		// h::log( 'e:>'.$title );
		
        if ( $post ) {

			// h::log( 'Adding $post data..' );

            // allow for custom title - via post meta "metatitle" ##
			$page_title = 
				\get_post_meta( $post->ID, "metatitle", true ) ? 
				\get_post_meta( $post->ID, "metatitle", true ).' '.$sep. ' ' : 
				$title ;

            // if this is a singular post - but not of type page or post add post type name as parent ##
            if (
                \is_singular( \get_post_type() )
                && \get_post_type() !== 'post'
                && \get_post_type() !== 'page'
            ) {

                if ( $obj = \get_post_type_object( \get_post_type() ) ) {

                    $page_title = $page_title.' '.$obj->labels->menu_name.' '.$sep.' ';

                }

            }

            // add parent page, if page ##
            if (
                $post->post_parent &&
                $post->post_type === 'page'
                && ! \is_search()
            ) {

                if ( $get_post_ancestor = \get_post_ancestors( $post->ID ) ) {

					// h::log( $get_post_ancestor );

                    $page_title = $page_title.' '.\get_the_title( $get_post_ancestor[0] ).' '.$sep.' ';

                }

            }

        }

        // if we're on a single category check if that page has a parent ##
        if ( \is_archive() ) {

            $term = \get_term_by( 'slug', \get_query_var( 'term' ), \get_query_var( 'taxonomy' ) );

            if (
                $term
                // && $term->parent > 0
            ) {

                // h::log( 'Archive title' );

                // just use the term name ##
                $page_title = $term->name.' '.$sep.' ';

                // // get parent name ##
                // $term_parent = \get_term_by( 'ID', $term->parent, \get_query_var( 'taxonomy' ) ) ;

                // if ( $term_parent && $term_parent->name ) {

                //     $page_title .= $term_parent->name.' '.$sep.' ';

                // }

            }

		}

		// go back to basics ##
		if ( ! $page_title ) { $page_title = $title.$sep; }

		// h::log( 'e:>'.$page_title );
		
        // compile ##
        $page_title = $page_title . \get_option( 'blogname' ); // with site name ##
        #$filtered_title = $page_title; // without site name ##

        // add paging number, if paged ##
        $page_title .= ( 2 <= $paged || 2 <= $page ) ? $sep . sprintf( __( 'Page %s' ), max( $paged, $page ) ) : '' ;

        // h::log( 'e:>'.$page_title );

        // filter ##
        $page_title = \apply_filters( 'q/hook/wp_head/wp_title', $page_title );

        // return title with tags stripped ( which are added by get_the_archive_title ) ##
        return strip_tags( $page_title );

    }




    /*
    * add extra classes to html body tag ##
    */
    public static function body_class( $classes ) {

        // browser classes ##
        global $post; // get $post object ##

        if ( $post ) {

            // $classes[] = 'depth-'.q_get_page_depth( $post->ID );  // page depth ##

            if (
                ! is_search()
                && ! is_404()
            ) {

                $classes[] = $post->post_type . '-' . $post->post_name; // posttype-slug ##
                $classes[] = $post->post_name; // post-slug ##
                $classes[] = str_replace( ".php", "", get_page_template_slug() ); // template-name ##

            }

        }

        return $classes; // return classes ##

    }

}
