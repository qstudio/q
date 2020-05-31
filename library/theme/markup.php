<?php

namespace q\theme;

// use q\core\core as core;
use q\core\helper as helper;
use q\plugin\google as google;
use q\controller\minifier as minifier;
// use q\controller\css as css;

// load it up ##
// \q\theme\ui::run();

class markup extends \Q {


	/**
     * Markup object based on %placeholders% and template
     *
     * @since    2.0.0
     * @return   Mixed
     */
    public static function apply( $markup = null, $data = null )
    {

        // sanity ##
        if (
            is_null( $markup )
            || is_null( $data )
            ||
            (
                ! is_array( $data )
                && ! is_object( $data )
            )
        ) {

            helper::log( 'missing parameters' );

            return false;

        }

        #helper::log( $data );
        #helper::log( $markup ); 

        // grab markup ##
        $return = $markup;

        // format markup with translated data ##
        foreach( $data as $key => $value ) {

            #helper::log( 'key: '.$key.' / value: '.$value );

            // only replace keys found in markup ##
            if ( false === strpos( $markup, '%'.$key.'%' ) ) {

                #helper::log( 'skipping '.$key );

                continue ;

            }

            // template replacement ##
            $return = str_replace( '%'.$key.'%', $value, $return );

        }

        #helper::log( $return );

        // return markup ##
        return $return;

    }



	
    public static function minify( $string = null, $type = 'js' )
    {

        // if debugging, do not minify ##
        if ( self::$debug ) {

            return $string;

        }

        switch ( $type ) {

            case "css" :

                $string = minifier::css( $string );

                break ;

            case "js" :
            default :

                $string = minifier::javascript( $string );

                break ;

        }

        // kick back ##
        return $string;

    }



	
    /**
    * Strip unwated tags and shortcodes from the_content
    *
    * @since       1.4.4
    * @return      String
    */
    public static function clean( $string = null )
    {

        // bypass ##
        return $string;

        // sanity check ##
        if ( is_null ( $string ) ) { return false; }

        // do some laundry ##
        $string = strip_tags( $string, '<a><ul><li><strong><p><blockquote><italic>' );

        // kick back the cleaned string ##
        return $string;

    }


    
    /**
     * Open HTML Tag, adding set classes
     *
     * @since       1.0.3
     * @return      string HTML formatted text
     */
    public static function get_tag( $tag = null, $classes = array(), $action = 'open' )
    {

        // sanity check ##
        if ( is_null ( $tag ) ) { return false; }

        // helper::log( $classes );

        // switch over "open" OR "close" ##
        switch ( $action ) {

            case 'close' :

?>
            </<?php echo $tag; ?>>
<?php

            break ;

            default :
            case 'open' :

?>
            <<?php echo $tag; ?> class="<?php echo implode( array_filter( (array)$classes ), " " ); ?>">
<?php
            break ;

        }

    }

    

    /**
     * Strip <style> tags from post_content
     *
     * @link        http://stackoverflow.com/questions/5517255/remove-style-attribute-from-html-tags
     * @since       0.7
     * @return      string HTML formatted text
     */
    public static function remove_style( $input = null )
    {
    
        if ( is_null ( $input ) ) { return false; }
    
        return preg_replace( '/(<[^>]+) style=".*?"/i', '$1', $input );
    
    }



    public static function rip_tags($string) {

        // ----- remove HTML TAGs -----
        $string = preg_replace ('/<[^>]*>/', ' ', $string);
    
        // ----- remove control characters -----
        $string = str_replace("\r", '', $string);    // --- replace with empty space
        $string = str_replace("\n", ' ', $string);   // --- replace with space
        $string = str_replace("\t", ' ', $string);   // --- replace with space
    
        // ----- remove multiple spaces -----
        $string = trim(preg_replace('/ {2,}/', ' ', $string));
    
        return $string;
    
    }



    public static function chop( $content, $length = 0 ) 
    {

        if ( $length > 0 ) { // trim required, perhaps ##
        
            if ( strlen( $content ) > $length ) { // long so chop ##
                return substr( $content , 0, $length ).'...';
            } else { // no chop ##
                return $content;
            }
        
        } else { // send as is ##
        
            return $content;
        
        }

    }



    public static function load_google_web_fonts( $fonts, $use_fallback = true, $debug = false ) 
    {

        // bounce to Google method ##
        return google::fonts( $fonts, $use_fallback = true, $debug = false );

    }

}