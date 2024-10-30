<?php

namespace Moredeal\application\admin;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\ModuleManager;
use Moredeal\application\components\MoredealManager;
use Moredeal\application\helpers\TemplateHelper;
use Moredeal\application\models\ProductModel;
use Moredeal\application\Plugin;

/**
 * ProductController class file
 *
 */
class ProductController {

	const slug = 'moredeal-product';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'remove_http_referer' ) );

	}

	public function remove_http_referer() {
		global $pagenow;
		// If we're on an admin page with the referer passed in the QS, prevent it nesting and becoming too long.
		if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == self::slug && ! empty( $_GET['_wp_http_referer'] ) && isset( $_SERVER['REQUEST_URI'] ) ) {
			wp_safe_redirect( remove_query_arg( array(
				'_wp_http_referer',
				'_wpnonce'
			), esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) ) );
			exit;
		}
	}

	public function add_admin_menu() {
		add_submenu_page( Plugin::slug, __( 'Product', 'moredeal' ) . ' &lsaquo; Moredeal', __( 'Product', 'moredeal' ), 'manage_options', self::slug, array(
			$this,
			'actionIndex'
		) );
	}

	public function actionIndex() {
		wp_enqueue_script( 'moredeal-blockUI', \Moredeal\PLUGIN_RES . '/js/jquery.blockUI.js', array( 'jquery' ) );
		wp_enqueue_style( 'moredeal-setting', \Moredeal\PLUGIN_RES . '/css/setting.css' );

		// 更新按钮
		if (! ProductModel::model()->getLastSync() || ( isset( $_GET['action'] ) && $_GET['action'] === 'update' ) ) {
			// 获取所有可以更新商品的模块
			$module_ids = ModuleManager::getInstance()->getVisitUpdateModuleIds();
			foreach ( $module_ids as $module_id ) {
				MoredealManager::getInstance()->updateProducts(array('module_id' => $module_id));
				// 更新最新商品更新时间
				ProductModel::model()->setLastSync();
			}
		}

		// 渲染列表数据
		$table = new ProductTable( ProductModel::model() );
		$table->prepare_items();

		// 处理最后更新时间
		$last_scaned = ProductModel::model()->getLastSync();
		if ( time() - $last_scaned <= ProductModel::PRODUCTS_TTL ) {
			$last_scaned_str = sprintf( __( '%s ago', '%s = human-readable time difference', 'moredeal' ), human_time_diff( $last_scaned, time() ) );
		} else {
			$last_scaned_str = TemplateHelper::dateFormatFromGmt( date('Y-m-d H:i:s', $last_scaned) );
		}

		AdminPlugin::getInstance()->render( 'product_index', array(
			'table'           => $table,
			'last_scaned_str' => $last_scaned_str
		) );
	}


}
