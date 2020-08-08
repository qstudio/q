<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_toast::__run();

class bs_toast extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('toast') );
		if ( 
			! isset( core\option::get('extension')->bs_toast )
			|| true !== core\option::get('extension')->bs_toast 
		){

			// h::log( 'd:>Toast is not enabled.' );

			return false;

		}
		
        // add html to footer ##
        \add_action( 'wp_footer', function(){
			asset\javascript::ob_get([
				'view'      => get_class(), 
				'method'    => 'javascript',
				// 'handle'    => str_replace( __NAMESPACE__.'\\', '', __CLASS__ )
			]);
		}, 3 );

        // add CSS to header ##
        \add_action( 'wp_head', function(){
			asset\css::ob_get([
				'view'      => get_class(), 
				'method'    => 'css',
				// 'handle'    => str_replace( __NAMESPACE__.'\\', '', __CLASS__ )
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
		$field['choices']['bs_toast'] = 'Bootstrap Toast';

		// make it selected ##
		$field['default_value'][0] = 'bs_toast';
		
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
/**
 * @author Script47 (https://github.com/Script47/Toast)
 * @description Toast - A Bootstrap 4.2+ jQuery plugin for the toast component
 * @version 1.1.0
 **/
(function ($) {
    const TOAST_CONTAINER_HTML = `<div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>`;

    $.toastDefaults = {
        position: 'bottom-right',
        dismissible: true,
        stackable: true,
        pauseDelayOnHover: true,
        style: {
            toast: '',
            info: '',
            success: '',
            warning: '',
            error: '',
        }
    };

    $('body').on('hidden.bs.toast', '.toast', function () {
        $(this).remove();
    });

    let toastRunningCount = 1;

    function render(opts) {
        /** No container, create our own **/
        if (!$('#toast-container').length) {
            const position = ['top-right', 'top-left', 'top-center', 'bottom-right', 'bottom-left', 'bottom-center'].includes($.toastDefaults.position) ? $.toastDefaults.position : 'top-right';

            $('body').prepend(TOAST_CONTAINER_HTML);
            $('#toast-container').addClass(position);
        }

        let toastContainer = $('#toast-container');
        let html = '';
        let classes = {
            header: {
                fg: '',
                bg: ''
            },
            subtitle: 'text-white',
            dismiss: 'text-white'
        };
        let id = `toast-${toastRunningCount}`;
        let type = opts.type;
        let title = opts.title;
        let subtitle = opts.subtitle;
        let content = opts.content;
        let img = opts.img;
        let delayOrAutohide = opts.delay ? `data-delay="${opts.delay}"` : `data-autohide="false"`;
        let hideAfter = ``;
        let dismissible = $.toastDefaults.dismissible;
        let globalToastStyles = $.toastDefaults.style.toast;
        let paused = false;

        if (typeof opts.dismissible !== 'undefined') {
            dismissible = opts.dismissible;
        }

        switch (type) {
            case 'info':
                classes.header.bg = $.toastDefaults.style.info || 'bg-info';
                classes.header.fg = $.toastDefaults.style.info || 'text-white';
                break;

            case 'success':
                classes.header.bg = $.toastDefaults.style.success || 'bg-success';
                classes.header.fg = $.toastDefaults.style.info || 'text-white';
                break;

            case 'warning':
                classes.header.bg = $.toastDefaults.style.warning || 'bg-warning';
                classes.header.fg = $.toastDefaults.style.warning || 'text-white';
                break;

            case 'error':
                classes.header.bg = $.toastDefaults.style.error || 'bg-danger';
                classes.header.fg = $.toastDefaults.style.error || 'text-white';
                break;
        }

        if ($.toastDefaults.pauseDelayOnHover && opts.delay) {
            delayOrAutohide = `data-autohide="false"`;
            hideAfter = `data-hide-after="${Math.floor(Date.now() / 1000) + (opts.delay / 1000)}"`;
        }

        html = `<div id="${id}" class="toast ${globalToastStyles}" role="alert" aria-live="assertive" aria-atomic="true" ${delayOrAutohide} ${hideAfter}>`;
        html += `<div class="toast-header ${classes.header.bg} ${classes.header.fg}">`;

        if (img) {
            html += `<img src="${img.src}" class="mr-2 ${img.class || ''}" alt="${img.alt || 'Image'}">`;
        }

        html += `<strong class="mr-auto">${title}</strong>`;

        if (subtitle) {
            html += `<small class="${classes.subtitle}">${subtitle}</small>`;
        }

        if (dismissible) {
            html += `<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true" class="${classes.dismiss}">&times;</span>
                    </button>`;
        }

        html += `</div>`;

        if (content) {
            html += `<div class="toast-body">
                        ${content}
                    </div>`;
        }

        html += `</div>`;

        if (!$.toastDefaults.stackable) {
            toastContainer.find('.toast').each(function () {
                $(this).remove();
            });

            toastContainer.append(html);
            toastContainer.find('.toast:last').toast('show');
        } else {
            toastContainer.append(html);
            toastContainer.find('.toast:last').toast('show');
        }

        if ($.toastDefaults.pauseDelayOnHover) {
            setTimeout(function () {
                if (!paused) {
                    $(`#${id}`).toast('hide');
                }
            }, opts.delay);

            $('body').on('mouseover', `#${id}`, function () {
                paused = true;
            });

            $(document).on('mouseleave', '#' + id, function () {
                const current = Math.floor(Date.now() / 1000),
                    future = parseInt($(this).data('hideAfter'));

                paused = false;

                if (current >= future) {
                    $(this).toast('hide');
                }
            });
        }

        toastRunningCount++;
    }

    /**
     * Show a snack
     * @param type
     * @param title
     * @param delay
     */
    $.snack = function (type, title, delay) {
        return render({
            type,
            title,
            delay
        });
    }

    /**
     * Show a toast
     * @param opts
     */
    $.toast = function (opts) {
        return render(opts);
    }
}(jQuery));

// call a snack ##
function q_snack( options ){

	// check if the object exists ##
	if ( typeof jQuery.snack === 'undefined' ) {

		// console.log( 'No snacks available...');

		return false;

	}

	// no content, no snack ##
	if( ! options.content ){

		// console.log( 'No snack content' );

		return false;

	}

	// global config ##
	defaults = { 
		'content'		: false,
		'style' 		: 'info',
		'timeout'		: 5000,
		'position'		: 'bottom-right',
		'dismissible'	: true,
		'stackable'		: true, // stacking is ok ##
		'hover'			: true
	};

	// merge passed options ##
	jQuery.extend( defaults, options );

	/*
	@TODO - define global settings ##
	$.toastDefaults.position = options.position; // 'bottom-right';
	$.toastDefaults.dismissible = options.dismissible; // true;
	$.toastDefaults.stackable = options.stackable; // true;
	$.toastDefaults.pauseDelayOnHover = options.hover; // true;
	*/

	// snack time ##
	jQuery.snack( defaults.style, defaults.content, defaults.timeout );

}

// call a toast ##
function q_toast( options ){

	// check if the object exists ##
	if ( typeof jQuery.toast === 'undefined' ) {

		// console.log( 'No toast available...');

		return false;

	}

	// no content, no snack ##
	if( ! options.content ){

		// console.log( 'No toast content' );

		return false;

	}

	// relative time ##
	var date = q_timestamp_to_human( + new Date() );

	// global config ##
	defaults = { 
		'title'			: 'Notice',
		'subtitle'		: date +' ago', // date ##
		'content'		: false,
		'style' 		: 'info',
		'timeout'		: 5000,
		'position'		: 'bottom-right',
		'dismissible'	: true,
		'stackable'		: true, // stacking is ok ##
		'hover'			: true,
		'img'			: false /*{
							src: 'https://via.placeholder.com/20',
							alt: 'Image'
						}*/
	};

	// merge passed options ##
	jQuery.extend( defaults, options );

	// @TODO - define global settings ##
	// $.toastDefaults.position = options.position; // 'bottom-right';
	// $.toastDefaults.dismissible = options.dismissible; // true;
	// $.toastDefaults.stackable = options.stackable; // true;
	// $.toastDefaults.pauseDelayOnHover = options.hover; // true;

	// console.dir( defaults );

	// snack time ##
	jQuery.toast({
		type		: defaults.style, 
		title		: defaults.title, 
		subtitle 	: defaults.subtitle, 
		content		: defaults.content, 
		delay		: defaults.timeout,
		img			: defaults.img, 
	});

}

</script>
<?php

    }



    public static function css()
    {

?>
<style>
/**
 * @author Script47 (https://github.com/Script47/Toast)
 * @description Toast - A Bootstrap 4.2+ jQuery plugin for the toast component
 * @version 1.1.0
 **/
.toast-container {
    position: fixed;
    z-index: 1055;
    margin: 5px;
}

.top-right {
    top: 0;
    right: 0;
}

.top-left {
    top: 0;
    left: 0;
}

.top-center {
    transform: translateX(-50%);
    top: 0;
    left: 50%;
}

.bottom-right {
    right: 0;
    bottom: 0;
}

.bottom-left {
    left: 0;
    bottom: 0;
}

.bottom-center {
    transform: translateX(-50%);
    bottom: 0;
    left: 50%;
}

.toast-container > .toast {
    min-width: 330px;
    background: transparent;
    border: none;
}

.toast-container > .toast > .toast-header {
    border: none;
}

.toast-container > .toast > .toast-header strong {
    padding-right: 20px;
}

.toast-container > .toast > .toast-body {
    background: white;
}
</style>
<?php

    }


}
