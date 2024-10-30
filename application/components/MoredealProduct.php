<?php

namespace Moredeal\application\components;

defined( '\ABSPATH' ) || exit;

/**
 *
 */
class MoredealProduct{

	const STOCK_STATUS_IN_STOCK = 1;
	const STOCK_STATUS_OUT_OF_STOCK = - 1;
	const STOCK_STATUS_UNKNOWN = 0;

	public $code;
	public $price;
	public $priceOld;
	public $currency;
	public $currencyCode;
	public $manufacturer;
	public $category;
	public $categoryPath = array();
	public $merchant;
	public $logo;
	public $domain;
	public $rating;
	public $availability;
	public $orig_url;
	public $features = array();
	public $stock_status;
	public $group;

}
