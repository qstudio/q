<?php

namespace q\core;

use q\core\core as core;
use q\core\helper as helper;
use q\core\wordpress as wordpress;

// load it up ##
\q\core\options::run();

class options extends \Q {

    public static $radio_options;
    protected static $q_options;

    /**
    * Class Constructor
    * 
    * @since       1.0
    * @return      void
    */
    public static function run()
    {
        
        if ( ! \is_admin() ) {

            return false;

        }

        // extend the radio_options property ##
        self::$radio_options = array(
            'yes' => array(
                'value' => '1',
                'label' => __( 'Yes', 'q-textdomain' )
            ),
            'no' => array(
                'value' => '0',
                'label' => __( 'No', 'q-textdomain' )
            ),
        );
        
        
        if ( is_admin() ) { // make sure this is only loaded up in the admin ##
            
            // options nags, panels and notices ##
            \add_action( 'admin_notices', array ( get_class(), 'options_notices' ) );
            \add_action( 'admin_init', array ( get_class(), 'options_notices_ignore' ) );
            
            // register settings API ##
            \add_action( 'admin_init', array ( get_class(), 'register_setting' ) );
            
            // plugin options page ##
            \add_action( 'admin_menu', array ( get_class(), 'add_submenu_page' ) );
            
            // filter saved data ##
            #\add_action( 'update_optionq_options', 'drop_support', 10, 2 );

        }
        
    }


    public static function drop_support( $old, $new ) {

        helper::log(  'Saving q_options from admin..' );

        return $new;

    }
        
    
    /**
    * Define Default Q Options
    * 
    * @since       1.0
    * @return      void
    */
    protected static function define()
    {
        
        // build options array ##
        self::$q_options = array ( 

            // plugin settings ##
            "plugin_css"                => true, // add plugin css styling ##
            "plugin_js"                 => true, // add plugin javascript ##

            // theme settings ##
            "theme_css"                 => true, // add theme css styling to child theme ##
            "theme_js"                  => true, // add theme javascript files to child theme ##

            // google codes ##
            "google_analytics"          => '', // tracking code ##
            "google_webmasters"         => '', // verification code ##

        );
        
    }
    
    
    /**
    * Expose options to other functions 
    * 
    * @since       1.0
    * @return      Object
    */
    public static function get() 
    {
        
        // check for options ##
        $q_options = \get_site_option( 'q_options' );

        // helper::log( $q_options );
        
        // no options loaded from wp_options ##
        if ( ! $q_options ) {

            // define default options ##
            self::define();
            
            // grab those options ##
            $q_options = self::$q_options;

            // still no options !! ##
            if ( ! is_array( $q_options ) ) { 

                // kill WP ##
                wp_die( 
                    _e( 
                        "<h2>Error!</h2><p>There was an error loading the required Q Options.</p>" 
                        ,'q-textdomain'
                    ) ); 

            } else { 

                // add wp_options reference ##
                wordpress::add_update_option( 'q_options', $q_options, '', 'yes' );

            }

        }
        
        // helper::log( $q_options );

        // kick it back ##
        #return \q_array_to_object( $q_options );
        return $q_options;

    }
    
    
    /**
    * Delete Q Options - could be used to clear old settings
    */
    public static function delete( $option = null )
    {

        $q_options = \get_site_option( 'q_options' );
        
        if ( 
            ! is_null( $option ) 
            && is_array( $q_options )
            && isset ( $q_options[$option] )
        ) {

            // remove key ##
            unset( $q_options[$option] );

            // update class property ##
            self::$q_options = $q_options;

        } else {

            // remove all options
            \delete_site_option( 'q_options' ); // delete option ##
        
            // update class property ##
            self::$q_options = false;

        }

        // update stored value ##
        wordpress::add_update_option( 'q_options', $q_options, '', 'yes' ); 

        // kick it back ##
        return self::$q_options;

    }


    /**
    * Update Q Options
    */
    public static function update( $item = null )
    {
        
        if ( is_null( $item ) ) {
            
            self::log( 'nothing passed' );

            return false;

        }

        $q_options = \get_site_option( 'q_options' );

        // add item to object ##
        #$q_options[$item] = true;
        $q_options[$item] = true;

        // update class property ##
        self::$q_options = $q_options;

        // update stored value ##
        wordpress::add_update_option( 'q_options', $q_options, '', 'yes' ); 
        
    }
    

    
    /**
    * Init plugin options to white list our options
    */
    public static function register_setting()
    {
        
        \register_setting( 'q_options', 'q_options', array ( get_class(), 'validate' ) );
        
    }
    
    
    /**
    * Load up the menu page
    */
    public static function add_submenu_page() 
    {
        
        \add_submenu_page( 
            'themes.php' 
            ,__( 'Q', 'q-textdomain' )
            ,__( 'Q', 'q-textdomain' )
            , 'manage_options'
            , 'q'
            , array ( get_class(), 'options_page')
        ); 
        
    }


    /**
        * Create the options page
        */
    public static function options_page() 
    {
        
        // get FRESH Q Plugin data ##
        $plugin_data = wordpress::plugin_data( true );

        // get Q options ## -- @todo, move to functions::array_to_object
        $options = core::array_to_object( self::get() );

?>
    <style>
        .update-nag { display: none; }
        .small { font-size: 70%; }
        .form-table th { font-weight: 100; }
        input[type="radio"] { margin: 0 10px; }
    </style>
<?php

    // check for update in request headers ##
    if ( isset( $_GET['settings-updated'] ) ) { 
            
?>
    <script type="text/javascript">
    jQuery(document).ready(function() {

        jQuery("div.updated p strong").html('<?php _e( 'Settings Saved.', 'q-textdomain' ); ?>'); 

    });
    </script>
<?php

    }

?>
    <div class="wrap">
<?php

        // page header ##
        $version = ' <span class="small">( version '.$plugin_data->version.' )</span>';
        echo "<h2>".__( 'Q Options', 'q-textdomain' ).$version."</h2>"; 
        echo "<p>".__( 'If the option you are looking for is not listed on this page, it has probably been added via a plugin.', 'q-textdomain' ) . "</p>"; 

?>
        <form method="post" action="options.php">
<?php                 

        // add nonce field ##
        \settings_fields( 'q_options' );

        // plugin settings ##
    
?>
            <h3>Plugin Settings:</h3>
            <table class="form-table">
                <tr valign="top"><th scope="row"><?php _e( 'Load Plugin CSS', 'q-textdomain' ); ?></th><td><fieldset>
                    <legend class="screen-reader-text"><span><?php _e( 'Load Plugin CSS', 'q-textdomain' ); ?></span></legend>
    <?php

                    // no checked option ##
                    if ( !isset( $checked ) ) {  $checked = ''; }

                    // loop all options ##
                    foreach ( self::$radio_options as $option ) {

                        // get value from options ##
                        $radio_setting = $options->plugin_css;
                        $bool_option_value = (bool)$option['value']; // cast to boolean ##
                        if ( $radio_setting === $bool_option_value ) {
                            $checked = "checked=\"checked\"";
                        } else {
                            $checked = '';
                        }

    ?>
                    <label class="description" style="margin-right: 10px;">
                        <input type="radio" name="q_options[plugin_css]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                    </label>
    <?php
                    } // foreach radio ##
    ?>
                </fieldset></td></tr>

                <tr valign="top"><th scope="row"><?php _e( 'Load Plugin JavaScript', 'q-textdomain' ); ?></th><td><fieldset>
                    <legend class="screen-reader-text"><span><?php _e( 'Load Plugin JavaScript', 'q-textdomain' ); ?></span></legend>
    <?php

                    // no checked option ##
                    if ( !isset( $checked ) ) {  $checked = ''; }

                    // loop all options ##
                    foreach ( self::$radio_options as $option ) {

                        // get value from options ##
                        $radio_setting = $options->plugin_js;
                        $bool_option_value = (bool)$option['value']; // convert to boolean ##
                        if ( $radio_setting === $bool_option_value ) {
                            $checked = "checked=\"checked\"";
                        } else {
                            $checked = '';
                        }

    ?>
                    <label class="description" style="margin-right: 10px;">
                        <input type="radio" name="q_options[plugin_js]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                    </label>
    <?php
                        }
    ?>
                </fieldset></td></tr>
                
            </table>
            
            <h3>Theme Settings:</h3>
            <table class="form-table">
                <tr valign="top"><th scope="row"><?php _e( 'Load Theme CSS', 'q-textdomain' ); ?></th><td><fieldset>
                    <legend class="screen-reader-text"><span><?php _e( 'Load Theme CSS', 'q-textdomain' ); ?></span></legend>
    <?php

                    // no checked option ##
                    if ( !isset( $checked ) ) {  $checked = ''; }

                    // loop all options ##
                    foreach ( self::$radio_options as $option ) {

                        // get value from options ##
                        $radio_setting = $options->theme_css;
                        $bool_option_value = (bool)$option['value']; // convert to boolean ##
                        if ( $radio_setting === $bool_option_value ) {
                            $checked = "checked=\"checked\"";
                        } else {
                            $checked = '';
                        }

    ?>
                    <label class="description" style="margin-right: 10px;">
                        <input type="radio" name="q_options[theme_css]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                    </label>
    <?php
                        }
    ?>
                </fieldset></td></tr>

                <tr valign="top"><th scope="row"><?php _e( 'Load Theme JavaScript', 'q-textdomain' ); ?></th><td><fieldset>
                    <legend class="screen-reader-text"><span><?php _e( 'Load Theme JavaScript', 'q-textdomain' ); ?></span></legend>
    <?php

                    // no checked option ##
                    if ( !isset( $checked ) ) {  $checked = ''; }

                    // loop all options ##
                    foreach ( self::$radio_options as $option ) {

                        // get value from options ##
                        $radio_setting = $options->theme_js;
                        $bool_option_value = (bool)$option['value']; // convert to boolean ##
                        if ( $radio_setting === $bool_option_value ) {
                            $checked = "checked=\"checked\"";
                        } else {
                            $checked = '';
                        }

    ?>
                    <label class="description" style="margin-right: 10px;">
                        <input type="radio" name="q_options[theme_js]" value="<?php esc_attr_e( $option['value'] ); ?>" <?php echo $checked; ?> /> <?php echo $option['label']; ?>
                    </label>
    <?php
                        }
    ?>
                </fieldset></td></tr>
            </table>
            
            <h3>Google Settings:</h3>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="q_options[google_analytics]"><?php _e( 'Google Analytics', 'q-textdomain' ); ?></label></th>
                    <td>
                        <input id="q_options[google_analytics]" class="regular-text" type="text" name="q_options[google_analytics]" value="<?php esc_attr_e( $options->google_analytics ); ?>" />
                        <p class="description" ><?php _e( 'Enter Your Google Analytics UA', 'q-textdomain' ); ?> - <a href="http://www.google.co.uk/analytics/" target="_blank">Sign Up</a></p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><label for="q_options[google_webmasters]"><?php _e( 'Google Webmasters', 'q-textdomain' ); ?></label></th>
                    <td>
                        <input id="q_options[google_webmasters]" class="regular-text" type="text" name="q_options[google_webmasters]" value="<?php esc_attr_e( $options->google_webmasters ); ?>" />
                        <p class="description" ><?php _e( 'Enter Your Google Webmasters Verify Code', 'q-textdomain' ); ?> - <a href="https://www.google.com/webmasters/" target="_blank">Sign Up</a></p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e( 'Save Options', 'q-textdomain' ); ?>" />
            </p>
            
        </form>
    </div>
<?php

    } // theme_options_do_page ##

    



    /**
     * Sanitize and validate input. Accepts an array, returns a sanitized array.
     */
    public static function validate( $input ) 
    {

        // helper::log( 'validation:' );
        // helper::log( $input );

        // remove all options added via q_add_theme_support - these are recompilled on load ##
        \delete_site_option( 'q_options' ); // delete option ##

        // force default value if radio empty ##
        $input['plugin_css']         = ( $input['plugin_css'] == 1 ? true : false );
        $input['plugin_js']          = ( $input['plugin_js'] == 1 ? true : false );

        $input['theme_css']            = ( $input['theme_css'] == 1 ? true : false );
        $input['theme_js']             = ( $input['theme_js'] == 1 ? true : false );

        $input['google_analytics']      = \wp_filter_nohtml_kses( $input['google_analytics'] );
        $input['google_webmasters']     = \wp_filter_nohtml_kses( $input['google_webmasters'] );

        return $input;

    }
    
    
    

    
    /**
     * Display a notice that can be dismissed 
     * 
     * @since       1.0
     */
    public static function options_notices() 
    {

        // grab options ##
        $q_options = self::get();

        // get options ##
        $q_key = isset( $q_options->q_key ) ? $q_options->q_key : false ;

        // user ##
        #global $current_user;
        #$user_id = $current_user->ID;

        // plugin ##
        $url_q_options = \admin_url( 'options-general.php?page=q', 'http' );
        $button_q_options = '<input type="button" style="margin-left: 10px;" class="button" value="'.__("Q Options", "q").'" onclick="document.location.href=\''.$url_q_options.'\'">';

    }
    
    
    /**
     * Hide Options Notice
     * 
     * @since       1.0
     * @return      void
     */
    public static function options_notices_ignore() 
    {
        
        global $current_user;
        $user_id = $current_user->ID;
        /* If user clicks to ignore the notice, add that to their user meta */
        if ( isset($_GET['q_nag_ignore']) && '0' == $_GET['q_nag_ignore'] ) {
            \add_user_meta ($user_id, 'q_ignore_notice', 'true', true);
        }
        
    }


    public static function add_theme_support( $support )
    {

        // if ( ! class_exists( '\q\core\options' ) ) {

        //     helper::log( 'options class missing, install q_ui plugin and activiate.' );

        //     return false;

        // } 
        
        // grab the options ##
        $q_options = self::get();

        // Q_Control::log( $q_options );

        if ( $support && is_array( $q_options ) ) { // check to see if $support passed ##

           if ( is_array( $support ) ) {

               foreach ( $support as $add ) {

                   if ( $add ) {

                        // Q_Control::log( 'Add single item from array: '.$add );
                        self::update( $add );

                   }

               }

          } else { // single variable ##

              #Q_Control::log( 'Add single item: '.$support );

              self::update( $support );

          }

       }

       #Q_Control::log( options::get() );

    }
    
}