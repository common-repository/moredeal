<?php

namespace Moredeal\application\scheduler;

use Moredeal\application\components\ModuleManager;
use Moredeal\application\components\MoredealManager;
use Moredeal\application\models\ProductModel;

defined( '\ABSPATH' ) || exit;

/**
 * ProductUpdateScheduler class file
 */
class ProductUpdateScheduler extends MoredealScheduler {

	const CRON_TAG = 'moredeal_product_update_scheduler';

	/**
	 * cron tag
	 * @return string
	 */
	public static function getCronTag(): string {
		return self::CRON_TAG;
	}

	/**
	 * 更新商品定时任务
	 * @return void
	 */
	public static function execute() {
		@set_time_limit( 3000 );
		// 更新商品
		self::updateProducts();
	}

	private static function updateProducts(): void {

		error_log( "scheduler begin: run update product" );
		try {
			// 获取所有可以更新商品的模块
			$module_ids = ModuleManager::getInstance()->getUpdateModuleIds();
			if ( ! $module_ids ) {
				error_log( "scheduler end : no module products need update" );

				return;
			}
			shuffle( $module_ids );
			$time = time();
			foreach ( $module_ids as $module_id ) {


				$module = ModuleManager::getInstance()->factory( $module_id );

				$lastSync = self::getModuleLastSync( $module_id );

				$ttl = $module->config( 'ttl_items' );

				// 如果上次更新时间小于当前时间减去ttl，则更新
				if ( ! $lastSync || ( $time - $ttl > $lastSync ) ) {
					MoredealManager::getInstance()->updateProducts( array( 'module_id' => $module_id ) );
					self::setModuleLastSync( $module_id );
					error_log( 'scheduler update product; module:  ' . $module_id . '  time: ' . date_i18n( 'Y-m-d H:i:s' ) );
				}

			}
		} catch ( \Exception $e ) {
			new \WP_Error( 'moredeal update product error', $e->getMessage() );

			return;
		}
		error_log( "scheduler end: run update product" );
	}

	/**
	 *
	 * @param $module_id
	 *
	 * @return string
	 */
	public static function moduleLastSyncPrefix( $module_id ): string {
		return ProductModel::TRANSIENT_LAST_SYNC_DATE . '_' . $module_id;
	}

	public static function getModuleLastSync( $module_id ) {
		return get_transient( self::moduleLastSyncPrefix( $module_id ) );
	}

	public static function setModuleLastSync( $module_id ) {
		set_transient( self::moduleLastSyncPrefix( $module_id ), time(), 3600 );
	}


}
