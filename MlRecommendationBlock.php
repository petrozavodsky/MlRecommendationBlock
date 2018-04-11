<?php
/*
Plugin Name: Ml Recommendation Block
Plugin URI: http://alkoweb.ru
Author: Petrozavodsky Vladimir
Author URI: http://alkoweb.ru
Requires PHP: 7.0
*/
	
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( plugin_dir_path( __FILE__ )."includes/Autoloader.php" );

use MlRecommendationBlock\Autoloader;

new Autoloader( __FILE__, 'MlRecommendationBlock' );


use MlRecommendationBlock\Base\Wrap;
use MlRecommendationBlock\Classes\MetaBox;
use MlRecommendationBlock\Utils\ActivateWidgets;

class MlRecommendationBlock extends Wrap {
	public $version = '1.0.0';
	public static $textdomine;

	function __construct() {
		self::$textdomine = $this->setTextdomain();

		new MetaBox();

		new ActivateWidgets(
			__FILE__,
			'Widgets',
			'MlRecommendationBlock'
		);


	}

}

function MlRecommendationBlock__init() {
	new MlRecommendationBlock();
}

add_action( 'plugins_loaded', 'MlRecommendationBlock__init', 30 );