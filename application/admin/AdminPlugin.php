<?php

namespace Moredeal\application\admin;

defined( '\ABSPATH' ) || exit;

require_once( "MoredealMetaBox.php" );

use Moredeal\application\components\LicenseManager;
use Moredeal\application\components\MoredealMetaBox;
use Moredeal\application\helpers\TextHelper;
use Moredeal\application\Plugin;
use Moredeal\application\scheduler\ProductUpdateScheduler;

/**
 * PluginAdmin class file
 *
 * @author Aclumsy
 */
class AdminPlugin {

	/**
	 * 实例
	 * @var AdminPlugin|null
	 */
	protected static ?AdminPlugin $instance = null;

	/**
	 * PluginAdmin 实例
	 * @return AdminPlugin|null
	 */
	public static function getInstance(): ?AdminPlugin {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造函数
	 * @throws \Exception
	 */
	private function __construct() {
		if ( ! is_admin() ) {
			die( 'You are not authorized to perform the requested action.' );
		}

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_load_scripts' ) );
		add_filter( 'parent_file', array( $this, 'highlight_admin_menu' ) );
		if ( isset( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'plugins.php' ) {
			wp_enqueue_script('moredeal-keywords', \Moredeal\PLUGIN_RES . '/js/keywords.js', array('jquery'), Plugin::version() );
			wp_enqueue_script('jquery-ui-tabs');
			wp_enqueue_script('jquery-ui-button');
			wp_enqueue_style('moredeal-admin-ui-css', \Moredeal\PLUGIN_RES . '/css/jquery-ui.min.css', false, Plugin::version());
			add_filter( 'plugin_row_meta', array( $this, 'add_plugin_row_meta' ), 10, 2 );
		}

		if ( Plugin::isFree() || ( Plugin::isPro() && Plugin::isActivated() ) || Plugin::isEnvato() ) {
			GeneralConfig::getInstance()->adminInit();
			TemplateConfig::getInstance()->adminInit();
//			ModuleManager::getInstance()->adminInit();
//			new ModuleController;
			new ProductController;
			HotKeywordsConfig::getInstance()->adminInit();
			LicenseConfig::getInstance()->adminInit();
			MoredealMetaBox::getInstance();
			BeforeDeletePost::getInstance();
			ProductUpdateScheduler::addMoredealScheduleEvent();
		}
		if ( ! Plugin::isFree() || Plugin::isExpire() ) {
			LicenseManager::getInstance()->adminInit();
		}
	}

	/**
	 * 添加菜单
	 * @return void
	 */
	public function add_admin_menu() {
		$icon_svg = \Moredeal\PLUGIN_RES . '/css/images/setting.svg';
		$title    = 'Moredeal';
//		if ( Plugin::isPro() ) {
//			$title .= ' Pro';
//		}
		add_menu_page( $title, $title, 'publish_posts', Plugin::slug, null, $icon_svg );
	}

	/**
	 * 加载脚本
	 * @return void
	 */
	function admin_load_scripts() {
		if ( $GLOBALS['pagenow'] != 'admin.php' || empty( $_GET['page'] ) ) {
			return;
		}

		if ( sanitize_key( wp_unslash( $_GET['page'] ) ) != Plugin::slug ) {
			return;
		}

		wp_enqueue_script( 'moredeal_base', \Moredeal\PLUGIN_RES . '/js/base.js', array( 'jquery' ) );
		wp_localize_script( 'moredeal_base', 'moredealL10n', array(
			'are_you_sure' => __( 'Are you sure?', 'moredeal' ),
			//'sitelang'     => 'en_US'//GeneralConfig::getInstance()->option( 'lang' ),
		) );

		wp_enqueue_style( 'moredeal_setting', \Moredeal\PLUGIN_RES . '/css/setting.css', null, Plugin::version() );
	}

	/**
	 * Highlight menu for hidden submenu item
	 *
	 * @param $file
	 *
	 * @return mixed
	 */
	function highlight_admin_menu( $file ) {
		global $plugin_page;

		// options.php - hidden submenu items
		if ( $file != 'options.php' || substr( $plugin_page, 0, strlen( Plugin::slug ) ) !== Plugin::slug ) {
			return $file;
		}

		$page_parts = explode( '--', $plugin_page );
		if ( count( $page_parts ) > 1 ) {
			$plugin_page = $page_parts[0];
		} else {
			$plugin_page = Plugin::slug;
		}

		return $file;
	}

	/**
	 * add_plugin_row_meta
	 *
	 * @param array $links
	 * @param $file
	 *
	 * @return array
	 */
	public function add_plugin_row_meta( array $links, $file ): array {
		if ( $file == plugin_basename( \Moredeal\PLUGIN_FILE ) && ( Plugin::isActivated() || Plugin::isFree() ) ) {
			return array_merge(
				$links, array(
					'<a href="' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin.php?page=moredeal">' . __( 'Settings', 'moredeal' ) . '</a>',
				)
			);
		}

		return $links;
	}

	/**
	 * 加载插件设置页面
	 *
	 * @param $view_name
	 * @param $_data
	 *
	 * @return void
	 */
	public static function render( $view_name, $_data = null ) {
		if ( is_array( $_data ) ) {
			extract( $_data, EXTR_PREFIX_SAME, 'data' );
		} else {
			$data = $_data;
		}

		include \Moredeal\PLUGIN_PATH . 'application/admin/views/' . TextHelper::clear( $view_name ) . '.php';
	}

}