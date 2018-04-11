<?php
/*
Plugin Name: Ml Recommendation Block
Plugin URI: http://alkoweb.ru
Author: Petrozavodsky Vladimir
Author URI: http://alkoweb.ru
Description: Wordpress recommendation posts widget
Requires PHP: 7.0
*/

if (!defined('ABSPATH')) {
    exit;
}

require_once(plugin_dir_path(__FILE__) . "includes/Autoloader.php");

use MlRecommendationBlock\Autoloader;

new Autoloader(__FILE__, 'MlRecommendationBlock');


use MlRecommendationBlock\Base\Wrap;
use MlRecommendationBlock\Classes\InvalidateCache;
use MlRecommendationBlock\Classes\MetaBox;
use MlRecommendationBlock\Utils\ActivateWidgets;

class MlRecommendationBlock extends Wrap
{

    public $version = '1.0.0';
    public static $cache = true;
    public static $cache_prefix = 'MlRecommendationBlock_cache_';

    function __construct()
    {
        $this->setTextdomain();

        // Создание метабокса
        new MetaBox();

        //Инвалидатор кешей
        new InvalidateCache();

        //Автозагрузчик виджетов
        new ActivateWidgets(
            __FILE__,
            'Widgets',
            'MlRecommendationBlock'
        );

    }

}

function MlRecommendationBlock__init()
{
    new MlRecommendationBlock();
}

//инициализируем плагин
add_action('plugins_loaded', 'MlRecommendationBlock__init', 30);