<?php

namespace q\search\widget;

use q\search\core\helper as h;
use q\search;
// use q\search\core;

/**
 * Widget - Search
 *
 * @since       2.0.0
 */

if ( ! class_exists( 'q_widget_search' ) )
{

    // load Widget on the widget_init action ##
    add_action('widgets_init', create_function('', 'return register_widget("q_widget_search");'));

    class q_widget_search extends \WP_Widget
    {

        static $properties = array();

        /**
        * Register widget with WordPress.
        */
        public function __construct(  ) {

            parent::__construct(
                'q_widget_search', // Base ID
                'Q - Search', // Name
                array( 'description' => __( 'Adds fields and front-end display to search widget.', 'q-textdomain' ), ) // Args
            );

        }



        /**
        * Front-end display of widget.
        *
        * @see WP_Widget::widget()
        *
        * @param array $args     Widget arguments.
        * @param array $instance Saved values from database.
        */
        public function widget( $args, $instance ) {
                
            // get widget settings ##
            $title = $instance['title'] ? $instance['title'] : core::properties("widget_title");
            $this->settings["title"] = \apply_filters( 'widget_title', $title );

            $post_type = $instance['post_type'] ? $instance['post_type'] : core\method::properties("post_type");
            $this->settings["post_type"] = \apply_filters( 'widget_post_type', $post_type );

            $taxonomies = $instance['taxonomies'] ? $instance['taxonomies'] : core::properties("taxonomies") ;
            $this->settings["taxonomies"] = \apply_filters( 'widget_taxonomies', $taxonomies );

            $posts_per_page = $instance['posts_per_page'] ? $instance['posts_per_page'] : core::properties("posts_per_page") ;
            $this->settings["posts_per_page"] = \apply_filters( 'widget_posts_per_page', $posts_per_page );

            $class = $instance['class'] ? $instance['class'] : core::properties("class") ;
            $this->settings["class"] = \apply_filters( 'widget_class', $class );

            // check if widget settings ok ##
            if ( isset( $this->settings ) && array_filter( $this->settings ) ) {

                // build search ##
                theme::render();

            } // setting ok ##

        }


	
        /**
        * Sanitize widget form values as they are saved.
        *
        * @see WP_Widget::update()
        *
        * @param array $new_instance Values just sent to be saved.
        * @param array $old_instance Previously saved values from database.
        *
        * @return array Updated safe values to be saved.
        */
        public function update( $new_instance, $old_instance ) {

            $instance = array();
            $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '' ;
            $instance['post_type'] = ( ! empty( $new_instance['post_type'] ) ) ? strip_tags( $new_instance['post_type'] ) : core::properties("post_type") ;
            $instance['taxonomies'] = ( ! empty( $new_instance['taxonomies'] ) ) ? strip_tags( $new_instance['taxonomies'] ) : core::properties("taxonomies") ;
            $instance['posts_per_page'] = ( ! empty( $new_instance['posts_per_page'] ) ) ? strip_tags( $new_instance['posts_per_page'] ) : core::properties("posts_per_page") ;
            $instance['class'] = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : core::properties("class") ;

            return $instance;

        }



        /**
        * Back-end widget form.
        *
        * @see WP_Widget::form()
        *
        * @param array $instance Previously saved values from database.
        */
        public function form( $instance ) {

            $title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : core::properties("widget_title") ;
            $post_type = isset( $instance[ 'post_type' ] ) ? $instance[ 'post_type' ] : core::properties("post_type") ;
            $taxonomies = isset( $instance[ 'taxonomies' ] ) ? $instance[ 'taxonomies' ] : core::properties("taxonomies") ;
            $posts_per_page = isset( $instance[ 'posts_per_page' ] ) ? $instance[ 'posts_per_page' ] : core::properties("posts_per_page") ;
            $class = isset( $instance[ 'class' ] ) ? $instance[ 'class' ] : core::properties("class") ;

?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post Type:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'post_type' ); ?>" name="<?php echo $this->get_field_name( 'post_type' ); ?>" type="text" value="<?php echo esc_attr( $post_type ); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'taxonomies' ); ?>"><?php _e( 'Taxonomies:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'taxonomies' ); ?>" name="<?php echo $this->get_field_name( 'taxonomies' ); ?>" type="text" value="<?php echo esc_attr( $taxonomies ); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php _e( 'Posts Per Page:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>" name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>" type="text" value="<?php echo esc_attr( $posts_per_page ); ?>">
            </p>
            <p>
                <label for="<?php echo $this->get_field_id( 'class' ); ?>"><?php _e( 'Class:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'class' ); ?>" name="<?php echo $this->get_field_name( 'class' ); ?>" type="text" value="<?php echo esc_attr( $class ); ?>">
            </p>
<?php


        }

    }

}