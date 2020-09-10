/**
 * @author Script47 (https://github.com/Script47/Toast)
 * @description Toast - A Bootstrap 4.2+ jQuery plugin for the toast component
 * @version 1.1.0
 **/
"use strict";

(function ($) {
  var TOAST_CONTAINER_HTML = "<div id=\"toast-container\" class=\"toast-container\" aria-live=\"polite\" aria-atomic=\"true\"></div>";
  $.toastDefaults = {
    position: 'middle-right',
    dismissible: true,
    stackable: true,
    pauseDelayOnHover: true,
    style: {
      toast: '',
      dark: '',
      primary: '',
      info: '',
      success: '',
      warning: '',
      error: ''
    }
  };
  $('body').on('hidden.bs.toast', '.toast', function () {
    $(this).remove();
  });
  var toastRunningCount = 1;

  function render(opts) {
    /** No container, create our own **/
    if (!$('#toast-container').length) {
      var position = ['top-right', 'top-left', 'top-center', 'middle-right', 'bottom-right', 'bottom-left', 'bottom-center'].includes($.toastDefaults.position) ? $.toastDefaults.position : 'middle-right';
      $('body').prepend(TOAST_CONTAINER_HTML);
      $('#toast-container').addClass(position);
    }

    var toastContainer = $('#toast-container');
    var html = '';
    var classes = {
      header: {
        fg: '',
        bg: ''
      },
      subtitle: 'text-white',
      dismiss: 'text-white'
    };
    var id = "toast-".concat(toastRunningCount);
    var type = opts.type;
    var title = opts.title;
    var subtitle = opts.subtitle;
    var content = opts.content;
    var img = opts.img;
    var delayOrAutohide = opts.delay ? "data-delay=\"".concat(opts.delay, "\"") : "data-autohide=\"false\"";
    var hideAfter = "";
    var dismissible = $.toastDefaults.dismissible;
    var globalToastStyles = $.toastDefaults.style.toast;
    var paused = false;

    if (typeof opts.dismissible !== 'undefined') {
      dismissible = opts.dismissible;
    }

    switch (type) {
      case 'dark':
        classes.header.bg = $.toastDefaults.style.dark || 'bg-dark';
        classes.header.fg = $.toastDefaults.style.dark || 'text-white';
        break;

      case 'primary':
        classes.header.bg = $.toastDefaults.style.primary || 'bg-primary';
        classes.header.fg = $.toastDefaults.style.primary || 'text-white';
        break;

      case 'info':
        classes.header.bg = $.toastDefaults.style.info || 'bg-info';
        classes.header.fg = $.toastDefaults.style.info || 'text-white';
        break;

      case 'success':
        classes.header.bg = $.toastDefaults.style.success || 'bg-success';
        classes.header.fg = $.toastDefaults.style.success || 'text-white';
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
      delayOrAutohide = "data-autohide=\"false\"";
      hideAfter = "data-hide-after=\"".concat(Math.floor(Date.now() / 1000) + opts.delay / 1000, "\"");
    }

    html = "<div id=\"".concat(id, "\" class=\"toast ").concat(globalToastStyles, "\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\" ").concat(delayOrAutohide, " ").concat(hideAfter, ">");
    html += "<div class=\"p-4 toast-header ".concat(classes.header.bg, " ").concat(classes.header.fg, "\">");

    if (img) {
      html += "<img src=\"".concat(img.src, "\" class=\"mr-2 ").concat(img["class"] || '', "\" alt=\"").concat(img.alt || 'Image', "\">");
    }

    html += "<strong class=\"mr-auto\">".concat(title, "</strong>");

    if (subtitle) {
      html += "<small class=\"".concat(classes.subtitle, "\">").concat(subtitle, "</small>");
    }

    if (dismissible) {
      html += "<button type=\"button\" class=\"ml-2 mb-1 close\" data-dismiss=\"toast\" aria-label=\"Close\">\n                        <span aria-hidden=\"true\" class=\"".concat(classes.dismiss, "\">&times;</span>\n                    </button>");
    }

    html += "</div>";

    if (content) {
      html += "<div class=\"toast-body\">\n                        ".concat(content, "\n                    </div>");
    }

    html += "</div>";

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
          $("#".concat(id)).toast('hide');
        }
      }, opts.delay);
      $('body').on('mouseover', "#".concat(id), function () {
        paused = true;
      });
      $(document).on('mouseleave', '#' + id, function () {
        var current = Math.floor(Date.now() / 1000),
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
      type: type,
      title: title,
      delay: delay
    });
  };
  /**
   * Show a toast
   * @param opts
   */


  $.toast = function (opts) {
    return render(opts);
  };
})(jQuery);

// call a snack ##
function q_snack( options ){

	// check if the object exists ##
	if ( typeof jQuery.snack === 'undefined' ) {

		console.log( 'No snacks available...');

		return false;

	}

	// no content, no snack ##
	if( ! options.content ){

		console.log( 'No snack content' );

		return false;

	}

	// global config ##
	defaults = { 
		'content'		: false,
		'style' 		: 'primary',
		'timeout'		: 5000,
		'position'		: 'bottom-right',
		'dismissible'	: true,
		'stackable'		: true, // stacking is ok ##
		'hover'			: true
	};

	// merge passed options ##
	jQuery.extend( defaults, options );

	// snack time ##
	jQuery.snack( defaults.style, defaults.content, defaults.timeout );

}

// call a toast ##
function q_toast( options ){

	// check if the object exists ##
	if ( typeof jQuery.toast === 'undefined' ) {

		console.log( 'No toast available...');

		return false;

	}

	// no content, no snack ##
	if( ! options.content ){

		console.log( 'No toast content' );

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
