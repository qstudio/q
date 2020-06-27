<?php

namespace q\get;

use q\core;
use q\core\helper as h;
use q\ui;
use q\get;

class deprecated extends \Q {

	
	






	
    /**
     * Build HTML WordPress Gallery
     *
     * @since       1.0.0
     * @return      string   HTML
     */
    public static function gallery( $args = array() )
    {

        // test incoming args ##
        #pr( $args );

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_gallery );

        // add post ID, if not passed ##
        $args->post = isset ( $args->post ) ? $args->post : $the_post->ID ;

        // test compilled arguments ##
        #pr( $args );

        // empty gallery ##
        $gallery = false;

        // define gallery source and grab images ##
        if ( isset( $args->post_meta ) ) {

            // built using new ACF field type ##
            if ( isset( $args->acf ) ) {

                $gallery = \get_field( $args->post_meta, $args->post );
                #pr( $gallery );

            } else if ( $post_meta = \get_post_meta( $args->post, $args->post_meta, true ) ) {

                #pr( $post_meta );

                $gallery = self::gallery_images( $args->post, $args->img_handle, $args->limit, $post_meta );

            }

        } else {

            $gallery = self::gallery_images( $args->post, $args->img_handle, $args->limit );

        }

        // test if we got any images ##
        if ( ! $gallery  ) { return false; }

        // test the gallery array ##
        #pr( $gallery );

        // loop over gallery ##
        foreach ( $gallery as $image ) {

            // toggle img / src depending on type ##
            $img_src = isset( $args->acf ) ? $image["sizes"]["{$args->img_handle}"] : $image['src'] ;

?>
                <img src="<?php echo $img_src; ?>" />
<?php

        }

    }



    /**
     * Check if a post has a gallery of images ( more than one ) or a post image
     *
     * @since       1.3.2
     * @return      String      HTML for image or gallery
     */
    public static function gallery_or_image( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object ) \wp_parse_args( $args, \q_theme::$the_gallery_or_image );

        // add post ID, if not passed ##
        $args->post = isset ( $args->post ) ? $args->post : $the_post->ID ;

        // empty gallery ##
        $gallery = false;

        // define gallery source and grab images ##
        if ( isset( $args->post_meta ) ) {

            // built using new ACF field type ##
            if ( isset( $args->acf ) ) {

                $gallery = \get_field( $args->post_meta, $args->post );
                #pr( $gallery );

            } else if ( $post_meta = \get_post_meta( $args->post, $args->post_meta, true ) ) {

                #pr( $post_meta );

                $gallery = self::get_gallery_images( $args->post, $args->img_handle, $args->limit, $post_meta );

            }

        } else {

            $gallery = self::get_gallery_images( $args->post, $args->img_handle, $args->limit );

        }

        // build it out ##
        if ( $gallery && is_array ( $gallery ) ) {

            // close content area ##
            if ( $args->layout == 'full_width' ) theme::the_content_close();

            // test the gallery array ##
            #pr( $gallery );

            // loop over gallery ##
            foreach ( $gallery as $image ) {

                // toggle img / src depending on type ##
                $img_src = isset( $args->acf ) ? 'url' : 'src' ;

?>
                    <img src="<?php echo $image[$img_src]; ?>" />
<?php

            }

            // reopen content area ##
            if ( $args->layout == 'full_width' ) theme::the_content_open();

        // check if we have a featured image ##
        } else if ( \has_post_thumbnail( $the_post->ID ) ) {

            echo \get_the_post_thumbnail( $the_post->ID, $args->img_handle, array( 'class' => $args->img_handle ) );

        }

    }




    /**
     * get all images from a post gallery
     *
     * @param   Object      $post       Post object to examine
     * @param   String      $size       Handle of image size to return
     * @param   Integer     $limit      Number of images to return, defaults to 10
     * @param   String      $field      Allows for a custom field to be used to grab the gallery shortcode
     * @since 1.1.0
     */
    public static function gallery_images( $post = null, $size = null, $limit = null, $field = null ){

        // passed post or global ##
        if ( ! $post ) global $post;

        // kickout if no post object ##
        if ( ! is_object( $post ) ) { $post = \get_post( $post ); }

        // kick out if we can't get a real post object ##
        if ( ! $post || ! is_object( $post ) ) {
            #echo 'kicked';
            return false;
        }

        // limit set ##
        $limit = ! is_null ( $limit ) ? $limit : \get_site_option( 'posts_per_page', 10 );

        // set content or field to grab [gallery] shortcode from ##
        $content = ! is_null ( $field ) ? $field : $post->post_content ;

        // test passed content field ##
        #pr( $content );

        // http://wordpress.stackexchange.com/questions/80408/how-to-get-page-post-gallery-attachment-images-in-order-they-are-set-in-backend
        $pattern = \get_shortcode_regex();

        if( preg_match_all( '/'. $pattern .'/s', $content, $matches )
            && array_key_exists( 2, $matches )
            && in_array( 'gallery', $matches[2] ) ):

            $keys = array_keys( $matches[2], 'gallery' );

            foreach( $keys as $key ):
                $atts = \shortcode_parse_atts( $matches[3][$key] );
                    if( array_key_exists( 'ids', $atts ) ):

                        $query_images = new \WP_Query(
                            array(
                                'posts_per_page'    => $limit,
                                'post_type'         => 'attachment',
                                'post_status'       => 'inherit',
                                'post__in'          => explode( ',', $atts['ids'] ),
                                'orderby'           => 'post__in'
                            )
                        );

                        \wp_reset_query();

                    endif;
            endforeach;

        endif;

        // empty array, just in case ##
        $images = array();

        // build images array ##
        foreach ( $query_images->posts as $image ) {

            // image src ##
            if ( $size ) {

                $image_src_array = \wp_get_attachment_image_src( $image->ID, $size );
                $image_src = $image_src_array[0];

                // get updated meta ##
                $image_meta = array(
                    "width" => $image_src_array[1], // width ##
                    "height" => $image_src_array[2], // height ##
                );

            } else {

                $image_meta = \wp_get_attachment_metadata( $image->ID ); // get dimensions ##
                $image_src = $image->guid;

            }

            $images[] = array (
                "ID"            => $image->ID,
                "src"           => $image_src,
                "width"         => $image_meta["width"],
                "height"        => $image_meta["height"],
                'alt'           => \get_post_meta( $image->ID, '_wp_attachment_image_alt', true ),
                'caption'       => $image->post_excerpt ? $image->post_excerpt : 'undefined',
                'description'   => $image->post_content,
                'href'          => \get_permalink( $image->ID ),
                #'src'           => $image->guid,
                'title'         => $image->post_title
            );
        }

        return $images;

    }




    /**
     * Get post avatar parts
     *
     * @since       1.0.1
     * @return      Mixed       Object || Boolean false
     */
    public static function avatar( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = self::the_post( $args ) ) { return false; }

        // set-up new object ##
        $object = new \stdClass;

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        $args = ( object )wp_parse_args( $args, \q_theme::$the_avatar );

        // add post ID, if not passed ##
        $args->post = isset ( $args->post ) ? $args->post : $the_post->ID ;

        // test args ##
        #pr( $args );

        // holder ##
        $object->src = $args->holder;

        // class ##
        $object->class = $args->class;

        // if taxonomy archive ##
        if ( $args->style == 'tax' ) {

            // category ##
            $object->category = \wp_get_post_terms( $args->post, 'category' );
            #pr( $object->category );

            // categories have a smaller holder image ##
            $object->src = helper::get( "theme/images/global/102x102.png", 'return' );

            if ( isset( $object->category[0] ) ) {

                // check for image ##
                if ( $image_src = \get_field( 'category_image', 'category_'.$object->category[0]->term_id ) ) {

                    // get attached image src ##
                    $image_src = \wp_get_attachment_image_src( $image_src, 'circle-small' );
                    #pr( $image_src );
                    $object->src = $image_src[0]; // take first array item ##

                }

            }

            // css ##
            #$object->class = 'circle-small';

        // single post ##
        } else {

            $image = \wp_get_attachment_image_src( \get_post_thumbnail_id( $args->post ), 'circle-large' ) ;

            if ( $image ) {

                $object->src = $image[0];

            }

            // css ##
            #$object->class = 'circle-large';

        }

        // kick back colour ##
        return $object;

    }



    /**
    * Get Video URL from oEmbed field in ACF
    *
    * @since		1.4.5
    * @return		String		Video URL
    */
    public static function video_thumbnail_uri( $video_uri = null )
    {

        $thumbnail_uri = '';

        // determine the type of video and the video id
        if ( ! $video = self::parse_video_uri( $video_uri ) ) { return false; }

        // get youtube thumbnail
        if ( $video['type'] == 'youtube' ) {
            $thumbnail_uri = 'https://img.youtube.com/vi/' . $video['id'] . '/mqdefault.jpg';
        }

        // get vimeo thumbnail
        if( $video['type'] == 'vimeo' ) {

            $thumbnail_uri = self::get_vimeo_thumbnail_uri( $video['id'] );

        // get wistia thumbnail
        } else if( $video['type'] == 'wistia' ) {

            $thumbnail_uri = self::get_wistia_thumbnail_uri( $video_uri );

        // get default/placeholder thumbnail ##
        } else if( ! $thumbnail_uri || \is_wp_error( $thumbnail_uri ) ) {

            return false;

        }

        //return thumbnail uri
        return $thumbnail_uri;

    }



	
    /**
    * Parse the video uri/url to determine the video type/source and the video id
    *
    * @since		1.4.5
    * @return		Array
    */
    public static function parse_video_uri( $url ) {

        // Parse the url
        $parse = parse_url( $url );

        // Set blank variables
        $video_type = '';
        $video_id = '';

        // Url is http://youtu.be/xxxx
        if ( $parse['host'] == 'youtu.be' ) {

            $video_type = 'youtube';
            $video_id = ltrim( $parse['path'],'/' );

        }

        // Url is http://www.youtube.com/watch?v=xxxx
        // or http://www.youtube.com/watch?feature=player_embedded&v=xxx
        // or http://www.youtube.com/embed/xxxx
        if ( ( $parse['host'] == 'youtube.com' ) || ( $parse['host'] == 'www.youtube.com' ) ) {

            $video_type = 'youtube';

            parse_str( $parse['query'] );

            $video_id = $v;

            if ( !empty( $feature ) )
                $video_id = end( explode( 'v=', $parse['query'] ) );

            if ( strpos( $parse['path'], 'embed' ) == 1 )
                $video_id = end( explode( '/', $parse['path'] ) );

        }

        // Url is http://www.vimeo.com
        if ( ( $parse['host'] == 'vimeo.com' ) || ( $parse['host'] == 'www.vimeo.com' ) ) {

            $video_type = 'vimeo';
            $video_id = ltrim( $parse['path'],'/' );

        }

        $host_names = explode(".", $parse['host'] );
        $rebuild = ( ! empty( $host_names[1] ) ? $host_names[1] : '') . '.' . ( ! empty($host_names[2] ) ? $host_names[2] : '');

        // Url is an oembed url wistia.com ##
        if ( ( $rebuild == 'wistia.com' ) || ( $rebuild == 'wi.st.com' ) ) {

            $video_type = 'wistia';

            if ( strpos( $parse['path'], 'medias' ) == 1 ) {

                $video_id = end( explode( '/', $parse['path'] ) );

            }

        }

        // If recognised type return video array
        if ( ! empty( $video_type ) ) {

            return array(
                'type' => $video_type,
                'id' => $video_id
            );

        } else {

            return false;

        }

    }


    /**
    * Takes a Vimeo video/clip ID and calls the Vimeo API v2 to get the large thumbnail URL.
    *
    * @since		1.4.5
    * @return		String		Video Thumbnail Src
    */
    public static function vimeo_thumbnail_uri( $clip_id = null )
    {

        // sanity check ##
        if ( is_null( $clip_id ) ) return false;

        $vimeo_api_uri = 'http://vimeo.com/api/v2/video/' . $clip_id . '.php';
        $vimeo_response = \wp_remote_get( $vimeo_api_uri );

        if( \is_wp_error( $vimeo_response ) ) {

            return $vimeo_response;

        } else {

            $vimeo_response = unserialize( $vimeo_response['body'] );
            return $vimeo_response[0]['thumbnail_large'];

        }

    }


    /**
    * Takes a wistia oembed url and gets the video thumbnail url.
    *
    * @since		1.4.5
    * @return		String		Video Thumbnail Src
    */
    public static function wistia_thumbnail_uri( $video_uri = null )
    {

        // sanity check ##
        if ( is_null( $video_uri ) ) return false;

        $wistia_api_uri = 'http://fast.wistia.com/oembed?url=' . $video_uri;
        $wistia_response = \wp_remote_get( $wistia_api_uri );

        if( \is_wp_error( $wistia_response ) ) {

            return $wistia_response;

        } else {

            $wistia_response = json_decode( $wistia_response['body'], true );
            return $wistia_response['thumbnail_url'];

        }

    }


}
