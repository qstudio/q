/*
* q/theme/1/handheld
*/

// @benny @todo - these hardcoded figures seem risky in case of zoom or browser differences ##
var 
    htmlHeight = 0,
    bbHeight = 37,
    headerHeight = 80,
    footerHeight = 141,        
    max_height = null
;

if( typeof jQuery !== 'undefined' ) {

     /*/ ADD DONATE LINK ON MOBILE NAV BEFORE DOCUMENT READY/*/
	if( !jQuery('#mobile_donate').length ) {  
		jQuery('.nav.navbar-nav').append('<li id="mobile_donate"><a title="Donate" href="/donate/">Donate</a></li>');
	}
	if(!jQuery('#mobile_close').length ) {
		jQuery('.nav.navbar-nav').append('<span id="mobile_close">X</span>');
	}
    
    jQuery(document).ready(function() {	

        headerHeight = jQuery('#header').outerHeight(),
        footerHeight = jQuery('footer').outerHeight();

        jQuery( '#mob_men_back').click(function(e){
            jQuery( this ).removeClass('menu_slidein').addClass('menu_slideout');
            jQuery('a.bs_backwrap').remove();           
        });
        jQuery( '')

        /*/ EVENTS /*/
        if(jQuery('.event-template-default').length ) {
            event_single_reflow();
        }

        /*/NAV MENU JS - Load before document ready /*/
        jQuery( ".navbar-toggle" ).click(function() {
            jQuery('ul.nav.navbar-nav li.menu-item-has-children ul.dropdown-menu').attr('style', 'display: none;');
            jQuery('a.bs_backwrap').remove();
            //jQuery( jQuery(this).attr('data-target') ).slideToggle( 400 );
            jQuery( jQuery(this).attr('data-target') ).removeClass('menu_slideout').addClass('menu_slidein').show();
        });
        bind_menu_x_click('#mobile_close');
        jQuery( ".dropdown-toggle" ).click(function(e) {
            e.preventDefault();
            $navitem = jQuery( this ).text();
            $submen = jQuery( this ).next();
            $submen.attr('style','display: block;');
            $backmen = document.createElement('a');
            jQuery($backmen).attr({'class': 'bs_backwrap', 'target': '#', });
            jQuery($backmen).append( '<span class="bs_backlink">' + $navitem + '</span>' );
            $submen.prepend($backmen);
            $submen.removeClass('menu_slideout').addClass('menu_slidein');
            bind_menuclick( $backmen );

        });
        
    });

    jQuery(window).on('load', function(){ // load
        
        htmlHeight = jQuery('html').outerHeight()

        // q_resize();
        // q_equal_heights();
        setTimeout( function() { q_resize() }, 1000 );

    });

    jQuery(window).on('resize orientationchange', function(){ // load

        htmlHeight = jQuery('html').outerHeight()

        q_equal_heights();

    });

}


function q_equal_heights() {
   	//console.log( 'Doing q_equal_heights...' );
    jQuery('.equal-group').each(function(){
        
        jQuery(this).addClass('equal-current');
        // var cols_per_row = 1;
        var cols_per_row = jQuery(this).data('equal-cols');
        if ( 
            window.innerWidth >= 620
            // || jQuery('body').hasClass('support')
        ){
            cols_per_row = 2;
        }
        
        // if ( jQuery(this).hasClass('sly-event-handheld')) {
            // cols_per_row = 4;
        // }
        
        // console.log( 'cols_per_row:' +cols_per_row);
        q_even_elements('.equal-current .equal-item', cols_per_row);
        jQuery(this).removeClass('equal-current');

    });
}

// Equal heights function for siblings
// cols_per_row - number of columns per row
function q_even_elements(selector, cols_per_row) {
    // jQuery(selector).css('height', 'auto');
    var rows = Math.ceil(jQuery(selector).length/cols_per_row);
   //console.log( 'rows: '+rows );
   //console.log( 'cols_per_row: '+cols_per_row );
    for (var i = 0; i < rows; i++) {
        var first_element = i * cols_per_row;
        // var max_height = 0;
        for (var j = 0; j < cols_per_row; j++) {
            var this_height = jQuery(selector+':eq('+ (first_element + j) + ')').height();
           //console.log( 'Height option: '+this_height );
            max_height = Math.max(max_height, this_height);
        }

       //console.log( 'Max Height: '+max_height );

        for (var j = 0; j < cols_per_row; j++) {
            jQuery(selector+':eq(' + (first_element + j) + ')').height(max_height).addClass("equalized");
        }
    }
    max_height = 0;
}


function q_resize(){

    // console.log( 'Resizing..' );
    if( typeof( Event ) === 'function' ) {
        // modern browsers
        window.dispatchEvent( new Event( 'resize' ) );

    } else {

        // for IE and other old browsers
        // causes deprecation warning on modern browsers
        var evt = window.document.createEvent('UIEvents'); 
        evt.initUIEvent('resize', true, false, window, 0); 
        window.dispatchEvent(evt);

    }
}
function bind_menu_x_click( tgt ){
    jQuery( tgt ).click(function(e){
        jQuery( '#navbar' ).removeClass('menu_slidein').addClass('menu_slideout');
        setTimeout(function(){
            jQuery('#navbar').hide();
        }, 200);
        
    });
}
function bind_menuclick( tgt ){
    jQuery( tgt ).first().click(function(e){
        jQuery( this ).parent().removeClass('menu_slidein').addClass('menu_slideout');
        jQuery('a.bs_backwrap').remove();           
    });
}

// get admin bar height ##
function adminBarHeight() {
    return (jQuery('body').hasClass('admin-bar')) ? jQuery('#wpadminbar').height() : 0;
}

// reflow event elements ##
function event_single_reflow(){ /*/ reflow the events-single page for mobile /*/
    
    var $ids_to_reflow = [//ID to reflow and ID of new parent or preceding element
        
        ['top-register', 'event-meta', true ],
        ['related-posts', 'event-map', false ],
        ['sharelines', 'page-content', false ]
        
    ];

    jQuery($ids_to_reflow).each( function(){ reflow( this[0], this[1], this[2] ); } );

}

function reflow( $ID, $TARGET, $PARENT ) {
	
	/*/ Argument 1 - id of element to reflow,
        Argument 2 - where to reflow this element - either the parent id for this to go into or the id for the element this follows
        Argument 3 - Boolean. True = Argument 2 is the parent element, False = Argument 2 is the element this comes after
    /*/
    var $move = jQuery( '#' + $ID );
    var $moveto = jQuery( '#' + $TARGET );
    if( $move.length && $moveto.length ) {
        if( typeof( $PARENT !== 'undefined' ) && $PARENT ) {
            jQuery($move).remove();
            jQuery($moveto).prepend($move);
        } else {
            jQuery($move).remove();
            jQuery($moveto).after($move);
        }
    }
	return false;
	
}