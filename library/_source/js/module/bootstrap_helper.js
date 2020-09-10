/* Q Module ~ Bootstrap Helper */

if( typeof jQuery !== 'undefined' ) {

	// assign target ##
	var $bs_element = "#bs_helper";
	var $bs_helper;

	// show breakpoint on load, tooltip will be updated to show actual document dimentions ## 
	jQuery(window).load(function(){

		// assign target ##
		$bs_helper = jQuery($bs_element);

		q_bootstrap_tooltip( true, false );

		// click to add debug borders ##
		$bs_helper.click( function() { 
			jQuery("body").toggleClass("debug"); 
		});

	});

	// resizing, open tooltip and update document dimensions as they change ##
	jQuery(window).on('resize', function(){

		q_bootstrap_tooltip( false, true );

	});

};

// Tooltip with BS breakpoint info ##
function q_bootstrap_tooltip( load, resize ) {

	// sizes ##
	vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
	vh = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);

	// resize ##
	resize = resize || false;
	load = load || false;

	// update attrs ##
	$bs_helper.attr('title', 'Width: '+ vw +'px<br />Height: '+ vh +'px' );
	$bs_helper.html( q_bootstrap_breakpoint().name );

	if ( load ) {

		// trigger tooltip ##
		$bs_helper.tooltip('show');

		setTimeout(function(){
			$bs_helper.tooltip('hide');
		}, 3000);

	}

	// possible delay, for resizing ##
	if ( resize ) {	

		// trigger tooltip ##
		$bs_helper.tooltip('show');

		// set short timeout to update document dimensions, as they change ##
		setInterval(function(){
			jQuery('.tooltip-inner').html( 'Width: '+ vw +'px<br />Height: '+ vh +'px' );
		}, 30);

		// add an event listener to check for an end to the resize action ##
		window.addEventListener(
			"resize",
			q_bootstrap_debounce(
				function(e){
			
					// when resizing is done, set a timeout for 3 secs, then close the tooltip ##
					setTimeout(function(){
						$bs_helper.tooltip('hide');
					}, 3000 );

				}
			), { passive: true } // Passive Event Listener ##
		);

	}

}

// resize debouncer, set form eventlistener ##
function q_bootstrap_debounce( func ){
	  
	var timer;
	return function(event){
		if(timer) clearTimeout(timer);
		timer = setTimeout(func,100,event);
	};

}

/*
https://cdn.jsdelivr.net/npm/bootstrap-detect-breakpoint/src/bootstrap-detect-breakpoint.js
*/
"use strict";

function q_bootstrap_breakpoint() {
  var breakpointNames = ["xl", "lg", "md", "sm", "xs"];
  var breakpointValues = [];

  for (var _i = 0, _breakpointNames = breakpointNames; _i < _breakpointNames.length; _i++) {
    var breakpointName = _breakpointNames[_i];
    breakpointValues[breakpointName] = window.getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-' + breakpointName);
  }

  var i = breakpointNames.length;

  for (var _i2 = 0, _breakpointNames2 = breakpointNames; _i2 < _breakpointNames2.length; _i2++) {
    var _breakpointName = _breakpointNames2[_i2];
    i--;

    if (window.matchMedia("(min-width: " + breakpointValues[_breakpointName] + ")").matches) {
      return {
        name: _breakpointName,
        height: vh,
        width: vw,
        index: i
      };
    }
  }

  return null;
}
