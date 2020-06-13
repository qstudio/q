<?php

namespace q\sticky\admin;

use q\sticky\core\core as core;
use q\sticky\core\helper as helper;

// load it up ##
\q\sticky\admin\admin::run();

class admin extends \q_sticky {

    public static function run()
    {

        \add_action( 'plugins_loaded', array( get_class(), 'user_roles' ), 10 );

        #\add_action( 'post_submitbox_misc_actions', array( get_class(), 'post_submitbox' ) );

        // hook to action to ensure functions are loaded ##
        \add_action( 'current_screen', array( get_class(), 'init' ) );

        // hook into save_post action and ensure stickyness is maintained ##
        \add_action( 'save_post', array( get_class(), 'save_post' ), 1, 10 );

    }


    public static function save_post( $post_id ){

        if ( ! $post_types = core::get_defined_post_types() ) {
            
            #helper::log( 'No post types defined as sticky' );

            return false;

        }

        global $post; 

        if ( 
            ! isset( $post->post_type ) 
            || ! $post->post_type    
        ) {

            // helper::log( 'No post_type defined in $post object' );

            return false;

        }
        
        // helper::log( 'Hook: save_post - type: '.$post->post_type );

        if ( ! in_array( $post->post_type, $post_types ) ) {
            
            #helper::log( 'Not our post types..' );

        }

        // get stickt posts ##
        $sticky_posts = core::get_sticky_posts();
        
        // check if this ID is - or should be - sticky ##
        if( in_array( $post_id, $sticky_posts ) ) {

            #helper::log( 'This post should be sticky..' );

            array_unshift( $sticky_posts, $post_id );

            // save sticky posts option
            if( \update_option( 'sticky_posts', $sticky_posts ) ) {
            
                #helper::log( 'saved sticky...' );
    
                return true;
    
            } else {
    
                #helper::log( 'Error saving sticky..' );
    
                return false;

            }

        }

        return false;

    }


    public static function user_roles()
    {

        // get the "administrator" role object ##
        $role = \get_role( 'administrator' );

        // add role to manage stickyness ##
        $role->add_cap( 'edit_post_sticky' );

    } 


    public static function init()
    {
        
        if ( 
            ! \current_user_can('edit_others_posts') 
            || ! \current_user_can('edit_post_sticky') 
        ) {

            #helper::log( 'User lacks permissions to get sticky.' );

            // stop ##
            return false;

        }

        if ( 
            ! $post_types = core::get_defined_post_types() 
        ) {

            // helper::log( 'No post types defined as sticky' );

            return false;

        }

        // helper::log( $post_types );

        // get current post type ##
        $get_current_post_type = self::get_current_post_type() ? self::get_current_post_type() : false ;

        #$screen = get_current_screen();
        #helper::log( $get_current_post_type );
        #helper::log( $post_types );

        if ( in_array( $get_current_post_type, $post_types ) ) {

            #wp_die( var_dump( $post_types ) );
            foreach ( $post_types as $post_type ) {

                if ( $get_current_post_type == $post_type ) {

                    #helper::log( $get_current_post_type .' == '. $post_type );

                    \add_filter( "manage_edit-{$post_type}_columns", array( get_class(), 'edit_columns' ) );
                    \add_action( "manage_{$post_type}_posts_custom_column", array( get_class(), 'column_content' ) );

                }

            }

        }

        #}

    }



    /**
    * gets the current post type in the WordPress Admin
    *
    * @since    2.0.0
    * @return   Mixed
    */
    public static function get_current_post_type() 
    {
        
        global $post, $typenow, $current_screen;

        //we have a post so we can just get the post type from that
        if ( $post && $post->post_type ) {
            
            return $post->post_type;

        //check the global $typenow - set in admin.php
        } elseif( $typenow ) {
            
            return $typenow;

        //check the global $current_screen object - set in sceen.php
        } elseif( $current_screen && $current_screen->post_type ) {

            return $current_screen->post_type;

        //lastly check the post_type querystring
        } elseif( isset( $_REQUEST['post_type'] ) ) {

            return \sanitize_key( $_REQUEST['post_type'] );

        }

        //we do not know the post type!
        return null;

    }



    /**
    * Edit listed columns
    *
    * @since    2.0.0
    * @return   Array
    */
    public static function edit_columns ( $columns )
    {

        $offset = 1;
        $new_array = array_slice( $columns, 0, $offset, true ) +
        array(
            'sticky' => apply_filters( "q/sticky/title", 'Feature' ) // defined ( "Q_STICKY_TITLE" ) ? Q_STICKY_TITLE : \__( "Sticky", 'q-sticky' )
        ) +
        array_slice( $columns, $offset, NULL, true );
        return $new_array;

    }




    /**
    * Edit column content
    *
    * @since    2.0.0
    * @return   Array
    */
    public static function column_content( $name )
    {

        global $post;

        if( $name == 'sticky' ) {

            echo self::link( $post->ID );

        }
    }


    /**
    * Edit column content
    *
    * @since    2.0.0
    * @return   Array
    */
    public static function link( $post_id = '' )
    {
     
        global $post;
        
        if( $post_id == '' ) {
            $post_id = $post->ID;
        }

        $class = '';
        $title = \__( 'Make Sticky' );
        
        if ( \is_sticky( $post_id ) ) {

            $class = 'is-sticky';
            $title = 'Remove Sticky';

        }

        $link = '<a href="id='.$post_id.'&code='.\wp_create_nonce('q-stick-nonce').'" id="q-sticky'.$post_id.'" class="q-sticky '.$class.'" title="'.$title.'"></a>';
        
        // kick it back ##
        return $link;

    }


    function post_submitbox()
    {
    
        global $post;
    
        if( $post->post_type !='page' ) {
        
            echo '<div id="q-stick-meta" class="misc-pub-section ">Make Sticky: '.get_q_sticky_link($post->ID).'</div>';
        
        }

    }

}