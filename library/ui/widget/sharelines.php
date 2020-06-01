<?php

namespace q\ui\widget;

use q\core\helper as h;
use q\core;

/**
 * Sharelines Quasi Widget
 *
 * @package WordPress
 * @since 2.0.0
 *
 */

// load it up ##
\q\theme\widget\sharelines::run();

class sharelines extends \Q {

    // plugin properties ##
    public static $properties = false;
    public static $args = array();

    public static function run()
    {

        // add acf fields ##
        \add_action( 'acf/init', array( get_class(), 'add_acf_fields' ), 1 );

	}




    public static function hook( array $args = null )
    {

        // hook assets ##
        self::assets();

        // merge any passed args ##
        self::$args = $args;

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
    public static function add_acf_fields()
    {

        if( function_exists('acf_add_local_field_group') ):

            // student CPT ##
            \acf_add_local_field_group(array(
                'key' => 'group_5522c13d4ae84',
                'title' => 'Student Countries',
                'fields' => array(
                    array(
                        'key' => 'field_5522c166872ae',
                        'label' => 'Shortcode',
                        'name' => 'country_shortcode',
                        'type' => 'text',
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
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => 2,
                        'readonly' => 0,
                        'disabled' => 0,
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'taxonomy',
                            'operator' => '==',
                            'value' => 'mos_country',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));

            // Admin editor ##
            \acf_add_local_field_group(array(
                'key' => 'group_54b0ed786dc62',
                'title' => 'Meet Our Students',
                'fields' => array(
                    array(
                        'key' => 'field_54d0d20429a3a',
                        'label' => 'Unique ID',
                        'name' => 'mos_unique_id',
                        'type' => 'text',
                        'instructions' => 'Enter the students unique identification code.',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                        'readonly' => 0,
                        'disabled' => 0,
                    ),
                    array(
                        'key' => 'field_5522c20beae7b',
                        'label' => 'Age',
                        'name' => 'mos_age',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'mos_age',
                        'field_type' => 'select',
                        'allow_null' => 0,
                        'load_save_terms' => 1,
                        'return_format' => 'object',
                        'multiple' => 0,
                        'add_term' => 1,
                        'load_terms' => 0,
                        'save_terms' => 1,
                    ),
                    array(
                        'key' => 'field_5522c2e57986b',
                        'label' => 'Gender',
                        'name' => 'mos_gender',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'mos_gender',
                        'field_type' => 'radio',
                        'allow_null' => 0,
                        'load_save_terms' => 1,
                        'return_format' => 'object',
                        'multiple' => 0,
                        'add_term' => 1,
                        'load_terms' => 0,
                        'save_terms' => 1,
                    ),
                    array(
                        'key' => 'field_5522c324d9b65',
                        'label' => 'Country',
                        'name' => 'mos_country',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'mos_country',
                        'field_type' => 'select',
                        'allow_null' => 0,
                        'load_save_terms' => 1,
                        'return_format' => 'object',
                        'multiple' => 0,
                        'add_term' => 1,
                        'load_terms' => 0,
                        'save_terms' => 1,
                    ),
                    array(
                        'key' => 'field_5522c27eb5178',
                        'label' => 'Interests',
                        'name' => 'mos_interests',
                        'type' => 'taxonomy',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'taxonomy' => 'mos_interest',
                        'field_type' => 'checkbox',
                        'allow_null' => 0,
                        'load_save_terms' => 1,
                        'return_format' => 'object',
                        'multiple' => 0,
                        'add_term' => 1,
                        'load_terms' => 0,
                        'save_terms' => 1,
                    ),
                    array(
                        'key' => 'field_54b0edd1f0784',
                        'label' => 'Program Start Date',
                        'name' => 'mos_program_start_date',
                        'type' => 'select',
                        'instructions' => '',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'Spring 2016' => 'Spring 2016',
                            'Summer 2016' => 'Summer 2016',
                            'Fall 2016' => 'Fall 2016',
                            'Spring 2017' => 'Spring 2017',
                        ),
                        'default_value' => array(
                        ),
                        'allow_null' => 0,
                        'multiple' => 0,
                        'ui' => 0,
                        'ajax' => 0,
                        'placeholder' => '',
                        'disabled' => 0,
                        'readonly' => 0,
                        'return_format' => 'value',
                    ),
                    array(
                        'key' => 'field_54b1068026159',
                        'label' => 'Attachment',
                        'name' => 'mos_attachment',
                        'type' => 'file',
                        'instructions' => '',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'url',
                        'library' => 'all',
                        'min_size' => 0,
                        'max_size' => 0,
                        'mime_types' => '',
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'student',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => 1,
                'description' => '',
            ));
            
        endif;

    }



    /**
    * Load required Assets 
    *
    * @since    2.0.0
    */
    public static function assets()
    {

        \wp_register_style( 'q-sharelines-css', h::get( "theme/css/widget.sharelines.css", 'return' ) );
        \wp_enqueue_style( 'q-sharelines-css' );

	}




    public static function config()
    {

        // new array ##
        $config = array();

        // values ##
        $config['title'] = \apply_filters( 'q/widget/sharelines/title', \__( 'Share', 'q-textdomain' ) );

        $config['facebook'] = \apply_filters( 'q/widget/sharelines/facebook', '137150683665520' );

        // merge ##
        if ( ! empty( self::$args ) ) {

            $config = array_merge( $config, self::$args );

        }

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
    * Validate that we have all the required data to make an API call to Instagram
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
    * Find all occurances of a single shortcode in a string
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
        
        while( \have_rows( 'sharelines') ) {
            
            // set-up the row ##
            \the_row(); 

            // grab the store text ##
            $array[] = \get_sub_field( 'text' );

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

        // test stored settings ##
        #h::log( $array );
        
        // array to test against ##
        $networks = array(
            'facebook'      => array (
                'class'     => 'facebook-share-button'
            ),
            'twitter'       => array (
                'class'     => 'twitter-share-button',
                'data-text' => $shareline,
                'via'       => 'greenheartcci',
                'href'      => 'https://twitter.com/share',
                'url'       => \wp_get_shortlink(),
                'counturl'  => \get_permalink()
            )
        );
        
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
    
    }
    
    
    /**
    * Render the widget
    * 
    * @since       0.1
    * @return      HTML
    */
    public static function render()
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
            
            // h::log( 'No Sharelines' );
            
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
        
        // get all shortcodes in use ##
        if ( ! $sharelines = self::get_sharelines( $post->post_content ) ) { return false; }
        
        // // get wrapper ##
        // $markup = $array['wrapper'];

        // // markup ##
        // $markup = str_replace( '%selector%', $array['selector'], $markup );

        // // add trigger element ##
        // echo $markup;

?>
        <li class="q-sharelines">
            <ul>
<?php

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
                    <span class="text"><span class="fade"></span><?php echo ui\markup::chop( $shareline, 140 ); ?></span>
                    <div class="q-clear"></div>
                </li>
<?php
                    
                }

?>
                <li class="q-clear"></li>
            </ul>
            <div class="q-clear"></div>
        </li>
        <div class="q-clear"></div>
        <div id="fb-root"></div>
        <div class="q-clear"></div>
<?php

        // add javascript ##
        ui\markup::minify( self::javascript() );

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
        $fb_pictures = \wp_get_attachment_image_src( \get_post_thumbnail_id( $post->ID ), 'thumbnail' ); // get post image ##
        #pr( $fb_pictures );
        $fb_picture = $fb_pictures[0];
        

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
                //console.log( $text );
                
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
        $(document).on({
            mouseenter: function(){
                $("li.q-sharelines li.item a").not(this).addClass("greyscale");
            },
            mouseleave: function(){
                $("li.q-sharelines li.item a").removeClass("greyscale");
            }
        }, 'li.q-sharelines li.item a');

    });
}
</script>
<?php

    }

}