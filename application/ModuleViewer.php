<?php

namespace Moredeal\application;

defined( '\ABSPATH' ) || exit;

use Exception;
use Moredeal\application\admin\GeneralConfig;
use Moredeal\application\components\BlockSearchTemplateManager;
use Moredeal\application\components\BlockTemplateManager;
use Moredeal\application\components\MoredealManager;
use Moredeal\application\components\MoredealMetaBox;
use Moredeal\application\components\MoredealProduct;
use Moredeal\application\components\SearchProductClient;

/**
 *  渲染模型
 */
require_once( "admin/MoredealMetaBox.php" );
class ModuleViewer {

	/**
	 * @var ModuleViewer|null 实例对象
	 */
	private static ?ModuleViewer $instance = null;

	/**
	 * 获取单例
	 * @return ModuleViewer|null
	 */
	public static function getInstance(): ?ModuleViewer {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	private function __construct() {
	}

	/**
	 * init
	 * @return void
	 */
	public function init() {
		// priority = 12 because do_shortcode() is registered as a default filter on 'the_content' with a priority of 11.
		add_filter( 'the_content', array( $this, 'viewData' ), 12 );
	}

	/**
	 * 渲染模型数据
	 *
	 * @param $content
	 *
	 * @return mixed
	 */
	public function viewData( $content ) {
		return $content;
	}

	/**
	 * 渲染模型数据
	 *
	 * @param int|null $post_id 文章ID
	 * @param array $params 参数
	 * @param string $content 内容
	 *
	 * @return string
	 */
	public function viewBlockData( int $post_id = null, array $params = array(), string $content = '' ): string {
		if ( ! $post_id ) {
			global $post;
			$post_id = $post->ID;
		}
		// 模版信息
		$template = $params['template'];
		// 如果是搜索模板
		if ( $template == "search_render_block" ) {
			return $this->viewBlockSearchTemplate( $post_id, $params, $content, $template );
		} else {
			return $this->viewBlockTemplate( $post_id, $params, $content, $template );
		}
	}

	/**
	 * 渲染模板 - 模板
	 *
	 * @param $post_id
	 * @param array $params
	 * @param string $content
	 * @param string $template
	 *
	 * @return string
	 */
	private function viewBlockTemplate( $post_id = null, array $params = array(), string $content = '', string $template = '' ): string {
		$blockTemplateManager = BlockTemplateManager::getInstance();
		if ( empty( $template ) || ! $blockTemplateManager->isTemplateExists( $template ) ) {
			return $content;
		}

		if ( ! $blockTemplateManager->getViewPath( $template ) ) {
			return $content;
		}

		// 获取产品列表
		$productList = $this->obtainBlockProductList( $post_id );
		if ( empty( $productList ) ) {
			return $content;
		}
		// 国际化信息
		$this->blockTemplateRegisterScripts( $template );

		// 渲染模板
		return $blockTemplateManager->render( $template, array(
			'data'             => $productList,
			'post_id'          => $post_id,
			'params'           => $params,
			'title'            => $params['title'],
			'limit'            => $params['limit'],
			'cols'             => "",
			'sort'             => $params['sort'],
			'order'            => $params['order'],
			'groups'           => $params['groups'],
			'btn_text'         => $params['btn_text'],
			'content'          => $content,
			'post_title'       => get_the_title( $post_id ),
			'post_type'        => get_post_type( $post_id ),
			'template'         => $template,
			'auth_code'        => Plugin::getAuthCode(),
			'isPro'            => Plugin::isPro(),
			'moredeal_version' => Plugin::version(),
			'wp_version'       => get_bloginfo( 'version' ),
			'wp_addr'          => SearchProductClient::getCurrDomain(),
			'is_show_more'     => true,
		) );
	}

	/**
	 * 渲染搜索模板
	 *
	 * @param $post_id
	 * @param array $params
	 * @param string $content
	 * @param string $template
	 *
	 * @return string
	 */
	private function viewBlockSearchTemplate( $post_id = null, array $params = array(), string $content = '', string $template = '' ): string {
		$blockSearchTemplateManager = BlockSearchTemplateManager::getInstance();
		if ( empty( $template ) || ! $blockSearchTemplateManager->isTemplateExists( $template ) ) {
			return $content;
		}
		if ( ! $blockSearchTemplateManager->getViewPath( $template ) ) {
			return $content;
		}
		// 国际化信息
		$localeMessage = MoredealMetabox::getInstance()->getLocaleMessage();
		// 全局脚本
		$this->searchTemplateRegisterScripts( $template );

		// 渲染模板
		return $blockSearchTemplateManager->render( $template, array(
			'post_id'          => $post_id,
			'params'           => $params,
			'content'          => $content,
			'post_title'       => get_the_title( $post_id ),
			'post_type'        => get_post_type( $post_id ),
			'template'         => $template,
			'auth_code'        => Plugin::getAuthCode(),
			'isPro'            => Plugin::isPro(),
			'moredeal_version' => Plugin::version(),
			'wp_version'       => get_bloginfo( 'version' ),
			'wp_addr'          => SearchProductClient::getCurrDomain(),
			'locale_message'   => $localeMessage,
			'is_show_more'     => false,
		) );
	}

	/**
	 * 获取商品数据
	 *
	 * @param $post_id
	 *
	 * @return array
	 */
	private function obtainBlockProductList( $post_id ): array {

		try {
			$mergeData = MoredealManager::getInstance()->obtainMetaBox( $post_id );
		} catch ( Exception $e ) {
			return array();
		}
		$moduleList  = $mergeData->modules ?? array();
		$productList = array();

		if ( empty( $moduleList ) ) {
			return array();
		}

		foreach ( $moduleList as $module ) {
			$products = $module->products ?? array();

			if ( empty( $products ) ) {
				continue;
			}

			foreach ( $products as $index => $item ) {
				$item    = (array) $item;
				$product = $this->productItem( $post_id, $module->module, $item, $index );
				if ( $product ) {
					$productList[] = $product;
				}
			}
		}

		return $productList;

	}

	/**
	 * 获取商品数据
	 *
	 * @param $post_id
	 * @param $module_id
	 * @param $item
	 * @param $index
	 *
	 * @return array|null
	 */
	public function productItem( $post_id, $module_id, $item, $index ): ?array {
		$item = $this->outOfStockProduct( $item );
		if ( ! $item ) {
			return null;
		}

		// 只选取一些必要的属性
		return array(
			'post_id'         => $post_id,
			'module_id'       => array_key_exists( 'module_id', $item ) ? $item['module_id'] : 'Amazon',
			'code'            => $item['code'],
			'title'           => array_key_exists( 'title', $item ) ? $item['title'] : '',
			'price'           => array_key_exists( 'price', $item ) ? $item['price'] : null,
			'category_id'     => array_key_exists( 'categoryId', $item ) ? $item['categoryId'] : null,
			'priceOld'        => array_key_exists( 'priceOld', $item ) ? $item['priceOld'] : null,
			'currencyCode'    => array_key_exists( 'unit', $item ) ? $item['unit'] : 'USD',
			'globalScore'     => array_key_exists( 'globalScore', $item ) ? $item['globalScore'] : 0,
			'description'     => array_key_exists( 'description', $item ) ? $item['description'] : '',
			'shippingType'    => array_key_exists( 'shippingType', $item ) ? $item['shippingType'] : null,
			'img'             => array_key_exists( 'mainImage', $item ) ? $item['mainImage'] : null,
			'picUrl'          => array_key_exists( 'picUrl', $item ) ? $item['picUrl'] : null,
			'commentCount'    => array_key_exists( 'commentCount', $item ) ? $item['commentCount'] : 0,
			'salesCount'      => array_key_exists( 'salesCount', $item ) ? $item['salesCount'] : null,
			'rating'          => array_key_exists( 'star', $item ) ? $item['star'] : null,
			'url'             => array_key_exists( 'url', $item ) ? $item['url'] : null,
			'domain'          => array_key_exists( 'source', $item ) ? $item['source'] : null,
			'lastUpdate'      => array_key_exists( 'changeTime', $item ) ? $item['changeTime'] : time(),
			'stock_status'    => array_key_exists( 'stock_status', $item ) ? $item['stock_status'] : null,
			'trace_id'        => array_key_exists( 'trace_id', $item ) ? $item['trace_id'] : null,
			'search_location' => array_key_exists( 'search_location', $item ) ? $item['search_location'] : null,
			'view_idx'        => $index + 1,
			's_id'            => array_key_exists( 'sid', $item ) ? $item['sid'] : null,
			'star'            => array_key_exists( 'star', $item ) ? $item['star'] : null,
			'firstDate'       => array_key_exists( 'firstDate', $item ) ? $item['firstDate'] : null,
			'firstRank'       => array_key_exists( 'firstRank', $item ) ? $item['firstRank'] : null,
			'firstRankName'   => array_key_exists( 'firstRankName', $item ) ? $item['firstRankName'] : null,
			'otherRank'       => array_key_exists( 'otherRank', $item ) ? $item['otherRank'] : null,
			'otherRankName'   => array_key_exists( 'otherRankName', $item ) ? $item['otherRankName'] : null,
		);
	}

	/**
	 * 无库存处理
	 *
	 * @param $item
	 *
	 * @return void|null
	 */
	public function outOfStockProduct( $item ) {
		// 无库存不显示商品或者价格
		$outOfStockProduct = GeneralConfig::getInstance()->option( 'out_of_stock_product' );
		if ( $item['stock_status'] && ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_OUT_OF_STOCK
		                                || $item['stock_status'] == MoredealProduct::STOCK_STATUS_UNKNOWN ) ) {
			if ( $outOfStockProduct == 'hide_product' ) {
				return null;
			} elseif ( $outOfStockProduct == 'hide_price' ) {
				$item['price']    = null;
				$item['priceOld'] = null;

				return $item;
			}
		}

		return $item;
	}

	/**
	 * 注册脚本
	 * @return void
	 */
	public function blockTemplateRegisterScripts( $template ) {

		// 注册样式
		wp_enqueue_style( 'moredeal-bootstrap-style' );
		wp_enqueue_style( 'moredeal-products-style' );

		// 注册脚本
		wp_enqueue_script( 'moredeal-bootstrap-script' );
		wp_enqueue_script( 'moredeal-sensors-sdk-script' );
		wp_enqueue_script( 'moredeal-sensors-init-script' );
		wp_enqueue_script( 'moredeal-view-point-script' );

		if ( in_array( $template, array( 'block_item', 'block_offers_list', 'block_top_listing' ) ) ) {
			wp_enqueue_script( 'moredeal-bootstrap-tooltip-script' );
			wp_enqueue_script( 'moredeal-bootstrap-popover-script' );
		}

	}

	/**
	 * 注册脚本
	 * @return void
	 */
	public function searchTemplateRegisterScripts( $template ) {

		// 注册脚本
		wp_enqueue_script( 'moredeal-bootstrap-tooltip-script' );
		wp_enqueue_script( 'moredeal-bootstrap-popover-script' );
		wp_enqueue_script( 'moredeal-bootstrap-script' );
		wp_enqueue_script( 'moredeal-sensors-sdk-script' );
		wp_enqueue_script( 'moredeal-sensors-init-script' );
		wp_enqueue_script( 'moredeal-view-point-script' );
		wp_enqueue_script( 'moredeal-vue-script' );
		wp_enqueue_script( 'moredeal-treeselect-script' );
		wp_enqueue_script( 'moredeal-element-ui-script' );
		wp_enqueue_script( 'moredeal-axios-script' );
		wp_localize_script( 'moredeal-metabox_rest-script', 'REST_URL', array( 'restUrl' => untrailingslashit( esc_url_raw( rest_url() ) ) ) );
		wp_enqueue_script( 'moredeal-metabox_rest-script' );
		wp_enqueue_script( 'moredeal-goods-script' );

		// 注册样式
		wp_enqueue_style( 'moredeal-bootstrap-style' );
		wp_enqueue_style( 'moredeal-products-style' );
		wp_enqueue_style( 'moredeal-card-feature-style' );
		wp_enqueue_style( 'moredeal-element-ui-style' );
		wp_enqueue_style( 'moredeal-treeselects-style' );
		wp_enqueue_style( 'moredeal-search-style' );

	}

}
