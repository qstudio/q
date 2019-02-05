/*
 * Hover Effects and Front-end UI JS
 * (c) 2017 Q Studio - qstudio.us
 */

// jQuery ##
if ( typeof jQuery !== 'undefined' ) {

    jQuery(document).ready(function() {

    // HERE IS WHERE WE PUT OUR JS FUNCTIONS FOR ALL THE COOL EFFECTS :)
    // MAKE WINDOW OBJECTS FOR EASIER BINDING
    // ALL EFFECTS MUST HAVE CORRESPONDING OUT EFFECTS


    window.more_info = function (e){
    	// console.log('more info firing');
    }
    window.more_info_out = function(e){
    	// console.log('more info out firing');
    }
    window.smallfade = function (e){
    	// if(effects_map.debug == 'true') console.log( e ); 
    	jQuery( e ).addClass( 'smallfade');
    }
    window.smallfade_out = function (e){
    	// if(effects_map.debug == 'true') console.log( e ); 
    	jQuery( e ).removeClass( 'smallfade');
    }
    window.extlink = function(e){
        var link_tgt = jQuery( e ).find('.brush-container').first();
        link_tgt.append('<div id="hoverlink"><div class="linkup"><span class="external_icon"></span> VISIT SITE</div></div>');
        link_tgt.addClass('add_link');
    }
    window.extlink_out = function(e){
        var link_tgt = jQuery( e ).find('.brush-container').first();
        link_tgt.removeClass('add_link').addClass('hide_link');
        jQuery('#hoverlink').remove();
        window.setTimeout( function(){  
            link_tgt.stop().removeClass('hide_link');
        }, 200 );
    }
    window.showdetails = function(e){
    	var deets = jQuery( e ).find( '.hover-up-details');
        var headline = jQuery( e ).find( 'h3').first();
        if( headline.height() > 32){ jQuery('.details').css({'margin-top' : headline.height() - 32 });}
    	deets.append('<div id="hoverlink"><span class="external_icon"></span> VISIT SITE</div>');

        headline.addClass('moveup');
    	deets.addClass('showdetails');


    }

    window.showdetails_out = function(e){
    	var deets = jQuery( e ).find('.hover-up-details');
        var headline = jQuery( e ).find( 'h3').first();
        headline.removeClass('moveup').addClass('movedown');
    	deets.removeClass('showdetails').addClass('hidedetails');
        jQuery('.details').css({'margin-top' : '0'});
    	jQuery('#hoverlink').remove();
    	window.setTimeout( function(){   
            deets.stop().removeClass('hidedetails'); 
            headline.stop().removeClass('movedown'); 
            }, 200 );
    
    }

/* This all works kinda but since a height is hardcoded there's still no way to get real offest

    function doit_showdetails(){
    	var doit = false;
    	for(var i in effects_map.map){ if( effects_map.map[i] === 'showdetails' ) { doit = i; }
    	}
    	return doit;  
    }
    function set_showdetails(){
    	var tgts = doit_showdetails();
    	if( tgts && jQuery( '.'+tgts ).length > 0 ){
    		var cards = jQuery( '.'+tgts );
    		cards.each( function(){
    			var angkor = jQuery( this ).find( 'a' ).first();
    			var offset = ( jQuery( this ).height() / 2 ) + angkor.height() + 10;
    			// should be 370 / 2 = 185 + 113 + 10 
    			jQuery( this ).find('.hover-up-details').attr( 'style', 'margin-top:' + offset + 'px;');
    		});

    	}
    }
    */


    window.quickzoom = function(e){

    };
    window.quickzoom_out = function(e){

    };

    window.smalljump = function (e){
    	// if(effects_map.debug == 'true') console.log( e ); 
    	jQuery( e ).parent().addClass( 'hoverframe' );
    	jQuery( e ).addClass( 'smalljump');
    }
    window.smalljump_out = function (e){
    	// if(effects_map.debug == 'true') console.log( e );
    	var ev = jQuery(e);//turn to jQuery object - query once
    	ev.removeClass( 'smalljump');
    	window.setTimeout( function(){ 
    		ev.parent().removeClass( 'hoverframe' );
    	//	jQuery('.hoverframe').stop().css( {'width' : '', 'height' : '', 'box-shadow' : ''} );
    	}, 200);
    }
    window.pickup = function (e){
        // if(effects_map.debug == 'true') console.log( e ); 
        jQuery( e ).addClass( 'pickup');
    }
    window.pickup_out = function (e){
        // if(effects_map.debug == 'true') console.log( e );
        var ev = jQuery(e);//turn to jQuery object - query once
        ev.removeClass( 'pickup');
    }

/*
*  THE BIND!!!
*  store each function name in data attribute then bind it's hover
*
*
*/

	function do_bind(){
  	for( var i in effects_map.map ) {
  		var tar = '.' + i;
  		var fun = String( effects_map.map[i] );
  		jQuery( tar ).attr('data-hvr', fun );
  		jQuery( tar ).hover(
  				function(){
  					window[ jQuery( this ).attr( 'data-hvr') ]( this ) }, 
  				function(){
  					window[ jQuery( this ).attr( 'data-hvr') + '_out']( this ) }
  			);
		
		}
	}
  	


    

    do_bind();
    //set_showdetails();

    });

}