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

		// filter comment_reply_link ##
		\add_filter( 'comment_reply_link', [ get_class(), 'comment_reply_link' ], 100, 4);
		
		// remove "url" field from comments ##
		\add_filter( 'comment_form_default_fields', [ get_class(), 'comment_form_default_fields' ] );
		
		// assets ##
		\add_action( 'wp_enqueue_scripts', [ get_class(), 'wp_enqueue_script' ], 100 );

		// ajax post comment
		\add_action( 'wp_ajax_ajaxcomments', [ get_class(), 'ajax_post_comment' ] ); // wp_ajax_{action} for registered user
		\add_action( 'wp_ajax_nopriv_ajaxcomments', [ get_class(), 'ajax_post_comment' ] ); // wp_ajax_nopriv_{action} for not registered users

		// ajax load comments
		\add_action( 'wp_ajax_q_ajax_comment_load', [ get_class(), 'ajax_load_comments' ] ); // wp_ajax_{action}
		\add_action( 'wp_ajax_nopriv_q_ajax_comment_load', [ get_class(), 'ajax_load_comments' ] ); // wp_ajax_nopriv_{action}
 
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
	


	/**
	 * Load comments from frontend, via AJAX
	 * 
	 * @since 4.5.0 
	*/
	public static function ajax_load_comments(){
 
		if ( ! isset( $_POST['post_id'] ) ){

			h::log( 'No post_id found' );

			return die;

		}

		if ( 
			! \check_ajax_referer( 'q_comment_load_ajax', 'nonce', false ) 
		) {

			h::log( 'e:>Load Nonce check failed' );

			return die;

		}

		// maybe it isn't the best way to declare global $post variable, but it is simple and works perfectly!
		// global $post;

		$post = \get_post( $_POST['post_id'] );
		
		\setup_postdata( $post );

		// $cpage = isset( $_POST['cpage'] ) ? $_POST['cpage'] : 1;
		// h::log( 'e:>AJAX cpage: '.$cpage );

		// load config ##
		$config = core\config::get([ 'context' => 'module', 'task' => 'comment' ]);

		// h::log( $config );

		// return array ##
		$array = [];

		// add count ##
		// $array['count'] = \get_comments_number( $post->ID );

		// build get args - we work from teh current post ##
		$get_args = [ 'post_id' => $post->ID ];

		// merge args from config ##
		if( $config['args']['get'] && is_array( $config['args']['get'] ) ){

			$get_args = array_merge( $get_args, $config['args']['get'] );

		}

		$get_args['number'] = 1000000; // all comments, hopefully... ## 
		// $get_args['offset'] = \get_site_option( 'comments_per_page' ) ?: 1; // skip one latest comment, already shown ##

		// h::log( $get_args );

		// filter ##
		$get_args = \apply_filters( 'q/module/comment/ajax/get_args', $get_args );

		// Gather comments for a specific page/post 
		$get_comments = \get_comments( $get_args );

		// h::log( $get_comments );

		if ( 
			\get_comments_number( $post->ID ) > 0
			&& $get_comments
		){ 

			// build get args - we work from teh current post ##
			$list_args = [];

			// merge args from config ##
			if( $config['args']['list'] && is_array( $config['args']['list'] ) ){

				$list_args = array_merge( $get_args, $config['args']['list'] );

			}

			$list_args['per_page'] = '10000'; // all comments, hopefully... ## 

			// h::log( $list_args );

			// filter ##
			$list_args = \apply_filters( 'q/module/comment/ajax/list_args', $list_args );
	 
			// actually we must copy the params from wp_list_comments() used in our theme
			\wp_list_comments( $list_args, $get_comments );

		}

		die; // don't forget this thing if you don't want "0" to be displayed

	}


	/**
	 * Check if a comment has_children
	 * 
	 * @since 4.5.0
	*/
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




	/**
	 * Post a comment from frontend, via AJAX
	 * 
	 * @since 4.5.0 
	*/
	public static function ajax_post_comment(){

		if ( 
			! \check_ajax_referer( 'q_comment_post_ajax', 'nonce', false ) 
		) {

			h::log( 'e:>Post Nonce check failed' );

			return die;

		}

		// handle comment subumission ##
		$comment = \wp_handle_comment_submission( \wp_unslash( $_POST ) );

		if ( \is_wp_error( $comment ) ) {

			$error_data = intval( $comment->get_error_data() );

			if ( ! empty( $error_data ) ) {
				\wp_die( '<p>' . $comment->get_error_message() . '</p>', __( 'Comment Submission Failure' ), array( 'response' => $error_data, 'back_link' => true ) );
			} else {
				\wp_die( 'Unknown error' );
			}
		}

		// notifications ##
		\wp_notify_postauthor( $comment->comment_ID );
	 
		// Set Cookies ##
		$user = \wp_get_current_user();
		\do_action( 'set_comment_cookies', $comment, $user );
	 
		// If you do not like this loop, pass the comment depth from JavaScript code ##
		$comment_depth = 1;
		$comment_parent = $comment->comment_parent;
		while( $comment_parent ){

			$comment_depth++;
			$parent_comment = \get_comment( $comment_parent );
			$comment_parent = $parent_comment->comment_parent;

		}
	 
	 	// Set the globals, so our comment functions below will work correctly ##
		$GLOBALS['comment'] = $comment;
		$GLOBALS['comment_depth'] = $comment_depth;

		$type = \get_comment_type( $comment->comment_ID );

		// load config ##
		$config = core\config::get([ 'context' => 'module', 'task' => 'comment' ]);

		// define args array ##
		$args = $config['args']['list'];

		// h::log( $comment );
		// h::log( $args );

		// comment template ##
		$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';

        $type = \get_comment_type( $comment->comment_ID );

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

		// filter ##
        $comment_classes = apply_filters( 'comment_walker/comment_class', $comment_classes, $comment, $depth, $args );

		// stringify ##
        $class_str = implode(' ', $comment_classes);

		// kick in to buffering ##
		ob_start();

?>
        <<?php echo $tag; ?> id="comment-<?php echo $comment->comment_ID; ?>" <?php \comment_class( $class_str, $comment ); ?>>

            <article id="div-comment-<?php echo $comment->comment_ID; ?>" class="media comment-body">

                <?php if ( 0 != $args['avatar_size'] && 'pingback' !== $type && 'trackback' !== $type ) { ?>
				<div class="p-1 mr-2">
					<?php echo self::get_comment_author_avatar( $comment, $args ); ?>
				</div>
                <?php }; ?>

                <div class="media-body">

                    <footer class="comment-meta p-0">
                        <div class="comment-author vcard">
                            <?php printf( __( '%s <span class="says sr-only">says:</span>' ), sprintf( '<b class="media-heading fn">%s</b>', \get_comment_author_link( $comment->comment_ID ) ) ); ?>
                        </div><!-- /.comment-author -->

                        <div class="comment-metadata">
                            <a href="<?php echo esc_url( \get_comment_link( $comment, $args ) ); ?>"><time datetime="<?php \comment_time( 'c' ); ?>"><?php
								/* translators: 1: comment date, 2: comment time */
								printf( __( '%1$s at %2$s' ), \get_comment_date( '', $comment->comment_ID ), \get_comment_time() );
								?></time></a>
                            <?php \edit_comment_link( __( 'Edit' ), ' | <span class="edit-link text-white">', '</span>' ); ?>
                        </div><!-- /.comment-metadata -->

                        <?php if ( '0' == $comment->comment_approved ) : ?>
                            <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
                        <?php endif; ?>
                    </footer><!-- /.comment-meta -->

                    <div class="comment-content pt-2">
                        <?php \comment_text( $comment->comment_ID ); ?>
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

			// children weird markup stuff ##
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
		// h::log( $comment_html );

		// echo back reponse to JS ##
		echo json_encode([ 'success' => true, 'comment' => $comment->comment_ID, 'result' => $comment_html ]);
			
		// AJAX must die ##
		die();
	 
	}




	/**
	 * Enqueue assets
	 * 
	 * @since 4.5.0
	*/
	public static function wp_enqueue_script(){

		// enqueue the js that performs in-link comment reply fanciness
		if ( 
			\is_singular() 
			&& \comments_open() 
			&& \get_option( 'thread_comments' ) 
		) {
		
			\wp_enqueue_script( 'comment-reply' ); 
		
		}

		// register script ##
		\wp_register_script( 'q_ajax_comment', h::get( 'asset/js/module/q.ajax.comment.js', 'return' ), array('jquery') );
	
		// localize php data to js via handle ##
		\wp_localize_script( 'q_ajax_comment', 'q_ajax_comment_params', array(
			'ajaxurl' 		=> \site_url() . '/wp-admin/admin-ajax.php',
			'post_nonce'	=> \wp_create_nonce( 'q_comment_post_ajax' ),
			'load_nonce'    => \wp_create_nonce( 'q_comment_load_ajax' ),
			'post_id' 		=> \get_the_ID()
		) );
	
		// enqueue script on frontend ##
		\wp_enqueue_script( 'q_ajax_comment' );

	}



	/**
	 * Filter cooment_reply_link
	 * 
	 * @since 4.5.0
	*/
	public static function comment_reply_link( $content ){

		$extra_classes = 'q_comment_reply btn btn-primary text-white ';

    	return preg_replace( '/comment-reply-link/', 'comment-reply-link ' . $extra_classes, $content);

	}



	/**
	 * Add HTML5 options to comment list
	 * 
	 * @since 4.5.0
	*/
	public static function html5_comment_list() {

		\add_theme_support( 'html5', array( 'comment-list' ) );

	}



    /**
    * Get comment UI
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
			/*
			if ( 
				\get_site_option( 'page_comments' )
				&& ( \get_comments_number( $the_post->ID ) / $list_args['per_page'] ) > 1
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
			*/
			
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
	


	/**
	 * Filter comment_form
	 * 
	 * @since 4.5.0
	*/
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
