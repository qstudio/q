<?php

namespace q\module;

use q\core;
use q\core\helper as h;
// use q\core\config as config;
use q\asset;

// load it up ##
\q\module\bs_form::__run();

class bs_form extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in extension select API ##
		\add_filter( 'acf/load_field/name=q_option_extension', [ get_class(), 'filter_acf_extension' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('bs_form') );
		if ( 
			! isset( core\option::get('extension')->bs_form )
			|| true !== core\option::get('extension')->bs_form 
		){

			// h::log( 'd:>Toast is not enabled.' );

			return false;

		}
		

        // add js to footer ##
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
		$field['choices']['bs_form'] = 'Bootstrap Form';

		// make it selected ##
		$field['default_value'][0] = 'bs_form';
		
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
// BS Form validation
(function() {
  'use strict';
  window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false);
})();
</script>
<?php

    }

}
