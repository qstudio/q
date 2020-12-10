<?php

namespace q\module\sticky;

use q\core\helper as h;
use q\module;

class admin extends module\sticky {

    function hooks(){

        \add_action( 'plugins_loaded', array( $this, 'user_roles' ), 10 );

        #\add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox' ) );

        // hook to action to ensure functions are loaded ##
        \add_action( 'current_screen', array( $this, 'init' ) );

        // hook into save_post action and ensure stickyness is maintained ##
        \add_action( 'save_post', array( $this, 'save_post' ), 1, 10 );

    }


    function save_post( $post_id ){

        if ( ! $post_types = method::get_defined_post_types() ) {
            
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
        $sticky_posts = method::get_sticky_posts();
        
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

    function user_roles(){

        // get the "administrator" role object ##
        $role = \get_role( 'administrator' );

        // add role to manage stickyness ##
        $role->add_cap( 'edit_post_sticky' );

    } 

    function init(){
        
        if ( 
            ! \current_user_can('edit_others_posts') 
            || ! \current_user_can('edit_post_sticky') 
        ) {

            #helper::log( 'User lacks permissions to get sticky.' );

            // stop ##
            return false;

        }

        if ( 
            ! $post_types = method::get_defined_post_types() 
        ) {

            // helper::log( 'No post types defined as sticky' );

            return false;

        }

        // helper::log( $post_types );

        // get current post type ##
        $get_current_post_type = method::get_current_post_type() ? method::get_current_post_type() : false ;

        #$screen = get_current_screen();
        #helper::log( $get_current_post_type );
        #helper::log( $post_types );

        if ( in_array( $get_current_post_type, $post_types ) ) {

            #wp_die( var_dump( $post_types ) );
            foreach ( $post_types as $post_type ) {

                if ( $get_current_post_type == $post_type ) {

                    #helper::log( $get_current_post_type .' == '. $post_type );

                    \add_filter( "manage_edit-{$post_type}_columns", array( $this, 'edit_columns' ) );
                    \add_action( "manage_{$post_type}_posts_custom_column", array( $this, 'column_content' ) );

                }

            }

        }

    }

    /**
    * Edit listed columns
    *
    * @since    2.0.0
    * @return   Array
    */
    function edit_columns ( $columns ){

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
    function column_content( $name ){

        global $post;

        if( $name == 'sticky' ) {

            echo $this->link( $post->ID );

        }
    }


    /**
    * Edit column content
    *
    * @since    2.0.0
    * @return   Array
    */
    function link( $post_id = '' ){
     
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

        $link = '<a href="id='.$post_id.'&code='.\wp_create_nonce('q-sticky-nonce').'" id="q-sticky'.$post_id.'" class="q-sticky '.$class.'" title="'.$title.'"></a>';
        
        // kick it back ##
        return $link;

    }

    function post_submitbox(){
    
        global $post;
    
        if( $post->post_type !='page' ) {
        
            echo '<div id="q-stick-meta" class="misc-pub-section ">Make Sticky: '.get_q_sticky_link($post->ID).'</div>';
        
        }

    }

}
