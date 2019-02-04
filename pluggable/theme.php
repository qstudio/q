<?php

/**
 * Q Theme Functions
 *
 * @since       1.0
 */
use q\ui\core\options as options;

if ( ! function_exists( 'q_locate_template' ) )
{    
    /**
    * check if a file exists with environmental fallback
    * first check the child, then the parent, then the framework
    * 
    * @param    $include string Include file with path ( from library/  ) to include. i.e. - templates/loop-nothing.php
    * @param    $echo boolean echo or return the results
     * 
    * @since 0.1
    * @uses q_get_option()
    */
    function q_locate_template( $include, $echo = true, $require = false, $return = false, $theme_only = false )
    {

        if ( $include ) { // carry on ##
            
            $q_locate_template = ''; // nada ##
            $path = ''; // local path ##
            
            if ( is_child_theme() && file_exists( q_get_option("path_child").'library/'.$include ) ) { // load child over parent - if this is a child theme ##

                $path = 'library/'; // path ##
                $q_locate_template = q_get_option("uri_child"); // template path ##
                if ( $require === true || $return === true ) { $q_locate_template = q_get_option("path_child"); } // require - to use file path ##

            } elseif ( file_exists( q_get_option("path_parent").'library/'.$include ) ) { // load parent over framework ##   
                
                $path = 'library/'; // path ##
                $q_locate_template = q_get_option("uri_parent"); // template path ##
                if ( $require === true || $return === true ) { $q_locate_template = q_get_option("path_parent"); } // require - to use file path ##

            } elseif ( file_exists( Q_DIR.'/'.$include ) && $theme_only === false ) { // load from Q Plugin ( if not set to only loads from theme locations ) ##   
                
                $path = true; // path ##
                $q_locate_template = Q_URLPATH; // template path ##
                if ( $require === true || $return === true ) { $q_locate_template = Q_DIR.'/'; } // require - to use file path ##
                
                #wp_die($q_locate_template.$path.$include);
                
            } 

            if ( $q_locate_template && $path ) { // continue ##
                
                if ( $path === true ) { $path = ''; } // real hack ##
                
                // compile with path ##
                $q_locate_template = $q_locate_template.$path.$include;

                // apply filters ##
                $q_locate_template = apply_filters( 'q_locate_template', $q_locate_template );

                // test #
                #echo '$q_locate_template: '.$q_locate_template.'<br />';

                // echo or return string ##
                if ( $echo === false ) { 

                    if ( $require === true ) { // return included file ##

                        return require_once( $q_locate_template );

                    } else { // return string value ##

                        return $q_locate_template;

                    }

                } else { // echo string value ##

                    echo $q_locate_template;

                }

            }

        }

    }
}



if ( ! function_exists( 'q_dynamic_sidebar' ) )
{
/**
* Widget Function
*
* @param string $widget            - ID of widget to include
* @param string $widget_name       - name of widget to include
* @param string $element           - DOM selector to wrap return
*
* @return string ( HTML Error | WP Widget )
* @since 0.1
*/
    function q_dynamic_sidebar( $widget, $widget_name, $element = 'sider' ){

        #global $q_options;

        // instatiate Q_Options Class
        #$q_options_class = new Q_Options();

        // grab the options ##
        $q_options = options::get();

        #Q_Control::log( $q_options );

        if ( $widget ) { // widget ID passed ##

           // get all widgets ##
           $widgets = wp_get_sidebars_widgets();

           if ( is_active_sidebar( $widget ) ) {

?>
                   <ul class="<?php echo $element; ?>">
                       <?php

                       dynamic_sidebar( $widget );

                       ?>
                   </ul>
<?php

           } else {

               // issue error to admin if requested widget does not exist or is empty ##
               if ( current_user_can('administrator') ) { // user is admin ##

                    $message = ( array_key_exists( $widget, $widgets ) ) ? __( "empty", "q-framework" ) : __( "error", "q-framework" ) ;

                    if ( ! current_theme_supports('widgets') ) { 
                        $message == __( "not supported!", "q-framework" ); 
                    }
                    
                    $name = $widget_name ? $widget_name : $widget;

                    #Q::log( "Widget: ".$message.' '.$name );

               }
           }
       }
    }
}


if ( ! function_exists( 'q_plugin_warning' ) )
{
/**
 * Return a warning about a non-installed or inactive plugin
 *
 * @since       1.0
 */
    function q_plugin_warning( $title, $plugin, $path )
    {

        #global $q_options;

        // instatiate Q_Options Class
        #$q_options_class = new Q_Options();

        // grab the options ##
        $q_options = options::get();

        #Q_Control::log( $q_options );

        if ( $plugin && current_user_can('administrator') && $q_options->framework_warning === TRUE ) {

           // check if plugin is installed ##
           $wppath = ABSPATH . 'wp-content/plugins/';
           #echo $wppath.$path;
           if ( file_exists( $wppath.$path ) ) { // plugin activation search ##

               $warning = __("inactive", "q-framework");
               $action = '<a href="'.admin_url().'plugins.php?plugin_status=inactive&s='.$title.'" title="Activate '.$plugin.'">'.__("fix it", "q-framework").'!</a>';

           } else { // plugin install search ##

               $warning = __("missing", "q-framework");
               $action = '<a href="'.admin_url().'plugin-install.php?tab=search&s='.$plugin.'" title="Install '.$plugin.'">'.__("fix it", "q-framework").'!</a>';

           }

           // compile ##
           $return = '<div class="error"><strong>'.__( "Plugin", "q-framework").' '.$warning.':</strong> '.$title.' - '.$action.'</div>';

           // apply filters ##
           $return = apply_filters( 'q_plugin_warning', $return );

           // return string ##
           echo $return;

       }
    }
}


if ( ! function_exists( 'q_plugin_is_active' ) )
{
/**
 * Check if a plugin is active
 */
    function q_plugin_is_active( $plugin ) {
       return in_array( $plugin, (array) get_option( 'active_plugins', array() ) );
    }
}


if ( ! function_exists( 'q_cache_wp_nav_menu' ) )
{
/**
 * Cache Menus
 *
 * @link        http://forrst.com/posts/Cache_WordPress_Menus_replace_wp_nav_menu-dAZ
 */
function q_cache_wp_nav_menu($args = array()){

   if(isset($args['menu'])){

       $menu_file = __DIR__ . '/cache/'.$args['menu'].'.html.cache';

       if(!file_exists($menu_file)){
           $_args = array(
               'menu_id' => '',
               'menu' => '',
               'theme_location' => '', // menu based on theme location ##
               'fallback_cb' => 'wp_page_menu',
               'container_class' => '', // container class ##
               'menu_class' => '',
               'container' => 'false',
               'echo' => false,
           );

           foreach($args as $arg => $value) {
               $_args[$arg] = $value;
           }

           $menu = "<!-- [cached] ".date("F j, Y, g:i a") ." -->\n";
           $menu .= wp_nav_menu($_args);
           $menu .= "\n<!-- // [cached] -->\n";

           file_put_contents($menu_file, $menu);
       }

       include_once($menu_file);

   } else { // backup ##

       wp_nav_menu($args);

   }

}
}


if ( ! function_exists( 'q_wp_nav_menu' ) )
{
/**
 * Build WP menu with warning about missing menus
 *
 * @since       1.0
 *
 */
    function q_wp_nav_menu( $menu, $selector = 'menu', $fallback_cb = 'wp_page_menu', $unique = false, $cache = true, $walker = '' ) {

       // grab some globals ##
       global $post;

       // instatiate transient object ##
       $q_transients = new Q_Transients();

       // fallback for non post pages ##
       $postID = ( !isset( $post ) ? '00' : $post->ID );

       // set nav menu transient ID ##
       $q_wp_nav_menu = ( $unique ? 'q_nav_menu_'.$menu.'_'.$postID : 'q_nav_menu_'.$menu );

       if ( $menu ) {

           if ( $cache !== false ) {
               $nav_menu = $q_transients->get( $q_wp_nav_menu );
               #pr( $nav_menu );
           }

           if ( !isset($nav_menu) || $nav_menu === false ) { // built it ##

               if ( has_nav_menu( $menu ) ) { // menu found ##

                   // get the menu ##
                   $nav_menu = wp_nav_menu( array (
                       'menu'              => $menu, // menu name ##
                       'theme_location'    => $menu, // menu based on theme location ##
                       'fallback_cb'       => $fallback_cb, // fallback function ##
                       //'items_wrap'      => '%3$s', // no ul wrap ##
                       'container_class'   => $selector, // container class ##
                       'menu_class'        => $menu,
                       //'menu_id'         => 'menu-'.$menu,
                       'container'         => 'false', // no container div ##
                       'echo'              => false, // return a variable ##
                       'walker'            => $walker // nav menu walker class ##
                   ) );

                   // cache it ##
                   if ( $cache !== false ) {
                       $q_transients->set( $q_wp_nav_menu, $nav_menu );
                   }

                   // filter it ##
                   apply_filters( 'q_wp_nav_menu_'.$menu, $nav_menu );

                   // return it ##
                   echo $nav_menu;

               } else {

                   // echo warning ##
                   q_wp_nav_menu_warning( $menu );

               }

           } else {

               // filter it ##
               apply_filters( 'q_wp_nav_menu_'.$menu, $nav_menu );

               // return it ##
               echo $nav_menu;

           }

       }

    }
}


if ( ! function_exists( 'q_wp_nav_menu_warning' ) )
{
/**
 * Return a warning about a missing menu
 *
 * @since       1.0
 */
    function q_wp_nav_menu_warning( $menu ){

        #global $q_options;

        // instatiate Q_Options Class
        $q_options = options::get();

        // grab the options ##
        #$q_options = $q_options_class->get();

        #Q_Control::log( $q_options );

        if ( $menu && current_user_can('administrator') && $q_options->framework_warning === TRUE ) {

           $fix = '';
           $locations = get_nav_menu_locations();
           #pr( $locations );
           if ( isset ( $locations[ $menu ] ) ) {
               $error = 'not selected';
               $fix = ' - <a href="'.admin_url().'nav-menus.php">fix</a>';
           } elseif ( is_array ( $locations ) && !array_key_exists( $menu, $locations ) ) {
               $error = 'name error';
           } else {
               $error = 'not selected';
               $fix = ' - <a href="'.admin_url().'nav-menus.php">fix</a>';
           }

           if ( !current_theme_supports('menus') ) { $error == __( "not supported!", "q-framework" ); }

           // compile ##
           $return = '<div class="error"><strong>Menu '.$error.':</strong> '.$menu.$fix.'</div>';

           // apply filters ##
           $return = apply_filters( 'q_wp_nav_menu_warning', $return );

           // return string ##
           return Q_Control::log ( $return );

       }
    }
}



if ( ! function_exists( 'q_add_update_option' ) ) 
{
/**
 * add or update option ##
 * 
 * @since 0.2
 */
    function q_add_update_option ( $option_name, $new_value, $deprecated = ' ', $autoload = 'no' ) {
        if ( get_option( $option_name ) != $new_value ) {
            update_option( $option_name, $new_value );
        } else {
            add_option( $option_name, $new_value, $deprecated, $autoload );
        }
    }
}



if ( ! function_exists( 'q_add_theme_support' ) )
{
/**
 * Add or Remove Support for Q functionality
 *
 * @return          void
 * @since           0.1
 */
    function q_add_theme_support( $support )
    {

        if ( ! class_exists( '\q\ui\core\options' ) ) {

            Q::log( 'options class missing, install q_ui plugin and activiate.' );

            return false;

        } 
        
        // grab the options ##
        $q_options = options::get();

        // Q_Control::log( $q_options );

        if ( $support && is_array( $q_options ) ) { // check to see if $support passed ##

           if ( is_array( $support ) ) {

               foreach ( $support as $add ) {

                   if ( $add ) {

                        // Q_Control::log( 'Add single item from array: '.$add );
                        options::update( $add );

                   }

               }

          } else { // single variable ##

              #Q_Control::log( 'Add single item: '.$support );

              options::update( $support );

          }

       }

       #Q_Control::log( options::get() );

    }

}


if ( ! function_exists( 'q_advanced_comment' ) )
{
/**
 *  Create Custom Comment Callback
 */
    function q_advanced_comment($comment, $args, $depth) {
       $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?>>
        <div class="padding" id="comment-<?php comment_ID() ?>">
            <div class="comment-author vcard">
                <?php

                #echo get_avatar( $comment, $size='48', $default=get_bloginfo("template_url").'/images/avatar.png' );
                // avatar ##
                $avatar = get_avatar( $comment, $size='48' );
                if ( !$avatar ) {
                    $avatar = '<img src="'.q_locate_template( "images/icon_avatar.png", false ).'" alt="Avatar"/>';
                }

                echo $avatar;

                ?>
                <div class="comment-meta">
                    <?php comment_author_link() ?>
                    <small><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></small>
                </div>
             </div>
             <div class="clear"></div>

             <?php if ($comment->comment_approved == '0') : ?>
               <span class="waiting"><?php _e( 'Your comment is awaiting moderation.', 'q-textdomain' ) ?></span><br />
             <?php endif; ?>

             <div class="comment-text">
                 <?php comment_text() ?>
             </div>

            <div class="reply">
                <?php edit_comment_link( __('Edit').' &raquo;',' ','') ?>
                <?php delete_comment_link(get_comment_ID()); ?>
                <?php #comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
                <?php comment_reply_link(array_merge( $args, array(
                        'reply_text' =>
                        'Reply &raquo;',
                        'depth' => $depth,
                        'max_depth' => $args['max_depth'],
                        'login_text' => ''
                    )),
                    $comment->comment_ID ) ?>
            </div>
        </div>
        <div class="clear"></div>
    <?php

    }
}


if ( ! function_exists( 'delete_comment_link' ) )
{
/**
 * Add Extra Comment Moderation Links ##
 */
    function delete_comment_link($id) {
        if ( current_user_can( 'edit_post' ) ) {
            echo '<a href="'.admin_url("comment.php?action=cdc&c=$id").'" class="comment-del-link" title="'.__( "Delete Comment", "q-textdomain" ).'">'.__( "Del", "q-textdomain" ).' &raquo;</a> ';
            echo '<a href="'.admin_url("comment.php?action=cdc&dt=spam&c=$id").'" class="comment-spam-link" title="'.__( "Spam Comment", "q-textdomain" ).'">'.__( "Spam", "q-textdomain" ).' &raquo;</a>';
        }
    }
}


if ( ! function_exists( 'check_referrer' ) )
{
/**
 * Add an Extra Layer of Spam Protection to Comments ##
 */
    add_action( 'check_comment_flood', 'check_referrer' );
    function check_referrer() {
        if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "") {
            wp_die( __('Please enable referrers in your browser, or, if you\'re a spammer, get out of here!') );
        }
    }
}

