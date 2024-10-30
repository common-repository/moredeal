<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\helpers\TextHelper;
use Moredeal\application\models\ProductModel;

/**
 * MoredealMetaBox class file
 */
class MoredealManager {

	/**
	 * 元数据 key
	 */
	const META_PREFIX_DATA = 'moredeal_metadata_key';

	/**
	 * 元数据更新时间 key
	 */
	const META_PREFIX_LAST_ITEMS_UPDATE = 'moredeal_last_update_key';

	/**
	 * 商品更新 Limit
	 */
	const PRODUCT_UPDATE_LIMIT = 100;

	/**
	 * 单例实例
	 * @var MoredealManager|null $instance 单例实例
	 */
	private static ?MoredealManager $instance = null;

	/**
	 * 获取单例实例
	 * @return MoredealManager|null
	 */
	public static function getInstance(): ?MoredealManager {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 保存文章时候把商品保存到 post meta 中
	 *
	 * @param int $post_id 文章 id
	 * @param null $content
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function saveMeta( int $post_id, $content = null ) {

		/**
		 * 流程
		 * 1，接受提交中到必要信息，包括 选择了哪些商品，通过 POST 直接提交
		 * 2，接收参数处理，保存到 meta 中
		 *   2.1 需要存到 meta 中的数据
		 *   2.2 需要存到 product 中的数据，商品 code，商品价格，商品老价格，商品更新时间，metadata_id, post_id
		 */
		if ( ! $content ) {
			$content = MoredealManager::getContent();
		}
		// 数据预处理
		$content = MoredealManager::prepare( json_decode( $content ) );

		// 最后更新时间更新，查询到更新，否则新增
		$meta = get_post_meta( $post_id, self::META_PREFIX_DATA, true );
		if ( $meta ) {
			update_post_meta( $post_id, self::META_PREFIX_DATA, $content );
		} else {
			add_post_meta( $post_id, self::META_PREFIX_DATA, $content );
		}
		// 获取 meta_id
		$meta_id = $this->getPostMetaId( $post_id, self::META_PREFIX_DATA );
		// 先删除该 post_id 下面的所有商品
		$this->deleteProductData( $post_id );
		$moduleList = $content->modules ?? array();
		// 处理并且添加到商品数据表中
		foreach ( $moduleList as $module ) {
			// 模块
			$moduleCode = $module->module ?? "";
			// 商品列表
			$productList = $module->products ?? array();

			// 重新添加商品
			$this->saveProductData( $post_id, $meta_id, $moduleCode, $productList );
		}

		// 更新最新文章更新时间
		$time = time();
		if ( get_post_meta( $post_id, self::META_PREFIX_LAST_ITEMS_UPDATE, true ) ) {
			update_post_meta( $post_id, self::META_PREFIX_LAST_ITEMS_UPDATE, $time );
		} else {
			add_post_meta( $post_id, self::META_PREFIX_LAST_ITEMS_UPDATE, $time );
		}
	}

	/**
	 * 获取合并之后的 MetaBox 数据
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	public function obtainMetaBox( $post_id ) {
		$content  = json_decode( MoredealManager::getContent() );
		$postMeta = get_post_meta( $post_id, MoredealManager::META_PREFIX_DATA, true );
		if ( $postMeta ) {
			$content = $postMeta;
		}
		// 获取商品数据
		$products = $this->selectProductData( $post_id ) ?? array();
		// 合并数据
		return MoredealManager::mergeProductData( $content, $products );
	}

	/**
	 * 获取 meta_id
	 *
	 * @param int $post_id 文章 id
	 * @param string $meta_key meta key
	 *
	 * @return int|null
	 */
	public function getPostMetaId( int $post_id, string $meta_key ): ?int {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT meta_id FROM $wpdb->postmeta WHERE post_id = %d AND meta_key = %s", $post_id, $meta_key ) );
	}

	/**
	 * 查询商品数据
	 *
	 * @param int $post_id 文章 id
	 *
	 * @return array
	 */
	public function selectProductData( int $post_id ): array {
		return ProductModel::model()->findProductsByPostId( $post_id );
	}

	/**
	 * 删除 post_id 下面的所有商品
	 *
	 * @param int $post_id 文章 id
	 *
	 * @return void
	 */
	public function deleteProductData( int $post_id ): void {
		ProductModel::model()->deleteAll( 'post_id = ' . $post_id );
	}

	/**
	 * 添加
	 *
	 * @param int $post_id 文章 id
	 * @param int $meta_id meta id
	 * @param string $module 模块
	 * @param array $productList 商品列表
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function saveProductData( int $post_id, int $meta_id, string $module, array $productList ) {
		$products = array();

		foreach ( $productList as $index => $item ) {
			$item                    = (array) $item;
			$product                 = array();
			$product['post_id']      = $post_id;
			$product['meta_id']      = $meta_id;
//			$product['module_id']    = $item['source'];
			$product['module_id']    = "Amazon";
			$product['code']         = $item['code'];
			$product['title']        = $item['title'];
			$product['url']          = self::handlerUrl( $item['url'] );
			$product['img']          = $item['picUrl'];
			$product['price']        = $item['price'];
			$product['last_update']  = $item['changeTime'];
			$product['stock_status'] = self::getStockStatus( $item['stock'] );
			$product['create_date']  = current_time( 'mysql' );
			$products[ $index ]      = $product;
		}
		ProductModel::model()->multipleInsert( $products );
	}

	/**
	 * 批量更新商品
	 * @return void
	 */
	public function updateProducts($params = array()) {
		// 获取所有的商品
		$products = ProductModel::model()->getProducts( $params['module_id'] );
		error_log( 'Moredeal Products count:  ' . count( $products ) );
		$limit = ceil( count( $products ) / self::PRODUCT_UPDATE_LIMIT );

		for ( $i = 0; $i < $limit; $i ++ ) {
			$offset        = $i * self::PRODUCT_UPDATE_LIMIT;
			$limitProducts = array_slice( $products, $offset, self::PRODUCT_UPDATE_LIMIT );
			// 组装请求数据
			$request      = $this->buildSearchRequest( $limitProducts, self::PRODUCT_UPDATE_LIMIT );
			$lastProducts = array();
//			if ( $params['module_id'] == 'AmazonNoApi' ) {
//				$lastProducts = $this->searchProductFromAdSystemLastData( $request );
//			} else {
//				// 获取最新商品数据
//				$lastProducts = $this->searchProductLastData( $request );
//			}
			// 目前统一使用 AdSystem更新商品
			$lastProducts = $this->searchProductFromAdSystemLastData( $request );
			// 组装商品数据
			$mergeProducts = $this->mergeUpdateProduct( $limitProducts, $lastProducts );
			error_log( 'Need UpdateProducts  count:  ' . count( $mergeProducts ) );
			// 批量更新商品数据
			if ( ! empty( $mergeProducts ) ) {
				ProductModel::model()->batchUpdateByCodes( $mergeProducts );
			}
		}
	}

	/**
	 * 构造请求参数
	 *
	 * @param $products
	 * @param int $pageSize
	 *
	 * @return object
	 */
	public function buildSearchRequest( $products, int $pageSize = 100 ): object {
		$codes = array();
		foreach ( $products as $product ) {
			$codes[] = $product['code'];
		}
		$codesList = array_values( array_unique( $codes ) );
		error_log( 'Moredeal Codes count:  ' . count( $codesList ) );
		$request = array(
			'page'          => array(
				'page'     => 1,
				'pageSize' => $pageSize,
			),
			'productSearch' => array(
				'productCodes' => $codesList,
			),
			'marketplace'   => ModuleManager::getInstance()->AmazonModule()->getMarketplace(),
		);

		return (object) $request;
	}

	/**
	 * 根据 code 获取商品最新数据
	 *
	 * @param $request
	 *
	 * @return array|\WP_Error
	 */
	public function searchProductLastData( $request ) {
		try {
			$result = SearchProductClient::getInstance()->restPost( '/search', json_encode( $request ) );
			$result = json_decode( $result, true );
			$result = (array) $result;
			if ( $result['code'] != 200 || $result['code'] != 201 ) {
				return array();
			}

			if ( ! $result['success'] ) {
				return array();
			}
			if ( empty( $result['records'] ) ) {
				return array();
			}
			error_log( 'searchProductLastData  count:  ' . count( $result['records'] ) );

			return $result['records'];
		} catch ( \Exception $e ) {
			return new \WP_Error( 'moredeal search products error', $e->getMessage() );
		}
	}

	/**
	 * 根据 code 获取商品最新数据
	 *
	 * @param $request
	 *
	 * @return array|\WP_Error
	 */
	public function searchProductFromAdSystemLastData( $request ) {
		try {
			$result = SearchProductClient::getInstance()->searchFromAdSystem( json_encode( $request ) );
			$result = (array) $result;

			if ( !in_array('success', $result) || ! $result['success'] ) {
				return array();
			}
			if (!in_array('data', $result) || empty( $result['data'] ) ) {
				return array();
			}
			error_log( 'AdSystem Products  count:  ' . count( $result['data'] ) );

			return $result['data'];
		} catch ( \Exception $e ) {
			return new \WP_Error( 'moredeal search products error', $e->getMessage() );
		}
	}

	/**
	 * 组装需要更新的商品数据
	 *
	 * @param $products
	 * @param $lastProducts
	 *
	 * @return array
	 */
	public function mergeUpdateProduct( $products, $lastProducts ): array {
		if ( empty( $products ) || empty( $lastProducts ) ) {
			return array();
		}
		$mergeProducts = array();
		foreach ( $products as $index => $product ) {
			foreach ( $lastProducts as $idx => $lastProduct ) {
				$lastProduct = (array) $lastProduct;
				error_log( 'lastProduct:  ' . json_encode( $lastProduct ) );
				if ( $product['code'] == $lastProduct['code'] ) {
					$mergeProduct                 = array();
					$mergeProduct['code']         = $product['code'];
					$mergeProduct['stock_status'] = self::getStockStatus( $lastProduct['stock'] );
					$mergeProduct['create_date']  = current_time( 'mysql' );
					if (array_key_exists('changeTime', $lastProduct)) {
						$mergeProduct['last_update']  = $lastProduct['changeTime'];
					} else {
						$mergeProduct['last_update']  = date( 'Y-m-d H:i:s', time() );
					}

					if (array_key_exists('price', $lastProduct) && $lastProduct['price']) {
						$mergeProduct['price'] = $lastProduct['price'];
						if ( $product['price'] != $lastProduct['price'] ) {
							$mergeProduct['price_old'] = $product['price'];
						} else {
							if (array_key_exists('price_old', $product) && $product['price_old']) {
								$mergeProduct['price_old'] = $product['price_old'];
							} else {
								$mergeProduct['price_old'] = null;
							}
						}
					} else {
						$mergeProduct['price'] = $product['price'];
						if (array_key_exists('price_old', $product) && $product['price_old']) {
							$mergeProduct['price_old'] = $product['price_old'];
						} else {
							$mergeProduct['price_old'] = null;
						}
					}
					$mergeProducts[] = $mergeProduct;
				}
			}
		}
		return $mergeProducts;
	}

	/**
	 * 合并商品表和元数据表的数据
	 *
	 * @param $content
	 * @param $products
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	private static function mergeProductData( $content, $products ) {
		$moduleList = $content->modules ?? array();
		if ( empty( $moduleList ) ) {
			return $content;
		}
		foreach ( $moduleList as $index => $module ) {
			foreach ( $module as $key => $value ) {
				if ( $key == 'products' ) {
					$module->products = MoredealManager::merge( $value, $products );
				}
			}
			$moduleList[ $index ] = $module;
		}
		$content->modules = $moduleList;

		return $content;
	}

	/**
	 * 合并商品表和元数据表的数据
	 *
	 * @param $metaProducts
	 * @param $products
	 *
	 * @return array
	 * @throws \Exception
	 */
	private static function merge( $metaProducts, $products ): array {
		if ( empty( $metaProducts ) ) {
			return array();
		}
		foreach ( $metaProducts as $metaIndex => $metaProduct ) {
			$metaProduct = (array) $metaProduct;
			foreach ( $products ?? array() as $product ) {
				if ( $metaProduct['code'] == $product['code'] ) {
					$metaProduct['price']        = $product['price'];
					$metaProduct['priceOld']     = self::getPriceOld( $product['price_old'] );
					$metaProduct['changeTime']   = $product['last_update'];
					$metaProduct['stock_status'] = $product['stock_status'];
					$metaProduct['url']          = self::getAssociateTagUrl( $metaProduct['url'], $product['module_id'] );
					$metaProduct['module_id']    = $product['module_id'];
					if ( ! TextHelper::isHtmlTagDetected( $metaProduct['description'] ) ) {
						$metaProduct['description'] = TextHelper::br2nl( $metaProduct['description'] );
					}
				}
				$metaProducts[ $metaIndex ] = $metaProduct;
			}
		}
		return $metaProducts;
	}

	/**
	 * 数据预处理
	 *
	 * @param mixed $content box数据
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	private static function prepare( $content ) {
		$moduleList = $content->modules ?? array();
		foreach ( $moduleList as $index => $module ) {
			foreach ( $module as $key => $value ) {
				if ( $key == 'products' ) {
					$module->products = MoredealManager::dataPrepare( $value ?? array() );
				}
			}
			$moduleList[ $index ] = $module;
		}
		$content->modules = $moduleList;

		return $content;
	}

	/**
	 * 数据预处理
	 *
	 * @param array $data 商品数据
	 *
	 * @return array
	 * @throws \Exception
	 */
	private static function dataPrepare( array $data ): array {
		foreach ( $data as $index => $product ) {
			foreach ( $product as $key => $value ) {
				if ( $key == 'price' ) {
					$product->price = (float) $value;
				}
				if ( $key == 'url' ) {
					$product->url = self::handlerUrl( $value );
				}
				if ( $key == 'description' && TextHelper::isHtmlTagDetected( $value ) ) {
					$product->description = TextHelper::nl2br( $value );
				}
				$data[ $index ] = $product;
			}
		}

		return $data;
	}

	/**
	 * 初始化 metaBox 元数据。现在只有一个场景，先这样写，后面有其他场景再改
	 * @return string
	 */
	public static function getContent(): string {
//		$list = ModuleManager::getInstance()->getModulesIdList( true );
//		foreach ( $list as $index => $value ) {
//			$list[ $index ] = array(
//				'module'   => $value,
//				'products' => array(),
//			);
//		}
		$list = array(
			0 => array(
				'module'   => 'Selected',
				'products' => array(),
			),
		);
		return '{"modules":' . json_encode( $list ) . '}';
	}

	/**
	 * 获取商品url
	 * @throws \Exception
	 */
	public static function getAssociateTagUrl( $url, $module_id ): string {
		$module = ModuleManager::factory( 'Amazon' );
		$tag = $module->getUrlAssociateTagParam();
		if (str_ends_with($url, $tag)) {
			return $url;
		}
		return $url . $tag;
	}

	/**
	 * 处理 URL
	 * @param $url
	 *
	 * @return string
	 * @throws \Exception
	 */
	public static function handlerUrl( $url ): string {
		if (!str_contains($url, '?')) {
			return $url;
		}
		$str_split = explode( "?", $url );
		return $str_split[0];
	}

	/**
	 * @param $priceOld
	 *
	 * @return float|null
	 */
	public static function getPriceOld( $priceOld ): ?float {
		if ( ! $priceOld ) {
			return null;
		}
		if ( $priceOld == 0.0 || $priceOld == 0 ) {
			return null;
		}

		return (float) $priceOld;
	}

	/**
	 * @param $stock
	 *
	 * @return int
	 */
	public static function getStockStatus( $stock ): int {
		$stock_status = MoredealProduct::STOCK_STATUS_UNKNOWN;

		if ( $stock === true ) {
			$stock_status = MoredealProduct::STOCK_STATUS_IN_STOCK;
		} else if ( $stock === false ) {
			$stock_status = MoredealProduct::STOCK_STATUS_OUT_OF_STOCK;
		}

		return $stock_status;
	}

	/**
	 * 获取整体模版
	 * @return array|bool
	 */
	public static function getTemplate() {
		$list      = BlockTemplateManager::getInstance()->getTemplatesList();

		$templates = array();
		foreach ( $list as $index => $item ) {
			if ($index == 'block_search') {
				continue;
			}
			$templates[] = array(
				'code'  => substr( $index, strlen( BlockTemplateManager::TEMPLATE_PREFIX ) ),
				'label' => __($index, 'moredeal'),
			);
		}
		return $templates;
	}

	public static function getTemplateDown() {
		$list      = BlockTemplateManager::getInstance()->getTemplatesList();

		$templates = array();
		foreach ( $list as $index => $item ) {
			$str      = substr( $index, strlen( BlockTemplateManager::TEMPLATE_PREFIX ) );
			if ($str == 'search') {
				continue;
			}
			$templates[$str] =  __($index, 'moredeal');
		}
		return $templates;
	}

	/**
	 * 获取渲染魔板类型数据
	 * @return array
	 */
	public static function getModuleTemplate(): array {
		return array(
			array(
				'code'  => 'list',
				'label' => 'List'
			),
			array(
				'code'  => 'item',
				'label' => 'Item'
			),
		);

	}

}