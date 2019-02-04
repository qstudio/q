<?php

/**
 * Functions hooked to after_setup_theme action in WP
 * 
 * @link        http://codex.wordpress.org/Plugin_API/Action_Reference
 * @since       0.1
 * @author:     Q Studio
 * @URL:        http://qstudio.us/
 */

if ( ! class_exists ( 'Q_Hook_The_Post' ) ) 
{

    // instatiate class via WP admin_init hook ##
    add_action( 'the_post', array ( 'Q_Hook_The_Post', 'init' ), 1 );
    
    // Q_Hook_After_Setup_Theme Class
    class Q_Hook_The_Post extends Q
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
            
            if ( ! is_admin() ) {
            
                // clean up gallery output in wp ##
                add_filter( 'gallery_style', array ( $this, 'q_gallery_style' ) );

                // cleaning up random code around images
                add_filter( 'the_content', array ( $this, 'q_filter_ptags_on_images' ));

                // cleaning up excerpt
                add_filter( 'excerpt_more', array ( $this, 'q_excerpt_more' ));

                // custom excerpt more length ##
                #add_filter( 'get_the_excerpt', array ( $this, 'q_get_the_excerpt', 20 );

                // custom excerpt more length ##
                add_filter( 'excerpt_length', array ( $this, 'q_excerpt_length' ), 999 );

                // filter archives_link to highlight current date ##
                add_filter( 'get_archives_link', array ( $this, 'q_get_archives_link' ) );

                // filter content links to open with target="_blank"
                add_filter( 'the_content', array ( $this, 'q_content_linktarget' ) );

                // exclude featured image from gallery ##
                #add_filter('post_gallery', array ( $this, 'q_exclude_thumbnail_from_gallery' ), 10, 2);

                // add featured image if content has no inline images ##
                #add_filter( 'the_content', array ( $this, 'q_has_inline_image', 1 );

                // gforms anchor - if not using AJAX validation ##
                // add_filter( "gform_confirmation_anchor", '__return_false' );
                
                // Filter WP's the_time() function ##
                add_filter( 'the_time', array( $this, 'q_the_time' ) );
                add_filter( 'get_the_time', array( $this, 'q_the_time' ) );
                
            }
            
        }
        
        
        
        /**
         * scripts not to be minifed - by handle ##
         * http://betterwp.net/wordpress-plugins/bwp-minify/#advanced_customization
         */
        public function q_parent_minify_script_ignore($excluded) {

            $excluded = array( 'wpsocialite', 'socialite-lib', 'jquery-easing' );
            return $excluded;

        }

        
        /**
         * remove injected CSS from gallery
         */
        public function q_gallery_style($css) {
            return preg_replace("!<style type='text/css'>(.*?)</style>!s", '', $css);
        }

        
        //remove WP default gallery css ##
        #add_filter('gallery_style', create_function('$a', 'return "<div class=\'gallery\'>";'));


        /*
         * remove the p from around imgs (http://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/)
         */
        public function q_filter_ptags_on_images($content){
           return preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
        }


        /**
         * removes the annoying […] from the Read More link
         */
        public function q_excerpt_more( $more ) {
            global $post;
            return rtrim( $more,'[&hellip;]' );
        }


        /**
         * remove link from the excerpt ##
         */
        public function q_get_the_excerpt( $output ) { 
            return preg_replace('/<a[^>]+>Continue reading.*?<\/a>/i','',$output);
        }


        /**
         * custom excerpt length ##
         */
        public function q_excerpt_length( $length ) {
            return 27;
        }


        /* *
         * highlight active post achive date 
         */
        #add_filter('get_archives_link', 'q_get_archives_link');
        public function q_get_archives_link ( $link_html ) {
            global $wp;
            static $current_url;
            if ( empty( $current_url ) ) {
                $current_url = add_query_arg( $_SERVER['QUERY_STRING'], '', home_url( $wp->request ) );
            }
            if ( stristr( $link_html, $current_url ) !== false ) {
                $link_html = preg_replace( '/(<[^\s>]+)/', '\1 class="current-cat"', $link_html, 1 );
            }
            return $link_html;
        }


        /**
         * open external links in new window ##
         */
        #add_filter('the_content', 'q_content_linktarget');
        public function q_content_linktarget($content) {
            return preg_replace_callback('/<a[^>]+/', array ( $this, 'q_content_linktarget_callback' ), $content);
        }


        // link callback function ##
        public function q_content_linktarget_callback($matches) {
            $link = $matches[0];
            $site_link = get_bloginfo('url'); // TODO UPDATE ##

            if (strpos($link, 'target') === false) {
                $link = preg_replace("%(href=\S(?!$site_link))%i", 'target="_blank" $1', $link);

            } elseif (preg_match("%href=\S(?!$site_link)%i", $link)) {
                $link = preg_replace('/target=S(?!_blank)\S*/i', 'target="_blank"', $link);
            }
            return $link;
        }


        /**
         * exclude featured image from gallery ##
         * http://stackoverflow.com/questions/4337999/wordpress-exclude-the-post-thumbnail-from-gallery-shortcode
         */
        public function q_exclude_thumbnail_from_gallery($null, $attr) {
            if ( !$thumbnail_ID = get_post_thumbnail_id() ) {
                return $null; // no point carrying on if no thumbnail ID
            }
            // temporarily remove the filter, otherwise endless loop!
            remove_filter('post_gallery', 'q_exclude_thumbnail_from_gallery');

            // pop in our excluded thumbnail
            if (!isset($attr['exclude']) || empty($attr['exclude']))
                $attr['exclude'] = array($thumbnail_ID);
            elseif (is_array($attr['exclude']))
                $attr['exclude'][] = $thumbnail_ID;

            // now manually invoke the shortcode handler
            $gallery = gallery_shortcode($attr);

            // add the filter back
            add_filter('post_gallery', 'q_exclude_thumbnail_from_gallery', 10, 2);

            // return output to the calling instance of gallery_shortcode()
            return $gallery;

        }


       /** 
        * Better Time Diff function
        * 
        * @link     http://www.jasonbobich.com/wordpress/a-better-way-to-add-time-ago-to-your-wordpress-theme/
        * @since    1.5.1
        */
        public function q_the_time() 
        {

            global $post;

            $date = get_post_time('G', true, $post);

            // Array of time period chunks
            $chunks = array(
                array( 60 * 60 * 24 * 365 , __( 'year', 'q-textdomain' ), __( 'years', 'q-textdomain' ) ),
                array( 60 * 60 * 24 * 30 , __( 'month', 'q-textdomain' ), __( 'months', 'q-textdomain' ) ),
                array( 60 * 60 * 24 * 7, __( 'week', 'q-textdomain' ), __( 'weeks', 'q-textdomain' ) ),
                array( 60 * 60 * 24 , __( 'day', 'q-textdomain' ), __( 'days', 'q-textdomain' ) ),
                array( 60 * 60 , __( 'hour', 'q-textdomain' ), __( 'hours', 'q-textdomain' ) ),
                array( 60 , __( 'minute', 'q-textdomain' ), __( 'minutes', 'q-textdomain' ) ),
                array( 1, __( 'second', 'q-textdomain' ), __( 'seconds', 'q-textdomain' ) )
            );

            if ( $date && !is_numeric( $date ) ) {
                #pr($date);
                $time_chunks = explode( ':', str_replace( ' ', ':', $date ) );
                #echo 'TIME: '.pr($time_chunks);
                $date_chunks = explode( '-', str_replace( ' ', '-', $date ) );
                #echo 'DATE: '.pr($date_chunks);
                $date = gmmktime( (int)$time_chunks[1], (int)$time_chunks[2], (int)$time_chunks[3], (int)$date_chunks[1], (int)$date_chunks[2], (int)$date_chunks[0] );
            }

            $current_time = current_time( 'mysql', $gmt = 0 );
            $newer_date = strtotime( $current_time );

            // Difference in seconds
            $since = $newer_date - $date;

            // Something went wrong with date calculation and we ended up with a negative date.
            if ( 0 > $since )
                return __( 'sometime', 'q-textdomain' );

            /**
             * We only want to output one chunks of time here, eg:
             * x years
             * xx months
             * so there's only one bit of calculation below:
             */

            //Step one: the first chunk
            for ( $i = 0, $j = count($chunks); $i < $j; $i++) {
                $seconds = $chunks[$i][0];

                // Finding the biggest chunk (if the chunk fits, break)
                if ( ( $count = floor($since / $seconds) ) != 0 )
                    break;
            }

            // Set output var
            $output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];


            if ( !(int)trim($output) ){
                $output = '0 ' . __( 'seconds', 'q-textdomain' );
            }

            $output .= __(' ago', 'q-textdomain');

            return $output;
            
        }
        
        
        
        /**
        * remove the gallery shortcode from the content
        * 
        * @since       1.1.0
        * @example     call using -- add_filter( 'the_content', 'q_remove_gallery_shortcode', 1 );
        */
        public function q_remove_gallery_shortcode( $content ) 
        {

            $expr = '/\[gallery(.*?)\]/i';
            return ("" . preg_replace( $expr, '', $content)); // deletes all existing gallery shortcodes

        }
        

    }


}

