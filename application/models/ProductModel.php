<?php

namespace Moredeal\application\models;

use Moredeal\application\components\MoredealProduct;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;

/**
 * ProductModel class file
 */
class ProductModel extends Model {

	const TRANSIENT_LAST_SYNC_DATE = 'moredeal_products_last_sync';

	const PRODUCTS_TTL = 3600;

	/**
	 * @return string
	 */
	public function tableName(): string {
		return $this->getDb()->prefix . 'moredeal_product';
	}

	/**
	 * @return string
	 */
	public function getDump(): string {
		return "CREATE TABLE " . $this->tableName() . " (
		 `id` int unsigned NOT NULL AUTO_INCREMENT,
		  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
		  `module_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
		  `meta_id` bigint unsigned DEFAULT NULL,
		  `post_id` bigint unsigned DEFAULT '0',
		  `create_date` datetime DEFAULT NULL,
		  `last_update` datetime DEFAULT NULL,
		  `stock_status` tinyint(1) DEFAULT '0',
		  `last_in_stock` datetime DEFAULT NULL,
		  `price` float(12,2) DEFAULT NULL,
		  `price_old` float(12,2) DEFAULT NULL,
		  `currency_code` char(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
		  `img` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
		  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
		  PRIMARY KEY (`id`),
		  KEY `uid` (`code`(80),`module_id`(30)),
		  KEY `post_id` (`post_id`)
		) $this->charset_collate;";
	}

	/**
	 * model
	 */
	public static function model( $className = __CLASS__ ) {
		return parent::model( $className );
	}

	/**
	 * attributeLabels
	 */
	public function attributeLabels(): array {
		return array(
			'id'           => 'ID',
			'title'        => __( 'Title', 'moredeal' ),
			'stock_status' => __( 'Stock', 'moredeal' ),
			'price'        => __( 'Price', 'moredeal' ),
		);
	}

	public function getProducts( $module_id) {
		$sql      = "SELECT * FROM " . $this->tableName() . " WHERE module_id = %s ORDER BY last_update DESC";
		$sql      = $this->getDb()->prepare( $sql, $module_id );
		$results  = $this->getDb()->get_results( $sql, ARRAY_A );
		return $results;
	}

	public function findProductsByPostId( $post_id) {
		$sql      = "SELECT * FROM " . $this->tableName() . " WHERE post_id = %s ORDER BY last_update DESC";
		$sql      = $this->getDb()->prepare( $sql, $post_id );
		$results  = $this->getDb()->get_results( $sql, ARRAY_A );
		return $results;
	}

	/**
	 *
	 * 更新商品信息
	 *
	 * @param $item
	 *
	 * @return void
	 */
	public function update( $item ) {
		$this->getDb()->update( $this->tableName(), $item, array( 'id' => $item['id'] ) );
	}

	/**
	 * 根据商品 Code 批量更新
	 *
	 * @param $date
	 *
	 * @return void
	 */
	public function batchUpdateByCodes( $date ) {
		$this->batchUpdate( $date, 'code' );
	}

	/**
	 * 批量更新
	 *
	 * @param $data
	 * @param $field
	 * @param array $params
	 *
	 * @return bool|int
	 */
	public function batchUpdate( $data, $field, array $params = [] ) {
		if ( ! is_array( $data ) || empty( $data ) || ! $field || ! is_array( $params ) ) {
			return false;
		}
		$updates = $this->parseUpdate( $data, $field );
		$where   = $this->parseParams( $params );
		// 获取所有键名为$field列的值，值两边加上单引号，保存在$fields数组中
		$fields = array_column( $data, $field );
		$fields = implode( ',', array_map( function ( $value ) {
			return "'" . $value . "'";
		}, $fields ) );
		$sql    = sprintf( "UPDATE `%s` SET %s WHERE `%s` IN (%s) %s", $this->tableName(), $updates, $field, $fields, $where );
		return $this->getDb()->query( $sql );
	}

	/**
	 * 解析更新字段
	 *
	 * @param $data
	 * @param $field
	 *
	 * @return string
	 */
	function parseUpdate( $data, $field ): string {
		$sql  = '';
		$keys = array_keys( current( $data ) );
		foreach ( $keys as $column ) {
			if ( $column == $field ) {
				continue;
			}
			$sql .= sprintf( "`%s` = CASE `%s` ", $column, $field );
			foreach ( $data as $line ) {
				$sql .= sprintf( "WHEN '%s' THEN '%s' ", $line[ $field ], $line[ $column ] );
			}
			$sql .= "END, ";
		}

		return rtrim( $sql, ', ' );
	}

	/**
	 * 解析参数
	 *
	 * @param $params
	 *
	 * @return string
	 */
	function parseParams( $params ): string {
		$where = [];
		foreach ( $params as $key => $value ) {
			$where[] = sprintf( "`%s` = '%s'", $key, $value );
		}

		return $where ? ' AND ' . implode( ' AND ', $where ) : '';
	}

	/**
	 * 获取最后同步时间
	 * @return int
	 */
	public function getLastSync(): int {
		return get_transient( self::TRANSIENT_LAST_SYNC_DATE );
	}

	/**
	 * 更新最后同步时间
	 * @return void
	 */
	public function setLastSync() {
		set_transient( self::TRANSIENT_LAST_SYNC_DATE, time(), self::PRODUCTS_TTL );
	}

	/**
	 * @return array
	 */
	static public function getStockStatuses(): array {
		return array(
			MoredealProduct::STOCK_STATUS_IN_STOCK     => __( 'In stock', 'moredeal' ),
			MoredealProduct::STOCK_STATUS_OUT_OF_STOCK => __( 'Out of stock', 'moredeal' ),
			MoredealProduct::STOCK_STATUS_UNKNOWN      => __( 'Unknown', 'moredeal' ),
		);
	}

	static public function getStock(): array {
		return array(
			MoredealProduct::STOCK_STATUS_IN_STOCK     => __( 'In stock', 'moredeal' ),
			MoredealProduct::STOCK_STATUS_OUT_OF_STOCK => __( 'Out of stock', 'moredeal' ),
			MoredealProduct::STOCK_STATUS_UNKNOWN      => __( 'Unknown', 'moredeal' ),
		);
	}

}
