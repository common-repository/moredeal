<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

/**
 * AffiliateParserModule abstract class file
 */
abstract class AffiliateParserModule extends ParserModule {

	final public function isAffiliateParser(): bool {
		return true;
	}

	public function isItemsUpdateAvailable(): bool {
		return false;
	}

	public function isCouponParser(): bool {
		if ( strpos( $this->getName(), 'Coupon' ) !== false || $this->getName() == 'CJ Links' ) {
			return true;
		} else {
			return false;
		}
	}

	public function isProductParser(): bool {
		return ! $this->isCouponParser();
	}


}
