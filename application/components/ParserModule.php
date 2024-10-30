<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

/**
 * ParserModule abstract class file
 */
abstract class ParserModule extends Module {

	const PARSER_TYPE_PRODUCT = 'PRODUCT';

	abstract public function getParserType();

	/**
	 * @throws \Exception
	 */
	public function isActive(): bool {
		if ( $this->is_active === null ) {
			if ( $this->getConfigInstance()->option( 'is_active' ) ) {
				$this->is_active = true;
			} else {
				$this->is_active = false;
			}
		}
		return $this->is_active;
	}

	final public function isParser(): bool {
		return true;
	}

}
