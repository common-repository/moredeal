<?php

namespace Moredeal\application;

use Moredeal\application\components\SearchProductClient;
use Moredeal\application\scheduler\ProductUpdateScheduler;

defined( '\ABSPATH' ) || exit;

/**
 * Installer class file
 */
class Installer {

	/**
	 * 实例
	 * @var Installer|null
	 */
	private static ?Installer $instance = null;

	/**
	 * 获取实例
	 * @return Installer|null
	 */
	public static function getInstance(): ?Installer {
		if ( self::$instance == null ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * 构造方法
	 */
	private function __construct() {
		if ( ! empty( $GLOBALS['pagenow'] ) && $GLOBALS['pagenow'] == 'plugins.php' ) {
			add_action( 'admin_init', array( $this, 'requirements' ), 0 );
		}

		add_action( 'admin_init', array( $this, 'upgrade' ) );
		add_action( 'admin_init', array( $this, 'redirect_after_activation' ) );
	}

	/**
	 * DB 版本
	 * @return int
	 */
	static public function dbVersion(): int {
		return Plugin::db_version;
	}

	/**
	 * 激活插件
	 */
	public static function activate() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		self::requirements();

		ProductUpdateScheduler::addMoredealScheduleEvent();
		add_option( Plugin::slug . '_do_activation_redirect', true );
		add_option( Plugin::slug . '_first_activation_date', time() );
		self::upgradeTables();
	}

	/**
	 * 停用插件
	 * @return void
	 */
	public static function deactivate() {
		ProductUpdateScheduler::clearMoredealScheduleEvent();
	}

	/**
	 * 要求
	 * @return void
	 */
	public static function requirements() {
		$php_min_version = '5.3';
		$extensions      = array(
			'simplexml',
			'mbstring',
			'hash',
		);

		$errors = array();
		$name   = get_file_data( \Moredeal\PLUGIN_FILE, array( 'Plugin Name' ), 'plugin' );

		global $wp_version;
		if ( version_compare( Plugin::wp_requires, $wp_version, '>' ) ) {
			$errors[] = sprintf( 'You are using Wordpress %s. <em>%s</em> requires at least <strong>Wordpress %s</strong>.', $wp_version, $name[0], Plugin::wp_requires );
		}

		$php_current_version = phpversion();
		if ( version_compare( $php_min_version, $php_current_version, '>' ) ) {
			$errors[] = sprintf( 'PHP is installed on your server %s. <em>%s</em> requires at least <strong>PHP %s</strong>.', $php_current_version, $name[0], $php_min_version );
		}

		foreach ( $extensions as $extension ) {
			if ( ! extension_loaded( $extension ) ) {
				$errors[] = sprintf( 'Requires extension <strong>%s</strong>.', $extension );
			}
		}
		if ( ! $errors ) {
			return;
		}
		unset( $_GET['activate'] );
		deactivate_plugins( plugin_basename( \Moredeal\PLUGIN_FILE ) );
		$e = sprintf( '<div class="error"><p>%1$s</p><p><em>%2$s</em> ' . 'cannot be installed!' . '</p></div>', join( '</p><p>', $errors ), $name[0] );
		wp_die( wp_kses_post( $e ) );
	}

	/**
	 * 卸载
	 * @return void
	 */
	public static function uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		delete_option( Plugin::slug . '_db_version' );
		if ( Plugin::isEnvato() ) {
			delete_option( Plugin::slug . '_env_install' );
		}
	}

	/**
	 * 升级
	 * @return void
	 */
	public static function upgrade() {
		$db_version = get_option( Plugin::slug . '_db_version' );
		if ( (int) $db_version >= self::dbVersion() ) {
			return;
		}
		self::upgradeTables();

		update_option( Plugin::slug . '_db_version', self::dbVersion() );
	}

	/**
	 * 升级表
	 */
	private static function upgradeTables() {
		$models = array( 'ProductModel' );
		$sql    = '';
		foreach ( $models as $model ) {
			$model = "\\Moredeal\\application\\models\\" . $model;
			$sql   .= $model::model()->getDump();
			$sql   .= "\r\n";
		}
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( $sql );
	}

	/**
	 * 激活后重定向
	 */
	public function redirect_after_activation() {
		if ( get_option( Plugin::slug . '_do_activation_redirect', false ) ) {
			delete_option( Plugin::slug . '_do_activation_redirect' );
			wp_safe_redirect( get_admin_url( get_current_blog_id(), 'admin.php?page=' . Plugin::slug ) );
		}
	}

}
