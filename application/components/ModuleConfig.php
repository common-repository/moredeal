<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\admin\AdminPlugin;
use Moredeal\application\admin\ModuleController;
use Moredeal\application\Plugin;

/**
 * ModuleConfig abstract class file
 */
abstract class ModuleConfig extends Config {

	protected $module_id;

	protected function __construct( $module_id = null ) {
		if ( $module_id ) {
			$this->module_id = $module_id;
		} else {
			$parts = explode( '\\', get_class( $this ) );
			$this->module_id = $parts[ count( $parts ) - 2 ];
		}
		parent::__construct();
	}

	public function getModuleId() {
		return $this->module_id;
	}

	/**
	 * @throws \Exception
	 */
	public function getModuleName() {
		return $this->getModuleInstance()->getName();
	}

	/**
	 * @throws \Exception
	 */
	public function getModuleInstance() {
		return ModuleManager::factory( $this->getModuleId() );
	}

	public function page_slug(): string {
		return ModuleController::slug . '--' . $this->getModuleId();
	}

	public function option_name(): string {
		return 'moredeal_modules_' . $this->getModuleId();
	}

	/**
	 * @throws \Exception
	 */
	public function add_admin_menu() {
		add_submenu_page( 'options.php', $this->getModuleName() . ' ' . __( 'settings', 'moredeal' ) . ' &lsaquo; Moredeal', '', 'manage_options', $this->page_slug(), array(
			$this,
			'settings_page'
		) );
	}

	/**
	 * @throws \Exception
	 */
	public function settings_page() {
		wp_enqueue_style( 'moredeal-setting', \Moredeal\PLUGIN_RES . '/css/setting.css', false, Plugin::version );
		AdminPlugin::render( 'module_settings', array( 'module' => $this->getModuleInstance(), 'config' => $this ) );
	}

	public function options(): array {
		return array();
	}

}
