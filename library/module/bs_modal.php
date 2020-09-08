<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// load it up ##
\q\module\bs_modal::__run();

class bs_modal extends \Q {
    
    static $args = array();

    public static function __run()
    {

		// add extra options in module select API ##
		\q\module::filter([
			'module'	=> str_replace( __NAMESPACE__.'\\', '', static::class ),
			'name'		=> 'Bootstrap ~ Modal',
			'selected'	=> true,
		]);

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
     * Add HTML to footer on all templates
	 * 
	 * @since 4.x.x
     */
    public static function wp_footer()
    {

/*
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-modal-title="test" data-modal-body="body" data-modal-target="#q_modal">
	Launch Modal
</button>
*/

// @todo - MAKE HTML filterable via Willow ##
		
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

}
