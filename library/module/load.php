<?php

namespace q\module;

use q\core\core as core;
use q\core\helper as helper;
use q\core\config as config;
use q\controller\generic as generic;

// load it up ##
// \q\module\load::__run();

class load extends \Q {
    
    static $args = [];

    public static function __run( $args = null )
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// h::log( core\option::get('bs_toggle') );
		if ( 
			! isset( core\option::get('module')->load )
			|| true !== core\option::get('module')->load 
		){

			h::log( 'd:>Load is not enabled.' );

			return false;

		}

		// ajax hooks ##
		\add_action( 'wp_ajax_q_load', [ get_class(), 'ajax' ] ); // wp_ajax_{action}
		\add_action( 'wp_ajax_nopriv_q_load', [ get_class(), 'ajax' ] ); // wp_ajax_nopriv_{action}

        // merge in defaults ##
        self::$args = array_merge( self::args(), $args );

        // add styles ##
        \add_action( 'wp_head', [ get_class(), 'css' ], 100 );

        // add scripts ##
        \add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_scripts' ] );

	}


	

	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['load'] = 'Q ~ AJAX Post Loader';

		// make it selected ##
		$field['default_value'][0] = 'load';
		
		return $field;

	}

    


    public static function hooks( $args = null )
    {

        // if ( \is_admin() ) {

            // ajax hooks ##
            \add_action( 'wp_ajax_q_load', [ get_class(), 'ajax' ] ); // wp_ajax_{action}
            \add_action( 'wp_ajax_nopriv_q_load', [ get_class(), 'ajax' ] ); // wp_ajax_nopriv_{action}

        // }

    }
    


    /**
    * Default args
    *
    * @since    2.0.0
    */
    public static function args( $key = null )
    {

        // define ##
        self::$args = array(
            'security'          => \wp_create_nonce( "q-load-nonce" ), // nonce ##
            'ajaxurl'           => \site_url() . '/wp-admin/admin-ajax.php',
            'view'              => false,
            'method'            => false,
            'target'            => '.q_load > ul.results', // DOM selector ##
            'trigger'           => '.q_load > .button', // trigger ##
            'load_more'         => \__( 'Load More', self::text_domain ),
            'no_more_posts'     => \__( "That's all folks :)", self::text_domain ),
            'post_type'         => 'post', ## 'post'
            'posts_per_page'    => get_option( 'posts_per_page' ), // how many we have on load ##
            'orderby'           => 'date',
            'order'             => 'DESC',
            'meta_key'          => '',
            'meta_value'        => '',
            'meta_type'         => '',
            'taxonomy'          => false,
            'tax_term'          => 'false',
            'exclude'           => [], // no posts loaded already
            'markup'            => '<li>%title%</li>',
            'handle'            => [],
            'holder'            => [],
            'date_format'       => 'F j, Y', // should pull from Q config ##
            'callback'          => false
        );

        // return ##
        return
            ( ! is_null( $key ) && isset( self::$args[$key] ) ) ?
            self::$args[$key] :
            self::$args ;

    }





    public static function render( $args = null )
    {

        // merge in updated defaults ##
        self::$args = array_merge( self::$args, $args );

        // helper::log( self::$args );

        // sanity ##
        if ( ! self::$args ) {

            helper::log( 'Sanity check failed...' );

            return false;

        }

        #helper::log( 'Loading up the loader' );
        
?>
        <div class="q_load">
            <ul class="results"></ul>
            <span class="button q_load_button" data-q-load-exclude="<?php echo json_encode( self::$args['exclude'] ); ?>">
                <?php echo self::$args['load_more']; ?>
            </span>
        </div>
<?php

        // done ##
        return true;

    }




    /**
    * Compile JS for AJAX callback
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function wp_enqueue_scripts()
    {

        // register script ##
        \wp_register_script( 'q_load', helper::get( "theme/javascript/q.load.js", 'return' ), array('jquery'), self::version, true );
    
        // pass params ##
        \wp_localize_script( 'q_load', 'q_load_params', self::$args );
    
        // call it up ##
        \wp_enqueue_script( 'q_load' );

    }



    
    /**
    * AJAX callback method
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function ajax()
    {

        // Nonce check ##
        \check_ajax_referer( 'q-load-nonce', 'security' );

        #helper::log( '$_POST' );

        // empty array ##
        $args = [];

        // grab $_POST data ##
        foreach( self::args() as $key => $value ) {

            $args[$key]     = isset( $_POST[$key] ) ? $_POST[$key] : self::args($key);

        }

        // format ##
        $args['exclude']            = $args['exclude'] ? json_decode( $args['exclude'] ) : [];
        $args['view']               = stripslashes ( $args['view'] );
        $args['tax_term']           = \sanitize_key( $args['tax_term'] );
        #helper::log( 'Tax term: '.$args['tax_term'] );

        // do we have a taxonomy - if so, format a tax_query correctly ##
        if ( 
            isset( $args['taxonomy'] ) 
            && isset( $args['tax_term'] ) 
            && 'false' != $args['taxonomy']
            && 'false' != $args['tax_term']
            && \get_term_by( 'slug', $args['tax_term'], $args['taxonomy'] ) // make sure we're dealing with a real term ##
        ) {

            $args['tax_query']  = array(
                array(
                    'taxonomy'  => $args['taxonomy'],
                    'field'     => 'slug',
                    'terms'     => $args['tax_term']
                )
            );

        }

        #helper::log($args);
        
        $posts = \get_posts( $args );
    
        #helper::log ($posts );

        // our loop
        if ( $posts ) {
            
            // raw markup ##
            $markup = '';

            foreach ( $posts as $post ) { 

                if ( ! $post ) { 
                    
                    helper::log( 'post missing...' );

                    continue;

                }

                // add to exclusion array ##
                $args['exclude'][] = $post->ID;

                // prepare WP ##
                \setup_postdata( $post );

                #helper::log( 'view::method: '.$args['view'].' / '.$args['method'] );
                
                // check if method exists in 'Q_Template' ##
                if ( 
                    isset( $args['view'] ) && isset( $args['method'] )
                    && is_callable( array( $args['view'], $args['method'] ) ) 
                ) {

                    // call template method ##
                    $returned_markup = call_user_func_array (
                            array( $args['view'], $args['method'] )
                        ,   array( $post, $args, $args['markup'] )
                    );
                
                    // helper::log( $returned_markup );

                    // append ##
                    $markup .= stripslashes( $returned_markup );

                // not in theme, so use plugin default verion ##
                } else {

                    // add item ##
                    $markup .= self::row( $post, $args );

                } // templates
                
            } // loop ##

            \wp_reset_postdata();

            // compile
            $return = array(
                'result'    => 'success',
                'exclude'   => json_encode( $args['exclude'] ),
                'markup'    => $markup
            );

            // kick it back ##
            echo json_encode( $return );

        } else {

            // compile
            $return = array(
                'result'    => 'failed',
                'exclude'   => json_encode( $args['exclude'] ),
                'markup'    => false
            );

            // kick it back ##
            echo json_encode( $return );

        }

        #helper::log( $return );
        
        // all ajax calls must die!! :( ##
        die; 

    }



    /*
    * Default row result render
    *
    * @since    2.0.0
    */
    public static function row( $post, $args ) {

        return str_replace( '%title%', \get_the_title( $post ), self::$args["markup"] );

    }



    /**
    * Add CSS inline
    *
    * @since    2.0.0
    * @return   String
    */
    public static function css( )
    {

?>
    <style>
        .q_load{
            float: left;
            width: 100%;
            clear: both;
            text-align: center;
        }
        .q_load .button{
            background-color: #ddd;
            border-radius: 2px;
            display: block;
            text-align: center;
            font-size: 14px;
            font-size: 0.875rem;
            font-weight: 800;
            letter-spacing:1px;
            cursor:pointer;
            text-transform: uppercase;
            padding: 10px 0;
            transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out, color 0.3s ease-in-out;  
        }
        .q_load .button:hover{
            background-color: #767676;
            color: #fff;
        }
    </style>
<?php

    }


}
