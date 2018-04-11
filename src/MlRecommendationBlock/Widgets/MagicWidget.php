<?php

namespace MlRecommendationBlock\Widgets;

use MlRecommendationBlock;
use MlRecommendationBlock\Classes\QueryParams;
use WP_Widget;

class MagicWidget extends WP_Widget {
	private $suffix = " - ML widget";

	function __construct() {
		$className = get_called_class();
		$className = str_replace( "\\", '-', $className );

		parent::__construct(
			$className,
			__( "Recommendation ", 'MlRecommendationBlock' ) . $this->suffix,
			[
				'description' => __( "Recommendation block widget", 'MlRecommendationBlock' ) . $this->suffix
			]
		);
	}

	public function widget( $args, $instance ) {
		$title          = apply_filters( 'widget_title', $instance['title'] );
		$post_types = apply_filters('MlRecommendationBlock__post_types',[ 'post' ]);

		echo $args['before_widget'];
		?>

		<?php
		echo $args['after_widget'];
	}


	public function form( $instance ) {

		?>

        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>">Заголовок</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                   name="<?php echo $this->get_field_name( 'title' ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $instance['title'] ); ?>"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>">
				<?php _e( 'Number of posts:', 'MlRecommendationBlock' ); ?>
            </label>
            <input id="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"
                   name="<?php echo $this->get_field_name( 'posts_per_page' ); ?>"
                   type="number" value="<?php echo $instance['posts_per_page']; ?>"
                   min="-1"
                   size="3"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'last_days' ); ?>">
				<?php _e( 'Number of posts:', 'MlRecommendationBlock' ); ?>
            </label>
            <input id="<?php echo $this->get_field_id( 'last_days' ); ?>"
                   name="<?php echo $this->get_field_name( 'last_days' ); ?>"
                   type="number" value="<?php echo $instance['last_days']; ?>"
                   min="-1"
                   size="3"/>
        </p>



		<?php
	}

	public function update( $new_instance, $old_instance ) {

	    $instance = $this->validate($new_instance);

		return $instance;
	}

	private function validate( $array ) {

		array_walk( $array, function ( $v, $k ) {

			if ( 'title' === $k || 'last_days' === $k ) {
				$array[ $k ] = ( ! empty( $v ) ) ? strip_tags( $v ) : '';
			} else if ( 'posts_per_page' === $k ) {
				$array[ $k ] = ( is_numeric( $v ) ) ? intval( $v ) : 8;
			}

		} );

		return $array;
	}

}