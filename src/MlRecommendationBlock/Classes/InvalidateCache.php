<?php

namespace MlRecommendationBlock\Classes;


use MlRecommendationBlock;

class InvalidateCache
{

    public function __construct()
    {
        add_action('save_post', [__CLASS__, 'invalidate'], 0, 2);
    }


    public static function invalidate($post_id, $post)
    {


        if (in_array($post->post_type, apply_filters('MlRecommendationBlock__post_types', ['post']))) {
            self::payload();
        }
    }

    public static function payload()
    {
        global $wpdb;
        $cache_field = MlRecommendationBlock::$cache_prefix;
        $wpdb->query("DELETE FROM `{$wpdb->prefix}options`WHERE `option_name` LIKE {$cache_field}");

    }

}