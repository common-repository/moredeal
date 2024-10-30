<?php

namespace Moredeal\application\admin;
defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\MoredealManager;
use Moredeal\application\models\ProductModel;

defined('\ABSPATH') || exit;

class BeforeDeletePost {

	/**
	 * @var BeforeDeletePost|null 实例对象
	 */
	private static ?BeforeDeletePost $instance = null;

	/**
	 * 获取单例
	 * @return BeforeDeletePost|null
	 */
	public static function getInstance(): ?BeforeDeletePost {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	private function __construct() {
		add_action('before_delete_post', array($this, 'delete_product')); //删除文章
	}

	public function delete_product($post_id) {
		global $wpdb;
		$resource = $wpdb->get_var( $wpdb->prepare( "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME =%s", ProductModel::model()->tableName() ) );
		if ( $resource == ProductModel::model()->tableName() ) {
			MoredealManager::getInstance()->deleteProductData($post_id);
		}
	}

}