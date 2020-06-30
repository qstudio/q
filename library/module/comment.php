<?php

namespace q\module;

use q\core;
use q\core\helper as h;

// Q ##
use q\get;

// load it up ##
\q\module\comment::run();

class comment extends \Q {
    
    static $args = array();

    /**
    * Get Main Posts Loop
    *
    * @since       1.0.2
    */
    public static function run()
    {

        // filter comment template ##
		add_filter( "comments_template", array( get_class(), "the_comment_template" ), 10 );
		
		// remove "url" field from comments ##
        \add_filter( 'comment_form_default_fields', array( get_class(), 'comment_form_default_fields' ) );

    }


    /**
    * Return the_content with basic filters applied
    *
    * @since       1.0.1
    * @return      string       HTML
    */
    public static function the_comment_template( $args = array() )
    {

        // get the_post ##
        if ( ! $the_post = get\post::object( $args ) ) { return false; }

        // Parse incoming $args into an array and merge it with $defaults - caste to object ##
        #$args = ( object )\wp_parse_args( $args, config::$the_content );

        /*
        * If the current post is protected by a password and the visitor has not yet
        * entered the password we will return early without loading the comments.
        */
        if ( \post_password_required() ) {
        
            return;

        }

?>
<span class="anchor" data-scroll="comments"></span>
<div id="comments" class="comments-area">
 
    <?php if ( \have_comments() ) : ?>
        <h2 class="comments-title">
            <?php
                printf( _nx( 'One thought on "%2$s"', '%1$s thoughts on "%2$s"', \get_comments_number(), 'comments title', 'twentythirteen' ),
                    \number_format_i18n( \get_comments_number() ), '<span>' . \get_the_title() . '</span>' );
            ?>
        </h2>
 
        <ol class="comment-list">
            <?php
                \wp_list_comments( array(
                    'style'       => 'ol',
                    'short_ping'  => true,
                    'avatar_size' => 48,
                ) );
            ?>
        </ol><!-- .comment-list -->
 
        <?php
            // Are there comments to navigate through?
            if ( \get_comment_pages_count() > 1 && \get_option( 'page_comments' ) ) :
        ?>
        <nav class="navigation comment-navigation" role="navigation">
            <h1 class="screen-reader-text section-heading"><?php _e( 'Comment navigation', 'twentythirteen' ); ?></h1>
            <div class="nav-previous"><?php \previous_comments_link( __( '&larr; Older Comments', 'twentythirteen' ) ); ?></div>
            <div class="nav-next"><?php \next_comments_link( __( 'Newer Comments &rarr;', 'twentythirteen' ) ); ?></div>
        </nav><!-- .comment-navigation -->
        <?php endif; // Check for comment navigation ?>
 
        <?php if ( ! \comments_open() && \get_comments_number() ) : ?>
        <p class="no-comments"><?php _e( 'Comments are closed.' , 'twentythirteen' ); ?></p>
        <?php endif; ?>
 
    <?php endif; // have_comments() ?>
 
    <?php \comment_form(); ?>
 
</div><!-- #comments -->
<?php

    }

	
    
    /**
     * Filter to remove URL field from comments form
     *
     * @since       1.6.1
     * @param       Array  $fields
     * @return      Array
     */
    public static function comment_form_default_fields( $fields )
    {

        if ( isset( $fields['url'] ) ) {

            unset($fields['url']);

        }

        // kick it back ##
        return $fields;

    }


}
