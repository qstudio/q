/*
Global JS work - all devices 
*/

// Avoid `console` errors in browsers that lack a console.
(function(){for(var a,e=function(){},b="assert clear count debug dir dirxml error exception group groupCollapsed groupEnd info log markTimeline profile profileEnd table time timeEnd timeStamp trace warn".split(" "),c=b.length,d=window.console=window.console||{};c--;)a=b[c],d[a]||(d[a]=e)})();

// LoremImages
(function(b){b.fn.loremImages=function(e,d,j){var a=b.extend({},b.fn.loremImages.defaults,j);return this.each(function(c,k){var f=b(k),g="";for(c=0;c<a.count;c++){var h=e+Math.round(Math.random()*a.randomWidth),i=d+Math.round(Math.random()*a.randomHeight);g+=a.itemBuilder.call(f,c,"//lorempixel.com/"+(a.grey?"g/":"")+h+"/"+i+"/"+(a.category?a.category+"/":"")+"?"+Math.round(Math.random()*1E3),h,i)}f.append(g)})};b.fn.loremImages.defaults={count:10,grey:0,randomWidth:0,randomHeight:0,category:0,itemBuilder:function(e,
d){return'<img src="'+d+'" alt="Lorempixel">'}}})(jQuery);

// jQuery easing 1.3
jQuery.easing.jswing=jQuery.easing.swing;
jQuery.extend(jQuery.easing,{def:"easeOutQuad",swing:function(e,a,c,b,d){return jQuery.easing[jQuery.easing.def](e,a,c,b,d)},easeInQuad:function(e,a,c,b,d){return b*(a/=d)*a+c},easeOutQuad:function(e,a,c,b,d){return-b*(a/=d)*(a-2)+c},easeInOutQuad:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a+c:-b/2*(--a*(a-2)-1)+c},easeInCubic:function(e,a,c,b,d){return b*(a/=d)*a*a+c},easeOutCubic:function(e,a,c,b,d){return b*((a=a/d-1)*a*a+1)+c},easeInOutCubic:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a+c:
b/2*((a-=2)*a*a+2)+c},easeInQuart:function(e,a,c,b,d){return b*(a/=d)*a*a*a+c},easeOutQuart:function(e,a,c,b,d){return-b*((a=a/d-1)*a*a*a-1)+c},easeInOutQuart:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a*a+c:-b/2*((a-=2)*a*a*a-2)+c},easeInQuint:function(e,a,c,b,d){return b*(a/=d)*a*a*a*a+c},easeOutQuint:function(e,a,c,b,d){return b*((a=a/d-1)*a*a*a*a+1)+c},easeInOutQuint:function(e,a,c,b,d){return 1>(a/=d/2)?b/2*a*a*a*a*a+c:b/2*((a-=2)*a*a*a*a+2)+c},easeInSine:function(e,a,c,b,d){return-b*Math.cos(a/
d*(Math.PI/2))+b+c},easeOutSine:function(e,a,c,b,d){return b*Math.sin(a/d*(Math.PI/2))+c},easeInOutSine:function(e,a,c,b,d){return-b/2*(Math.cos(Math.PI*a/d)-1)+c},easeInExpo:function(e,a,c,b,d){return 0==a?c:b*Math.pow(2,10*(a/d-1))+c},easeOutExpo:function(e,a,c,b,d){return a==d?c+b:b*(-Math.pow(2,-10*a/d)+1)+c},easeInOutExpo:function(e,a,c,b,d){return 0==a?c:a==d?c+b:1>(a/=d/2)?b/2*Math.pow(2,10*(a-1))+c:b/2*(-Math.pow(2,-10*--a)+2)+c},easeInCirc:function(e,a,c,b,d){return-b*(Math.sqrt(1-(a/=d)*
a)-1)+c},easeOutCirc:function(e,a,c,b,d){return b*Math.sqrt(1-(a=a/d-1)*a)+c},easeInOutCirc:function(e,a,c,b,d){return 1>(a/=d/2)?-b/2*(Math.sqrt(1-a*a)-1)+c:b/2*(Math.sqrt(1-(a-=2)*a)+1)+c},easeInElastic:function(e,a,c,b,d){var e=1.70158,f=0,g=b;if(0==a)return c;if(1==(a/=d))return c+b;f||(f=0.3*d);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return-(g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f))+c},easeOutElastic:function(e,a,c,b,d){var e=1.70158,f=0,g=b;if(0==a)return c;if(1==
(a/=d))return c+b;f||(f=0.3*d);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return g*Math.pow(2,-10*a)*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInOutElastic:function(e,a,c,b,d){var e=1.70158,f=0,g=b;if(0==a)return c;if(2==(a/=d/2))return c+b;f||(f=d*0.3*1.5);g<Math.abs(b)?(g=b,e=f/4):e=f/(2*Math.PI)*Math.asin(b/g);return 1>a?-0.5*g*Math.pow(2,10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+c:0.5*g*Math.pow(2,-10*(a-=1))*Math.sin((a*d-e)*2*Math.PI/f)+b+c},easeInBack:function(e,a,c,b,d,f){void 0==
f&&(f=1.70158);return b*(a/=d)*a*((f+1)*a-f)+c},easeOutBack:function(e,a,c,b,d,f){void 0==f&&(f=1.70158);return b*((a=a/d-1)*a*((f+1)*a+f)+1)+c},easeInOutBack:function(e,a,c,b,d,f){void 0==f&&(f=1.70158);return 1>(a/=d/2)?b/2*a*a*(((f*=1.525)+1)*a-f)+c:b/2*((a-=2)*a*(((f*=1.525)+1)*a+f)+2)+c},easeInBounce:function(e,a,c,b,d){return b-jQuery.easing.easeOutBounce(e,d-a,0,b,d)+c},easeOutBounce:function(e,a,c,b,d){return(a/=d)<1/2.75?b*7.5625*a*a+c:a<2/2.75?b*(7.5625*(a-=1.5/2.75)*a+0.75)+c:a<2.5/2.75?
b*(7.5625*(a-=2.25/2.75)*a+0.9375)+c:b*(7.5625*(a-=2.625/2.75)*a+0.984375)+c},easeInOutBounce:function(e,a,c,b,d){return a<d/2?0.5*jQuery.easing.easeInBounce(e,2*a,0,b,d)+c:0.5*jQuery.easing.easeOutBounce(e,2*a-d,0,b,d)+0.5*b+c}});

// jQuery throttle / debounce - v1.1 - 3/7/2010
(function(b,c){var $=b.jQuery||b.Cowboy||(b.Cowboy={}),a;$.throttle=a=function(e,f,j,i){var h,d=0;if(typeof f!=="boolean"){i=j;j=f;f=c}function g(){var o=this,m=+new Date()-d,n=arguments;function l(){d=+new Date();j.apply(o,n)}function k(){h=c}if(i&&!h){l()}h&&clearTimeout(h);if(i===c&&m>e){l()}else{if(f!==true){h=setTimeout(i?k:l,i===c?e-m:e)}}}if($.guid){g.guid=j.guid=j.guid||$.guid++}return g};$.debounce=function(d,e,f){return f===c?a(d,e,false):a(d,f,e!==false)}})(this);

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

// FINDING AND BINDING
jQuery( document ).ready( function($){
    slyInit();
    // sticky header
    $(window).scrollTop() > $('#sticky-top').height() ? $('#sticky-top').addClass('fixed-top') : $('#sticky-top').removeClass('fixed-top');
    $(window).on('scroll', function(event) {
        $(window).scrollTop() > $('#sticky-top').height() ? $('#sticky-top').addClass('fixed-top') : $('#sticky-top').removeClass('fixed-top');
    });

	// newsletter cancel bound to modal close ##
	jQuery( 'body' ).on( "click", '.nl-decline', function(e){

		// if ( q_constant_contact.debug ) console.log( 'Cancel signup..' );
		q_modal_do_close(e);

	});

	// REMOVING BLANK HREF BUTTONS 
	jQuery( 'a.button' ).each( function(){
		if( jQuery( this ).attr("href") == '' ) {
			console.log( 'removing blank href...' );
			jQuery( this ).remove();
		}
	});

	// do lazy loading ##
	q_do_lazy();

});

// HEADROOM
function q_do_headroom(){
	
  	var hdr = document.getElementById("header");
  
	// construct an instance of Headroom, passing the element
  	var headroom = new Headroom(hdr);
  
	// initialise
	headroom.init();
  
}


function q_snackbar( options ){

	// check if the object exists ##
	if ( typeof jQuery.snackbar === 'undefined' ) {

		// console.log( 'No snacks available...');

		return false;

	}

	// global config ##
	defaults = { 
		'content'	: 'Something went wrong :(',
		'class' 	: 'q-snackbar',
		'timeout'	: 5000,
		'id'		: 'q-snackbar',
		'stack'		: false // only show one at a time ##
	};
	
	// merge passed options ##
	jQuery.extend( defaults, options );

	// no stacking ##	
	if ( ! options.stack ) {

		// console.log( 'Hiding Snacks' );

		// hide open snacks.. ##
		// jQuery('#'+options.id).hide().snackbar("hide");
		q_snackbar_delete();

	}

	// test ##
	// console.dir( options );

	// run the snackbar ##
	$snackbar = jQuery.snackbar(options);

	// kick something back ##
	return $snackbar;

}


function q_snackbar_delete(){

	jQuery('#snackbar-container > .snackbar').slideUp().remove();

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
			q_equal_heights();
		}
	});

}

function q_video_play(){
	
	if ( $q_video_playing ) {

		// console.log( 'Video already playing...' );

		return false;

	}

	jQuery("#frontpage-cta source").each(function() {

		var sourceFile = jQuery(this).attr("data-src");
		jQuery(this).attr("src", sourceFile);
		var video = this.parentElement;

		video.load();
		// video.play();

		$video = jQuery("video#frontpage-cta");
		videoElement = $video[0];

		$video.on('canplaythrough', function(e){

			// console.log( 'Video ready..' );

			$video.get(0).play();

			$q_video_playing = true;

			return true;

		});

		// If the video is in the cache of the browser,
		// the 'canplaythrough' event might have been triggered
		// before we registered the event handler.
		if (videoElement.readyState > 3) {
			
			// console.log( 'Video cached and ready..' );

			video.get(0).play();

			$q_video_playing = true;

			return true;

		}

	});

}

function q_video_prepare(){

	// if video is already playing, bulk ##
	if ( $q_video_prepared ) {

		// console.log('video already prepared...');
		
	}

	// console.log('preparing video'); //sanity

	// get video ##
	var $video = jQuery('#frontpage-cta');

	// size video ##
	var v_height = jQuery('.home-banner').height();
	var v_width = jQuery('.home-banner').width();
	$video.css("height", v_height);
	$video.css("width", v_width );
	$video.removeClass('hidden');
	$video.addClass('fadeIn');

	// update tracker ##
	$q_video_prepared = true;

}

// Cookies
function createCookie( name, value, days ) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	}
	else var expires = "";               

	// console.log( 'create: '+name );

	document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie( name ) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');

	// console.log( 'read: '+name );
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}

function eraseCookie( name ) {
	createCookie( name, "", -1 );
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

$q_recaptcha_checked = false;
$q_recaptcha_loaded = false;
$q_recaptcha_timeout = null;
$grecaptcha_id = null;
$form_moved = false;
$event_form = false;
$load_count = typeof $load_count === "undefined" ? 0 : $load_count ; // allow templates to override
$load_loop = 0;
$q_modal_key = false;

jQuery(document).bind('gform_post_render', function(){
	
	// q_is_local() ? $q_recaptcha_checked = true : false ;

	// console.log( 'is_local: '+q_is_local() );
	// console.log( 'Form Rendered...' );
	// console.log( 'Load Count: '+ $load_count );

	if ( 
		$load_count >= 1 
		// || q_is_modal()
	) {

		// disable future submits ##
		q_gf_disable_submit();
		
		// recaptcha ##
		recapatch_render();

	}

	if( jQuery( "div.validation_error" ).length != 0 ){
		
		// console.log( 'load loop: '+$load_loop );
		if ( 1 == $load_count ) { 
		
			// console.log( 'Clearing validation...' );
			q_gf_validation(); 

		}

		if ( $load_count >= 2 ) {

			// scroll to top ##
			jQuery('.featherlight-content').animate({
				scrollTop:  jQuery('.featherlight-content h1').outerHeight() +
							jQuery('.featherlight-content .tldr').outerHeight() +
							jQuery('.featherlight-content .details').outerHeight()/* +
							jQuery('.gform_heading').position().top + 200*/
							// Top of the form or first invalid field?
			}, 500 );

		}
		
		// console.log( 'form id: '+$form_id );

		// reset count ##
		// $load_count = 0;

	}

	// iterate ##
	$load_count = $load_count + 1 ;
	// $load_loop = $load_loop + 1;

	// console.log( '$q_modal_key: '+$q_modal_key );

});

function q_gf_submit(){

	// console.log( 'Submit GF Form' );

	jQuery( '.gform_footer input[type="submit"]' ).click();
	jQuery( '.gform_next_button:first' ).click();

}

function q_gf_disable_submit( $element ){

	$element = $element || '.gform_wrapper form input[type="submit"].gform_button';

	if ( 
		$q_recaptcha_checked 
	) {
		
		// console.log( 'Captcha already complete, no need to disable..' );

		return true;

	}

	// console.log( 'disable submit...' );

	jQuery( $element ).prop( 'disabled', true );

}

function q_gf_enable_submit( $element ){
	
	$element = $element || '.gform_wrapper form input[type="submit"].gform_button';

	// console.log( 'enable submit...' );

	jQuery( $element ).prop( 'disabled', false );

}


function q_gf_validation( $display ){
	
	$display = $display || 'hide';

	// console.log( 'q_gf_validation - action: '+$display );

	if ( 'hide' == $display ) {

		jQuery( ".validation_error, .validation_message" ).hide() ;

	} else {

		jQuery( ".validation_error, .validation_message" ).show() ;

	}
	
}

var recaptcha_callback = function () {
  
	// console.log( 'reCaptcha ready..' );

	// q_gf_disable_submit();

  	recapatch_render();
  
}

function q_recaptcha_checked(){

	// console.log( 'q_recaptcha_checked called..' );

	q_gf_enable_submit();

	$q_recaptcha_checked = true;

}

function q_recaptcha_expired(){

	// console.log( 'q_recaptcha_expired called..' );

}

function recaptcha_position( $to, $from ){

	// console.log( 'recaptcha_position called..' );

	// jQuery( '.gform_body > ul' ).append( jQuery('#recaptcha') );
	jQuery( '.gform_button' ).closest("div").before( jQuery('#recaptcha') );

}

function recapatch_render( $element, $api_key ){

	$element = $element || 'recaptcha';
	$api_key = $api_key || '6LfBpzkUAAAAACJFHVDIsRf61JSRy7o0-PH9eD_P'; // @todo - make API key global var ##

	if ( 
		$q_recaptcha_checked 
	) {
		
		// console.log( 'Captcha already complete, no need to render..' );

		return true;

	}

	// console.log( 'recaptcha_render called..' );

	// check if we have recaptcha available, if not set timeout to call this method again ##
	if ( 
		typeof grecaptcha === 'undefined' 
		|| typeof grecaptcha.render !== 'function'
		// || ! $q_recaptcha_loaded
	) {

		// console.log( 'recaptcha not available.. try again in 500 ms..' );

		window.clearTimeout( $q_recaptcha_timeout );
	
		$q_recaptcha_timeout = window.setTimeout(
			function(){
				recapatch_render( $element, $api_key );
			},500
		); 

		return false;

	}

	$q_recaptcha_loaded = true;

	jQuery('#recaptcha').empty().remove();
	// recapatch_destroy();

	// create new element ##
	var $div = jQuery("<div>", {id: "recaptcha", "class": "recaptcha"});
	jQuery( 'body' ).append( $div )

	$grecaptcha_id = grecaptcha.render( 
		$element 
		, {
			'sitekey'           : $api_key,
			'theme'             : 'light',
			'callback'          : q_recaptcha_checked,
			'expired-callback'  : q_recaptcha_expired,
		}
	);

	// position things ##
	recaptcha_position();

}


function recapatch_destroy( $element){
	
	$element = $element || '#recaptcha';

	// console.log( 'recaptcha_destroy called..' );

	grecaptcha.reset( $grecaptcha_id );

	jQuery( $element ).empty().remove();

}


// extend script loader ##
jQuery.loadScript = function (url, callback) {
	jQuery.ajax({
		url: url,
		dataType: 'script',
		success: callback,
		async: true
	});
}


function q_is_local(){

	return -1 === location.hostname.indexOf( 'qlocal.com' ) ? false : true ;

}

/** @viktor - calls to sly will be install specific, so need to move to a something not loaded globally via Q */
function slyInit() {
    var $frame = jQuery('.sly-all');
    var $wrap  = $frame.parent();

    if (!$frame.length) {
        return;
    }

    // Call Sly on frame
    $frame.sly({
        pagesBar: '.pages',
        activatePageOn: 'click',

        horizontal: 1,
        itemNav: 'basic',
        smart: 1,
        activateMiddle: 1,
        activateOn: 'click',
        mouseDragging: 1,
        touchDragging: 1,
        releaseSwing: 1,
        startAt: 0,
        scrollBar: $wrap.find('.scrollbar'),
        scrollBy: 1,
        speed: 300,
        elasticBounds: 1,
        easing: 'easeOutExpo',
        dragHandle: 1,
        dynamicHandle: 1,
        clickBar: 1,

        // Buttons
        prevPage: $wrap.find('.prev'),
        nextPage: $wrap.find('.next')
    });
}

