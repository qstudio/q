<?php

namespace q\admin;

use q\plugin as q;
use q\core;
use q\core\helper as h;

class method {

	public static function empty_directory( $path = null, $pattern = '*' ){

		// sanity ##
		if( is_null( $path ) ){

			// nothing possible ##
			h::log( 'e:>Error, no path sent to function' );

			return false;

		}

		// check if path exists ##
		if( ! is_dir( $path ) ) {

			// nothing possible ##
			h::log( 'e:>Error, "'.$path.'" does not exist' );

			return false;

		}

		// log ##
		$log = [];

		// Standard PHP Library (SPL)
		$di = new \RecursiveDirectoryIterator( $path, \FilesystemIterator::SKIP_DOTS );
		$ri = new \RecursiveIteratorIterator( $di, \RecursiveIteratorIterator::CHILD_FIRST );

		foreach ( $ri as $file ) {

			if ( $file->isDir() ) {
				
				$log[] = $file.' -> Directory Deleted';

				rmdir($file);
				
			} else {

				$log[] = $file.' -> File Deleted';

				unlink($file);

			}

		}

		/*
		// h::log( $path );
		$files = glob( $path.$pattern ); // get all file names
		// h::log( $files );
		$log = [];

		foreach( $files as $file ) { // iterate files

			if( is_file( $file ) ) {

				$log[] = $file;
				unlink( $file ); // delete file
		
			}

		}
		*/

		// h::log( $log );

		return $log;

	}

	public static function copy_files( $args = null ){

		// sanity ##
		if( 
			is_null( $args['source'] ) 
			|| is_null( $args['destination'] ) 
			|| is_null( $args['files'] )
			|| ! is_array( $args['files'] )
		){

			// h::log( 'e:>source: '.$args['source'] );
			// h::log( 'e:>destination: '.$args['destination'] );
			// h::log( $args['files'] );

			// nothing possible ##
			h::log( 'e:>Error in arguments sent to function' );

			return false;

		}

		// check if source path exists ##
		if( ! is_dir( $args['source'] ) ) {

			// nothing possible ##
			h::log( 'e:>Error, source: "'.$path.'" does not exist' );

			return false;

		}

		// check if destination path exists ##
		if( ! is_dir( $args['destination'] ) ) {

			// nothing possible ##
			h::log( 'e:>Error, destination: "'.$args['destination'].'" does not exist' );

			return false;

		}	

		// h::log( 'e:>source: '.$args['source'] );
		// h::log( 'e:>destination: '.$args['destination'] );
		// h::log( $args['files'] );
		$log = [];

		// iterate files ##
		foreach( $args['files'] as $file ) { 

			// chcek if file exists ##
			if( 
				! file_exists( $args['source'].$file ) ) {

				$log[] = '404: '.$args['source'].$file;

			} else if( 
				copy( $args['source'].$file, $args['destination'].$file ) ) {

				$log[] = 'Copied: "'.$args['destination'].$file.'"';
		
			}

		}

		// h::log( $log );

		return $log;

	}

	/**
	 * Recursive copy directories and content
	 * 
	 * @link		https://stackoverflow.com/a/2050909/591486
	 * @since		4.7.2
	*/
	public static function copy_recursive( $source = null, $destination = null, &$log = [] ) {

		// is directory ##
		if ( is_dir( $source ) ) {

			$log[] = 'is_dir: '.$source;

			// log results of mkdir call ##
			$log[] = '@mkdir( "'.$destination.'" ): '.@mkdir( $destination );

			// get source directory contents ##
			$source_directory = dir( $source );

			// loop over items in source directory ##
			while ( FALSE !== ( $entry = $source_directory->read() ) ) {
				
				// skip hidden ##
				if ( $entry == '.' || $entry == '..' ) {

					$log[] = 'skip hidden entry: '.$entry;

					continue;

				}

				// get full source "entry" path ##
				$source_entry = $source . '/' . $entry; 

				// recurse for directories ##
				if ( is_dir( $source_entry ) ) {

					$log[] = 'is_dir: '.$source_entry;

					// return to self, with new arguments ##
					self::copy_recursive( $source_entry, $destination.'/'.$entry, $log );

					// break out of loop, to stop processing ##
					continue;

				}

				$log[] = 'copy: "'.$source_entry.'" --> "'.$destination.'/'.$entry.'"';

				// copy single files ##
				copy( $source_entry, $destination.'/'.$entry );

			}

			// close connection ##
			$source_directory->close();

		} else {

			$log[] = 'copy: "'.$source.'" --> "'.$destination.'"';

			// plain copy, as $destination is a file ##
			copy( $source, $destination );

		}

		// clean up log ##
		$log = array_unique( $log );

		// kick back log for debugging ##
		return $log;

	}
	
    /**
     * restrict_manage_posts filter
     *
     * @param       Array       $args       Array of custom post types and taxaonomies to filter
     */
    public static function restrict_manage_posts( $args = null ){

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
    public static function generate_taxonomy_options( $tax_slug, $tax_name, $parent = '', $level = 0,$selected = null ){

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
    public static function add_thumbnail_to( $post_types = null ){
        
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
    public static function admin_add_thumbnail_column( $cols ){
        
        $cols['thumbnail'] = __('Thumbnail');
        return $cols;
        
    }

    /**
     * Add row thumbnail value 
     * 
     * @param type $column_name
     * @param type $post_id
     */
    public static function admin_add_thumbnail_value( $column_name, $post_id ){

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
