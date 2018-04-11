<?php

namespace MlRecommendationBlock\Classes;

class QueryParams
{
    public $excluded = [];

    public $tags = [];

    public $date_qyery = [];

    public function __construct($days)
    {
        $this->excluded();

        $this->date_offset($days);

        if (is_singular()) {
            // если страница записей то получаем ее теги
            $this->tags($GLOBALS['wp_query']->queried_object_id);
        } else {
            // если если главная страница то получаем теги только последней записи
            // в противном случае их может быть очень много
            // теперь виджет можно выводить и на главной странице
            $posts = $GLOBALS['wp_query']->posts;
            $this->tags(array_pop($posts)->ID);
        }
    }

    private function date_offset($days = 3)
    {
        $this->date_qyery = [
            'after' => "{$days} day ago"
        ];

    }

    private function excluded()
    {
        // Получаем id использованые в текущем цикле что-бы одним махом усключить их из вывода
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