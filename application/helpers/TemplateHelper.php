<?php

namespace Moredeal\application\helpers;

defined( '\ABSPATH' ) || exit;

use Moredeal\application\admin\GeneralConfig;
use Moredeal\application\admin\TemplateConfig;
use Moredeal\application\components\ModuleManager;
use Moredeal\application\components\MoredealProduct;
use Moredeal\application\Translator;

class TemplateHelper {

	/**
	 * 商家信息
	 * @var null | array
	 */
	static ?array $shop_info = null;

	/**
	 * 翻译信息
	 *
	 * @param $str
	 *
	 * @return string|null
	 */
	public static function __( $str ): ?string {
		return Translator::translate( $str );
	}

	/**
	 * @param bool $echo
	 *
	 * @return string
	 */
	public static function printRel( bool $echo = true ): string {
		if ( ! $rel = self::getRelValue() ) {
			return '';
		}

		return ' rel="' . esc_attr( $rel ) . '"';
	}

	/**
	 * 解析 URL
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public static function parseUrl( $item ): string {
		try {
			$url = $item['url'];
			if ( ! $url ) {
				return '#';
			}
			$urls     = explode( "?", $url );
			$url      = $urls[0];
			$moduleId = $item['module_id'] ?? 'Amazon';
			$module   = ModuleManager::factory( 'Amazon' );
			$tag      = $module->getUrlAssociateTagParam();

			return $url . $tag;
		} catch ( \Exception $e ) {
			return '#';
		}
	}

	/**
	 * 获取AssociateTag
	 * @return mixed
	 * @throws \Exception
	 */
	public static function getAssociateTagParam() {
		$module = ModuleManager::factory( 'Amazon' );

		return $module->getUrlAssociateTagParam();
	}

	/**
	 *
	 * @return string
	 */
	public static function getRelValue(): string {
		$rel = GeneralConfig::getInstance()->option( 'rel_attribute' );

		return join( ' ', $rel );
	}

	/**
	 * 评分
	 *
	 * @param array $item
	 * @param string $size
	 *
	 * @return void
	 */
	public static function printRating( array $item, string $size = 'default' ) {
		if ( ! $item['rating'] ) {
			return;
		}
		if ( ! in_array( $size, array( 'small', 'big', 'default' ) ) ) {
			$size = 'default';
		}

		$rating = $item['rating'] * 20;
		echo '<span class="moredeal-stars-container moredeal-stars-' . esc_attr( $size ) . ' moredeal-stars-' . esc_attr( $rating ) . '">★★★★★</span>';
	}

	public static function printRating2( array $item, string $size = 'default' ) {
		if ( ! $item['rating'] ) {
			return;
		}
		if ( ! in_array( $size, array( 'small', 'big', 'default' ) ) ) {
			$size = 'default';
		}

		$rating = $item['rating'] * 20;
		echo '<span class="stars-container moredeal-stars-' . esc_attr( $size ) . ' moredeal-stars-' . esc_attr( $rating ) . '">★★★★★</span>';
	}

	public static function printShippingType( array $item ) {
		if ( ! $item['shippingType'] ) {
			return '';
		}
		$shippingType = $item['shippingType'];
		if ( $shippingType == '1' ) {
			return esc_html( 'Amazon' );
		} else if ( $shippingType == '2' ) {
			return esc_html( 'FBA' );
		} else if ( $shippingType == '3' ) {
			return esc_html( 'FBM' );
		}

	}

	/**
	 * Ratings
	 *
	 * @param $count
	 * @param $post_id
	 *
	 * @return array
	 */
	public static function generateStaticRatings( $count, $post_id = null ): array {
		if ( ! $post_id ) {
			global $post;
			if ( ! empty( $post->ID ) ) {
				$post_id = $post->ID;
			} else {
				$post_id = $count;
			}
		}

		$ratings = array();
		mt_srand( $post_id );
		$rating = 10;
		for ( $i = 0; $i < $count; $i ++ ) {
			if ( $i <= 3 ) {
				$rand = mt_rand( 0, 6 ) / 10;
			} elseif ( $count > 9 && $i > 4 ) {
				$rand = mt_rand( 0, 3 ) / 10;
			} elseif ( $i > 8 ) {
				$rand = mt_rand( 0, 4 ) / 10;
			} else {
				$rand = mt_rand( 0, 10 ) / 10;
			}

			$rating    = round( $rating - $rand, 2 );
			$ratings[] = $rating;
		}

		return $ratings;
	}

	/**
	 * getChance
	 *
	 * @param int $position
	 * @param int $max
	 *
	 * @return int
	 */
	public static function getChance( int $position, int $max = 1 ): int {
		global $post;
		if ( ! empty( $post->ID ) ) {
			$post_id = $post->ID;
		} else {
			$post_id = time();
		}
		mt_srand( $post_id + $position );

		return mt_rand( 0, 1 );
	}

	/**
	 * 打印
	 *
	 * @param $value
	 *
	 * @return void
	 */
	public static function printProgressRing( $value ) {
		if ( $value <= 0 ) {
			return;
		}

		$p  = round( $value * 100 / 10 );
		$r1 = round( $p * 314 / 100 );
		$r2 = 314 - $r1;

		echo '<svg width="75" height="75" viewBox="0 0 120 120"><circle cx="60" cy="60" r="50" fill="none" stroke="#E1E1E1" stroke-width="12"/><circle cx="60" cy="60" r="50" transform="rotate(-90 60 60)" fill="none" stroke-dashoffset="314" stroke-dasharray="314"  stroke="dodgerblue" stroke-width="12" ><animate attributeName="stroke-dasharray" dur="3s" values="0,314;' . esc_attr( $r1 ) . ',' . esc_attr( $r2 ) . '" fill="freeze" /></circle><text x="60" y="63" fill="black" text-anchor="middle" dy="7" font-size="27">' . esc_html( $value ) . '</text></svg>';
	}

	/**
	 * 打印按钮
	 *
	 * @param bool $print
	 * @param array $item
	 * @param string $forced_text
	 *
	 * @return array|mixed|string|string[]|null
	 */
	public static function buyNowBtnText( bool $print = true, array $item = array(), string $forced_text = '', $template = '' ) {
		return self::btnText( 'btn_text_buy_now', __( 'Buy Now', 'moredeal' ), $print, $item, $forced_text, $template );
	}

	/**
	 * 按钮颜色
	 *
	 * @param $template
	 * @param $btnColor
	 *
	 * @return string
	 */
	public static function getButtonColor( $template, $btnColor ): string {
		if ( $btnColor ) {
			return $btnColor;
		}
		$tem      = substr( $template, 6 );
		$temColor = TemplateConfig::getInstance()->option( $tem . '_button_color' );
		if ( $temColor ) {
			return $temColor;
		}
		$color = GeneralConfig::getInstance()->option( 'button_color' );
		if ( $color ) {
			return $color;
		}

		return '#d9534f';
	}

	/**
	 * 按钮颜色
	 * @return string
	 */
	public static function getPriceColor( $template, $priceColor ): string {
		if ( $priceColor ) {
			return $priceColor;
		}
		$tem      = substr( $template, 6 );
		$temColor = TemplateConfig::getInstance()->option( $tem . '_price_color' );
		if ( $temColor ) {
			return $temColor;
		}
		$color = GeneralConfig::getInstance()->option( 'price_color' );
		if ( $color ) {
			return $color;
		}

		return '#000000';
	}

	/**
	 * 按钮文字
	 *
	 * @param $option_name
	 * @param $default
	 * @param bool $print
	 * @param array $item
	 * @param string $forced_text
	 *
	 * @return array|mixed|string|string[]|void
	 */
	public static function btnText( $option_name, $default, bool $print = true, array $item = array(), string $forced_text = '', $template = '' ) {

		if ( $forced_text ) {
			$text = $forced_text;
		} else {
			$tem  = substr( $template, 6 );
			$text = TemplateConfig::getInstance()->option( $tem . '_btn_text' );
			if ( ! $text ) {
				$text = GeneralConfig::getInstance()->option( $option_name );
				if ( ! $text ) {
					$text = $default;
				}
			}

		}

		$text = self::replacePatterns( $text, $item );

		if ( ! $print ) {
			return $text;
		}

		echo esc_attr( $text );
	}

	/**
	 * 替换模式
	 *
	 * @param $template
	 * @param array $item
	 *
	 * @return array|mixed|string|string[]
	 */
	private static function replacePatterns( $template, array $item ) {
		if ( ! $item ) {
			return $template;
		}
		if ( ! preg_match_all( '/%[a-zA-Z0-9_\.\,\(\)]+%/', $template, $matches ) ) {
			return $template;
		}

		$replace = array();
		foreach ( $matches[0] as $pattern ) {
			if ( stristr( $pattern, '%PRICE%' ) ) {
				if ( ! empty( $item['price'] ) && $item['currencyCode'] ) {
					$replace[ $pattern ] = TemplateHelper::formatPriceCurrency( $item['price'], $item['currencyCode'] );
				} else {
					$replace[ $pattern ] = '';
				}
				continue;
			}
			if ( stristr( $pattern, '%MERCHANT%' ) ) {
				if ( $merchant = TemplateHelper::merchantName( $item ) ) {
					$replace[ $pattern ] = $merchant;
				} else {
					$replace[ $pattern ] = '';
				}
				continue;
			}
			if ( stristr( $pattern, '%DOMAIN%' ) ) {
				if ( ! empty( $item['domain'] ) ) {
					$replace[ $pattern ] = $item['domain'];
				} else {
					$replace[ $pattern ] = TemplateHelper::merchantName( $item );
				}
				continue;
			}
			if ( stristr( $pattern, '%STOCK_STATUS%' ) ) {
				$replace[ $pattern ] = TemplateHelper::getStockStatusStr( $item );
			}
		}

		return str_ireplace( array_keys( $replace ), array_values( $replace ), $template );
	}

	/**
	 * 格式化单位
	 *
	 * @param $price
	 * @param $currencyCode
	 * @param string $before_symbol
	 * @param string $after_symbol
	 *
	 * @return string
	 */
	public static function formatPriceCurrency( $price, $currencyCode, string $before_symbol = '', string $after_symbol = '' ): string {
		if ( ! $price ) {
			return '';
		}
		$decimal_sep  = __( 'number_format_decimal_point', 'moredeal' );
		$thousand_sep = __( 'number_format_thousands_sep', 'moredeal' );
		if ( $decimal_sep == 'number_format_decimal_point' ) {
			$decimal_sep = null;
		}
		if ( $thousand_sep == 'number_format_thousands_sep' ) {
			$thousand_sep = null;
		}

		return CurrencyHelper::getInstance()->currencyFormat( $price, $currencyCode, $thousand_sep, $decimal_sep, $before_symbol, $after_symbol );
	}

	/**
	 * 库存状态
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public static function getStockStatusClass( array $item ): string {
		if ( ! isset( $item['stock_status'] ) ) {
			return '';
		}
		if ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_IN_STOCK ) {
			return 'instock';
		} elseif ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_OUT_OF_STOCK ) {
			return 'outofstock';
		} elseif ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_UNKNOWN ) {
			return 'unknown';
		}

		return '';
	}

	/**
	 * 库存状态
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	public static function getStockStatusStr( array $item ): string {
		if ( ! isset( $item['stock_status'] ) ) {
			return '';
		}

		$show_status = GeneralConfig::getInstance()->option( 'show_stock_status' );
		if ( $show_status == 'hide_status' ) {
			return '';
		} elseif ( $show_status == 'show_out_of_stock' && $item['stock_status'] == MoredealProduct::STOCK_STATUS_IN_STOCK ) {
			return '';
		} elseif ( $show_status == 'show_in_stock' && $item['stock_status'] == MoredealProduct::STOCK_STATUS_OUT_OF_STOCK ) {
			return '';
		}

		if ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_IN_STOCK ) {
			return TemplateHelper::__( 'in stock' );
		} elseif ( $item['stock_status'] == MoredealProduct::STOCK_STATUS_OUT_OF_STOCK ) {
			return TemplateHelper::__( 'out of stock' );
		} else {
			return '';
		}
	}


	/**
	 * @param $lastUpdate
	 * @param bool $time
	 *
	 * @return string
	 */
	public static function getLastUpdateFormatted( $lastUpdate, bool $time = true ): string {
		return self::dateFormatFromGmt( $lastUpdate, $time );
	}

	/**
	 * 时间格式转化
	 *
	 * @param $date
	 * @param bool $time
	 *
	 * @return string formatted date
	 */
	public static function dateFormatFromGmt( $date, bool $time = true ): string {
		$format = get_option( 'date_format' );
		if ( $time ) {
			$format .= ' ' . get_option( 'time_format' );
		}

		// last update date stored in gmt, convert into local time
		$timestamp = strtotime( get_date_from_gmt( $date ) );

		return date_i18n( $format, $timestamp );
	}

	/**
	 * 输出 Amazon 的更新时间描述
	 * @return void
	 */
	public static function printAmazonDisclaimer() {

		echo '<i class="moredeal-ico-info-circle moredeal-tip" data-title="' . self::getAmazonDisclaimer() . '"></i>'; // phpcs:ignore
	}

	public static function printColumnTip( $index, $params ): void {
		if ( array_key_exists( 'grid_' . ( $index + 1 ) . '_column_tip', $params ) && $params[ 'grid_' . ( $index + 1 ) . '_column_tip' ] ) {
			$tip = $params[ 'grid_' . ( $index + 1 ) . '_column_tip' ];
		} else {
			$tip = TemplateConfig::getInstance()->option( 'grid_' . ( $index + 1 ) . '_column_tip' );
		}
		$title = TemplateConfig::getInstance()->option( 'grid_' . ( $index + 1 ) . '_column_text' );
		if ( empty( $tip ) ) {
			$tip = $title;
		}
		echo '<i class="moredeal-ico-info-circle title-tip" data-title="' . $tip . '" ></i>'; // phpcs:ignore
	}


	/**
	 * Amazon 更新时间 tip 描述
	 * @return string
	 */
	public static function getAmazonDisclaimer(): string {
		$amazon = ModuleManager::getInstance()->AmazonModule();
		if ( $disclaimer_text = $amazon->getDisclaimerText() ) {
			return $disclaimer_text;
		} else {
			return __( 'As an Amazon associate I earn from qualifying purchases.', 'moredeal' ) . ' ' . __( 'Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on Amazon at the time of purchase will apply to the purchase of this product.', 'moredeal' );
		}
	}

	/**
	 *
	 * @param string $string 被截断的字符串
	 * @param int $length 字符串长度为多少时候进行截断
	 * @param string $etc 截断后的字符串后缀
	 * @param string $charset 字符串编码
	 * @param bool $break_words 是否截断单词
	 * @param bool $middle 是否截断中间的字符串
	 *
	 * @return mixed|string $string
	 */
	public static function truncate( string $string, int $length = 80, string $etc = '...', string $charset = 'UTF-8', bool $break_words = false, bool $middle = false ) {
		if ( $length == 0 ) {
			return '';
		}

		if ( mb_strlen( $string, 'UTF-8' ) > $length ) {
			$length -= min( $length, mb_strlen( $etc, 'UTF-8' ) );
			if ( ! $break_words && ! $middle ) {
				$string = preg_replace( '/\s+?(\S+)?$/', '', mb_substr( $string, 0, $length + 1, $charset ) );
			}
			if ( ! $middle ) {
				return mb_substr( $string, 0, $length, $charset ) . $etc;
			} else {
				return mb_substr( $string, 0, $length / 2, $charset ) . $etc . mb_substr( $string, - $length / 2, $charset );
			}
		} else {
			return $string;
		}
	}

	/**
	 * 展示图片信息
	 *
	 * @param array $item 商品信息
	 * @param int $max_width 图片最大宽度
	 * @param int $max_height 图片最大高度
	 * @param array $params 图片参数
	 *
	 * @return void
	 */
	public static function displayImage( array $item, int $max_width, int $max_height, array $params = array() ) {
		if ( ! isset( $item['img'] ) ) {
			return;
		}

		if ( isset( $item['title'] ) ) {
			$params['alt'] = $item['title'];
		}

		$params['src'] = self::getOptimizedImage( $item, $max_width, $max_height );

		if ( $sizes = self::getImageSizesRatio( $item, $max_width, $max_height ) ) {
			$params = array_merge( $params, $sizes );
		}
		echo '<img ' . self::buildTagParams( $params ) . ' />'; // phpcs:ignore
	}

	/**
	 * 展示图片信息
	 *
	 * @param array $item 商品信息
	 * @param int $max_width 图片最大宽度
	 * @param int $max_height 图片最大高度
	 * @param array $params 图片参数
	 *
	 * @return void
	 */
	public static function displayImageWidth( array $item, int $max_width, int $max_height, int $width, array $params = array() ) {
		if ( ! isset( $item['img'] ) ) {
			return;
		}

		if ( isset( $item['title'] ) ) {
			$params['alt'] = $item['title'];
		}

		$params['src'] = self::getOptimizedImage( $item, $max_width, $max_height );

		if ( $sizes = self::getImageSizesRatio( $item, $max_width, $max_height ) ) {
			$params = array_merge( $params, $sizes );
		}
		echo '<img ' . self::buildTagParams( $params ) . ' style="max-width: ' . $width . 'px; height: auto" />'; // phpcs:ignore
	}

	/**
	 * 处理图片
	 *
	 * @param array $item
	 * @param $max_width
	 * @param $max_height
	 *
	 * @return mixed
	 */
	public static function getOptimizedImage( array $item, $max_width, $max_height ) {

		if ( $item['module_id'] == 'Amazon' && strpos( $item['img'], 'https://m.media-amazon.com' ) !== false ) {
			if ( ! isset( $item['extra']['primaryImages'] ) ) {
				return $item['img'];
			}

			if ( $max_height <= 160 ) {
				return $item['extra']['primaryImages']['Medium']['URL'];
			} elseif ( $max_height <= 75 ) {
				return $item['extra']['primaryImages']['Small']['URL'];
			} else {
				return $item['img'];
			}
		}

		return $item['img'];
	}

	/**
	 * 获取图片的宽高比
	 *
	 * @param array $item
	 * @param int $max_width
	 * @param int $max_height
	 *
	 * @return array
	 */
	public static function getImageSizesRatio( array $item, int $max_width, int $max_height ): array {
		if ( $item['module_id'] == 'Amazon' && strpos( $item['img'], 'https://m.media-amazon.com' ) !== false ) {
			if ( ! isset( $item['extra']['primaryImages'] ) ) {
				return array();
			}

			$width  = $item['extra']['primaryImages']['Large']['Width'];
			$height = $item['extra']['primaryImages']['Large']['Height'];

			if ( ! $max_width ) {
				$max_width = $width;
			}
			if ( ! $max_height ) {
				$max_height = $height;
			}

			$ratio = $width / $height;

			if ( $ratio > 1 && $width > $max_width ) {
				return array( 'width' => round( $max_width ), 'height' => round( $max_width / $ratio ) );
			} else {
				return array( 'width' => round( $max_height * $ratio ), 'height' => round( $max_height ) );
			}
		}

		return array();
	}


	/**
	 * 数组排序
	 *
	 * @param array $data
	 * @param string $order
	 * @param string $field
	 *
	 * @return array
	 */
	public static function sortByPrice( array $data, string $order = 'asc', string $field = 'price' ): array {

		foreach ( $data as $key => $row ) {
			$data[ $key ] = (array) $row;
		}

		if ( ! in_array( $order, array( 'asc', 'desc' ) ) ) {
			$order = 'asc';
		}

		if ( ! in_array( $field, array( 'price', 'discount' ) ) ) {
			$field = 'price';
		}

		// convert all prices to one currency
		$currency_codes = array();
		foreach ( $data as $item ) {
			if ( empty( $item['currencyCode'] ) ) {
				continue;
			}

			if ( ! isset( $currency_codes[ $item['currencyCode'] ] ) ) {
				$currency_codes[ $item['currencyCode'] ] = 1;
			} else {
				$currency_codes[ $item['currencyCode'] ] ++;
			}
		}

		arsort( $currency_codes );
		$base_currency = key( $currency_codes );
		foreach ( $data as $key => $d ) {
			$rate = 1;
			if ( ! empty( $d['currencyCode'] ) && $d['currencyCode'] != $base_currency ) {
				$rate = CurrencyHelper::getCurrencyRate( $d['currencyCode'], $base_currency );
			}
			if ( ! $rate ) {
				$rate = 1;
			}

			if ( isset( $d['price'] ) ) {
				if ( $field == 'discount' ) {
					if ( ! empty( $d['priceOld'] ) ) {
						$data[ $key ]['converted_price'] = (float) ( $d['priceOld'] - $d['price'] ) * $rate;
					} else {
						$data[ $key ]['converted_price'] = 0.00001;
					}
				} else {
					$data[ $key ]['converted_price'] = (float) $d['price'] * $rate;
				}
			} else {
				$data[ $key ]['converted_price'] = 0;
				$data[ $key ]['price']           = 0;
				if ( $field == 'discount' ) {
					$data[ $key ]['converted_price'] = 99999999999;
				}
			}
		}

		// sort by price and priority
		usort( $data, function ( $a, $b ) {
			if ( ! $a['converted_price'] ) {
				return 1;
			}

			if ( ! $b['converted_price'] ) {
				return - 1;
			}

			return ( $a['converted_price'] < $b['converted_price'] ) ? - 1 : 1;
		} );
		if ( $order == 'desc' ) {
			$data = array_reverse( $data );
		}

		return $data;
	}

	/**
	 * 调节亮度
	 *
	 * @param $hexCode
	 * @param $adjustPercent
	 *
	 * @return string
	 */
	public static function adjustBrightness( $hexCode, $adjustPercent ): string {
		$hexCode = ltrim( $hexCode, '#' );

		if ( strlen( $hexCode ) == 3 ) {
			$hexCode = $hexCode[0] . $hexCode[0] . $hexCode[1] . $hexCode[1] . $hexCode[2] . $hexCode[2];
		}

		$hexCode = array_map( 'hexdec', str_split( $hexCode, 2 ) );

		foreach ( $hexCode as & $color ) {
			$adjustableLimit = $adjustPercent < 0 ? $color : 255 - $color;
			$adjustAmount    = ceil( $adjustableLimit * $adjustPercent );

			$color = str_pad( dechex( $color + $adjustAmount ), 2, '0', STR_PAD_LEFT );
		}

		return '#' . implode( $hexCode );
	}

	/**
	 * 获取卖家名称 这里默认用 domain 字段，元数据中的 sellerNameLow 字段
	 *
	 * @param array $item
	 * @param bool $print
	 *
	 * @return mixed|string
	 */
	public static function merchantName( array $item, bool $print = false ) {
		if ( ! empty( $item['domain'] ) ) {
			if ( $item['domain'] == 'AmazonNoApi' ) {
				return 'Amazon';
			} else {
				$name = ucfirst( $item['domain'] );
			}
		} elseif ( ! empty( $item['merchant'] ) ) {
			$name = $item['merchant'];
		} else {
			$name = '';
		}
		if ( $print ) {
			echo esc_html( $name );
		}

		return $name;
	}

	/**
	 * 商户信息
	 *
	 * @param array $item
	 *
	 * @return mixed|string
	 */
	public static function getShopInfo( array $item ) {
		if ( ! isset( $item['domain'] ) ) {
			return '';
		}
		$domain = $item['domain'];
		if ( self::$shop_info === null ) {
			$merchants = GeneralConfig::getInstance()->option( 'merchants' );
			if ( ! $merchants ) {
				$merchants = array();
			}
			foreach ( $merchants as $merchant ) {
				self::$shop_info[ $merchant['name'] ] = $merchant['shop_info'];
			}
		}

		return self::$shop_info[ strtolower( $domain ) ] ?? '';
	}

	/**
	 * 输出商户信息
	 *
	 * @param array $item
	 * @param array $p
	 *
	 * @return void
	 */
	public static function printShopInfo( array $item, array $p = array() ) {
		if ( ! $shop_info = self::getShopInfo( $item ) ) {
			return;
		}
		$params = array(
			'data-toggle'    => 'moredeal-popover',
			'data-html'      => 'true',
			'data-placement' => 'left',
			'data-title'     => self::merchantName( $item ),
			'data-content'   => $shop_info,
			'tabindex'       => '0',
			'data-trigger'   => 'focus',
		);
		$params = array_merge( $params, $p );
		self::displayInfoIcon( $params );
	}

	/**
	 * 输出商户信息到 html
	 *
	 * @param array $params
	 * @param string $text
	 *
	 * @return void
	 */
	public static function displayInfoIcon( array $params = array(), $text = '' ) {
		echo '<i class="moredeal-ico-info-circle" ' . self::buildTagParams( $params ) . '>';
		if ( $text ) {
			echo ' <small style="cursor: pointer;">' . esc_html( $text ) . '</small>';
		}
		echo '</i>';
	}

	/**
	 * 构建标签参数
	 *
	 * @param array $params
	 *
	 * @return string
	 */
	public static function buildTagParams( array $params = array() ): string {
		$res = '';
		$i   = 0;
		foreach ( $params as $key => $value ) {
			if ( $i > 0 ) {
				$res .= ' ';
			}
			$res .= esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			$i ++;
		}

		return $res;
	}

	/**
	 * esc html
	 *
	 * @param $str
	 *
	 * @return void
	 */
	public static function esc_html_e( $str ) {
		echo esc_html( Translator::translate( $str ) );
	}

	/**
	 * @param $end_time_gmt
	 * @param $return_array
	 *
	 * @return array|string
	 */
	public static function getTimeLeft( $end_time_gmt, $return_array = false ) {
		$current_time = strtotime( gmdate( "M d Y H:i:s" ) );
		$timeleft     = strtotime( $end_time_gmt ) - $current_time;
		if ( $timeleft < 0 ) {
			return '';
		}

		$days_left  = floor( $timeleft / 86400 );
		$hours_left = floor( ( $timeleft - $days_left * 86400 ) / 3600 );
		$min_left   = floor( ( $timeleft - $days_left * 86400 - $hours_left * 3600 ) / 60 );
		if ( $return_array ) {
			return array(
				'days'  => $days_left,
				'hours' => $hours_left,
				'min'   => $min_left,
			);
		}

		if ( $days_left ) {
			return $days_left . __( 'd', 'moredeal' ) . ' ';
		} elseif ( $hours_left ) {
			return $hours_left . __( 'h', 'moredeal' ) . ' ';
		} elseif ( $min_left ) {
			return $min_left . __( 'm', 'moredeal' );
		} else {
			return '<1' . __( 'm', 'moredeal' );
		}
	}

	/**
	 * @param $datetime
	 * @param string $type
	 * @param string $separator
	 *
	 * @return string
	 */
	public static function formatDatetime( $datetime, string $type = 'mysql', string $separator = ' ' ): string {
		if ( 'mysql' == $type ) {
			return mysql2date( get_option( 'date_format' ), $datetime ) . $separator . mysql2date( get_option( 'time_format' ), $datetime );
		} else {
			return date_i18n( get_option( 'date_format' ), $datetime ) . $separator . date_i18n( get_option( 'time_format' ), $datetime );
		}
	}

	/**
	 * @param $timestamp
	 * @param bool $gmt
	 *
	 * @return string
	 */
	public static function formatDate( $timestamp, bool $gmt = false ): string {
		return date_i18n( get_option( 'date_format' ), $timestamp, $gmt );
	}

	public static function isCashbackTrakerActive(): bool {
		if ( class_exists( '\CashbackTracker\application\Plugin' ) ) {
			return true;
		} else {
			return false;
		}
	}

	public static function getCashbackStr( array $product ): string {
//		if ( GeneralConfig::getInstance()->option( 'cashback_integration' ) != 'enabled' ) {
//			return '';
//		}

		if ( ! self::isCashbackTrakerActive() ) {
			return '';
		}

		return \CashbackTracker\application\components\DeeplinkGenerator::getCashbackStrByUrl( $product['url'] );
	}

	/**
	 * 打印商品 feature
	 * @param array $product
	 * @param $featureNumber
	 *
	 * @return void
	 */
	public static function printDescription( array $product, $featureNumber ): void {
		$description = $product['description'];
		if ( ! $description ) {
			return;
		}
		$features     = explode( ";;;;", $description );
		$featureArray = array();
		if (count($features) == 1 && str_starts_with( $features[0], '[description]' )) {
			$feature = str_replace( '[description]', '', $features[0] );
			$featureArray[0] = $feature;
		} else {
			foreach ( $features as $feature ) {
				$feature = trim( $feature );
				if ( ! $feature || str_starts_with( $feature, '[description]' ) ) {
					continue;
				}
				if ( str_starts_with( $feature, '[fivePoint]' ) ) {
					$feature = str_replace( '[fivePoint]', '', $feature );
				}
				$featureArray[] = $feature;
			}
		}

		if ( $featureNumber ) {
			$num = $featureNumber;
		} else {
			$num = TemplateConfig::getInstance()->option( 'card_feature_num' );
		}
		if ( ! $num ) {
			$num = 1000;
		}
		if ( $num == 0 ) {
			return;
		}
		if ( $num >= count( $featureArray ) ) {
			$num = count( $featureArray );
		}
		$featureArray = array_slice( $featureArray, 0, $num );
		echo '<ul class="moredeal-features">';
		foreach ( $featureArray as $feature ) {
			echo '<li class="moredeal-feature ">' . esc_html( $feature ) . '</li>';
		}
		echo '</ul>';
	}

	/**
	 * feature 排序文本
	 * @param $sortText
	 *
	 * @return string
	 */
	public static function getCardFeatureSortText( $sortText ): string {
		if ( ! empty( $sortText ) ) {
			return $sortText;
		}
		$text = TemplateConfig::getInstance()->option( 'card_feature_sort_text' );
		if ( ! $text ) {
			$text = 'Bestseller No. ';
		}

		return $text;
	}

	/**
	 * feature 排序颜色
	 * @param $sortColor
	 *
	 * @return string
	 */
	public static function getCardFeatureSortColor( $sortColor ): string {
		if ( ! empty( $sortColor ) ) {
			return $sortColor;
		}
		$color = TemplateConfig::getInstance()->option( 'card_feature_sort_color' );
		if ( ! $color ) {
			$color = '#e47911';
		}

		return $color;
	}

	/**
	 * 处理 商品标题
	 * @param $item
	 * @param $len
	 *
	 * @return string
	 */
	public static function printTitle( $item, $len ): string {
		$title = $item['title'];
		if ( ! $title ) {
			return '';
		}
		if ( $len && strlen( $title ) > $len ) {
			$title = mb_substr( $title, 0, $len, 'utf-8' ) . '...';
		}

		return esc_html( $title );
	}

	/**
	 * 获取列标题
	 * @param $index
	 * @param $params
	 *
	 * @return string
	 */
	public static function getColumnText( $index, $params ): string {
		if ( array_key_exists( 'grid_' . ( $index + 1 ) . '_column_text', $params ) ) {
			return $params[ 'grid_' . ( $index + 1 ) . '_column_text' ];
		}
		$column = TemplateConfig::getInstance()->option( 'grid_' . ( $index + 1 ) . '_column_text' );
		if ( ! $column ) {
			return '';
		}

		return esc_html( __( $column, 'moredeal' ) );

	}

	/**
	 * 获取列标题背景颜色
	 * @param $params
	 *
	 * @return string
	 */
	public static function getHeaderColumnColor( $params ): string {
		if ( array_key_exists( 'grid_head_color', $params ) && $params['grid_head_color'] ) {
			return $params['grid_head_color'];
		}
		$color = TemplateConfig::getInstance()->option( 'grid_head_color' );
		if ( ! $color ) {
			return '#55befb';
		}

		return esc_attr( $color );
	}

	/**
	 * 获取数据
	 * @param $data
	 * @param $limit
	 *
	 * @return array
	 */
	public static function getData( $data, $limit ): array {

		if ( ! $data ) {
			return array();
		}
		if ( $limit ) {
			$num = $limit;
		} else {
			$num = 10000000;
		}
		if ( $num > count( $data ) ) {
			$num = count( $data );
		}

		return array_slice( $data, 0, $num );
	}

	/**
	 * 获取 limit 数据
	 * @param $data
	 * @param $limit
	 *
	 * @return array
	 */
	public static function getLimitData( $data, $limit ): array {
		if ( ! $data ) {
			return array();
		}

		if ( $limit ) {
			$num = $limit;
		} else {
			$num = TemplateConfig::getInstance()->option( 'grid_product_num' );
		}
		if ( $num >= count( $data ) ) {
			$num = count( $data );
		}

		return array_slice( $data, 0, $num );
	}

	/**
	 * 获取比较数据
	 * @param $data
	 * @param $limit
	 *
	 * @return array
	 */
	public static function getComparisonData( $data, $limit ): array {
		if ( ! $data ) {
			return array();
		}
		if ( $limit ) {
			$num = $limit;
		} else {
			$num = TemplateConfig::getInstance()->option( 'comparison_product_num' );
		}
		if ( $num > count( $data ) ) {
			$num = count( $data );
		}

		return array_slice( $data, 0, $num );

	}

	/**
	 * 标记比较数据
	 * @param $data
	 *
	 * @return array
	 */
	public static function markComparisonData( $data ): array {
		// best price
		$bestPrice = array_column( $data, 'price' );
		array_multisort( $bestPrice, SORT_ASC, $data );
		$bastPriceItem = $data[0];
		foreach ( $data as $datum => $item ) {
			if ( ! $item['price'] || $item['stock_status'] != "1" ) {
				continue;
			}
			$data[$datum]['bestPrice'] = true;
			$bastPriceItem     = $item;
			break;
		}

		// bastChoice
		$globalScore = array_column( $data ?? array(), 'globalScore' );
		array_multisort( $globalScore, SORT_NUMERIC, SORT_DESC, $data );
		$data[0]['bestChoice'] = true;
		$bestChoiceItem        = $data[0];

		if ( $bastPriceItem['code'] == $bestChoiceItem['code'] ) {
			$data[0]['bestPrice'] = false;
		}
		return $data;
	}

	/**
	 * 获取比较数据需要比较的属性
	 * @return array
	 */
	public static function getComparisonRows(): array {
		$rows = TemplateConfig::getInstance()->option( 'comparison_row_attribute' );
		if ( ! $rows ) {
			return array();
		}

		return $rows;
	}

	/**
	 * 获取比较数据需要比较的属性描述
	 * @param $row
	 *
	 * @return string
	 */
	public static function getComparisonRowText( $row ): string {
		return __( TemplateConfig::getComparisonRows()[ $row ], 'moredeal' );
	}

	/**
	 * 展示商品图
	 * @param $data
	 * @param $row
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displayPreview( $data, $row, $params ) {
		if ( $row != 'preview' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {
			if ( $item['img'] ) {
				$it = array(
					'post_id'         => $item['post_id'],
					'product_code'    => $item['code'],
					'category_id'     => $item['category_id'],
					's_id'            => $item['s_id'],
					'trace_id'        => $item['trace_id'],
					'view_id'         => $item['view_idx'],
					'search_location' => $item['search_location'],
				);
				self::displayBastPreview( $item, 'top', $params, $it );
				echo '<a ' . self::printRel() . ' target="_blank" href="' . esc_url_raw( self::parseUrl( $item ) ) . '" data-product=' . json_encode( $it ) . ' >';
				self::displayImage( $item, 300, 300 );
				echo '</a>';
				echo '</div>';
			} else {
				self::displayBastPreview( $item, '', $params );
				echo '</div>';
			}
		}
	}

	/**
	 * 展示商品标题
	 * @param $data
	 * @param $row
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displayTitle( $data, $row, $params ) {
		if ( $row != 'title' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {
			if ( $item['title'] ) {
				$it = array(
					'post_id'         => $item['post_id'],
					'product_code'    => $item['code'],
					'category_id'     => $item['category_id'],
					's_id'            => $item['s_id'],
					'trace_id'        => $item['trace_id'],
					'view_id'         => $item['view_idx'],
					'search_location' => $item['search_location'],
				);
				self::displayBast( $item, '', $params );
				$color = '#448FD5';
				echo '<a ' . self::printRel() . ' target="_blank" style="color: ' . $color . '" href="' . esc_url_raw( self::parseUrl( $item ) ) . '" data-product=' . json_encode( $it ) . '>' . esc_html( $item['title'] ) . '</a>';
				echo '</div>';
			} else {
				self::displayBast( $item, '', $params );
				echo '</div>';
			}
		}
	}

	/**
	 * 展示商品评分
	 * @param $data
	 * @param $row
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displayRating( $data, $row, $params ) {
		if ( $row != 'rating' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {
			if ( $item['rating'] ) {
				self::displayBast( $item, '', $params );
				echo '<div class="moredeal-mb5">';
				self::printRating( $item );
				echo '</div>';
				echo '</div>';
			} else {
				self::displayBast( $item, '', $params );
				echo '</div>';
			}
		}
	}

	/**
	 * 展示商品评论数
	 * @param $data
	 * @param $row
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displayReview( $data, $row, $params ) {
		if ( $row != 'review' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {
			if ( $item['commentCount'] ) {
				self::displayBast( $item, '', $params );
				echo '<span>' . esc_html( $item['commentCount'] ) . '</span></div>';
			} else {
				self::displayBast( $item, '', $params );
				echo '</div>';
			}
		}
	}

	/**
	 * 展示商品月销量
	 * @param $data
	 * @param $row
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displaySalesCount( $data, $row, $params ) {
		if ( $row != 'monthlySales' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {
			if ( $item['salesCount'] ) {
				self::displayBast( $item, '', $params );
				echo '<span>' . esc_html( $item['salesCount'] ) . '</span></div>';
			} else {
				self::displayBast( $item, '', $params );
				echo '</div>';
			}
		}
	}

	/**
	 * 展示商品价格
	 * @param $data
	 * @param $row
	 * @param $template
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displayPrice( $data, $row, $template, $params ) {
		if ( $row != 'price' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {

			self::displayBast( $item, '', $params );
			if ( $item['price'] && $item['stock_status'] == '1' ) {
				$color = '#7A7A7A';
				if ( array_key_exists( 'bestChoice', $item ) && $item['bestChoice'] ) {
					$color = self::getBestChoicePriceColor( $params );
				} else if ( array_key_exists( 'bestPrice', $item ) && $item['bestPrice'] ) {
					$color = self::getBestPricePriceColor( $params );
				}
				echo '<span style="color: ' . esc_attr( $color ) . '">' . wp_kses( self::formatPriceCurrency( $item['price'], $item['currencyCode'], '<span class="moredeal-currency">', '</span>' ), array( 'span' => array( 'class' ) ) ) . '</span></div>';
			} else {
				echo '<span>' . esc_html( __( 'Out of stock', 'moredeal' ) ) . '</span></div>';
			}
		}
	}

	/**
	 * 展示商品优惠
	 * @param $data
	 * @param $row
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displayPrimeBenefits( $data, $row, $params ) {
		if ( $row != 'primeBenefits' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {
			self::displayBast( $item, '', $params );
			if ( $item['shippingType'] && $item['shippingType'] != "3" ) {
				$it = array(
					'post_id'         => $item['post_id'],
					'product_code'    => $item['code'],
					'category_id'     => $item['category_id'],
					's_id'            => $item['s_id'],
					'trace_id'        => $item['trace_id'],
					'view_id'         => $item['view_idx'],
					'search_location' => $item['search_location'],
				);
				echo '<span class="has-prime"><a ' . self::printRel() . ' target="_blank" href="' . esc_url_raw( self::parseUrl( $item ) ) . '" data-product=' . json_encode( $it ) . '></a></span></div>';
			} else {
				echo '<span class="no-prime">' . esc_html( __( 'No Prime', 'moredeal' ) ) . '</span></div>';
			}
		}
	}

	/**
	 * 展示 see deal 按钮
	 * @param $data
	 * @param $row
	 * @param $template
	 * @param $params
	 *
	 * @return string|void
	 */
	public static function displaySeeDeal( $data, $row, $template, $params ) {
		if ( $row != 'see' ) {
			return '';
		}
		foreach ( $data as $index => $item ) {
			$it = array(
				'post_id'         => $item['post_id'],
				'product_code'    => $item['code'],
				'category_id'     => $item['category_id'],
				's_id'            => $item['s_id'],
				'trace_id'        => $item['trace_id'],
				'view_id'         => $item['view_idx'],
				'search_location' => $item['search_location'],
			);
			self::displayBast( $item, 'bottom', $params );
			echo '<div class="moredeal-row-btn" >';
			echo '<a class="submits-base" ' . self::printRel() . ' target="_blank" href="' . esc_url_raw( self::parseUrl( $item ) ) . '" data-product=' . json_encode( $it ) . ' ';
			echo 'style="color: #fff; background-color: ' . esc_attr( self::getButtonColor( $template, $params['btn_color'] ) ) . '; border-color: ' . esc_attr( self::getButtonColor( $template, $params['btn_color'] ) ) . ';" >';
			echo '<span>' . self::buyNowBtnText( true, $item, $params['btn_text'], $template ) . '</span>';
			echo '</a></div></div>';
		}
	}

	/**
	 * 展示 bestChoice 和 bestPrice 标签
	 * @param $item
	 * @param $location
	 * @param $params
	 *
	 * @return void
	 */
	public static function displayBast( $item, $location, $params ): void {
		if ( array_key_exists( 'bestChoice', $item ) && $item['bestChoice'] ) {
			$text  = self::getBestChoiceText( $params );
			$color = self::getBestChoiceColor( $params );
			self::bestDisplay( $text, $color, $location );
		} else if ( array_key_exists( 'bestPrice', $item ) && $item['bestPrice'] ) {
			$text  = self::getBestPriceText( $params );
			$color = self::getBestPriceColor( $params );
			self::bestDisplay( $text, $color, $location );
		} else {
			echo '<div class="com_data data_product" >';
		}
	}

	/**
	 * 展示 bestChoice 和 bestPrice 标
	 * @param $text
	 * @param $color
	 * @param $location
	 *
	 * @return void
	 */
	public static function bestDisplay( $text, $color, $location = '' ): void {
		if ( $location == 'top' ) {
			echo '<div class="data_image" style="border: 1px solid ' . $color . '; border-bottom: none;" >';
			echo '<div class="ribbon" style="background-color: ' . $color . '">' . esc_html( __( $text, 'moredeal' ) ) . '</div>';
		} else if ( $location == 'bottom' ) {
			echo '<div class="data_image" style="border: 1px solid ' . $color . '; border-top: none; " >';
		} else {
			// opacity: 0.2; background-color: '.$color.'
			echo '<div class="data_image" style="border: 1px solid ' . $color . '; border-bottom: none; border-top: none; " >';
		}
	}

	/**
	 * 展示 bestChoice 和 bestPrice 标签
	 * @param $item
	 * @param $location
	 * @param $params
	 *
	 * @return void
	 */
	public static function displayBastPreview( $item, $location, $params, $it ): void {
		if ( array_key_exists( 'bestChoice', $item ) && $item['bestChoice'] ) {
			$text  = self::getBestChoiceText( $params );
			$color = self::getBestChoiceColor( $params );
			self::bestPreviewDisplay( $text, $color, $it, $location );
		} else if ( array_key_exists( 'bestPrice', $item ) && $item['bestPrice'] ) {
			$text  = self::getBestPriceText( $params );
			$color = self::getBestPriceColor( $params );
			self::bestPreviewDisplay( $text, $color, $it, $location );
		} else {
			echo '<div class="com_data data_product" data-product=' . json_encode( $it ) . '>';
		}
	}

	/**
	 * 展示 bestChoice 和 bestPrice 标
	 * @param $text
	 * @param $color
	 * @param $location
	 *
	 * @return void
	 */
	public static function bestPreviewDisplay( $text, $color, $it, $location = '' ): void {
		if ( $location == 'top' ) {
			echo '<div class="data_image" style="border: 1px solid ' . $color . '; border-bottom: none;" data-product=' . json_encode( $it ) . '>';
			echo '<div class="ribbon" style="background-color: ' . $color . '">' . esc_html( __( $text, 'moredeal' ) ) . '</div>';
		} else if ( $location == 'bottom' ) {
			echo '<div class="data_image" style="border: 1px solid ' . $color . '; border-top: none; " data-product=' . json_encode( $it ) . '>';
		} else {
			// opacity: 0.2; background-color: '.$color.'
			echo '<div class="data_image" style="border: 1px solid ' . $color . '; border-bottom: none; border-top: none; " data-product=' . json_encode( $it ) . '>';
		}
	}

	/**
	 * BestChoice 文本
	 * @param $params
	 *
	 * @return string
	 */
	public static function getBestChoiceText( $params ): string {
		if ( array_key_exists( 'best_choice_text', $params ) && $params['best_choice_text'] ) {
			return $params['best_choice_text'];
		}
		$text = TemplateConfig::getInstance()->option( 'comparison_best_choice_text' );
		if ( ! $text ) {
			$text = 'Best Choice';
		}

		return $text;
	}

	/**
	 * BestChoice 颜色
	 * @param $params
	 *
	 * @return string
	 */
	public static function getBestChoiceColor( $params ): string {
		if ( array_key_exists( 'best_choice_color', $params ) && $params['best_choice_color'] ) {
			return $params['best_choice_color'];
		}
		$color = TemplateConfig::getInstance()->option( 'comparison_best_choice_color' );
		if ( ! $color ) {
			$color = '#256AAF';
		}

		return $color;
	}

	/**
	 * BestChoice 价格颜色
	 * @param $params
	 *
	 * @return string
	 */
	public static function getBestChoicePriceColor( $params ): string {
		if ( array_key_exists( 'best_choice_price_color', $params ) && $params['best_choice_price_color'] ) {
			return $params['best_choice_price_color'];
		}
		$color = TemplateConfig::getInstance()->option( 'comparison_best_choice_price_color' );
		if ( ! $color ) {
			$color = '#256AAF';
		}

		return $color;
	}

	/**
	 * BestPrice 文本
	 * @param $params
	 *
	 * @return string
	 */
	public static function getBestPriceText( $params ): string {
		if ( array_key_exists( 'best_price_text', $params ) && $params['best_price_text'] ) {
			return $params['best_price_text'];
		}
		$text = TemplateConfig::getInstance()->option( 'comparison_best_price_text' );
		if ( ! $text ) {
			$text = 'Best Price';
		}

		return $text;
	}

	/**
	 * BestPrice 颜色
	 * @param $params
	 *
	 * @return string
	 */
	public static function getBestPriceColor( $params ): string {
		if ( array_key_exists( 'best_price_color', $params ) && $params['best_price_color'] ) {
			return $params['best_price_color'];
		}
		$color = TemplateConfig::getInstance()->option( 'comparison_best_price_color' );
		if ( ! $color ) {
			$color = '#2ABA9A';
		}

		return $color;
	}

	/**
	 * BestPrice 价格颜色
	 * @param $params
	 *
	 * @return string
	 */
	public static function getBestPricePriceColor( $params ): string {
		if ( array_key_exists( 'best_price_price_color', $params ) && $params['best_price_price_color'] ) {
			return $params['best_price_price_color'];
		}
		$color = TemplateConfig::getInstance()->option( 'comparison_best_price_price_color' );
		if ( ! $color ) {
			$color = '#2ABA9A';
		}

		return $color;
	}

	/**
	 * 渲染 more 按钮
	 *
	 * @param bool $isShowMore
	 * @param string $moreUrl
	 *
	 * @return void
	 */
	public static function displayMoreButton( bool $isShowMore, string $moreUrl ) {
		if ( $isShowMore && $moreUrl != '' ) {
			echo '<a target="_blank" href="' . $moreUrl . '" >';
			echo '	<div class="more">';
			echo '      <img style="width: 14px;height: 14px;margin-right: 3px;vertical-align: middle" src="' . \Moredeal\PLUGIN_RES . '/logos/more.png"/>more';
			echo '	</div>';
			echo '</a>';
		}
	}

	/**
	 * 模版添加宽度
	 *
	 * @param bool $isShowMore
	 * @param string $moreUrl
	 * @param string $postType
	 *
	 * @return void
	 */
	public static function templateGlobalStyle( bool $isShowMore, string $moreUrl, string $postType ) {
		if ( $postType == 'post' ) {
			echo '';
		}
		if ( $postType == 'page' ) {
			if ( $isShowMore && $moreUrl ) {
				echo '';
			} else {
				echo ' style="width: 964px; "';
			}
		}
	}

}