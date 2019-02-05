<?php

/**
 * PHP Helper functions for Q Framework
 *
 * @since 0.1
 */


/*
 * convert an array to an object ##
 *
 * @param array $array
 * @return object
 * @since 0.1
 *
 */
if ( !function_exists( 'q_array_to_object' ) ) {
function q_array_to_object($array) {
    #wp_die( 'here..' );
    if(!is_array($array)) {
        return $array;
    }

    $object = new stdClass();
    if (is_array($array) && count($array) > 0) {
      foreach ($array as $name=>$value) {
         $name = strtolower(trim($name));
         if (!empty($name)) {
            $object->$name = q_array_to_object($value);
         }
      }
      return $object;
    }
    else {
      return false;
    }
}}


/**
 * flatten an Array
 *
 * @since 1.0.1
 */
if ( !function_exists( 'q_flatten_array' ) ) {
function q_flatten_array($array) {
    $result = array();
    foreach($array as $key=>$value) {
        if(is_array($value)) {
            $result = $result + q_flatten_array($value, $key . '.');
        }
        else {
            $result[$key] = $value;
        }
    }
    return $result;
}}

/**
 * Return a random key from an array
 *
 * @since 0.7
 */
if ( !function_exists( 'q_array_random' ) ) {
function q_array_random($arr, $num = 1) {

    if ( !is_array($arr) ) {
        #echo 'not array';
        return;
    }

    shuffle($arr);

    $r = array();
    for ($i = 0; $i < $num; $i++) {
        $r[] = $arr[$i];
    }
    return $num == 1 ? $r[0] : $r;

}}

/**
 * check for a valid internet connection
 *
 * @return boolean
 */
if ( !function_exists( 'is_connected' ) ) {
function is_connected(){
    $connected = @fsockopen( "www.google.com", "80" ); // domain and port
    if ($connected){
        fclose($connected);
        return true; //action when connected
    }else{
        return false; //action in connection failure
    }
    #return $is_conn;
}}


/**
 * validate URL
 *
 * @since 0.4
 */
// if ( !function_exists( 'is_url' ) ) {
// function is_url( $url ){

//     if ( !$url ) return;

//     return preg_replace("
//         #((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",
//         "'<a href=\"$1\" target=\"_blank\">$3</a>$4'",
//         $url
//     );

// }}


if ( ! function_exists( 'q_get_page_depth' ) ) 
{
/**
 * get page depth
 * 
 * @return integer
 * @since 0.1
 */
    function q_get_page_depth( $id = '' ){

        return count( get_post_ancestors( get_post( $id )));

    }
}

/*
 * Human Time Difference including weeks and months
 *
 * @source http://pastebin.com/7zffa3Wn
 * @since 0.3
 */
if ( !function_exists( 'q_human_time_diff' ) ) {
function q_human_time_diff( $from, $to = '' ) {
    $chunks = array(
        array( 60 * 60 * 24 * 365 , '%s year', '%s years' ),
        array( 60 * 60 * 24 * 30 , '%s month', '%s months' ),
        array( 60 * 60 * 24 * 7, '%s week', '%s weeks' ),
        array( 60 * 60 * 24 , '%s day', '%s days' ),
        array( 60 * 60 , '%s hour', '%s hours' ),
        array( 60 , '%s minute', '%s minutes' ),
        array( 1 , '%s second', '%s seconds' ),
    );

    if ( empty( $to ) )
                $to = time();

    $diff = (int) abs( $to - $from );


    for ( $i = 0, $j = count( $chunks ); $i < $j; $i++)
    {
        $seconds = $chunks[$i][0];
        $name1 = $chunks[$i][1];
        $name2 = $chunks[$i][2];

        if ( ( $count = floor( $diff / $seconds ) ) != 0)
            break;
    }

    $since = sprintf( _n( $name1, $name2, $count ), $count );

    $i++;

    if ( $i < $j )
    {
        $seconds_p2 = $chunks[$i][0];
        $name1 = $chunks[$i][1];
        $name2 = $chunks[$i][2];

        if ( ( $count = floor( ( $diff - ( $seconds * $count ) ) / $seconds_p2 ) ) != 0 )
        {
            if( is_rtl() )
                $since = sprintf( _n( $name1, $name2, $count ), $count ) ." ". $since;
            else
                $since = $since. " " . sprintf( _n( $name1, $name2, $count ), $count );
        }
    }

    return $since;
}}



/**
 * rip all tags
 * http://php.net/manual/en/function.strip-tags.php
 */
if ( !function_exists( 'q_rip_tags' ) ) {
function q_rip_tags($string) {

    // ----- remove HTML TAGs -----
    $string = preg_replace ('/<[^>]*>/', ' ', $string);

    // ----- remove control characters -----
    $string = str_replace("\r", '', $string);    // --- replace with empty space
    $string = str_replace("\n", ' ', $string);   // --- replace with space
    $string = str_replace("\t", ' ', $string);   // --- replace with space

    // ----- remove multiple spaces -----
    $string = trim(preg_replace('/ {2,}/', ' ', $string));

    return $string;

}}


/**
 * chop string function
 *
 * @since       0.1
 * @pluggable   true
 */
if ( !function_exists( 'q_chop' ) ) {
function q_chop ( $content, $length = 0 ) {
    if ( $length > 0 ) { // trim required, perhaps ##
        if ( strlen( $content ) > $length ) { // long so chop ##
        	return substr( $content , 0, $length ).'...';
        } else { // no chop ##
        	return $content;
        }
    } else { // send as is ##
        return $content;
    }
}}


/**
 * Load Google Web Fonts
 *
 * Description:	A PHP script for loading Google Webfonts' css files in an orderly manner
 * Version:			0.8
 * Author:			Maarten Zilverberg (www.mzilverberg.com)
 * Examples:                    https://github.com/mzilverberg/LoadGoogleWebfonts
 *
 * Licensed under the GPL license:
 * http://www.gnu.org/licenses/gpl.html
 *
 * Last but not least:
 * if you like this script, I would appreciate it if you took the time to share it
*/
if ( !function_exists( 'q_load_google_web_fonts' ) )
{
    function q_load_google_web_fonts( $fonts, $use_fallback = true, $debug = false ) {

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


/**
* Pretty print_r / var_dump
*
* @since       0.1
* @param       Mixed       $var        PHP variable name to dump
* @param       string      $title      Optional title for the dump
* @return      String      HTML output
*/
if ( ! function_exists ( "pr" ) )
{
    function pr( $var = null, $title = null )
    {

        // sanity check ##
        if ( is_null ( $var ) ) { return false; }

        // add a title to the dump ? ##
        if ( $title ) $title = '<h2>'.$title.'</h2>';

        // print it out ##
        print '<pre class="var_dump">'; echo $title; var_dump($var); print '</pre>';

    }
}

/**
 * Make sure the function does not exist before defining it
 */
if( ! function_exists( 'remove_class_filter' ) ){

	/**
	 * Remove Class Filter Without Access to Class Object
	 *
	 * In order to use the core WordPress remove_filter() on a filter added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove filters with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 * Updated 2-27-2017 to use internal WordPress removal for 4.7+ (to prevent PHP warnings output)
	 *
	 * @param string $tag         Filter to remove
	 * @param string $class_name  Class name for the filter's callback
	 * @param string $method_name Method name for the filter's callback
	 * @param int    $priority    Priority of the filter (default 10)
	 *
	 * @return bool Whether the function is removed.
	 */
	function remove_class_filter( $tag, $class_name = '', $method_name = '', $priority = 10 ) {

		global $wp_filter;

		// Check that filter actually exists first
		if ( ! isset( $wp_filter[ $tag ] ) ) {
			return FALSE;
		}

		/**
		 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
		 * a simple array, rather it is an object that implements the ArrayAccess interface.
		 *
		 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
		 *
		 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
		 */
		if ( is_object( $wp_filter[ $tag ] ) && isset( $wp_filter[ $tag ]->callbacks ) ) {
			// Create $fob object from filter tag, to use below
			$fob       = $wp_filter[ $tag ];
			$callbacks = &$wp_filter[ $tag ]->callbacks;
		} else {
			$callbacks = &$wp_filter[ $tag ];
		}

		// Exit if there aren't any callbacks for specified priority
		if ( ! isset( $callbacks[ $priority ] ) || empty( $callbacks[ $priority ] ) ) {
			return FALSE;
		}

		// Loop through each filter for the specified priority, looking for our class & method
		foreach ( (array) $callbacks[ $priority ] as $filter_id => $filter ) {

			// Filter should always be an array - array( $this, 'method' ), if not goto next
			if ( ! isset( $filter['function'] ) || ! is_array( $filter['function'] ) ) {
				continue;
			}

			// If first value in array is not an object, it can't be a class
			if ( ! is_object( $filter['function'][0] ) ) {
				continue;
			}

			// Method doesn't match the one we're looking for, goto next
			if ( $filter['function'][1] !== $method_name ) {
				continue;
			}

			// Method matched, now let's check the Class
			if ( get_class( $filter['function'][0] ) === $class_name ) {

				// WordPress 4.7+ use core remove_filter() since we found the class object
				if ( isset( $fob ) ) {
					// Handles removing filter, reseting callback priority keys mid-iteration, etc.
					$fob->remove_filter( $tag, $filter['function'], $priority );

				} else {
					// Use legacy removal process (pre 4.7)
					unset( $callbacks[ $priority ][ $filter_id ] );
					// and if it was the only filter in that priority, unset that priority
					if ( empty( $callbacks[ $priority ] ) ) {
						unset( $callbacks[ $priority ] );
					}
					// and if the only filter for that tag, set the tag to an empty array
					if ( empty( $callbacks ) ) {
						$callbacks = array();
					}
					// Remove this filter from merged_filters, which specifies if filters have been sorted
					unset( $GLOBALS['merged_filters'][ $tag ] );
				}

				return TRUE;
			}
		}

		return FALSE;
	}
}

/**
 * Make sure the function does not exist before defining it
 */
if( ! function_exists( 'remove_class_action') ){

	/**
	 * Remove Class Action Without Access to Class Object
	 *
	 * In order to use the core WordPress remove_action() on an action added with the callback
	 * to a class, you either have to have access to that class object, or it has to be a call
	 * to a static method.  This method allows you to remove actions with a callback to a class
	 * you don't have access to.
	 *
	 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
	 *
	 * @param string $tag         Action to remove
	 * @param string $class_name  Class name for the action's callback
	 * @param string $method_name Method name for the action's callback
	 * @param int    $priority    Priority of the action (default 10)
	 *
	 * @return bool               Whether the function is removed.
	 */
	function remove_class_action( $tag, $class_name = '', $method_name = '', $priority = 10 ) {
		remove_class_filter( $tag, $class_name, $method_name, $priority );
	}
}