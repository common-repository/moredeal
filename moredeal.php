<?php

namespace Moredeal;

/*
Plugin Name: Moredeal - Monetize your WordPress content
Text Domain: moredeal
Plugin URI: https://mdc.ai
Description: Our plugin is used to help the individual blogers select the best products for their articles. set the different product selection strategies according to the comprehensive indicators. The blogger can select the best product for their articales through the different strategies to get more traffice and the conversion.
Author: mdc.ai
Version: 2.0.25
Author URI: mdc.ai
*/

use Moredeal\application\Installer;

defined( '\ABSPATH' ) || die( 'No direct script access allowed!' );

const NS = __NAMESPACE__ . '\\';
define( NS . 'PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( NS . 'PLUGIN_FILE', __FILE__ );
define( NS . 'PLUGIN_RES', plugins_url( 'res', __FILE__ ) );

require_once PLUGIN_PATH . 'loader.php';

/**
 * Plugin class file
 */
class Moredeal {

	public function __construct() {
		add_action( 'plugins_loaded', array( '\Moredeal\application\Plugin', 'getInstance' ) );

		if ( is_admin() ) {
			register_activation_hook(__FILE__, array(Installer::getInstance(), 'activate'));
			register_deactivation_hook(__FILE__, array(Installer::getInstance(), 'deactivate'));
			register_uninstall_hook(__FILE__, array('\Moredeal\application\Installer', 'uninstall'));
			add_action( 'init', array( '\Moredeal\application\admin\AdminPlugin', 'getInstance' ) );
		}

	}

}

new Moredeal;


