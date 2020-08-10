<?php

namespace q\module;

use q\core;
use q\core\helper as h;
use q\get;

// load it up ##
\q\module\comment::__run();

class comment extends \Q {
    
    static $args = array();

    /**
    * Get Main Posts Loop
    *
    * @since       1.0.2
    */
    public static function __run()
    {

		// add extra options in module select API ##
		\add_filter( 'acf/load_field/name=q_option_module', [ get_class(), 'filter_acf_module' ], 10, 1 );

		// make running dependent on module selection in Q settings ##
		// h::log( core\option::get('tab') );
		if ( 
			! isset( core\option::get('module')->comment )
			|| true !== core\option::get('module')->comment 
		){

			// h::log( 'd:>Module->Comment is not enabled.' );

			return false;

		}


		// html5 -- https://github.com/bourafai/wp-bootstrap-4-comment-walker ##
		\add_action( 'after_setup_theme', [ get_class(), 'html5_comment_list' ] );

		\add_filter('comment_reply_link', [ get_class(), 'comment_reply_link' ], 100, 4);
		
		// remove "url" field from comments ##
		\add_filter( 'comment_form_default_fields', [ get_class(), 'comment_form_default_fields' ] );
		
		// assets ##
		\add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_script' ], 100 );

		// ajax comments
		\add_action( 'wp_ajax_ajaxcomments', [ get_class(), 'ajaxcomments' ] ); // wp_ajax_{action} for registered user
		\add_action( 'wp_ajax_nopriv_ajaxcomments', [ get_class(), 'ajaxcomments' ] ); // wp_ajax_nopriv_{action} for not registered users
 

	}


	protected static function has_children( $comment_id ) {

		return \get_comments( [ 'parent' => $comment_id, 'count' => true ] ) > 0;

	}


	/**
     * Generate avatar markup
     *
     * @access protected
     * @since 0.1.0
     *
     * @param object $comment Comment to display.
     * @param array  $args    An array of arguments.
     */
    protected static function get_comment_author_avatar( $comment, $args )
    {
        $avatar_string = \get_avatar( $comment, $args['avatar_size'] );
        $comment_author_url = \get_comment_author_url( $comment );

        if ( '' !== $comment_author_url ) {
            $avatar_string = sprintf(
				// '<a href="%1$s" class="author-link url" rel="external nofollow">%2$s</a>',
				'<a href="%1$s" rel="external nofollow" class="author-link url">%2$s',
                esc_url($comment_author_url),
                $avatar_string
            );
        };

        return $avatar_string;
    }


	public static function ajaxcomments(){
		/*
		 * Wow, this cool function appeared in WordPress 4.4.0, before that my code was muuuuch mooore longer
		 *
		 * @since 4.4.0
		 */
		$comment = \wp_handle_comment_submission( \wp_unslash( $_POST ) );

		if ( \is_wp_error( $comment ) ) {

			$error_data = intval( $comment->get_error_data() );

			if ( ! empty( $error_data ) ) {
				\wp_die( '<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure' ), array( 'response' => $error_data, 'back_link' => true ) );
			} else {
				\wp_die( 'Unknown error' );
			}
		}
	 
		/*
		 * Set Cookies
		 */
		$user = \wp_get_current_user();
		\do_action('set_comment_cookies', $comment, $user);
	 
		/*
		 * If you do not like this loop, pass the comment depth from JavaScript code
		 */
		$comment_depth = 1;
		$comment_parent = $comment->comment_parent;
		while( $comment_parent ){

			$comment_depth++;
			$parent_comment = \get_comment( $comment_parent );
			$comment_parent = $parent_comment->comment_parent;

		}
	 
		/*
		* Set the globals, so our comment functions below will work correctly
		*/
		$GLOBALS['comment'] = $comment;
		$GLOBALS['comment_depth'] = $comment_depth;

		$type = get_comment_type( $comment->comment_ID );

		// load config ##
		$config = core\config::get([ 'context' => 'module', 'task' => 'comment' ]);

		$args = $config['args']['list'];

		// h::log( $comment );
		// h::log( $args );

		// $render = new \Comment_Walker;
		// echo $render::html5_comment( $comment, $comment_depth, $config['args']['list'] );
		
		/*
		 * Here is the comment template, you can configure it for your website
		 * or you can try to find a ready function in your theme files
		 */

		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

        $type = get_comment_type( $comment->comment_ID );

        $comment_classes = array();
        $comment_classes[] = 'media border-bottom col-12 px-0';

        // if it's a parent
        if ( self::has_children( $comment->comment_ID ) ) {
            $comment_classes[] = 'parent';
			$comment_classes[] = 'has-children';
			$comment_classes[] = 'pb-3 mb-3';
			$comment_classes[] = 'border-bottom';
        }

        // if it's a child
        if ( $comment_depth > 1 ) {
            $comment_classes[] = 'child';
			$comment_classes[] = 'has-parent';
			$comment_classes[] = 'border-bottom-0';
			$comment_classes[] = 'mt-3 mb-0 pb-0';
            $comment_classes[] = 'parent-' . $comment->comment_parent;
		}
		
		// no children, top level ##
		if ( 
			! self::has_children( $comment->comment_ID )
			&& $comment_depth = 1
		) {
			$comment_classes[] = 'pb-3 mb-3';
		}

        // $comment_classes = apply_filters( 'comment_walker/comment_class', $comment_classes, $comment, $depth, $args );

        $class_str = implode(' ', $comment_classes);

		ob_start();

?>
        <<?php echo $tag; ?> id="comment-<?php echo $comment->comment_ID; ?>" <?php comment_class( $class_str, $comment ); ?>>

            <article id="div-comment-<?php echo $comment->comment_ID; ?>" class="media comment-body">

                <?php if ( 0 != $args['avatar_size'] && 'pingback' !== $type && 'trackback' !== $type ) { ?>
				<div class="p-1 mr-2">
					<?php echo self::get_comment_author_avatar( $comment, $args ); ?>
				</div>
                <?php }; ?>

                <div class="media-body">

                    <footer class="comment-meta">
                        <div class="comment-author vcard">
                            <?php printf( __( '%s <span class="says sr-only">says:</span>' ), sprintf( '<b class="media-heading fn">%s</b>', get_comment_author_link( $comment->comment_ID ) ) ); ?>
                        </div><!-- /.comment-author -->

                        <div class="comment-metadata">
                            <a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>"><time datetime="<?php comment_time( 'c' ); ?>"><?php
								/* translators: 1: comment date, 2: comment time */
								printf( __( '%1$s at %2$s' ), get_comment_date( '', $comment->comment_ID ), get_comment_time() );
								?></time></a>
                            <?php edit_comment_link( __( 'Edit' ), ' | <span class="edit-link text-white">', '</span>' ); ?>
                        </div><!-- /.comment-metadata -->

                        <?php if ( '0' == $comment->comment_approved ) : ?>
                            <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
                        <?php endif; ?>
                    </footer><!-- /.comment-meta -->

                    <div class="comment-content pt-2">
                        <?php comment_text( $comment->comment_ID ); ?>
                    </div><!-- /.comment-content -->

<?php 

			// $comment, $depth, $args, $add_below = 'reply-comment'
			\comment_reply_link( array_merge( $args, array(
				'add_below' => 'reply-comment',
				'depth'     => $comment_depth,
				'max_depth' => $args['max_depth'],
				'before'    => '<div id="reply-comment-'.$comment->comment_ID.'" class="reply">',
				'after'     => '</div>'
			) ) );

			if ( self::has_children( $comment->comment_ID ) ) { 
				
?>
				<div class="child-comments children">
<?php 

			} else { 
				
?>

				</div><!-- /.media-body -->
			</article><!-- /.comment-body -->
<?php 

			} 

?>
		</div>
<?php

		// get output ##
		$comment_html = ob_get_clean();

		// test ##
		h::log( $comment_html );

		// echo ##
		echo $comment_html;

		/*
		$comment_html = '<div ' . \comment_class('', null, null, false ) . ' id="comment-' . \get_comment_ID() . '">
			<article class="comment-body" id="div-comment-' . \get_comment_ID() . '">
				<footer class="comment-meta">
					<div class="comment-author vcard">
						' . \get_avatar( $comment, $config['list']['avatar_size'] ) . '
						<b class="fn">' . \get_comment_author_link() . '</b> <span class="says">says:</span>
					</div>
					<div class="comment-metadata">
						<a href="' . \esc_url( \get_comment_link( $comment->comment_ID ) ) . '">' . sprintf('%1$s at %2$s', \get_comment_date(),  \get_comment_time() ) . '</a>';
	 
						if( $edit_link = \get_edit_comment_link() )
							$comment_html .= '<span class="edit-link"><a class="comment-edit-link" href="' . $edit_link . '">Edit</a></span>';
	 
					$comment_html .= '</div>';
					if ( $comment->comment_approved == '0' )
						$comment_html .= '<p class="comment-awaiting-moderation">Your comment is awaiting moderation.</p>';
	 
				$comment_html .= '</footer>
				<div class="comment-content">' . \apply_filters( 'comment_text', \get_comment_text( $comment ), $comment ) . '</div>
			</article>
		</div>';

		echo $comment_html;
		*/
	 
		die();
	 
	}


	public static function wp_enqueue_script(){

		// Required for nested reply function that moves reply inline with JS ##
		if ( 
			\is_singular() 
			&& \comments_open() 
			&& \get_option( 'thread_comments' ) 
		) {
		
			\wp_enqueue_script( 'comment-reply' ); // enqueue the js that performs in-link comment reply fanciness
		
		}

		// I think jQuery is already included in your theme, check it yourself
		// wp_enqueue_script('jquery');
	
		// just register for now, we will enqueue it below
		\wp_register_script( 'q_ajax_comment', h::get( 'asset/js/module/q.ajax.comment.js', 'return' ), array('jquery') );
	
		// let's pass ajaxurl here, you can do it directly in JavaScript but sometimes it can cause problems, so better is PHP
		\wp_localize_script( 'q_ajax_comment', 'q_ajax_comment_params', array(
			'ajaxurl' => \site_url() . '/wp-admin/admin-ajax.php'
		) );
	
		\wp_enqueue_script( 'q_ajax_comment' );

	}



	public static function comment_reply_link( $content ){

		$extra_classes = 'btn btn-primary text-white';

    	return preg_replace( '/comment-reply-link/', 'comment-reply-link ' . $extra_classes, $content);

	}



	/**
     * Add new libraries to Q Settings via API
     * 
     * @since 2.3.0
     */
    public static function filter_acf_module( $field )
    {

		// pop on a new choice ##
		$field['choices']['comment'] = 'Comment';

		// make it selected ##
		$field['default_value'][0] = 'comment';

		// kick back ##
		return $field;

	}
	

	public static function html5_comment_list() {

		\add_theme_support( 'html5', array( 'comment-list' ) );

	}


    /**
    * Return comment UI
    *
    * @since       1.0.1
    * @return      string       HTML
    */
    public static function get( $args = array() )
    {

		// h::log('e:>HERE..');

        // get the_post ##
        if ( ! $the_post = get\post::object( $args ) ) { 

			h::log( 'd:>No post object found' );
			return false; 
		
		}


		/*
        * If the current post is protected by a password and the visitor has not yet
        * entered the password we will return early without loading the comments.
        */
        if ( \post_password_required( $the_post ) ) {
			
			h::log( 'd:>post is password protected' );
            return false;

		}

		// load config ##
		$config = core\config::get([ 'context' => 'module', 'task' => 'comment' ]);

		// h::log( $config );

		// return array ##
		$array = [];

		// add count ##
		$array['count'] = \get_comments_number( $the_post->ID );

		// build get args - we work from teh current post ##
		$get_args = [ 'post_id' => $the_post->ID ];

		// merge args from config ##
		if( $config['args']['get'] && is_array( $config['args']['get'] ) ){

			$get_args = array_merge( $get_args, $config['args']['get'] );

		}

		// h::log( $get_args );

		// filter ##
		$get_args = \apply_filters( 'q/module/comment/get_args', $get_args );

		// Gather comments for a specific page/post 
		$get_comments = \get_comments( $get_args );
		// $comments_query = new \WP_Comment_Query;
		// $get_comments = $comments_query->query($get_args);

		// h::log( $get_comments );

		if ( 
			\get_comments_number( $the_post->ID ) > 0
			&& $get_comments
			// || \have_comments() 
		){ 

			// title ##
			$array['title'] = 
				sprintf( 
					_nx( 
						isset( $config['text']['title'][0] ) ? $config['text']['title'][0] : 'One comment', 
						isset( $config['text']['title'][1] ) ? $config['text']['title'][1] : '%1$s comments', 
						\get_comments_number( $the_post->ID ), 
						'comments title', 
						'q-textdomain'
					),
					\number_format_i18n( \get_comments_number( $the_post->ID ) ), 
				);

			// start buffer ##
			ob_start();

			// build get args - we work from teh current post ##
			$list_args = [];

			// merge args from config ##
			if( $config['args']['list'] && is_array( $config['args']['list'] ) ){

				$list_args = array_merge( $get_args, $config['args']['list'] );

			}

			// h::log( $list_args );

			// $list_args['per_page'] = \get_option( 'page_comments' ) ?: 5; 

			// h::log( $list_args );

			h::log( 'e:>comments per page: '.\get_option( 'comments_per_page' ) );
			h::log( 'e:>total pages: '.\get_comment_pages_count( $get_comments, $list_args['per_page'] )  );

			// filter ##
			$list_args = \apply_filters( 'q/module/comment/list_args', $list_args );

			\wp_list_comments( $list_args, $get_comments );

			// get comments ##
			$array['comments'] = ob_get_clean();

			// Are there comments to navigate through?
			if ( 
				\get_site_option( 'page_comments' )
				// && \get_comment_pages_count( $get_comments, $list_args['per_page'] ) > 1 
				&& ( \get_comments_number( $the_post->ID ) / $list_args['per_page'] ) > 1
				// && isset( $blah )
				// && \get_site_option( 'page_comments' ) 
			){

				h::log('e:>adding pagination');

				// start buffer ##
				ob_start();

?>
        <nav class="navigation comment-navigation" role="navigation">
            <h1 class="screen-reader-text section-heading"><?php \_e( 'Comment navigation', 'q-textdomain' ); ?></h1>
            <div class="nav-previous"><?php \get_previous_comments_link( \_e( '&larr; Older Comments', 'q-textdomain' ) ); ?></div>
            <div class="nav-next"><?php \get_next_comments_link( \_e( 'Newer Comments &rarr;', 'q-textdomain' ) ); ?></div>
        </nav>
<?php 

				// get pagination ##
				$array['pagination'] = ob_get_clean();

			} // Check for comment navigation 
			
?>
        
<?php 
	
		} else {

			$array['comments'] = 
				isset( $config['text']['default'] ) ? 
				$config['text']['default'] : 
				'<p class="no-comments">'._e('No comments yet').'</p>';

		} 

		// get reply form ##
		$array['reply'] = self::comment_form( $args );

		// h::log( $array['pagination'] );

		// return to renderer ##
		return $array;

	}
	



	protected static function comment_form( $args ){

		// get the_post ##
        if ( ! $the_post = get\post::object( $args ) ) { 

			h::log( 'd:>No post object found' );
			return false; 
		
		}


		/*
        * If the current post is protected by a password and the visitor has not yet
        * entered the password we will return early without loading the comments.
        */
        if ( \post_password_required( $the_post ) ) {
			
			h::log( 'd:>post is password protected' );
            return false;

		}

		// load config ##
		$config = core\config::get([ 'context' => 'module', 'task' => 'comment' ]);
		// h::log( $config );

		if ( ! \comments_open( $the_post->ID ) && \get_comments_number( $the_post->ID ) ) {
		
			return 
				isset( $config['text']['closed'] ) ? 
				$config['text']['closed'] : 
				'<p class="no-comments">'.__( 'Comments are closed.' , 'q-textdomain' ).'</p>';

		}

		ob_start();

		// generate form with args ##
		\comment_form( $config['form'], $the_post->ID );

		return ob_get_clean();

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
