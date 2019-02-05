<?php

namespace q\controller;

use q\core\core as core;
use q\core\helper as helper;

// load it up ##
#\q\q_theme\theme\frontpage::run();

class snippets extends \Q {


    /**
    * Load and return a defined array of snippets from their slug
    *
    * @since       1.0.1
    * @return      string       HTML
    */
    public static function load( $slug = null, $args = array() )
    {

        // pass to parent method
        wordpress::get_snippet( $slug, $args );

    }



    /**
    * Check if the page has a "template_quote" - if so, echo it
    *
    * @since       1.0.6
    * @return      string       HTML
    */
    public static function blockquote( $args = array() )
    {

        #pr( $args );

        // get the_post ##
        if ( ! $the_post = wordpress::the_post( $args ) ) { return false; }

        // grab post to show ##
        if ( ! $the_post->post_block_quote ) { return false; }

        #Q_Template::the_divider( );

?>
        <blockquote><?php echo $the_post->post_block_quote; ?></blockquote>
<?php

        // get divider ##
        #Q_Template::the_divider( );

    }



    /**
    * Check if the page has a "template_footer" - if so, echo it
    *
    * @since       1.0.6
    * @return      string       HTML
    */
    public static function footer( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = wordpress::the_post( $args ) ) { return false; }

        // grab post to show ##
        if ( ! $the_post->template_footer ) { return false; }

?>
        <div class="column-left">
            <div class="template_footer">
<?php

            // echo content ##
            echo \wpautop( $the_post->template_footer );

?>
            </div>
        </div>
<?php

    }




    /**
    * Get Donate Link
    *
    * @since       1.4.0
    * @return      string   HTML
    */
    public static function donate( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = wordpress::the_post( $args ) ) { return false; }

?>
        <ul class="donate-link">
            <li>
                <a href="https://greenheart.info/donate" target="_blank" title="<?php _e( "Donate" , self::text_domain ); ?>">
                    <?php _e( "Donate" , self::text_domain ); ?>
                </a>
            </li>
        </ul>
<?php

    }



    /**
    * Get Q_facebook share widget
    *
    * @since       1.0.5
    * @return      string       HTML
    */
    public static function share( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = wordpress::the_post( $args ) ) { return false; }

        $fb_name = \get_the_title( $the_post->ID );
        $fb_link = \get_permalink( $the_post->ID );
        $fb_description = \q_excerpt_from_id( $the_post->ID );
        $fb_caption = \get_post_meta( \get_post_thumbnail_id( $the_post->ID ), '_wp_attachment_image_alt', true );
        $fb_pictures = \wp_get_attachment_image_src( \get_post_thumbnail_id( $the_post->ID ), 'thumb' ); // get post image ##
        $fb_picture = $fb_pictures[0];

?>
        <span class="widget widget-share widget-facebook">
            <a href="#" title="<?php _e( 'Share on Facebook', self::text_domain ); ?>" rel="share" class="share_link q_facebook_share">
                <?php _e( 'Share on Facebook', self::text_domain ); ?>
            </a>
        </span>

        <div id="fb-root"></div>
        <script>
            window.fbAsyncInit = function() {
                FB.init({appId: 'YOUR APP ID', status: true, cookie: true,
                xfbml: true});
                };
                (function() {
                var e = document.createElement('script'); e.async = true;
                e.src = document.location.protocol +
                '//connect.facebook.net/en_US/all.js';
                document.getElementById('fb-root').appendChild(e);
            }());
        </script>
        <script>

            jQuery(document).ready(function($) {

                // FB share ##
                $(".widget-facebook a").click(function(e) {

                    console.log( "clicked share... FB not found yet.." );

                    e.preventDefault();

                    if ( typeof FB !== "undefined" ) {

                        FB.ui (
                            {
                                method: 'feed',
                                name: '<?php echo esc_js( $fb_name ); ?>',
                                link: '<?php echo esc_js( $fb_link ); ?>',
                                picture: '<?php echo esc_js( $fb_picture ); ?>',
                                caption: '<?php echo esc_js( $fb_caption ); ?>',
                                description: '<?php echo esc_js( $fb_description ); ?>'
                            },
                            function(response) {
                                if ( response && response.post_id ) {
                                    jQuery(".q_facebook_share").text('<?php _e( 'Shared :)', self::text_domain ); ?>');
                                } else {
                                    jQuery(".q_facebook_share").text('<?php _e( 'Failed :(', self::text_domain ); ?>');
                                    fb_restore = setTimeout(function(){
                                        jQuery(".q_facebook_share").text('<?php _e( 'Share', self::text_domain ); ?>');
                                    }, 3000);
                                }
                            }
                        );

                    }

                });

            });

        </script>
<?php

    }



    /**
    * Get blog post categories and return in html markup
    *
    * @since       1.0.7
    * @return      string       HTML
    */
    public static function post_categories( $args = array() )
    {

?>
        <div class="widget widget-post-categories">
            <h3><?php \_e( 'Categories', self::text_domain ); ?></h3>
            <ul>
<?php

                // get all categories ##
                $get_the_category = \get_the_category();

                // no highlight ##
                $current_category = 0;

                // if we found the current category ##
                if ( $get_the_category ) {

                    // take first item ##
                    $current_category = $get_the_category[0]->cat_ID;

                }

                // allow highlight ##
                if ( \is_single() ) {

                    $args = array(
                        'current_category'  => $current_category,
                        'echo'              => 0,
                        'orderby'           => 'count',
                        'order'             => 'DESC',
                        'show_count'        => 1,
                        'title_li'          => false,
                    );

                    #$print_category = wp_list_categories('current_category='.$current_category.'&echo=0&orderby=count&show_count=1&title_li=');

                // no highlight ##
                } else {

                    $args = array(
                        'current_category'  => 0,
                        'echo'              => 0,
                        'orderby'           => 'count',
                        'order'             => 'DESC',
                        'show_count'        => 1,
                        'title_li'          => false,
                    );

                    #$print_category = wp_list_categories('current_category=0&echo=0&orderby=count&show_count=1&title_li=');

                }

                // call it ##
                $print_category = \wp_list_categories( $args );

                // open class ##
                $print_category = str_replace( "(", "<span class='count'>(", $print_category);

                // close class ##
                $print_category = str_replace( ")", ")</span>", $print_category);

                // print it ##
                echo $print_category;

?>
            </ul>
        </div>
<?php


    }



    /**
    * Get widget data
    *
    * @since       1.0.4
    * @return      Mixed
    */
    public static function follow( $args = array() )
    {

        // grab widget data ##
        if ( ! $widget_data = \get_option( "widget_q_widget_follow", false ) ) { return false; }

?>
        <ul class="widget widget-follow wrapper-padding">
<?php

            foreach ( $widget_data[2]["follow"] as $key => $value ) {

                // skip if no value found for follow item ##
                if ( ! $value || $value == '' ) { continue; }

                // grab image - skip if not found ##
                if ( ! $img = \q_locate_template( 'images/q/social_'.$key.'.png', false ) ) { continue; }

?>
            <li class="<?php echo $key; ?>">
                <a href="http://<?php echo $value; ?>" target="_blank" alt="<?php echo $key; ?>">
                    <img src="<?php echo $img; ?>"/>
                </a>
            </li>
<?php

            } // loop back for more ##

            // Q Credit link ##
            if ( $args == 'show' ) { self::the_q(); }

?>
        </ul>
<?php

    }


    /**
     * Get Q Link
     *
     * @since       0.7
     * @return      string   HTML
     */
    public static function q()
    {

?>
        <li class="q">
            <a href="http://qstudio.us" target="_blank" title="<?php _e( "Q Studio: We &hearts; WordPress", self::text_domain ); ?>" class="q_link">
                <span class="em"><?php _e( "by", self::text_domain ); ?></span><img src="<?php echo q_locate_template('images/q.png'); ?>" />
            </a>
        </li>
<?php

    }




    /**
     * CCI GH Contact Details
     *
     * @since       1.4.0
     * @return      string       HTML
     */
    public static function contact( $args = array() )
    {

        // get the_post ##
        #if ( ! $the_post = self::the_post( $args ) ) { return false; }

?>
        <ul class="widget widget-contact wrapper-padding">
            <li class="company">CCI Greenheart</li>
            <li class="greenheart hide-handheld">A division of Greenheart International</li>
            <li class="address hide-handheld">746 N. LaSalle Drive, Chicago, IL, 60654</li>
            <li class="address hide-handheld">United States</li>
            <li class="tel-1">Tel: 312-944-2544</li>
            <li class="tel-2 hide-handheld">Toll-free ( in the U.S. ): 866-CCI-0061</li>
        </ul>
<?php

    }


    



    /**
     * CCI GH Contact Details
     *
     * @since       1.4.1
     * @return      string       HTML
     */
    public static function contact_details( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = wordpress::the_post( $args ) ) { return false; }

        // Contact data is stored in 3 different field groups:
        // 1. contact_programs - this is a repeater with fields "title" & "text"
        // 2. International Partners - with fields "contacts_partners" & "contacts_partners_text"
        // 3. Educators - with fields "contacts_educators" & "contacts_educators_text"

        if( \have_rows( 'contact_programs' ) ) {

?>
            <ul class="contact-programs">
<?php

            while( \have_rows( 'contact_programs') ) :

                \the_row();

                // vars ##
                $title = \get_sub_field( 'title' );
                $text = \get_sub_field( 'text' );

                // check we've got all we need ##
                if ( $title && $text ) {

?>
                <li>
                    <h2><?php echo $title; ?></h2>
                    <p><?php echo $text; ?></p>
                </li>
<?php

                }

            endwhile ;

?>
            </ul>
            <div class="clear"></div>
<?php

            // grab international partners ##
            if ( \get_field( 'contacts_partners' ) && \get_field( 'contacts_partners_text' ) ) {

?>
            <ul class="contact-partners">
                <li>
                    <h2><?php \the_field( 'contacts_partners' ); ?></h2>
                    <p><?php \the_field( 'contacts_partners_text' ); ?></p>
                </li>
            </ul>
<?php

            } // partners ##

            // grab international partners ##
            if ( \get_field( 'contacts_educators' ) && \get_field( 'contacts_educators_text' ) ) {

?>
            <ul class="contact-educators">
                <li>
                    <h2><?php \the_field( 'contacts_educators' ); ?></h2>
                    <p><?php \the_field( 'contacts_educators_text' ); ?></p>
                </li>
            </ul>
<?php

            } // educators ##


        }

    }

    


    /**
     * Get Contact Link
     *
     * @since       0.7
     * @return      string   HTML
     */
    public static function contact_link( $title = null )
    {

        // check we got a result ##
        if ( ! $post = wordpress::get_post_by_meta( array( 'meta_value' => 'contact' ) ) ) { return false; }

        // allow for customized title -- passed in method arguments ##
        $contact_title = ! is_null ( $title ) ? $title : get_the_title( $post->ID );

?>
        <a href="<?php echo esc_url( get_permalink( $post->ID ) ); ?>" title="<?php echo esc_attr( $contact_title ); ?>" rel="contact" class="contact_link">
            <?php echo esc_attr( $contact_title ); ?>
        </a>
<?php

    }



    /**
     * Get Rights Link
     *
     * @since       0.7
     * @return      string   HTML
     */
    public static function rights_link()
    {

?>
        <ul class="rights-link">
            <li>
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
                    &copy; <?php echo date("Y"); ?> Greenheart Travel
                </a>
            </li>
<?php

            // privacy link ##
            wordpress::the_snippet( 'privacy_link' );

?>
        </ul>
<?php

    }



    /**
     * Get Privacy Link
     *
     * @since       1.4.0
     * @return      string   HTML
     */
    public static function privacy_link()
    {

        // check we got a result ##
        if ( ! $post = wordpress::get_post_by_meta( array( 'meta_value' => 'privacy' ) ) ) { return false; }

?>
        <li class="privacy_link">
            <a href="<?php echo \esc_url( \get_permalink( $post->ID ) ); ?>" title="<?php echo \esc_attr( \get_the_title( $post->ID ) ); ?>" rel="privacy">
                <?php echo \esc_attr( \get_the_title( $post->ID ) ); ?>
            </a>
        </li>
<?php

    }



    /**
     * back to parent button, for deep sitting pages
     *
     * @since       1.0.1
     * @return      string   HTML
     */
    public static function parent( $args = array() )
    {

        // try to load up parent ##
        if ( ! $object = wordpress::get_parent( $args ) ) { return false; }

?>
        <ul class="widget widget-parent">
            <li>
                <a href="<?php echo $object->url; ?>"><?php echo $object->text; ?></a>
            </li>
        </ul>
<?php

    }





}