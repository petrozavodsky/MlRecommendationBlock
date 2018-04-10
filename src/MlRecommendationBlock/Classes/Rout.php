<?php

namespace MlRecommendationBlock\Classes;

class Rout
{
    public $excluded = [];

    public function __construct()
    {
        $this->excluded();
        d($this->excluded);
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
}