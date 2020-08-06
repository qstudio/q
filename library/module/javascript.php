<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\javascript::__run();

class javascript extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('javascript') );
		if ( 
			! isset( core\option::get('extension')->javascript )
			|| true !== core\option::get('extension')->javascript 
		){

			// h::log( 'd:>Tab is not enabled.' );

			return false;

		}
		
        // add html to footer ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
				'handle'    => str_replace( __NAMESPACE__.'\\', '', __CLASS__ )
			]);
		}, 3 );

    }


	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_extension( $field )
    {

		// pop on a new choice ##
		$field['choices']['javascript'] = 'Global Javascript';

		// make it selected ##
		$field['default_value'][0] = 'javascript';

		// kick back ##
		return $field;

	}


    
    
    /**
    * JS for modal
    *
    * @since    2.0.0
    * @return   String HTML
    */
    public static function javascript( $args = null )
    {

    // helper::log( self::$args );

?>
<script>
// Avoid `console` errors in browsers that lack a console.
(function(){for(var a,e=function(){},b="assert clear count debug dir dirxml error exception group groupCollapsed groupEnd info log markTimeline profile profileEnd table time timeEnd timeStamp trace warn".split(" "),c=b.length,d=window.console=window.console||{};c--;)a=b[c],d[a]||(d[a]=e)})();

// jQuery IE 11 assign polyfill - https://stackoverflow.com/questions/35215360/getting-error-object-doesnt-support-property-or-method-assign
if (typeof Object.assign !== 'function') {
	Object.assign = function(target) {
	  'use strict';
	  if (target == null) {
		throw new TypeError('Cannot convert undefined or null to object');
	  }
  
	  target = Object(target);
	  for (var index = 1; index < arguments.length; index++) {
		var source = arguments[index];
		if (source != null) {
		  for (var key in source) {
			if (Object.prototype.hasOwnProperty.call(source, key)) {
			  target[key] = source[key];
			}
		  }
		}
	  }
	  return target;
	};
}

function q_timestamp_to_human( timestamp ){

	var now = new Date(),
	secondsPast = (now.getTime() - timestamp) / 1000;

	if (secondsPast < 60) {
		return parseInt(secondsPast) + 's';
	}
	if (secondsPast < 3600) {
		return parseInt(secondsPast / 60) + 'm';
	}
	if (secondsPast <= 86400) {
		return parseInt(secondsPast / 3600) + 'h';
	}
	if (secondsPast > 86400) {
		day = timestamp.getDate();
		month = timestamp.toDateString().match(/ [a-zA-Z]*/)[0].replace(" ", "");
		year = timestamp.getFullYear() == now.getFullYear() ? "" : " " + timestamp.getFullYear();
		return day + " " + month + year;
	}

}

function q_do_lazy() {
	
	// check if the object exists ##
	if ( 
		typeof jQuery.fn.Lazy === 'undefined' 
	) {

		console.log( 'No lazy available...');

		return false;

	}

	// lazy loading ##
	jQuery('.lazy').Lazy({
		// delay: 5000, // temp delay ##
		// placeholder: "../css/images/holder/greenheart.svg",
		beforeLoad: function(element) {
			// console.log( 'Loading: '+element.data("src") );
		},
		afterLoad: function(element) {
			element.addClass( 'lazied' ).removeClass( 'lazy' );
		},
		onFinishedAll: function() {
			// var lazies = document.getElementsByClassName('lazy');
			// while (lazies.length) lazies[0].classList.remove('lazy'); //loop from bottom
			// q_equal_heights();
		}
	});

}

function q_get_hash_value_from_key( $key ){

	$key = $key || null;

	// get new hash string ##
    $hash = window.location.hash.substring(1);
    // console.log( 'hash is: '+$hash );

    // check if hash includes any forward slashes and if so, get first value ##
    if ( $hash.toLowerCase().indexOf( $key ) >= 0 ) {

        var $bits = $hash.split('/');

        $filter_key = q_extract_key_value( $bits, $key );

        // console.log( 'Filter is $key number: '+$filter_key );

        // get next $key value ##
        if ( $hash = q_get_next_key( $bits, $filter_key ) ) {
			// console.log( 'Next $key: '+$hash );

			// console.log( 'hash contains '+ $key +' command: '+$hash );
			
		} else {

			// console.log( 'hash contains '+ $key +' command, but value is missing.' );

			// nada ##
			$hash = false;

		}

    } else {

        // console.log( 'hash is not a '+ $key +' command' );

		// nada ##
        $hash = false;

	}

	// console.log( 'Returning hash: '+$hash );
	
	// kick it back ##
	return $hash;

}

function q_extract_key_value( obj, value ) {

	return obj.indexOf( value );

	// return Object.keys(obj)[Object.values(obj).indexOf(value)];
	
	// var vals = Object.keys(obj).map(function(value) {
	// 	return obj[value];
	// });

}

function q_get_next_key(object, $key){   
	var found = 0; 
	for(var k in object){
	  if(found){ return object[k]; }
	  if(k == $key){ found = 1; }
	}
}

function q_check_password_strength( 
	$pass_one,
	$pass_two,
	$strength_result,
	$submit_button,
	blacklist_array 
){

	var pass_one = $pass_one.val();
	var pass_two = $pass_two.val();

	// Reset the form & meter
	$submit_button.attr( 'disabled', 'disabled' );
	$strength_result.removeClass( 'short bad good strong' );

	// Extend our blacklist array with those from the inputs & site data
	blacklist_array = blacklist_array.concat( wp.passwordStrength.userInputBlacklist() )

	// Get the password strength
	var strength = wp.passwordStrength.meter( pass_one, blacklist_array, pass_two );

	// console.log( 'Strength: '+strength );

	// Add the strength meter results
	switch ( strength ) {

		case 2:
			$strength_result.addClass( 'bad' ).html( pwsL10n.bad );
			break;

		case 3:
			$strength_result.addClass( 'good' ).html( pwsL10n.good );
			break;

		case 4:
			$strength_result.addClass( 'strong' ).html( pwsL10n.strong );
			break;

		case 5:
			$strength_result.addClass( 'bad' ).html( pwsL10n.mismatch );
			break;

		default:
			$strength_result.addClass( 'bad' ).html( pwsL10n.short );

	}

	// The meter function returns a result even if pass_two is empty,
	// enable only the submit button if the password is strong and
	// both passwords are filled up
	if ( 
		( 
			strength === 3 //good ##
			|| strength === 4 // strong ##
		)
		&& '' !== pass_two.trim() // not empty ##
	) {

		// console.log( 'Renable save' );

		$submit_button.removeAttr( 'disabled' );

	}

	return strength;

}
</script>
<?php

    }


}
