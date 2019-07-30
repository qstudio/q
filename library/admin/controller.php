<?php

namespace q\admin;

use q\core\core as core;
use q\core\helper as helper;

// load it up ##
\q\admin\controller::run();

class controller extends \Q {

    public static function run()
    {

        if ( \is_admin() ) {

            // admin js ##
            \add_action( 'admin_enqueue_scripts', array( get_class(), 'admin_enqueue_scripts' ), 1 );

            // set-up admin image sizes ##
            \add_action( "admin_init", array( get_class(), 'admin_setup_images' ) );
                
            // add theme support ##
            \add_action( 'init', array( get_class(), 'add_support' ) );
                
            // add thumbnails to admin columns ##
            // \add_action( 'admin_init', create_function( '', Q_Admin::add_thumbnail_to( array( 'posts', 'pages' ) ) ) );

            // filter admin preview link ##
            \add_filter( 'preview_post_link', [ get_class(), 'preview_post_link' ], 10, 2 );

            // Add Filter Hook
            \add_filter( 'post_mime_types', array( get_class(), 'post_mime_types' ) );

        }

        // remove admin search bar ##
        \add_action( 'admin_bar_menu', array( get_class(), 'remove_admin_bar_search' ), 999 );   

        // remove admin color schemes - silly idea ##
        \remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );

        // remove "url" field from comments ##
        \add_filter( 'comment_form_default_fields', array( get_class(), 'comment_form_default_fields' ) );

    }





    /**
    * include plugin admin assets
    *
    * @since        0.1.0
    * @return       __void
    */
    public static function admin_enqueue_scripts() {

        // add JS ## -- after all dependencies ##
        \wp_enqueue_script( 'q-admin-js', helper::get( "theme/javascript/q.admin.global.js", 'return' ), array( 'jquery' ), self::version );

        // nonce ##
        $nonce = \wp_create_nonce( 'q-admin-nonce' );

        // pass variable values defined in parent class ##
        \wp_localize_script( 'q-admin-js', 'q_admin', array(
            'ajaxurl'           => \admin_url( 'admin-ajax.php', \is_ssl() ? 'https' : 'http' ), ## add 'https' to use secure URL ##
            'debug'             => self::$debug,
            'nonce'             => $nonce
        ));

        // add snackbar CSS ##
        \wp_register_style( 'q-snackbar-admin', helper::get( "theme/css/snackbar.min.css", 'return' ), '', self::version, 'all' );
        \wp_enqueue_style( 'q-snackbar-admin' );

        // add snackbar JS ##
        \wp_register_script( 'q-snackbar-admin', helper::get( "theme/javascript/snackbar.min.js", 'return' ), array( 'jquery' ), self::version );
        \wp_enqueue_script( 'q-snackbar-admin' );

    }




    /**
     * Set-up image sizes in WP admin 
     * 
     * @since       1.2.0
     * @return      void
     */
    public static function admin_setup_images()
    {
    
        // default thumb size in admin ##
        \set_post_thumbnail_size( 260, 200, true );

        // this theme uses post thumbnails - set the sizes below ##
        \add_image_size( 'admin-list-thumb', 60, 40, true ); // admin thumbs ##
        \add_image_size( 'dashboard', 100, 40, true );
        
    }




    
    /**
     * Adds Support for shared Q features.
     *
     * @since       0.1
     * @return      void
     */
    public static function add_support()
    {

        // add thumbnails ##
        \add_theme_support( 'post-thumbnails' );

        // default Post Thumbnail dimensions
        \set_post_thumbnail_size( 194, 97 );

    }




    /**
     * Add Thumbnail Column to Post Type in admin
     * 
     * @since       1.2.0
     * @param       Array    $post_types
     */
    public static function add_thumbnail_to( $post_types = null )
    {
        
        // sanity check ##
        if ( ! $post_types ) { return false; } // nothing to do ##
        
        // make sure this is only loaded up in the admin ##
        if ( \is_admin() ) {
            
            foreach ( $post_types as $post_type ) {
                
                if ( \post_type_supports( $post_type, 'thumbnail' ) ) {
                
                    // add thumbnails for post_type ##
                    \add_filter( "manage_{$post_type}_columns", array( get_class(), 'admin_add_thumbnail_column' ) );
                    \add_action( "manage_{$post_type}_custom_column", array( get_class(), 'admin_add_thumbnail_value' ), 10, 2 );

                }
                
            }
            
        }
        
    }
    
    
    /**
     * Add thumbnail column
     * 
     * @param       Array    $cols
     * @return      Array
     */
    public static function admin_add_thumbnail_column( $cols ) 
    {
        
        $cols['thumbnail'] = __('Thumbnail');
        return $cols;
        
    }

    
    /**
     * Add row thumbnail value 
     * 
     * @param type $column_name
     * @param type $post_id
     */
    public static function admin_add_thumbnail_value( $column_name, $post_id ) 
    {

        $width = (int) 200;
        $height = (int) 125;

        if ( 'thumbnail' == $column_name ) {
            // thumbnail of WP 2.9
            $thumbnail_id = \get_post_meta( $post_id, '_thumbnail_id', true );
            // image from gallery
            $attachments = \get_children( array('post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
            if ( $thumbnail_id ) {
                #$thumb = wp_get_attachment_image( $thumbnail_id, array($width, $height), true );
                #echo $thumbnail_id;
                $thumb = \wp_get_attachment_image( $thumbnail_id, 'admin-list-thumb', true );
            } elseif ($attachments) {
                foreach ( $attachments as $attachment_id => $attachment ) {
                    #$thumb = wp_get_attachment_image( $attachment_id, array($width, $height), true );
                    $thumb = \wp_get_attachment_image( $attachment_id, 'admin-list-thumb', true );
                }
            }
            if ( isset($thumb) && $thumb ) {
                echo $thumb;
            }
        }
    }
    
    



    /**
     * Fix for broken preview link in admin - link to default url ?p=ID
     * 
     * @since       0.1.0
     */
    public static function preview_post_link( $preview_link, $post ) 
    {

        if ( 
            \get_post_status ( $post->ID ) != 'draft' 
            && \get_post_status ( $post->ID ) != 'auto-draft' 
        ) {
      
            // preview URL for all published posts ##
            return \home_url()."?p=".$post->ID; 
      
         } else {
            
            // preview URL for all posts which are in draft ##
            return \home_url()."?p=".$post->ID; 

        }

    }



    
    
    /**
     * Add filters to WP Media Library
     *
     * @since       1.4.2
     * @return      Array
     */
    public static function post_mime_types( $post_mime_types )
    {

        // select the mime type, here: 'application/pdf'
        // then we define an array with the label values
        $post_mime_types['application/pdf'] = array(
            __( 'PDF' ),
            __( 'Show PDF' ),
            \_n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' )
        );

        // then we return the $post_mime_types variable
        return $post_mime_types;

    }






    /**
    * Remove admin bar search, as gives SSl Error
    *
    * @return      void
    */
    public static function remove_admin_bar_search( $wp_admin_bar )
    {

        $wp_admin_bar->remove_node( 'search' );

    }




    /**
    * Remove unrequired menu items
    *
    * @since    2.0.0
    * @return   _false
    */
    public static function remove_menus()
    {

        \remove_menu_page( 'edit.php?post_type=ai_galleries' );       

    }


    

    /**
     * restrict_manage_posts filter
     *
     * @param       Array       $args       Array of custom post types and taxaonomies to filter
     */
    public static function restrict_manage_posts( $args = null )
    {

        // sanity check ##
        if ( is_null ( $args ) || ! array_filter( $args ) ) { 
            
            return false; 
        
        }

        // caste input to array ##
        if ( ! is_array( $args ) ) (array) $args;

        // only display these taxonomy filters on desired custom post_type listings
        global $typenow;

        foreach ( $args as $cpt => $tax ) {

            // cpt matched ##
            if ( $cpt == $typenow ) {

                // create an array of taxonomy slugs you want to filter by - if you want to retrieve all taxonomies, could use get_taxonomies() to build the list
                $filters = (array) $tax;

                foreach ( $filters as $tax_slug ) {

                    // retrieve the taxonomy object
                    $tax_obj = \get_taxonomy($tax_slug);
                    //pr($tax_obj);
                    $tax_name = $tax_obj->labels->name;
                    //pr($tax_name);
                    // output html for taxonomy dropdown filter
                    echo "<select name='".strtolower($tax_slug)."' id='".strtolower($tax_slug)."' class='postform'>";
                    echo "<option value=''>".__( "All", 'q-textdomain' )." $tax_name</option>";
                    self::generate_taxonomy_options( $tax_slug, $tax_name, 0, 0, (isset($_GET[strtolower($tax_slug)])? $_GET[strtolower($tax_slug)] : null) );
                    echo "</select>";

                }

            }

        }

    }



    /**
     * Generate Admin <select>'s ##
     *
     * @param type $tax_slug
     * @param type $tax_name
     * @param type $parent
     * @param type $level
     * @param type $selected
     */
    public static function generate_taxonomy_options( $tax_slug, $tax_name, $parent = '', $level = 0,$selected = null)
    {

        $args = array( 'show_empty' => 1, 'hierarchical' => true );
        #if( !is_null($parent)) {
            #$args = array( 'get' => 'all' );
        #}

        $terms = \get_terms( $tax_slug, $args );

        #if ( $tax_slug == 'what' ) {echo('what terms ('.pr($args).'): '.pr($terms)); }

        $tab = '';
        for( $i=0; $i < $level; $i++ ){
            $tab.='--';
        }

        foreach ( $terms as $term ) {
            // output each select option line, check against the last $_GET to show the current option selected

            // indent children ##
            $indent = ''; // nada ##
            if ( $term->parent > 0 ) {
                $indent = '&rsaquo; '; // indent it ##
            }

            echo '<option value='. $term->slug, $selected == $term->slug ? ' selected="selected"' : '','>' .$indent.$tab. $term->name .' (' . $term->count .')</option>';
            #generate_taxonomy_options($tax_slug, $term->term_id, $level+1,$selected);

        }

    }

    

    /**
    * Add Admin Bar menu item ##
    *
    * @since      1.0.1
    * @link       http://blog.rutwick.com/add-items-anywhere-to-the-wp-3-3-admin-bar
    * @return     void
    */
    public function admin_bar_menu_device( $admin_bar )
    {

        #self::log( 'AdminBar..' );

        // check plugin is active ##
        if ( 
            function_exists('is_plugin_active') && 
            ! is_plugin_active( "device-theme-switcher/dts_controller.php" ) 
        ) {

            return false;

        }

        global $current_user;

        if ( ! $current_user->has_cap( 'manage_options' ) ) {

            return false;

        }

        $args = array(
            'id'        => 'dts-switch'
            ,'title'    => 'Theme'
            ,'href'     => '#'
            ,'meta'     => array(
                            'title' => __('Theme')
                        )
        );

        // parent menu ##
        $admin_bar->add_menu( $args);

        // array of children menu items ##
        $children = array(
                array( 'id' => 'dts-switch-handheld', 'title' => __( 'Handheld', 'q-textdomain' ), 'url' => '?theme=handheld' )
            ,   array( 'id' => 'dts-switch-tablet', 'title' => __( 'Tablet', 'q-textdomain' ), 'url' => '?theme=tablet' )
            ,   array( 'id' => 'dts-switch-desktop', 'title' => __( 'Desktop', 'q-textdomain'), 'url' => '?theme=desktop' )
            #,   array( 'id' => 'dts-switch-low-support', 'title' => __('Low Support', self::text_domain ), 'url' => '?theme=low_support' )
        );

        // get the current URL ##
        #global $wp;
        #$current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );

        // loop over array and add each child item ##
        foreach ( $children as $child ) {

            $args = array(
                'id'        => $child["id"]
                ,'title'    => $child["title"]
                ,'href'     => $child["url"]
                ,'meta'     => array(
                                'title' => $child["title"]
                            )
                ,'parent'   => 'dts-switch'
            );

            // child menu items##
            $admin_bar->add_menu( $args);

        }

    }



    
    /**
     * Filter to remove URL field from comments form
     *
     * @since       1.6.1
     * @param       Array  $fields
     * @return      Array
     */
    public static function comment_form_default_fields( $fields )
    {

        if ( isset( $fields['url'] ) ) {

            unset($fields['url']);

        }

        // kick it back ##
        return $fields;

    }



}