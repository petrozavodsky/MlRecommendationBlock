<?php

namespace MlRecommendationBlock\Classes;

class QueryParams
{
    public $excluded = [];

    public $tags = [];

    public function __construct()
    {
        $this->excluded();
        $this->date_offset();
        if (is_singular()) {
            $this->tags($GLOBALS['wp_query']->queried_object_id);
        }

    }

    private function excluded()
    {
        $this->excluded = array_map(
            function ($elem) {
                return $elem->ID;
            },
            $GLOBALS['wp_query']->posts
        );
    }

    private function tags($post_id)
    {
        $this->tags = wp_get_post_tags($post_id, ['fields' => 'ids']);
    }

}