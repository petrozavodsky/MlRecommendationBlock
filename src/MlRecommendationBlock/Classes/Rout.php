<?php

namespace MlRecommendationBlock\Classes;

class Rout
{
    public $excluded = [];

    private $tags = [];

    public function __construct()
    {
        $this->excluded();
        if (is_singular()) {
            $this->tags($GLOBALS['wp_query']->queried_object_id);
        }

        d($this);
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