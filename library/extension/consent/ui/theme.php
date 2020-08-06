<?php

// namespace ##
namespace q\extension\consent;

// Q ##
use q\core;
use q\core\helper as h;
use q\extension;

// load it up ##
\q\extension\consent\theme::__run();

class theme extends extension\consent {

    /**
     * Instatiate Class
     *
     * @since       0.2
     * @return      void
     */
    public static function __run()
    {

        // render consent bar ##
        \add_action( 'q_action_body_open', [ get_class(), 'render' ], 4 );

        // styles and scripts ##
        \add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_scripts' ], 99 );

    }



    public static function is_active()
    {

        // helper::log( 'Checking if Consent is active' );

        if (
			true == core\option::get( 'extension' )->consent // wow.. short way around ##
        ) {

            // h::log( 'd:>Consent UI active' );

            // seems good ##
            return true;
        
        }

        // helper::log( 'Consent UI not active' );

        // inactive ##
        return false;    

    }



    /**
     * WP Enqueue Scripts - on the front-end of the site
     *
     * @since       0.1
     * @return      void
     */
    public static function wp_enqueue_scripts()
    {

        // check if the feature has been activated in the admin ##
        if (
            ! self::is_active()
        ) {

            // kick out ##
            return false;

        }

        // Register the script ##
        \wp_register_script( 'q-consent-js', h::get( 'extension/consent/ui/asset/js/consent.js', 'return' ), array( 'jquery' ), self::version, true );

        // Now we can localize the script with our data.
        $translation_array = array(
                'ajax_nonce'    => \wp_create_nonce( 'ajax_consent' )
            ,   'ajax_url'      => \get_home_url( '', 'wp-admin/admin-ajax.php' )
            ,   'saved'         => __( "Saved!", 'q-consent' )
            ,   'disabled'      => __( "Functional Cookies cannot be disabled", 'q-consent' )
        );
        \wp_localize_script( 'q-consent-js', 'q_consent', $translation_array );

        // enqueue the script ##
        \wp_enqueue_script( 'q-consent-js' );

        // wp_register_style( 'q-consent-css', h::get( 'extension/consent/library/ui/asset/scss/index.css', 'return' ), '', self::version );
        // wp_enqueue_style( 'q-consent-css' );

        return false;
        
    }



    /**
     * Render Consent UI
     *
     * @since       0.1.0
     * @return      HTML
     */
    public static function render()
    {
        
        // check if the feature has been activated in the admin ##
        if (
            ! self::is_active()
        ) {

            // kick out ##
            return false;

        }

        // check if the user has already given active consent - if not, we continue to push them to take an action ##
        if ( cookie::consent() ) {

            // h::log( 'd:>Consent already given, so do not display bar' );

            return false;

        }

        // check if the user is in the EU contient, for GDPR compliance
        if ( ! geotarget::is_eu() ) {

            // h::log( 'd:>User is outside the EU, so we do not need to show the bar' );

            // return false;

		}
		
		// pass to willow render template method ##
		\q\willow\render\template::partial([
			'context' 	=> 'extension', 
			'task' 		=> 'consent',
			// array of data to include in template ##
			'data'		=> [
				'settings' 			=> \esc_html( self::settings() ),
				'button_class'		=> ' q-consent-set',
				'privacy_permalink'	=> \get_permalink().'privacy/',
				'data'				=> 'data-q-consent-marketing="1" data-q-consent-analytics="1"'
			],
			'template'	=> 'bar', // markup->property ##
			// 'return'	=> 'echo' // also defined in config ## 
		]);

    }




    /**
     * Render Consent Settings in Modal
     *
     * @todo    tie into core method to save cookie
     * @since   0.1.0
     */
    public static function settings()
    {

		// pass to willow render template method ##
		return \q\willow\render\template::partial([
			'context' 	=> 'extension', 
			'task' 		=> 'consent',
			// array of data to include in template ##
			'data' => [
				'option_functional'	=> 
								self::option([
									'field'     => 'functional',
									'value'     => 1, // no opt-out ##
									'disabled'  => true
								]),
				'option_marketing'	=> 
								self::option([
									'field'     => 'marketing',
									'value'     => self::$cookie['marketing'],
									'disabled'  => false
								]),
				'option_analytics'	=> 
								self::option([
									'field'     => 'analytics',
									'value'     => self::$cookie['analytics'],
									'disabled'  => false
								]),
				'buttons'			=> '<button type="button" class="btn btn-dark reset q-consent-reset">RESET</button>
										<a
										href="#"
										data-tab-trigger="settings"
										class="btn btn-primary modal-trigger accept q-consent-set"
										data-q-consent-marketing="'.self::$cookie['marketing'].'"
										data-q-consent-analytics="'.self::$cookie['analytics'].'"
									>SAVE</a>'
			],
			'template'			=> 'settings', // markup->property ##
			'return'			=> 'return' // also defined in config ## 
		]);

    }


    public static function option( $args = null )
    {

        // sanity check ##
        if ( is_null( $args ) ) {

            h::log( 'e:>Error in passed args' );

            return false;

        }

		ob_start();

?>
		<div class="checkbox q-consent-option <?php echo $args['disabled'] ? 'disabled' : '' ?>" data-q-consent-field="<?php echo $args["field"]; ?>">
			<label>
				<input data-toggle="toggle" class="q-toggle" type="checkbox" value="1" <?php echo $args['disabled'] ? 'disabled' : '' ?> <?php echo $args['value'] == '1' ? 'checked' : '' ?>>
			</label>
		</div>
		
<?php

		return ob_get_clean();

    }
}
