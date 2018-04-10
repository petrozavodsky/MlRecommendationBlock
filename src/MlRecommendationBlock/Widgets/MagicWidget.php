<?php

namespace MlRecommendationBlock\Widgets;

use MlRecommendationBlock;
use MlRecommendationBlock\Classes\QueryParams;
use WP_Widget;

class MagicWidget extends WP_Widget {
	private $suffix = " - ML widget";

	function __construct() {
		$className        = get_called_class();
		$className        = str_replace( "\\", '-', $className );

		parent::__construct(
			$className,
			__( "Recommendation ", 'MlRecommendationBlock' ) . $this->suffix,
			[
				'description' => __( "Recommendation block widget", 'MlRecommendationBlock' ) . $this->suffix
			]
		);
	}

	public function widget( $args, $instance ) {
	    new QueryParams();
		echo $args['before_widget'];
		?>

		<?php
		echo $args['after_widget'];
	}


	public function form( $instance ) {
		echo '<p class="no-options-widget">' . __( 'There are no options for this widget.', 'MlRecommendationBlock' ) . '</p>';

		return 'noform';
	}

	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

}