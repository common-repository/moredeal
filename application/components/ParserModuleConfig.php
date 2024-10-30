<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\Plugin;

/**
 * ParserModuleConfig abstract class file
 */
abstract class ParserModuleConfig extends ModuleConfig {

	public function options(): array {
		$options     = array(
			'is_active'          => array(
				'title'       => __( 'Enable module', 'moredeal' ),
				'description' => '',
				'callback'    => array( $this, 'render_checkbox' ),
				'default'     => true,
				'section'     => 'default',
				'validator'   => array(
					array(
						'call'    => array( $this, 'checkRequirements' ),
						'message' => __( 'Could not activate.', 'moredeal' ),
					),
				),
			),
			'priority'           => array(
				'title'       => __( 'Priority', 'moredeal' ),
				'description' => __( 'Priority sets order of modules in post. 0 - is the most highest priority.', 'moredeal' ),
				                 __( 'Also it applied to price sorting.', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => 10,
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => 'default',
			),
//			'tpl_title'          => array(
//				'title'       => __( 'Title', 'moredeal' ),
//				'description' => __( 'Templates may use title on data output.', 'moredeal' ),
//				'callback'    => array( $this, 'render_input' ),
//				'default'     => '',
//				'validator'   => array(
//					'trim',
//				),
//				'section'     => 'default',
//			),
		);

		return array_merge( parent::options(), $options );
	}

	/**
	 * @throws \Exception
	 */
	public function checkRequirements( $value ): bool {
		if ( $requirements = $this->getModuleInstance()->requirements() ) {
			return false;
		} else {
			return true;
		}
	}

	protected static function moveRequiredUp( array $options ): array {
		$keys = array( 'is_active' );

		foreach ( $options as $key => $option ) {
			if ( strpos( $option['title'], '*' ) ) {
				$keys[] = $key;
			}

			$options[ $key ]['title'] = str_replace( '**', '', $option['title'] );
		}

		$res = array();
		foreach ( $keys as $key ) {
			$res[ $key ] = $options[ $key ];
			unset( $options[ $key ] );
		}

		$res = array_merge( $res, $options );

		return $res;
	}

}
