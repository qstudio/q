<?php

namespace q\theme;

use q\core\core as core;
use q\core\helper as helper;
use q\core\options as options;

// load it up ##
\q\theme\ui::run();

class ui extends \Q {

    public static function run()
    {


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



    public static function load_google_web_fonts( $fonts, $use_fallback = true, $debug = false ) {

        // if debugging, use &lt; and $gt; notation for output as plain text
        // otherwise, use < and > for output as html
        $debug ? $x = array('&lt;', '&gt;') : $x = array('<', '>');
        // create a new font array
        $array = array();
        // create a new fallback array for storing possible fallback urls
        $fallback_urls = array();
        // determine how many fonts are requested by checking if the array key ['name'] exists
        // if it exists, push that single font into the $array variable
        // otherwise, just copy the $fonts variable to $array
        isset($fonts['name']) ? array_push($array, $fonts) : $array = $fonts;
        // request the link for each font
        foreach ($array as $font) {
    
                // set the basic url
                $base_url = 'https://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $font['name']) . ':';
                $url = $base_url;
                // create a new array for storing the font weights
                $weights = array();
                // if the font weights are passed as a string (from which all spaces will be removed), insert each value into the $weights array
                // otherwise, just copy the weights passed
                if(isset($font['weight'])) {
                    gettype($font['weight']) == 'string' ? $weights = explode(',', str_replace(' ', '', $font['weight'])) : $weights = $font['weight'];
                // if font weights aren't defined, default to 400 (normal weight)
                } else {
                    $weights = array('400');
                }
                // add each weight to $url and remove the last comma from the url string
                foreach($weights as $weight) {
                    $url .= $weight . ',';
                    // if the fallback notation is necessary, add a single weight url to the fallback array
                    if($use_fallback && count($weights) != 1) array_push($fallback_urls, "$base_url$weight");
                }
                $url = substr_replace($url, '', -1);
                // normal html output
                echo $x[0] . 'link href="' . $url . '" rel="stylesheet" type="text/css" /' . $x[1] . "\n";
    
        }
        // add combined conditional comment for each font weight if necessary
        if ( $use_fallback && !empty( $fallback_urls ) ) {
                // begin conditional comment
                echo $x[0] . '!--[if lte IE 8]' . $x[1] . "\n";
                // add each fallback url within the conditional comment
                foreach($fallback_urls as $fallback_url) {
                    echo '  ' . $x[0] . 'link href="' . $fallback_url . '" rel="stylesheet" type="text/css" /' . $x[1] . "\n";
                }
                // end conditional comment
                echo $x[0] . '![endif]--' . $x[1] . "\n";
        }
    }

}