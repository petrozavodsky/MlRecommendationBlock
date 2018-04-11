<?php

namespace MlRecommendationBlock\Classes;

class QueryParams {
	public $excluded = [];

	public $tags = [];

	public $date_qyery = [];

	public function __construct( $days ) {
		$this->excluded();
		$this->date_offset( $days );
		if ( is_singular() ) {
			$this->tags( $GLOBALS['wp_query']->queried_object_id );
		} else {
			$this->tags( $GLOBALS['wp_query']->posts[0]->ID );
		}
	}

	private function date_offset( $days = 3 ) {
		$this->date_qyery = [
			[
				'after' => "{$days} day ago"
			]
		];

	}

	private function excluded() {
		$this->excluded = array_map(
			function ( $elem ) {
				return $elem->ID;
			},
			$GLOBALS['wp_query']->posts
		);
	}

	private function tags( $post_id ) {
		$this->tags = wp_get_post_tags( $post_id, [ 'fields' => 'ids' ] );
	}

}