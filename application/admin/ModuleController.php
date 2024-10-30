<?php

namespace Moredeal\application\admin;
defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\ModuleManager;
use Moredeal\application\Plugin;

/**
 * ModuleController class file
 */
class ModuleController {

	const slug = 'moredeal-modules';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function add_admin_menu() {
		add_submenu_page( Plugin::slug, __( 'Module', 'moredeal' ) . ' &lsaquo; Moredeal', __( 'Module', 'moredeal' ), 'manage_options', self::slug, array(
			$this,
			'actionIndex'
		) );
	}

	public function actionIndex() {
		wp_enqueue_style( 'moredeal-setting', \Moredeal\PLUGIN_RES . '/css/setting.css', false, Plugin::version );
		wp_enqueue_style( 'moredeal-bootstrap', \Moredeal\PLUGIN_RES . '/bootstrap/css/moredeal-bootstrap.min.css', array(), Plugin::version() );
		AdminPlugin::getInstance()->render( 'module_index', array( 'modules' => ModuleManager::getInstance()->getConfigurableModules() ) );
	}


}
