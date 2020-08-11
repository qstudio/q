<?php

namespace q\get;

// Q ##
use q\core;
use q\core\helper as h;
use q\ui;
use q\get;
use q\render;

// Q Theme ##
use q\theme;

class meta extends \q\get {

	
	/**
     * Get Post meta field from acf, format if required and markup
     *
     * @since       4.1.0
     */
    public static function field( $args = null )
    {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
			// || ! isset( $args['field'] )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// pst ID ##
		$post_id = isset( $args['config']['post'] ) ? $args['config']['post']->ID : null ;

		// get field ##
		if ( $value = \get_field( $args['task'], $post_id ) ) {

			// h::log( $value );

			return $value;

		}

		h::log( 'e:>get_field retuned no data - field: "'.$args['task'].'"');
		
		// return ##
		return false;

	}

	

	/**
	 * Get post author
	 * 
	 * @since 4.1.0
	*/
	public static function author( $args = null ) {

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// get post ##
		$post = $args['config']['post'];
		
		// get author ##
		$author = $post->post_author;
		$authordata = \get_userdata( $author );

		// validate ##
		if (
			! $authordata
		) {

			h::log( 'd:>Error in returned author data' );

			return false;

		}

		// get author name ##
		$author_name = $authordata && isset( $authordata->display_name ) ? $authordata->display_name : 'Author' ;

		// new array ##
		$array = [];

		// assign values ##
		$array['permalink'] = \esc_url( \get_author_posts_url( $author ) );
		$array['slug'] = $authordata->user_login;
		$array['title'] = $author_name;

		// h::log( $array );

		// return array ##
		// return $array;

		// return ##
		return get\method::prepare_return( $args, $array );

	}



	public static function comment( $args = null ){

		// sanity ##
		if (
			is_null( $args )
			|| ! is_array( $args )
		){

			h::log( 'e:>Error in passed args' );

			return false;

		}

		// get post ##
		$post = $args['config']['post'];

		// comments ##
		if ( 
			core\config::get([ 'context' => 'global', 'task' => 'config', 'property' => 'allow_comments' ])
			&& 
			'open' == $post->comment_status 
		) {
			
			// new array ##
			$array = [];

			// get number of comments ##
			$comments_number = \get_comments_number( $post->ID );

			if ( $comments_number == 0 ) {
				$comments = __( 'Comment', self::text_domain );
			} elseif ( $comments_number > 1 ) {
				$comments = $comments_number.' '.__( 'Comments', self::text_domain );
			} else {
				$comments = '1 '.__( 'Comment', self::text_domain );
			}

			// assign ##
			$array['title'] = $comments;
			$array['count'] = $comments_number;

			if ( \is_single() ) {

				$array['permalink'] = \get_the_permalink( $post->ID ).'#/scroll/comments';

			} else {

				$array['permalink'] = \get_the_permalink( $post->ID ).'#/scroll/comments'; // variable link ##

			}

			// h::log( $array );

			// return ##
			return get\method::prepare_return( $args, $array );

		}

		// comments are closed ##
		return false;

	}



}
