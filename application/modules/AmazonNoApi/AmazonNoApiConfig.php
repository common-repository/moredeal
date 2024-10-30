<?php

namespace Moredeal\application\modules\AmazonNoApi;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\AffiliateParserModuleConfig;

/**
 * AmazonConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2015 keywordrush.com
 */
class AmazonNoApiConfig extends AffiliateParserModuleConfig {

	public function options(): array {
		$options = array(
			'associate_tag'           => array(
				'title'       => __( 'Default Associate Tag', 'moredeal' ) . ' <span class="moredeal_required">*</span>',
				'description' => __( 'An alphanumeric token that uniquely identifies you as an Associate. To obtain an Associate Tag, refer to ', 'moredeal' ) . '<a target="_blank" href="https://docs.aws.amazon.com/AWSECommerceService/latest/DG/becomingAssociate.html">'. __('Becoming an Associate', 'moredeal') .'</a>.',
				'callback'    => array( $this, 'render_input' ),
				'validator'   => array(
					'trim',
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'required' ),
						'when'    => 'is_active',
						'message' => __( 'The "Default Associate Tag" can not be empty.', 'moredeal' ),
					),
				),
				'section'     => 'default',
				'metaboxInit' => true,
			),
			'locale'                  => array(
				'title'            => __( 'Default locale', 'moredeal' ) . ' <span class="moredeal_required">*</span>',
				'description'      => __( 'The branch/locale of Amazon. Each branch requires a separate registration in certain affiliate program.', 'moredeal' ),
				'callback'         => array( $this, 'render_dropdown' ),
				'dropdown_options' => self::getLocalesList(),
				'default'          => self::getDefaultLocale(),
				'style'            => 'width: 200px',
				'validator'        => array(
					'trim',
					array(
						'call'    => array( '\Moredeal\application\helpers\FormValidator', 'required' ),
						'when'    => 'is_active',
						'message' => __( 'The "Local" can not be empty.', 'moredeal' ),
					),
				),
				'section'          => 'default',
				'metaboxInit'      => true,
			),
		);
		foreach ( self::getLocalesList() as $locale_id => $locale_name ) {
			$options[ 'associate_tag_' . $locale_id ] = array(
				'title'       => sprintf( __( 'Associate Tag for %s locale', 'moredeal' ), $locale_name ),
				'description' =>sprintf( __( 'Type here your tracking ID for this %s Associate Tag', 'moredeal' ), $locale_name ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => '',
				'validator'   => array(
					'trim',
				),
				'metaboxInit' => true,
			);
		}

		$parent                         = parent::options();
		$parent['ttl_items']['default'] = 86400;

		return $this-> moveRequiredUp(array_merge( $parent, $options ));
	}

	/**
	 * 语言列表
	 * @return string[]
	 */
	public static function getLocalesList(): array {
		return array(
			'us' => 'US',
			'uk' => 'UK',
			'de' => 'DE',
			'jp' => 'JP',
			'cn' => 'CN',
			'fr' => 'FR',
			'it' => 'IT',
			'es' => 'ES',
			'ca' => 'CA',
			'br' => 'BR',
			'in' => 'IN',
			'mx' => 'MX',
			'au' => 'AU'
		);
	}

	/**
	 * 默认的语言
	 * @return string
	 */
	public static function getDefaultLocale(): string {
		return 'us';
	}

}
