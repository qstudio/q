<?php

/**
 * WordPress general functions
 *
 * @since 0.1
 */


if ( ! function_exists( 'q_get_post_count_by_meta' ) )
{
/*
* count the number of posts that have a set meta_key & meta_value
*
* @since 1.1.0
*/
    function q_get_post_count_by_meta( $meta_key, $meta_value, $post_type = '', $compare = 'tight' )  {

        $args = array();

        if ( $post_type ) $args["post_type"] = $post_type;

        $args['posts_per_page'] = -1;
        $args['post_status'] = 'publish';

        if ( $meta_key && $meta_value ) {

            if ( is_array($meta_value) || $compare == 'easy' ) {

                $args['meta_query'][] = array(
                    'key' => $meta_key,
                    'value' => $meta_value,
                    'compare' => 'LIKE'
                );

            } else {

                $args['meta_query'][] = array(
                    'key' => $meta_key,
                    'value' => $meta_value
                );
            }
        }

        #pr($args);

        $posts = get_posts($args);

        #pr($posts);

        $count = count($posts);

        return (int)$count;

    }
}


if ( ! function_exists( 'q_has_inline_image' ) )
{
/**
 * check if a post has an inline image
 *
 * @added for GH
 * @since 0.7
 */
    function q_has_inline_image( $check = true, $return = false, $size = '' ){

        global $post;

        // Set the post content to a variable
        $szPostContent = $post->post_content;

        // Define the pattern to search
        $szSearchPattern = '~<img [^\>]*\ />~';

        // Run preg_match_all to grab all the images and save the results in $aPics
        preg_match_all( $szSearchPattern, $szPostContent, $aPics );

        // Count the results
        $iNumberOfPics = count($aPics[0]);

        if ( $check === true ) {

            return $iNumberOfPics === 0 ? false : true;

        }

        if ( $return === true && $iNumberOfPics > 0 ) {

            return $aPics[0];

        }

    }
}


if ( ! function_exists( 'q_get_gallery_images' ) )
{
/**
 * get all images from a post gallery
 *
 * @param   Object      $post       Post object to examine
 * @param   String      $size       Handle of image size to return
 * @param   Integer     $limit      Number of images to return, defaults to 10
 * @param   String      $field      Allows for a custom field to be used to grab the gallery shortcode
 * @since 1.1.0
 */
    function q_get_gallery_images( $post = null, $size = null, $limit = null, $field = null ){

        // passed post or global ##
        if ( ! $post ) global $post;

        // kickout if no post object ##
        if ( ! is_object( $post ) ) { $post = get_post( $post ); }

        // kick out if we can't get a real post object ##
        if ( ! $post || ! is_object( $post ) ) {
            #echo 'kicked';
            return false;
        }

        // limit set ##
        $limit = ! is_null ( $limit ) ? $limit : get_option( 'posts_per_page', 10 );

        // set content or field to grab [gallery] shortcode from ##
        $content = ! is_null ( $field ) ? $field : $post->post_content ;

        // test passed content field ##
        #pr( $content );

        // http://wordpress.stackexchange.com/questions/80408/how-to-get-page-post-gallery-attachment-images-in-order-they-are-set-in-backend
        $pattern = get_shortcode_regex();

        if( preg_match_all( '/'. $pattern .'/s', $content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'gallery', $matches[2] ) ):

            $keys = array_keys( $matches[2], 'gallery' );

            foreach( $keys as $key ):
                $atts = shortcode_parse_atts( $matches[3][$key] );
                    if( array_key_exists( 'ids', $atts ) ):

                        $query_images = new WP_Query(
                            array(
                                'posts_per_page'    => $limit,
                                'post_type'         => 'attachment',
                                'post_status'       => 'inherit',
                                'post__in'          => explode( ',', $atts['ids'] ),
                                'orderby'           => 'post__in'
                            )
                        );

                        wp_reset_query();

                    endif;
            endforeach;

        endif;

        // empty array, just in case ##
        $images = array();

        // build images array ##
        foreach ( $query_images->posts as $image ) {

            // image src ##
            if ( $size ) {

                $image_src_array = wp_get_attachment_image_src( $image->ID, $size );
                $image_src = $image_src_array[0];

                // get updated meta ##
                $image_meta = array(
                    "width" => $image_src_array[1], // width ##
                    "height" => $image_src_array[2], // height ##
                );

            } else {

                $image_meta = wp_get_attachment_metadata( $image->ID ); // get dimensions ##
                $image_src = $image->guid;

            }

            $images[] = array (
                "ID"            => $image->ID,
                "src"           => $image_src,
                "width"         => $image_meta["width"],
                "height"        => $image_meta["height"],
                'alt'           => get_post_meta( $image->ID, '_wp_attachment_image_alt', true ),
                'caption'       => $image->post_excerpt ? $image->post_excerpt : 'undefined',
                'description'   => $image->post_content,
                'href'          => get_permalink( $image->ID ),
                #'src'           => $image->guid,
                'title'         => $image->post_title
            );
        }

        return $images;

    }
}


if ( ! function_exists( 'q_remove_gallery_shortcode' ) )
{
/**
 * remove the gallery shortcode from the content
 *
 * @since       1.1.0
 * @example     call using -- add_filter( 'the_content', 'q_remove_gallery_shortcode', 1 );
 */
    function q_remove_gallery_shortcode( $content ) {

        $expr = '/\[gallery(.*?)\]/i';
        return ( preg_replace( $expr, '', $content ) ); // deletes all existing gallery shortcodes

    }
}


if ( ! function_exists( 'q_get_attachment_by_size' ) )
{
/**
 * get post attached image by image size ##
 *
 * @added for GH
 * @since 0.7
 */
    function q_get_attachment_by_size( $id, $size = 'thumbnail', $count = -1, $tag = true ) {

        global $post;

        if ( !$post ) return;

        if (
            $images = get_children(
                array(
                    'post_parent'       => $post->ID,
                    'post_type'         => 'attachment',
                    //'numberposts'       => intval($count),
                    'post_mime_type'    => 'image',
                    'post_status'       => null,
                    )
                )
            )
        {

            #echo 'count: '.$count;
            // loop out required number ##
            $q_get_post_image = array();
            foreach( $images as $image ) {

                $attachment_image = wp_get_attachment_image_src( $image->ID, $size );
                if ( ! empty ( $attachment_image ) ) {
                    if ( $tag === true ) { // wrap in img tag ##
                        $q_get_post_image[] = '<img src="'.$attachment_image[0].'" />';
                    } else { // don't wrap in tag ##
                        $q_get_post_image[] = $attachment_image[0];
                    }
                }
            }

            // filters ##
            apply_filters( 'q_get_post_image', $q_get_post_image );

            // test ##
            #pr($q_get_post_image);

            // return img ##
            return $q_get_post_image;

        }
    }
}


if ( ! function_exists( 'q_paged' ) )
{
/**
 * wp page count - for page h1 and title tags uniqueness ##
 *
 * @since 0.1
 */
    function q_paged(){

        // title pagination ##
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        if ( $paged > 1 ) {
            echo '<span class="paged">'.__( "page", 'q-textdomain' ).' '.$paged.'</span>'; // show page number ##
        }

    }
}


if ( ! function_exists( 'q_excerpt_from_id' ) )
{
/**
 * Gets the excerpt of a specific post ID or object
 *
 * @param   $post       object/int  the ID or object of the post to get the excerpt of
 * @param   $length     int         the length of the excerpt in words
 * @param   $tags       string      the allowed HTML tags. These will not be stripped out
 * @param   $extra      string      text to append to the end of the excerpt
 *
 * @link    http://pippinsplugins.com/a-better-wordpress-excerpt-by-id-function/        Reference
 *
 * @since 0.1
 */
    function q_excerpt_from_id( $post, $length = 155, $tags = null, $extra = '&hellip;' )
    {

        if( is_int( $post) ) {
            $post = get_post( $post );
        } elseif( ! is_object( $post ) ) {
            // var_dump( 'no $post' );
            return false;
        }

        if( has_excerpt( $post->ID ) ) {
            $the_excerpt = $post->post_excerpt;
        } else {
            $the_excerpt = $post->post_content;
        }

        $the_excerpt = strip_shortcodes( strip_tags( $the_excerpt, $tags ) );
		#pr( $length );

        if ( $length > 0 && strlen( $the_excerpt ) > $length ) { // length set and excerpt too long so chop ##
            $the_excerpt = substr( $the_excerpt, 0, $length ).$extra;
        }

        // var_dump( $the_excerpt );

        return apply_filters( 'q_excerpt_from_id', $the_excerpt );

    }

}


if ( ! function_exists( 'q_get_terms_exclude' ) )
{
/**
 * function to get_terms with exclusion
 * was called - q_get_the_term_list
 *
 * @since 0.1
 */
    function q_get_terms_exclude( $id = 0, $taxonomy, $before = '', $sep = '', $after = '', $exclude = array() ) {

        $term_links = '';
        $exclude_string = implode ( ',', $exclude );
        $terms = get_terms( $taxonomy, array(
                    'exclude' => $exclude_string,
                ) );

        if ( is_wp_error( $terms ) )
            return $terms;

        if ( empty( $terms ) )
            return false;

        foreach ( $terms as $term ) {

            if( has_term( $term->name, $taxonomy ) ) {
                $link = get_term_link( $term, $taxonomy );
                if ( is_wp_error( $link ) ) {
                    return $link;
                }
                $term_links[] = '<a href="' . $link . '" rel="tag">' . $term->name . '</a>';
            }
        }

        if ( $term_links ) {

            $term_links = apply_filters( "term_links-$taxonomy", $term_links );
            return $before . implode( $sep, $term_links ) . $after;

        } else {

            return 'None';

        }
    }
}


if ( ! function_exists( 'q_query_string' ) )
{
/*
 * get and clean search string
 *
 * @since 0.1
 */
    function q_query_string(){

        global $query_string;
        #echo 'string'.$query_string.'<br />';
        $query_string = esc_html(stripslashes($query_string));
        #echo 'string'.$query_string.'<br />';

        $query_args = explode("&", $query_string);
        $search_query = array();

        foreach($query_args as $key => $string) {
            $query_split = explode("=", $string);
            $search_query[$query_split[0]] = urldecode($query_split[1]);
        } // foreach

        return $search_query;

    }
}


if ( ! function_exists( 'q_get_roles_by_user_id' ) )
{
/**
 * get user role from ID - used for filtering q_authors
 *
 * @since 0.1
 */
    function q_get_roles_by_user_id( $user_id ) {

        $user = get_userdata( $user_id );
        return empty( $user ) ? array() : $user->roles;

    }
}


if ( ! function_exists( 'q_remove_parent_classes' ) )
{
/**
 * remove parent class from nav-menu ##
 *
 * @since 0.1
 */
    function q_remove_parent_classes( $class )
    {
        // check for current page classes, return false if they exist.
        return ($class == 'current_page_item' || $class == 'current_page_parent' || $class == 'current_page_ancestor'  || $class == 'current-menu-item') ? FALSE : TRUE;
    }
}


if ( ! function_exists( 'q_list_image_sizes' ) )
{
/*
 * list all image sizes
 *
 * @link    http://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes
 */
    function q_list_image_sizes(){

        global $_wp_additional_image_sizes;

        $sizes = array();

        foreach( get_intermediate_image_sizes() as $s ){
            $sizes[ $s ] = array( 0, 0 );
            if( in_array( $s, array( 'thumbnail', 'medium', 'large' ) ) ){
                $sizes[ $s ][0] = get_option( $s . '_size_w' );
                $sizes[ $s ][1] = get_option( $s . '_size_h' );
            } else {
                if( isset( $_wp_additional_image_sizes ) && isset( $_wp_additional_image_sizes[ $s ] ) )
                    $sizes[ $s ] = array( $_wp_additional_image_sizes[ $s ]['width'], $_wp_additional_image_sizes[ $s ]['height'], );
            }
        }

        $image_sizes = array();
        foreach( $sizes as $size => $atts ){
            $image_sizes[] = array(
                'name' => $size,
                'width' => $atts[0],
                'height' => $atts[1]
            );
        }

        return $image_sizes;

    }

}


if ( ! function_exists( 'q_custom_taxonomy_dropdown' ) )
{
/*
 * build taxonomy dropdowns
 *
 * @link        http://frankiejarrett.com/create-a-dropdown-of-custom-taxonomies-in-wordpress-the-easy-way/
 */
    function q_custom_taxonomy_dropdown (
        $post_type = 'post',
        $taxonomy,
        $orderby = 'menu_order',
        $order = 'ASC',
        $limit = '-1',
        $name,
        //$show_option_all = null,
        $show_option_none = null,
        $active = '',
        $parent = false,
        $default = '', // passed default value ##
        $value = 'term_id' // term_id or slug ##
    )
        {

        // sort args ##
        $args = array(
            'orderby'       => $orderby,
            'order'         => $order,
            'number'        => $limit,
            'hierarchical'  => $parent,
            //'get'           => 'all'
        );

        $terms = get_terms( $taxonomy, $args );
        $name = ( $name ) ? $name : $taxonomy;
        $active = $active ? $active : ''; // activate select ##

        if ( $terms ) {

            // open select ##
            printf( '<select name="%s" id="%s" class="postform '.$active.'" '.$active.'>', esc_attr( $name ), esc_attr( $name ) );

            // top option ##
            if ( $show_option_none ) {
                printf( '<option value="0" data-value="0" class="default">%s</option>', esc_html( $show_option_none ) );
            }

            // loop out terms in tax according to args ##
            foreach ( $terms as $term ) {

                // add child / parent classes if in the "what" tax ##
                $parent_class = '';
                if ( $name == 'what' ) {

                    if ( $term->parent > 0 ) {

                        $parent_class = 'parent_'.$term->parent;
                        #var_dump($term);

                    } else {

                        $parent_class = 'parent';

                    }

                }

                // highlight passed value ##
                $selected = '';
                if ( $default && $term->term_id == $default ) {
                    $selected = ' selected="selected"';
                    $parent_class .= ' filter-selected';
                }

                // term_id or slug ##
                $term_value = ( $value == 'term_id' ) ? esc_attr( $term->term_id ) : esc_html( $term->slug );

                if ( $value == 'term_id' ) {

                    $tax_query = array(
                        array(
                            'taxonomy'  => $taxonomy,
                            'field'     => 'id',
                            'terms'     => $term_value
                        )
                    );

                } else {

                    $tax_query = array(
                        array(
                            'taxonomy'  => $taxonomy,
                            'field'     => 'slug',
                            'terms'     => $term_value
                        )
                    );

                }

                // build id array of posts and add to data value in option ##
                $data_ids = array();

                $data_ids_args = array(
                    'post_type'         => $post_type,
                    'posts_per_page'    => -1,
                    'post_status'       => 'publish',
                    //'category'          => $taxonomy
                    'tax_query' =>      $tax_query
                );

                $data_ids_posts = get_posts($data_ids_args);

                // grab all ids ##
                foreach( $data_ids_posts as $post ) {
                    $data_ids[] = $post->ID;
                }

                // flatten id array to string - seperate with a comma ##
                $data_ids_string = implode( ',', $data_ids );

                // echo option ##
                printf( '<option value="%s" class="'.$parent_class.'" data-value="%s" data-ids="'.$data_ids_string.'" data-tax="'.$name.'=%s" '.$selected.'>%s</option>', $term_value, esc_attr( $term->term_id ), esc_attr( $term->term_id ), esc_html( $term->name ) );
            }
            print( '</select>' );
        }
    }

}


if ( ! function_exists( 'q_get_attachment' ) )
{
/*
 * Function to grab attachment data from ID
 * src: http://wordpress.org/ideas/topic/functions-to-get-an-attachments-caption-title-alt-description
 */
    function q_get_attachment( $attachment_id ) {

        if ( ! $attachment_id ) return;

        $attachment = get_post( $attachment_id );

        if ( !$attachment ) return;

        return array(
            'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
            'caption' => $attachment->post_excerpt,
            'description' => $attachment->post_content,
            'href' => get_permalink( $attachment->ID ),
            'src' => $attachment->guid,
            'title' => $attachment->post_title
        );

    }

}


if ( ! function_exists('q_inspect_queue') )
{
/**
 * list all enqueued styles & scripts
 */
    function q_inspect_queue() {

        global $wp_scripts, $wp_styles;

        echo 'Styles: ';
        foreach( $wp_styles->queue as $handle ) :
            echo $handle.',';
        endforeach;
        echo '<br/>';

        echo 'Scripts: ';
        foreach( $wp_scripts->queue as $handle ) :
            echo $handle.',';
        endforeach;
        echo '<br/>';

    }

}



if ( ! function_exists( 'list_shortcodes' ) )
{
/**
 * list all shortcodes
 *
 * @since   1.0
 * @usage    list_shortcodes();
 */
    function list_shortcodes()
    {

        global $shortcode_tags;

        pr( $shortcode_tags, __( 'listing all shortcodes' ) );

    }
}


if ( ! function_exists( 'q_get_option' ) )
{
/**
 * Return path or uri or child or parent
 * required for child / parent compatibility functions
 *
 * @request string indicating which settings to retrieve
 * @echo boolean echo or return the results
 * @uses get_stylesheet_directory_uri()
 * @uses get_stylesheet_directory()
 * @uses get_bloginfo()
 * @uses TEMPLATEPATH ( constant )
 * @return array
 * @since 0.1
 */
    function q_get_option( $request = null, $echo = false )
    {

        if ( is_null ( $request ) ) { return false; }

        #delete_transient( 'q_get_option' ); // after URL change ##
        #$q_get_option = get_transient( 'q_get_option' );

        #if ( false === $q_get_option || '' === $q_get_option ) { // nothing in transients ##

            // variables ##
            #$q_get_option["path_child"] = get_stylesheet_directory().'/'; // child path ##
            #$q_get_option["uri_child"] = get_stylesheet_directory_uri().'/'; // child uri ##
            #$q_get_option["path_parent"] = TEMPLATEPATH.'/'; // parent path ##
            $q_get_option["path_parent"] = get_template_directory().'/'; // parent path ##
            $q_get_option["uri_parent"] = get_bloginfo('template_directory').'/'; // parent uri ##
            $q_get_option["path_root"] = ABSPATH; // wp root directory ##

            #set_transient( 'q_get_option', $q_get_option, 60*60*60 );

        #} // transient empty ##

        // apply filters ##
        $q_get_option = apply_filters( 'q_get_option', $q_get_option );

        if ( $request && array_key_exists( $request, $q_get_option ) ) {

            // echo or return string ##
            if ( $echo === true ) {
                echo $q_get_option[$request];
            } else {
                return $q_get_option[$request];
            }

        }

    }
}


if ( ! function_exists( 'q_get_template_part' ) )
{
/**
 * Include requested template file.
 * Checks first in child theme, then in parent theme, then in Q - includes in that order.
 * Warns only admin if template part not found.
 *
 * @param       string      $include        Include file with path from library folder - i.e. - "snippets/loop-nothing.php"
 * @param       string      $setting_title  Title of Setting option - optional
 * @param       boolean     $setting        Setting option constant - optional
 *
 * @uses        get_option()
 * @uses        Q::log()
 * @since       0.1
 * @return      Mixed
 */
function q_get_template_part( $include, $setting_title = 'Warning', $setting = true )
    {

        if ( $setting === true && $include ) { // check to see if setting is active and include path passed ##

            // if ( file_exists( q_get_option("path_child").'library/'.$include ) ) { // load child over parent ##

            //     return require( q_get_option("path_child").'library/'.$include );

            // } else
            
            if ( file_exists( q_get_option("path_parent").'library/'.$include ) ) { // load from parent ##

                return require( q_get_option("path_parent").'library/'.$include );

            // } elseif ( file_exists( q_get_option("path_parent").'framework/'.$include ) ) { // load from parent ##

            //     return require( q_get_option("path_parent").'framework/'.$include );

            } else { // not found issue a warning ##

                // issue template warning ##
                Q::log( 'warning: '.$include, $setting_title, $setting );

            }

        } else { // setting inactive or no path passed ##

            // issue template warning ##
            Q::log( 'warning: '.$include, $setting_title, $setting );

        }

    }

}


if ( ! function_exists( 'q_plugin_data' ) )
{
/**
* Get Q Plugin data
*
* @return  void
* @since   0.3
*/
    function q_plugin_data( $refresh = false ){

        if ( $refresh === true ) {

            #echo 'refrshing stored framework data<br />'; ##
            delete_option( 'q_plugin_data' ); // delete option ##

        }

        if ( ! $q_plugin_data = get_option( 'q_plugin_data' ) ) {

            $q_plugin_data = array (
                'version'       => Q_VERSION
            );

            if ( $q_plugin_data ) {

                q_add_update_option( 'q_plugin_data', $q_plugin_data, '', 'yes' );

            }

        }

        return q_array_to_object( $q_plugin_data );

        #$q_framework_data = json_decode( $q_framework_data );

    }
}


if ( ! function_exists( 'q_theme_data' ) )
{
/**
* Get installed theme data
*
* @return  Object
* @since   0.3
*/
    function q_theme_data( $refresh = false )
    {

       if ( $refresh === true ) {

           #echo 'refrshing stored theme data<br />'; ##
           delete_option( 'q_theme_data' ); // delete option ##

       }

       // declare global variable ##
       global $q_theme_data;

       $q_theme_data = get_option( 'q_theme_data' );
       if ( ! get_option( 'q_theme_data' ) ) {

           #echo 'stored theme option empty<br />';
           #$q_theme_data = @file_get_contents( q_get_option("uri_parent")."library/version/");

           if(function_exists( 'wp_get_theme' )){
               $q_theme_data = wp_get_theme( get_option( 'template' ));
               #$theme_version = $theme_data->Version;
           } else {
               $q_theme_data = get_theme_data( get_template_directory() . '/style.css');
               #$theme_version = $theme_data['Version'];
           }
           #$theme_base = get_option('template');

           if ( $q_theme_data ) {

               q_add_update_option( 'q_theme_data', $q_theme_data, '', 'yes' );
               #echo 'stored fresh theme data<br />';

           }

       }

       return q_array_to_object( $q_theme_data );
       #$q_theme_data = json_decode( $q_theme_data );

    }
}



if ( ! function_exists( "q_trim_post" ) )
{
/**
 * Trim down $post objects based on passed of defined values
 *
 * @since       1.5.2
 * @param       type        $posts
 * @param       type        $args
 * @return      Object      Trimmed WP_Posts Object
 */
function q_trim_post( $post = null, $args = array() )
{

    // sanity check ##
    if ( is_null ( $post ) || ! is_object ( $post ) ) { return $post; }

    // set-up default properties to trim ##
    $defaults = array(
            'post_content'
        ,   'post_excerpt'
        ,   'post_author'
        ,   'post_date'
        ,   'post_date_gmt'
        ,   'post_status'
        ,   'comment_status'
        ,   'ping_status'
        ,   'post_password'
        ,   'post_name'
        ,   'to_ping'
        ,   'pinged'
        ,   'post_modified'
        ,   'post_modified_gmt'
        ,   'post_content_filtered'
        ,   'post_parent'
        ,   'guid'
        ,   'menu_order'
        ,   'post_type'
        ,   'post_mime_type'
        ,   'comment_count'
        ,   'filter'
    );

    // merge defaults and passed args ##
    $args = wp_parse_args( $args, $defaults );

    foreach ( $args as $arg ) {

        unset( $post->$arg );

    }

    // kick it back ##
    return $post;

}}



if ( ! function_exists( "q_remove_style" ) )
{
/**
 * Strip <style> tags from post_content
 *
 * @link        http://stackoverflow.com/questions/5517255/remove-style-attribute-from-html-tags
 * @since       0.7
 * @return      string HTML formatted text
 */
function q_remove_style( $input = null )
{

    if ( is_null ( $input ) ) { return false; }

    return preg_replace( '/(<[^>]+) style=".*?"/i', '$1', $input );

}}


if ( ! function_exists( "q_add_http" ) )
{
/**
 * Add http:// if it's not in the URL?
 *
 * @param string $url
 * @return string
 * @link    http://stackoverflow.com/questions/2762061/how-to-add-http-if-its-not-exists-in-the-url
 */
function q_add_http( $url = null ) {

    if ( is_null ( $url ) ) { return false; }

    if ( ! preg_match("~^(?:f|ht)tps?://~i", $url ) ) {

        $url = "http://" . $url;

    }

    return $url;

}}


if ( ! function_exists( "pra" ) )
{
/**
 * Print Variable, with WP is_administrator check
 *
 * @param       type        $var
 * @param       string      $title
 * @return      Mixed       var_dump of passed variable
 */
function pra( $var = null, $title = null )
{

    // sanity check ##
    if ( is_null ( $var ) ) { return false; }

    if ( ! current_user_can( 'manage_options' ) ) { return false; }

    // add a title to the dump ? ##
    if ( $title ) $title = '<h2>'.$title.'</h2>';

    // print it out ##
    print '<pre class="var_dump">'; echo $title; var_dump( $var ); print '</pre>';

}}


if ( ! function_exists( "q_restrict_manage_posts" ) )
{
/**
 * restrict_manage_posts filter
 *
 * @param       Array       $args       Array of custom post types and taxaonomies to filter
 */
function q_restrict_manage_posts( $args = null )
{

    // sanity check ##
    if ( is_null ( $args ) || ! array_filter( $args ) ) { return false; }

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
                $tax_obj = get_taxonomy($tax_slug);
                //pr($tax_obj);
                $tax_name = $tax_obj->labels->name;
                //pr($tax_name);
                // output html for taxonomy dropdown filter
                echo "<select name='".strtolower($tax_slug)."' id='".strtolower($tax_slug)."' class='postform'>";
                echo "<option value=''>".__( "All", 'q-textdomain' )." $tax_name</option>";
                q_generate_taxonomy_options( $tax_slug, $tax_name, 0, 0, (isset($_GET[strtolower($tax_slug)])? $_GET[strtolower($tax_slug)] : null) );
                echo "</select>";

            }

        }

    }

}}



if ( ! function_exists( "q_generate_taxonomy_options" ) )
{
/**
 * Generate Admin <select>'s ##
 *
 * @param type $tax_slug
 * @param type $tax_name
 * @param type $parent
 * @param type $level
 * @param type $selected
 */
function q_generate_taxonomy_options( $tax_slug, $tax_name, $parent = '', $level = 0,$selected = null)
{

    $args = array( 'show_empty' => 1, 'hierarchical' => true );
    #if( !is_null($parent)) {
        #$args = array( 'get' => 'all' );
    #}

    $terms = get_terms( $tax_slug, $args );

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

}}
