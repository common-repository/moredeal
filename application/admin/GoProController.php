<?php

namespace Moredeal\application\admin;
defined( '\ABSPATH' ) || exit;

use Moredeal\application\Plugin;

class GoProController {

	const slug = 'go-pro';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function add_admin_menu() {
		if ( Plugin::isFree() && ! Plugin::isEnvato() ) {
			global $submenu;
			$submenu['moredeal'][] = array(
				'<b style="color: #dd9933;">' . __('Go Pro', 'moredeal') . '</b>',
				'manage_options',
				Plugin::pluginGoProUrl()
			);
		}
	}


}