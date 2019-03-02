<?php

/**
 * Q Control Class
 * 
 * @since       2.0.0
 * @Author:     Q Studio
 * @URL:        https://qstudio.us/
 */

if ( ! class_exists( 'Q_Control' ) ) {

    class Q_Control extends Q {


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
            if ( $file = get_attached_file( $id ) ) {

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

                    if ( $object = get_post( $post ) ) {

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
            $args = ( object )wp_parse_args( $args, q_theme::$set_force_post );

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
            if ( ! $the_post = self::the_post( $args ) ) { return false; }

            // set-up new array ##
            $array = array();

            // get page template ##
            $template =
                get_body_class() ?
                (array) get_body_class() :
                array( "home" ) ;

            // add to array ##
            array_push( $array, $template[0] );

            // add page-$post_name ##
            $post_name = ( isset( $the_post ) && get_post_type() == "page" ) ? "page-{$the_post->post_name}" : '' ;

            // add to array ##
            array_push( $array, $post_name );

            // added passed element, if ! is_null ##
            if ( isset ( $args->element ) ) {

                array_push( $array, $args->element );

            }

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
            $columns = esc_sql( $columns );

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
                    ,   esc_sql( '%'.$wpdb->esc_like( trim( $title )  ).'%' )
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

            // self::log( $args );

            // Parse incoming $args into an array and merge it with $defaults - caste to object ##
            $args_array = wp_parse_args( $args, is_search() ? q_theme::$the_search : q_theme::$the_posts );
            $args = ( object )$args_array;
            // self::log( $args );

            // pagination ##
            $paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 ;

            // args ##
            $posts_args = array (
                'posts_per_page'    => $args->limit,
                'paged'             => $paged
            );

            // merge in global $wp_query variables ? ( required for archive pages ) ##
            if ( $args->query_vars ) {

                // grab all global wp_query args ##
                global $wp_query;

                // merge all args together ##
                $posts_args = array_merge( $wp_query->query_vars, $posts_args );

                // self::log( array( 'added query vars' => $posts_args ) );

            }

			// merge in global $wp_query variables ? ( required for archive pages ) ##
            if ( $args->search ) {

                // self::log( 'searching...' );

                $posts_args['post_type'] = isset( $args->post_type ) ? $args->post_type : 'any' ;
				#$posts_args['posts_per_page'] = 100; // get them all ##

            }

            // self::log( $posts_args );

            // set-up main query ##
            $q_query = new WP_Query( $posts_args );

            // self::log( $q_query->request );
            // self::log( $q_query->found_posts );
            // self::log( $q_query->post_count, 'post_count' );

            // weird WPE hack - to reduce the returned array to the size of $args->limit ##
            if ( -1 != $args->limit && $q_query->post_count > $args->limit ) {

                // self::log( "splicing.." );
                #array_splice( $q_query->posts, 2 );
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
                self::get_tag( $args->tag, array ( 'posts', $args->class ) );

                // total ##
                if ( isset( $args->total ) ) {

                    echo str_replace( '%total%', $q_query->found_posts, $args->total );

                }

                // loop ##
                foreach ( $get_posts as $post ) {

                    // iterate the post loop ##
                    setup_postdata( $post );

                    #pr( $post->ID );

                    // add post ID to passed args ##
                    $args_array['post'] = $post->ID;

                    // check if method exists in 'q_theme' ##
                    if (
                        method_exists( $args->view, $args->method )
                        // && is_callable( array( "\q\theme\theme\view\{$args->template}\{$args->template}", "the_{$args->template}_loop" ) )
                    ) {

                        #pr( $args_array );

                        // call template method ##
                        call_user_func (
                                array( $args->view, $args->method )
                            ,   (array)$args_array
                        );

                    } else {

                        self::log ( "Method Missing : {$args->view}::{$args->method}" );

                    }

                    // tidy up ##
                    \wp_reset_postdata();

                }

                // close wrapping tag ##
                self::get_tag( $args->tag, '', 'close' );

                // get sidebar ##
                if ( $args->sidebar ) {
                    
                    \q\theme\theme\controller\navigation\navigation::the_sidebar();

                }

                // pagination ##
                // self::log( 'pagination: '.$args->pagination );
				#pr( $posts_args['posts_per_page'], 'posts_per_page' );
				#pr( $q_query->post_count, 'post_count' );
                if ( $args->pagination ) {

                    // self::log( 'Adding pagination..' );
                    
                    \q\theme\theme\controller\navigation\navigation::the_pagination([
					    'posts_per_page'	=> $posts_args['posts_per_page'],
					    'post_count'		=> count( $q_query->post_count )
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
            $args = ( object )wp_parse_args( $args, q_theme::$the_loop );

            // set-up new object ##
            $object = new stdClass;

            // default image ##
            $object->src = $args->holder;

            // check for featured image ##
            if ( has_post_thumbnail( $the_post->ID ) ) {

                $src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_post->ID ), $args->image_handle );
                $object->src = $src[0];

            }

            // title - keep to 60 characters ##
            $object->title = q_chop( get_the_title( $the_post->ID ), $args->title_length );

            // get the excerpt ##
            $object->excerpt = q_excerpt_from_id( $the_post->ID, $args->excerpt_length );

            // kick it back ##
            return $object;

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
            $args = ( object )wp_parse_args( $args, q_theme::$the_posts );

            #pr( $the_post->ID );

            // build a new object ##
            $object = new stdClass;

            #return pr( $post->ID );

            // ID ##
            $object->ID = $the_post->ID;

            // sticky ?? ##
            $object->sticky = 
                in_array( $the_post->ID, \get_option( 'sticky_posts' ) ) ?
                true :
                false ;

            // title ##
            $object->title = get_the_title( $the_post->ID );

            // permalink ##
            $object->permalink = get_permalink( $the_post->ID );

            // category ##
            $object->category = wp_get_post_terms( $the_post->ID, 'category' );
            #pr( $category );

            // tag ##
            $object->tag = wp_get_post_terms( $the_post->ID, 'post_tag' );
            #pr( $category );

            // is this an "auto" tagged post ##
            $object->auto = has_tag( "Auto", $the_post ) ? true : false ;

            // do we have a real image ##
            $has_src = true;
            $object->src = '';

            // image ##
            if ( has_post_thumbnail( $the_post->ID ) ) {

                // show small image, linking to larger image ##
                $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_post->ID ), $args->handle[Q::get_device()] ); 
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
                    is_tag() ? 
                    get_field( 'taxonomy_image', 'post_tag_'.$object->tag[0]->term_id ) : 
                    get_field( 'taxonomy_image', 'category_'.$object->category[0]->term_id )

            ) {

                $src_array = wp_get_attachment_image_src( $src, $args->handle[Q::get_device()] );
                $object->src = $src_array[0];

            }

            // backup ##
            if ( ! $has_src ) {

                $object->src = $args->holder[Q::get_device()];

            }

            // content ##
            $object->content = q_excerpt_from_id( $the_post->ID, $args->length ) ? q_excerpt_from_id( $the_post->ID, $args->length ) : get_bloginfo( 'description' ) ;

            // date ##
            $object->date = get_the_date( $args->date_format, $the_post->ID ); 

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
            $args = ( object )wp_parse_args( $args, q_theme::$the_page );

            // build a new object ##
            $object = new stdClass;

            #self::log( $post->ID );

            // ID ##
            $object->ID = $the_post->ID;

            // title ##
            $object->title = get_the_title( $the_post->ID );

            // permalink ##
            $object->permalink = get_permalink( $the_post->ID );

            // header image ##
            if ( has_post_thumbnail( $the_post->ID ) ) {

                // grab array ##
                $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_post->ID ), $args->handle ); // @todo - device specific handle ##
                $object->src = $img_src[0]; // take first array item ##

            // backup ##
            } else {

                $object->src = q_locate_template( 'images/holder/'.$args->holder.'.png', false, false, false ); // @todo - device specific handle ##

            }

            // excerpt ##
            $object->excerpt = q_excerpt_from_id( $the_post->ID, 200 ) ? q_excerpt_from_id( $the_post->ID, 200 ) : get_bloginfo( 'description' ) ; // @todo, change fallback ##

            // content ##
            $object->content = apply_filters( 'the_content', get_post_field('post_content', $the_post->ID ) );

            // is the form on this program destination active ##
            $object->form_switch = get_field( 'form_switch', $the_post->ID ) ? get_field( 'form_switch', $the_post->ID ) : '0' ;

            // kick back object ##
            return $object;

        }



        /**
         * Get single program data
         *
         * @since   1.6.2
         * @return  Mixed boolean false or Object
         */
        public static function get_program_content( $args = array() )
        {

            #pr( $args );

            // get the_post ##
            if ( ! $the_post = self::the_post( $args ) ) { return false; }

            $handle = self::get_device_handle() == 'desktop' ? 'desktop-single-post' : 'handheld-single-post' ; // device based image handle ##
            $holder = self::get_device_handle() == 'desktop' ? 'desktop-single-post' : 'handheld-single-post' ; // device based image handle ##

            // build a new object ##
            $object = new stdClass;

            #self::log( $post->ID );

            // ID ##
            $object->ID = $the_post->ID;

            // title ##
            $object->title = get_the_title( $the_post->ID );

            // permalink ##
            $object->permalink = get_permalink( $the_post->ID );

            // do we have a real image ##
            $has_src = true;

            // header image ##
            if ( has_post_thumbnail( $the_post->ID ) ) {

                // grab array ##
                $img_src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_post->ID ), $handle ); // @todo - device specific handle ##
                $object->src = $img_src[0]; // take first array item ##

                if ( self::attachment_exists( get_post_thumbnail_id( $the_post->ID ) ) ) {

                    #self::log( 'found: '.$object->src );

                    // update flag ##
                    $has_src = true;

                }

            }

            // backup ##
            if ( ! $has_src ) {

                $object->src = q_locate_template("images/holder/".$holder.".svg", false, false, false );

                #self::log( 'holder: '.$object->src );

            }

            // excerpt ##
            $object->excerpt = q_excerpt_from_id( $the_post->ID, 200 ) ? q_excerpt_from_id( $the_post->ID, 200 ) : get_bloginfo( 'description' ) ; // @todo, change fallback ##

            // remove the gallery [shortcode] from the content ##
            add_filter( 'the_content', 'q_remove_gallery_shortcode', 1 );

            // content ##
            $object->content = apply_filters( 'the_content', get_post_field('post_content', $the_post->ID ) );

            // is the form on this program destination active ##
            $object->form_switch = get_field( 'form_switch', $the_post->ID ) ? get_field( 'form_switch', $the_post->ID ) : '0' ;

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
            $args = ( object )wp_parse_args( $args, q_theme::$the_post_thumbnail );

            #pr( $args );

            // set-up a new object ##
            $object = new stdClass;

            if ( ! has_post_thumbnail( $the_post->ID ) ) { return false; }

            // self::log( 'Handle: '.$args->handle[self::get_device()] );

            // show small image, linking to larger image ##
            $object->src = wp_get_attachment_image_src( get_post_thumbnail_id( $the_post->ID ), $args->handle[self::get_device()] );
            $img_alt = get_post_meta( get_post_thumbnail_id( $the_post->ID ), '_wp_attachment_image_alt', true);
            $object->alt = ( $img_alt ? $img_alt : get_the_title() ) ;

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
            if ( ! $global_post = self::$force_post ? self::$force_post : self::the_post() ) { return false; }

            // Parse incoming $args into an array and merge it with $defaults - caste to object ##
            $args = ( object )wp_parse_args( $args, q_theme::$the_navigation );

            #// find out "depth" of current page ##
            #$depth = count( get_post_ancestors( $global_post->ID ) );
            #pr( $depth );

            // work out who to list pages from ##
            #$post_parent = $depth == 0 ? $global_post : get_post( $global_post->post_parent );
            #pr( $post_parent->ID );

            $post_parent = array_reverse( get_post_ancestors( $global_post->ID ) );
            #$first_parent = get_page($parent[0]);
            #pr( $post_parent[0] );

            // meta_query to exclude certain sub pages from desktop on screen sub navigation ##
            $meta_query = array(); // nada ##
            if ( self::get_device() == 'desktop' ) {

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

            $object = new WP_Query( $wp_args );

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
                    get_post_meta( $post->ID, 'template_navigation_title' ) ? 
                    get_post_meta( $post->ID, 'template_navigation_title', true ) : 
                    $post->post_title ;

                // permalink ##
                $item->permalink = get_permalink( $post->ID );

                // class & highlight ##
                $item->class = 'post';

                // class & highlight ##
                $item->li_class = 
                    $post->ID === $global_post->ID ? 
                    'current_page_item page-'.sanitize_key( $post->post_name ).' post-'.$post->ID : 
                    'post-'.$post->ID.' page-'.sanitize_key( $post->post_name ) ;

                // class ##
                $item->data = false;

                // slim post object to fields we need ##
                #$item = q_trim_post( $item );

                // sort out global $post after WP_Query loop ##
                wp_reset_postdata();

                #self::log( $item );

                // add to array ##
                $posts[] = $item;

            }

            // test posts #
            #pr( $object->posts );

            // allow posts to be filtered ##
            $posts = \apply_filters( 'filter_get_navigation', $posts );

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
            $args = ( object )wp_parse_args( 
                $args
                , q_theme::$the_nav_menu 
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
            $args = ( object )wp_parse_args( $args, q_theme::$the_landing );

            // find out "depth" of current page ##
            $depth = count( get_post_ancestors( $the_post->ID ) );

            // work out who to list pages from ##
            $post_parent = $depth == 0 ? $the_post : get_post( $the_post->post_parent );

            $args = array (
                'child_of'          => $post_parent->ID,
                'sort_column'       => 'menu_order',
                'sort_order'        => 'ASC',
            );
            $pages = get_pages($args);

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

            $object = new WP_Query( $wp_args );

            // nothing found - why? ##
            if ( 0 === $object->post_count ) { return false; }

            // get children ##
            $children = get_pages(
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
            $args = ( object )wp_parse_args( $args, q_theme::$the_gallery );

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

                    $gallery = get_field( $args->post_meta, $args->post );
                    #pr( $gallery );

                } else if ( $post_meta = get_post_meta( $args->post, $args->post_meta, true ) ) {

                    #pr( $post_meta );

                    $gallery = q_get_gallery_images( $args->post, $args->img_handle, $args->limit, $post_meta );

                }

            } else {

                $gallery = q_get_gallery_images( $args->post, $args->img_handle, $args->limit );

            }

            // test if we got any images ##
            if ( ! $gallery  ) { return false; }

            // close content area ##
            if ( $args->layout == 'full_width' ) q_theme::the_content_close();

            // open wrapping tag ##
            self::get_tag( $args->tag, array ( $args->class ) );

            // test the gallery array ##
            #pr( $gallery );

            // loop over gallery ##
            foreach ( $gallery as $image ) {

                // toggle img / src depending on type ##
                $img_src = isset( $args->acf ) ? $image["sizes"]["{$args->img_handle}"] : $image['src'] ;

                // tag_node + class ##
                self::get_tag( $args->tag_node, array ( $args->class_node ) );

?>
                    <img src="<?php echo $img_src; ?>" />
<?php

                // tag_node + class ##
                self::get_tag( $args->tag_node, '', 'close' );

            }

            // close wrapping tag ##
            self::get_tag( $args->tag, '', 'close' );

            // reopen content area ##
            if ( $args->layout == 'full_width' ) q_theme::the_content_open();

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
            $args = ( object )wp_parse_args( $args, q_theme::$the_gallery_or_image );

            // add post ID, if not passed ##
            $args->post = isset ( $args->post ) ? $args->post : $the_post->ID ;

            // empty gallery ##
            $gallery = false;

            // define gallery source and grab images ##
            if ( isset( $args->post_meta ) ) {

                // built using new ACF field type ##
                if ( isset( $args->acf ) ) {

                    $gallery = get_field( $args->post_meta, $args->post );
                    #pr( $gallery );

                } else if ( $post_meta = get_post_meta( $args->post, $args->post_meta, true ) ) {

                    #pr( $post_meta );

                    $gallery = q_get_gallery_images( $args->post, $args->img_handle, $args->limit, $post_meta );

                }

            } else {

                $gallery = q_get_gallery_images( $args->post, $args->img_handle, $args->limit );

            }

            // build it out ##
            if ( $gallery && is_array ( $gallery ) ) {

                // close content area ##
                if ( $args->layout == 'full_width' ) q_theme::the_content_close();

                // open wrapping tag ##
                self::get_tag( $args->tag, array ( $args->class ) );

                // test the gallery array ##
                #pr( $gallery );

                // loop over gallery ##
                foreach ( $gallery as $image ) {

                    // toggle img / src depending on type ##
                    $img_src = isset( $args->acf ) ? 'url' : 'src' ;

                    // tag_node + class ##
                    self::get_tag( $args->tag_node, array ( $args->class_node ) );

?>
                        <img src="<?php echo $image[$img_src]; ?>" />
<?php

                    // tag_node + class ##
                    self::get_tag( $args->tag_node, '', 'close' );

                }

                // close wrapping tag ##
                self::get_tag( $args->tag, '', 'close' );

                // reopen content area ##
                if ( $args->layout == 'full_width' ) q_theme::the_content_open();

            // check if we have a featured image ##
            } else if ( has_post_thumbnail( $the_post->ID ) ) {

                echo get_the_post_thumbnail( $the_post->ID, $args->img_handle, array( 'class' => $args->img_handle ) );

            }

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
            $object = new stdClass;

            // Parse incoming $args into an array and merge it with $defaults - caste to object ##
            $args = ( object )wp_parse_args( $args, q_theme::$the_avatar );

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
                $object->category = wp_get_post_terms( $args->post, 'category' );
                #pr( $object->category );

                // categories have a smaller holder image ##
                $object->src = q_locate_template( "images/holder/102x102.png", false, false, false );

                if ( isset( $object->category[0] ) ) {

                    // check for image ##
                    if ( $image_src = get_field( 'category_image', 'category_'.$object->category[0]->term_id ) ) {

                        // get attached image src ##
                        $image_src = wp_get_attachment_image_src( $image_src, 'circle-small' );
                        #pr( $image_src );
                        $object->src = $image_src[0]; // take first array item ##

                    }

                }

                // css ##
                #$object->class = 'circle-small';

            // single post ##
            } else {

                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $args->post ), 'circle-large' ) ;

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
         * Open HTML Tag, adding set classes
         *
         * @since       1.0.3
         * @return      string HTML formatted text
         */
        public static function get_tag( $tag = null, $classes = array(), $action = 'open' )
        {

            // sanity check ##
            if ( is_null ( $tag ) ) { return false; }

            // switch over "open" OR "close" ##
            switch ( $action ) {

                case 'close' :

?>
                </<?php echo $tag; ?>>
<?php

                break ;

                default :
                case 'open' :

?>
                <<?php echo $tag; ?> class="<?php echo implode( array_filter( (array)$classes ), " " ); ?>">
<?php
                break ;

            }

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
            $slug = sanitize_key( $slug );

            // check if method exists in 'q_theme' ##
            if (
                method_exists( '\q\theme\theme\snippets\snippets', $slug )
                && is_callable( array( '\q\theme\theme\snippets\snippets', $slug ) )
            ) {

                // check args are in array, if not caste ##
                #if ( ! is_array( $args ) ) { $args =  $args; }

                // call class emthod and pass arguments ##
                call_user_func_array (
                        array( '\q\theme\theme\snippets\snippets', $slug )
                    ,   ( array )$args
                );

            }

        }



        /**
         * Get Post object by post_meta query
         *
         * @since       1.0.4
         * @return      Object      WP post object
         */
        public static function get_post_by_meta( $args = array() )
        {

            // Parse incoming $args into an array and merge it with $defaults - caste to object ##
            $args = ( object )wp_parse_args( $args, q_theme::$get_post_by_meta );

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
            $posts = get_posts( $post_args );

            // check results ##
            if ( ! $posts || is_wp_error( $posts ) ) return false;

            // test it ##
            #pr( $posts[0] );
            #pr( $args->posts_per_page );

            // if we've only got a single item - shuffle up the array ##
            if ( 1 === $args->posts_per_page && $posts[0] ) { return $posts[0]; }

            // kick back results ##
            return $posts;

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
            $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

			// work out total ##
			$total =
				is_search() && isset( $args["posts_per_page"] ) && isset( $args["post_count"] ) ?
				intval( $args["posts_per_page"] / $args["post_count"] ) :
				$wp_query->max_num_pages ;

			#pr( $total );
            // self::log( 'device handle: '.self::get_device_handle() );

            // prepare first item ##
            $first = '<a class="page-numbers first" href="'.get_pagenum_link().'">First</a>';

            // prepae last item ##
            $last = '<a class="page-numbers last" href="'.get_pagenum_link( $wp_query->max_num_pages ).'">Last</a>';

            // build array to query from ##
            $array = array(
                'base'                  => @add_query_arg('paged','%#%'),
                'format'                => '',
                //'total'               => $total,
                'current'               => $current,
                'show_all'              => false,
				'end_size'		        => 'desktop' == self::get_device_handle() ? 1 : 0,
				'mid_size'		        => 'desktop' == self::get_device_handle() ? 2 : 0,
                'type'                  => 'plain',
                'prev_text'             => 'desktop' == self::get_device_handle() ? '&laquo; '.__('Previous', 'q-textdomain' ) : '&laquo;',
                'next_text'             => 'desktop' == self::get_device_handle() ? __('Next', 'q-textdomain' ).' &raquo;' : '&raquo;',
                'first'                 => 'desktop' == self::get_device_handle() ? false : $first,
                'last'                  => 'desktop' == self::get_device_handle() ? false : $last,
            );

			#pr( $wp_query->max_num_pages );

            // using permalinks ##
            #if( $wp_rewrite->using_permalinks() ) {
                #$array['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );
            #}

            // add search query var ##
            if( ! empty($wp_query->query_vars['s']) ) {
                $array['add_args'] = array( 's' => get_query_var( 's' ) );
            }

			#pr( $array );

            // kick back array ##
            return $array;

        }



        /**
         * back to parent button, for deep sitting pages
         *
         * @since       1.0.1
         * @return      string   HTML
         */
        public static function get_parent( $args = array() )
        {

            // sanity check ##
            if ( $args == new stdClass() ) { return false; }

            #self::log( $args );

            // set-up new object ##
            $object = new stdClass;

            // // grab page - polylang will take take or language selection ##
            // $args = array(
            //     'meta_query'        => array(
            //         array(
            //             'key'       => 'ID',
            //             'value'     => (string) $args->ID
            //         )
            //     ),
            //     'post_type'         => (string) $args->post_type,
            //     'posts_per_page'    => 1
            // );

            #$posts = get_posts( $args );

            #self::log( $posts );

            $post = get_post( $args->ID );

            #self::log( $post );

            // check we got a result ##
            if ( $post ) {

                $object->text = __( "View More", 'q-textdomain' );
                $object->url = get_permalink( $post->ID );
                $object->slug = $post->post_name;
                $object->title = $post->post_title;

                #self::log( $object );

                // kick back object ##
                return $object;

            }

            // nada ##
            return false;

        }



        /**
         * Search for and print translateable text string
         *
         * @param       Array       $args
         * @since       1.3.0
         * @return      String
         */
        public static function get_text( $string = null, $post = null )
        {

            // sanity check ##
            if ( is_null( $string ) ) { return false; }

            // get the key ##
            $key = strtolower( str_replace(' ', '_', $string ) ); // Replaces all spaces with underscore ##
            $key = preg_replace('/[^A-Za-z0-9\_]/', '', $key ); // Removes special chars ##

            // test the key ##
            #pr( $key );

            // grab post and check for meta keys ##
            if ( $the_post = self::the_post( $post ) ) {

                // fields found ##
                if( have_rows('template_cta') ) {

                    // loop through the rows of data
                    while ( have_rows('template_cta') ) :

                        // set-up the row ##
                        the_row();

                        // display a sub field value
                        $text_key = get_sub_field( 'key' );
                        $text_value = get_sub_field( 'value' );

                        // try to match key ##
                        if ( $key == $text_key ) {

                            #pr( $text_value );
                            return $text_value;

                        }

                    endwhile;

                }

            }

            // still here - so check for a translated string ##
            if ( array_key_exists( $key, self::$text ) ) {

                return self::$text[$key];

            }

            // still here - echo the original string passed ##
            return $string;

        }


        /**
        * Get Video URL from oEmbed field in ACF
        *
        * @since		1.4.5
        * @return		String		Video URL
        */
        public static function get_video_thumbnail_uri( $video_uri = null )
        {

            $thumbnail_uri = '';

            // determine the type of video and the video id
            if ( ! $video = self::parse_video_uri( $video_uri ) ) { return false; }

            // get youtube thumbnail
            if ( $video['type'] == 'youtube' ) {
                $thumbnail_uri = 'https://img.youtube.com/vi/' . $video['id'] . '/mqdefault.jpg';
            }

            // get vimeo thumbnail
            if( $video['type'] == 'vimeo' ) {

                $thumbnail_uri = self::get_vimeo_thumbnail_uri( $video['id'] );

            // get wistia thumbnail
            } else if( $video['type'] == 'wistia' ) {

                $thumbnail_uri = self::get_wistia_thumbnail_uri( $video_uri );

            // get default/placeholder thumbnail ##
            } else if( ! $thumbnail_uri || is_wp_error( $thumbnail_uri ) ) {

                return false;

            }

            //return thumbnail uri
            return $thumbnail_uri;

        }


        /**
        * Parse the video uri/url to determine the video type/source and the video id
        *
        * @since		1.4.5
        * @return		Array
        */
        public static function parse_video_uri( $url ) {

            // Parse the url
            $parse = parse_url( $url );

            // Set blank variables
            $video_type = '';
            $video_id = '';

            // Url is http://youtu.be/xxxx
            if ( $parse['host'] == 'youtu.be' ) {

                $video_type = 'youtube';
                $video_id = ltrim( $parse['path'],'/' );

            }

            // Url is http://www.youtube.com/watch?v=xxxx
            // or http://www.youtube.com/watch?feature=player_embedded&v=xxx
            // or http://www.youtube.com/embed/xxxx
            if ( ( $parse['host'] == 'youtube.com' ) || ( $parse['host'] == 'www.youtube.com' ) ) {

                $video_type = 'youtube';

                parse_str( $parse['query'] );

                $video_id = $v;

                if ( !empty( $feature ) )
                    $video_id = end( explode( 'v=', $parse['query'] ) );

                if ( strpos( $parse['path'], 'embed' ) == 1 )
                    $video_id = end( explode( '/', $parse['path'] ) );

            }

            // Url is http://www.vimeo.com
            if ( ( $parse['host'] == 'vimeo.com' ) || ( $parse['host'] == 'www.vimeo.com' ) ) {

                $video_type = 'vimeo';
                $video_id = ltrim( $parse['path'],'/' );

            }

            $host_names = explode(".", $parse['host'] );
            $rebuild = ( ! empty( $host_names[1] ) ? $host_names[1] : '') . '.' . ( ! empty($host_names[2] ) ? $host_names[2] : '');

            // Url is an oembed url wistia.com ##
            if ( ( $rebuild == 'wistia.com' ) || ( $rebuild == 'wi.st.com' ) ) {

                $video_type = 'wistia';

                if ( strpos( $parse['path'], 'medias' ) == 1 ) {

                    $video_id = end( explode( '/', $parse['path'] ) );

                }

            }

            // If recognised type return video array
            if ( ! empty( $video_type ) ) {

                return array(
                    'type' => $video_type,
                    'id' => $video_id
                );

            } else {

                return false;

            }

        }


        /**
        * Takes a Vimeo video/clip ID and calls the Vimeo API v2 to get the large thumbnail URL.
        *
        * @since		1.4.5
        * @return		String		Video Thumbnail Src
        */
        public static function get_vimeo_thumbnail_uri( $clip_id = null )
        {

            // sanity check ##
            if ( is_null( $clip_id ) ) return false;

            $vimeo_api_uri = 'http://vimeo.com/api/v2/video/' . $clip_id . '.php';
            $vimeo_response = wp_remote_get( $vimeo_api_uri );

            if( is_wp_error( $vimeo_response ) ) {

                return $vimeo_response;

            } else {

                $vimeo_response = unserialize( $vimeo_response['body'] );
                return $vimeo_response[0]['thumbnail_large'];

            }

        }


        /**
        * Takes a wistia oembed url and gets the video thumbnail url.
        *
        * @since		1.4.5
        * @return		String		Video Thumbnail Src
        */
        public static function get_wistia_thumbnail_uri( $video_uri = null )
        {

            // sanity check ##
            if ( is_null( $video_uri ) ) return false;

            $wistia_api_uri = 'http://fast.wistia.com/oembed?url=' . $video_uri;
            $wistia_response = wp_remote_get( $wistia_api_uri );

            if( is_wp_error( $wistia_response ) ) {

                return $wistia_response;

            } else {

                $wistia_response = json_decode( $wistia_response['body'], true );
                return $wistia_response['thumbnail_url'];

            }

        }


            

        // /**
        // * Add requires assets for Gravity Form
        // *
        // * @since       1.0.1
        // * @return      void
        // */
        // public static function gform_enqueue_scripts()
        // {

        //     // check for function ##
        //     if( ! function_exists( 'gravity_form_enqueue_scripts' ) ) { return false; }

        //     // get post gravity form ID ##
        //     gravity_form_enqueue_scripts( self::gform_get_id(), true );

        // }


        // /**
        // * Grab ID of Gravity Form
        // *
        // * @since       1.0.1
        // * @return      int     ID of gravity form to include
        // */
        // public static function gform_get_id()
        // {

        //     // get the_post ##
        //     if ( ! $the_post = self::the_post() ) { return false; }

        //     // kick it back ##
        //     return
        //         \get_post_meta( $the_post->ID, 'contact_form_'.self::$language, true ) ?
        //         \get_post_meta( $the_post->ID, 'contact_form_'.self::$language, true ) :
        //         1 ;

        // }



    }

}