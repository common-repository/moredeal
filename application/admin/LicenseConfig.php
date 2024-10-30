<?php

namespace Moredeal\application\admin;

use Moredeal\application\components\Config;
use Moredeal\application\components\LicenseManager;
use Moredeal\application\components\SearchProductClient;
use Moredeal\application\Plugin;

defined( '\ABSPATH' ) || exit;

/**
 * LicenseConfig class file
 */
class LicenseConfig extends Config {

	/**
	 * slug
	 * @return string
	 */
	const slug = 'moredeal-license';

	/**
	 * 添加菜单
	 * @return void
	 */
	public function add_admin_menu() {
		 $this->unbindLicense();
		add_submenu_page( Plugin::slug, __( 'License', 'moredeal' ) . ' &lsaquo; Moredeal', __( 'License', 'moredeal' ), 'manage_options', $this->page_slug(), array(
			$this,
			'licenseIndex'
		) );
	}

	/**
	 * 加载License页面
	 * @return void
	 */
	public function licenseIndex() {
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_style( 'moredeal-admin-ui-css', \Moredeal\PLUGIN_RES . '/css/jquery-ui.min.css', false, Plugin::version );
		wp_enqueue_style( 'moredeal-setting', \Moredeal\PLUGIN_RES . '/css/setting.css', false, Plugin::version );
		AdminPlugin::render( 'license_index', array( 'page_slug' => $this->page_slug() ) );
	}

	/**
	 * page_slug
	 * @return string
	 */
	public function page_slug(): string {
		return self::slug;
	}

	/**
	 * option_name
	 * @return string
	 */
	public function option_name(): string {
		return 'moredeal_license';
	}

	/**
	 * 操作选项
	 * @return array[]
	 */
	protected function options(): array {

		return array(
			// 按钮 text
			'license_key' => array(
				'title'       => __( 'License key', 'moredeal' ),
				'description' => sprintf( __( 'Please enter your license key. You can find your key on the %s.', 'moredeal' ), sprintf( ' <a href="%s" target="_blank">' . __( 'Documentation', 'moredeal' ) . '</a>', Plugin::pluginDocsUrl() ) ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'style'       => 'width: 600px',
				'validator'   => array(
					'trim',
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'required' ),
						'message' => __( 'The License key can not be empty.', 'moredeal' ),
					),
					array(
						'call'    => array( $this, 'licenseFormat' ),
						'message' => __( 'Invalid License Key, Please check your License Key.', 'moredeal' ),
					),
					array(
						'call'    => array( $this, 'activatingLicense' ),
						//'message' => '',
//							__( 'License key is not accepted. Make sure that you use actual key.', 'moredeal' ) .
//							' ' .
//							__( 'If you have correct key, but it is not accepted, this means that your server blocks external connections or there is any other reason that your server does not allow to connect to mdc.ai site.', 'moredeal' ) .
//							' ' .
//							__( 'Please, write about this to your hosting provider.', 'moredeal' ) .
//							' ' .
//							sprintf( __( 'If you need our help, you can view %s.', 'moredeal' ), sprintf( ' <a href="%s" target="_blank">' . __( 'Documentation', 'moredeal' ) . '</a>', Plugin::pluginGoProUrl() ) )
					),
				),
				'section'     => __( 'License', 'moredeal' ),
			),
		);
	}

	/**
	 * 检查License格式
	 *
	 * @param $value string
	 *
	 * @return bool true: 格式正确
	 */
	public function licenseFormat( string $value ): bool {

		if ( preg_match( '/[^0-9a-zA-Z_~\-]/', $value ) ) {
			return false;
		}
		if ( strlen( $value ) !== 32 && ! preg_match( '/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/', $value ) ) {
			return false;
		}

		return true;
	}

	/**
	 * 激活License
	 *
	 * @param $value string
	 *
	 * @return bool true: 激活成功
	 */
	public function activatingLicense( string $value ): bool {
		$result = SearchProductClient::getInstance()->active( $value );
		if ( $result == null ) {
			add_settings_error( 'license_key', 'license_key', __( 'Active fail, place contact your administrator', 'moredeal') );
			return false;
		}
		$result = (array) $result;
		if (array_key_exists('code' , $result) && $result['code'] != 200) {
			if (array_key_exists('msg' , $result) && !empty($result['msg'])) {
				add_settings_error( 'license_key', 'license_key', $result['msg'] );
			} else {
				add_settings_error( 'license_key', 'license_key', __( 'Active fail, place contact your administrator', 'moredeal') );
			}
			return false;
		}

		if ( array_key_exists( 'success', $result ) && ! $result['success'] ) {
			if ( array_key_exists( 'errorMessage', $result ) && ! empty( $result['errorMessage'] ) ) {
				add_settings_error( 'license_key', 'license_key', $result['errorMessage'] );
			} else {
				add_settings_error( 'license_key', 'license_key', __( 'Active fail, place contact your administrator', 'moredeal') );
			}
			return false;
		}

		if ( array_key_exists( 'success', $result ) && $result['success'] ) {
			if ( array_key_exists( 'data', $result ) && $result['data'] ) {

				return true;
			}
			add_settings_error( 'license_key', 'license_key', __( 'Active fail, place contact your administrator', 'moredeal' ) );

			return false;
		}

		add_settings_error( 'license_key', 'license_key', __( 'Active fail, place contact your administrator', 'moredeal' ) );

		return false;
	}

	/**
	 * 解绑
	 * @return void
	 */
	public function unbindLicense() {
		if ( isset( $_POST['license_unbind_tag'] ) && $_POST['license_unbind_tag'] == 'unbind' ) {
			if ( ! isset( $_POST['license'] ) || ! $_POST['license'] ) {
				error_log( $_POST['license'] );
				add_settings_error( 'license_key', 'license_key', __( 'Unbind fail, license cant ben find', 'moredeal' ) );

				return;
			}
			if ( ! isset( $_POST['domain'] ) || ! $_POST['domain'] ) {
				add_settings_error( 'license_key', 'license_key', __( 'Unbind fail, domain cant ben find', 'moredeal' ) );

				return;
			}
			$unbind = json_encode( array( 'license' => $_POST['license'], 'domain' => $_POST['domain'] ) );
			$result = SearchProductClient::getInstance()->unbind( $unbind );
			if ( $result == null ) {
				add_settings_error( 'license_key', 'license_key', __( 'Unbind fail, place contact your administrator', 'moredeal' ) );

				return;
			}
			$result = (array) $result;
			if ( array_key_exists( 'code', $result ) && $result['code'] != 200 ) {
				if ( array_key_exists( 'msg', $result ) && ! empty( $result['msg'] ) ) {
					add_settings_error( 'license_key', 'license_key', $result['msg'] );
				} else {
					add_settings_error( 'license_key', 'license_key', __( 'Unbind fail, place contact your administrator', 'moredeal' ) );
				}

				return;
			}
			if ( array_key_exists( 'success', $result ) && ! $result['success'] ) {
				if ( array_key_exists( 'errorMessage', $result ) && ! empty( $result['errorMessage'] ) ) {
					add_settings_error( 'license_key', 'license_key', $result['errorMessage'] );
				} else {
					add_settings_error( 'license_key', 'license_key', __( 'Unbind fail, place contact your administrator', 'moredeal' ) );
				}

				return;
			}
			if ( array_key_exists( 'success', $result ) && $result['success'] ) {
				if ( array_key_exists( 'data', $result ) && $result['data'] ) {
					LicenseManager::getInstance()->clearData();
					if ( $_POST['domain'] == SearchProductClient::getCurrDomain() ) {
						delete_option( 'moredeal_license' );
						Plugin::resetPro();
					}

					return;
				}
			}
			add_settings_error( 'license_key', 'license_key', __( 'Unbind fail, place contact your administrator', 'moredeal' ) );
		}
	}

}