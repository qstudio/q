<?php

namespace q\extension\search;

// Q ##
use q\core;
use q\get;
use q\core\helper as h;
use q\extension;

// load it up ##
// \q\extension\search\method::run();

class method extends extension\search {

    public static function run( $args = null )
    {


    }


	/*
    public static function config()
    {

        // new array ##
        $config = [];

        // h::log( 'Device: '.h::device() );

		// values ##
		// $config["control"]          = \apply_filters( 'q/search/control', [ 'load' => '1', 'empty' => '1' ] ); //loading state ##
        // $config["widget_title"]     = __( "Search", 'q-search' );
        // $config["results"]          = \apply_filters( 'q/search/results', ['Result found', 'Results found'] ); // results text ##
		// $config["no_results"]       = \apply_filters( 'q/search/no_results', 'No Results found' ); // results text ##
		// $config["load_empty"]       = \apply_filters( 'q/search/load_empty', [ // empty load text ##
		// 									'title' => 'Search Tool',
		// 									'body' 	=> 'Use the search option and filters to find results.' 
		// 							]);
        // $config["table"]            = \apply_filters( 'q/search/table', 'posts' ); // users or posts ##
        // $config["args"]             = \apply_filters( 'q/search/args', false ); // additional args passed to render method ##
        // $config["application"]      = \apply_filters( 'q/search/application', 'general' ); // for filtering via ajax ##
        // $config["device"]           = \apply_filters( 'q/search/device', h::device() ); // for device check via ajax ##
        $config["post_type"]        = \apply_filters( 'q/search/post_type', 'post' );
        $config["taxonomies"]       = \apply_filters( 'q/search/taxonomies', 'category,post_tag' );
        $config["category_name"]    = \apply_filters( 'q/search/category_name', \get_query_var( 'category_name', '' ) );
        $config["author_name"]      = \apply_filters( 'q/search/author_name', \get_query_var( 'author_name', '' ) );
        $config["tag"]              = \apply_filters( 'q/search/tag', \get_query_var( 'tag', '' ) );
        $config["posts_per_page"]   = \apply_filters( 'q/search/posts_per_page', get_option( "posts_per_page", 10 ) );
        $config["class"]            = \apply_filters( 'q/search/class', 'q_search' );
        $config["grid_input"]       = \apply_filters( 'q/search/grid/input', 'col-md-4 col-12' );
        $config["grid_select"]      = \apply_filters( 'q/search/grid/select', 'col-md-8 col-12' );
        $config["callback"]         = \apply_filters( 'q/search/callback', false );
        $config["order"]            = \apply_filters( 'q/search/order', 'DESC' );
        $config["order_by"]         = \apply_filters( 'q/search/order_by', 'date' );
        $config['role__not_in']     = \apply_filters( 'q/search/role__not_in', [ 'Administrator' ] );
        $config["meta_key"]         = \apply_filters( 'q/search/meta_key', false ); // for wp_user_query ordering ##
        $config["filter_type"]      = \apply_filters( 'q/search/filter_type', 'select' );
        // $config["filter_position"]  = \apply_filters( 'q/search/filter_position', 'top' );
        $config["show_count"]       = \apply_filters( 'q/search/show_count', 0 );
        $config["show_input_text"]  = \apply_filters( 'q/search/show_input_text', false );
        $config["hide_titles"]      = \apply_filters( 'q/search/hide_titles', 0 );
        $config["pagination"]       = \apply_filters( 'q/search/pagination', true );
        $config["ajax_section"]     = \apply_filters( 'q/search/ajax_section', true );
        $config["markup"]           = \apply_filters( 'q/search/markup', 
                                        '<li class="ajax-loaded q-search-default">
                                            <h3>%title%</h3>
                                            <a href="%permalink%" title="%title%">
                                                <img src="%src%" />
                                            </a>
                                            <p>%content%</p>
                                            <a href="%permalink%" title="%title%">Read More</a>
                                        </li>' 
                                        );

        // check ##
        // h::log( $config );

        // populate static property ##
        return self::$properties = $config;

    }
	*/

    /**
    * Load plugin properties
    *
    * @since    2.0.0
    * @return   Array
    */
    public static function properties( $key = null, $format = 'string' )
    {

        // h::log( 'd:>called for key: '.$key );

        // properties not defined yet ##
        if ( ! self::$properties ) {

            // h::log( 'properties empty, so loading fresh...' );
            // h::log( self::$passed_args );

            // self::config();
			self::$properties = core\config::get([ 'context' => 'extension', 'task' => 'search' ]); 
			// h::log( self::$properties );

		}
		
		// h::log( 'd:>key: '.$key.' format: '.$format );

		// missing specified key ##
		if ( 
			! is_null( $key ) 
			&& ! isset( self::$properties[$key] ) 
		){

			// h::log( 'd:>key: '.$key.' missing' );

			return false;

		}

		// kick back specified key or whole array ##
		if ( 
			! is_null( $key ) 
			&& isset( self::$properties[$key] ) 
			// && array_key_exists( $key, self::$properties ) 
		){

			if ( 
				is_array ( self::$properties[$key] ) 
				&& 'string' == $format 
			){

				// flat csv ##
				$return = implode( ",", self::$properties[$key] ) ;

				// h::log( 'd:>>key: '.$key.' -- array as string--'.$return );

			} else {

				 // as array ##
				$return = self::$properties[$key] ;

				// h::log( 'd:>string OR array--' );
				// h::log( $return );

			}

		} else {

			// whole thing ##
            $return = self::$properties ;

		}

		// kick back ##
		return $return;


        // return 
        //     ( 
		// 		! is_null( $key ) 
		// 		&& isset( self::$properties[$key] ) 
		// 		// && array_key_exists( $key, self::$properties ) 
		// 	) ? 

        //     // single array item ##
        //     ( is_array ( self::$properties[$key] ) && 'string' == $return ) ? 
        //     implode( ",", self::$properties[$key] ) : // flat csv ##
        //     self::$properties[$key] : // as array ##
            
        //     // whole thing ##
        //     self::$properties ;

    }



    public static function wp_enqueue_scripts()
    {

        return self::wp_enqueue_scripts();

    }



    public static function render()
    {

		#h::log( 'rendering search..' );
        return self::render();

    }



    public static function default_args( $posted = null )
    {

        // sanity ##
        if ( is_null( $posted ) ) {

            h::log( 'd:>no defaults..' );

            return false;

        }

        switch ( self::properties( 'table' ) ) {

            // allow for searching users ##
            case "users" :
            
                // build args list ##
                $args = array(
                    'number'                => $posted['posts_per_page'] > 20 ? 20 : intval( $posted['posts_per_page'] ), #core::properties( 'posts_per_page' ),
                    'post_type'             => 'users', #core::properties( 'post_type' ),
                    'role__not_in'          => self::properties( 'role__not_in' ), // 'Administrator',
                    'meta_key'              => self::properties( 'meta_key' ),
                    'orderby'               => self::properties( 'order_by' ),
                    'order'                 => self::properties( 'order' ) 
                );

            break ;

            // default wp_posts query ##
            case "posts" :
            default :

                // build args list ##
                $args = array(
                        "post_type"         => $posted['post_type']
                    ,   "posts_per_page"    => $posted['posts_per_page'] > 20 ? 20 : (int) $posted['posts_per_page'] // max 20 ##
                    ,   "tax_query"         => array()
                    ,   "orderby"           => $posted['order_by']
                    ,   "order"             => $posted['order']
                    ,   "post_status"       => "publish"
                );

            break ;

        }

        // return filterable values ##
        return \apply_filters( 'q/search/default_args/', $args );

    }




    public static function empty_args( $posted = null )
    {

        // sanity ##
        if ( is_null( $posted ) ) {

            h::log( 'no empty...' );

            return false;

        }

        switch ( self::properties( 'table' ) ) {

            // allow for searching users ##
            case "users" :

                // build args list ##
                $args = array(
                    'number'                => $posted['posts_per_page'] > 20 ? 20 : intval( $posted['posts_per_page'] ), #self::properties( 'posts_per_page' ),
                    'post_type'             => 'users', #self::properties( 'post_type' ),
                    'role__not_in'          => 'Administrator',
                    'meta_key'              => self::properties( 'meta_key' ),
                    'orderby'               => self::properties( 'order_by' ),
                    'order'                 => self::properties( 'order' ) 
                );

            break ;

            // default wp_posts query ##
            case "posts" :
            default :

                // h::log( \get_option( 'sticky_posts', [] ) );

                // get sticky posts just for the registered post_type ##
                $posts = \get_posts([
                    'post_type'         => $posted['post_type'],
                    'posts_per_page'    => -1, // all of them ##
                    "post_status"       => "publish",
                    "post__in"          => \get_option( 'sticky_posts', [] )
                ]);

                // get IDs ##
                $post__in = \wp_list_pluck( $posts, 'ID' );

                // h::log( $post__in );

                // now match ID's to the sticky posts ##
                #$post__in = array_intersect( $posts_ids, \get_option( 'sticky_posts', [] ) );

                // h::log( $post__in );
                
                // build args list ##
                $args = array(
                    'post__in'              => $post__in, // self::array_truncate( \get_option( 'sticky_posts' ), 12 ) ##
                    'posts_per_page'        => $posted['posts_per_page'] > 20 ? 20 : intval( $posted['posts_per_page'] ), #
                    'post_type'             => $posted['post_type'], 
                    'ignore_sticky_posts'   => true, // hmmm ##
                    "post_status"           => "publish"
                );

            break ;

        }

        // return filterable values ##
        return \apply_filters( 'q/search/empty_args/', $args );

    }




    public static function get_posted()
    {

        // h::log( $_POST );

        // grab posted data ##
        $posted['table']              = isset( $_POST['table'] ) ? $_POST['table'] : self::properties( 'table' );
        $posted['application']        = isset( $_POST['application'] ) ? $_POST['application'] : self::properties( 'application' );
        $posted['device']             = isset( $_POST['device'] ) ? $_POST['device'] : self::properties( 'device' );
		$posted['post_type']          = isset( $_POST['post_type'] ) ? 
										explode( ',', $_POST['post_type'] ) : 
										explode( ',', self::properties( 'post_type' ) );
        $posted['posts_per_page']     = isset( $_POST['posts_per_page'] ) ? (int)$_POST['posts_per_page'] : self::properties( 'posts_per_page' );
        // $posted['class']              = isset( $_POST['class'] ) ? $_POST['class'] : self::properties( 'class' ) ;
        $posted['order']              = isset( $_POST['order'] ) ? $_POST['order'] : self::properties( 'order' ) ;
        $posted['order_by']           = isset( $_POST['order_by'] ) ? $_POST['order_by'] : self::properties( 'order_by' ) ;
        $posted['category_name']      = isset( $_POST['category_name'] ) ? $_POST['category_name'] : self::properties( 'category_name' ) ;
        $posted['author_name']        = isset( $_POST['author_name'] ) ? $_POST['author_name'] : self::properties( 'author_name' ) ;
        $posted['tag']                = isset( $_POST['tag'] ) ? $_POST['tag'] : self::properties( 'tag' ) ;

        // h::log( isset( $_POST['filters'] ) ? $_POST['filters'] : '' );

        // $filters = [];
        if ( isset( $_POST['filters'] ) ) {
        
            parse_str( $_POST['filters'], $filters );
            $posted['_POST_filters'] = $filters;

        } else {

            $posted['_POST_filters'] = [];

        }

        switch ( self::properties( 'table' ) ) {
            
            // allow for searching users ##
            case "users" :
            
                // allow for custom data ##
                $posted['post_type']          = 'users'; // force ##

            break ;

            // default wp_posts query ##
            case "posts" :
            default :

                // allow for custom data ##

            break ;

        }

        // return filterable values ##
        return \apply_filters( 'q/search/get_posted/', $posted );

    }





    public static function get_filters( $posted = null )
    {
        // sanity ##
        if ( is_null( $posted ) ){

            return false;

        }


        // h::log( $posted['_POST_filters'] );

        // blank ##
        // $filters = '';
        $filters = 
            isset( $posted['_POST_filters'] ) ? 
            array_filter( $posted['_POST_filters'] ) : 
            false ;

        // if $filters is an rmpty array, let's kick back false  #
        if ( empty( $filters ) ) {

            // h::log( 'Nothing interesting in filters..' );

            $filters = false;

        } 

        // counter ##
        // $c = 0;

        // switch over user cases ##
        switch ( self::properties( 'table' ) ) {
            
            // allow for searching users ##
            case "users" :
            
                // allow for custom data ##

            break ;

            // default wp_posts query ##
            case "posts" :
            default :

                // allow for custom data ##

            break ;

        }

        // return filterable values ##
        return \apply_filters( 'q/search/get_filters/', $filters );

    }





    public static function do_query( $args = null )
    {

        // sanity ##
        if ( is_null( $args ) ) {
            
            // h::log( '$args empty...' );

            return false;

        }

        #h::log( 'table: '.self::properties( 'table' ) );

        switch ( self::properties( 'table' ) ) {
            
            // allow for searching users ##
            case "users" :
            
                // query user data ##
                self::wp_user_query( $args );

            break ;

            // default wp_posts query ##
            case "posts" :
            default :

                // query posts table ##
                self::wp_query( $args );

            break ;

        }

    }



    
    public static function wp_user_query( $args )
    {

        // check args ##
        #h::log( $args );

        // new WP_Query ##
        $qs_query = new \WP_User_Query( $args );

        // log SQL ##
        // h::log( $qs_query->request );

        // Get the results ##
        $users = $qs_query->get_results();

        // Check for results ##
        if ( ! empty( $users ) ) {

            // show results count ##
            render::count_results( $qs_query->get_total() );
            #h::log( 'Count: '.$qs_query->get_total() );

            // loop over results ##
            foreach ( $users as $user_row ) {

                // get all the user's data
                $user = \get_userdata( $user_row->ID );

                if ( 
                    class_exists( $args['class'] ) 
                    && method_exists( $args['class'], 'q_search' ) 
                ) {

                    #h::log( "class found.." );

                    // call class method ##
                    call_user_func_array (
                        array( $args['class'], "q_search" ), 
                        array( 
                            $user, // WP_User object ##
                            self::properties(), // internal posted and filtered args ##
                        )
                    );

                } else {

					// internal result display ##
                    render::result();

                } // template ##

                // iterate ##
                #$i++;

            } // foreach ##

        } else {

            #h::log( 'No results found, we need to show that..' );

            render::no_results();

        }

        if( $args['pagination'] ) {
            
            render::pagination( $qs_query->get_total(), $args['number'], self::get_posted() );

        }

        // reset global post object ##
        \wp_reset_query();

    }




    public static function wp_query( $args )
    {

        // new WP_Query ##
		$qs_query = new \WP_Query( $args );
        
        // h::log( $args );

        // parse args ##
        // $qs_query->query( $args );
        // count ##
        $count = 0;
        $ids = [];

        if ( $qs_query->have_posts() ) {

            // log ##
            // h::log( 'Posts found, continue..' );

            // show results count ##
            render::count_results( $qs_query->found_posts );
            // h::log( $qs_query );
            // h::log( 'Count: '.$qs_query->found_posts );

			// we need to move to q/render here ##

            // h::log( $args );

            while ( 
                $qs_query->have_posts() 
                && $count < $args['posts_per_page']    
            ) {

                $qs_query->the_post();

                if ( 
                    in_array( \get_the_ID(), $ids ) 
                ) {

                    // h::log( 'ID already listed' );

                    continue;

                }

                $ids[] = \get_the_ID();

                if ( $count > $args['posts_per_page'] ) {

                    // h::log( 'Too many rows...' );

                    break;

                }

                // iterate ##
                $count ++;

                // if ( 
                //     class_exists( $args['class'] ) 
                //     && method_exists( $args['class'], 'q_search' ) 
                // ) {

                //     // h::log( "class found.." );

                //     // call class method ##
                //     call_user_func_array (
                //         array( $args['class'], "q_search" ),
                //         array(
                //             \get_the_ID(),
                //             self::properties()
                //         )
                //     );

                // } else {

                    // h::log( 'default template..' );

                    render::result();

                // } // template ##

            } // while loop ##

        } else {

            // h::log( 'No results found, we need to show that..' );

            render::no_results();

        }

        // h::log( 'Looped: '.$count );

        // reset global post object ##
        \wp_reset_query();

        // build pagination ##
        if( $args['pagination'] ) {

			// h::log( 'd:>loading pagination..' );

            // build pagination ##
            render::pagination( $qs_query->found_posts, $args['posts_per_page'], self::get_posted() );

        }

    }



	public static function get_control( $args = null, $field = null ){

		// h::log( $args[$field] );

		// sanity ##
		if ( 
			! isset( $args ) 
			|| ! is_array( $args ) 
			|| ! isset( $field ) 
			|| ! isset( $args[$field] )
		) {

			// h::log( 'Error in passed $args' );

			return '1';

		}

		// kick back arg ##
		return $args[$field];

	}



    /**
     * AJAX callback method to query and render results
     * 
     * @since       1.7.0
     * @return      string      HTML for results
     */
    public static function query( $load = null ) 
    {
		
		// h::log( self::properties( 'control', 'array' ) );
		// $control = is_null( $control ) ? self::properties( 'control', 'array' ) : $control ;

		// if ( 
		// 	'0' === $control = self::get_control( $load, 'load' )
		// ) {

		// 	// h::log( $control );
		// 	// h::log( 'Load Blank' );

		// 	return render::load_empty( self::properties( 'load_empty', 'array' ) );

        //     // die();

		// }

        // define options ##
        $pagination = self::properties( "pagination" );

        // post data passed, so update values ##
        if( $_POST ){

            // secure with a nonce ##
            $nonce = \check_ajax_referer( 'q-search-nonce', false, false );
            #h::log( 'nonce: '.$nonce );

        }

        // grab post data ##
		$posted = self::get_posted();
		// h::log( $posted );

        // get posted filters ##
        $filters = self::get_filters( $posted );
        // h::log( $filters );

        // build args list ##
        $args = self::default_args( $posted );
        // h::log( $args );

        // check if we should progress ##
        if ( 
            empty( $filters ) 
            #&& ! $args['load'] // not first load ##
        ) {

            // seems not ##
            // render::no_results(  __( 'Please select a filter.', 'q-search' ) ); // show the sad face :(

			// h::log( '$Filters were empty..' );

			if ( 
				'0' === $control = self::get_control( $load, 'load' )
			) {
	
				// h::log( $control );
				// h::log( 'Load Blank' );
	
				return render::load_empty( self::properties( 'load_empty', 'array' ) );
	
				// die();
	
			}
			
            // get args ##
            $args = self::empty_args( $posted );
            
            // nope ##
            $pagination = \apply_filters( 'q/search/pagination/load/', false );

            // h::log( 'Pagination: '.$pagination );

        }

        // no posted data - in load state ##
        if ( ! $_POST ) {

            // get args ##
            $args = self::empty_args( $posted );

			h::log( 'No $_POST object available' );
			
			if ( 
				'0' === $control = self::get_control( self::properties( 'control', 'array' ), 'empty' )
			) {
	
				// h::log( $control );
				// h::log( 'Empty Blank' );
	
				return render::load_empty( self::properties( 'load_empty', 'array' ) );

            	die();
	
			}

            // nope ##
            $pagination = \apply_filters( 'q/search/pagination/empty/', false );;

            // h::log( 'Pagination: '.$pagination );

            #h::log('Running load state query');
            // h::log( $args );

        } else {

			// h::log( '$_POST object IS available' );

			if( ! isset( $args['tax_query'] ) ) {

				$args['tax_query'] = [];
	
			}

            // check if the queried_object is good ##
            if( $_POST['queried_object'] != 'qs_null' ) {

                // explode queried_object string ##
                $queried_object = explode( '##', $_POST['queried_object'] );

                if ( isset( $queried_object[1] ) ) {

                    // push array items into a tax_query ##
                    array_push($args['tax_query'],
                        array(
                                'taxonomy'  => $queried_object[0]
                            ,   'field'     => 'id'
                            ,   'terms'     => $queried_object[1]
                        )
                    );

                }
            }

        }

        // add in category_name, if in query_var and not set in tax_query... ##
        if ( 
            isset( $posted['category_name'] )
            && ! $filters
        ) {

            $args['category_name'] = $posted['category_name'];

        }

        // add in author_name, if in query_var ##
        if ( 
            isset( $posted['author_name'] ) 
            && ! $filters
        ) {

            $args['author_name'] = $posted['author_name'];

        }

        // add in tag, if in query_var ##
        if ( 
            isset( $posted['tag'] ) 
            && ! $filters
        ) {

            $args['tag'] = $posted['tag'];

        }

        // check if paging value passed, if so add to the query ##
        if ( isset ( $_POST['paged'] ) ) {
            $args['paged'] = $_POST['paged'];
        } else {
            $args['paged'] = 1;
        }

        #h::log( $filters );

        // filters ##
        #$this->date_range = false;
        if ( ! empty( $filters ) ){

            // h::log( 'Filters passed: ' );
            // h::log( $filters );

            // add all the filters to tax_query ##
            foreach( $filters as $key => $value ){

                // // h::log( 'key: '.$key );

                // data filtering ##
                if ( $key == 'date' ) {

                    #$this->date_range = $value;
                    $args['date_query'] = array(
                        array(
                            'column' => 'post_modified_gmt',
                            'after'  => str_replace( "-", " ", $value ),
                        ),
                    );


                // authoer filtering ##
                } elseif ( $key == 'author' ) {

                    $args['author'] = $value;

                // text search filtering ##
                } elseif ( $key == 'searcher' ) {

                    if ( 'users' == self::properties( 'table' ) ) {
                        
                        #h::log( 'searcing by username: '.$value );
                        $args['search'] = '*'.$value.'*';

                    } else {

                        $args['s'] = $value;

                    }

                // user_meta ##
                } elseif ( 
                    $key == 'user_meta' 
                    && 'users' == self::properties( 'table' )
                    && $user_meta = \apply_filters( 'q/search/user_meta', false )
                ) {

                    // get user_meta settings ##
                    $user_meta = \apply_filters( 'q/search/user_meta', false );
                    
                    $args['meta_key'] = $user_meta['field'];
                    $args['meta_value'] = $value;

                // taxonomy filtering ##
                } else {

                    #pr( $value );

                    if ( ! is_array( $value ) ) { $value = explode(' ', $value); }

                    // h::log( $value );

                    foreach( $value as $id ){
                        array_push( $args['tax_query'],
                            array(
                                'taxonomy'  => $key,
                                'field'     => 'id',
                                'terms'     => $id
                            )
                        );
                    }

                }

            }

        }

        // h::log( $args );

        // inserts a "AND" relation if more than one array in the tax_query ##
        if( isset( $args['tax_query'] ) && count( $args['tax_query'] ) > 1 ) {

            $args['tax_query']['relation'] = 'AND';

        }

        #h::log($args);
        if ( self::properties( "date_range" ) ) {

            #\add_filter( 'posts_where', [ get_class(), 'filter_where' ] );

        }

        // grab extra data to pass to query ##
        // $args['class'] = $posted['class'];
        $args['pagination'] = $pagination;

        // h::log( $args );

        // do query ##
        self::do_query( $args );

        // remove filter - might need to do this dynamically ##
        if ( self::properties( "date_range" ) ) {
        
            #\remove_filter( 'posts_where', array ( 'core', 'q_ajax_filter_where' ) );
            
        }

        // called from ajax - so needs to die ##
        if ( $_POST ) {

            //echo 'nada';
            die();

        }
        
    }




    public static function get_taxonomy( $taxonomy = null )
    {

        if ( is_null( $taxonomy ) ) {

            h::log( 'No tax sent.' );

            return false;

        }

        if ( $taxonomy == 'date' ) {

            $the_tax_name = __( 'Date Range', 'q-search' );

            $terms = array (
                '1' => array (
                    'slug' => 'today',
                    'term_id' => '1',
                    'name' => __( 'Today', 'q-search' )
                ),
                '2' => array (
                    'slug' => 'one-week',
                    'term_id' => '7',
                    'name' => __( 'One Week', 'q-search' )
                ),
                '3' => array (
                    'slug' => 'one-month',
                    'term_id' => '31',
                    'name' => __( 'One Month', 'q-search' )
                ),
                '4' => array (
                    'slug' => 'one-year',
                    'term_id' => '365',
                    'name' => __( 'One Year', 'q-search' )
                ),
                '5' => array (
                    'slug' => 'five-year',
                    'term_id' => '1825',
                    'name' => __( 'Five Years', 'q-search' )
                )
            );

            // cast to object ##
            $terms = core\method::array_to_object($terms);

        } elseif ( $taxonomy == 'author' ) {

            $the_tax_name = __( 'Authors', 'q-search' );

            $terms = array(
                '0' => array ()
            );

            $authors = \get_users ('role=contributor');
            foreach ( $authors as $author ) {

                // skip authors with zero posts ##
                $numposts = \count_user_posts($author->ID);
                if ( $numposts < 1 ) continue;

                $terms[] = array (
                    'slug' => $author->user_login,
                    'term_id' => $author->ID,
                    'name' => $author->display_name ? $author->display_name : $author->user_login
                );
            }

            // cast to object ##
            #pr($terms);
            $terms = core\method::array_to_object($terms);

        //     #pr("first_key : {$first_key}");

        //     // get tax name ##
        //     $the_tax = get_taxonomy( $terms[$first_key]->taxonomy );
        //     #pr( $the_tax->labels->singular_name );
        //     #$the_tax_name = $the_tax->labels->singular_name;
        //     $the_tax_name = isset( $the_tax->labels->q_search_name ) ? $the_tax->labels->q_search_name : $the_tax->labels->singular_name ;
        //     $the_tax_label = isset( $the_tax->labels->q_search_label ) ? $the_tax->labels->q_search_label : $the_tax->labels->singular_name ;

        } else {

            // h::log($taxonomy);
            $terms = \get_terms( 
                array(
                    'taxonomy'      => trim($taxonomy),
                    // 'hierarchical'  => true,
                    'orderby'       => 'name',
                    'order'         => 'ASC',
                    'hide_empty'    => 1
                )
            );

            if ( ! isset( $terms ) || empty ( $terms ) || \is_wp_error( $terms ) ) {

                // h::log($terms);
                // h::log( "term empty or error - skipping {$taxonomy}" );
                return false;

            }

            reset($terms);
            $first_key = key($terms);

            // nothing cooking in this taxonomy ##
            if ( ! $terms[$first_key] ) {

                #echo "no first key - skipping {$taxonomy}";
                #pr($terms[$first_key]);
                return false;

            }

            #pr("first_key : {$first_key}");

            // get tax name ##
            $the_tax = \get_taxonomy( $terms[$first_key]->taxonomy );
            #pr( $the_tax->labels->singular_name );
            #$the_tax_name = $the_tax->labels->singular_name;
            $the_tax_name = isset( $the_tax->labels->q_search_name ) ? $the_tax->labels->q_search_name : $the_tax->labels->singular_name ;
            $the_tax_label = isset( $the_tax->labels->q_search_label ) ? $the_tax->labels->q_search_label : $the_tax->labels->singular_name ;

        }

        // kick it back ##
        return array(
            'terms' => $terms,
            #'tax'       => $the_tax,
            'name'  => $the_tax_name,
            'label' => $the_tax_label
        );

    }


    /**
    * Paging Info
    *
    * @since   1.7.0
    * @link    http://stackoverflow.com/questions/8361808/limit-pagination-page-number
    * @return  array   data for paging
    */
    public static function get_pagination( $total_posts, $posts_per_page, $page_number )
    {

		// h::log( $total_posts );

		// // no data - no pagination ##
		// if ( 
		// 	! $total_posts 
		// 	|| 0 == $total_posts
		// ) {

		// 	return false;

		// }

        $pages = ceil( $total_posts / $posts_per_page ); // calc pages

        $data = array(); // start out array
        $data['offset']         = ( $page_number * $posts_per_page ) - $posts_per_page; // what row to start at -- was ["si"]
        $data['pages']          = $pages;                   // add the pages
        $data['page_number']    = $page_number;               // Whats the current page

        return $data; // return the paging data

    }



    /**
    * Check if there are any posts available to search
    *
    * @since       0.0.5
    * @return      Boolean
    */
    public static function has_posts()
    {

        // test settings ##
		// h::log( self::properties("post_type") );
		
		// Get any existing copy of our transient data
		if ( false === ( $test = \get_site_transient( 'q_search_has_posts' ) ) ) {

			switch ( self::properties( "post_type" ) ) {

				case "users" :

					// build search args ##
					$args = array( 
						'role__not_in'  => 'Administrator',
						'number'        => 1
					);

					$test = \get_users( $args );

				break ;

				default :

					// build search args ##
					$args = array( 
						'post_type'         => self::properties( "post_type" ),
						'post_per_page'     => 1
					);

					$test = \get_posts( $args );

				break ;

			}

			\set_site_transient( 'q_search_has_posts', $test, 24 * HOUR_IN_SECONDS );

		}

        // h::log( $test );

        // has or not ##
        return $test ? true : false ;

    }




    /** 
    * Create a new filtering function that will add our where clause to the query
    *
    * @since    2.0.0
    * @return   String
    */
    public static function filter_where( $where = '' ) {

        // nothing to do ##
        if ( ! self::properties( "date_range" ) ) { return false; }

        // get highest value, as that's what counts ##
        #pr($this->date_range);
        $key = array_search( max ( self::properties( "date_range" ) ), self::properties( "date_range" ) );
        $range = self::properties( "date_range" )[$key];
        #pr($range);
        $date = getdate();
        $cutoff = date('Y-m-d', mktime( 0, 0, 0, $date['mon'], $date['mday'] - $range, $date['year']));
        $where .= " AND post_date > '$cutoff'";
        #pr( $where );
        #wp_die("where filtered: ".$where);

        return $where;

    }
    


    /**
    * Caste Array to Object
    *
    * @param type $array
    * @return \stdClass|boolean
    */
    public static function array_to_object( $array )
    {

        if( ! is_array( $array ) ) {

            return $array;

        }

        $object = new stdClass();

        if ( 
            is_array( $array ) 
            && count( $array ) > 0
        ) {
        
            foreach ( $array as $name => $value ) {
        
                $name = strtolower( trim( $name ) );
        
                if ( ! empty( $name ) ) {

                    $object->$name = self::array_to_object( $value );

                }

            }

            return $object;
        }
        else {
            return false;
        }

    }



    /**
    * Truncate an array to a fixed length
    * @param  array  $array [description]
    * @param  [type] $left  [description]
    * @param  [type] $right [description]
    * @return [type]        [description]
    */
    public static function array_truncate( array $array, $total )
    {

        // slice it up ##
        $array = array_slice( $array, - $total, $total );

        // kick it back ##
        return $array;

    }



    public static function multi_array_key_exists( array $array, $key ) {

        h::log( $array );

        // is in base array?
        if ( array_key_exists( $key, $array ) ) {

            h::log( 'Key found top level: '.$key ) ;

            return true;

        }
    
        // check arrays contained in this array
        foreach ( $array as $element ) {

            if ( is_array( $element ) ) {

                if ( self::multi_array_key_exists( $element, $key ) ) {

                    h::log( 'Key found in deep: '.$key ) ;

                    return true;

                }
            }
    
        }
    
        h::log( 'Key NOT found: '.$key ) ;

        return false;

    }



}
