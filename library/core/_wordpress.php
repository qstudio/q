<?php

namespace q\core;

// use q\core\core as core;
use q\core\helper as helper;
use q\theme\template as template;
use q\theme\ui as ui;
use q\controller\generic as generic;

// Q Theme ##
// use q\theme\theme\ui\generic as theme;

// load it up ##
#\q\core\wordpress::run();

class wordpress extends \Q {

    public static function run()
    {


    }


    /**
     * Check if a plugin is active
     * 
     * @since       2.0.0
     * @return      Boolean
     */
    public static function plugin_is_active( $plugin ) 
    {
        
        return in_array( $plugin, (array) \get_site_option( 'active_plugins', [] ) );
    
    }


    /**
     * Save a value to the options table, either updating or creating a new key
     * 
     * @since       2.0.0
     * @return      Void
     */
    public static function add_update_option( $option_name, $new_value, $deprecated = ' ', $autoload = 'no' ) 
    {
    
        if ( \get_site_option( $option_name ) != $new_value ) {

            \update_site_option( $option_name, $new_value );

        } else {

            \add_site_option( $option_name, $new_value, $deprecated, $autoload );

        }
    
    }


    /**
    * Get Q Plugin data
    *
    * @return   Object
    * @since    0.3
    */
    public static function plugin_data( $refresh = false ){

        if ( $refresh ) {

            #echo 'refrshing stored framework data<br />'; ##
            \delete_site_option( 'q_plugin_data' ); // delete option ##

        }

        if ( ! $array = \get_site_option( 'q_plugin_data' ) ) {

            $array = array (
                'version'       => \Q::version
            );

            if ( $array ) {

                self::add_update_option( 'q_plugin_data', $array, '', 'yes' );

            }

        }

        return core::array_to_object( $array );

    }



    /**
    * Get installed theme data
    *
    * @return  Object
    * @since   0.3
    */
    public static function theme_data( $refresh = false )
    {

       if ( $refresh ) {

           #echo 'refrshing stored theme data<br />'; ##
           \delete_site_option( 'q_theme_data' ); // delete option ##

       }

       // declare global variable ##
       global $q_theme_data;

       $array = \get_site_option( 'q_theme_data' );

       if ( ! \get_site_option( 'q_theme_data' ) ) {

           #echo 'stored theme option empty<br />';
           #$array = @file_get_contents( q_get_option("uri_parent")."library/version/");

           if( function_exists( 'wp_get_theme' ) ) {
               $array = \wp_get_theme( \get_site_option( 'template' ));
               #$theme_version = $theme_data->Version;
           } else {
               $array = \get_theme_data( \get_template_directory() . '/style.css');
               #$theme_version = $theme_data['Version'];
           }
           #$theme_base = get_option('template');

           if ( $array ) {

               self::add_update_option( 'q_theme_data', $array, '', 'yes' );
               #echo 'stored fresh theme data<br />';

           }

       }

       return core::array_to_object( $array );

    }



    /**
     * Check if an attached file exists
     *
     * @since       1.6.3
     * @return      boolean
     */
    public static function attachment_exists( $id = null )
    {

        // sanity ##
        if ( is_null ( $id ) ) {

            return false;

        }

        // get attachment path ##
        if ( $file = \get_attached_file( $id ) ) {

            if ( file_exists( $file ) ) {

                return true;

            }

        }

        // nothng cooking ##
        return false;

    }



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

        // self::log( $args );

        // let's try and get a $post from the passed $args ##
        if ( ! is_null ( $args ) && isset( $args ) ) {

            if ( is_array( $args ) && isset( $args["post"] ) ) {

                $post = $args["post"];

            } else if ( is_object ( $args ) && isset ( $args->post ) ) {

                $post = $args->post;

            } else if ( is_integer( $args ) ) {

                $post = $args;

            }

        }

        // self::log( $post );

        // first let's see if anything was set ##
        if ( isset ( $post ) ) {

            if ( ! is_object ( $post ) && is_int( $post ) ) {

                if ( $object = \get_post( $post ) ) {

                    // self::log( 'got post: '.$post );

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
     * Force Post ID based on pased arguments, or return false to keep property null
     *
     * @since       1.0.7
     * @return      Mixed       Int Post ID or void
     */
    public static function set_force_post( $args = array() )
    {

        // grab global post - or kick back ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$set_force_post );

        // if we're requesting the parent - grab that ##
        if ( $args->post_parent === true && $the_post->post_parent ) {

            return self::$force_post = get_post( $the_post->post_parent );

        }

        // nothing cooking ##
        return false;

    }



    /**
     * Open .content HTML
     *
     * @since       1.0.2
     * @return      string   HTML
     */
    public static function get_content_open( $args = array() ) 
    {

        // grab global post - or kick back ##
        if ( ! $the_post = self::the_post( $args ) ) { 
            
            // helper::log( 'Cannot find post...' );

            // return false; 
        
        }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = \wp_parse_args( $args, \q_theme::$the_content_open );
        // $args = ( object )$args_array;

        // helper::log( $args );

        // set-up new array ##
        $array = array();

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

            // helper::log( 'Adding passed class..' );

            if ( is_array( $args['class'] ) ) {
                
                $args['class'] = implode( array_filter( $args['class'] ), ' ' ) ;

            } 

            // add it in ##
            array_push( $array, $args['class'] );

        }

        // helper::log( $array );

        // kick it back ##
        return $array;

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
    public static function get_post_with_title_like( $title = null, $method = 'get_col', $columns = array ( 'ID' ) )
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
     * Get Main Posts Loop
     *
     * @since       1.0.2
     */
    public static function get_posts( $args = array() )
    {

        // helper::log( $args );

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args_array = \wp_parse_args( $args, \is_search() ? \q_theme::$the_search : \q_theme::$the_posts );
        $args = ( object )$args_array;
		// helper::log( $args );
		
		// we need $args->controller and $args->method for this to work ##
		$args->controller = "\\q\\theme\\theme\\controller\\{$args->template}";
		$args->method = "the_{$args->template}_loop";

        // pagination ##
        $paged = \get_query_var( 'paged' ) ? \get_query_var( 'paged' ) : 1 ;

        // args ##
        $posts_args = array (
            'posts_per_page'    => $args->limit,
            'paged'             => $paged
        );

        // merge in global $wp_query variables ? ( required for archive pages ) ##
        if ( isset( $args->query_vars ) ) {

            // grab all global wp_query args ##
            global $wp_query;

            // merge all args together ##
            $posts_args = array_merge( $wp_query->query_vars, $posts_args );

            // helper::log( array( 'added query vars' => $posts_args ) );

        }

        // merge in global $wp_query variables ? ( required for archive pages ) ##
        if ( isset( $args->search ) ) {

            // helper::log( 'searching...' );

            $posts_args['post_type'] = isset( $args->post_type ) ? $args->post_type : 'any' ;
            #$posts_args['posts_per_page'] = 100; // get them all ##

        }

        // helper::log( $posts_args );

        // set-up main query ##
        $q_query = new \WP_Query( $posts_args );

        // helper::log( $q_query->request );
        // helper::log( $q_query->found_posts );
        // helper::log( $q_query->post_count );

        // weird WPE hack - to reduce the returned array to the size of $args->limit ##
        if ( -1 != $args->limit && $q_query->post_count > $args->limit ) {

            // helper::log( "splicing.." );
            $get_posts = array_slice( $q_query->posts, 0, $args->limit, true );

        } else {

            $get_posts = $q_query->posts;

        }

        // self::log( count( $q_query->posts ) );
        // self::log( count( $get_posts ) );

        if ( 
            $q_query->posts 
            && count( $get_posts ) > 0
        ) {

            // open wrapping tag ##
            ui::get_tag( $args->tag, array ( 'posts', $args->class ) );

            // total ##
            if ( isset( $args->total ) ) {

                echo str_replace( '%total%', $q_query->found_posts, $args->total );

            }

            // loop ##
            foreach ( $get_posts as $post ) {

                // iterate the post loop ##
                \setup_postdata( $post );

                // helper::log( $post->ID );

                // add post ID to passed args ##
                $args_array['post'] = $post->ID;

                // check if method exists in 'q_theme' ##
                if (
                    method_exists( $args->controller, $args->method )
                    // && is_callable( array( "\q\theme\theme\view\{$args->template}\{$args->template}", "the_{$args->template}_loop" ) )
                ) {

                    #pr( $args_array );

                    // call template method ##
                    call_user_func (
                            array( $args->controller, $args->method )
                        ,   (array)$args_array
                    );

                } else {

                    helper::log ( "Method Missing : {$args->controller}::{$args->method}" );

                }

                // tidy up ##
                \wp_reset_postdata();

            }

            // close wrapping tag ##
            ui::get_tag( $args->tag, '', 'close' );

            // get sidebar ##
            if ( $args->sidebar ) {
                
                \q\controller\navigation::the_sidebar();

            }

            // pagination ##
            
            if ( isset( $args->pagination ) ) {

                // self::log( 'Adding pagination..' );
                
                \q\controller\navigation::the_pagination([
                    'posts_per_page'	=> $posts_args['posts_per_page'],
                    'post_count'		=> $q_query->found_posts
                ]);

            }

        } else {

            // nothing found ##
            \q\theme\theme\view\fourzerofour\fourzerofour::render();

        }

    }



    /**
     * Get object with loop variables
     *
     * @since       1.0.4
     * @return      Object      each property holds a loop variable
     */
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
        $object->title = ui::chop( \get_the_title( $the_post->ID ), $args->title_length );

        // get the excerpt ##
        $object->excerpt = self::excerpt_from_id( $the_post->ID, $args->excerpt_length );

        // kick it back ##
        return $object;

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
    public static function excerpt_from_id( $post, $length = 155, $tags = null, $extra = '&hellip;' )
    {

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

        return \apply_filters( 'q/wordpress/excerpt_from_id', $the_excerpt );

    }




    /**
     * Get Post Loop
     *
     * @since       1.0.2
     */
    public static function get_post_loop( $args = array() )
    {

        #pr( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_posts );

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
            $handle = is_array( $args->handle ) ? $args->handle[helper::get_device()] : $args->handle ;

            // show small image, linking to larger image ##
			$img_src = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), $args->handle ); 
			
			// helper::log( $img_src );

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

            $src_array = \wp_get_attachment_image_src( $src, $args->handle[helper::get_device()] );
            $object->src = $src_array[0];

		}
		*/

        // backup ##
        if ( ! $has_src ) {

            $object->src = $args->holder[helper::get_device()];

        }

        // content ##
        $object->excerpt = 
            self::excerpt_from_id( $the_post->ID, $args->length ) ? 
            self::excerpt_from_id( $the_post->ID, $args->length ) : 
            \get_bloginfo( 'description' ) ;

        // content ##
        $object->content = \apply_filters( 'q/wordpress/get_post_loop', $the_post->post_content );

        // date ##
        $object->date = \get_the_date( $args->date_format, $the_post->ID ); 

        // kick back post loop object ##
        return $object;

    }




    /**
     * Get single page data
     *
     * @since   1.6.2
     * @return  Mixed boolean false or Object
     */
    public static function get_page_content( $args = array() )
    {

        #pr( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_page );

        // build a new object ##
        $object = new \stdClass;

        #self::log( $post->ID );

        // ID ##
        $object->ID = $the_post->ID;

        // title ##
        $object->title = \get_the_title( $the_post->ID );

        // permalink ##
        $object->permalink = \get_permalink( $the_post->ID );

        // header image ##
        if ( \has_post_thumbnail( $the_post->ID ) ) {

            // grab array ##
            $img_src = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), $args->handle ); // @todo - device specific handle ##
            $object->src = $img_src[0]; // take first array item ##

        // backup ##
        } else {

            $object->src = ''; #@todo... helper::get( 'images/holder/'.$args->holder.'.png', false, false, false ); // @todo - device specific handle ##

        }

        // excerpt ##
        $object->excerpt = self::excerpt_from_id( $the_post->ID, 200 ) ? self::excerpt_from_id( $the_post->ID, 200 ) : \get_bloginfo( 'description' ) ; // @todo, change fallback ##

        // helper::log( $the_post );
        // helper::log( \get_post_field( 'post_content', $the_post->ID ) );

        // content ##
        $object->content = \apply_filters( 'q/wordpress/get_page_content', $the_post->post_content );

        // is the form on this program destination active ##
        $object->form_switch = \get_field( 'form_switch', $the_post->ID ) ? \get_field( 'form_switch', $the_post->ID ) : '0' ;

        // kick back object ##
        return $object;

    }



    /**
     * Check for a return post thumbnail images and exif-data baed on passed settings ##
     *
     */
    public static function get_post_thumbnail( $args = array() )
    {

        // test incoming args ##
        // self::log( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post() ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_post_thumbnail );

        #pr( $args );

        // set-up a new object ##
        $object = new \stdClass;

        if ( ! \has_post_thumbnail( $the_post->ID ) ) { return false; }

        // self::log( 'Handle: '.$args->handle[self::get_device()] );

        // show small image, linking to larger image ##
        $object->src = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), $args->handle[helper::get_device()] );
        $img_alt = \get_post_meta( \get_post_thumbnail_id( $the_post->ID ), '_wp_attachment_image_alt', true);
        $object->alt = ( $img_alt ? $img_alt : \get_the_title() ) ;

        // image found ? ##
        if ( ! $object->src ) { return false; }

        // kick back object ##
        return $object;

    }


    /**
     * Get Sibling pages
     *
     * @since       1.0.1
     * @return      string       HTML Menu
     */
    public static function get_navigation( $args = array() )
    {

        // get $the_post - allows for post_forcing ##
        // move global post to a new variable, for later use ##
        if ( ! $global_post = self::$force_post ? self::$force_post : \Q::the_post() ) { return false; }

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
        if ( helper::get_device() == 'desktop' ) {

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
        $posts = \apply_filters( 'q/wordpress/get_navigation', $posts );

        // return object ##
        return $posts;

    }



    /**
     * get nav_menu based on parent page slug
     *
     * @since       1.3.3
     * @return      string       HTML Menu
     */
    public static function get_nav_menu( $args = array() )
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
     * Get Sibling pages and return them in a flexible "landing" format
     *
     * @since       1.3.0
     * @return      string       HTML Menu
     * @todo        Add exception to block certain pages from showing - "Hide_landing = true"
     */
    public static function get_landing( $args = array() )
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
     * Check if a page has children
     *
     * @since       1.3.0
     * @param       integer         $post_id
     * @return      boolean
     */
    public static function has_children( $post_id = null )
    {

        // nothing to do here ##
        if ( is_null ( $post_id ) ) { return false; }

        // meta query to allow for inclusion and exclusion of certain posts / pages ##
        $meta_query =
                array(
                    array(
                        'key'       => 'program_sub_group',
                        'value'     => '',
                        'compare'   => '='
                    )
                );

        // query for child or sibling's post ##
        $wp_args = array(
            'post_type'         => 'page',
            'orderby'           => 'menu_order',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
            'meta_query'        => $meta_query,
        );

        #pr( $wp_args );

        $object = new \WP_Query( $wp_args );

        // nothing found - why? ##
        if ( 0 === $object->post_count ) { return false; }

        // get children ##
        $children = \get_pages(
            array(
                'child_of'      => $post_id,
                'meta_key'      => '',
                'meta_value'    => '',
            )
        );

        // count 'em ##
        if( count( $children ) == 0 ) {

            // No children ##
            return false;

        } else {

            // Has Children ##
            return true;

        }

    }



    /**
     * Build HTML WordPress Gallery
     *
     * @since       1.0.0
     * @return      string   HTML
     */
    public static function get_gallery( $args = array() )
    {

        // test incoming args ##
        #pr( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_gallery );

        // add post ID, if not passed ##
        $args->post = isset ( $args->post ) ? $args->post : $the_post->ID ;

        // test compilled arguments ##
        #pr( $args );

        // empty gallery ##
        $gallery = false;

        // define gallery source and grab images ##
        if ( isset( $args->post_meta ) ) {

            // built using new ACF field type ##
            if ( isset( $args->acf ) ) {

                $gallery = \get_field( $args->post_meta, $args->post );
                #pr( $gallery );

            } else if ( $post_meta = \get_post_meta( $args->post, $args->post_meta, true ) ) {

                #pr( $post_meta );

                $gallery = q_get_gallery_images( $args->post, $args->img_handle, $args->limit, $post_meta );

            }

        } else {

            $gallery = q_get_gallery_images( $args->post, $args->img_handle, $args->limit );

        }

        // test if we got any images ##
        if ( ! $gallery  ) { return false; }

        // close content area ##
        if ( $args->layout == 'full_width' ) theme::the_content_close();

        // open wrapping tag ##
        ui::get_tag( $args->tag, array ( $args->class ) );

        // test the gallery array ##
        #pr( $gallery );

        // loop over gallery ##
        foreach ( $gallery as $image ) {

            // toggle img / src depending on type ##
            $img_src = isset( $args->acf ) ? $image["sizes"]["{$args->img_handle}"] : $image['src'] ;

            // tag_node + class ##
            ui::get_tag( $args->tag_node, array ( $args->class_node ) );

?>
                <img src="<?php echo $img_src; ?>" />
<?php

            // tag_node + class ##
            ui::get_tag( $args->tag_node, '', 'close' );

        }

        // close wrapping tag ##
        ui::get_tag( $args->tag, '', 'close' );

        // reopen content area ##
        if ( $args->layout == 'full_width' ) theme::the_content_open();

    }



    /**
     * Check if a post has a gallery of images ( more than one ) or a post image
     *
     * @since       1.3.2
     * @return      String      HTML for image or gallery
     */
    public static function get_gallery_or_image( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_gallery_or_image );

        // add post ID, if not passed ##
        $args->post = isset ( $args->post ) ? $args->post : $the_post->ID ;

        // empty gallery ##
        $gallery = false;

        // define gallery source and grab images ##
        if ( isset( $args->post_meta ) ) {

            // built using new ACF field type ##
            if ( isset( $args->acf ) ) {

                $gallery = \get_field( $args->post_meta, $args->post );
                #pr( $gallery );

            } else if ( $post_meta = \get_post_meta( $args->post, $args->post_meta, true ) ) {

                #pr( $post_meta );

                $gallery = self::get_gallery_images( $args->post, $args->img_handle, $args->limit, $post_meta );

            }

        } else {

            $gallery = self::get_gallery_images( $args->post, $args->img_handle, $args->limit );

        }

        // build it out ##
        if ( $gallery && is_array ( $gallery ) ) {

            // close content area ##
            if ( $args->layout == 'full_width' ) theme::the_content_close();

            // open wrapping tag ##
            ui::get_tag( $args->tag, array ( $args->class ) );

            // test the gallery array ##
            #pr( $gallery );

            // loop over gallery ##
            foreach ( $gallery as $image ) {

                // toggle img / src depending on type ##
                $img_src = isset( $args->acf ) ? 'url' : 'src' ;

                // tag_node + class ##
                ui::get_tag( $args->tag_node, array ( $args->class_node ) );

?>
                    <img src="<?php echo $image[$img_src]; ?>" />
<?php

                // tag_node + class ##
                ui::get_tag( $args->tag_node, '', 'close' );

            }

            // close wrapping tag ##
            ui::get_tag( $args->tag, '', 'close' );

            // reopen content area ##
            if ( $args->layout == 'full_width' ) theme::the_content_open();

        // check if we have a featured image ##
        } else if ( \has_post_thumbnail( $the_post->ID ) ) {

            echo \get_the_post_thumbnail( $the_post->ID, $args->img_handle, array( 'class' => $args->img_handle ) );

        }

    }




    /**
     * get all images from a post gallery
     *
     * @param   Object      $post       Post object to examine
     * @param   String      $size       Handle of image size to return
     * @param   Integer     $limit      Number of images to return, defaults to 10
     * @param   String      $field      Allows for a custom field to be used to grab the gallery shortcode
     * @since 1.1.0
     */
    public static function get_gallery_images( $post = null, $size = null, $limit = null, $field = null ){

        // passed post or global ##
        if ( ! $post ) global $post;

        // kickout if no post object ##
        if ( ! is_object( $post ) ) { $post = \get_post( $post ); }

        // kick out if we can't get a real post object ##
        if ( ! $post || ! is_object( $post ) ) {
            #echo 'kicked';
            return false;
        }

        // limit set ##
        $limit = ! is_null ( $limit ) ? $limit : \get_site_option( 'posts_per_page', 10 );

        // set content or field to grab [gallery] shortcode from ##
        $content = ! is_null ( $field ) ? $field : $post->post_content ;

        // test passed content field ##
        #pr( $content );

        // http://wordpress.stackexchange.com/questions/80408/how-to-get-page-post-gallery-attachment-images-in-order-they-are-set-in-backend
        $pattern = \get_shortcode_regex();

        if( preg_match_all( '/'. $pattern .'/s', $content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'gallery', $matches[2] ) ):

            $keys = array_keys( $matches[2], 'gallery' );

            foreach( $keys as $key ):
                $atts = \shortcode_parse_atts( $matches[3][$key] );
                    if( array_key_exists( 'ids', $atts ) ):

                        $query_images = new WP_Query(
                            array(
                                'posts_per_page'    => $limit,
                                'post_type'         => 'attachment',
                                'post_status'       => 'inherit',
                                'post__in'          => explode( ',', $atts['ids'] ),
                                'orderby'           => 'post__in'
                            )
                        );

                        \wp_reset_query();

                    endif;
            endforeach;

        endif;

        // empty array, just in case ##
        $images = array();

        // build images array ##
        foreach ( $query_images->posts as $image ) {

            // image src ##
            if ( $size ) {

                $image_src_array = \wp_get_attachment_image_src( $image->ID, $size );
                $image_src = $image_src_array[0];

                // get updated meta ##
                $image_meta = array(
                    "width" => $image_src_array[1], // width ##
                    "height" => $image_src_array[2], // height ##
                );

            } else {

                $image_meta = \wp_get_attachment_metadata( $image->ID ); // get dimensions ##
                $image_src = $image->guid;

            }

            $images[] = array (
                "ID"            => $image->ID,
                "src"           => $image_src,
                "width"         => $image_meta["width"],
                "height"        => $image_meta["height"],
                'alt'           => \get_post_meta( $image->ID, '_wp_attachment_image_alt', true ),
                'caption'       => $image->post_excerpt ? $image->post_excerpt : 'undefined',
                'description'   => $image->post_content,
                'href'          => \get_permalink( $image->ID ),
                #'src'           => $image->guid,
                'title'         => $image->post_title
            );
        }

        return $images;

    }



    /**
     * Get post avatar parts
     *
     * @since       1.0.1
     * @return      Mixed       Object || Boolean false
     */
    public static function get_avatar( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // set-up new object ##
        $object = new \stdClass;

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )wp_parse_args( $args, \q_theme::$the_avatar );

        // add post ID, if not passed ##
        $args->post = isset ( $args->post ) ? $args->post : $the_post->ID ;

        // test args ##
        #pr( $args );

        // holder ##
        $object->src = $args->holder;

        // class ##
        $object->class = $args->class;

        // if taxonomy archive ##
        if ( $args->style == 'tax' ) {

            // category ##
            $object->category = \wp_get_post_terms( $args->post, 'category' );
            #pr( $object->category );

            // categories have a smaller holder image ##
            $object->src = helper::get( "theme/images/global/102x102.png", 'return' );

            if ( isset( $object->category[0] ) ) {

                // check for image ##
                if ( $image_src = \get_field( 'category_image', 'category_'.$object->category[0]->term_id ) ) {

                    // get attached image src ##
                    $image_src = \wp_get_attachment_image_src( $image_src, 'circle-small' );
                    #pr( $image_src );
                    $object->src = $image_src[0]; // take first array item ##

                }

            }

            // css ##
            #$object->class = 'circle-small';

        // single post ##
        } else {

            $image = \wp_get_attachment_image_src( \get_post_thumbnail_id( $args->post ), 'circle-large' ) ;

            if ( $image ) {

                $object->src = $image[0];

            }

            // css ##
            #$object->class = 'circle-large';

        }

        // kick back colour ##
        return $object;

    }




    /**
     * Load and return a snippet from a method slug
     *
     * @since       1.0.1
     * @return      string       HTML
     */
    public static function get_snippet( $slug = null, $args = array() )
    {

        // check arguments ##
        if ( is_null( $slug ) ) { return false; }

        // sanitize input ##
        $slug = \sanitize_key( $slug );

        // check if method exists in 'q_theme' ##
        if (
            method_exists( '\q\controller\snippets\snippets', $slug )
            && is_callable( array( '\q\controller\snippets\snippets', $slug ) )
        ) {

            // check args are in array, if not caste ##
            #if ( ! is_array( $args ) ) { $args =  $args; }

            // call class emthod and pass arguments ##
            call_user_func_array (
                    array( '\q\controller\snippets\snippets', $slug )
                ,   ( array )$args
            );

        }

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



    public static function get_the_password_form()
    {

?>
        <div class="password" style="text-align: center; margin: 20px;">
            <?php echo \get_the_password_form(); ?>
        </div>
<?php

        return true;

    }


    /**
     * Get Pagination links
     *
     * @since       1.0.2
     * @return      String      HTML
     */
    public static function get_pagination( $args = array() )
    {

        #pr( $args, 'pagination args' );

        // grab some global variables ##
        global $wp_query, $wp_rewrite;

        // work out paging ##
        $wp_query->query_vars['paged'] > 1 ? 
            $current = $wp_query->query_vars['paged'] : 
            $current = 1;

        // work out total ##
        $total =
            // \is_search() && isset( $args["posts_per_page"] ) && isset( $args["post_count"] ) ?
            // intval( $args["posts_per_page"] / $args["post_count"] ) :
            intval( $wp_query->found_posts / ( $args["posts_per_page"] ? $args["posts_per_page"] : 20 ) );

        // helper::log( $total );
        // helper::log( $wp_query->found_posts );
        // helper::log( 'device handle: '.self::get_device() );
        // helper::log( $current );

        // pagination url ##
        $base_url = \get_site_url( \get_current_blog_id() ).'?s='.\get_search_query().'&paged=';

        // prepare first item ##
        $first = '<a class="page-numbers page-first pagelink-1 pagelink" rel="1" href="'.$base_url.'1">&laquo; First</a>';

        // prepae last item ##
        $last = '<a class="page-numbers page-last pagelink pagelink-'.$total.'" href="'.$base_url.$total.'">Last &raquo;</a>';

        // build array to query from ##
        $array = array(
            'base'                  => @\add_query_arg('paged','%#%'),
            'format'                => '',
            'total'                 => $total,
            'current'               => $current,
            'show_all'              => false,
            'end_size'		        => 'desktop' == helper::get_device() ? 0 : 0,
            'mid_size'		        => 'desktop' == helper::get_device() ? 4 : 0,
            'type'                  => 'plain',
            'prev_text'             => 
                                        'desktop' == helper::get_device() ? 
                                        '&lsaquo; '.__('Previous', 'q-textdomain' ) : 
                                        '&lsaquo; '.__('Previous', 'q-textdomain' ) , 
            'next_text'             => 
                                        'desktop' == helper::get_device() ? 
                                        __('Next', 'q-textdomain' ).' &rsaquo;' : 
                                        __('Next', 'q-textdomain' ).' &rsaquo;' , 
            'first'                 => 'desktop' == helper::get_device() ? false : $first,
            'last'                  => 'desktop' == helper::get_device() ? false : $last,
        );

        #pr( $wp_query->max_num_pages );

        // using permalinks ##
        #if( $wp_rewrite->using_permalinks() ) {
            #$array['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );
        #}

        // add search query var ##
        if( ! empty($wp_query->query_vars['s']) ) {
            $array['add_args'] = array( 's' => \get_query_var( 's' ) );
        }

        #pr( $array );

        // kick back array ##
        return $array;

	}
	


	
    /**
     * Generic H1 title tag
     *
     * @param       Array       $args
     * @since       1.3.0
     * @return      String
     */
    public static function the_title( Array $args = null ) {

		// Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = \wp_parse_args( $args, \q_theme::$the_parent );

		// merge any default args with any pass args ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
		) {

			helper::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['post'] ) 
			&& $args['post'] instanceof \WP_Post
		) {

			$the_post = $args['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			helper::log( 'Error with post object, validate.' );

			return false;

		}

        // helper::log( $args );

        // set-up new array ##
		$array = [];

        // type ##
        $type = 'page';

        // get the title ##
        if (
            \is_home() )
        {

            $the_post = \get_option( 'page_for_posts' );
            // helper::log( 'Loading home title: '.$the_post );

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

            // helper::log('Loading archive title');
			$array['title'] = \__('Oops! It looks like you\'re lost');
			// $array['permalink'] = \get_permalink( \get_site_option( 'page_on_front' ) );

        } else if (

            \is_search()

        ){

            // helper::log( 'is_search' );

            // type ##
            $type = 'is_search';

            // helper::log('Loading archive title');
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

            // helper::log('Loading archive title');
			$array['title'] = \get_the_archive_title();
			// $array['permalink'] = \get_permalink( \get_site_option( 'page_on_front' ) );

        } else {

			$type = 'is_single';

            // helper::log('Loading post title');

            $the_post = $the_post->ID;

            // add the title ##
			$array['title'] = \get_the_title( $the_post );
			// $array['permalink'] = \get_permalink( $the_post );

        }

        // filter ##
        $array['title'] = \apply_filters( 'q/wordpres/the_title', $array['title'], $the_post, $type );

        // helper::log( $array );
		
		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			helper::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = generic::markup( $args['markup'], $array );

		// helper::log( $string );

		// echo ##
		echo $string ;

		// stop ##
		return true;

    }




	/**
     * link to parent, works for single WP Post or page objects
     *
     * @since       1.0.1
     * @return      string   HTML
     */
    public static function the_parent( Array $args = null ) {

		// Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = \wp_parse_args( $args, \q_theme::$the_parent );

		// merge any default args with any pass args ##
		if ( 
			is_null( $args )
			|| ! is_array( $args )
		) {

			helper::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['post'] ) 
			&& $args['post'] instanceof \WP_Post
		) {

			$the_post = $args['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			helper::log( 'Error with post object, validate.' );

			return false;

		}

        // helper::log( $args );

        // set-up new array ##
		$array = [];
		
		// pages might have a parent
		if ( 
			'page' === \get_post_type( $the_post ) 
			&& $the_post->post_parent
		) {

			// $array['text'] = __( "View More", 'q-textdomain' );
            $array['permalink'] = \get_permalink( $object->ID );
            $array['slug'] = $object->post_name;
            $array['title'] = $object->post_title;

		// is singular post ##
		} elseif ( \is_single( $the_post ) ) {

			// helper::log( 'Get category title..' );

			// $args->ID = $the_post->post_parent;
			if ( 
				! $array = self::get_the_category([ 'post' => $the_post ])
			){

				return false;

			}


		}

        // helper::log( $array );
		
		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			helper::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = generic::markup( $args['markup'], $array );

		// helper::log( $string );

		// echo ##
		echo $string ;

		// stop ##
		return true;

	}



    /**
	 * Helper Method to get parent
	 */
	public static function get_the_parent( Array $args = null ){

		// we want to return ##
		$args['return'] = 'return';

		// bounce on, and return array ##
		return \apply_filters( 'q/wordpress/get_the_parent', self::the_parent( $args ) );

	}


	


	public static function the_category( Array $args = null ) {

		if ( 
			is_null( $args )
		) {

			helper::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['post'] ) 
			&& $args['post'] instanceof \WP_Post
		) {

			$the_post = $args['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			helper::log( 'Error with post object, validate.' );

			return false;

		}

		// try and get_post_categories ##
		if ( 
			! $get_the_category = \get_the_category( $the_post->ID )
		){

			helper::log( 'No categories found for Post: '.$the_post->post_title );

			return false;

		}

		// we only want the first array item ##
		$category = $get_the_category[0];

		// test ##
		// helper::log( $category );

		// categories ##
		if (
			! is_object( $category )
			|| ! $category instanceof \WP_Term
		) {

			helper::log( 'Error in returned category' );

			return false;

		}

		$array['permalink'] = \get_category_link( $category );
		$array['slug'] = $category->slug;
		$array['title'] = $category->cat_name;

		// helper::log( $array );

		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			helper::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = generic::markup( $args['markup'], $array );

		// helper::log( $string );

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

			helper::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['post'] ) 
			&& $args['post'] instanceof \WP_Post
		) {

			$the_post = $args['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			helper::log( 'Error with post object, validate.' );

			return false;

		}

		// get author ##
		$author = $the_post->post_author;
		$authordata = \get_userdata( $author );

		// validate ##
		if (
			! $authordata
		) {

			helper::log( 'Error in returned author data' );

			return false;

		}

		// get author name ##
		$author_name = $authordata && isset( $authordata->display_name ) ? $authordata->display_name : 'Author' ;

		// assign values ##
		$array['permalink'] = \esc_url( \get_author_posts_url( $author ) );
		$array['slug'] = $authordata->user_login;
		$array['title'] = $author_name;

		// helper::log( $array );

		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			helper::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = generic::markup( $args['markup'], $array );

		// helper::log( $string );

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

			helper::log( 'Error in passed $args' );

			return false;

		}

		// get post ##
		if ( 
			isset( $args['post'] ) 
			&& $args['post'] instanceof \WP_Post
		) {

			$the_post = $args['post'];

		} else {

			$the_post = self::the_post();

		}

		// last check ##
		if ( ! $the_post ) {

			helper::log( 'Error with post object, validate.' );

			return false;

		}

		// get author ##
		$author = $the_post->post_author;
		$authordata = \get_userdata( $author );

		// validate ##
		if (
			! $authordata
		) {

			helper::log( 'Error in returned author data' );

			return false;

		}

		// get author name ##
		$author_name = $authordata && isset( $authordata->display_name ) ? $authordata->display_name : 'Author' ;

		// assign values ##
		$array['permalink'] = \esc_url( \get_author_posts_url( $author ) );
		$array['slug'] = $authordata->user_login;
		$array['title'] = $author_name;

		// helper::log( $array );

		if ( isset( $args['return'] ) && 'return' == $args['return'] ) {
			
			return $array ;

		}

		if ( ! isset( $args['markup'] ) ) {

			helper::log( 'Missing "markup", returning false.' );

			return false;

		}

		$string = generic::markup( $args['markup'], $array );

		// helper::log( $string );

		// echo ##
		echo $string ;

		// stop ##
		return true;

	}



	/**
	 * Helper Method to get the author
	 */
	public static function get_the_data( Array $args = null ){

		// we want to return ##
		$args['return'] = 'return';

		// bounce on, and return array ##
		return \apply_filters( 'q/wordpress/get_the_date', self::the_date( $args ) );

	}




    public static function list_image_sizes()
    {

        global $_wp_additional_image_sizes; 
        if( self::$debug ) helper::log( $_wp_additional_image_sizes ); 

    }



}