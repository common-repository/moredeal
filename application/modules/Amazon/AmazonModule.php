<?php

namespace Moredeal\application\modules\Amazon;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\components\AffiliateParserModule;
use Moredeal\application\Plugin;

/**
 * AmazonModule class file
 */
class AmazonModule extends AffiliateParserModule {

	/**
	 * 详细信息
	 * @return array
	 */
	public function info(): array {
		return array(
			'name'        => 'Amazon',
			'description' => __( 'Adds products from Amazon.', 'moredeal' ),
		);
	}

	/**
	 * 解析类型
	 * @return string
	 */
	public function getParserType(): string {
		return self::PARSER_TYPE_PRODUCT;
	}

	/**
	 * 默认模版名称
	 * @return string
	 */
	public function defaultTemplateName(): string {
		return 'data_item';
	}

	/**
	 * 是否可更新
	 * @return bool
	 */
	public function isItemsUpdateAvailable(): bool {
		return true;
	}

	/**
	 * 是否免费
	 * @return bool
	 */
	public function isFree(): bool {
		return true;
	}

	/**
	 * @throws \Exception
	 */
	public function getUrlAssociateTagParam(): string {
		$locale             = $this->config( 'locale' );
		$localeAssociateTag = $this->config( 'associate_tag_' . $locale );
		// 如果配置了国家的associate_tag，就使用国家的associate_tag
		if ( $localeAssociateTag ) {
			return '?associate_tag_' . $locale . '=' . $localeAssociateTag;
		}

		// 如果没有配置国家的associate_tag，就使用默认的associate_tag
		return '?tag=' . $this->config( 'associate_tag' );
	}

	public function getDisclaimerText() {
		return $this->config( 'disclaimer_text' );
	}

	public function getMarketPlace() {
		return $this->config( 'locale' );
	}

}
