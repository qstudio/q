<?php

namespace q\admin;

use q\core;
use q\core\helper as h;

// load it up ##
// \q\admin\controller::run();

class method extends \Q {


	
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



	// add thumbnails to admin columns ##
	// \add_action( 'admin_init', function(){ return self::add_thumbnail_to( array( 'posts', 'pages' ) ) ) );
	

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
    
    
}
