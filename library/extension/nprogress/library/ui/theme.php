<?php

namespace q\nprogress\ui;

use q\nprogress\core\helper as h;
use q\nprogress\core\core as core;

// load it up ##
\q\nprogress\ui\theme::run();

class theme extends \q_nprogress {

  public static function run()
  {

		// front-end options ##
		if (
			! \is_admin() // no need for such fanciness in the admin ##
			&& ! core::is_mobile() // and no need to use on mobile devices - as most have their own progress bar ##
		) {

			// add scripts ##
			\add_action( 'wp_enqueue_scripts', array( get_class(), 'wp_enqueue_scripts' ), 1 );

			// add call to JS to wp_footer() action hook ##
			\add_action( 'wp_footer', array( get_class(), 'wp_footer' ), 999 );

		}

  }



	/**
	 * Enqueue Plugin Scripts & Styles
	 *
	 * @since       0.5
	 */
	public static function wp_enqueue_scripts()
	{

		// add CSS ##
		\wp_register_style( 'q-nprogress', self::get_plugin_url( 'library/ui/css/q.nprogress.css' ), '', self::version );
		\wp_enqueue_style( 'q-nprogress' );

		// add JS ##
		\wp_enqueue_script( 'q-nprogresss', self::get_plugin_url( 'library/ui/javascript/q.nprogress.js' ), array( "jquery" ), self::version, false );

	}


	public static function wp_footer()
	{

?>
	<script>
		// configure ##
		NProgress.configure({ showSpinner: false });

		// Show the progress bar
		NProgress.start();

		// Increase randomly
		var interval = setInterval(function() { NProgress.inc(); }, 1000);

		// Trigger finish when page fully loaded
		jQuery(window).load(function () {
			clearInterval(interval);
			NProgress.done();
		});

		// Trigger bar when exiting the page
		window.onbeforeunload = function() {
			//console.log("triggered");
			NProgress.start();
		};

		// target progress bar on all form submits ##
		jQuery( document ).on('submit', function(){
			//console.log("progress triggered");
			NProgress.start();
		});

		// stop progress on GF confirmation about submit ##
		jQuery( document ).bind('gform_confirmation_loaded', function( event, form_id ){
			//console.log("progress done");
			NProgress.done();
		});

		// stop progress on GF AJAX loaded, with validation errors ##
		jQuery( document ).bind( 'gform_post_render', function(){
			if ( typeof NProgress !== 'undefined' ) {
				//console.log("progress done");
				NProgress.done();
			}
		});

		// stop progress bar on all AJAX completions ##
		jQuery( document ).ajaxComplete( function() {
			//console.log("progress done");
			NProgress.done();
		});
	</script>
<?php

	}

}