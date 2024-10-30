<?php

namespace Moredeal\application;

defined('\ABSPATH') || exit;

use Moredeal\application\admin\LicenseConfig;
use Moredeal\application\components\LicenseManager;
use Moredeal\application\components\SearchProductClient;
use Moredeal\application\scheduler\ProductUpdateScheduler;
use function get_option;

/**
 * Plugin class file
 */
class Plugin {

	/**
	 * 版本
	 */
	const version = '2.0.25';

	/**
	 * DB 版本
	 */
	const db_version = 54;

	/**
	 * slug
	 */
	const slug = 'moredeal';

	/**
	 * 插件名称
	 */
	const name = 'Moredeal';

	/**
	 * wp require minimum version
	 *
	 */
	const wp_requires = '4.6.1';

	/**
	 * 实例
	 * @var Plugin|null
	 */
	private static ?Plugin $instance = null;

	/**
	 * 是否是 pro 版本
	 * @var bool|null
	 */
	private static ?bool $is_pro = null;

	/**
	 * 是否是 过期
	 * @var bool|null
	 */
	private static ?bool $is_expire = null;

	/**
	 * @var bool|null
	 */
	private static ?bool $is_envato = null;

	/**
	 * Plugin 实例
	 * @return Plugin|null
	 */
	public static function getInstance(): ?Plugin {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	private function __construct() {
		$this->loadTextDomain();
		if ( self::isFree() || ( self::isPro() && self::isActivated() ) || self::isEnvato() ) {
			if ( ! is_admin() ) {
				add_action( 'wp_enqueue_scripts', array( $this, 'registerScripts' ) );
				add_action( 'amp_post_template_css', array( $this, 'registerAmpStyles' ) );
				ModuleViewer::getInstance()->init();
				BlockShortcode::getInstance();
				BlockSearchShortcode::getInstance();
				SearchProductClient::getInstance();
			}
			ProductUpdateScheduler::initAction();
		}
	}

	/**
	 * 国际化
	 * @return void
	 */
	private function loadTextDomain() {
		// plugin admin
		load_plugin_textdomain( 'moredeal', false, dirname( plugin_basename( \Moredeal\PLUGIN_FILE ) ) . '/languages/' );

		// frontend templates
//		$lang     = GeneralConfig::getInstance()->option( 'lang' );
//		$mo_files = array();
//		if ( defined( 'LOCO_LANG_DIR' ) ) {
//			$mo_files[] = trailingslashit( LOCO_LANG_DIR ) . 'plugins/moredeal-tpl-' . $lang . '.mo';
//		}
//
//		// loco lang dir
//		$mo_files[] = trailingslashit( WP_LANG_DIR ) . 'plugins/moredeal-tpl-' . $lang . '.mo';
//		$mo_files[] = \Moredeal\PLUGIN_PATH . 'languages/tpl/moredeal-tpl-' . strtoupper( $lang ) . '.mo';
//		foreach ( $mo_files as $mo_file ) {
//			if ( file_exists( $mo_file ) && is_readable( $mo_file ) ) {
//				if ( load_textdomain( 'moredeal-tpl', $mo_file ) ) {
//					return;
//				}
//			}
//		}
	}

	/**
	 * 注册脚本
	 * @return void
	 */
	public function registerScripts() {
		wp_register_style( 'moredeal-element-ui-style', \Moredeal\PLUGIN_RES . '/js/search/element_ui.css' );
		wp_register_style( 'moredeal-treeselects-style', \Moredeal\PLUGIN_RES . '/js/search/treeselect.css' );
		wp_register_style( 'moredeal-search-style', \Moredeal\PLUGIN_RES . '/css/search.css' );
		wp_register_style( 'moredeal-products-style', \Moredeal\PLUGIN_RES . '/css/products.css', array(), Plugin::version() );
		wp_register_style( 'moredeal-bootstrap-style', \Moredeal\PLUGIN_RES . '/bootstrap/css/moredeal-bootstrap.min.css', array(), Plugin::version() );
		wp_register_style( 'moredeal-jquery-ui-css-style', \Moredeal\PLUGIN_RES . '/css/jquery-ui.min.css', false, Plugin::version() );
		wp_register_style( 'moredeal-card-feature-style', \Moredeal\PLUGIN_RES . '/css/block/block_card_feature_style.css', false, Plugin::version() );

		wp_register_script( 'moredeal-vue-script', \Moredeal\PLUGIN_RES . '/js/search/vue.min.js' );
		wp_register_script( 'moredeal-treeselect-script', \Moredeal\PLUGIN_RES . '/js/search/treeselect.js' );
		wp_register_script( 'moredeal-element-ui-script', \Moredeal\PLUGIN_RES . '/js/search/element_ui.min.js' );
		wp_register_script( 'moredeal-axios-script', \Moredeal\PLUGIN_RES . '/js/search/axios.min.js' );
		wp_register_script( 'moredeal-metabox_rest-script', \Moredeal\PLUGIN_RES . '/js/request.js', array(), Plugin::version() );
		wp_register_script( 'moredeal-goods-script', \Moredeal\PLUGIN_RES . '/js/goods.js' );

		wp_register_script( 'moredeal-bootstrap-script', \Moredeal\PLUGIN_RES . '/bootstrap/js/bootstrap.min.js', array( 'jquery' ), null, false );
		wp_register_script( 'moredeal-bootstrap-tooltip-script', \Moredeal\PLUGIN_RES . '/bootstrap/js/tooltip.js', array( 'jquery' ), null, false );
		wp_register_script( 'moredeal-bootstrap-popover-script', \Moredeal\PLUGIN_RES . '/bootstrap/js/popover.js', array( 'moredeal-bootstrap-tooltip-script' ), null, false );
		wp_register_script( 'moredeal-sensors-sdk-script', \Moredeal\PLUGIN_RES . '/js/sensorsdata.min.js' );
		wp_register_script( 'moredeal-sensors-init-script', \Moredeal\PLUGIN_RES . '/js/point/sensors_init.js' );
		wp_register_script( 'moredeal-view-point-script', \Moredeal\PLUGIN_RES . '/js/point/view_point.js' );

	}

	/**
	 * 注册 amp 样式
	 * @return void
	 */
	public function registerAmpStyles() {
		echo '.moredeal-container table td{padding:0} .moredeal-container .btn,.moredeal-container .moredeal-price{white-space:nowrap;font-weight:700}.moredeal-couponcode,.moredeal-gridbox a{text-decoration:none}.moredeal-container .moredeal-gridbox{box-shadow:0 8px 16px -6px #eee;border:1px solid #ddd;margin-bottom:25px;padding:20px}.moredeal-container .moredeal-listcontainer .row-products>div{margin-bottom:12px}.moredeal-container .btn{display:inline-block;padding:7px 14px;margin-bottom:0;font-size:14px;line-height:1.42857143;text-align:center;vertical-align:middle;touch-action:manipulation;cursor:pointer;user-select:none;background-image:none;border:1px solid transparent;border-radius:4px}.moredeal-container .btn-danger{color:#fff;background-color:#5cb85c;border-color:#4cae4c;text-decoration:none}.moredeal-container .panel-default{border:1px solid #ddd;padding:20px}.moredeal-price-alert-wrap,.moredeal-price-tracker-item div[id$=chart]{display:none}.moredeal-price-tracker-panel .btn{margin-bottom:6px}.moredeal-container .moredeal-no-top-margin{margin-top:0}.moredeal-container .moredeal-mb5{margin-bottom:5px}.moredeal-container .moredeal-mb10{margin-bottom:10px}.moredeal-container .moredeal-mb15{margin-bottom:15px}.moredeal-container .moredeal-mb20{margin-bottom:20px}.moredeal-container .moredeal-mb25{margin-bottom:25px}.moredeal-container .moredeal-mb30{margin-bottom:30px}.moredeal-container .moredeal-mb35{margin-bottom:35px}.moredeal-container .moredeal-lineh-20{line-height:20px}.moredeal-container .moredeal-mr10{margin-right:10px}.moredeal-container .moredeal-mr5{margin-right:5px}.moredeal-container .btn.moredeal-btn-big{padding:13px 60px;line-height:1;font-size:20px;font-weight:700}.moredeal-couponcode{text-align:center;background:#efffda;padding:8px;display:block;border:2px dashed #5cb85c;margin-bottom:12px}.moredeal-bordered-box{border:2px solid #ededed;padding:25px}.moredeal-price-tracker-item .moredeal-price{font-size:22px;font-weight:700}.moredeal-list-coupons .btn{font-size:16px;font-weight:700;display:block}.moredeal-listlogo-title{line-height:18px;font-size:15px}.moredeal-list-withlogos .moredeal-price,.moredeal-listcontainer .moredeal-price{font-weight:700;font-size:20px;color:#5aaf0b}.moredeal-container .moredeal-list-withlogos .btn{font-weight:700;font-size:15px;padding:8px 16px}.moredeal-price-row strike{opacity:.42;font-size:90%}.moredeal-list-logo-title{font-weight:700;font-size:17px}.moredeal-container .moredeal-btn-grid .btn{display:block;margin-bottom:10px}#moredeal_market .moredeal-image-container img{max-height:350px}.moredeal-review-block{padding:20px;border:1px solid #eee}.moredeal-line-hr{clear:both;border-top:1px solid #eee;height:1px}.amp-wp-article-content .moredeal-btn-row amp-img,.amp-wp-article-content .moredeal-desc-cell amp-img,.amp-wp-article-content .moredeal-price-tracker-panel .moredeal-mb5 amp-img,.amp-wp-article-content .producttitle amp-img{display:inline-block;margin:0 4px 0 0;vertical-align:middle}.moredeal-container .moredeal-promotion{top:25px;left:0;position:absolute;z-index:10}.moredeal-container .moredeal-discount{background-color:#eb5e58;border-radius:0 4px 4px 0;color:#fff;display:inline-block;font-size:16px;padding:3px 5px}.moredeal-thumb{position:relative}'
		     . '@media (max-width: 767px) {body .moredeal-container .hidden-xs {display: none;} body .moredeal-container .visible-xs {display: block;}} body .moredeal-container .visible-xs {display: none;}';
	}

	/**
	 * 插件版本
	 * @return string
	 */
	public static function version(): string {
		return self::version;
	}

	/**
	 * 是否是免费版
	 * @return bool
	 */
	public static function isFree(): bool {
		return ! self::isPro();
	}

	/**
	 * 是否是专业版
	 * @return bool|null
	 */
	public static function isPro(): ?bool {
		if ( self::$is_pro === null || self::$is_expire === null ) {
			$licenseKey  = LicenseConfig::getInstance()->option( 'license_key' );
			$licenseInfo = LicenseManager::getInstance()->getData();
			if ( $licenseKey
			     && $licenseInfo
			     && is_array( $licenseInfo )
			     && array_key_exists( 'license', $licenseInfo )
			     && $licenseInfo['license']
			     && $licenseInfo['license'] == $licenseKey ) {
				if ( array_key_exists( 'codeStatus', $licenseInfo )
				     && $licenseInfo['codeStatus']
				     && strtoupper( $licenseInfo['codeStatus'] ) == 'ACTIVE' ) {
					self::$is_pro    = true;
					self::$is_expire = false;
				} else {
					self::$is_pro    = false;
					self::$is_expire = true;
				}
			} else {
				self::$is_pro = false;
				self::$is_expire = false;
			}
		}
		return self::$is_pro;
	}

	public static function resetPro() {
		self::$is_pro = null;
		self::$is_expire = null;
	}

	public static function isExpire(): bool {
		if ( self::$is_expire === null ) {
			self::isPro();
		}
		return self::$is_expire;
	}

	/**
	 * 是否是激活状态
	 * @return bool
	 */
	public static function isActivated(): bool {
		if ( self::isPro() ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * isEnvato
	 * @return bool|null
	 */
	public static function isEnvato(): ?bool {
		return false;
	}

	/**
	 * isInactiveEnvato
	 * @return bool
	 */
	public static function isInactiveEnvato(): bool {
		if ( self::isEnvato() && ! self::isActivated() ) {
			return true;
		} else {
			return false;
		}
	}

	public static function getAuthCode() {
		$codeSlug = get_option('moredeal_license_info');
		return ! empty($codeSlug) ? $codeSlug['codeSlug'] : SearchProductClient::DEFAULT_WP_AUTH_SLUG;
	}

	public static function getPluginDomain(): string {
		return 'https://docs.mdc.ai/';
	}

	public static function pluginGoProUrl(): string {
		return "https://docs.mdc.ai/getting-started/upgrade-free-to-pro";
	}

	public static function pluginDocsUrl(): string {
		return 'https://docs.mdc.ai/';
	}

	public static function pluginFeedBackUrl(): string {
		return 'https://www.mdc.ai/contact-us/';
	}
}
