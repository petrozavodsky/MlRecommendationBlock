<?php

namespace MlRecommendationBlock\Widgets;

use MlRecommendationBlock;
use MlRecommendationBlock\Classes\MetaBox;
use MlRecommendationBlock\Classes\QueryParams;
use MlRecommendationBlock\Utils\WidgetHelper;
use WP_Widget;

class WidgetRecommendation extends WP_Widget {
	use WidgetHelper;

	public $css = true;
	private $suffix = " - ML widget";

	function __construct() {
		$className = get_called_class();
		$className = str_replace( "\\", '-', $className );

		$this->addWidgetAssets();


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


		$s_posts = $this->get_posts( $post_types, $instance['posts_per_page'], $instance['last_days'], $instance['exclude_posts_in_taxonomy'] );

		$p_include = $this->get_posts_imclude( $post_types, $instance['posts_per_page'], $instance['last_days'] );

		if ( ! empty( $p_include ) ) {
			$f_posts = array_merge( $p_include, $s_posts );
			$f_posts = array_slice( $f_posts, 0, $instance['posts_per_page'] );
		} else {
			$f_posts = $s_posts;
		}

		if ( 0 < count( $f_posts ) ):
			echo $args['before_widget'];
			?>
            <div class="ml-recommendation__wrapper">
                <div class="ml-recommendation__wrap">
					<?php if ( ! empty( $title ) ): ?>
                        <div class="ml-recommendation__title">
                            <h2><?php echo $title; ?></h2>
                        </div>
					<?php endif; ?>
                    <div class="ml-recommendation__posts-wrap">
						<?php foreach ( $f_posts as $post ): ?>
							<?php echo apply_filters(
								'MlRecommendationBlock__post_item_template',
								$this->template( $post, $image_size ),
								$post,
								$image_size
							); ?>
						<?php endforeach; ?>
                    </div>
                </div>
            </div>
			<?php
			echo $args['after_widget'];
		endif;
	}

	private function get_posts_imclude( $post_types, $posts_per_page, $last_days ) {
		$params = new QueryParams( $last_days );

		$args = [
			'posts_per_page' => $posts_per_page,
			'post_type'      => $post_types,
			'exclude'        => $params->excluded,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => MetaBox::$form_attr_name . '_flag',
					'value'   => 'include',
					'compare' => '='
				]
			]
		];

		return get_posts( $args );
	}

	private function get_posts( $post_types, $posts_per_page, $last_days, $exclude_tax ) {
		$params = new QueryParams( $last_days );

		$args = [
			'posts_per_page' => $posts_per_page,
			'post_type'      => $post_types,
			'exclude'        => $params->excluded,
			'meta_query'     => [
				'relation' => 'AND',
				[
					'key'     => MetaBox::$form_attr_name . '_flag',
					'value'   => 'exclude',
					'compare' => '!='
				],
				[
					'key'     => MetaBox::$form_attr_name . '_flag',
					'value'   => 'include',
					'compare' => '!='
				]
			]
		];

		if ( 'none' !== $exclude_tax ) {
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

		if ( 'none' !== $exclude_tax ) {

			$args['tax_query'] = array_merge(
				$args['tax_query'],
				[
					[
						'taxonomy' => $exclude_tax,
						'operator' => 'NOT EXISTS',
					]
				]
			);

		}

		return get_posts( $args );
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