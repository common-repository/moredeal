<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\Plugin;

/**
 * ParserModuleConfig abstract class file
 */
abstract class AffiliateParserModuleConfig extends ParserModuleConfig {

	/**
	 * @throws \Exception
	 */
	public function options(): array {
		$options = array();

		if ( $this->getModuleInstance()->isItemsUpdateAvailable() ) {
			$options['ttl_items'] = array(
				'title'       => __( 'Price update', 'moredeal' ),
				'description' => __( 'Time in seconds for updating prices, availability, etc. 0 - never update', 'moredeal' ),
				'callback'    => array( $this, 'render_input' ),
				'default'     => 604800,
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => 'default',
			);
		}
		$options['update_mode'] = array(
			'title'            => __( 'Update mode', 'moredeal' ),
			'description'      => __( 'Product update mode, You can chose update by Page view or Cron', 'moredeal' ),
			'callback'         => array( $this, 'render_dropdown' ),
			'style'            => 'width: 200px',
			'dropdown_options' => array(
				'visit'      => __( 'Page view', 'moredeal' ),
				'cron'       => __( 'Cron', 'moredeal' ),
				'visit_cron' => __( 'Page view + Cron', 'moredeal' ),
			),
			'default'          => 'visit',
		);

		return array_merge( parent::options(), $options );
	}


}
