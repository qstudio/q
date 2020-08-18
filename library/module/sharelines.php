<?php

namespace q\module;

use q\core\helper as h;
use q\core;
use q\strings;

/**
 * Sharelines Quasi Widget
 *
 * @package WordPress
 * @since 2.0.0
 *
 */

// load it up ##
\q\module\sharelines::__run();

class sharelines extends \Q {

    // plugin properties ##
    public static $properties = false;

    public static function __run()
    {

		// add extra options in widget select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->sharelines )
			|| true !== core\option::get('module')->sharelines 
		){

			// h::log( 'd:>Tab is not enabled.' );

			return false;

		}

        // add acf fields ##
		\add_action( 'acf/init', function() { \q\plugin\acf::add_field_groups( self::add_field_groups() ); }, 1 );

		// add js to footer ##
		/*
		\add_action( 'wp_footer', function(){
			\q\asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
			]);
		}, 3 );
		*/
		\add_action( 'wp_footer', [ get_class(), 'javascript' ], 1000, 0 );

	}



	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['sharelines'] = 'Sharelines';

		// make it selected ##
		// $field['default_value'][0] = 'bs_tab';

		// kick back ##
		return $field;

	}



    /**
    * Deactivation callback method
    * 
    * @since       0.1
    * @return      void
    */
    public static function deactivation_hook()
    {
        
        if ( ! \current_user_can( 'activate_plugins' ) ) {
            
            return;
            
        }
        
        $plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
        \check_admin_referer( "deactivate-plugin_{$plugin}" );

        // check if we have any posts tagged ##
        if ( $posts = self::get_posts_by_meta( 
                array( 
                    'meta_key'      => '_q_sharelines', 
                    'meta_value'    => '1',
                ) 
            ) 
        ) {
            
            // loop over each post ##
            foreach( $posts as $post ) {
                
                // delete post meta marker ##
                \delete_post_meta( $post->ID, '_q_sharelines' );
                
            }
            
            #pr( $posts );
            #exit( var_dump( $_GET ) );
            
        }
        
    }


        
    /**
    * Get Post object by post_meta query
    *
    * @use         $post = get_post_by_meta( array( meta_key = 'page_name', 'meta_value = 'contact' ) )
    * @since       1.0.4
    * @return      Object      WP post object   
    */
    public static function get_posts_by_meta( $args = array() )
    {

		// h::log( 't:>move to query::posts_by_meta()' );

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args );

        // grab page - polylang will take take or language selection ##
        $args = array(
            'meta_query'        => array(
                array(
                    'key'       => $args->meta_key,
                    'value'     => $args->meta_value
                )
            ),
            'post_type'         => \get_post_type(),
            'posts_per_page'    => -1
        );

        // run query ##
        $posts = \get_posts( $args );

        // check results ##
        if ( ! $posts || \is_wp_error( $posts ) ) return false;

        // test it ##
        #pr( $posts[0] );

        // kick back results ##
        return $posts;

    }



    /**
    * Load up ACF fields
    * 
    * @since       1.0.0
    */
    public static function add_field_groups()
    {

		// define field groups - exported from ACF ##
        $groups = array (

            'q_option_test'   => array(
                'key' => 'sharelines',
				'title' => 'Sharelines',
				'fields' => array(
					array(
						'key' => 'field_sharelines_repeater',
						'label' => 'sharelines',
						'name' => 'sharelines',
						'type' => 'repeater',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'collapsed' => 'field_shareline_test',
						'min' => 0,
						'max' => 4,
						'layout' => 'table',
						'button_label' => 'Add Shareline',
						'sub_fields' => array(
							array(
								'key' => 'field_shareline_test',
								'label' => 'Text',
								'name' => 'text',
								'type' => 'textarea',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'maxlength' => 140,
								'rows' => 2,
								'new_lines' => '',
							),
						),
					),
				),
				'location' => array(
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'post',
						),
					),
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'page',
						),
					),
					array(
						array(
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'question',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => true,
				'description' => '',
			)

        );

		// h::log( $groups );
		return $groups;

    }



    public static function config()
    {

        // new array ##
        $config = array();

        // values ##
        // $config['title'] = \apply_filters( 'q/module/sharelines/title', \__( 'Share', 'q-textdomain' ) );

        $config['facebook'] = \apply_filters( 'q/module/sharelines/facebook', '1055454871138781' );

        // check ##
        #h::log( $config );

        // populate static property ##
        return self::$properties = $config;

    }


    /**
    * Load plugin properties
    *
    * @since    2.0.0
    * @return   Array
    */
    public static function properties( $key = null, $return = 'string' )
    {

        #h::log( 'called for key: '.$key );

        // properties not defined yet ##
        if ( ! self::$properties ) {

            #h::log( 'properties empty, so loading fresh...' );
            #h::log( self::$passed_args );

            self::config();

        }

        #h::log( self::$properties );

        // kick back specified key or whole array ##
        return 
            ( ! is_null( $key ) && isset( self::$properties[$key] ) && array_key_exists( $key, self::$properties ) ) ? 

            // single array item ##
            ( is_array ( self::$properties[$key] ) && 'string' == $return ) ? 
            implode( ",", self::$properties[$key] ) : // flat csv ##
            self::$properties[$key] : // as array ##
            
            // whole thing ##
            self::$properties ;

    }



    /**
    * Validate that we have all the required data
    *
    * @since    2.0.0
    **/
    public static function validate()
    {

        // get stored properties ##
        $array = self::properties();

        #h::log( $array );

        // reject if missing required data ##
        if ( 
            empty( $array )
            || false == $array
            || ! isset( $array['facebook'] )
            || false == $array['facebook']
        ) {

            h::log( 'Missing required config.' );

            return false;

        }

        // ok ##
        return true;

    }


        
    /**
    * Get data from ACF
    * 
    * @param       string      $string
    * @since       0.1
    * @return      string
    */
    public static function get_sharelines()
    {
        
        global $post;
        #pr( $post );
        
        // no post ##
        if ( ! $post ) { return false; }
        
        // no sharelines found ##
        if ( ! self::has_sharelines() ) { return false; }
        
        // set-up a new empty array ##
		$array = array();
		
		$count = 0;
        
        while( \have_rows( 'sharelines') ) {
            
            // set-up the row ##
            \the_row(); 

            // grab the store text ##
			$array[$count]['text'] = \esc_html( \get_sub_field( 'text' ) );
			$array[$count]['short_text'] = \esc_js( \q\strings\method::chop( \get_sub_field( 'text' ), 140 ) );
			$array[$count]['icons'] = self::icons( \esc_js( \get_sub_field( 'text' ) ) );

			// iterate ##
			$count ++;

        }
        
        // kick it back ##
        return $array;
        
    }
    


    
    /***
    * Check if the current post has any sharelines
    * 
    * @since       0.1
    * @return      Boolean
    */
    public static function has_sharelines()
    {
        
        global $post;
        #pr( $post );
        
        // no post ##
        if ( ! $post ) { return false; }
        
        // check for marker ##
        if ( ! $sharelines = \get_post_meta( $post->ID, 'sharelines' )  ) {
            
            #pr( "kicked 1" );
            return false;
            
        }
        
        if( ! \have_rows( 'sharelines' ) ) {
            
            #pr( "kicked 2" );
            return false;
            
        }
        
        // ok to continue ##
        return true;
        
    }
        
        
    /**
    * Check for and include icons for social media sharing options
    * 
    * @since       0.4
    * @return      String      HTML
    */
    public static function icons( $shareline = null )
    {
		
		/*
        // get stored properties ##
        $array = self::properties();
        
        // reject if missing required data ##
        if ( 
            empty( $array )
            || false == $array
        ) {

            h::log( 'Missing required config.' );

            return false;

		}
		*/

        // test stored settings ##
        #h::log( $array );
        
        // array to test against ##
        $networks = array(
            'facebook'      => array (
                'class'     => 'facebook-share-button greyscale'
            ),
            'twitter'       => array (
                'class'     => 'twitter-share-button greyscale',
                'data-text' => $shareline,
                'via'       => '_qstudio', // @TODO -- make filterable ##
                'href'      => 'https://twitter.com/share',
                'url'       => \wp_get_shortlink(),
                'counturl'  => \get_permalink()
            )
		);
		
		ob_start();
        
        foreach ( $networks as $key => $value ) {
        
            // twitter ##
            if ( 'twitter' == $key ) {
                
?>
                <a href="<?php echo $value["href"]; ?>?text=<?php echo \esc_js($shareline); ?>&via=<?php echo $value["via"]; ?>&data-url=<?php echo $value["url"]; ?>&data-counturl=<?php echo $value["counturl"]; ?>" target="_blank" class="icon <?php echo $value["class"]; ?>"></a>
<?php
                
            // facebook ##
            } else {
?>
                <a class="icon <?php echo $value["class"]; ?>"></a>
<?php
            
            }


		}
		
		return ob_get_clean();
    
    }
    
    
    /**
    * Get widget data
    * 
    * @since       0.1
    * @return      HTML
    */
    public static function get()
    {
		
        global $post;
        #pr( $post );
        
        // no post ##
        if ( ! $post ) { 
            
            // h::log( 'No Post' );

            return false; 
        
        }
        
        // no sharelines found ##
        if ( ! self::has_sharelines() ) { 
            
            // h::log( 'd:>No Sharelines' );
            
            return false; 
        
        }
        
        // we should stop if we're missing key settings ##
        if ( ! self::validate() ) {
            
            // h::log( 'Config Error...' );

            return false;

        }

        // get properties ##
        $array = self::properties();
        
        // reject if missing required data ##
        if ( 
            empty( $array )
            || false == $array
        ) {

            h::log( 'Missing required config.' );

            return false;

        }
        
        // check we have data ##
		if ( ! $sharelines = self::get_sharelines() ) { return false; }
		
		// h::log( $sharelines );

		// return data ##
		return [
			'data' 		=> $sharelines,
		];

		/*

            // title ##
            if ( $array['title'] ) {
    
?>
                <li class="title">
                    <h3><?php echo $array['title']; ?></h3>
                </li>
<?php

            }

                // loop over each item ##
                foreach ( $sharelines as $shareline ) {
                    
                    // grab content ##
                    //$fb_description = self::chop( $shareline, 140 );
                    
?>
                <li class="item" data-shareline="<?php echo \esc_js( $shareline ); ?>">
                    <?php echo self::icons( \esc_js( $shareline ) ); ?>
                    <span class="text"><span class="fade"></span><?php echo \q\strings\method::chop( $shareline, 140 ); ?></span>
                    <div class="q-clear"></div>
                </li>
<?php
                    
                }


        
		strings\method::minify( self::javascript() ); // add javascript ##
		*/

    }


    
    public static function javascript()
    {

        global $post;
        #pr( $post );
        
        // no post ##
        if ( ! $post ) { return false; }

        // get properties ##
        $array = self::properties();
        
        #h::log( $array );

        // reject if missing required data ##
        if ( 
            empty( $array )
            || false == $array
        ) {

            h::log( 'Missing required config.' );

            return false;

        }

        // facebook ##
        #$facebook = isset( self::settings['facebook'] ) ? self::settings['facebook'] : self::$facebook ;
        $facebook = $array['facebook'];
        
        // get details to share ##
        $fb_name = \get_the_title( $post->ID );
        $fb_link = \get_permalink( $post->ID );
        $fb_caption = \get_post_meta( \get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
        $fb_pictures = \wp_get_attachment_image_src( \get_post_thumbnail_id( $post->ID ), 'medium' ); // get post image ##
        #h::log( $fb_pictures );
        $fb_picture = $fb_pictures[0];
		
		// ob_start();

?>
<script>
if ( typeof jQuery !== 'undefined' ) {
    jQuery(document).ready(function($) {

        // FB share ##
        jQuery(".facebook-share-button").click(function( e ) {

            e.preventDefault();
            
            if ( typeof FB !== "undefined" ) {
                
                // save "this" ##
                $t = jQuery(this);
                
                // grab current text ##
                $text = $t.parent().data("shareline");
                // console.log( $text );
                
                FB.ui (
                    {
                        method: 'feed',
                        name: '<?php echo \esc_js( $fb_name ); ?>',
                        link: '<?php echo \esc_js( $fb_link ); ?>',
                        picture: '<?php echo \esc_js( $fb_picture ); ?>',
                        caption: '<?php echo \esc_js( $fb_caption ); ?>',
                        description: $text // get content from clicked item ##
                    },
                    function( response ) {
                        if ( response && response.post_id ) {
                            $t.parent().find("span.text").text( '<?php \_e( 'Shared :)', 'q-textdomain' ); ?>' );
                        } else {
                            $t.parent().find("span.text").text( '<?php \_e( 'Failed :(', 'q-textdomain' ); ?>' );
                        }
                    }
                );

            } else {
                
                // debug ##
                $t.text( '<?php \_e( 'Facebook Error :(', 'q-textdomain' ); ?>' );
                fb_restore = setTimeout(function(){
                    $t.text( $text );
                }, 3000);
            
            }

        });

        // late load fb sharing library ##
        $facebook = jQuery('.facebook-share-button');
        if ( $facebook.length != 0 ) { // load options, if '.q-sharelines' element found ##
            
            jQuery.ajaxSetup({ cache: true });
            jQuery.getScript( '//connect.facebook.net/en_UK/all.js', function(){
                FB.init({
                    appId: '<?php echo $facebook; ?>'
                });     
			});
			
        }

		// hover ##
        $("body").on("mouseenter", "li.q-sharelines li.item a", function(){
			$(this).removeClass("greyscale");
		}).on("mouseleave", "li.q-sharelines li.item a", function(){
			$(this).addClass("greyscale");
		});

        // hover ##
		/*
        $("body").on({
            mouseenter: function(){
                $("li.q-sharelines li.item a").not(this).removeClass("greyscale");
            },
            mouseleave: function(){
                $("li.q-sharelines li.item a").addClass("greyscale");
            }
        }, 'li.q-sharelines li.item a');
		*/

    });
}
</script>
<?php

		// return ob_get_clean();

    }

}
