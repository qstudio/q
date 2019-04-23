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
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

use q\q_theme\core\options as options;

if ( ! class_exists ( 'Q_Hook_WP_Head' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'wp_head', array ( 'Q_Hook_WP_Head', 'init' ), 0 );
    
    // Q_Hook_WP_Head Class
    class Q_Hook_WP_Head extends Q
    {

        
        /**
        * Creates a new instance.
        *
        * @wp-hook      init
        * @see          __construct()
        * @since        0.1
        * @return       void
        */
        public static function init() 
        {
            new self;
        }
        

        private function __construct()
        {
            
            // pre-fetch data ##
            #add_action( 'wp_head', 'q_prefetch', 1 );
            
            // remove WP version from RSS
            add_filter( 'the_generator', array ( $this, 'q_rss_version' ) );
            
            // remove pesky injected css for recent comments widget
            add_filter( 'wp_head', array ( $this, 'q_remove_wp_widget_recent_comments_style' ), 1 );

            // clean up comment styles in the head
            add_action( 'wp_head', array ( $this, 'q_remove_recent_comments_style' ), 0 );
            
            // simple SEO stuff #
            add_action( 'wp_head', array ( $this, 'q_simple_SEO' ) );

            // simple SEO stuff #
            add_action( 'wp_head', array ( $this, 'q_webmasters' ), 3 );

            // remove category feeds
            remove_action( 'wp_head', 'feed_links_extra', 3 );

            // remove post and comment feeds
            remove_action( 'wp_head', 'feed_links', 2 );

            // remove EditURI link
            remove_action( 'wp_head', 'rsd_link' );

            // remove windows live writer
            remove_action( 'wp_head', 'wlwmanifest_link' );

            // remove index link
            remove_action( 'wp_head', 'index_rel_link' );

            // remove previous link
            remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );

            // remove start link
            remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );

            // remove links for adjacent posts
            remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );

            // remove WP version
            remove_action( 'wp_head', 'wp_generator' );

            // remove WP version from css
            #add_filter( 'style_loader_src', array ( $this, 'q_remove_wp_ver_css_js' ), 9999 );

            // remove Wp version from scripts
            #add_filter( 'script_loader_src', array ( $this, 'q_remove_wp_ver_css_js' ), 9999 );

            // add favicon ##
            add_action( 'wp_head', array ( $this, 'q_favicon' ) ); // add to theme ##
            add_action( 'admin_head', array ( $this, 'q_favicon' ) ); // add to backend ##

            // google analytics tracking code - add just before </head> ##
            //add_action( 'wp_head','q_google_analytics', 1000 ); // add to backend ##
            #add_action( 'q_action_body_open', array ( $this, 'q_google_analytics' ), 0 );
            
            // add body classes ##
            add_filter( 'body_class', array ( $this, 'q_body_class' ), 1 );
            
        }
        
        
        /**
         * remove WP version from RSS
         */
        public function q_rss_version() { return ''; }

        
        
        /**
         * remove recent comments filter ##
         */
        public function q_remove_wp_widget_recent_comments_style() {
            if ( has_filter('wp_head', 'wp_widget_recent_comments_style') ) {
                remove_filter('wp_head', 'wp_widget_recent_comments_style' );
            }
        }


        /**
         * remove injected CSS from recent comments widget
         */
        public function q_remove_recent_comments_style() {
            global $wp_widget_factory;
            if ( isset( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'] ) ) {
                remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
            }
        }
        
        

        /* helper function to get parent term name */
        public function parent_cat_names( $sep = '|' ) {
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
        public function q_prefetch() {

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
         * @subpackage 4Trees
         * @since 0.1
         */
        public function q_simple_SEO( $length = 155, $echo = true )
		{
            
			$length = 155;
			global $post;
			
			#pr( $length, 'length' );
            
			// post found ##
            if ( ! isset ( $post ) ) { return false; }
            
            // assign ID ##
            $id = $post->ID;
            
            $meta_robots = ''; // nada ##

            if( is_category() || is_archive() ) {
                
                $meta_desc = ( category_description() )? category_description() : single_cat_title( '', false ).' '.__("category archive.","q_framework") ;

            } elseif( is_tag() ) {
                
                $meta_desc = single_tag_title( '', false).' '.__("tag archive.", "q_framework");

            } elseif( is_month() ) {
                
                $meta_desc = get_the_time('F, Y').' '.__("archive page.", "q_framework");

            } elseif ( is_search() ) {
                
                $meta_desc = esc_html(stripslashes($_GET['s'])).' '.__("search results.", "q_framework");

            } elseif ( is_404() ) {
                
                $meta_desc = __("404 - page not found.", "q_framework");
                $meta_robots = 'noindex,nofollow';

            } else { // normal page or post ##
                
                 // custom field not set - use excerpt ##
                if ( $meta_desc = get_post_meta( $id, "metadescription", true ) ) {
                    
					#pr( '1' );
                    $meta_desc = q_excerpt_from_id( get_the_ID(), $length, '', '' );
                    
                } else if ( $meta_desc = get_post_meta ( $id, 'template_meta_description', true ) ) {
                    
					#pr( '2' );
                    $meta_desc = get_post_meta ( $id, 'template_meta_description', true );
                    
                } else { 
                    
					#pr( '3' );
                    $meta_desc = q_excerpt_from_id( $id, $length );
                    
                }
            }
			
			#wp_die( $meta_desc );
			
            // fall-back ##
            if ( ! $meta_desc ) { $meta_desc = q_excerpt_from_id( $id, $length ); }

            // extra fall-back ##
            if ( !$meta_desc ) { $meta_desc = get_the_title( $id ); }

            // clean up ##
            $meta_desc = q_rip_tags($meta_desc);

            // replacements ##
            $meta_desc = str_replace( "\"", "'", $meta_desc );

            // keep it all to size ##
            $meta_desc = q_chop( $meta_desc, $length );

            // apply filters ##
            $meta_desc = apply_filters( 'q_meta_description', $meta_desc );

            // Q_Control::log( '$meta_desc: '.$meta_desc );

            // add required tag ##
            $meta_desc = '<meta name="description" content="'.$meta_desc.'" />' . "\n"; // this clears a line to make it neat in the html :) ##

            // apply filters ##
            $meta_robots = apply_filters( 'q_meta_robots', $meta_robots );

            // robots meta ##
            if ( '1' == get_option('blog_public') ) { // site public ##
                if ( $meta_robots ) {
                    $meta_robots = '    <meta name="robots" content="'.$meta_robots.'">' . "\n"; 
                }
            } else { // site private, so meta robots added already by WP ( general-template.php ) ##
                $meta_robots = '';
            }

            // compile ##
            $q_simple_SEO = $meta_desc.$meta_robots;
            
            // echo or return string ##
            if ( $echo === true ) { 
                echo $q_simple_SEO;
            } else {
                return $q_simple_SEO;
            }

        }


        /**
         * add webmaster verification meta to head ##
         */
        public function q_webmasters() {

            #global $q_options; // load framework options ##
        
            // instatiate Q_Options Class
            #$q_options_class = new Q_Options();

            // grab the options ##
            #$q_options = $q_options_class->options_get();

            #Q_Control::log( $q_options );
            
            $q_options = options::get();

            $webmasters = $q_options["google_webmasters"];

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
        public function q_remove_wp_ver_css_js( $src ) {
            if ( strpos( $src, 'ver=' ) )
                $src = remove_query_arg( 'ver', $src );
            return $src;
        }


        /**
         * favicon function ##
         * reference favicon.png in header if found in top directory of child or parent theme ##
         * include favicon.ico on IE if found ## 
         */
        public function q_favicon(){
            
            /*
            if ( file_exists( q_get_option("path_child").'favicon.png' ) ) { // load child over parent ##

        ?>
            <link rel="icon" type="image/png" href="<?php echo q_get_option("uri_child"); ?>favicon.png" /><!-- Major Browsers -->
            <!--[if IE]><link rel="SHORTCUT ICON" href="<?php echo q_get_option("uri_child"); ?>favicon.ico" /><![endif]--><!-- Internet Explorer-->
        <?php

            } else
            */

            if ( file_exists( q_get_option("path_parent").'favicon.png' ) ) { // load from parent ##

        ?>
            <link rel="icon" type="image/png" href="<?php echo q_get_option("uri_parent"); ?>favicon.png" /><!-- Major Browsers -->
            <!--[if IE]><link rel="SHORTCUT ICON" href="<?php echo q_get_option("uri_parent"); ?>favicon.ico" /><![endif]--><!-- Internet Explorer-->
        <?php 

             }
        }


        /**
         * Google Analytics tracking code ##
         */
        public function q_google_analytics() {
            
            #global $q_options; // load framework options ##
            
            // instatiate Q_Options Class
            #$q_options_class = new Q_Options();

            // grab the options ##
            #$q_options = $q_options_class->options_get();

            $q_options = options::get();

            #Q_Control::log( $q_options );
            
            $analytics = $q_options->google_analytics;

            if ( ! $analytics ) { return false; }
            
            if ( class_exists( 'q_theme' ) && method_exists( 'q_theme', 'the_analytics' ) ) {

                // print markup ##
                echo Q_Template::the_analytics();

            // which template file to use ( plugin or theme ) -- TODO ##
            } else {

                q_get_template_part( "templates/analytics.php" );    
            
            }
            
        }
        
        
        
        /*
         * add extra classes to html body tag ##
         */
        public function q_body_class( $classes ) {

            // browser classes ##
            global $post; // get $post object ##
            
            if ( $post ) { 
                
                $classes[] = 'depth-'.q_get_page_depth( $post->ID );  // page depth ##
				
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
    
}


