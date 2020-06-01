<?php

namespace q\plugin;

use q\core\core as core;
use q\core\helper as helper;
use q\theme\ui\template as template;
use q\theme\ui\view\fourzerofour\fourzerofour as fourzerofour;

// load it up ##
\q\plugin\aesop::run();

class aesop extends \Q {

    private static $loaded = false;
    private static $count = 0;

    public static function run()
    {

        // remove empty aesop error ##
        add_action( 'admin_head', [ get_class(), 'css' ], 10 );

        // register taxonomy ##
        \add_action( 'init', array( get_class(), 'register_taxonomy' ), 1 );
        
        // move taxonomy to sub menu ##
        \add_action('admin_menu', array( get_class(), 'add_submenu_page' ) ) ;

        // add ezine cpt to branch tax ##
        \add_action( 'init', [ get_class(), 'register_taxonomy_for_object_type' ], 20 );

        // filter collections module ##
        \add_filter( 'aesop_collection_query', [ get_class(), 'aesop_collection_query' ], 10, 1 );

        // Set some constants - these need to be updated also in Jorgen functions.php
		if ( ! defined( 'JORGEN_THEME_DIR' ) ) define( 'JORGEN_THEME_DIR', WP_CONTENT_DIR.'/themes/jorgen' );
		if ( ! defined( 'JORGEN_THEME_URL' ) ) define( 'JORGEN_THEME_URL', WP_CONTENT_URL.'/themes/jorgen' );

        // add Aesop / Jorgen theme specific functionality to admin ##
        \add_action( 'current_screen', [ get_class(), 'admin' ], 10 );

        #\add_action('admin_head', array( 'aesopThemeFunctions', 'setup' ), 11 );

        // add Jorgen logic ## -- bulks later if this theme is not Aesop ##
        \add_action( 'get_header', array( get_class(), 'add_jorgen' ), 100000 );

        //remove awful aesop upgrade nags
        \remove_class_action( 'admin_notices', 'AesopGalleryComponentAdmin', 'upgrade_galleries_notice', 10 );
        \remove_class_action( 'wp_ajax_upgrade_galleries', 'AesopGalleryComponentAdmin', 'upgrade_galleries', 10 );
        \remove_class_action( 'admin_head', 'AesopGalleryComponentAdmin', 'upgrade_click_handle', 10 );

    }


    public static function css(){

?>
    <style>
        .error.aesop-notice{
            display: none;
        }
    </style>
<?php
        
    }



    public static function admin()
    {

        // admin only ##
        if ( ! \is_admin() ) {

            // helper::log( 'Aesop not admin..' );

            return false;

        }

        self::$count ++;

        #helper::log( 'Hook called: '.self::$count.' times' );

        // // we need a post object, so check if it's set ##
        $screen = \get_current_screen();
        if ( ! $screen ) {

            // helper::log( 'wp called in admin, but no $screen available...' );

            return false;

        }

        // helper::log( 'Post type: '.$screen->post_type );

        // only load this on set post types ##
        if ( 
            'ezine' != $screen->post_type 
            && 'impact' != $screen->post_type 
        ) {

            // helper::log( 'Not an Aesop post_type' );
            
            return false;

        }

        // load up library ##
        if ( ! self::$loaded ) {
            
            require_once( JORGEN_THEME_DIR.'/functions.php' );

            self::$loaded = true;

        }

        $aesop = new \aesopThemeFunctions;
        $aesop->setup();

    }





    public static function register_taxonomy_for_object_type() 
    {
        
        // add taxonomy to post types ##
        \register_taxonomy_for_object_type( 'aesop_category', 'ezine' );

        \register_taxonomy_for_object_type( 'aesop_category', 'impact' );

    }



            
    /* 
    * register Taxonomy 
    *
    * @since    0.1.0
    */
    public static function register_taxonomy() 
    {

        \register_taxonomy(
            'aesop_category',
            array( 'ezine', 'impact' ),
            array(
                'label'             => __( 'Aesop Category' ),
                'rewrite'           => array( 'slug' => 'publication' ),
                'hierarchical'      => true,
                'show_ui'           => true,
                'show_admin_column' => true,
                'show_in_menu'      => true
            )
        );

    }



    /**
    * Move taxonomy to sub menu 
    *
    * @since        2.0.0
    *
    **/
    public static function add_submenu_page() 
    { 

        add_submenu_page( 'edit.php?post_type=page', 'Aesop Category', 'Aesop Category', 'manage_options', 'edit-tags.php?taxonomy=aesop_category&post_type=ezine'); 

    }  



    /**
    * Filter $args used for Aesop component query 
    *
    * @since        2.5.0
    * @return       Array
    **/
    public static function aesop_collection_query( Array $args )
    {

        // only load on defined templates ##
        if ( 
            'aesop' != template::get() 
        ) {

            helper::log( 'Not on an Aesop template' );

            return $args;

        }

        // grab global $post object ##
        global $post;

        if ( ! $post ) {

            // helper::log( 'No global $post available' );

            return $args;

        }

        // helper::log( 'Filtering Aesop $args' );
        // helper::log( $args );

        // start with empty arrays ##
        $args_passed = $args;
        $args = [];
        $post_ids = [];

        // if post has a parent, we need to get parent and all siblings ##
        if( $post->post_parent ) {

            // get all children ##
            $query = new \WP_Query( 
                array( 
                    'post_parent'   => $post->post_parent, // parent ID ## 
                    'fields'        => 'ids',
                    'post_type'     => [ 'ezine', 'impact' ]
                ) 
            );

            // helper::log( $query->posts );
            $post_ids = $query->posts;

            // build IDs for query ##
            // array_push( $post_ids, $query->posts );
            array_push( $post_ids, $post->post_parent );

        // else, if post has no parent, get itself and all children ##
        } else {

            // get all children ##
            $query = new \WP_Query( 
                array( 
                    'post_parent'   => $post->ID, // parent ID ## 
                    'fields'        => 'ids',
                    'post_type'     => [ 'ezine', 'impact' ]
                ) 
            );

            // helper::log( $query->posts );
            $post_ids = $query->posts;

            // build IDs for query ##
            // array_push( $post_ids, $query->posts );
            array_push( $post_ids, $post->ID );

        }

        // add post types as array ##
        $args['post_type']      = [ 'ezine', 'impact' ];
        $args['posts_per_page'] = -1;
        $args['post__in']       = $post_ids;
        $args['order']          = 'ASC' ; // isset( $args_passed ) ? $args_passed : 
        $args['orderby']        = 'menu_order';

        // helper::log( $args );

        // kick it back ##
        return $args;

    }



    /**
    * Add Jorgen Functions
    *
    * @since    2.0.0
    */
    public static function add_jorgen()
    {

        // only load on defined templates ##
        if ( 
            'aesop' === template::get() 
        ) {

            // helper::log( 'Loading Aesop Theme :)' );

            // load up library ##
            require_once( JORGEN_THEME_DIR.'/functions.php' );

            // $aesop = new \aesopThemeFunctions;
            // $aesop->setup();

            // ok ##
            return self::$loaded = true;
            
        } else {

            // helper::log( 'Aesop not happy :(' );

            // remove -- add_action( 'wp_enqueue_scripts', array( $this, 'merger' ), 11 );
            \remove_class_action( 'wp_enqueue_scripts', 'aiCoreCSSMerger', 'merger', 11 );

            // remove -- add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
            \remove_class_action( 'wp_enqueue_scripts', 'Aesop_Core', 'scripts', 10 );

            return false;

        }

    }




    /**
    * Display single aesop content
    *
    * @since    2.0.0
    * @return   Mixed Boolean on error or HTML string
    */
    public static function render()
    {

        // grab global post ##
        if ( ! $the_post = wordpress::the_post() ) { 

            // nothing found ##
            return fourzerofour::render();

        }

        // get program content ##
        if ( ! $object = wordpress::get_page_content() ) {

            // nothing found ##
            return fourzerofour::render();

        }

        if ( ! self::$loaded ) {

            helper::log( 'Aesop not loaded...' );

            // nothing found ##
            return fourzerofour::render();

        }

        // pre action ##
        \do_action('jorgen_single_before');

        // helper::log( 'Aesop loading..' );

?>
        <div id="post-<?php echo $object->ID; ?>" class="aesop">
<?php

            #echo $object->content;

            // pull theme template in ##
            include( JORGEN_THEME_DIR.'/content-single.php' );
?>
        </div>
<?php

        // post action ##
        \do_action('jorgen_single_after');

    }




}