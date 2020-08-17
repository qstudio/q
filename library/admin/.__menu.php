<?php

namespace q\admin;

use q\core\plugin as plugin;

// load it up ##
\q\admin\menu::run();

class menu extends \Q {

    public static function run()
    {

        // @todo - build API to allow for dynamic insertion of radio options
        // @todo - build shown options based on saved options ##
        #\add_action( 'admin_menu', [ get_class(), 'admin_menu' ] );

    }

    public static function admin_menu()
    {

        \add_options_page( 'Global UI', 'Global UI', 'manage_options', self::text_domain, function() {

            // these settings should be filtered into the global Q settings ##
            // @todo - Ray to build API from Q to allow new menu items to be added to settings page and saved to db

            // validate
            if ( 
                $_POST 
                && isset($_POST['action']) 
                && self::text_domain === $_POST['action'] ) {
                
                // sanitize ##
                $settings['active'] = intval( $_POST['settings']['active'] );

                // save ##
                if ( \update_option( self::text_domain, $settings ) ) {
                    
                    print '<div class="updated"><p><strong>Settings saved.</strong></p></div>';

                }
            }

            // get setting from db ##
            $settings = \get_option( self::text_domain );

?>
            <h1>Global UI</h1>

            <form method="post" action="">
                <table class="form-table">
                    <tr>
                        <th>
                            Include Global UI
                        </th>
                        <td>
                            Off
                            <input type="radio" name="settings[active]" value="0" checked />
                            On
                            <input type="radio" name="settings[active]" value="1" <?php \checked( $settings['active'], 1 ); ?> />
                        </td>
                    </tr>
                </table>

                <input name="nonce" type="hidden" value="<?php echo \esc_attr( \wp_create_nonce( self::text_domain ) ); ?>" />
                <input name="action" type="hidden" value="<?php echo \esc_attr( self::text_domain ); ?>" />
                <input type="submit" class="button-primary" value="Save" />
            </form>
<?php

        });
    }
}


// Flush menu cache if menus are changed
if( isset($_POST['action']) && isset($pagenow) && $pagenow === 'nav-menus.php' ){
    array_map( 'unlink', glob(__DIR__ . '/cache/'.'*.html.cache') );
}