<?php

namespace q\module;

use q\core;
use q\view;
use q\core\helper as h;
use q\asset;

// load it up ##
\q\module\bs_scrollspy::__run();

class bs_scrollspy extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		if ( 
			! isset( core\option::get('module')->bs_scrollspy )
			|| true !== core\option::get('module')->bs_scrollspy 
		){

			// h::log( 'd:>bs_scrollspy is not enabled.' );

			return false;

		}

		// add acf fields ##
		// \add_action( 'acf/init', function() { \q\plugin\acf::add_field_groups( self::add_field_groups() ); }, 1 );
		
        // add JS to footer/script ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
			]);
		}, 3 );

    }

	

	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['bs_scrollspy'] = 'Bootstrap ~ Scrollspy';

		// make it selected ##
		$field['default_value'][0] = 'bs_scrollspy';

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
if( typeof jQuery !== 'undefined' ) {

	jQuery(window).load(function(){

		// offset ##
		var navOffset = 0 || jQuery('#wpadminbar').height();

		// check for accordion hash ##
		anchor_hash = q_get_hash_value_from_key( 'anchor' );
		// console.log( 'anchor hash: '+anchor_hash );
		var anchor_loaded = false;

		// Do scrollto ##
		if ( anchor_hash && ! anchor_loaded ) {

			// console.log( 'scroll to anchor: '+anchor_hash );

			jQuery('html,body').animate({ 
				scrollTop: jQuery( '#'+anchor_hash ).offset().top - navOffset
			}, 100 );

			// just once ##
			anchor_loaded = true;

		}

		// console.log( 'navOffset: '+navOffset );

		if( jQuery( '#bs_scrollspy' ).length ) {

			// move .scrollspy-item links below active page in navigation
			jQuery('.scrollspy-item').insertAfter('.current');

			// reset scroll watcher ##
			clearTimeout( jQuery.data(this, 'scrollTimer'));

			jQuery(window).on('activate.bs.scrollspy', function ( e,obj ) {
				
				// console.log(obj.relatedTarget);

				// drop the hash #
				// scroll_hash = obj.relatedTarget.replace( '#', '' ); 

				// Don't let the browser scroll ##
				// event.preventDefault();
				// window.location.hash = '#/anchor/'+scroll_hash;

				// This function can be namespaced. In this example, we define it on window:
				// window.replaceHash( '#/anchor/'+scroll_hash );

				// window.history.replaceState()
				
				jQuery.data( this, 'scrollTimer', setTimeout(function() {
					
					// do something
					// console.log("Didn't scroll in 1/4 sec..");

					// no scroll items are marked as active ##
					if ( ! jQuery(".scrollspy-item.active")[0]){ 

						// console.log("Can't fund any active scrollspy items..");

						// add class to parent again ##
						jQuery(".list-group-item.current").addClass( 'active' );

					}

				}, 1500 ) );

			});

			// Setup Scrollspy ##
			jQuery('body')
				.attr('data-spy', 'scroll')
				.attr('data-target', '#bs_scrollspy')
				.css({ 'position': 'relative' })
				.scrollspy({ target: '#bs_scrollspy', offset: navOffset });

			// Sort out scrolling ##
			jQuery('.scrollspy-item').click(function(event) {
				
				var href = jQuery(this).attr('href');
				var anchor = jQuery(this).data('anchor');

				// drop the hash #
				// href.replace( '#', '' ); 

				// Don't let the browser scroll ##
				event.preventDefault();
				// window.location.hash = '#/anchor/'+anchor;
				window.replaceHash( '#/anchor/'+anchor );

				// check ##
				// console.log( 'Spy anchor: '+href );

				// Do scrollto ##
				jQuery('html,body').animate({ 
					scrollTop: jQuery( href ).offset().top - navOffset
				}, 100 );

			});

		}

		// anchors ##
		anchors.add('.anchored');

	});

};

// @license magnet:?xt=urn:btih:d3d9a9a6595521f9666a5e94cc830dab83b65699&dn=expat.txt Expat
//
// AnchorJS - v4.2.2 - 2019-11-14
// https://www.bryanbraun.com/anchorjs/
// Copyright (c) 2019 Bryan Braun; Licensed MIT
//
// @license magnet:?xt=urn:btih:d3d9a9a6595521f9666a5e94cc830dab83b65699&dn=expat.txt Expat
!function(A,e){"use strict";"function"==typeof define&&define.amd?define([],e):"object"==typeof module&&module.exports?module.exports=e():(A.AnchorJS=e(),A.anchors=new A.AnchorJS)}(this,function(){"use strict";return function(A){function f(A){A.icon=A.hasOwnProperty("icon")?A.icon:"",A.visible=A.hasOwnProperty("visible")?A.visible:"hover",A.placement=A.hasOwnProperty("placement")?A.placement:"right",A.ariaLabel=A.hasOwnProperty("ariaLabel")?A.ariaLabel:"Anchor",A.class=A.hasOwnProperty("class")?A.class:"",A.base=A.hasOwnProperty("base")?A.base:"",A.truncate=A.hasOwnProperty("truncate")?Math.floor(A.truncate):64,A.titleText=A.hasOwnProperty("titleText")?A.titleText:""}function p(A){var e;if("string"==typeof A||A instanceof String)e=[].slice.call(document.querySelectorAll(A));else{if(!(Array.isArray(A)||A instanceof NodeList))throw new Error("The selector provided to AnchorJS was invalid.");e=[].slice.call(A)}return e}this.options=A||{},this.elements=[],f(this.options),this.isTouchDevice=function(){return!!("ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch)},this.add=function(A){var e,t,i,n,o,s,a,r,c,h,l,u,d=[];if(f(this.options),"touch"===(l=this.options.visible)&&(l=this.isTouchDevice()?"always":"hover"),0===(e=p(A=A||"h2, h3, h4, h5, h6")).length)return this;for(!function(){if(null!==document.head.querySelector("style.anchorjs"))return;var A,e=document.createElement("style");e.className="anchorjs",e.appendChild(document.createTextNode("")),void 0===(A=document.head.querySelector('[rel="stylesheet"], style'))?document.head.appendChild(e):document.head.insertBefore(e,A);e.sheet.insertRule(" .anchorjs-link {   opacity: 0;   text-decoration: none;   -webkit-font-smoothing: antialiased;   -moz-osx-font-smoothing: grayscale; }",e.sheet.cssRules.length),e.sheet.insertRule(" *:hover > .anchorjs-link, .anchorjs-link:focus  {   opacity: 1; }",e.sheet.cssRules.length),e.sheet.insertRule(" [data-anchorjs-icon]::after {   content: attr(data-anchorjs-icon); }",e.sheet.cssRules.length),e.sheet.insertRule(' @font-face {   font-family: "anchorjs-icons";   src: url(data:n/a;base64,AAEAAAALAIAAAwAwT1MvMg8yG2cAAAE4AAAAYGNtYXDp3gC3AAABpAAAAExnYXNwAAAAEAAAA9wAAAAIZ2x5ZlQCcfwAAAH4AAABCGhlYWQHFvHyAAAAvAAAADZoaGVhBnACFwAAAPQAAAAkaG10eASAADEAAAGYAAAADGxvY2EACACEAAAB8AAAAAhtYXhwAAYAVwAAARgAAAAgbmFtZQGOH9cAAAMAAAAAunBvc3QAAwAAAAADvAAAACAAAQAAAAEAAHzE2p9fDzz1AAkEAAAAAADRecUWAAAAANQA6R8AAAAAAoACwAAAAAgAAgAAAAAAAAABAAADwP/AAAACgAAA/9MCrQABAAAAAAAAAAAAAAAAAAAAAwABAAAAAwBVAAIAAAAAAAIAAAAAAAAAAAAAAAAAAAAAAAMCQAGQAAUAAAKZAswAAACPApkCzAAAAesAMwEJAAAAAAAAAAAAAAAAAAAAARAAAAAAAAAAAAAAAAAAAAAAQAAg//0DwP/AAEADwABAAAAAAQAAAAAAAAAAAAAAIAAAAAAAAAIAAAACgAAxAAAAAwAAAAMAAAAcAAEAAwAAABwAAwABAAAAHAAEADAAAAAIAAgAAgAAACDpy//9//8AAAAg6cv//f///+EWNwADAAEAAAAAAAAAAAAAAAAACACEAAEAAAAAAAAAAAAAAAAxAAACAAQARAKAAsAAKwBUAAABIiYnJjQ3NzY2MzIWFxYUBwcGIicmNDc3NjQnJiYjIgYHBwYUFxYUBwYGIwciJicmNDc3NjIXFhQHBwYUFxYWMzI2Nzc2NCcmNDc2MhcWFAcHBgYjARQGDAUtLXoWOR8fORYtLTgKGwoKCjgaGg0gEhIgDXoaGgkJBQwHdR85Fi0tOAobCgoKOBoaDSASEiANehoaCQkKGwotLXoWOR8BMwUFLYEuehYXFxYugC44CQkKGwo4GkoaDQ0NDXoaShoKGwoFBe8XFi6ALjgJCQobCjgaShoNDQ0NehpKGgobCgoKLYEuehYXAAAADACWAAEAAAAAAAEACAAAAAEAAAAAAAIAAwAIAAEAAAAAAAMACAAAAAEAAAAAAAQACAAAAAEAAAAAAAUAAQALAAEAAAAAAAYACAAAAAMAAQQJAAEAEAAMAAMAAQQJAAIABgAcAAMAAQQJAAMAEAAMAAMAAQQJAAQAEAAMAAMAAQQJAAUAAgAiAAMAAQQJAAYAEAAMYW5jaG9yanM0MDBAAGEAbgBjAGgAbwByAGoAcwA0ADAAMABAAAAAAwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAABAAH//wAP) format("truetype"); }',e.sheet.cssRules.length)}(),t=document.querySelectorAll("[id]"),i=[].map.call(t,function(A){return A.id}),o=0;o<e.length;o++)if(this.hasAnchorJSLink(e[o]))d.push(o);else{if(e[o].hasAttribute("id"))n=e[o].getAttribute("id");else if(e[o].hasAttribute("data-anchor-id"))n=e[o].getAttribute("data-anchor-id");else{for(c=r=this.urlify(e[o].textContent),a=0;void 0!==s&&(c=r+"-"+a),a+=1,-1!==(s=i.indexOf(c)););s=void 0,i.push(c),e[o].setAttribute("id",c),n=c}(h=document.createElement("a")).className="anchorjs-link "+this.options.class,h.setAttribute("aria-label",this.options.ariaLabel),h.setAttribute("data-anchorjs-icon",this.options.icon),this.options.titleText&&(h.title=this.options.titleText),u=document.querySelector("base")?window.location.pathname+window.location.search:"",u=this.options.base||u,h.href=u+"#"+n,"always"===l&&(h.style.opacity="1"),""===this.options.icon&&(h.style.font="1em/1 anchorjs-icons","left"===this.options.placement&&(h.style.lineHeight="inherit")),"left"===this.options.placement?(h.style.position="absolute",h.style.marginLeft="-1em",h.style.paddingRight="0.5em",e[o].insertBefore(h,e[o].firstChild)):(h.style.paddingLeft="0.375em",e[o].appendChild(h))}for(o=0;o<d.length;o++)e.splice(d[o]-o,1);return this.elements=this.elements.concat(e),this},this.remove=function(A){for(var e,t,i=p(A),n=0;n<i.length;n++)(t=i[n].querySelector(".anchorjs-link"))&&(-1!==(e=this.elements.indexOf(i[n]))&&this.elements.splice(e,1),i[n].removeChild(t));return this},this.removeAll=function(){this.remove(this.elements)},this.urlify=function(A){return this.options.truncate||f(this.options),A.trim().replace(/\'/gi,"").replace(/[& +$,:;=?@"#{}|^~[`%!'<>\]\.\/\(\)\*\\\n\t\b\v]/g,"-").replace(/-{2,}/g,"-").substring(0,this.options.truncate).replace(/^-+|-+$/gm,"").toLowerCase()},this.hasAnchorJSLink=function(A){var e=A.firstChild&&-1<(" "+A.firstChild.className+" ").indexOf(" anchorjs-link "),t=A.lastChild&&-1<(" "+A.lastChild.className+" ").indexOf(" anchorjs-link ");return e||t||!1}}});
// @license-end
</script>
<?php

    }


}
