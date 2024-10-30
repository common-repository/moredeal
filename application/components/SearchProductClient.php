<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

use Exception;
use Moredeal\application\admin\GeneralConfig;
use Moredeal\application\admin\HotKeywordsConfig;
use Moredeal\application\admin\LicenseConfig;
use Moredeal\application\helpers\AttributeHelper;
use Moredeal\application\helpers\TemplateHelper;
use Moredeal\application\libs\MoredealRestClient;
use Moredeal\application\ModuleViewer;
use Moredeal\application\Plugin;
use WP_REST_Request;

class SearchProductClient extends MoredealRestClient {

	const WP_AUTHORIZATION = 'License-Authorization';

	const DOMAIN = 'License-Domain';

	const DEFAULT_WP_AUTH = 'c85d43dc0c2e4a3eb41fd558397e1b51';

	const DEFAULT_WP_AUTH_SLUG = 'c85d43dc51';

	const DEFAULT_HOST = 'http://bo.moredeal.us/';

	const API_URI_SUFFIX = '/stage-api/wordpress';

	/**
	 * 单例实例
	 * @var SearchProductClient|null $instance 单例实例
	 */
	private static ?SearchProductClient $instance = null;

	/**
	 * 相应类型
	 */
	protected array $_responseTypes = array( 'json' );

	/**
	 * 获取单例实例
	 * @return SearchProductClient|null
	 */
	public static function getInstance(): ?SearchProductClient {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造函数
	 */
	private function __construct() {
		// 设置请求地址, 从通用配置中获取
		$host = GeneralConfig::getInstance()->option( 'seastar_api_host', self::DEFAULT_HOST );
		if (str_ends_with($host, '/')) {
			$host = substr( $host, 0, - 1 );
		}
		$uri = $host . self::API_URI_SUFFIX;
		parent::__construct( $uri );
		add_action( 'rest_api_init', array( $this, 'registerSearchRestRoute' ) );
		$this->setCustomHeaders( array(
			'Content-Type' => 'application/json',
			'Accept'       => 'application/json',
		) );
	}

	/**
	 * 注册路由
	 * @return void
	 */
	public function registerSearchRestRoute() {
		// 生成简码
		register_rest_route( 'seastar/v1', '/shortcode', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'generateShortcode' ),
			'permission_callback' => '__return_true',
		) );
		// 搜索条件
		register_rest_route( 'seastar/v1', '/product/conditions', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'conditions' ),
			'permission_callback' => '__return_true',
		) );
		// 选品策略
		register_rest_route( 'seastar/v1', '/product/pageSelectionStrategy', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'pageSelectionStrategy' ),
			'permission_callback' => '__return_true',
		) );
		// 排序
		register_rest_route( 'seastar/v1', '/product/orderByMeta', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'orderByMeta' ),
			'permission_callback' => '__return_true',
		) );
		// 排序
		register_rest_route( 'seastar/v1', '/product/hotSearchKeywordList', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'hotSearchKeywordList' ),
			'permission_callback' => '__return_true',
		) );
		// 类目
		register_rest_route( 'seastar/v1', '/product/categoryList', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'categoryList' ),
			'permission_callback' => '__return_true',
		) );
		// 搜索商品
		register_rest_route( 'seastar/v1', '/product/search', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'search' ),
			'permission_callback' => '__return_true',
		) );
		// 基于模版搜索
		register_rest_route( 'seastar/v1', '/product/template/templateSearch', array(
			'methods'             => 'POST',
			'callback'            => array( $this, 'templateSearch' ),
			'permission_callback' => '__return_true',
		) );
		// 搜索商品
		register_rest_route( 'seastar/v1', '/license/unbind', array(
			'methods'             => 'GET',
			'callback'            => array( $this, 'unbind' ),
			'permission_callback' => '__return_true',
		) );


	}

	/**
	 * 生成简码
	 *
	 * @param WP_REST_Request $request 请求
	 *
	 * @return string 简码
	 */
	public function generateShortcode( WP_REST_Request $request ): string {
		$requestBody = $request->get_body();
		$requestBody = json_decode( $requestBody, true );
		$module      = $requestBody['module'] ?? '';
		$template    = $requestBody['template'];
		if ( ! isset( $template ) || $template === '' ) {
			return '';
		}
		if ( isset( $module ) && $module !== '' ) {
			$shortcode = '[moredeal module=' . $module . ' template=' . $template . ']';
		} else {
			$shortcode = '[moredeal-block template=' . $template . ']';
		}

		return $shortcode;
	}

	/**
	 * 激活授权码
	 * @return mixed|\WP_Error
	 */
	public function active( string $license_key ) {
		try {
			$body = '{"license": "' . $license_key . '", "domain": "' . self::getCurrDomain() . '"}';
			error_log( 'active body: ' . $body );

			return json_decode( $this->restPost( '/license/active', $body ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal active license error', $e->getMessage() );
		}
	}

	/**
	 * 激活授权码
	 * @return mixed|\WP_Error
	 */
	public function unbind( $request ) {
		try {
			error_log( 'unbind body: ' . $request );

			return json_decode( $this->restPost( '/license/unbind', $request ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal unbind license error', $e->getMessage() );
		}
	}

	/**
	 * 激活授权码
	 * @return mixed|\WP_Error
	 */
	public function activeInfo( $value ) {
		try {
			$body = '{"license": "' . $value . '", "domain": "' . self::getCurrDomain() . '"}';
			error_log( 'info body: ' . $body );

			return json_decode( $this->restPost( '/license/info', $body ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal info license error', $e->getMessage() );
		}
	}

	/**
	 * 搜索条件
	 *
	 */
	public function conditions() {
		try {
			return json_decode( $this->restPost( '/product/conditions?lang=' . get_locale() ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal get conditions error', $e->getMessage() );
		}
	}

	/**
	 * 选品策略
	 *
	 */
	public function pageSelectionStrategy(WP_REST_Request $request) {
		$body = $request->get_body() ?: '{"page": {"page": 1, "pageSize": 10000}}';
		try {
			return json_decode( $this->restPost( '/product/pageSelectionStrategy?lang=' . get_locale(), $body ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal get pageSelectionStrategy error', $e->getMessage() );
		}
	}

	/**
	 * 排序元数据
	 * @return mixed|\WP_Error
	 */
	public function orderByMeta() {
		try {
			return json_decode( $this->restPost( '/product/orderByMeta?lang=' . get_locale() ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal get orderMetaData error', $e->getMessage() );
		}
	}

	/**
	 * 排序元数据
	 * @return mixed|\WP_Error
	 */
	public function hotSearchKeywordList() {
		try {
			$hotKeywords = HotKeywordsConfig::getHotKeywords();
			if (count($hotKeywords) > 0) {
				return array(
					'success' => true,
					'errorCode' => null,
					'errorMessage' => null,
					'data' => $hotKeywords
				);
			}
			return json_decode( $this->restPost( '/product/hotSearchKeywordList?lang=' . get_locale() ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal get orderMetaData error', $e->getMessage() );
		}
	}

	/**
	 * 排序元数据
	 * @return mixed|\WP_Error
	 */
	public function categoryList() {
		try {
			return json_decode( $this->restPost( '/product/categoryList?lang=' . get_locale() ) );
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal get categoryList error', $e->getMessage() );
		}
	}

	/**
	 * 查询商品
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return mixed
	 */
	public function search( WP_REST_Request $request ) {
		$body                   = $request->get_body() ?: '{"page": {"page": 1, "pageSize": 10}}';
		$bodyarr                = (array) json_decode( $body );
		$bodyarr['marketPlace'] = ModuleManager::getInstance()->AmazonModule()->getMarketPlace() ?: 'US';
		$body                   = json_encode( $bodyarr );
		error_log( 'search body: ' . $body );
		try {
			$result = json_decode( $this->restPost( '/product/search?lang=' . get_locale(), $body ) );

//			$this->resetLicenseInfo( $result );
			return $result;
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal search products error', $e->getMessage() );
		}
	}

	/**
	 * 查询商品数据
	 *
	 * @param $body
	 *
	 * @return array
	 */
	public function getSearchData( $body ) {
		error_log( 'search body: ' . $body );

		try {
			$result = json_decode( $this->restPost( '/product/search?lang=' . get_locale(), $body ) );
//			$this->resetLicenseInfo( $result );
			$result = (array) $result;
			if ( array_key_exists( 'success', $result ) && $result['success'] ) {
				if ( array_key_exists( 'records', $result ) && $result['records'] != null ) {
					$records = (array) $result['records'];
					if ( count( $records ) > 0 ) {
						return $records;
					}
				}
			}

			return array();
		} catch ( Exception $e ) {
			error_log( 'moredeal search products error: ' . $e->getMessage() );

			return array();
		}
	}

	/**
	 * 查询商品
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return array|string
	 * @throws Exception
	 */
	public function templateSearch( WP_REST_Request $request ) {
		$body        = $request->get_body();
		$requestBody = (array) json_decode( $body );
		// 请求接口参数
		$restRequest                     = array_key_exists( 'request', $requestBody ) ? (array) $requestBody['request'] : array(
			'page' => array(
				'page'     => 1,
				'pageSize' => 10,
			)
		);
		$restRequest['marketPlace']      = ModuleManager::getInstance()->AmazonModule()->getMarketPlace() ?: 'US';
		$restRequest['page']             = (array) $restRequest['page'];
		$restRequest['page']['pageSize'] = min( $restRequest['page']['pageSize'], 20 );
		// 获取请求数据
		$products = $this->getSearchData( json_encode( $restRequest ) );

		// 获取模板
		$searchTemplate = array_key_exists( 'template', $requestBody ) ? $requestBody['template'] : 'default';

		$searchTemplate = BlockTemplateManager::getInstance()->prepareShortcodeTemplate( $searchTemplate );

		// 获取属性
		$params = array_key_exists( 'attributes', $requestBody ) ? (array) $requestBody['attributes'] : array();

		foreach ( $products as $index => $item ) {
			$item                 = (array) $item;
			$item['stock_status'] = MoredealManager::getStockStatus( $item['stock'] );
			$item['url']          = TemplateHelper::parseUrl( $item );
			$products[ $index ]   = ModuleViewer::getInstance()->productItem( $params['post_id'], 'Amazon', $item, $index );
		}

		if ( ! in_array( $searchTemplate, AttributeHelper::getCanRenderSearchTemplate() ) ) {
			$searchTemplate = 'default';
		}

		if ( $searchTemplate === 'default' || $searchTemplate === 'block_default' ) {
			return $products;
		}

		$blockTemplateManager = BlockTemplateManager::getInstance();

		if ( empty( $searchTemplate ) || ! $blockTemplateManager->isTemplateExists( $searchTemplate ) ) {
			return '';
		}

		if ( ! $blockTemplateManager->getViewPath( $searchTemplate ) ) {
			return '';
		}

		// 渲染模板
		return $blockTemplateManager->render( $searchTemplate, array(
			'data'             => $products,
			'params'           => $params,
			'post_id'          => $params['post_id'],
			'post_title'       => $params['post_title'],
			'template'         => $searchTemplate,
			'auth_code'        => Plugin::getAuthCode(),
			'isPro'            => Plugin::isPro(),
			'moredeal_version' => Plugin::version(),
			'wp_version'       => get_bloginfo( 'version' ),
			'wp_addr'          => SearchProductClient::getCurrDomain(),
			'is_show_more'     => false,
		) );
	}

	/**
	 * 查询商品
	 *
	 * @param string $body
	 *
	 * @return mixed
	 */
	public function searchFromAdSystem(string $body ) {
		error_log( 'search body: ' . $body );
		try {
			$result = json_decode( $this->restPost( '/product/listProductFromAdSystemWp?lang=' . get_locale(), $body ) );

			return $result;
		} catch ( Exception $e ) {
			return new \WP_Error( 'moredeal search products error', $e->getMessage() );
		}
	}

	/**
	 * @param $path
	 *
	 * @return void
	 */
	protected function _prepareRest( $path ) {

		if ( strstr( $path, 'http://' ) || strstr( $path, 'https://' ) ) {
			$uri = $path;
		} else {
			$uri = $this->getUri();
			if ( $path && $path[0] != '/' && $uri[ strlen( $uri ) - 1 ] != '/' ) {
				$path = '/' . $path;
			}
			$uri = $uri . $path;
		}

		$client = self::getHttpClient();

		$client->resetParameters();
		$client->setUri( $uri );
		foreach ( $this->_custom_header as $header => $value ) {
			$client->setHeaders( $header, $value );
		}
		if ( '/license/active' != $path ) {
			if ( ! empty( LicenseConfig::getInstance()->option( 'license_key' ) ) ) {
				$client->setHeaders( self::WP_AUTHORIZATION, LicenseConfig::getInstance()->option( 'license_key' ) );
			} else {
				$client->setHeaders( self::WP_AUTHORIZATION, self::DEFAULT_WP_AUTH );
			}
		}
		$client->setHeaders( self::DOMAIN, self::getCurrDomain() );

		error_log( self::WP_AUTHORIZATION . ' Header: ' . json_encode( $client->getHeader( self::WP_AUTHORIZATION ) ) );
		error_log( self::DOMAIN . ' Header: ' . json_encode( $client->getHeader( self::DOMAIN ) ) );
		error_log( 'URI: ' . $uri );
	}

	/**
	 * 重新获取授权信息
	 *
	 * @param $result
	 *
	 * @return void
	 */
	public function resetLicenseInfo( $result ) {
		$result = (array) $result;
		if ( array_key_exists( 'msg', $result ) && $result['msg'] ) {
			LicenseManager::getInstance()->deleteCache();
			LicenseManager::getInstance()->getData();
		}
	}

	/**
	 * 获取domain
	 * @return string|null
	 */
	public static function getCurrDomain(): ?string {
		return parse_url( site_url(), PHP_URL_HOST );
	}

}