<?php

namespace MlRecommendationBlock\Widgets;

use MlRecommendationBlock;
use MlRecommendationBlock\Classes\QueryParams;
use WP_Widget;

class WidgetRecommendation extends WP_Widget {
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
		$title      = apply_filters( 'widget_title', $instance['title'] );
		$post_types = apply_filters( 'MlRecommendationBlock__post_types', [ 'post' ] );
		$image_size = apply_filters( 'MlRecommendationBlock__image_size', 'thumbnail' );


		$params = new QueryParams( $instance['last_days'] );

		$args = [
			'posts_per_page' => $instance['posts_per_page'],
			'post_type'      => $post_types,
			'exclude'        => $params->excluded,
		];


		if ( 'none' !== $instance['exclude_posts_in_taxonomy'] ) {
			$args['tax_query'] = [
				'relation' => 'AND'
			];
		} else {
			$args['tax_query'] = [];
		}

		$args['tax_query'] = array_merge(
			$args['tax_query'],
			[
				[
					'taxonomy' => 'post_tag',
					'field'    => 'id',
					'terms'    => $params->tags,
					'operator' => 'IN'
				]
			]
		);

		if ( 'none' !== $instance['exclude_posts_in_taxonomy'] ) {

			$args['tax_query'] = array_merge(
				$args['tax_query'],
				[
					[
						'taxonomy' => $instance['exclude_posts_in_taxonomy'],
						'operator' => 'NOT EXISTS',
					]
				]
			);

		}


		$posts = get_posts( $args );

		if ( 0 < count( $posts ) ):
			echo $args['before_widget'];
			?>
            <div class="ml-recommendation__wrapper">
                <div class="ml-recommendation__wrap">
					<?php if ( ! empty( $title ) ): ?>
                        <div class="ml-recommendation__title">
                            <h2><?php echo $title; ?></h2>
                        </div>
					<?php endif; ?>
					<?php

					foreach ( $posts as $post ):?>
						<?php echo apply_filters(
							'MlRecommendationBlock__post_item_template',
							$this->template( $post, $image_size ),
							$post,
							$image_size
						); ?>
					<?php endforeach; ?>
                </div>
            </div>
			<?php
			echo $args['after_widget'];
		endif;
	}

	private function template( $post, $image_size ) {
		ob_start();
		?>
        <div class="recommendation__post-wrapper">
            <div class="recommendation__post-wrap">
                <a href="<?php echo get_permalink( $post ); ?>" class="recommendation__post-link">
                    <div class="recommendation__post-image">
						<?php echo get_the_post_thumbnail( $post, $image_size ); ?>
                    </div>

                    <div class="recommendation__post-title">
						<?php echo get_the_title( $post ); ?>
                    </div>
                </a>
            </div>
        </div>
		<?php
		$res = ob_get_contents();
		ob_end_clean();

		return $res;
	}

	public function form( $instance ) {

		$instance = $this->validate( $instance );
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
				<?php _e( 'Last days:', 'MlRecommendationBlock' ); ?>
            </label>
            <input id="<?php echo $this->get_field_id( 'last_days' ); ?>"
                   name="<?php echo $this->get_field_name( 'last_days' ); ?>"
                   type="number" value="<?php echo $instance['last_days']; ?>"
                   min="1"
                   size="3"/>
        </p>

        <p>
            <label for="<?php echo $this->get_field_id( 'exclude_posts_in_taxonomy' ); ?>">
				<?php _e( 'Exclude Taxonomy:', 'MlRecommendationBlock' ); ?>
                <select name="<?php echo $this->get_field_name( 'exclude_posts_in_taxonomy' ); ?>">

                    <option value="none" <?php selected( $instance['exclude_posts_in_taxonomy'], 'none', true ); ?> >
						<?php _e( 'none', 'MlRecommendationBlock' ); ?>
                    </option>

					<?php foreach ( $this->taxonomy_helper() as $item ) : ?>
                        <option
                                value="<?php echo $item; ?>"
							<?php selected( $instance['exclude_posts_in_taxonomy'], $item, true ); ?>
                        ><?php echo $item; ?></option>
					<?php endforeach; ?>

                </select>
            </label>
        </p>

		<?php
	}

	public function update( $new_instance, $old_instance ) {

		$instance = $this->validate( $new_instance );

		return $instance;
	}

	private function taxonomy_helper() {

		$taxonomies         = get_taxonomies();
		$taxonomies_default = apply_filters(
			'MlRecommendationBlock__exclude_default_taxonomies',
			[
				'category',
				'post_tag',
				'nav_menu',
				'link_category',
				'post_format'
			]
		);

		$diff = array_diff( $taxonomies, $taxonomies_default );

		if ( empty( $diff ) ) {
			return false;
		}

		return array_keys( $diff );
	}

	private function validate( $array ) {

		$array = array_map( 'trim', $array );

		array_walk( $array, function ( $v, $k ) use ( &$array ) {

			if ( 'title' === $k ) {
				$array[ $k ] = ( ! empty( $v ) ) ? strip_tags( $v ) : '';
			} else if ( 'last_days' === $k ) {
				$array[ $k ] = ( ! empty( $v ) && is_numeric( $v ) ) ? intval( $v ) : 3;
			} else if ( 'posts_per_page' === $k ) {
				$array[ $k ] = ( ! empty( $v ) && is_numeric( $v ) ) ? intval( $v ) : 8;
			} else if ( 'exclude_posts_in_taxonomy' === $k ) {
				$array[ $k ] = ( ! empty( $v ) ) ? strip_tags( $v ) : 'none';
			}

		} );

		return $array;
	}

}