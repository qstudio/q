<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_modal::__run();

class bs_modal extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// h::log( core\option::get('modal') );
		if ( 
			! isset( core\option::get('module')->bs_modal )
			|| true !== core\option::get('module')->bs_modal 
		){

			// h::log( 'd:>Modal is not enabled.' );

			return false;

		}

        // add html to footer ##
        \add_action( 'wp_footer', [ get_class(), 'wp_footer' ], 3 );

    }



	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['bs_modal'] = 'Bootstrap Modal';

		// make it selected ##
		$field['default_value'][0] = 'bs_modal';

         return $field;

	}



    
    /**
     * Deal nicely with JS
     */
    public static function wp_footer()
    {

        \q\asset\javascript::ob_get([
            'view'      => get_class(), 
            'method'    => 'javascript',
            // 'priority'  => 3,
            // 'handle'    => 'BS Modal'
		]);

/*
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-modal-title="test" data-modal-body="body" data-modal-target="#q_modal">
	Launch Modal
</button>
*/

// @todo - MAKE HTML filterable ##
		
?>
<!-- Modal -->
<div class="modal fade" id="q_modal" tabindex="-1" role="dialog" aria-labelledby="q_modal_title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content"><!-- TODO -->
      <div class="modal-header">
        <h5 class="modal-title" id="q_modal_long_title"></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php

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
	
	// @TODO - re-add hash controls - perhaps not back and forwards, but loading modal from fragement - perhaps like tabs.. 

?>
<script>

// open modal with dynamic data ##
jQuery(document).on("click","[data-modal-target]", function (e) {

	// stop ##
	e.preventDefault();

	// get target ##
	var target = jQuery(this).attr('data-modal-target');
	// console.log('modal target: ' + target);

	var title = jQuery(this).attr('data-modal-title');
	var body = jQuery(this).attr('data-modal-body');
	var size = jQuery(this).attr('data-modal-size') ? jQuery(this).attr('data-modal-size') : 'modal-normal' ;

	// sanity ##
	if( ! title || ! body || ! size ){
		console.log( 'Error in passed params' );
		// jQuery(target).modal('dispose');
		// return false;
	}

	// console.log( 'size: '+size );

	// add data ##
	jQuery(target).find('.modal-title').html(title);
	jQuery(target).find('.modal-body').html(body);
	jQuery(target).find('.modal-dialog').addClass(size);

	// open modal ##
	jQuery(target).modal("show");

});
</script>
<?php

    }

    
}
